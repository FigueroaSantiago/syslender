<?php
session_start();
include '../../includes/functions.php';
include '../../includes/header2.php';

// Verifica si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Verifica si el usuario tiene el rol de cobrador o administrador
if ($_SESSION['role_id'] != 2 && $_SESSION['role_id'] != 1) {
    header('Location: no_permitido.php');
    exit();
}

// Obtener alerta desde la sesión, si existe
$alerta = $_SESSION['alerta'] ?? null;
unset($_SESSION['alerta']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Préstamo</title>
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
</head>

<body>
<div class="container">
    <div class="form-container">
        <h1 class="mb-4">Agregar Préstamo</h1>

        <form id="prestamoForm" action="procesar_agregar_prestamo.php" method="POST">
            <!-- Campo para seleccionar la ruta -->
            <div class="form-group">
                <label for="ruta">Ruta</label>
                <select id="ruta" class="form-control">
                    <option value="" disabled selected>Selecciona una ruta</option>
                    <?php
                    $rutas = getAllRutas(); // Supongamos que esta función devuelve las rutas
                    foreach ($rutas as $ruta) {
                        echo "<option value='{$ruta['id_ruta']}'>{$ruta['nombre_ruta']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Campo para seleccionar el cliente -->
            <div class="form-group">
                <label for="cliente">Cliente</label>
                <select id="cliente" name="id_cliente" class="form-control" required>
                    <option value="" disabled selected>Selecciona un cliente</option>
                    <?php
                    $clientes = getAllClientes();
                    foreach ($clientes as $cliente) {
                        echo "<option value='{$cliente['id_cliente']}' data-ruta='{$cliente['id_ruta']}'>
                                {$cliente['nombres']} {$cliente['apellidos']}
                              </option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="monto_inicial">Monto Inicial</label>
                <input type="number" id="monto_inicial" name="monto_inicial" class="form-control" required min="1" step="0.01">
            </div>

            <div class="form-group">
                <label for="fecha_desembolso">Fecha de Desembolso</label>
                <input type="date" id="fecha_desembolso" name="fecha_desembolso" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="duracion">Duración del Crédito (días)</label>
                <select id="duracion" name="duracion" class="form-control" required>
                    <option value="" disabled selected>Selecciona la duración</option>
                    <option value="20">20 días</option>
                    <option value="24">24 días</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success btn-block">Guardar</button>
        </form>
    </div>
</div>

<script>
    // Filtrar clientes por ruta seleccionada
    document.getElementById('ruta').addEventListener('change', function() {
        var rutaSeleccionada = this.value;
        var clientes = document.querySelectorAll('#cliente option');

        // Mostrar u ocultar clientes según la ruta seleccionada
        clientes.forEach(function(cliente) {
            if (!cliente.dataset.ruta || cliente.dataset.ruta === rutaSeleccionada) {
                cliente.style.display = '';
            } else {
                cliente.style.display = 'none';
            }
        });

        // Resetear el valor del select de clientes
        document.getElementById('cliente').value = '';
    });

    // Validaciones del lado del cliente
    document.querySelector('#prestamoForm').addEventListener('submit', function(event) {
        var monto = document.getElementById('monto_inicial').value;
        var duracion = document.getElementById('duracion').value;

        if (monto <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El monto inicial debe ser un número positivo.'
            });
            event.preventDefault();
        }

        if (duracion === "") {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Por favor, selecciona una duración válida.'
            });
            event.preventDefault();
        }
    });
    // Validaciones del lado del cliente
    document.querySelector('#prestamoForm').addEventListener('submit', function(event) {
        var monto = document.getElementById('monto_inicial').value;
        var duracion = document.getElementById('duracion').value;

        // Validación del monto (debe ser un número positivo)
        if (monto <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El monto inicial debe ser un número positivo.'
            });
            event.preventDefault();  // Evitar el envío del formulario
        }

        // Validación de la duración (debe estar seleccionada)
        if (duracion === "") {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Por favor, selecciona una duración válida.'
            });
            event.preventDefault();  // Evitar el envío del formulario
        }
    });

    // Mostrar alerta de sesión si existe
    <?php if (isset($alerta)): ?>
        Swal.fire({
            icon: '<?php echo $alerta['tipo']; ?>',
            title: '<?php echo ucfirst($alerta['tipo']); ?>',
            text: '<?php echo $alerta['mensaje']; ?>'
        }).then((result) => {
            // Redirigir después de confirmar la alerta, solo si es necesario
            if ('<?php echo $alerta['tipo']; ?>' === 'success') {
                window.location.href = '../prestamos.php';
            }
        });
    <?php endif; ?>
</script>
</body>
</html>
