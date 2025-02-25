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

// Conectar a la base de datos
$conn = getDatabaseConnection();
if (!$conn) {
    registrarError("Error al conectar a la base de datos.");
    $_SESSION['alerta'] = ['tipo' => 'error', 'mensaje' => 'No se pudo conectar a la base de datos.'];
    header('Location: ../prestamos.php');
    exit();
}

// Obtener datos del formulario
$id_prestamo = $_POST['id_prestamo'] ?? null;
$password = $_POST['password'] ?? null;
$user_id = $_SESSION['user_id'];

// Validar datos obligatorios
if (!$id_prestamo || !$password) {
    registrarError("Datos faltantes para eliminar préstamo. ID préstamo: $id_prestamo, contraseña proporcionada: " . !empty($password));
    $_SESSION['alerta'] = ['tipo' => 'error', 'mensaje' => 'Se requieren todos los campos para eliminar el préstamo.'];
    header('Location: ../prestamos.php');
    exit();
}

try {
    // Verificar existencia del usuario y contraseña
    $stmt = $conn->prepare("SELECT password FROM user WHERE id_user = ?");
    if (!$stmt) {
        throw new Exception("Error al preparar consulta de contraseña: " . $conn->error);
    }
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    if (!$hashed_password || !password_verify($password, $hashed_password)) {
        throw new Exception('Contraseña incorrecta.');
    }

    // Verificar existencia del préstamo y datos relacionados
    $stmt = $conn->prepare("
        SELECT saldo_actual, COUNT(cuota_prestamo.id_cuota) AS cuotas_relacionadas
        FROM prestamo
        LEFT JOIN cuota_prestamo ON prestamo.id_prestamo = cuota_prestamo.id_prestamo
        WHERE prestamo.id_prestamo = ?
    ");
    if (!$stmt) {
        throw new Exception("Error al preparar consulta de datos del préstamo: " . $conn->error);
    }
    $stmt->bind_param('i', $id_prestamo);
    $stmt->execute();
    $stmt->bind_result($saldo_actual, $cuotas_relacionadas);
    $stmt->fetch();
    $stmt->close();

    if ($saldo_actual === null) {
        throw new Exception('El préstamo no existe.');
    }

    if ($saldo_actual > 0) {
        throw new Exception('El préstamo no puede eliminarse porque tiene un saldo mayor a 0.');
    }

    // Confirmar eliminación de cuotas relacionadas si existen
    if ($cuotas_relacionadas > 0) {
        // Si existen cuotas, eliminarlas primero
        $conn->begin_transaction();

        $stmt = $conn->prepare("DELETE FROM cuota_prestamo WHERE id_prestamo = ?");
        if (!$stmt) {
            throw new Exception("Error al preparar consulta para eliminar cuotas: " . $conn->error);
        }
        $stmt->bind_param('i', $id_prestamo);
        $stmt->execute();

        $stmt->close();
    }

    // Eliminar el préstamo
    $stmt = $conn->prepare("DELETE FROM prestamo WHERE id_prestamo = ?");
    if (!$stmt) {
        throw new Exception("Error al preparar consulta para eliminar préstamo: " . $conn->error);
    }
    $stmt->bind_param('i', $id_prestamo);
    $stmt->execute();

    // Confirmar transacción
    $conn->commit();

    $_SESSION['alerta'] = ['tipo' => 'success', 'mensaje' => 'Préstamo eliminado correctamente.'];
    header('Location: ../prestamos.php');
    exit();
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollback();
    registrarError("Error al eliminar préstamo: " . $e->getMessage());
    $_SESSION['alerta'] = ['tipo' => 'error', 'mensaje' => $e->getMessage()];
    header('Location: ../prestamos.php');
    exit();
} finally {
    // Cerrar conexión y sentencia
    if (isset($stmt) && $stmt instanceof mysqli_stmt) {
        $stmt->close();
    }
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
