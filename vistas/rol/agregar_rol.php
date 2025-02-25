<?php
session_start();
include '../../includes/header2.php';
include '../../includes/functions.php';

// Redirige si el usuario no está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$message = ''; // Variable para almacenar el mensaje de respuesta

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rol = $_POST['rol'];

    // Sanitización del nombre del rol
    $rol = htmlspecialchars(trim($rol));  // Elimina espacios en blanco y caracteres especiales
    
    // Validación del nombre del rol
    if (empty($rol)) {
        $message = "empty";
    } elseif (!preg_match("/^[a-zA-Z0-9 ]+$/", $rol)) {
        // Solo letras, números y espacios permitidos
        $message = "invalid";
    } elseif (strlen($rol) < 3 || strlen($rol) > 50) {
        // Longitud mínima y máxima del rol
        $message = "length";
    } else {
        // Verifica si el rol ya existe en la base de datos
        if (rolExiste(  $rol)) {
            $message = "exists";
        } else {
            // Llama a la función para agregar el rol
            if (agregarRol($rol)) {
                $message = "success";
            } else {
                $message = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Rol</title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <style>
        body {
            background-color: #f4f7fa;
            font-family: 'Arial', sans-serif;
        }

        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 30px;
        }

        .form-group label {
            font-weight: bold;
        }

        .btn-success {
            border-radius: 25px;
            padding: 10px 30px;
            font-size: 1rem;
            transition: background-color 0.3s, transform 0.2s;
        }

        .btn-success:hover {
            transform: scale(1.05);
        }

        .form-control {
            border-radius: 25px;
        }

        .error {
            color: red;
            font-size: 0.9em;
        }

        .input-group-text {
            background-color: #28a745;
            color: white;
        }
    </style>
</head>
<body>
<div class="container">
<div class="form-container">
        <h1 class="mb-4">Agregar Rol</h1>
        <form action="agregar_rol.php" method="POST" id="addRoleForm">
            <div class="form-group">
                <label for="rol">Rol</label>
                <input type="text" id="rol" name="rol" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Guardar</button>
        </form>
    </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form");
            form.addEventListener("submit", function(e) {
                e.preventDefault();
                const rol = document.getElementById("rol").value;

                // Validación del rol antes de enviar el formulario
                if (rol.trim() === "") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'El campo rol no puede estar vacío.',
                    });
                    return;
                }

                // Validación de caracteres especiales (solo letras y números permitidos)
                if (!/^[a-zA-Z0-9 ]+$/.test(rol)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'El rol solo puede contener letras, números y espacios.',
                    });
                    return;
                }

                // Validación de la longitud del rol
                if (rol.length < 3 || rol.length > 50) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'El nombre del rol debe tener entre 3 y 50 caracteres.',
                    });
                    return;
                }

                // Enviar el formulario si todo es válido
                this.submit();
            });
        });

        // Mostrar alertas con SweetAlert2 según el resultado
        <?php if ($message == "success"): ?>
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: 'Rol agregado correctamente.',
            }).then(() => {
                window.location = '../roles.php';  // Redirige a la página de roles
            });
        <?php elseif ($message == "error"): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un problema al agregar el rol.',
            });
        <?php elseif ($message == "exists"): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El rol ingresado ya existe.',
            }).then(() => {
                window.location = '../roles.php'; 
            });
        <?php elseif ($message == "empty"): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El campo rol no puede estar vacío.',
            });
        <?php elseif ($message == "invalid"): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El rol solo puede contener letras, números y espacios.',
            });
        <?php elseif ($message == "length"): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El rol debe tener entre 3 y 50 caracteres.',
            });
        <?php endif; ?>
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
