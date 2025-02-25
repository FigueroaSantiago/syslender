<?php
session_start();
include '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_rol = $_POST['id_rol'];
    try {
        eliminarRol($id_rol); // Asegúrate de que esta función está bien definida
        echo 'success';
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') { // Código de error de restricción de clave foránea
            echo 'constraint_error';
        } else {
            echo 'error';
        }
    }
} else {
    echo 'invalid_request';
}
