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
$cliente = isset($_GET['cliente']) ? $_GET['cliente'] : '';
$estado = isset($_GET['estado']) ? $_GET['estado'] : '';

// Construcción de la consulta SQL con filtros
$query = "SELECT p.id_prestamo, c.nombres AS cliente, p.inicio_fecha, p.saldo_actual, p.estado
          FROM prestamo p 
          JOIN cliente c ON p.id_cliente = c.id_cliente 
          WHERE c.nombres LIKE :cliente AND p.estado LIKE :estado";
$stmt = $pdo->prepare($query);
$stmt->execute([
    ':cliente' => "%$cliente%",
    ':estado' => "%$estado%"
]);

$reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes de Préstamos</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #343a40;
        }
        .navbar-brand, .nav-link {
            color: #fff !important;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .btn-export {
            margin-left: 10px;
        }
        #reporteGrafico {
            max-height: 400px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center">Reportes de Préstamos</h1>

    <!-- Filtros de búsqueda -->
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-5">
                <input type="text" name="cliente" class="form-control" placeholder="Buscar por cliente" value="<?= $cliente ?>">
            </div>
            <div class="col-md-4">
                <select name="estado" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="activo" <?= $estado == 'activo' ? 'selected' : '' ?>>Activo</option>
                    <option value="finalizado" <?= $estado == 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                    <option value="penalizado" <?= $estado == 'penalizado' ? 'selected' : '' ?>>Penalizado</option>
                </select>
            </div>
            <div class="col-md-3 d-flex">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </div>
    </form>

    <!-- Tabla de reportes con paginación -->
    <div class="table-responsive">
        <table id="tablaReportes" class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                   
                    <th>Cliente</th>
                    <th>Fecha Desembolso</th>
                    <th>Saldo Actual</th>
                    <th>Estado</th>
                    
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($reportes): ?>
                    <?php foreach ($reportes as $reporte): ?>
                        <tr>
                            
                            <td><?= $reporte['cliente']; ?></td>
                            <td><?= $reporte['inicio_fecha']; ?></td>
                            <td><?= $reporte['saldo_actual']; ?></td>
                            <td><?= $reporte['estado']; ?></td>
                            
                            <td>
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detalleModal<?= $reporte['id_prestamo']; ?>">Ver Detalles</button>
                            </td>
                        </tr>

                        <!-- Modal para ver los detalles del préstamo -->
                        <div class="modal fade" id="detalleModal<?= $reporte['id_prestamo']; ?>" tabindex="-1" aria-labelledby="detalleModalLabel<?= $reporte['id_prestamo']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="detalleModalLabel<?= $reporte['id_prestamo']; ?>">Detalles del Préstamo</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>ID Préstamo:</strong> <?= $reporte['id_prestamo']; ?></p>
                                        <p><strong>Cliente:</strong> <?= $reporte['cliente']; ?></p>
                                        <p><strong>Fecha de Desembolso:</strong> <?= $reporte['inicio_fecha']; ?></p>
                                        <p><strong>Saldo Actual:</strong> <?= $reporte['saldo_actual']; ?></p>
                                        <p><strong>Estado:</strong> <?= $reporte['estado']; ?></p>
                                      
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No se encontraron resultados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Gráfica de préstamos por estado -->
    <div class="row mt-5">
        <div class="col-12">
            <canvas id="reporteGrafico"></canvas>
        </div>
    </div>
</div>

<!-- Scripts de Bootstrap, DataTables y Chart.js -->
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
        type: 'doughnut',
        data: {
            labels: ['Activo', 'Finalizado', 'Penalizado'],
            datasets: [{
                data: [
                    <?php
                    // Contar préstamos por estado
                    $stmt = $pdo->query("SELECT estado, COUNT(*) AS cantidad FROM prestamo GROUP BY estado");
                    $estados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($estados as $estado) {
                        echo $estado['cantidad'] . ',';
                    }
                    ?>
                ],
                backgroundColor: ['#28a745', '#17a2b8', '#dc3545'],
            }]
        },
        options: {
            responsive: true
        }
    });
</script>
</body>
</html>
