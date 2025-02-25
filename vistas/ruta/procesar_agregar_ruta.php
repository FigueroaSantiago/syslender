<?php
session_start();
include '../../includes/functions.php';

// Verifica si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_ruta = trim($_POST['nombre_ruta']);
    $ciudad = trim($_POST['ciudad']);
    $description = trim($_POST['description']);
    $id_rol_user = intval($_POST['id_rol_user']);

    // Validar que el cobrador no tenga ya una ruta asignada
    if (cobradorTieneRuta($id_rol_user)) {
        $_SESSION['mensaje_error'] = 'El cobrador seleccionado ya tiene una ruta asignada.';
        header('Location: agregar_ruta.php');
        exit();
    }

    // Procesar la inserción en la base de datos
    $resultado = agregarRuta($nombre_ruta, $ciudad, $description, $id_rol_user);

    if ($resultado) {
        $_SESSION['mensaje_exito'] = 'Ruta agregada exitosamente.';
        header('Location: ../rutas.php');
        exit();
    } else {
        $_SESSION['mensaje_error'] = 'Ocurrió un error al agregar la ruta. Inténtalo de nuevo.';
        header('Location: agregar_ruta.php');
        exit();
    }
}

// Redirige si se accede al script directamente
header('Location: agregar_ruta.php');
exit();
?>