<?php
session_start();

// Verificar si hay múltiples cuentas para seleccionar
if (!isset($_SESSION['cuentas_admin']) || empty($_SESSION['cuentas_admin'])) {
    header("Location: dashboard.php"); // Si no hay cuentas, redirigir al dashboard
    exit();
}

$cuentas = $_SESSION['cuentas_admin'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Cuenta</title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2>Seleccionar Cuenta</h2>
    <form action="asignar_cuenta_admin.php" method="post">
        <div class="form-group">
            <label>Selecciona una cuenta:</label>
            <select name="id_cuenta" class="form-control" required>
                <?php foreach ($cuentas as $cuenta) { ?>
                    <option value="<?= htmlspecialchars($cuenta) ?>">Cuenta ID: <?= htmlspecialchars($cuenta) ?></option>
                <?php } ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Usar esta Cuenta</button>
    </form>
</div>
</body>
</html>
