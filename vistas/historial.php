<?php
session_start();

// Validar sesión y rol
if (!isset($_SESSION['user_id'])) {
    header("Location: login/login.php");
    exit();
}

include '../includes/header.php';
include '../includes/functionsco.php';

$user_id = $_SESSION['user_id'];
$role_id = $_SESSION['role_id']; // Asumiendo que el rol está guardado en la sesión

// Consulta para obtener los préstamos según el rol
if ($role_id == 1) { // Si es administrador (role_id 1)
    // Consulta para obtener todos los préstamos
    $sql = "SELECT p.id_prestamo, p.saldo_actual, p.inicio_fecha, p.vencimiento_fecha, p.estado, c.nombres, c.apellidos 
            FROM prestamo p
            JOIN cliente c ON p.id_cliente = c.id_cliente
            ORDER BY p.inicio_fecha DESC";
} else { // Si es cobrador (role_id 2)
    // Consulta para obtener los préstamos solo de la ruta o clientes del cobrador
    // Se asume que cada cobrador tiene una ruta asignada (id_ruta en cliente)
    // Y se obtienen los préstamos relacionados con los clientes de esa ruta
    $sql =   $sql = "SELECT 
    p.id_prestamo, 
    p.saldo_actual, 
    p.inicio_fecha, 
    p.vencimiento_fecha, 
    p.estado, 
    c.nombres, 
    c.apellidos 
FROM 
    prestamo p
JOIN 
    cliente c ON p.id_cliente = c.id_cliente
JOIN 
    ruta r ON c.id_ruta = r.id_ruta
JOIN 
    rol_user ru ON r.id_rol_user = ru.id_rol_user
WHERE 
    ru.id_user = ? -- Aquí se usa el ID del usuario que está autenticado
ORDER BY 
    p.inicio_fecha DESC"    ;
}

$stmt = $conn->prepare($sql);

// Si el rol es cobrador, pasamos el id_user del cobrador
if ($role_id == 2) {
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Préstamos</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4">Historial de Préstamos</h2>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Préstamo</th>
                <th>Cliente</th>
                <th>Monto</th>
                <th>Fecha Inicio</th>
                <th>Fecha Vencimiento</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id_prestamo']}</td>
                            <td>{$row['nombres']} {$row['apellidos']}</td>
                            <td>$" . number_format($row['saldo_actual'], 2) . "</td>
                            <td>{$row['inicio_fecha']}</td>
                            <td>{$row['vencimiento_fecha']}</td>
                            <td>{$row['estado']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>No hay préstamos registrados</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

