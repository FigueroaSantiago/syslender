<?php
session_start();
include '../../includes/header2.php';
include '../../includes/functions.php';

// Redirige si el usuario no está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}



// Obtén los roles de cobrador
$roles = getRolesDeCobrador();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Ruta</title>

    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
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
       
            <h1 class="mb-4">Agregar Ruta</h1>
            <form id="addRouteForm" action="procesar_agregar_ruta.php" method="POST">
                <div class="form-group">
                    <label for="nombre_ruta">Nombre de la Ruta</label>
                    <input type="text" id="nombre_ruta" name="nombre_ruta" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="ciudad">Ciudad</label>
                    <input type="text" id="ciudad" name="ciudad" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="description">Descripción</label>
                    <textarea id="description" name="description" class="form-control" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label for="rol_user">Rol de Usuario</label>
                    <select id="rol_user" name="id_rol_user" class="form-control" required>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?php echo htmlspecialchars($rol['id_rol_user']); ?>">
                                <?php echo htmlspecialchars($rol['nombre_completo']) . ' - ' . htmlspecialchars($rol['rol']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" id="submitBtn" class="btn btn-success btn-block">Guardar</button>
            </form>
        </div>
    </div>
   
    <?php if (isset($_SESSION['mensaje_exito'])): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '<?php echo $_SESSION['mensaje_exito']; ?>',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = '../rutas.php';
            });
        </script>
        <?php unset($_SESSION['mensaje_exito']); ?>
    <?php elseif (isset($_SESSION['mensaje_error'])): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $_SESSION['mensaje_error']; ?>',
                showConfirmButton: false,
                timer: 3000
            }).then(() => {
                window.location.href = 'agregar_ruta.php';
            });
        </script>
        <?php unset($_SESSION['mensaje_error']); ?>
    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('addRouteForm');
            const submitBtn = document.getElementById('submitBtn');

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const nombreRuta = document.getElementById('nombre_ruta').value.trim();
                const ciudad = document.getElementById('ciudad').value.trim();
                const rolUser = document.getElementById('rol_user').value;

                // Validaciones
                if (nombreRuta === "" || ciudad === "" || rolUser === "") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Por favor, completa todos los campos requeridos.',
                    });
                    return;
                }

                // Validación adicional: solo permitir letras y espacios en el nombre de la ruta y ciudad
                const nombreRutaRegex = /^[a-zA-Z\s]+$/;
                const ciudadRegex = /^[a-zA-Z\s]+$/;
                
                if (!nombreRutaRegex.test(nombreRuta)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'El nombre de la ruta solo puede contener letras y espacios.',
                    });
                    return;
                }
                
                if (!ciudadRegex.test(ciudad)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'La ciudad solo puede contener letras y espacios.',
                    });
                    return;
                }

                // Confirmación antes de enviar
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¿Deseas agregar esta ruta?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, agregar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitBtn.disabled = true; // Deshabilitar botón para evitar doble envío
                        form.submit(); // Enviar el formulario
                    }
                });
            });
        });
    </script>
</body>
</html>
