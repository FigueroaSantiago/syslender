<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_cuenta'])) {
    $_SESSION['id_cuenta'] = $_POST['id_cuenta'];
    header("Location: dashboard.php"); // Redirigir después de seleccionar
    exit();
}

header("Location: seleccionar_cuenta_admin.php");
exit();
