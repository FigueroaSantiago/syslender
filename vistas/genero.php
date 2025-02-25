<?php
include '../includes/header2.php';
include '../includes/functions.php';
$generos = getAllGeneros();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Géneros</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        body {
            background-color: #e9ecef;
        }
        .container {
            margin-top: 50px;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #343a40;
            text-align: center;
            margin-bottom: 30px;
        }
        .btn-custom {
            border-radius: 20px;
            transition: background-color 0.3s, transform 0.2s;
        }
        .btn-custom:hover {
            transform: scale(1.05);
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .table th {
            background-color: #333333;
            color: white;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .actions .btn {
            border-radius: 20px;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Listado de Géneros</h1>
    <a href="genero/agregar_genero.php" class="btn btn-success mb-3">Agregar Género</a>
    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Género</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($generos as $genero): ?>
            <tr>
                <td><?php echo $genero['id_genero']; ?></td>
                <td><?php echo $genero['genero']; ?></td>
                <td>
                    <a href="editar_genero.php?id=<?php echo $genero['id_genero']; ?>" class="btn btn-warning">Editar</a>
                    <a href="eliminar_genero.php?id=<?php echo $genero['id_genero']; ?>" class="btn btn-danger">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
