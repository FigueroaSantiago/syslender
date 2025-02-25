<?php
session_start();
include '../../includes/header2.php';
include '../../includes/functions.php';

// Validar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Obtener el ID y los datos de la ruta a editar
$id_ruta = isset($_GET['id']) ? intval($_GET['id']) : 0;
$ruta = obtenerRutaPorId($id_ruta);
if (!$ruta) {
    echo "<script>Swal.fire({icon: 'error', title: 'Error', text: 'Ruta no encontrada.'}).then(() => window.location.href = '../rutas.php');</script>";
    exit();
}

// Obtener los roles de cobrador
$roles = getRolesDeCobrador();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Ruta</title>
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
            <h1 class="mb-4">Editar Ruta</h1>

            <?php
            if (isset($_SESSION['mensaje_exito'])) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: '{$_SESSION['mensaje_exito']}',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        window.location.href = '../rutas.php';
                    });
                </script>";
                unset($_SESSION['mensaje_exito']);
            } elseif (isset($_SESSION['mensaje_error'])) {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '{$_SESSION['mensaje_error']}',
                        confirmButtonText: 'Aceptar'
                    });
                </script>";
                unset($_SESSION['mensaje_error']);
            }
            ?>

            <form id="editForm" action="procesar_editar_ruta.php" method="POST">
                <input type="hidden" name="id_ruta" value="<?php echo htmlspecialchars($ruta['id_ruta']); ?>">
                <div class="form-group">
                    <label for="nombre_ruta">Nombre de la Ruta</label>
                    <input type="text" id="nombre_ruta" name="nombre_ruta" class="form-control" value="<?php echo htmlspecialchars($ruta['nombre_ruta']); ?>" required minlength="3">
                </div>
                <div class="form-group">
                    <label for="ciudad">Ciudad</label>
                    <input type="text" id="ciudad" name="ciudad" class="form-control" value="<?php echo htmlspecialchars($ruta['ciudad']); ?>" required minlength="3">
                </div>
                <div class="form-group">
                    <label for="description">Descripción</label>
                    <textarea id="description" name="description" class="form-control" rows="4"><?php echo htmlspecialchars($ruta['descripcion']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="rol_user">Rol de Usuario</label>
                    <select id="rol_user" name="id_rol_user" class="form-control" required>
                        <option value="">Seleccione un rol</option>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?php echo htmlspecialchars($rol['id_rol_user']); ?>" <?php echo $rol['id_rol_user'] == $ruta['id_rol_user'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($rol['nombre_completo']) . ' - ' . htmlspecialchars($rol['rol']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-success btn-block">Guardar</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("editForm");

            form.addEventListener("submit", function(e) {
                e.preventDefault();

                // Validar campos vacíos
                const nombreRuta = document.getElementById("nombre_ruta").value.trim();
                const ciudad = document.getElementById("ciudad").value.trim();
                const rolUser = document.getElementById("rol_user").value;

                if (!nombreRuta || nombreRuta.length < 3) {
                    Swal.fire("Error", "El nombre de la ruta debe tener al menos 3 caracteres.", "warning");
                    return;
                }

                if (!ciudad || ciudad.length < 3) {
                    Swal.fire("Error", "La ciudad debe tener al menos 3 caracteres.", "warning");
                    return;
                }

                if (!rolUser) {
                    Swal.fire("Error", "Selecciona un rol de usuario.", "warning");
                    return;
                }

                // Confirmación de envío
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¿Deseas actualizar la ruta?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, actualizar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
</body>
</html>
