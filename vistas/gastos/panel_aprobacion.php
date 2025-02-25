<?php
session_start();
include '../../includes/header2.php';
include '../../includes/functions.php';
include '../../includes/db.php';

// Validar que el usuario sea administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header('Location: ../login/login.php');
    exit();
}
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

// Verificar si se encontraron los datos del usuario
// Manejo de acciones (aprobar/rechazar)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_gasto = $_POST['id_gasto'];
    $accion = $_POST['accion']; // Puede ser "aprobar" o "rechazar"
    $id_admin = $_SESSION['id_rol_user'];

    try {
        $pdo->beginTransaction();

        // Obtener información del gasto
        $stmt = $pdo->prepare("
            SELECT g.id_rol_user, g.monto, ab.id_base, b.base AS saldo_base
            FROM gastos g
            INNER JOIN asignacion_base ab ON ab.id_cobrador = g.id_rol_user
            INNER JOIN base b ON ab.id_base = b.id_base
            WHERE g.id_gastos = :id_gasto AND g.estado = 'pendiente'
            LIMIT 1
        ");
        $stmt->execute(['id_gasto' => $id_gasto]);
        $gasto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$gasto) {
            throw new Exception("El gasto no está disponible o ya fue procesado.");
        }

        $id_rol_user = $gasto['id_rol_user'];
        $monto = $gasto['monto'];
        $id_base = $gasto['id_base'];
        $saldo_base = $gasto['saldo_base'];

        if ($accion == 'aprobar') {
            // Validar si la base tiene fondos suficientes
            if ($saldo_base < $monto) {
                throw new Exception("Fondos insuficientes en la base. No se puede aprobar este gasto.");
            }

            // Descontar de la base específica
            $stmt = $pdo->prepare("UPDATE base SET base = base - :monto WHERE id_base = :id_base");
            $stmt->execute(['monto' => $monto, 'id_base' => $id_base]);

            // Registrar movimiento en movimientos_base
            $stmt = $pdo->prepare("
                INSERT INTO movimientos_base (id_asignacion_base, tipo_movimiento, monto, fecha, descripcion)
                VALUES (
                    (SELECT id_asignacion FROM asignacion_base WHERE id_base = :id_base LIMIT 1),
                    'gasto', :monto, NOW(), :descripcion
                )
            ");
            $stmt->execute([
                'id_base' => $id_base,
                'monto' => $monto,
                'descripcion' => 'Aprobación de gasto por administrador.',
            ]);

            // Actualizar estado del gasto
            $stmt = $pdo->prepare("UPDATE gastos SET estado = 'aprobado', aprobado_por = :id_admin WHERE id_gastos = :id_gasto");
            $stmt->execute([
                'id_admin' => $id_admin,
                'id_gasto' => $id_gasto,
            ]);

            $_SESSION['mensaje'] = "Gasto aprobado exitosamente.";
        } elseif ($accion == 'rechazar') {
            // Actualizar estado del gasto a "rechazado"
            $stmt = $pdo->prepare("UPDATE gastos SET estado = 'rechazado' WHERE id_gastos = :id_gasto");
            $stmt->execute(['id_gasto' => $id_gasto]);

            $_SESSION['mensaje'] = "Gasto rechazado exitosamente.";
        }

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['mensaje'] = "Error: " . $e->getMessage();
    }

    header('Location: panel_aprobacion.php');
    exit();
}




// Listar solicitudes pendientes
$solicitudes = $pdo->query("
    SELECT 
    g.id_gastos, 
    g.monto, 
    g.comentarios, 
    g.fecha_creacion, 
    t.descripcion AS tipo_gasto, 
    u.nombre AS creado_por, 
    r.rol AS rol_creador
FROM 
    gastos g
INNER JOIN 
    tipo_gastos t ON g.id_tipo_gasto = t.id_tipo_gasto
INNER JOIN 
    rol_user ru ON g.creado_por = ru.id_rol_user
INNER JOIN 
    user u ON ru.id_user = u.id_user
INNER JOIN 
    rol r ON ru.id_rol = r.id_rol
WHERE 
    g.estado = 'pendiente';

")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Aprobación</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Panel de Aprobación de Gastos</h1>

        <!-- Mostrar mensajes -->
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?>
            </div>
        <?php endif; ?>

        <?php if (count($solicitudes) > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo de Gasto</th>
                        <th>Monto</th>
                        <th>Comentarios</th>
                        <th>Creado Por</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($solicitudes as $solicitud): ?>
                        <tr>
                            <td><?= $solicitud['id_gastos']; ?></td>
                            <td><?= $solicitud['tipo_gasto']; ?></td>
                            <td><?= number_format($solicitud['monto'], 2); ?></td>
                            <td><?= $solicitud['comentarios']; ?></td>
                            <td><?= $solicitud['creado_por']; ?></td>
                            <td><?= $solicitud['fecha_creacion']; ?></td>
                            <td>
                                <form action="panel_aprobacion.php" method="POST" class="d-inline">
                                    <input type="hidden" name="id_gasto" value="<?= $solicitud['id_gastos']; ?>">
                                    <input type="hidden" name="accion" value="aprobar">
                                    <button type="submit" class="btn btn-success btn-sm">Aprobar</button>
                                </form>
                                <form action="panel_aprobacion.php" method="POST" class="d-inline">
                                    <input type="hidden" name="id_gasto" value="<?= $solicitud['id_gastos']; ?>">
                                    <input type="hidden" name="accion" value="rechazar">
                                    <button type="submit" class="btn btn-danger btn-sm">Rechazar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">No hay solicitudes pendientes.</p>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
