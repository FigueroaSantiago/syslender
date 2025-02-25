<?php
session_start();
include '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = $_POST['id_user'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $contacto = $_POST['contacto'];
    $cedula = $_POST['cedula'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Llamada a la función para actualizar el usuario
    actualizarUsuario($id_user, $nombre, $apellido, $contacto, $cedula, $password);

    // Almacena el mensaje de éxito en la sesión
    $_SESSION['mensaje_exito'] = "Usuario editado correctamente.";

    // Redirige de vuelta a la página de edición
    header("Location: editar_usuario.php?id=$id_user");
    exit();
}
