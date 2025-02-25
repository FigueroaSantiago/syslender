<?php
session_start();
include '../../includes/functions.php';
include '../../Conexion/conexion.php';

// Activar reporte de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ruta para el registro de errores
define('ERROR_LOG_FILE', '../../logs/errores.log');

// Función personalizada para manejar errores
function registrarError($mensaje) {
    error_log(date('[Y-m-d H:i:s] ') . $mensaje . PHP_EOL, 3, ERROR_LOG_FILE);
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    registrarError('Acceso denegado: usuario no autenticado.');
    $_SESSION['alerta'] = ['tipo' => 'error', 'mensaje' => 'Debe iniciar sesión para continuar.'];
    header('Location: login.php');
    exit();
}

// Verificar el rol del usuario
if ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2) {
    registrarError('Acceso denegado: usuario con rol no autorizado. ID usuario: ' . $_SESSION['user_id']);
    $_SESSION['alerta'] = ['tipo' => 'error', 'mensaje' => 'No tienes permisos para realizar esta acción.'];
    header('Location: no_permitido.php');
    exit();
}


$conn = getDatabaseConnection();
if (!$conn) {
    registrarError("Error al conectar a la base de datos.");
    $_SESSION['alerta'] = ['tipo' => 'error', 'mensaje' => 'No se pudo conectar a la base de datos.'];
    header('Location: editar_prestamo.php');
    exit();
}

$stmt = $conn->prepare("SELECT COUNT(*) FROM rol_user WHERE id_rol_user = ?");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($user_exists);
$stmt->fetch();
$stmt->close();

// Obtener datos del formulario
$id_prestamo = $_POST['id_prestamo'] ?? null;
$id_cliente = $_POST['id_cliente'] ?? null;
$monto_inicial = $_POST['monto'] ?? null;
$fecha_desembolso = isset($_POST['fecha_desembolso']) ? date('Y-m-d', strtotime($_POST['fecha_desembolso'])) : null;
$duracion = $_POST['duracion'] ?? null;

// Validar datos obligatorios
if (!$id_prestamo || !$id_cliente || !$monto_inicial || !$fecha_desembolso || !$duracion) {
    registrarError("Validación fallida: Datos obligatorios faltantes. ID préstamo: $id_prestamo");
    $_SESSION['alerta'] = ['tipo' => 'error', 'mensaje' => 'Todos los campos son obligatorios.'];
    header('Location: editar_prestamo.php?id=' . $id_prestamo);
    exit();
}

// Validar monto y duración
if ($monto_inicial <= 0 || $duracion <= 0) {
    registrarError("Validación fallida: Valores no positivos. Monto: $monto_inicial, Duración: $duracion");
    $_SESSION['alerta'] = ['tipo' => 'error', 'mensaje' => 'El monto y la duración deben ser valores positivos.'];
    header('Location: editar_prestamo.php?id=' . $id_prestamo);
    exit();
}

// Calcular fecha de vencimiento excluyendo domingos
try {
    $fecha_vencimiento = $fecha_desembolso;
    $dias_habiles = 0;
    while ($dias_habiles < $duracion) {
        $fecha_vencimiento = date('Y-m-d', strtotime($fecha_vencimiento . ' +1 day'));
        if (date('N', strtotime($fecha_vencimiento)) != 7) { // Excluir domingos (N=7)
            $dias_habiles++;
        }
    }
} catch (Exception $e) {
    registrarError("Error al calcular fecha de vencimiento: " . $e->getMessage());
    $_SESSION['alerta'] = ['tipo' => 'error', 'mensaje' => 'Error al calcular la fecha de vencimiento.'];
    header('Location: editar_prestamo.php?id=' . $id_prestamo);
    exit();
}

// Conectar a la base de datos
$conn = getDatabaseConnection();
if (!$conn) {
    registrarError("Error al conectar a la base de datos.");
    $_SESSION['alerta'] = ['tipo' => 'error', 'mensaje' => 'No se pudo conectar a la base de datos.'];
    header('Location: editar_prestamo.php?id=' . $id_prestamo);
    exit();
}

// Iniciar transacción
$conn->begin_transaction();
try {
    // Verificar si alguna cuota ya tiene pagos registrados
    $stmt = $conn->prepare("SELECT COUNT(*) FROM cuota_prestamo WHERE id_prestamo = ? AND estado = 'pago'");
    if (!$stmt) {
        throw new Exception("Error al preparar consulta: " . $conn->error);
    }
    $stmt->bind_param('i', $id_prestamo);
    $stmt->execute();
    $stmt->bind_result($cuotas_pagadas);
    $stmt->fetch();
    $stmt->close();

    if ($cuotas_pagadas > 0) {
        throw new Exception('No se puede editar el préstamo porque tiene cuotas ya pagadas.');
    }

    // Obtener tasa de interés
    $stmt = $conn->prepare("SELECT tasa_interes FROM configuracion WHERE id_configuracion = 1");
    if (!$stmt) {
        throw new Exception("Error al preparar consulta para tasa de interés: " . $conn->error);
    }
    $stmt->execute();
    $stmt->bind_result($tasa_interes);
    $stmt->fetch();
    $stmt->close();

    if (!$tasa_interes) {
        throw new Exception('No se encontró la tasa de interés en la configuración.');
    }

    // Calcular intereses
    $interes_total = $monto_inicial * ($tasa_interes / 100);
    $saldo_actual = $monto_inicial + $interes_total;

    // Actualizar préstamo
    $stmt = $conn->prepare("
        UPDATE prestamo SET 
        id_cliente = ?, monto_inicial = ?, saldo_actual = ?, interes_total = ?, 
        inicio_fecha = ?, vencimiento_fecha = ?, duracion = ?,  fecha_modificacion = NOW()
        WHERE id_prestamo = ?
    ");
    if (!$stmt) {
        throw new Exception("Error al preparar consulta de actualización: " . $conn->error);
    }
    $stmt->bind_param(
        'idddssii',
        $id_cliente, $monto_inicial, $saldo_actual, $interes_total,
        $fecha_desembolso, $fecha_vencimiento, $duracion,  $id_prestamo
    );
    if (!$stmt->execute()) {
        throw new Exception('Error al actualizar el préstamo: ' . $stmt->error);
    }

    // Eliminar cuotas existentes
    $stmt = $conn->prepare("DELETE FROM cuota_prestamo WHERE id_prestamo = ?");
    if (!$stmt) {
        throw new Exception("Error al preparar consulta para eliminar cuotas: " . $conn->error);
    }
    $stmt->bind_param('i', $id_prestamo);
    if (!$stmt->execute()) {
        throw new Exception('Error al eliminar las cuotas: ' . $stmt->error);
    }

    // Generar nuevas cuotas
    generarCuotas($id_prestamo, $fecha_desembolso, $duracion, $monto_inicial, $interes_total);

    // Confirmar la transacción
    $conn->commit();

    $_SESSION['alerta'] = ['tipo' => 'success', 'mensaje' => 'Préstamo actualizado y cuotas regeneradas exitosamente.'];
    header('Location: ../prestamos.php');
    exit();
} catch (Exception $e) {
    $conn->rollback();
    registrarError("Transacción fallida: " . $e->getMessage());
    $_SESSION['alerta'] = ['tipo' => 'error', 'mensaje' => $e->getMessage()];
    header('Location: editar_prestamo.php?id=' . $id_prestamo);
    exit();
} finally {
    if (isset($stmt) && $stmt instanceof mysqli_stmt) {
        $stmt->close();
    }
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>
