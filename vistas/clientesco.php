<?php
session_start();
include '../includes/headerco.php';
include '../includes/db.php';
include '../includes/functions.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id'])) {
    die('Acceso denegado.');
}

// Obtener datos del usuario para la sesión
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

// Muestra los valores
//echo "Nombre: " . $_SESSION['nombre'] . "<br>";
//echo "Rol: " . $_SESSION['rol'] . "<br>";
//echo "ID Rol Usuario: " . $_SESSION['rol_user_id'] . "<br>";

// Obtener id_ruta asociado al rol del usuario
$stmt = $pdo->prepare("
    SELECT r.id_ruta 
    FROM ruta AS r
    JOIN rol_user AS ru ON r.id_rol_user = ru.id_rol_user
    WHERE ru.id_rol_user = :id_rol_user
");
$stmt->bindParam(':id_rol_user', $_SESSION['rol_user_id'], PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$result) {
    die('Error: No se encontró una ruta asociada al usuario.');
}

$id_ruta = $result['id_ruta'];

// Obtener clientes asociados a la ruta
$clientes = getClientesByRutaForUser($pdo, $id_ruta);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Clientes</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
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
    </style>
</head>
<body>
<div class="container">
    <h1>Listado de Clientes</h1>
    <a href="cliente/agregar_clienteco.php" class="btn btn-custom btn-success mb-3">Agregar Cliente</a>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Dirección Casa</th>
                <th>Dirección Negocio</th>
                <th>Teléfono</th>
                <th>Cédula</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?php echo $cliente['nombres']; ?></td>
                    <td><?php echo $cliente['apellidos']; ?></td>
                    <td><?php echo $cliente['direccion_casa']; ?></td>
                    <td><?php echo $cliente['direccion_negocio']; ?></td>
                    <td><?php echo $cliente['telefono']; ?></td>
                    <td><?php echo $cliente['cedula']; ?></td>
                    <td class="action-buttons">
                        <a href="cliente/detalles.php?id=<?php echo $cliente['id_cliente']; ?>" class="btn btn-secondary btn-custom btn-sm">
                            <i class="fas fa-info-circle"></i> Ver Detalles
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
