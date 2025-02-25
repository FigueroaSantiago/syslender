<?php
session_start();
include '../../includes/header.php';
include '../../includes/db.php';
include '../../includes/functions.php';

// Verificar si la sesión está iniciada y el rol es el correcto
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) { // Supongo que el rol del administrador es 1
    die('Acceso denegado.');
}

// Obtener todos los cuadres subidos por los cobradores
$stmt_cuadres = $pdo->prepare("
    SELECT 
        cd.id_cuadre, 
        cd.fecha, 
        CONCAT(u.nombre, ' ', u.apellido) AS cobrador, 
        cd.base_inicial, 
        cd.total_pagos, 
        cd.total_gastos, 
        cd.total_prestamos, 
        cd.saldo_final, 
        cd.estado,
        cd.observaciones
    FROM 
        cuadres_diarios cd
    INNER JOIN 
        rol_user ru ON cd.id_cobrador = ru.id_rol_user
    INNER JOIN 
        user u ON ru.id_user = u.id_user
    ORDER BY 
        cd.fecha DESC
");
$stmt_cuadres->execute();
$cuadres = $stmt_cuadres->fetchAll(PDO::FETCH_ASSOC);


$fecha_actual = date('Y-m-d');

// Preparar la consulta
$stmt_bases = $pdo->prepare("
    SELECT 
        'Asignación' AS tipo, 
        ab.fecha_asignacion AS fecha, 
        CONCAT('Asignación de base: ', b.base, ' al cobrador ', u.nombre) AS descripcion
    FROM 
        asignacion_base ab
    INNER JOIN 
        base b ON ab.id_base = b.id_base
    INNER JOIN 
        rol_user ru ON ab.id_cobrador = ru.id_rol_user
    INNER JOIN 
        user u ON ru.id_user = u.id_user
    WHERE 
        ab.id_cobrador = :id_cobrador AND DATE(ab.fecha_asignacion) = :fecha
    
    UNION ALL
    
    SELECT 
        'Finalización' AS tipo, 
        ab.fecha_finalizacion AS fecha, 
        CONCAT('Finalización de asignación de base: ', b.base, ' del cobrador ', u.nombre) AS descripcion
    FROM 
        asignacion_base ab
    INNER JOIN 
        base b ON ab.id_base = b.id_base
    INNER JOIN 
        rol_user ru ON ab.id_cobrador = ru.id_rol_user
    INNER JOIN 
        user u ON ru.id_user = u.id_user
    WHERE 
        ab.id_cobrador = :id_cobrador AND DATE(ab.fecha_finalizacion) = :fecha
    ORDER BY 
        fecha DESC
");

// Ejecutar la consulta con valores reales
$stmt_bases->execute([
    
    ':fecha' => $fecha_actual
]);

$bases = $stmt_bases->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f7fa;
            font-family: 'Arial', sans-serif;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .table thead {
            background-color: #343a40;
            color: white;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">Panel de Administración</h1>

    <!-- Resumen de Bases y Movimientos -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            Resumen de Movimientos de Bases
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Cobrador</th>
                    <th>ID Base</th>
                    <th>Monto Base</th>
                    <th>Fecha Asignación</th>
                    <th>Fecha Finalización</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($bases): ?>
                    <?php foreach ($bases as $base): ?>
                        <tr>
                            <td><?= $base['cobrador'] ?></td>
                            <td><?= $base['id_base'] ?></td>
                            <td><?= number_format($base['base'], 2) ?></td>
                            <td><?= $base['fecha_asignacion'] ?></td>
                            <td><?= $base['fecha_finalizacion'] ?: 'En uso' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No hay movimientos de bases registrados.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Cuadres Subidos por Cobradores -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            Cuadres Subidos por Cobradores
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Cobrador</th>
                    <th>Base Inicial</th>
                    <th>Total Pagos</th>
                    <th>Total Gastos</th>
                    <th>Total Préstamos</th>
                    <th>Saldo Final</th>
                    <th>Estado</th>
                    <th>Observaciones</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($cuadres): ?>
                    <?php foreach ($cuadres as $cuadre): ?>
                        <tr>
                            <td><?= $cuadre['fecha'] ?></td>
                            <td><?= $cuadre['cobrador'] ?></td>
                            <td><?= number_format($cuadre['base_inicial'], 2) ?></td>
                            <td><?= number_format($cuadre['total_pagos'], 2) ?></td>
                            <td><?= number_format($cuadre['total_gastos'], 2) ?></td>
                            <td><?= number_format($cuadre['total_prestamos'], 2) ?></td>
                            <td class="<?= $cuadre['saldo_final'] < 0 ? 'text-danger' : 'text-success' ?>">
                                <?= number_format($cuadre['saldo_final'], 2) ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= $cuadre['estado'] === 'aprobado' ? 'success' : ($cuadre['estado'] === 'rechazado' ? 'danger' : 'warning') ?>">
                                    <?= ucfirst($cuadre['estado']) ?>
                                </span>
                            </td>
                            <td><?= $cuadre['observaciones'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">No hay cuadres registrados.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>
