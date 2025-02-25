<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['cuentas'])) {
    header("Location: login.php");
    exit();
}

$cuentas = $_SESSION['cuentas'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Seleccionar Cuenta</title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container">
    <h2>Seleccionar Cuenta</h2>
    <form action="asignar_cuenta.php" method="post">
        <div class="form-group">
            <label>Selecciona una cuenta:</label>
            <select name="id_cuenta" class="form-control" required>
                <?php foreach ($cuentas as $cuenta) { ?>
                    <option value="<?= $cuenta['id_cuenta'] ?>"><?= $cuenta['nombre'] ?></option>
                <?php } ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Iniciar con esta Cuenta</button>
    </form>
</div>
</body>
</html>
