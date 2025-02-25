
<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';
include '../includes/functions.php';


// Verificar si la sesión está iniciada y el usuario tiene rol de administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    die('Acceso denegado.');
}

// Consultar estadísticas generales
$fecha_hoy = date('Y-m-d');
$stmt_resumen = $pdo->prepare("
    SELECT 
        COUNT(*) AS total_cuadres,
        SUM(CASE WHEN saldo_final >= 0 THEN 1 ELSE 0 END) AS balanceados,
        SUM(CASE WHEN saldo_final < 0 THEN 1 ELSE 0 END) AS desbalanceados
    FROM cuadres_diarios
    WHERE DATE(fecha) = ?
");
$stmt_resumen->execute([$fecha_hoy]);
$resumen = $stmt_resumen->fetch(PDO::FETCH_ASSOC);

// Obtener listado de cuadres diarios
$stmt_cuadres = $pdo->prepare("
    SELECT 
        cd.id_cuadre,
        u.nombre,
        u.apellido,
        cd.fecha,
        cd.saldo_final,
        cd.estado,
        cd.observaciones
    FROM cuadres_diarios cd
    JOIN rol_user ru ON cd.id_cobrador = ru.id_rol_user
    JOIN user u ON ru.id_user = u.id_user
    ORDER BY cd.fecha DESC
");
$stmt_cuadres->execute();
$cuadres = $stmt_cuadres->fetchAll(PDO::FETCH_ASSOC);

// Obtener movimientos de base
$stmt_bases = $pdo->prepare("
    SELECT 
        b.id_base,
        b.base AS saldo_base,
        ab.fecha_asignacion,
        CONCAT(u.nombre, ' ', u.apellido) AS asignado_a
    FROM base b
    LEFT JOIN asignacion_base ab ON b.id_base = ab.id_base
    LEFT JOIN rol_user ru ON ab.id_cobrador = ru.id_rol_user
    LEFT JOIN user u ON ru.id_user = u.id_user
    ORDER BY b.id_base
");
$stmt_bases->execute();
$bases = $stmt_bases->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista de Administrador</title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Vista de Administrador</h1>
    
    <!-- Resumen General -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">Resumen del Día</div>
        <div class="card-body">
            <p><strong>Fecha:</strong> <?= date('d/m/Y') ?></p>
            <p><strong>Total Cuadres:</strong> <?= $resumen['total_cuadres'] ?></p>
            <p><strong>Balanceados:</strong> <?= $resumen['balanceados'] ?></p>
            <p><strong>Desbalanceados:</strong> <?= $resumen['desbalanceados'] ?></p>
        </div>
    </div>

    <!-- Listado de Cuadres -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Listado de Cuadres</div>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cobrador</th>
                        <th>Fecha</th>
                        <th>Saldo Final</th>
                        <th>Estado</th>
                        <th>Observaciones</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cuadres as $cuadre): ?>
                        <tr>
                            <td><?= $cuadre['id_cuadre'] ?></td>
                            <td><?= $cuadre['nombre'] . ' ' . $cuadre['apellido'] ?></td>
                            <td><?= date('d/m/Y', strtotime($cuadre['fecha'])) ?></td>
                            <td class="<?= $cuadre['saldo_final'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= number_format($cuadre['saldo_final'], 2) ?>
                            </td>
                            <td><?= ucfirst($cuadre['estado']) ?></td>
                            <td><?= $cuadre['observaciones'] ?></td>
                            <td>
                                <a href="detalles_cuadre.php?id=<?= $cuadre['id_cuadre'] ?>" class="btn btn-sm btn-info">Ver Detalles</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Movimientos de Base -->
    <div class="card">
        <div class="card-header bg-secondary text-white">Movimientos de Bases</div>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID Base</th>
                        <th>Saldo</th>
                        <th>Asignado a</th>
                        <th>Fecha de Asignación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bases as $base): ?>
                        <tr>
                            <td><?= $base['id_base'] ?></td>
                            <td><?= number_format($base['saldo_base'], 2) ?></td>
                            <td><?= $base['asignado_a'] ?? 'No asignado' ?></td>
                            <td><?= $base['fecha_asignacion'] ? date('d/m/Y', strtotime($base['fecha_asignacion'])) : 'N/A' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
