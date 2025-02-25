<?php
session_start();
include '../../includes/header2.php';
include '../../includes/functions.php';

// Activa la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Redirige si el usuario no está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Verifica si se ha pasado el id_rol
if (!isset($_GET['id_rol'])) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'ID de rol no especificado.'
            }).then(() => {
                window.location = '../roles.php';
            });
          </script>";
    exit();
}

$id_rol = $_GET['id_rol'];
$rol = getRolById($id_rol);

// Verifica que el rol exista antes de continuar
if ($rol === false) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Rol no encontrado.'
            }).then(() => {
                window.location = '../roles.php';
            });
          </script>";
    exit();
}

$permisos = getPermisos(); // Obtiene todos los permisos
if ($permisos === false) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudieron obtener los permisos.'
            }).then(() => {
                window.location = '../roles.php';
            });
          </script>";
    exit();
}

// Obtener los permisos actuales asignados al rol
$permisos_asignados = getPermisosAsignados($id_rol);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $permisos_asignados = $_POST['permisos'] ?? []; // Arreglo de permisos seleccionados

    // Validación de backend para asegurar que se ha seleccionado al menos un permiso
    if (empty($permisos_asignados)) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se puede guardar sin seleccionar al menos un permiso.'
                });
              </script>";
        exit();
    }

    if (asignarPermisos($id_rol, $permisos_asignados)) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: 'Permisos asignados correctamente. Los permisos anteriores han sido limpiados.',
                }).then(() => {
                    window.location = '../roles.php';
                });
              </script>";
    } else {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron asignar los permisos. Intenta nuevamente.',
                });
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Permisos</title>
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

        .form-check {
            margin: 10px 0;
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

        .form-check-label {
            font-size: 1rem;
            margin-left: 10px;
        }

        .input-group-text {
            background-color: #28a745;
            color: white;
        }

        .error {
            color: red;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h1 class="text-center">Asignar Permisos a <?php echo htmlspecialchars($rol['rol']); ?></h1>
            <form action="asignarpermisos.php?id_rol=<?php echo $id_rol; ?>" method="POST">
                <div class="form-group mb-4">
                    <h5>Selecciona los permisos:</h5>
                    <?php foreach ($permisos as $permiso): ?>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="permisos[]" value="<?php echo $permiso['id_permisos']; ?>" id="permiso_<?php echo $permiso['id_permisos']; ?>"
                                   <?php echo in_array($permiso['id_permisos'], $permisos_asignados) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="permiso_<?php echo $permiso['id_permisos']; ?>">
                                <?php echo htmlspecialchars($permiso['nombre']); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" class="btn btn-success btn-block">Guardar Permisos</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form");

            // Validación en el evento submit del formulario
            form.addEventListener("submit", function(e) {
                const checkboxes = document.querySelectorAll('input[name="permisos[]"]:checked');
                if (checkboxes.length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Debes seleccionar al menos un permiso.'
                    });
                }
            });
        });

        <?php if (isset($_SESSION['response'])) : ?>
        Swal.fire({
            icon: '<?php echo $_SESSION['response']['status']; ?>',
            title: '<?php echo ucfirst($_SESSION['response']['status']); ?>',
            text: '<?php echo $_SESSION['response']['message']; ?>'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../roles.php';  // Redirigir a una página específica
            }
        });
        <?php unset($_SESSION['response']); endif; ?>
    </script>
</body>
</html>
