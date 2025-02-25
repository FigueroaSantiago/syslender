<?php
session_start();
include '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Aquí va tu lógica de procesamiento
    $id_ruta = isset($_POST['id_ruta']) ? intval($_POST['id_ruta']) : 0;
    $nombre_ruta = isset($_POST['nombre_ruta']) ? htmlspecialchars(trim($_POST['nombre_ruta'])) : '';
    $ciudad = isset($_POST['ciudad']) ? htmlspecialchars(trim($_POST['ciudad'])) : '';
    $description = isset($_POST['description']) ? htmlspecialchars(trim($_POST['description'])) : '';
    $id_rol_user = isset($_POST['id_rol_user']) ? intval($_POST['id_rol_user']) : 0;

    if (empty($nombre_ruta) || empty($ciudad) || $id_rol_user <= 0) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
                window.onload = function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Por favor, completa todos los campos requeridos.'
                    }).then(() => {
                        window.history.back();
                    });
                }
              </script>";
        exit();
    }
    if (cobradorTieneRutas($id_rol_user,$id_ruta)) {
        $_SESSION['mensaje_error'] = 'El cobrador seleccionado ya tiene una ruta asignada.';
        header('Location: ../rutas.php');
        exit();
    }


    // Intentar actualizar en la base de datos
    if (editarRuta($id_ruta, $nombre_ruta, $ciudad, $description, $id_rol_user)) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
                window.onload = function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Ruta editada exitosamente.'
                    }).then(() => {
                        window.location = '../rutas.php';
                    });
                }
              </script>";
    } else {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
                window.onload = function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un error al editar la ruta en la base de datos.'
                    }).then(() => {
                        window.history.back();
                    });
                }
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Procesar Edición de Ruta</title>
    <!-- Incluye SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
