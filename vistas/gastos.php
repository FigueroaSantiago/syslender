<?php
session_start();

include '../includes/header.php';
include '../includes/functions.php';
include '../includes/config.php';

// Verificación de sesión y rol del usuario
if (!isset($_SESSION['user_id'])) {
    header("Location: login/login.php");
    exit();
}

// Verificar si el usuario tiene el rol 1 (administrador) mediante la tabla rol_user
$query_role = "
    SELECT r.id_rol 
    FROM rol_user ru
    JOIN rol r ON ru.id_rol = r.id_rol
    WHERE ru.id_user = :user_id
";

$stmt_role = $pdo->prepare($query_role);
$stmt_role->execute(['user_id' => $_SESSION['user_id']]);
$role = $stmt_role->fetch(PDO::FETCH_ASSOC);

// Variables para los filtros
$year = date('Y');
$fecha_inicio = $_GET['fecha_inicio'] ?? "$year-01-01"; // Por defecto, primer día del año actual
$fecha_fin = $_GET['fecha_fin'] ?? "$year-12-31"; // Por defecto, último día del año actual
$tipo_gasto = $_GET['tipo_gasto'] ?? '';
$id_cobrador = $_GET['id_cobrador'] ?? '';

// Consulta para obtener los gastos
$query = "
    SELECT g.id_gastos, g.monto, g.comentarios, g.fecha, g.estado, 
           t.descripcion AS tipo_gasto, 
           u.nombre AS creado_por, 
           a.nombre AS aprobado_por
    FROM gastos g
    LEFT JOIN tipo_gastos t ON g.id_tipo_gasto = t.id_tipo_gasto
    LEFT JOIN user u ON g.creado_por = u.id_user
    LEFT JOIN user a ON g.aprobado_por = a.id_user
    WHERE g.fecha BETWEEN :fecha_inicio AND :fecha_fin
";

// Agregar filtros opcionales
$params = [
    'fecha_inicio' => $fecha_inicio,
    'fecha_fin' => $fecha_fin,
];

if ($tipo_gasto) {
    $query .= " AND g.id_tipo_gasto = :tipo_gasto";
    $params['tipo_gasto'] = $tipo_gasto;
}

if ($id_cobrador) {
    $query .= " AND g.creado_por = :id_cobrador";
    $params['id_cobrador'] = $id_cobrador;
}

$query .= " ORDER BY g.fecha DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$gastos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener tipos de gasto y cobradores para los filtros
$tipos_gasto = $pdo->query("SELECT id_tipo_gasto, descripcion FROM tipo_gastos")->fetchAll(PDO::FETCH_ASSOC);

// Obtener cobradores (usuarios con rol de cobrador)
$query_cobradores = "
    SELECT u.id_user, u.nombre 
    FROM user u
    JOIN rol_user ru ON u.id_user = ru.id_user
    JOIN rol r ON ru.id_rol = r.id_rol
    WHERE r.id_rol = 2
";

$cobradores = $pdo->query($query_cobradores)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Gastos</title>
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
    </style> 
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Listado de Gastos</h1>
            <a href="gastos/panel_aprobacion.php" class="btn btn-success">Solicitudes de Aprobación</a>
        </div>

        <!-- Formulario de filtros -->
        <form method="GET" class="bg-light p-4 rounded mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label for="fecha_inicio" class="font-weight-bold">Fecha Inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" value="<?= htmlspecialchars($fecha_inicio); ?>">
                </div>
                <div class="col-md-3">
                    <label for="fecha_fin" class="font-weight-bold">Fecha Fin:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" value="<?= htmlspecialchars($fecha_fin); ?>">
                </div>
                <div class="col-md-3">
                    <label for="tipo_gasto" class="font-weight-bold">Tipo de Gasto:</label>
                    <select id="tipo_gasto" name="tipo_gasto" class="form-control">
                        <option value="">Todos</option>
                        <?php foreach ($tipos_gasto as $tipo): ?>
                            <option value="<?= $tipo['id_tipo_gasto']; ?>" <?= $tipo_gasto == $tipo['id_tipo_gasto'] ? 'selected' : ''; ?>>
                                <?= $tipo['descripcion']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="id_cobrador" class="font-weight-bold">Cobrador:</label>
                    <select id="id_cobrador" name="id_cobrador" class="form-control">
                        <option value="">Todos</option>
                        <?php foreach ($cobradores as $cobrador): ?>
                            <option value="<?= $cobrador['id_user']; ?>" <?= $id_cobrador == $cobrador['id_user'] ? 'selected' : ''; ?>>
                                <?= $cobrador['nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12 text-right">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="listado_gastos.php" class="btn btn-secondary">Reiniciar</a>
                </div>
            </div>
        </form>

        <!-- Tabla de resultados -->
        <?php if (count($gastos) > 0): ?>
            <table class="table table-striped table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Tipo de Gasto</th>
                        <th>Monto</th>
                        <th>Comentarios</th>
                        <th>Creado Por</th>
                        <th>Aprobado Por</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gastos as $gasto): ?>
                        <tr>
                            <td><?= $gasto['id_gastos']; ?></td>
                            <td><?= $gasto['tipo_gasto']; ?></td>
                            <td><?= number_format($gasto['monto'], 2); ?></td>
                            <td><?= htmlspecialchars($gasto['comentarios']); ?></td>
                            <td><?= $gasto['creado_por']; ?></td>
                            <td><?= $gasto['aprobado_por'] ?: 'N/A'; ?></td>
                            <td><?= $gasto['fecha']; ?></td>
                            <td>
                                <span class="badge badge-<?= $gasto['estado'] == 'aprobado' ? 'success' : ($gasto['estado'] == 'rechazado' ? 'danger' : 'warning'); ?>">
                                    <?= ucfirst($gasto['estado']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">No se encontraron gastos en el rango de fechas seleccionado.</p>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
