<?php
session_start();
include '../../Conexion/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_user = $_POST['id_user'];

    // Verificar el estado actual del usuario
    $sql = "SELECT estado FROM user WHERE id_user = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();
        $nuevo_estado = ($usuario['estado'] == 'activo') ? 'inactivo' : 'activo';

        // Actualizar el estado del usuario
        $update_sql = "UPDATE user SET estado = ? WHERE id_user = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $nuevo_estado, $id_user);

        if ($update_stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "not_found";
    }

    $stmt->close();
    $conn->close();
}
