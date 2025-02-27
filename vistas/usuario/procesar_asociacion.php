<?php
session_start();
include '../../Conexion/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_admin']) && isset($_POST['id_cuenta'])) {
    $id_admin = $_POST['id_admin'];
    $id_cuenta = $_POST['id_cuenta'];

    // Verificar si ya existe la asociación
    $sql = "SELECT * FROM cuenta_admin WHERE id_admin = ? AND id_cuenta = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_admin, $id_cuenta);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['response'] = ['status' => 'error', 'message' => 'Esta asociación ya existe.'];
    } else {
        // Insertar nueva asociación
        $sql = "INSERT INTO cuenta_admin (id_admin, id_cuenta) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id_admin, $id_cuenta);
        if ($stmt->execute()) {
            $_SESSION['response'] = ['status' => 'success', 'message' => 'Asociación creada correctamente.'];
        } else {
            $_SESSION['response'] = ['status' => 'error', 'message' => 'Error al asociar la cuenta.'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <script>
        <?php if (isset($_SESSION['response'])) : ?>
            Swal.fire({
                icon: '<?php echo $_SESSION['response']['status']; ?>',
                title: '<?php echo ucfirst($_SESSION['response']['status']); ?>',
                text: '<?php echo $_SESSION['response']['message']; ?>'
            }).then(() => {
                window.location.href = "../usuarios.php";
            });
            <?php unset($_SESSION['response']); ?>
        <?php endif; ?>
    </script>
</body>
</html>
