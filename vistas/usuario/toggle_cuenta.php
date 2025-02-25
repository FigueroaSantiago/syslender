<?php
session_start();
include '../../Conexion/conexion.php';

// Verificar si el usuario tiene permiso (solo gestores pueden modificar)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 18) {
    echo "error_permiso";
    exit();
}

// Verificar si se recibió el ID de la cuenta
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_cuenta'])) {
    $id_cuenta = intval($_POST['id_cuenta']);

    // Validar que la cuenta existe
    $stmt = $conn->prepare("SELECT estado FROM cuentas WHERE id_cuenta = ?");
    if (!$stmt) {
        echo "error_stmt1: " . $conn->error;
        exit();
    }
    
    $stmt->bind_param("i", $id_cuenta);
    $stmt->execute();
    $result = $stmt->get_result();
    $cuenta = $result->fetch_assoc();

    if (!$cuenta) {
        echo "error_no_cuenta";
        exit();
    }

    // Cambiar el estado de la cuenta
    $nuevo_estado = ($cuenta['estado'] == 'activa') ? 'inactiva' : 'activa';
    $stmt = $conn->prepare("UPDATE cuentas SET estado = ? WHERE id_cuenta = ?");
    if (!$stmt) {
        echo "error_stmt2: " . $conn->error;
        exit();
    }
    
    $stmt->bind_param("si", $nuevo_estado, $id_cuenta);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error_update: " . $stmt->error;
    }
} else {
    echo "error_request";
}

$conn->close();
?>
