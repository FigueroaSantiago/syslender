<?php
session_start();
include '../includes/header.php';
include '../includes/functions.php';

// Redirige si el usuario no está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Filtros
$estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$usuario = isset($_GET['usuario']) ? $_GET['usuario'] : '';

// Construcción de la consulta SQL con filtros
$query = "SELECT g.id_gastos, g.monto, g.fecha_creacion, g.estado, g.comentarios, 
                 u.nombre AS creado_por, t.descripcion AS tipo_gasto
          FROM gastos g
          JOIN rol_user ru ON g.id_rol_user = ru.id_rol_user
          JOIN user u ON ru.id_user = u.id_user
          JOIN tipo_gastos t ON g.id_tipo_gasto = t.id_tipo_gasto
          WHERE g.estado LIKE :estado AND u.nombre LIKE :usuario";

$stmt = $pdo->prepare($query);
$stmt->execute([
    ':estado' => "%$estado%",
    ':usuario' => "%$usuario%"
]);

$reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes de Gastos</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center">Reportes de Gastos</h1>
    <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Listado de Gastos</h1>
            <a href="gastos/panel_aprobacion.php" class="btn btn-success">Solicitudes de Aprobación</a>
        </div>

    <!-- Filtros de búsqueda -->
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-6">
                <select name="estado" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" <?= $estado == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="aprobado" <?= $estado == 'aprobado' ? 'selected' : '' ?>>Aprobado</option>
                    <option value="rechazado" <?= $estado == 'rechazado' ? 'selected' : '' ?>>Rechazado</option>
                </select>
            </div>
            <div class="col-md-6">
                <input type="text" name="usuario" class="form-control" placeholder="Buscar por usuario" value="<?= $usuario ?>">
            </div>
            <div class="col-md-12 mt-3 text-center">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </div>
    </form>

    <!-- Tabla de reportes con paginación -->
    <div class="table-responsive">
        <table id="tablaReportes" class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID Gasto</th>
                    <th>Tipo de Gasto</th>
                    <th>Monto</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Comentarios</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($reportes): ?>
                    <?php foreach ($reportes as $reporte): ?>
                        <tr>
                            <td><?= $reporte['id_gastos']; ?></td>
                            <td><?= $reporte['tipo_gasto']; ?></td>
                            <td><?= $reporte['monto']; ?></td>
                            <td><?= $reporte['fecha_creacion']; ?></td>
                            <td><?= ucfirst($reporte['estado']); ?></td>
                            <td><?= $reporte['comentarios']; ?></td>
                            <td><?= $reporte['creado_por']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No se encontraron resultados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Gráfica de gastos por estado -->
    <div class="row mt-5">
    <div class="col-12 d-flex justify-content-center">
        <div style="width: 400px; height: 300px;">
            <canvas id="reporteGrafico"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tablaReportes').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es_es.json"
            }
        });
    });

    var ctx = document.getElementById('reporteGrafico').getContext('2d');
    var reporteGrafico = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: [ 'Aprobado', 'Pendiente','rechados'],
            datasets: [{
                data: [
                    <?php
                    // Contar gastos por estado
                    $stmt = $pdo->query("SELECT estado, COUNT(*) AS cantidad FROM gastos GROUP BY estado");
                    $estados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($estados as $estado) {
                        echo $estado['cantidad'] . ',';
                    }
                    ?>
                ],
                backgroundColor: ['#28a745','#ffc107','#dc3545' ]
            }]
        },
        options: {
            responsive: true
        }
    });
</script>
</body>
</html>
