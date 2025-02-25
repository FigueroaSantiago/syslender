<?php
include '../includes/header.php';
include '../includes/functions.php';

session_start();

// Redirige si el usuario no está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$id_gasto = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    eliminarGasto($id_gasto);
    header('Location: listar_gastos.php');
    exit();
}

$gasto = getGastoById($id_gasto);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Gasto</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Eliminar Gasto</h1>
        <p>¿Estás seguro de que deseas eliminar el gasto de <strong><?php echo $gasto['tipo_gasto']; ?></strong> por <strong><?php echo $gasto['precio']; ?></strong>?</p>
        <form action="eliminar_gasto.php?id=<?php echo $id_gasto; ?>" method="POST">
            <button type="submit" class="btn btn-danger">Eliminar</button>
            <a href="listar_gastos.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
