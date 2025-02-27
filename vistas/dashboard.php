<?php
session_start();
date_default_timezone_set('America/Bogota'); 
include '../Conexion/conexion.php';

// 🔹 Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role_id = $_SESSION['role_id'];

// 🔹 Inicializar variables
$dias_restantes = "N/A";
$estado_cuenta = "N/A";

// 🔹 Si el usuario es ADMINISTRADOR, debe tener una cuenta seleccionada
if ($role_id == 1 && !isset($_SESSION['id_cuenta'])) {
    die("Error: No se ha seleccionado ninguna cuenta.");
}

// 🔹 Si el usuario es GESTOR, no necesita una cuenta
if ($role_id == 18) {
    echo "Este usuario (Gestor) no necesita una cuenta.<br>";
}

// 🔹 Si el usuario es COBRADOR, obtiene su cuenta directamente de la tabla `user`
if ($role_id == 2) {
    $sql = "SELECT id_cuenta FROM user WHERE id_user = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cuenta = $result->fetch_assoc();

    if ($cuenta && !empty($cuenta['id_cuenta'])) {
        $_SESSION['id_cuenta'] = $cuenta['id_cuenta'];
    } else {
        echo "⚠ Error: No hay una cuenta asociada a este cobrador.";
        $_SESSION['id_cuenta'] = null;
    }
}

// 🔹 Consultar la fecha de vencimiento de la cuenta **SOLO SI ES ADMINISTRADOR O COBRADOR Y TIENE UNA CUENTA ASIGNADA**
if (($role_id == 1 || $role_id == 2) && !empty($_SESSION['id_cuenta'])) {
    $id_cuenta = $_SESSION['id_cuenta'];

    // 🔹 Obtener fecha de vencimiento y estado actual
    $sql = "SELECT fecha_creacion, fecha_vencimiento, estado FROM cuentas WHERE id_cuenta = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_cuenta);
    $stmt->execute();
    $result = $stmt->get_result();
    $cuenta = $result->fetch_assoc();

    if (!$cuenta) {
        die("⚠ Error: Cuenta no encontrada.");
    }

    // 🔹 Si la cuenta YA ESTÁ INACTIVA en la base de datos, salir inmediatamente
    if ($cuenta['estado'] == "inactiva") {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Acceso denegado',
                    text: 'Esta cuenta ya está inactiva.'
                }).then(() => { window.location.href = 'login/login.php'; });
              </script>";
        session_destroy();
        exit();
    }

    // 🔹 Convertir fechas a timestamps
    $fecha_actual = strtotime(date("Y-m-d"));
    $fecha_vencimiento = strtotime($cuenta['fecha_vencimiento']);

    // 🔹 Calcular la diferencia en días
    $dias_restantes = round(($fecha_vencimiento - $fecha_actual) / 86400);

    // 🔹 Si la cuenta está vencida, actualizar a INACTIVA y bloquear acceso
    if ($dias_restantes < 0) {
        $estado_cuenta = "Expirada";
        $dias_restantes = 0;

        // 🔹 Actualizar estado en la base de datos
        $update_sql = "UPDATE cuentas SET estado = 'inactiva' WHERE id_cuenta = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $id_cuenta);

        if ($update_stmt->execute()) {
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Acceso denegado',
                        text: 'La cuenta ha vencido y ha sido desactivada automáticamente.'
                    }).then(() => { window.location.href = 'login/login.php'; });
                  </script>";
        } else {
            echo "<strong>⚠ Error al actualizar la cuenta: " . $update_stmt->error . "</strong><br>";
        }

        session_destroy();
        exit();
    } else {
        $estado_cuenta = "Activa";
    }
}

// ✅ Permitimos el acceso a Administradores (1), Gestores (18) y Cobradores (2)
if ($role_id != 1 && $role_id != 18 && $role_id != 2) {
    header('Location: no_permitido.php');
    exit();
}

include '../includes/header.php';
include '../includes/functions.php';


// 🔹 Consulta para obtener el nombre y el rol del usuario
$sql = "SELECT user.nombre, user.apellido, rol.rol FROM user 
        JOIN rol_user ON user.id_user = rol_user.id_user 
        JOIN rol ON rol_user.id_rol = rol.id_rol 
        WHERE user.id_user = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();


$totalClientes = getCounts('cliente');
$totalPrestamos = getCounts('prestamo');
$totalrutas = getCounts('ruta');
$prestamosActivos = getprestamosactivos();
$totalGastos = getTotalgastos();

// Nuevas consultas para estadísticas adicionales
$totalPagos = getTotalPayments();

$prestamosPorEstado = getLoansByStatus();
$gastosPorTipo = getExpensesByType();

$alertas = $alertas ?? [];

$sql = "SELECT 
    cliente.nombres AS cliente, 

    prestamo.saldo_actual AS prestamo,
    SUM(gastos.monto) AS gastos, 
    DATE(prestamo.fecha_creacion) AS fecha_creacion 
FROM 
    prestamo 
LEFT JOIN 
    cliente ON prestamo.id_cliente = cliente.id_cliente 
LEFT JOIN 
    rol_user ON prestamo.id_rol_user = rol_user.id_rol_user 
LEFT JOIN 
    gastos ON rol_user.id_rol_user = gastos.id_rol_user 
GROUP BY 
    cliente.nombres, prestamo.monto_inicial, prestamo.fecha_creacion
ORDER BY prestamo.fecha_creacion DESC
LIMIT 5";


$result = $conn->query($sql);

// Verificar si la consulta fue exitosa
if ($result === FALSE) {
    die("Error en la consulta: " . $conn->error);
}



// Procesar los resultados
$resumen = [];
while ($row = $result->fetch_assoc()) {
    $resumen[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        /* Estilo general para las tarjetas */
        .card {
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            /* Sombra suave */
            transition: transform 0.3s ease;
            /* Animación en hover */
        }

        /* Efecto de elevación en hover */
        .card:hover {
            transform: translateY(-5px);
            /* Efecto de elevación al pasar el cursor */
        }

        /* Estilos para el encabezado de la tarjeta */
        .card-header {
            border-bottom: 1px solid #e3e6f0;
        }

        /* Estilos para el cuerpo de la tarjeta */
        .card-body {
            padding: 1.5rem;
        }

        /* Alineación vertical de las celdas de la tabla */
        .table th,
        .table td {
            vertical-align: middle;
        }

        /* Fondo claro para el encabezado de la tabla */
        .table thead th {
            background-color: #f8f9fc;
        }

        /* Estilo para los gráficos */
        .chart-area {
            position: relative;
            height: 250px;
        }

        /* Estilo para el texto en el dashboard */
        .dashboard-header {
            font-size: 1.25rem;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <div class="container mt-5">
        <!-- Encabezado con el nombre del usuario y su rol -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">Dashboard</h1>
            <div class="text-right">
                <h5 class="dashboard-header"><?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?></h5>
                <p class="mb-0 text-muted"><?= htmlspecialchars($user['rol']) ?></p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Días restantes de licencia</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $dias_restantes ?> días</div>
                                <small class="text-muted">Estado: <?= $estado_cuenta ?></small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tarjetas para las métricas principales -->
             <!--
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Clientes</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= htmlspecialchars($totalClientes) ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>-->

            <!-- Tarjeta para el número de rutas -->
             <!--
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Rutas</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= htmlspecialchars($totalrutas) ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-road fa-2x text-gray-300"></i> 
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->

            <!-- Tarjeta para los préstamos activos -->
             <!--
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Préstamos Activos</div>
                                <div class="h5 mb-3 font-weight-bold text-gray-800"><?= htmlspecialchars($prestamosActivos) ?></div>

                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-0">Total Préstamos</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= htmlspecialchars($totalPrestamos) ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>-->

            <!-- Tarjeta para el total de la cartera (revisar que esté relacionado con los gastos correctos) -->
             <!--
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total de cartera</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">$<?= htmlspecialchars($totalGastos) ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-coins fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>-->

        <!-- Gráficas -->
         <!--
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Distribución de Préstamos por Estado</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="loanStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Distribución de Gastos por Tipo</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="expenseTypeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>-->

        <!-- Tabla de Resumen -->
         <!--
        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Tabla de Resumen</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
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

                                            <td><?= htmlspecialchars($row['fecha_creacion']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>-->



    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loanStatusCtx = document.getElementById('loanStatusChart').getContext('2d');
            const expenseTypeCtx = document.getElementById('expenseTypeChart').getContext('2d');

            const loanStatusChart = new Chart(loanStatusCtx, {
                type: 'pie',
                data: {
                    labels: ['Activo', 'Cerrado'],
                    datasets: [{
                        label: 'Préstamos por Estado',
                        data: <?= json_encode(array_values($prestamosPorEstado)) ?>,
                        backgroundColor: ['rgba(40, 167, 69, 0.7)', 'rgba(108, 117, 125, 0.7)'],
                    }]
                },
                options: {
                    responsive: true,
                }
            });

            const expenseTypeChart = new Chart(expenseTypeCtx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode(array_keys($gastosPorTipo)) ?>,
                    datasets: [{
                        label: 'Gastos por Tipo',
                        data: <?= json_encode(array_values($gastosPorTipo)) ?>,
                        backgroundColor: 'rgba(25, 135, 84, 0.8)',
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>

    <?php include '../includes/footer.php'; ?>
</body>

</html>