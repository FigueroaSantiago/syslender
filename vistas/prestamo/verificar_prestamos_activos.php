<?php
include '../../includes/functions.php';
$conn = getDatabaseConnection();

$id_cliente = $_POST['id_cliente'];

// Verifica si el cliente ya tiene dos préstamos activos
$stmt = $conn->prepare("SELECT COUNT(*) FROM prestamo WHERE id_cliente = ? AND estado = 'activo'");
$stmt->bind_param('i', $id_cliente);
$stmt->execute();
$stmt->bind_result($prestamos_activos);
$stmt->fetch();
$stmt->close();
$conn->close();

echo $prestamos_activos >= 2 ? "excede" : "ok";
?>
