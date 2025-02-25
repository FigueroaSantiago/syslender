<?php
session_start();
include '../includes/headerco.php';
include '../includes/functions.php';

// Suponiendo que el ID del usuario está guardado en la sesión
$id_user = $_SESSION['user_id']; 
$id_roll = $_SESSION['role_id']; 

// Obtener la información del usuario
$query = $pdo->prepare("
    SELECT u.nombre,  u.contacto, r.rol AS rol, ru.id_rol_user
    FROM user u
    JOIN rol_user ru ON u.id_user = ru.id_user
    JOIN rol r ON ru.id_rol = r.id_rol
    WHERE u.id_user = ?
");
$query->execute([$id_user]);
$user = $query->fetch(PDO::FETCH_ASSOC);

// Si es cobrador, obtener la base, la ruta y los últimos 4 gastos
$es_cobrador = ($user['rol'] == 'cobrador');
if ($es_cobrador) {
    // Obtener la base del cobrador
    $query_base = $pdo->prepare("
        SELECT b.base, b.fecha
        FROM base b
        JOIN asignacion_base ab ON b.id_base = ab.id_base
        WHERE ab.id_cobrador = ?
        ORDER BY b.fecha DESC LIMIT 1
    ");
    $query_base->execute([$user['id_rol_user']]);
    $base = $query_base->fetch(PDO::FETCH_ASSOC);

    // Obtener la ruta del cobrador
    $query_ruta = $pdo->prepare("
        SELECT r.nombre_ruta
        FROM ruta r
        JOIN rol_user ru ON ru.id_rol_user = r.id_rol_user
        WHERE ru.id_user = ?
    ");
    $query_ruta->execute([$id_user]);
    $ruta = $query_ruta->fetch(PDO::FETCH_ASSOC);

    // Obtener los últimos 4 gastos del cobrador
    $query_gastos = $pdo->prepare("
        SELECT g.comentarios, g.monto, g.fecha_creacion
        FROM gastos g
        WHERE g.id_rol_user = ?
        ORDER BY g.fecha DESC
        LIMIT 4
    ");
    $query_gastos->execute([$user['id_rol_user']]);
    $gastos = $query_gastos->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener los permisos del usuario
$query_permisos = $pdo->prepare("
    SELECT p.nombre AS permiso
    FROM permisos p
    JOIN rol_permisos pr ON p.id_permisos = pr.id_permiso
    WHERE pr.id_rol = 2
");
$query_permisos->execute();
$permisos = $query_permisos->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #198754;
            color: white;
            font-weight: bold;
        }
        .card-body {
            background-color: white;
        }
        .list-group-item {
            border-radius: 5px;
            margin-bottom: 10px;
            background-color: #f1f1f1;
            border: none;
        }
        .list-group-item:hover {
            background-color: #d4edda;
            cursor: pointer;
        }
        .container {
            max-width: 1200px;
        }
        
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Perfil de Usuario</h1>

        <!-- Información básica del usuario -->
        <div class="card mb-4">
            <div class="card-header">
                Información Personal
            </div>
            <div class="card-body">
                <p><strong>Nombre:</strong> <?= $user['nombre'] ?></p>
                <p><strong>Contacto:</strong> <?= $user['contacto'] ?></p>
                <p><strong>Rol:</strong> <?= $user['rol'] ?></p>
            </div>
        </div>

        <!-- Si el usuario es Cobrador, mostrar información adicional -->
        <?php if ($es_cobrador): ?>
            <div class="card mb-4">
                <div class="card-header">
                    Información del Cobrador
                </div>
                <div class="card-body">
                    <p><strong>Base Asignada:</strong> <?= $base['base'] ?> (Asignada el <?= $base['fecha'] ?>)</p>
                    <p><strong>Ruta:</strong> <?= $ruta['nombre_ruta'] ?></p>
                </div>
            </div>

            <!-- Últimos 4 gastos registrados -->
            <div class="card mb-4">
                <div class="card-header">
                    Últimos 4 Gastos Registrados
                </div>
                <div class="card-body">
                    <?php if (!empty($gastos)): ?>
                        <ul class="list-group">
                            <?php foreach ($gastos as $gasto): ?>
                                <li class="list-group-item">
                                    <strong><?= $gasto['comentarios'] ?>:</strong>   <?= number_format($gasto['monto'], 2) ?> - <em><?= $gasto['fecha_creacion'] ?></em>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No se han registrado gastos recientes.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Permisos del usuario -->
        <div class="card mb-4">
            <div class="card-header">
                Permisos del Usuario
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php foreach ($permisos as $permiso): ?>
                        <li class="list-group-item"><?= $permiso['permiso'] ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
