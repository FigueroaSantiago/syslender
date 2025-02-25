<?php
include '../../includes/header2.php';
include '../../includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_genero = $_POST['nombre_genero'];
    agregarGenero($nombre_genero);
    header("Location: ../genero.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Género</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Agregar Género</h1>
    <form action="agregar_genero.php" method="POST">
        <div class="form-group">
            <label for="nombre_genero">Género</label>
            <input type="text" id="nombre_genero" name="nombre_genero" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
</body>
</html>
