<?php
session_start();
include '../../includes/header2.php';
include '../../includes/functions.php';
include '../../includes/db.php'; // Asegúrate de que esta es tu conexión PDO

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_cliente = $_POST['id_cliente'];
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $id_genero = $_POST['id_genero'];
    $direccion_casa = $_POST['direccion_casa'];
    $direccion_negocio = $_POST['direccion_negocio'];
    $telefono = $_POST['telefono'];
    $cedula = $_POST['cedula'];

    // Verificar si la cédula ya está registrada en otro cliente
    $sql = "SELECT id_cliente FROM cliente WHERE cedula = ? AND id_cliente != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $cedula, $id_cliente);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // La cédula ya está registrada
        $_SESSION['mensaje_error'] = "La cédula ingresada ya está registrada con otro cliente.";
        header("Location: editar_cliente.php?id=$id_cliente");
        exit();
    }

    // Llamada a la función para actualizar el cliente
    actualizarCliente($id_cliente, $nombres, $apellidos, $id_genero, $direccion_casa, $direccion_negocio, $telefono, $cedula);

    // Almacena el mensaje de éxito en la sesión
    $_SESSION['mensaje_exito'] = "Cliente editado correctamente.";

    // Redirige de vuelta a la página de edición
    header("Location: editar_cliente.php?id=$id_cliente");
    exit();
}

// Obtiene el cliente para editar
$id_cliente = $_GET['id'];
$cliente = getClienteById($id_cliente);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
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
        <h1 class="mb-4">Editar Cliente</h1>
        
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
                    window.location.href = '../clientes.php';
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

        <form action="editar_cliente.php" method="POST" id="editClientForm">
            <input type="hidden" name="id_cliente" value="<?php echo $cliente['id_cliente']; ?>">
            
            <!-- Nombres -->
            <div class="form-group">
                <label for="nombres">Nombres</label>
                <input type="text" id="nombres" name="nombres" class="form-control" value="<?php echo $cliente['nombres']; ?>" required>
            </div>

            <!-- Apellidos -->
            <div class="form-group">
                <label for="apellidos">Apellidos</label>
                <input type="text" id="apellidos" name="apellidos" class="form-control" value="<?php echo $cliente['apellidos']; ?>" required>
            </div>

            <!-- Género -->
            <div class="form-group">
                <label for="id_genero">Género</label>
                <select id="id_genero" name="id_genero" class="form-control" required>
                    <option value="1" <?php echo $cliente['id_genero'] == 1 ? 'selected' : ''; ?>>Masculino</option>
                    <option value="2" <?php echo $cliente['id_genero'] == 2 ? 'selected' : ''; ?>>Femenino</option>
                </select>
            </div>

            <!-- Dirección Casa -->
            <div class="form-group">
                <label for="direccion_casa">Dirección de Casa</label>
                <input type="text" id="direccion_casa" name="direccion_casa" class="form-control" value="<?php echo $cliente['direccion_casa']; ?>" required>
            </div>

            <!-- Dirección Negocio -->
            <div class="form-group">
                <label for="direccion_negocio">Dirección de Negocio</label>
                <input type="text" id="direccion_negocio" name="direccion_negocio" class="form-control" value="<?php echo $cliente['direccion_negocio']; ?>" required>
            </div>

            <!-- Teléfono -->
            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" id="telefono" name="telefono" class="form-control" value="<?php echo $cliente['telefono']; ?>" required>
            </div>

            <!-- Cédula -->
            <div class="form-group">
                <label for="cedula">Cédula</label>
                <input type="text" id="cedula" name="cedula" class="form-control" value="<?php echo $cliente['cedula']; ?>" required>
            </div>

            <button type="submit" class="btn btn-success btn-block">Actualizar</button>
        </form>
    </div>
    </div>
    <script>
        document.getElementById('editClientForm').addEventListener('submit', function(event) {
            var nombres = document.getElementById('nombres').value;
            var apellidos = document.getElementById('apellidos').value;
            var cedula = document.getElementById('cedula').value;

            var regex = /^[A-Za-z\s]+$/;
            if (!regex.test(nombres) || !regex.test(apellidos)) {
                event.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'Los nombres y apellidos solo deben contener letras y espacios.',
                });
                return false;
            }

            if (!/^\d+$/.test(cedula)) {
                event.preventDefault();
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
