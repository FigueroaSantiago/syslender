<?php
session_start();
include '../../includes/header2co.php';
include '../../includes/db.php';
include '../../includes/functions.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id'])) {
    die('Acceso denegado.');
}

// Obtener id_ruta asociado al cobrador
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

$message = ''; // Para almacenar el mensaje de éxito o error

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $id_genero = $_POST['id_genero'];
    $direccion_casa = trim($_POST['direccion_casa']) ?? '';
    $direccion_negocio = trim($_POST['direccion_negocio']) ?? '';
    $telefono = $_POST['telefono'];
    $cedula = $_POST['cedula'];

    // Validar si la cédula ya existe
    if (cedulaExists($cedula)) {
        $message = 'Error: La cédula ya está registrada.';
    } else {
        $id_cliente = agregarCliente($nombres, $apellidos, $id_genero, $direccion_casa, $direccion_negocio, $telefono, $cedula, $id_ruta);
        
        if ($id_cliente) {
            $message = 'Cliente agregado con éxito.';
            echo "<script>
                setTimeout(function() {
                    window.location.href = '../clientesco.php';
               }, 1500);
            </script>";
        } else {
            $message = 'No se pudo agregar el cliente.';
        }
    }
}

$generos = getGeneros();

function cedulaExists($cedula) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cliente WHERE cedula = :cedula");
    $stmt->execute(['cedula' => $cedula]);
    return $stmt->fetchColumn() > 0;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Cliente</title>
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
    </style>
</head>
<body>
<div class="container">
<div class="form-container">
    <h1 class="mb-4">Agregar Cliente</h1>

    <?php if ($message): ?>
        <script>
            Swal.fire({
                icon: '<?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>',
                title: '<?php echo strpos($message, 'Error') !== false ? 'Error' : 'Éxito'; ?>',
                text: '<?php echo $message; ?>',
                showConfirmButton: true
            });
        </script>
    <?php endif; ?>

    <form id="clienteForm" action="agregar_clienteco.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nombres">Nombres</label>
            <input type="text" class="form-control" id="nombres" name="nombres" required>
        </div>
        <div class="form-group">
            <label for="apellidos">Apellidos</label>
            <input type="text" class="form-control" id="apellidos" name="apellidos" required>
        </div>
        <div class="form-group">
            <label for="id_genero">Género</label>
            <select class="form-control" id="id_genero" name="id_genero" required>
                <?php foreach ($generos as $genero): ?>
                    <option value="<?= $genero['id_genero'] ?>"><?= $genero['genero'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="direccion_casa">Dirección Casa</label>
            <input type="text" class="form-control" id="direccion_casa" name="direccion_casa" required>
        </div>
        <div class="form-group">
            <label for="direccion_negocio">Dirección Negocio</label>
            <input type="text" class="form-control" name="direccion_negocio" id="direccion_negocio">
        </div>
        <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="text" class="form-control" id="telefono" name="telefono" required>
        </div>
        <div class="form-group">
            <label for="cedula">Cédula</label>
            <input type="text" class="form-control" id="cedula" name="cedula" required>
        </div>
        <!-- Ruta oculta -->
        <input type="hidden" name="id_ruta" value="<?php echo htmlspecialchars($id_ruta); ?>">

        <button type="submit" class="btn btn-success btn-block">Guardar</button>
    </form>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
