<?php
session_start();
include '../../includes/functions.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = $_POST['id_user'];

    // Validar que no sea administrador
    if (esAdministrador($id_user)) {
        echo 'admin_error';
        exit(); // Detener la ejecución aquí
    }

    // Validar que no tenga rutas asignadas
    if (tieneRutaAsignada($id_user)) {
        echo 'ruta_error';
        exit(); // Detener la ejecución aquí
    }

    // Intentar eliminar
    if (eliminarUsuario($id_user)) {
      
        exit(); // Detener la ejecución después del éxito
    } else {

        exit(); // Detener la ejecución después de un error
    }
} else {
    echo 'invalid_request';
    exit(); // Detener la ejecución para solicitudes no válidas
}
