<?php
session_start();
include '../../Conexion/conexion.php';

header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

$searchTerm = isset($_POST['searchTerm']) ? $_POST['searchTerm'] : '';

// Verifica si hay un error en la conexión
if (!$conn) {
    echo json_encode(['error' => 'Error en la conexión a la base de datos']);
    exit();
}

// 🔹 Consulta corregida: Se especifica la tabla para evitar ambigüedad
$sql = "SELECT user.id_user, CONCAT(user.nombre, ' ', user.apellido) AS nombre_completo 
        FROM user 
        INNER JOIN rol_user ON user.id_user = rol_user.id_user 
        WHERE rol_user.id_rol = 1 
        AND (user.nombre LIKE ? OR user.apellido LIKE ?) 
        LIMIT 10";

$search = "%$searchTerm%";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['error' => 'Error en la preparación de la consulta']);
    exit();
}

$stmt->bind_param("ss", $search, $search);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = ['id' => $row['id_user'], 'text' => $row['nombre_completo']];
}

// 🔹 Devuelve los resultados como JSON
echo json_encode($data);
?>
