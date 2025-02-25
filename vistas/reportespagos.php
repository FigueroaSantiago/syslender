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
$tipo_pago = isset($_GET['tipo_pago']) ? $_GET['tipo_pago'] : '';
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';

// Construcción de la consulta SQL con filtros
$query = "SELECT id_pago, id_prestamo, monto, fecha, tipo_pago, observacion
          FROM pagos
          WHERE tipo_pago LIKE :tipo_pago AND DATE(fecha) LIKE :fecha";
$stmt = $pdo->prepare($query);
$stmt->execute([
    ':tipo_pago' => "%$tipo_pago%",
    ':fecha' => "%$fecha%"
]);

$reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes de Pagos</title>
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
    <h1 class="text-center">Reportes de Pagos</h1>

    <!-- Filtros de búsqueda -->
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-6">
                <select name="tipo_pago" class="form-select">
                    <option value="">Todos los tipos</option>
                    <option value="pago" <?= $tipo_pago == 'pago' ? 'selected' : '' ?>>Pago</option>
                    <option value="no_pago" <?= $tipo_pago == 'no_pago' ? 'selected' : '' ?>>No Pago</option>
                    <option value="abono" <?= $tipo_pago == 'abono' ? 'selected' : '' ?>>Abono</option>
                </select>
            </div>
            <div class="col-md-6">
                <input type="date" name="fecha" class="form-control" value="<?= $fecha ?>">
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
                    <th>ID Pago</th>
                    <th>ID Préstamo</th>
                    <th>Monto</th>
                    <th>Fecha</th>
                    <th>Tipo de Pago</th>
                    <th>Observación</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($reportes): ?>
                    <?php foreach ($reportes as $reporte): ?>
                        <tr>
                            <td><?= $reporte['id_pago']; ?></td>
                            <td><?= $reporte['id_prestamo']; ?></td>
                            <td><?= $reporte['monto']; ?></td>
                            <td><?= $reporte['fecha']; ?></td>
                            <td><?= ucfirst($reporte['tipo_pago']); ?></td>
                            <td><?= $reporte['observacion']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No se encontraron resultados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Gráfica de pagos por tipo -->
    <div class="row mt-5">
        <div class="col-12">
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
        type: 'bar',
        data: {
            labels: ['Pago', 'No Pago', 'Abono'],
            datasets: [{
                data: [
                    <?php
                    // Contar pagos por tipo
                    $stmt = $pdo->query("SELECT tipo_pago, COUNT(*) AS cantidad FROM pagos GROUP BY tipo_pago");
                    $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($tipos as $tipo) {
                        echo $tipo['cantidad'] . ',';
                    }
                    ?>
                ],
                backgroundColor: ['#28a745', '#17a2b8', '#ffc107']
            }]
        },
        options: {
            responsive: true
        }
    });
</script>
</body>
</html>
