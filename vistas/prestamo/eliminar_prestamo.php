<?php
session_start();
include '../../includes/functions.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    $_SESSION['alerta'] = ['tipo' => 'error', 'mensaje' => 'Debe iniciar sesión para continuar.'];
    header('Location: login.php');
    exit();
}

// Obtener el ID del préstamo
$id_prestamo = $_GET['id'] ?? null;
if (!$id_prestamo) {
    $_SESSION['alerta'] = ['tipo' => 'error', 'mensaje' => 'ID del préstamo no proporcionado.'];
    header('Location: prestamos.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Eliminación del Préstamo</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
            color: #dc3545;
        }

        .form-description {
            font-size: 1rem;
            margin-bottom: 20px;
            color: #6c757d;
            text-align: center;
        }

        .form-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input {
            height: 45px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            padding: 10px;
            width: 100%;
        }

        footer {
            margin-top: 30px;
            text-align: center;
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <h1 class="form-title">Confirmar Eliminación del Préstamo</h1>
        <p class="form-description">
            El préstamo que está intentando eliminar tiene datos relacionados. Si continúa, se eliminarán todos los datos asociados. 
            <strong>Esta acción no se puede deshacer.</strong>
        </p>

        <form action="procesar_eliminar_prestamo.php" method="POST">
            <input type="hidden" name="id_prestamo" value="<?php echo htmlspecialchars($id_prestamo); ?>">

            <div class="form-group">
                <label for="password">Confirme su contraseña:</label>
                <input type="password" name="password" id="password" required placeholder="Ingrese su contraseña">
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn btn-danger btn-lg">Confirmar y Eliminar</button>
                <a href="../prestamos.php" class="btn btn-secondary btn-lg">Cancelar</a>
            </div>
        </form>
    </div>

    <footer>
        <p>Gucobro &copy; <?php echo date('Y'); ?> - Todos los derechos reservados</p>
    </footer>

    <script>
        // Mostrar alerta de advertencia al cargar la página
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'warning',
                title: 'Advertencia',
                text: 'Este préstamo tiene datos relacionados. Proceda con precaución.',
                confirmButtonText: 'Entendido',
                customClass: {
                    confirmButton: 'btn btn-danger'
                },
                buttonsStyling: false
            });
        });
    </script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

