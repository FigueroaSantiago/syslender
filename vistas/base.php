<?php
include '../includes/header.php';
include '../includes/functions.php';

// Obtener todos los cobradores y sus asignaciones de bases
$query = $pdo->query("
    SELECT ru.id_rol_user, u.nombre, b.id_base, b.base, b.fecha
    FROM rol_user ru
    JOIN user u ON ru.id_user = u.id_user
    LEFT JOIN asignacion_base ab ON ru.id_rol_user = ab.id_cobrador
    LEFT JOIN base b ON ab.id_base = b.id_base
    WHERE ru.id_rol = 2 -- Filtrar solo los cobradores (suponiendo que el rol 2 es para cobradores)
    ORDER BY u.nombre ASC
");
$cobradores = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Bases de Cobradores</title>
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
        <h1 class="text-center mb-4">Gestión de Bases de Cobradores</h1>

        <!-- Tabla de cobradores y sus bases -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead >
                    <tr>
                        <th>Cobrador</th>

                        <th>Cantidad Base</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cobradores as $cobrador): ?>
                        <tr>
                            <td><?= $cobrador['nombre'] ?></td>

                            <td><?= $cobrador['base'] ? $cobrador['base'] : '<span class="text-danger">No asignada</span>' ?></td>
                            <td><?= $cobrador['fecha'] ? $cobrador['fecha'] : '<span class="text-danger">No asignada</span>' ?></td>
                            <td>
                                <?php if ($cobrador['id_base']): ?>
                                    <!-- Si el cobrador ya tiene una base asignada -->
                                    <a href="base/editar_base.php?id=<?= $cobrador['id_base'] ?>" class="btn btn-warning btn-sm">Editar Base</a>
                                    <a href="base/eliminar_base.php?id=<?= $cobrador['id_base'] ?>" class="btn btn-danger btn-sm">Eliminar Base</a>
                                <?php else: ?>
                                    <!-- Si el cobrador no tiene base asignada -->
                                    <a href="base/asignar_base.php?id_cobrador=<?= $cobrador['id_rol_user'] ?>" class="btn btn-success btn-sm">Asignar Base</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
