<?php
session_start();
include '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_cliente = $_POST['id_cliente'];
    eliminarCliente($id_cliente);
    
    // Si no hay error, redirige a la página de clientes
    if (!isset($_SESSION['error_eliminar'])) {
        $_SESSION['mensaje_exito'] = "Cliente eliminado correctamente.";
    }
    header('Location: ../clientes.php');
    exit();
}
