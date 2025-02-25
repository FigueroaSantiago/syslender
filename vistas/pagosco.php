<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login/login.php");
    exit();
}

include '../includes/headerco.php';
include '../includes/db.php';

$user_id = $_SESSION['user_id'];
$role_id = $_SESSION['role_id'];

// Consultar información del usuario
$query_user = "SELECT u.nombre AS nombre_usuario, r.rol AS nombre_rol, rt.nombre_ruta AS nombre_ruta
               FROM user u
               JOIN rol_user ru ON u.id_user = ru.id_user
               JOIN rol r ON ru.id_rol = r.id_rol
               LEFT JOIN ruta rt ON ru.id_rol_user = rt.id_rol_user
               WHERE u.id_user = ?";
$stmt_user = $conn->prepare($query_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();
$stmt_user->close();

// Consultar préstamos
$query_prestamos = "SELECT p.id_prestamo, c.nombres AS cliente_nombre, p.monto_inicial, 
                    p.saldo_actual, rt.nombre_ruta 
                    FROM prestamo p 
                    JOIN cliente c ON p.id_cliente = c.id_cliente
                    JOIN ruta rt ON c.id_ruta = rt.id_ruta
                    JOIN rol_user ru ON rt.id_rol_user = ru.id_rol_user";

if ($role_id !== 1) {
    $query_prestamos .= " WHERE ru.id_user = ? AND p.estado = 'activo'";
    $stmt_prestamos = $conn->prepare($query_prestamos);
    $stmt_prestamos->bind_param("i", $user_id);
} else {
    $query_prestamos .= " WHERE p.estado = 'activo'";
    $stmt_prestamos = $conn->prepare($query_prestamos);
}

$stmt_prestamos->execute();
$prestamos = $stmt_prestamos->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_prestamos->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Préstamos Activos</title>
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

        /* Estilo para la barra lateral */
        .sidebar {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.5rem;
        }

        /* Colores para los estados de las cuotas */
        .estado-pagado {
            color: green;
            font-weight: bold;
        }

        .estado-abonado {
            color: orange;
            font-weight: bold;
        }

        .estado-no-pagado {
            color: red;
            font-weight: bold;
        }

        .estado-sin-cobrar {
            color: gray;
            font-weight: bold;
        }


        .list-group-item-custom {
            transition: all 0.3s;
        }

        .list-group-item-custom:hover {
            background-color: #f0f8ff;
            cursor: pointer;
        }

        .btn-primary-custom {
            background-color: #007bff;
            border: none;
        }

        .btn-primary-custom:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-3">
                <div class="sidebar bg-light p-3 rounded">
                    <h4>Bienvenido, <?= htmlspecialchars($user['nombre_usuario']) ?></h4>
                    <p>Rol: <?= htmlspecialchars($user['nombre_rol']) ?></p>
                    <p>Ruta: <?= htmlspecialchars($user['nombre_ruta'] ?? 'Sin ruta asignada') ?></p>
                </div>
            </div>
            <div class="col-9">
                <h2 class="mb-4">Préstamos Activos</h2>
                <?php if (empty($prestamos)): ?>
                    <div class="alert alert-info">No hay préstamos activos para esta ruta.</div>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($prestamos as $prestamo): ?>
                            <a href="#" class="list-group-item list-group-item-action list-group-item-custom"
                               data-bs-toggle="modal" data-bs-target="#modalCuotas<?= $prestamo['id_prestamo'] ?>">
                                <h5><?= htmlspecialchars($prestamo['cliente_nombre']) ?></h5>
                                <p>Ruta: <?= htmlspecialchars($prestamo['nombre_ruta']) ?></p>
                                <p>Monto: $<?= number_format($prestamo['monto_inicial'], 2) ?> - 
                                 Saldo: $<?= number_format($prestamo['saldo_actual'], 2) ?></p>
                            </a>

                            <!-- Modal para Cuotas -->
                            <div class="modal fade" id="modalCuotas<?= $prestamo['id_prestamo'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header modal-header-custom ">
                                            <h5 class="modal-title">Cuotas de <?= htmlspecialchars($prestamo['cliente_nombre']) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <?php
                                            // Consultar cuotas
                                            $query_cuotas = "SELECT id_cuota, numero_cuota, fecha_cuota, saldo_pendiente , valor_cuota, estado 
                                                             FROM cuota_prestamo WHERE id_prestamo = ? 
                                                             ORDER BY numero_cuota";
                                            $stmt_cuotas = $conn->prepare($query_cuotas);
                                            $stmt_cuotas->bind_param("i", $prestamo['id_prestamo']);
                                            $stmt_cuotas->execute();
                                            $cuotas = $stmt_cuotas->get_result()->fetch_all(MYSQLI_ASSOC);
                                            $stmt_cuotas->close();
                                            ?>
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th># Cuota</th>
                                                        <th>Fecha</th>
                                                        <th>Valor de cuota</th>
                                                        <th>Saldo pendiente</th>
                                                        <th>Estado</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($cuotas as $cuota): ?>
                                                        <tr>
                                                            <td><?= $cuota['numero_cuota'] ?></td>
                                                            <td><?= $cuota['fecha_cuota'] ?></td>
                                                            <td>$<?= number_format($cuota['valor_cuota'], 2) ?></td>
                                                            <td>$<?= number_format($cuota['saldo_pendiente'], 2) ?></td>
                                                            <td class="<?php 
                                                                switch ($cuota['estado']) {
                                                                    case 'pagado': echo 'estado-pagado'; break;
                                                                    case 'abonado': echo 'estado-abonado'; break;
                                                                    case 'no pagado': echo 'estado-no-pagado'; break;
                                                                    default: echo 'estado-sin-cobrar'; break;
                                                                }
                                                            ?>">
                                                                <?= ucfirst($cuota['estado']) ?>
                                                            </td>
                                                            <td>
                                                                <a href="pagos/registrar_pagoco.php?id_cuota=<?= $cuota['id_cuota'] ?>&id_prestamo=<?= $prestamo['id_prestamo'] ?>" 
                                                                   class="btn btn-success btn-sm btn-success-custom">
                                                                    Registrar Pago
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
