<?php
include '../../includes/header2.php';
include '../../includes/functions.php';

// Obtener las asignaciones de bases
$query = $pdo->query("
    SELECT ab.id_asignacion, u.nombre AS cobrador, b.base, ab.fecha_asignacion 
    FROM asignacion_base ab
    JOIN rol_user ru ON ab.id_cobrador = ru.id_rol_user
    JOIN user u ON ru.id_user = u.id_user
    JOIN base b ON ab.id_base = b.id_base
    ORDER BY ab.fecha_asignacion DESC
");
$asignaciones = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignaciones de Bases</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Asignaciones de Bases a Cobradores</h1>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID Asignación</th>
                    <th>Cobrador</th>
                    <th>Base</th>
                    <th>Fecha de Asignación</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($asignaciones as $asignacion): ?>
                    <tr>
                        <td><?= $asignacion['id_asignacion'] ?></td>
                        <td><?= $asignacion['cobrador'] ?></td>
                        <td><?= $asignacion['base'] ?></td>
                        <td><?= $asignacion['fecha_asignacion'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
