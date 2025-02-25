<?php
session_start();
include '../../includes/header2.php';
include '../../includes/functions.php';

// Redirige si el usuario no está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$id_rol = $_GET['id_rol'];
$rol = getRolById($id_rol);

// Variable para almacenar el mensaje de alerta
$alerta = ''; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rol_nuevo = $_POST['rol'];

    // Verificar si el rol ya existe antes de actualizar
    if (rolExiste($rol_nuevo)) {
        $alerta = "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'El rol ya existe, elige un nombre diferente.',
                    });
                   </script>";
    } else {
        // Proceder con la actualización del rol
        if (actualizarRol($id_rol, $rol_nuevo)) {
            $alerta = "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: 'Rol actualizado correctamente.',
                        }).then(() => {
                            window.location = '../roles.php';
                        });
                       </script>";
        } else {
            $alerta = "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un problema al actualizar el rol.',
                        });
                       </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Rol</title>
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
        <h1 class="mb-4">Editar Rol</h1>
        <?php if ($alerta) echo $alerta; ?>
        <form action="editar_rol.php?id_rol=<?php echo $id_rol; ?>" method="POST">
            <div class="form-group">
                <label for="rol">Rol</label>
                <input type="text" id="rol" name="rol" class="form-control" value="<?php echo htmlspecialchars($rol['rol']); ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Actualizar</button>
        </form>
    </div>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form");
            form.addEventListener("submit", function(e) {
                e.preventDefault();
                const rol = document.getElementById("rol").value;

                if (rol.trim() === "") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'El campo rol no puede estar vacío.',
                    });
                    return;
                }

                // Alerta de confirmación antes de enviar
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¿Deseas actualizar el rol?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, actualizar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Enviar el formulario
                    }
                });
            });
        });
    </script>
</body>
</html>
