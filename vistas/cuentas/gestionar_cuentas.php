<?php
session_start();
include '../../Conexion/conexion.php';


// Verificar si el usuario es Gestor (rol 18)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 18) {
    die("Acceso denegado.");
}

// Actualizar la fecha de vencimiento y estado si se envía el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_cuenta'])) {
    $id_cuenta = $_POST['id_cuenta'];
    $nueva_fecha = $_POST['fecha_vencimiento'];
    $estado = $_POST['estado'];

    // Validar la fecha ingresada
    if (strtotime($nueva_fecha) < strtotime(date("Y-m-d"))) {
        die("⚠ No puedes asignar una fecha vencida.");
    }

    // Actualizar la cuenta
    $sql = "UPDATE cuentas SET fecha_vencimiento = ?, estado = ? WHERE id_cuenta = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nueva_fecha, $estado, $id_cuenta);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Cuenta actualizada correctamente.'); window.location.href='gestionar_cuentas.php';</script>";
    } else {
        echo "<script>alert('❌ Error al actualizar la cuenta.');</script>";
    }
}

// Obtener la lista de cuentas
$sql = "SELECT id_cuenta, nombre, fecha_vencimiento, estado FROM cuentas";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Cuentas</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Gestión de Cuentas</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Fecha Vencimiento</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?= $row['id_cuenta'] ?></td>
                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                        <td><?= $row['fecha_vencimiento'] ?></td>
                        <td><?= ucfirst($row['estado']) ?></td>
                        <td>
                            <form method="POST" action="gestionar_cuentas.php">
                                <input type="hidden" name="id_cuenta" value="<?= $row['id_cuenta'] ?>">
                                <input type="date" name="fecha_vencimiento" value="<?= $row['fecha_vencimiento'] ?>" required>
                                <select name="estado">
                                    <option value="activa" <?= $row['estado'] == "activa" ? "selected" : "" ?>>Activa</option>
                                    <option value="inactiva" <?= $row['estado'] == "inactiva" ? "selected" : "" ?>>Inactiva</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">Actualizar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
