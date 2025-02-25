<?php
session_start();
include '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_ruta = isset($_POST['id_ruta']) ? intval($_POST['id_ruta']) : 0;

    // Verificar si la ruta tiene clientes asociados
    $clientes = getClientesByRuta($id_ruta);
    if (count($clientes) > 0) {
        echo 'constraint_error'; // No puede eliminarse si hay clientes
        exit();
    }

    // Intentar eliminar la ruta
    $result = eliminarRuta($id_ruta);
    echo $result;  // Devuelve el resultado de la eliminación
    exit(); // Finaliza el script
} else {
    echo 'invalid_request';
}
?>


