<?php
include '../../includes/header2.php';
include '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $base = $_POST['base'];
    $fecha = $_POST['fecha'];

    $stmt = $pdo->prepare("INSERT INTO base (base, fecha) VALUES (?, ?)");
    $stmt->execute([$base, $fecha]);

    header('Location: ../base.php');
}
