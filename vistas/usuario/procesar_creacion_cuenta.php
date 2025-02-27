<?php
session_start();
include '../../Conexion/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cod_cuenta']) && isset($_POST['nombre_cuenta'])) {
    $cod_cuenta = $_POST['cod_cuenta'];
    $nombre_cuenta = $_POST['nombre_cuenta'];
    $empresa = $_POST['empresa'];
    $cod_sirc = $_POST['cod_sirc'];
    $fecha_vencimiento = $_POST['fecha_vencimiento'];
    $estado = "activa"; // Estado por defecto siempre activo

    $conn->begin_transaction();
    
    try {
        // 🚀 Crear la nueva cuenta
        $sql = "INSERT INTO cuentas (cod_cuenta, nombre, empresa, cod_sirc, fecha_creacion, fecha_vencimiento, estado) 
                VALUES (?, ?, ?, ?, CURDATE(), ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $cod_cuenta, $nombre_cuenta, $empresa, $cod_sirc, $fecha_vencimiento, $estado);
        $stmt->execute();
        $id_cuenta = $conn->insert_id;

        // 🚀 Obtener el ID del administrador recién creado
        if (!isset($_SESSION['id_admin_creado'])) {
            throw new Exception("No hay un administrador para asociar.");
        }
        $id_admin = $_SESSION['id_admin_creado'];

        // 🚀 Asociar la cuenta con el administrador
        $sql = "INSERT INTO cuenta_admin (id_admin, id_cuenta) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id_admin, $id_cuenta);
        $stmt->execute();

        // 🚀 Confirmar transacción
        $conn->commit();

        $_SESSION['response'] = ['status' => 'success', 'message' => 'Cuenta creada y asignada al administrador correctamente.'];
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['response'] = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
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
                window.location.href = "../usuarios.php"; // 🔄 Redirige de vuelta a la lista de usuarios
            });
            <?php unset($_SESSION['response']); ?>
        <?php endif; ?>
    </script>
</body>
</html>
