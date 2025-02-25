<?php
session_start();
include '../../includes/header2.php';
include '../../includes/functions.php';
include '../../Conexion/conexion.php'; // Asegúrate de incluir tu conexión a la base de datos.

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Verificar si se pasa el ID del cliente por GET
if (isset($_GET['id'])) {
    $cliente_id = intval($_GET['id']); // Sanitizar el ID del cliente

    // Consultar información del cliente
    $cliente = getClienteById($cliente_id);

    // Verificar si el cliente existe
    if (!$cliente) {
        echo "<p class='alert alert-danger'>Cliente no encontrado.</p>";
        exit();
    }

    // Consultar información adicional
    $prestamos = getPrestamosByCliente($cliente_id);
    $historial_pagos = getHistorialPagosByCliente($cliente_id);
    $cobrador = getCobradorByCliente($cliente_id);
    $ruta = getRutaByCliente($cliente_id);

} else {
    header('Location: clientes_prestamos.php');
    exit();
}

// Función para obtener los datos del cliente por ID

// Función para obtener los préstamos de un cliente
function getPrestamosByCliente($cliente_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM prestamo WHERE id_cliente = ?");
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $prestamos = [];
    while ($row = $result->fetch_assoc()) {
        $prestamos[] = $row;
    }
    return $prestamos;
}

// Función para obtener el historial de pagos de un cliente
function getHistorialPagosByCliente($cliente_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT 
            cp.fecha_cuota AS fecha_programada, 
            cp.valor_cuota AS monto_programado,
            p.fecha,
            p.id_prestamo,
            p.monto, 
            p.observacion
        FROM cuota_prestamo cp
        LEFT JOIN pagos p ON cp.id_cuota = p.id_cuotas
        JOIN prestamo pr ON cp.id_prestamo = pr.id_prestamo
        WHERE pr.id_cliente = ?
        ORDER BY cp.fecha_cuota ASC
    ");
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $historial = [];
    while ($row = $result->fetch_assoc()) {
        $historial[] = $row;
    }
    return $historial;
}


// Función para obtener el cobrador asignado
function getCobradorByCliente($cliente_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT u.nombre, u.apellido
        FROM user u
        JOIN rol_user ru ON u.id_user = ru.id_user
        JOIN ruta r ON ru.id_rol_user = r.id_rol_user
        JOIN cliente c ON c.id_ruta = r.id_ruta
        WHERE c.id_cliente = ? AND ru.id_rol = (SELECT id_rol FROM rol WHERE id_rol = 2 )
    ");
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if (!$data) {
        error_log("No se encontró un cobrador para el cliente con ID: $cliente_id");
        return false; // Retorna false si no se encuentra el cobrador
    }

    return $data;
}


// Función para obtener la ruta asignada al cliente
function getRutaByCliente($cliente_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT r.nombre_ruta
        FROM ruta r
        JOIN cliente c ON r.id_ruta = c.id_ruta
        WHERE c.id_cliente = ?");
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Cliente</title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
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
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Detalles del Cliente</h1>
        
        <!-- Información General del Cliente -->
        <div class="card">
            <div class="card-header">Información General</div>
            <div class="card-body">
                <p><strong>Nombre Completo:</strong> <?php echo htmlspecialchars($cliente['nombres'] . " " . $cliente['apellidos']); ?></p>
                <p><strong>Dirección Casa:</strong> <?php echo htmlspecialchars($cliente['direccion_casa']); ?></p>
                <p><strong>Dirección Negocio:</strong> <?php echo htmlspecialchars($cliente['direccion_negocio']); ?></p>
                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($cliente['telefono']); ?></p>
                <p><strong>Cobrador Asignado:</strong> <?php echo htmlspecialchars($cobrador['nombre'] . " " . $cobrador['apellido']); ?></p>
                <p><strong>Ruta Asignada:</strong> <?php echo htmlspecialchars($ruta['nombre_ruta']); ?></p>
            </div>
        </div>

        <!-- Tabla de Préstamos -->
        <div class="card mt-4">
            <div class="card-header">Préstamos del Cliente</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID Préstamo</th>
                            <th>Monto Total</th>
                            <th>Saldo Actual</th>
                            <th>Interés</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($prestamos)) { ?>
                            <tr>
                                <td colspan="5" class="text-center">No hay préstamos registrados.</td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($prestamos as $prestamo) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($prestamo['id_prestamo']); ?></td>
                                    <td><?php echo htmlspecialchars($prestamo['monto_inicial']); ?></td>
                                    <td><?php echo htmlspecialchars($prestamo['saldo_actual']); ?></td>
                                    <td><?php echo htmlspecialchars($prestamo['interes_total']); ?></td>
                                    <td><?php echo htmlspecialchars($prestamo['estado']); ?></td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

            <!-- Historial de Pagos -->
  <!-- Historial de Pagos -->
<div class="card mt-4">
    <div class="card-header">Historial de Pagos</div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                <th>id prestamo</th>
                    <th>Fecha de Pago</th>
                    <th>Monto Pagado</th>
                    <th>Comentarios</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Filtrar pagos válidos antes de iterar
                $pagos_validos = array_filter($historial_pagos, function($pago) {
                    return !empty($pago['fecha']) && !empty($pago['monto'] && !empty($pago['id_prestamo']));
                });

                if (empty($pagos_validos)) { ?>
                    <tr>
                        <td colspan="3" class="text-center">No hay pagos registrados.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($pagos_validos as $pago) { ?>
                        <tr>   <td><?php echo htmlspecialchars($pago['id_prestamo']); ?></td>
                            <td><?php echo htmlspecialchars($pago['fecha']); ?></td>
                            <td><?php echo htmlspecialchars($pago['monto']); ?></td>
                            <td><?php echo htmlspecialchars($pago['observacion'] ?? ''); ?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
