<?php
// obtener_clientes.php
session_start();
include '../../includes/functions.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verifica si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit();
}

// Verifica si el parámetro id_ruta existe
if (!isset($_GET['id_ruta'])) {
    echo json_encode(['error' => 'Ruta no especificada']);
    exit();
}

$ruta_id = $_GET['id_ruta'];

// Supongamos que tienes una función que obtiene los clientes por ID de ruta
$clientes = getClientesByRuta($ruta_id);

if ($clientes) {
    echo json_encode($clientes); // Retorna los clientes encontrados
} else {
    echo json_encode([]); // Retorna un array vacío si no hay clientes
}
?>
