<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

include '../../includes/header.php';
include '../../includes/functions.php';
include '../../Conexion/conexion.php';

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $errors[] = 'Todos los campos son obligatorios.';
    } else {
        if (strlen($new_password) < 8) {
            $errors[] = 'La nueva contraseña debe tener al menos 8 caracteres.';
        }
        
        if (!preg_match('/[A-Za-z]/', $new_password) || !preg_match('/[0-9]/', $new_password) ) {
            $errors[] = 'La nueva contraseña debe incluir letras y números.';
        }

        if ($new_password != $confirm_password) {
            $errors[] = 'Las contraseñas no coinciden.';
        }

        $query = $conn->prepare('SELECT password FROM user WHERE id_user = ?');
        $query->bind_param('i', $user_id);
        $query->execute();
        $result = $query->get_result();
        $user = $result->fetch_assoc();

        if (!password_verify($current_password, $user['password'])) {
            $errors[] = 'La contraseña actual es incorrecta.';
        }

        if (empty($errors)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = $conn->prepare('UPDATE user SET password = ? WHERE id_user = ?');
            $update_query->bind_param('si', $hashed_password, $user_id);
            if ($update_query->execute()) {
                $success = "¡Contraseña actualizada con éxito!";
            } else {
                $errors[] = 'Error al actualizar la contraseña. Inténtalo de nuevo.';
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
    <title>Cambiar Contraseña</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <!-- FontAwesome para íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- SweetAlert2 CSS and JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            border: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-success {
            width: 100%;
            border-radius: 25px;
            padding: 10px 30px;
            font-size: 1rem;
            transition: background-color 0.3s, transform 0.2s;
        }
    </style>
</head>
<body>

<div class="container">
    
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0 text-center">Cambiar Contraseña</h4>
                </div>
                <div class="card-body">
                    <form action="cambiar_contrasena.php" method="POST" onsubmit="return validateForm()">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Contraseña Actual</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Actualizar Contraseña</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function validateForm() {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        if (newPassword.length < 8) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'La nueva contraseña debe tener al menos 8 caracteres.'
            });
            return false;
        }

        if (!/[A-Za-z]/.test(newPassword) || !/[0-9]/.test(newPassword) ) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'La nueva contraseña debe incluir letras y números. '
            });
            return false;
        }

        if (newPassword !== confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Las contraseñas no coinciden.'
            });
            return false;
        }

        return true;
    }

    <?php if (!empty($success)): ?>
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: '<?php echo $success; ?>'
        });
    <?php elseif (!empty($errors)): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: '<?php echo implode("<br>", $errors); ?>'
        });
    <?php endif; ?>
</script>

<!-- Bootstrap JS -->

<script src="../../assets/js/bootstrap.min.js"></script>

</body>
</html>
