<?php
session_start();
include '../../includes/header2co.php';
include '../../includes/functions.php';
include '../../includes/db.php';

// Validación de sesión activa
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header('Location: ../login/login.php');
    exit();
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

// Verificar si se encontraron los datos del usuario
if ($datos_usuario) {
    $_SESSION['nombre'] = $datos_usuario['nombre'];
    $_SESSION['rol_user_id'] = $datos_usuario['id_rol_user'];
    $_SESSION['rol'] = $datos_usuario['rol'];
} else {
    // Manejo de error: el usuario no tiene un rol asociado
    die("El usuario no tiene un rol asociado.");
}

// Puedes usar aquí los datos obtenidos de $_SESSION si los necesitas
//echo "Nombre: " . $_SESSION['nombre'] . "<br>";
//echo "Rol: " . $_SESSION['rol'] . "<br>";
//echo "ID Rol Usuario: " . $_SESSION['rol_user_id'] . "<br>";




$tipos_gastos = $pdo->query("SELECT id_tipo_gasto, descripcion FROM tipo_gastos")->fetchAll(PDO::FETCH_ASSOC);

// Manejo del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipo_gasto = $_POST['tipo_gasto'];
    $monto = $_POST['monto'];
    $comentarios = $_POST['comentarios'];
    $id_usuario = $_SESSION['user_id']; // ID del cobrador

    // Insertar la solicitud en la tabla `gastos` con estado pendiente
    $stmt = $pdo->prepare("
        INSERT INTO gastos (id_rol_user, id_tipo_gasto, monto, comentarios, creado_por, fecha_creacion)
        VALUES (:id_rol_user, :id_tipo_gasto, :monto, :comentarios, :creado_por, NOW())
    ");
    $stmt->execute([
        'id_rol_user' => $_SESSION['rol_user_id'], // Relación del usuario con la base
        'id_tipo_gasto' => $tipo_gasto,
        'monto' => $monto,
        'comentarios' => $comentarios,
        'creado_por' => $_SESSION['rol_user_id'],
    ]);

    $_SESSION['mensaje'] = 'Solicitud enviada exitosamente. Espera aprobación.';
    header('Location: solicitar_gastos.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f4f7fa;
            font-family: 'Arial', sans-serif;
        }

        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 30px;
        }

        .form-group label {
            font-weight: bold;
        }

        .btn-success {
            border-radius: 25px;
            padding: 10px 30px;
            font-size: 1rem;
            transition: background-color 0.3s, transform 0.2s;
        }

        .btn-success:hover {
            transform: scale(1.05);
        }

        .form-control {
            border-radius: 25px;
        }

        .input-group-text {
            background-color: #28a745;
            color: white;
        }
    </style>
    <title>Subpágina - Gucobro</title>
</head>
<body>
<div class="container">
    <div class="form-container">
        <h1 class="mb-4">Solicitar Gasto</h1>
        
        <!-- Mostrar mensajes -->
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?>
            </div>
        <?php endif; ?>

        <form action="solicitar_gastos.php" method="POST">
            <div class="form-group">
                <label for="tipo_gasto">Tipo de Gasto</label>
                <select id="tipo_gasto" name="tipo_gasto" class="form-control" required>
                    <option value="">Seleccionar...</option>
                    <?php foreach ($tipos_gastos as $tipo): ?>
                        <option value="<?= $tipo['id_tipo_gasto']; ?>"><?= $tipo['descripcion']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="monto">Monto</label>
                <input type="number" id="monto" name="monto" class="form-control" min="0.01" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="comentarios">Comentarios</label>
                <textarea id="comentarios" name="comentarios" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
        </form>
    </div>
    </div>
   

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
