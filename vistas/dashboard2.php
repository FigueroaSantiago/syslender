<?php
session_start();

// Validar sesión y rol
if (!isset($_SESSION['user_id'])) {
    header("Location: login/login.php");
    exit();
}

if ($_SESSION['role_id'] != 2) { // Verifica que sea cobrador
    header('Location: no_permitido.php');
    exit();
}

include '../includes/headerco.php';
include '../includes/functionsco.php';

$user_id = $_SESSION['user_id'];

// Obtener id_rol_user para el cobrador
$sqlRolUser = "SELECT id_rol_user FROM rol_user WHERE id_user = ?";
$stmtRolUser = $conn->prepare($sqlRolUser);
$stmtRolUser->bind_param("i", $user_id);
$stmtRolUser->execute();
$resultRolUser = $stmtRolUser->get_result();
$rolUser = $resultRolUser->fetch_assoc();
$id_rol_user = $rolUser['id_rol_user'] ?? null;

// Consulta para obtener el nombre del cobrador
$sql = "SELECT user.nombre, user.apellido, rol.rol 
        FROM user 
        JOIN rol_user ON user.id_user = rol_user.id_user 
        JOIN rol ON rol_user.id_rol = rol.id_rol 
        WHERE user.id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Obtener métricas específicas para el cobrador
$totalClientes = getClientesEnRuta($conn, $id_rol_user) ?? 0;
$prestamosActivos = getPrestamosActivosCobrador($conn, $id_rol_user) ?? 0;
$gastosDiarios = getGastosPorCobrador($conn, $id_rol_user) ?? 0.00;
$prestamosPorEstado = getLoansByStatusCobrador($conn, $id_rol_user) ?? [];
$resumen = getResumenPrestamosCobrador($conn, $id_rol_user) ?? [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard del Cobrador</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        .card {
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .table thead th {
            background-color: #f8f9fc;
        }
        .chart-area {
            position: relative;
            height: 250px;
        }
        .dashboard-header {
            font-size: 1.25rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Dashboard del Cobrador</h1>
        <div class="text-right">
            <h5 class="dashboard-header"><?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?></h5>
            <p class="mb-0 text-muted"><?= htmlspecialchars($user['rol']) ?></p>
        </div>
    </div>

    <!-- Tarjetas de métricas -->
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Clientes en Ruta</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= htmlspecialchars($totalClientes) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Préstamos Activos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= htmlspecialchars($prestamosActivos) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Gráficos -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Préstamos por Estado</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="loanStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Resumen -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tabla de Resumen</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Préstamo</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resumen as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['cliente']) ?></td>
                                        <td>$<?= htmlspecialchars($row['prestamo']) ?></td>
                                        <td><?= htmlspecialchars($row['fecha']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($resumen)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No hay datos disponibles</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('loanStatusChart').getContext('2d');
    const loanStatusChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?= json_encode(array_keys($prestamosPorEstado)) ?>,
            datasets: [{
                data: <?= json_encode(array_values($prestamosPorEstado)) ?>,
                backgroundColor: ['#4e73df', '#36b9cc', '#e74a3b']
            }]
        },
        options: {
            responsive: true
        }
    });
});
</script>


</body>
</html>
