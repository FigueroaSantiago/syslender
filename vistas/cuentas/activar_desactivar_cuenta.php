<?php
session_start();
include '../../Conexion/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_cuenta'])) {
    $id_cuenta = $_POST['id_cuenta'];

    // 🔹 Obtener el estado actual de la cuenta
    $sql = "SELECT estado FROM cuentas WHERE id_cuenta = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_cuenta);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $cuenta = $result->fetch_assoc();
        $nuevo_estado = $cuenta["estado"] === "activa" ? "inactiva" : "activa";

        // 🔹 Actualizar el estado de la cuenta
        $sql = "UPDATE cuentas SET estado = ? WHERE id_cuenta = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nuevo_estado, $id_cuenta);
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "error";
    }
}
?>
