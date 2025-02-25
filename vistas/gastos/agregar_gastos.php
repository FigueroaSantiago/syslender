<?php
session_start();

include '../../includes/header.php';
include '../../includes/functions.php';



// Redirige si el usuario no está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipo_gasto = $_POST['tipo_gasto'];
    $precio = $_POST['precio'];
    agregarGasto($tipo_gasto, $precio);
    header('Location: listar_gastos.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Gasto</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Agregar Gasto</h1>
        <form action="agregar_gasto.php" method="POST">
            <div class="form-group">
                <label for="tipo_gasto">Tipo de Gasto</label>
                <input type="text" id="tipo_gasto" name="tipo_gasto" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="precio">Precio</label>
                <input type="number" id="precio" name="precio" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
