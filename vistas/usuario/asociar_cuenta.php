<?php
session_start();
include '../../includes/header2.php';
include '../../Conexion/conexion.php';

// Obtener el ID del admin recién creado
if (!isset($_SESSION['id_admin_creado'])) {
    echo "<script>
            alert('Error: No hay un administrador para asociar.');
            window.location.href = 'agregar_usuarios.php'; 
          </script>";
    exit();
}

$id_admin = $_SESSION['id_admin_creado'];

// Obtener los datos del administrador recién creado
$sql = "SELECT nombre, apellido, cedula FROM user WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_admin);
$stmt->execute();
$admin_data = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asociar Cuenta</title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
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

        .error {
            color: red;
            font-size: 0.9em;
        }

        .input-group-text {
            background-color: #28a745;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="form-container">
            <h2 class="mb-4">Asociar Administrador a una Cuenta</h2>

            <!-- 📌 Datos del Administrador Recién Creado -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Datos del Administrador</h5>
                    <p><strong>Nombre:</strong> <?= $admin_data['nombre'] . " " . $admin_data['apellido']; ?></p>
                    <p><strong>Cédula:</strong> <?= $admin_data['cedula']; ?></p>
                </div>
            </div>

            <!-- 📌 Formulario para asociar cuenta -->
            <form action="procesar_asociacion.php" method="POST">
                <!-- Campo oculto con el ID del Administrador -->
                <input type="hidden" name="id_admin" value="<?= $id_admin ?>">

                <div class="form-group">
                    <label>Seleccionar Cuenta:</label>
                    <select name="id_cuenta" class="form-control" required>
                        <option value="">Seleccionar...</option>
                        <?php
                        $sql = "SELECT id_cuenta, nombre FROM cuentas";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $cuentas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                        foreach ($cuentas as $cuenta): ?>
                            <option value="<?= $cuenta['id_cuenta'] ?>"><?= $cuenta['nombre'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Asociar</button>
            </form>
        </div>
    </div>
</body>
</html>
