<?php
session_start();
// Conexión a la base de datos
include '../includes/headerco.php';
include '../includes/db.php';
include '../includes/functions.php';

// Verifica si la sesión está iniciada y si el rol es el correcto
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 2) {
    die('Acceso denegado.');
}

// Consulta para obtener información del usuario y su rol
$stmt = $pdo->prepare("
    SELECT 
        user.nombre, 
        user.apellido, 
        rol_user.id_rol_user, 
        rol.rol 
    FROM 
        user 
    JOIN 
        rol_user ON user.id_user = rol_user.id_user 
    JOIN 
        rol ON rol_user.id_rol = rol.id_rol 
    WHERE 
        user.id_user = ?
");
$stmt->execute([$_SESSION['user_id']]);
$datos_usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Asigna los valores de usuario y rol a la sesión
$_SESSION['nombre'] = $datos_usuario['nombre'];
$_SESSION['rol'] = $datos_usuario['rol'];
$_SESSION['rol_user_id'] = $datos_usuario['id_rol_user'];

// Variables iniciales
$id_rol_user = $_SESSION['rol_user_id'];
$fecha = date('Y-m-d');

// Obtener base inicial asignada al cobrador
$stmt_base = $pdo->prepare("
    SELECT ab.id_base 
    FROM asignacion_base ab 
    WHERE ab.id_cobrador = ? 
    ORDER BY ab.fecha_asignacion DESC LIMIT 1
");
$stmt_base->execute([$id_rol_user]);
$asignacion_base = $stmt_base->fetch(PDO::FETCH_ASSOC);

if ($asignacion_base) {
    $id_base = $asignacion_base['id_base'];
} else {
    die("No se encontró una base asignada para este cobrador.");
}

// Obtener saldo de la base
$stmt_saldo = $pdo->prepare("
SELECT b.base
FROM base b 
WHERE b.id_base = ?");
$stmt_saldo->execute([$id_base]);
$base_info = $stmt_saldo->fetch(PDO::FETCH_ASSOC);
$base_inicial = $base_info['base'] ?? 0;

// Calcular totales del día
$total_pagos = $pdo->query("
    SELECT SUM(pagos.monto) AS total_pagos
    FROM pagos
    INNER JOIN prestamo ON pagos.id_prestamo = prestamo.id_prestamo
    INNER JOIN cliente ON prestamo.id_cliente = cliente.id_cliente
    INNER JOIN ruta ON cliente.id_ruta = ruta.id_ruta
    INNER JOIN rol_user ON ruta.id_rol_user = rol_user.id_rol_user
    WHERE rol_user.id_rol_user = $id_rol_user AND DATE(pagos.fecha) = '$fecha'
")->fetchColumn() ?? 0;

$total_gastos = $pdo->query("
    SELECT SUM(monto) AS total_gastos 
    FROM gastos 
    WHERE id_rol_user = $id_rol_user AND DATE(fecha_creacion) = '$fecha' AND estado = 'aprobado'
")->fetchColumn() ?? 0;

$total_prestamos = $pdo->query("
    SELECT SUM(monto_inicial) AS total_prestamos
    FROM prestamo
    INNER JOIN cliente ON prestamo.id_cliente = cliente.id_cliente
    INNER JOIN ruta ON cliente.id_ruta = ruta.id_ruta
    INNER JOIN rol_user ON ruta.id_rol_user = rol_user.id_rol_user
    WHERE rol_user.id_rol_user = $id_rol_user AND DATE(prestamo.fecha_creacion) = '$fecha'
")->fetchColumn() ?? 0;

// Calcular saldo final
$saldo_final = $base_inicial + $total_pagos - $total_gastos - $total_prestamos;
$diferencia = $saldo_final < 0 ? 'Desbalance' : 'Balanceado';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuadre Diario</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Cuadre Diario - Cobrador</h2>
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Resumen</div>
                <div class="card-body">
                    <p><strong>Base Inicial:</strong> <?= number_format($base_inicial, 2) ?> </p>
                    <p><strong>Total Pagos:</strong> <?= number_format($total_pagos, 2) ?> </p>
                    <p><strong>Total Gastos:</strong> <?= number_format($total_gastos, 2) ?> </p>
                    <p><strong>Total Préstamos:</strong> <?= number_format($total_prestamos, 2) ?> </p>
                    <hr>
                    <p class="<?= $saldo_final < 0 ? 'text-danger' : 'text-success' ?>">
                        <strong>Saldo Final:</strong> <?= number_format($saldo_final, 2) ?> 
                    </p>
                    <p><strong>Estado:</strong> <?= $diferencia ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">Observaciones</div>
                <div class="card-body">
                    <form action="guardar_cuadre.php" method="POST">
                        <textarea class="form-control mb-3" name="observaciones" rows="5" placeholder="Escribe tus observaciones..."></textarea>
                        <input type="hidden" name="saldo_final" value="<?= $saldo_final ?>">
                        <button type="submit" class="btn btn-success w-100">Guardar Cuadre</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <h4>Detalles de Movimientos</h4>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Monto</th>
                    <th>Fecha</th>
                    <th>Tipo</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt_movimientos = $pdo->prepare("
                    SELECT 'Pago' AS tipo, pagos.monto AS monto, pagos.fecha AS fecha, CONCAT('Pago de cliente: ', cliente.nombre, ' ', cliente.apellido) AS descripcion
                    FROM pagos
                    INNER JOIN prestamo ON pagos.id_prestamo = prestamo.id_prestamo
                    INNER JOIN cliente ON prestamo.id_cliente = cliente.id_cliente
                    INNER JOIN ruta ON cliente.id_ruta = ruta.id_ruta
                    INNER JOIN rol_user ON ruta.id_rol_user = rol_user.id_rol_user
                    WHERE rol_user.id_rol_user = ? AND DATE(pagos.fecha) = ?

                    UNION ALL

                    SELECT 'Gasto' AS tipo, gastos.monto AS monto, gastos.fecha_creacion AS fecha, CONCAT('Gasto: ', tipo_gastos.nombre) AS descripcion
                    FROM gastos
                    INNER JOIN tipo_gastos ON gastos.id_tipo_gasto = tipo_gastos.id_tipo_gasto
                    WHERE gastos.id_rol_user = ? AND DATE(gastos.fecha_creacion) = ? AND gastos.estado = 'aprobado'

                    UNION ALL

                    SELECT 'Préstamo' AS tipo, prestamo.monto_inicial AS monto, prestamo.fecha_creacion AS fecha, CONCAT('Préstamo a cliente: ', cliente.nombre, ' ', cliente.apellido) AS descripcion
                    FROM prestamo
                    INNER JOIN cliente ON prestamo.id_cliente = cliente.id_cliente
                    INNER JOIN ruta ON cliente.id_ruta = ruta.id_ruta
                    INNER JOIN rol_user ON ruta.id_rol_user = rol_user.id_rol_user
                    WHERE rol_user.id_rol_user = ? AND DATE(prestamo.fecha_creacion) = ?
                ");

                $stmt_movimientos->execute([$id_rol_user, $fecha, $id_rol_user, $fecha, $id_rol_user, $fecha]);
                $movimientos = $stmt_movimientos->fetchAll(PDO::FETCH_ASSOC);

                if ($movimientos) {
                    foreach ($movimientos as $mov) {
                        echo "<tr>";
                        echo "<td>{$mov['descripcion']}</td>";
                        echo "<td>" . number_format($mov['monto'], 2) . "</td>";
                        echo "<td>{$mov['fecha']}</td>";
                        echo "<td>{$mov['tipo']}</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No se encontraron movimientos para el día.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
