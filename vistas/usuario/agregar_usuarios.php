<?php
session_start();
include '../../includes/header2.php';
include '../../Conexion/conexion.php';
include '../../includes/functions.php';

$errores = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $contacto = $_POST['contacto'];
    $cedula = $_POST['cedula'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role_id = $_POST['role_id'];
    $user_role = $_SESSION['role_id']; // Rol del usuario que está creando el cobrador

    // 🚀 Si el usuario es un Administrador, usa la cuenta activa
    if ($user_role == 1) {
        $id_cuenta = $_SESSION['id_cuenta'];
    } elseif ($user_role == 18) {
        // 🚀 Si el usuario es un Gestor, obtiene la cuenta seleccionada
        $id_admin = $_POST['id_admin'];
        $id_cuenta = $_POST['id_cuenta'];

        // Validar que el administrador y la cuenta coincidan en la BD
        $sql = "SELECT id_cuenta FROM cuenta_admin WHERE id_admin = ? AND id_cuenta = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id_admin, $id_cuenta);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $_SESSION['response'] = ['status' => 'error', 'message' => 'El administrador y la cuenta seleccionados no coinciden.'];
            header("Location: agregar_usuario.php");
            exit();
        }
    } else {
        $_SESSION['response'] = ['status' => 'error', 'message' => 'No tienes permisos para crear cobradores.'];
        header("Location: agregar_usuario.php");
        exit();
    }

    // Verificar si la cédula ya está registrada
    $sql = "SELECT id_user FROM user WHERE cedula = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $cedula);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errores['cedula'] = 'Esta cédula ya está registrada como usuario.';
    } else {
        try {
            $conn->begin_transaction();

            // 1️⃣ Insertar usuario con id_cuenta
            $sql = "INSERT INTO user (nombre, apellido, contacto, cedula, password, id_cuenta) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $nombre, $apellido, $contacto, $cedula, $password, $id_cuenta);
            $stmt->execute();
            $user_id = $conn->insert_id;

            // 2️⃣ Asignar rol de Cobrador
            $sql = "INSERT INTO rol_user (id_user, id_rol) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $user_id, $role_id);
            $stmt->execute();

            $conn->commit();
            $_SESSION['response'] = ['status' => 'success', 'message' => 'Usuario cobrador registrado exitosamente.'];
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['response'] = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    header("Location: agregar_usuarios.php");
    exit();
}
?>




<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Usuario</title>
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
            <h1>Agregar Usuario</h1>
            <?php
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }


            // Verificar el rol del usuario que está creando el cobrador
            $user_role = $_SESSION['role_id'];

            // Si es un Gestor (18), obtenemos los administradores con sus cuentas
            if ($user_role == 18) {
                $sql = "SELECT u.id_user, u.nombre, c.id_cuenta, c.nombre as cuenta_nombre 
            FROM user u 
            INNER JOIN rol_user ru ON u.id_user = ru.id_user 
            INNER JOIN cuenta_admin ca ON u.id_user = ca.id_admin
            INNER JOIN cuentas c ON ca.id_cuenta = c.id_cuenta
            WHERE ru.id_rol = 1"; // Solo administradores
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $administradores = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            }
            ?>

            <form action="agregar_usuarios.php" method="POST">
                <h3>Datos del Usuario</h3>
                <label>Nombre:</label>
                <input type="text" name="nombre" required>

                <label>Apellido:</label>
                <input type="text" name="apellido" required>

                <label>Contacto:</label>
                <input type="text" name="contacto" required>

                <label>Cédula:</label>
                <input type="text" name="cedula" required>

                <label>Contraseña:</label>
                <input type="password" name="password" required>

                <label>Rol:</label>
                <select name="role_id" required>
                    <option value="2">Cobrador</option>
                </select>

                <?php if ($user_role == 18): ?>
                    <!-- 🚀 Si el usuario es un Gestor, debe elegir el Administrador y su cuenta -->
                    <label>Seleccionar Administrador:</label>
                    <select name="id_admin" id="id_admin" required>
                        <option value="">Seleccionar...</option>
                        <?php foreach ($administradores as $admin): ?>
                            <option value="<?= $admin['id_user'] ?>" data-cuenta="<?= $admin['id_cuenta'] ?>">
                                <?= $admin['nombre'] ?> - <?= $admin['cuenta_nombre'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Input oculto para almacenar el id_cuenta seleccionado -->
                    <input type="hidden" name="id_cuenta" id="id_cuenta">
                <?php else: ?>
                    <!-- 🚀 Si el usuario es Administrador, se asigna automáticamente la cuenta activa -->
                    <input type="hidden" name="id_cuenta" value="<?= $_SESSION['id_cuenta'] ?>">
                <?php endif; ?>

                <button type="submit">Guardar</button>
            </form>

            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const adminSelect = document.getElementById("id_admin");
                    if (adminSelect) {
                        adminSelect.addEventListener("change", function() {
                            const selectedOption = adminSelect.options[adminSelect.selectedIndex];
                            document.getElementById("id_cuenta").value = selectedOption.dataset.cuenta;
                        });
                    }
                });
            </script>



        </div>
    </div>

    <script>
        // Validaciones del lado del cliente
        document.querySelector('form').addEventListener('submit', function(event) {
            var nombre = document.getElementById('nombre').value.trim();
            var apellido = document.getElementById('apellido').value.trim();
            var cedula = document.getElementById('cedula').value;
            var contacto = document.getElementById('contacto').value;

            // Validación de nombres y apellidos (solo letras y espacios)
            var nombreApellidoRegex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+(?: [a-zA-ZáéíóúÁÉÍÓÚñÑ]+)*$/;


            if (!nombreApellidoRegex.test(nombre)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El nombre solo debe contener letras y espacios.'
                });
                event.preventDefault();
            }

            if (!nombreApellidoRegex.test(apellido)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El apellido solo debe contener letras y espacios.'
                });
                event.preventDefault();
            }

            // Validación de cédula (Ejemplo: solo números)
            if (!/^\d{7,10}$/.test(cedula)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La cédula debe ser un número válido entre 7 y 10 dígitos.'
                });
                event.preventDefault();
            }

            // Validación de contacto (Ejemplo: solo números y ciertos caracteres)
            if (!/^[0-9\s\+\-]+$/.test(contacto)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El contacto debe ser un número válido.'
                });
                event.preventDefault();
            }
        });

        <?php if (isset($_SESSION['response'])) : ?>
            Swal.fire({
                icon: '<?php echo $_SESSION['response']['status']; ?>',
                title: '<?php echo ucfirst($_SESSION['response']['status']); ?>',
                text: '<?php echo $_SESSION['response']['message']; ?>'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../usuarios.php'; // Redirigir a una página específica
                }
            });
        <?php unset($_SESSION['response']);
        endif; ?>
    </script>
</body>

</html>