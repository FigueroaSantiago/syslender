<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_POST['id_cuenta'])) {
    header("Location: login.php");
    exit();
}

$_SESSION['id_cuenta'] = $_POST['id_cuenta'];
unset($_SESSION['cuentas']); // Limpiar las cuentas temporales

header("Location: ../dashboard.php");
exit();
?>
