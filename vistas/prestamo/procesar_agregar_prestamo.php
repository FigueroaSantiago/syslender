<?php
session_start();
include '../../includes/functions.php';

// Activar el reporte de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    $_SESSION['alerta'] = ['tipo' => 'error', 'mensaje' => 'Debe iniciar sesión para continuar.'];
    session_write_close();
    header('Location: login.php');
    exit();
}

// Verificar el rol del usuario (Administrador o Cobrador)
if ($_SESSION['role_id'] != 2 && $_SESSION['role_id'] != 1) {
    $_SESSION['alerta'] = ['tipo' => 'error', 'mensaje' => 'No tienes permisos para realizar esta acción.'];
    session_write_close();
    header('Location: no_permitido.php');
    exit();
}

// Obtener los datos enviados desde el formulario
$id_cliente = $_POST['id_cliente'] ?? null;
$monto_inicial = $_POST['monto_inicial'] ?? null;
$fecha_inicio = isset($_POST['fecha_desembolso']) ? date('Y-m-d', strtotime($_POST['fecha_desembolso'])) : null;
$duracion = $_POST['duracion'] ?? null;
$id_garantias = !empty($_POST['id_garantias']) ? $_POST['id_garantias'] : null;
$estado = 'activo';
$creado_por = $_SESSION['user_id'];

// Validaciones de los datos del formulario
if ($monto_inicial <= 0 || empty($fecha_inicio) || $duracion <= 0) {
    $_SESSION['alerta'] = ['tipo' => 'error', 'mensaje' => 'Datos del formulario incorrectos.'];
    session_write_close();
    header('Location: agregar_prestamo.php');
    exit();
}

// Verificar que todos los campos requeridos estén completos
if (is_null($id_cliente) || is_null($fecha_inicio) || is_null($duracion)) {
    $_SESSION['alerta'] = ['tipo' => 'error', 'mensaje' => 'Todos los campos obligatorios deben ser completados.'];
    session_write_close();
    header('Location: agregar_prestamo.php');
    exit();
}

// Validación de fecha en PHP para evitar fechas en el pasado
if (strtotime($fecha_inicio) < strtotime(date('Y-m-d'))) {
    $_SESSION['alerta'] = ['tipo' => 'error', 'mensaje' => 'La fecha de desembolso no puede ser en el pasado.'];
    session_write_close();
    header('Location: agregar_prestamo.php');
    exit();
}

// Conexión a la base de datos
$conn = getDatabaseConnection();

// Función para calcular la fecha de vencimiento excluyendo domingos

// Calcular la fecha de vencimiento
$vencimiento_fecha = calcularFechaVencimiento($fecha_inicio, $duracion);

// Iniciar transacción para asegurar consistencia de datos
$conn->begin_transaction();
try {
    // Obtener la tasa de interés desde la configuración
    $stmt = $conn->prepare("SELECT tasa_interes FROM configuracion WHERE id_configuracion = 1");
    $stmt->execute();
    $stmt->bind_result($tasa_interes);
    $stmt->fetch();
    $stmt->close();

    if (!$tasa_interes) {
        throw new Exception('No se encontró la tasa de interés en la configuración.');
    }

    // Calcular el interés total y saldo actual
    $interes_total = $monto_inicial * ($tasa_interes / 100);
    $saldo_actual = $monto_inicial + $interes_total;

    // Verificar si el cliente ya tiene dos préstamos activos
    $stmt = $conn->prepare("SELECT COUNT(*) FROM prestamo WHERE id_cliente = ? AND estado = 'activo'");
    $stmt->bind_param('i', $id_cliente);
    $stmt->execute();
    $stmt->bind_result($prestamos_activos);
    $stmt->fetch();
    $stmt->close();

    if ($prestamos_activos >= 2) {
        throw new Exception('El cliente ya tiene dos préstamos activos.');
    }

    // Obtener el id_rol_user del usuario actual
    $stmt = $conn->prepare("SELECT id_rol_user FROM rol_user WHERE id_user = ?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($id_rol_user);
    $stmt->fetch();
    $stmt->close();

    if (!$id_rol_user) {
        throw new Exception('El ID del usuario no está asociado a un rol válido.');
    }

/// Obtener el id_ruta del cliente
$stmt = $conn->prepare("SELECT id_ruta FROM cliente WHERE id_cliente = ?");
$stmt->bind_param('i', $id_cliente);
$stmt->execute();
$stmt->bind_result($id_ruta);
$stmt->fetch();
$stmt->close();

if (!$id_ruta) {
    throw new Exception('No se encontró una ruta asignada para el cliente.');
}

// Obtener el id_rol_user del cobrador asignado a la ruta del cliente
$stmt = $conn->prepare("SELECT id_rol_user FROM ruta WHERE id_ruta = ?");
$stmt->bind_param('i', $id_ruta);
$stmt->execute();
$stmt->bind_result($cobrador_id);
$stmt->fetch();
$stmt->close();

if (!$cobrador_id) {
    throw new Exception('No se encontró un cobrador asignado a la ruta del cliente.');
}

// Consultar la asignación de la base para este cobrador
$stmt = $conn->prepare("SELECT id_base FROM asignacion_base WHERE id_cobrador = ?");
$stmt->bind_param('i', $cobrador_id);
$stmt->execute();
$stmt->bind_result($id_base);
$stmt->fetch();
$stmt->close();

if (!$id_base) {
    throw new Exception('No se encontró una base asignada para este cobrador.');
}

// Consultar el monto de la base disponible
$stmt = $conn->prepare("SELECT base FROM base WHERE id_base = ?");
$stmt->bind_param('i', $id_base);
$stmt->execute();
$stmt->bind_result($base_disponible);
$stmt->fetch();
$stmt->close();

if (!$base_disponible) {
    throw new Exception('El monto de la base no está disponible o no es válido.');
}

// Verificar si la base es suficiente para el préstamo solicitado
if ($base_disponible < $monto_inicial) {
    throw new Exception('El cobrador no tiene suficiente base para cubrir este préstamo. la base del cobrador es '. $base_disponible);
}

// Restar el monto del préstamo de la base del cobrador
$nueva_base = $base_disponible - $monto_inicial;
$stmt = $conn->prepare("UPDATE base SET base = ? WHERE id_base = ?");
$stmt->bind_param('di', $nueva_base, $id_base);

if (!$stmt->execute()) {
    throw new Exception('Error al actualizar la base del cobrador: ' . $stmt->error);
}
$stmt->close();

// Obtener el id_asignacion_base para este cobrador
$stmt = $conn->prepare("SELECT id_asignacion FROM asignacion_base WHERE id_base = ?");
$stmt->bind_param('i', $id_base);
$stmt->execute();
$stmt->bind_result($id_asignacion_base);
$stmt->fetch();
$stmt->close();

if (!$id_asignacion_base) {
    throw new Exception('No se encontró una asignación de base para este cobrador.');
}

// Registrar el movimiento en la tabla movimientos_base
$stmt = $conn->prepare("
    INSERT INTO movimientos_base (id_asignacion_base, tipo_movimiento, monto, descripcion, fecha)
    VALUES (?, 'desembolso_prestamo', ?, 'Desembolso de préstamo al cliente', NOW())
");
$stmt->bind_param('di', $id_asignacion_base, $monto_inicial);

if (!$stmt->execute()) {
    throw new Exception('Error al registrar el movimiento en movimientos_base: ' . $stmt->error);
}
$stmt->close();




// Aquí va el código para insertar el préstamo, calcular los intereses, y demás operaciones



    
    // Insertar el préstamo con el id_rol_user correcto y la fecha de vencimiento
    $stmt = $conn->prepare("
        INSERT INTO prestamo (
            id_rol_user, id_cliente, monto_inicial, saldo_actual, interes_total, inicio_fecha, duracion, 
            estado, creado_por, fecha_creacion, id_garantias, vencimiento_fecha
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)
    ");
    $stmt->bind_param(
        'iidddsisiis', 
        $id_rol_user, $id_cliente, $monto_inicial, $saldo_actual, $interes_total,
        $fecha_inicio, $duracion, $estado, $id_rol_user, $id_garantias, $vencimiento_fecha
    );

    if (!$stmt->execute()) {
        throw new Exception('Error en la inserción del préstamo: ' . $stmt->error);
    }

    // Obtener el ID del préstamo insertado
    $id_prestamo = $stmt->insert_id;

    // Generar cuotas y manejar posibles errores
    generarCuotas($id_prestamo, $fecha_inicio, $duracion, $monto_inicial, $interes_total);

    // Confirmar la transacción
    $conn->commit();

    $_SESSION['alerta'] = ['tipo' => 'success', 'mensaje' => 'Préstamo agregado y cuotas generadas exitosamente.'];

    session_write_close();
    header('Location: ../prestamos.php');
    exit();
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollback();
    $_SESSION['alerta'] = ['tipo' => 'error', 'mensaje' => 'Error: ' . $e->getMessage()];
    session_write_close();
    header('Location: agregar_prestamo.php');
    exit();
} finally {
    // Cerrar conexión y declaración de manera segura
    if (isset($stmt) && $stmt instanceof mysqli_stmt) {
        $stmt->close();
    }
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>
