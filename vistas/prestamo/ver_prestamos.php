<?php
include '../../includes/header2.php';
include '../../includes/functions.php';

// Obtener todos los préstamos activos con toda la información asociada
$query = $pdo->query("
    SELECT p.id_prestamo, p.principal_prestamo, p.saldo_actual, p.fecha_desembolso, p.fecha_vencimiento, 
           p.estado, c.nombres AS cliente, u.nombre AS cobrador, r.nombre_ruta AS ruta
    FROM prestamo p
    JOIN cliente c ON p.id_cliente = c.id_cliente
    JOIN rol_user ru ON p.id_rol_user = ru.id_rol_user
    JOIN user u ON ru.id_user = u.id_user
    LEFT JOIN ruta r ON r.id_rol_user = u.id_user 
    WHERE p.estado = 'activo'
    ORDER BY p.fecha_desembolso DESC
");
$prestamos = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Préstamos Activos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Préstamos Activos</h1>

        <!-- Tabla de préstamos activos -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID Préstamo</th>
                        <th>Cliente</th>
                        <th>Cobrador</th>
                        <th>Ruta</th>
                        <th>Principal</th>
                        <th>Saldo Actual</th>
                        <th>Fecha Desembolso</th>
                        <th>Fecha Vencimiento</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prestamos as $prestamo): ?>
                        <tr>
                            <td><?= $prestamo['id_prestamo'] ?></td>
                            <td><?= $prestamo['cliente'] ?></td>
                            <td><?= $prestamo['cobrador'] ?></td>
                            <td><?= $prestamo['ruta'] ? $prestamo['ruta'] : '<span class="text-danger">Sin Ruta</span>' ?></td>
                            <td><?= number_format($prestamo['principal_prestamo'], 2) ?></td>
                            <td> <?= number_format($prestamo['saldo_actual'], 2) ?></td>
                            <td><?= $prestamo['fecha_desembolso'] ?></td>
                            <td><?= $prestamo['fecha_vencimiento'] ?></td>
                            <td>
                                <span class="badge bg-success"><?= ucfirst($prestamo['estado']) ?></span>
                            </td>
                            <td>
                                <a href="prestamo/detalle_prestamo.php?id=<?= $prestamo['id_prestamo'] ?>" class="btn btn-info btn-sm">Ver Detalles</a>
                                <a href="prestamo/editar_prestamo.php?id=<?= $prestamo['id_prestamo'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="prestamo/eliminar_prestamo.php?id=<?= $prestamo['id_prestamo'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este préstamo?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
