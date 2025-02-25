<?php
session_start();
include '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = $_POST['id_user'];
    eliminarUsuario($id_user);
    
    // Si no hay error, redirige a la página de usuarios
    if (!isset($_SESSION['error_eliminar'])) {
        $_SESSION['mensaje_exito'] = "Usuario eliminado correctamente.";
    }
    header('Location: ../usuarios.php');
    exit();
}
