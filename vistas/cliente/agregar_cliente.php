<?php
session_start();
include '../../includes/header2.php';
include '../../includes/functions.php';

// Verificar si hay una cuenta activa en la sesión
if (!isset($_SESSION['id_cuenta'])) {
    echo "<script>
            alert('No tienes una cuenta seleccionada.');
            window.location.href = '../../seleccionar_cuenta.php';
          </script>";
    exit();
}

$id_cuenta = $_SESSION['id_cuenta']; // Cuenta activa
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $id_genero = $_POST['id_genero'];
    $direccion_casa = $_POST['direccion_casa'];
    $direccion_negocio = $_POST['direccion_negocio'];
    $telefono = $_POST['telefono'];
    $cedula = $_POST['cedula'];
    $ruta = $_POST['id_ruta'];

    // Validar si la cédula ya existe en esta cuenta
    if (cedulaExists($cedula, $id_cuenta)) {
        $message = 'Error: La cédula ya está registrada en esta cuenta.';
    } else {
        $id_cliente = agregarCliente($nombres, $apellidos, $id_genero, $direccion_casa, $direccion_negocio, $telefono, $cedula, $ruta, $id_cuenta);

        if ($id_cliente) {
            $message = 'Cliente agregado con éxito.';
            echo "<script>
                setTimeout(function() {
                    window.location.href = '../clientes.php';
                }, 1500);
            </script>";
        } else {
            $message = 'No se pudo agregar el cliente.';
        }
    }
}

// Obtener rutas y géneros
$ruta = getAllRutas();
$generos = getGeneros();

// Función para verificar si la cédula ya existe en la misma cuenta
function cedulaExists($cedula, $id_cuenta) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cliente WHERE cedula = :cedula AND id_cuenta = :id_cuenta");
    $stmt->execute(['cedula' => $cedula, 'id_cuenta' => $id_cuenta]);
    return $stmt->fetchColumn() > 0;
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Cliente y Garantía</title>
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

    <form id="clienteForm" action="agregar_cliente.php" method="post" enctype="multipart/form-data">
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
            <input type="text" class="form-control" id="direccion_negocio" name="direccion_negocio">
        </div>
        <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="text" class="form-control" id="telefono" name="telefono" required>
        </div>
        <div class="form-group">
            <label for="cedula">Cédula</label>
            <input type="text" class="form-control" id="cedula" name="cedula" required>
        </div>
        <div class="form-group">
            <label for="ruta">Rutas</label>
            <select id="id_ruta" name="id_ruta" class="form-control" required>
                <?php foreach ($ruta as $rutas): ?>
                    <option value="<?php echo htmlspecialchars($rutas['id_ruta']); ?>">
                        <?php echo htmlspecialchars($rutas['nombre_ruta']); ?> - <?php echo htmlspecialchars($rutas['ciudad']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-success btn-block">Guardar</button>
    </form>
</div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("clienteForm");

        form.addEventListener("submit", function(e) {
            e.preventDefault();

            const nombres = document.getElementById("nombres").value.trim();
            const apellidos = document.getElementById("apellidos").value.trim();
            const telefono = document.getElementById("telefono").value.trim();
            const cedula = document.getElementById("cedula").value.trim();

            if (!/^[A-Za-záéíóúÁÉÍÓÚñÑ\s¿?]+$/.test(nombres)) {
                mostrarAlertaError('Error en Nombres', 'Ingrese un nombre válido.');
            } else if (!/^[A-Za-záéíóúÁÉÍÓÚñÑ\s¿?]+$/.test(apellidos) {
                mostrarAlertaError('Error en Apellidos', 'Ingrese apellidos válidos.');
            } else if (!/^\d{7,10}$/.test(telefono)) {
                mostrarAlertaError('Error en Teléfono', 'Ingrese un teléfono válido (7-10 dígitos).');
            } else if (!/^\d+$/.test(cedula)) {
                mostrarAlertaError('Error en Cédula', 'Ingrese una cédula válida.');
            } else {
                form.submit();
            }
        });

        function mostrarAlertaError(titulo, mensaje) {
            Swal.fire({
                icon: 'error',
                title: titulo,
                text: mensaje
            });
        }
    });
</script>

<?php include '../../includes/footer.php'; ?>
</body>
</html>