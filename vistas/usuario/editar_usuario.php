<?php
session_start();
include '../../includes/header2.php';
include '../../includes/functions.php';
include '../../Conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = $_POST['id_user'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $contacto = $_POST['contacto'];
    $cedula = $_POST['cedula'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Verificar si la cédula ya está registrada
    $sql = "SELECT id_user FROM user WHERE cedula = ? AND id_user != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $cedula, $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // La cédula ya está registrada
        $_SESSION['mensaje_error'] = "La cédula ingresada ya está registrada con otro usuario.";
        header("Location: editar_usuario.php?id=$id_user");
        exit();
    }

    // Llamada a la función para actualizar el usuario
    actualizarUsuario($id_user, $nombre, $apellido, $contacto, $cedula, $password);

    // Almacena el mensaje de éxito en la sesión
    $_SESSION['mensaje_exito'] = "Usuario editado correctamente.";

    // Redirige de vuelta a la página de edición
    header("Location: editar_usuario.php?id=$id_user");
    exit();
}

// Obtiene el usuario para editar
$id_user = $_GET['id'];
$usuario = getUsuarioById($id_user);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        <h1 class="mb-4">Editar Usuario</h1>
        
        <?php
        // Muestra la alerta de éxito si existe
        if (isset($_SESSION['mensaje_exito'])) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: '{$_SESSION['mensaje_exito']}',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    // Recarga la página para limpiar el mensaje
                    window.location.href = '../usuarios.php';
                });
            </script>";
            unset($_SESSION['mensaje_exito']); // Borra el mensaje después de mostrarlo
        }
        
        // Muestra la alerta de error si existe
        if (isset($_SESSION['mensaje_error'])) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: '{$_SESSION['mensaje_error']}',
                    confirmButtonText: 'Aceptar'
                });
            </script>";
            unset($_SESSION['mensaje_error']); // Borra el mensaje de error después de mostrarlo
        }
        ?>

        <form action="editar_usuario.php" method="POST" id="editUserForm">
            <input type="hidden" name="id_user" value="<?php echo $usuario['id_user']; ?>">
            
            <!-- Nombre -->
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo $usuario['nombre']; ?>" required>
            </div>

            <!-- Apellido -->
            <div class="form-group">
                <label for="apellido">Apellido</label>
                <input type="text" id="apellido" name="apellido" class="form-control" value="<?php echo $usuario['apellido']; ?>" required>
            </div>

            <!-- Contacto -->
            <div class="form-group">
                <label for="contacto">Contacto</label>
                <input type="text" id="contacto" name="contacto" class="form-control" value="<?php echo $usuario['contacto']; ?>" required>
            </div>

            <!-- Cédula -->
            <div class="form-group">
                <label for="cedula">Cédula</label>
                <input type="text" id="cedula" name="cedula" class="form-control" value="<?php echo $usuario['cedula']; ?>" required>
            </div>

            <!-- Contraseña -->
            <div class="form-group">
                <label for="contrasena">Contraseña</label>
                <input type="password" id="contrasena" name="password" class="form-control">
            </div>

            <button type="submit" class="btn btn-success">Actualizar</button>
        </form>
    </div>
    </div>

    <script>
        // Validación de los campos en el lado del cliente con JavaScript
        document.getElementById('editUserForm').addEventListener('submit', function(event) {
            var nombre = document.getElementById('nombre').value;
            var apellido = document.getElementById('apellido').value;
            var cedula = document.getElementById('cedula').value;

            // Validar que no haya caracteres especiales en los nombres
            var regex = /^[A-Za-z\s]+$/;
            if (!regex.test(nombre) || !regex.test(apellido)) {
                event.preventDefault(); // Prevenir el envío del formulario
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'El nombre y apellido solo pueden contener letras y espacios.',
                });
                return false;
            }

            // Validar la cédula (esto debería ser una validación más específica, por ejemplo, para formato)
            if (!/^\d+$/.test(cedula)) {
                event.preventDefault(); // Prevenir el envío del formulario
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'La cédula solo puede contener números.',
                });
                return false;
            }
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
