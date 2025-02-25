<?php
session_start();
include '../../includes/functions.php';
include '../../includes/header2.php';

// Verifica si se ha pasado el ID del préstamo
if (!isset($_GET['id'])) {
    die('ID del préstamo no proporcionado');
}

$id_prestamo = $_GET['id'];

// Verifica que la función getPrestamoById esté definida en functions.php
$prestamo = getPrestamoById($id_prestamo);

if (!$prestamo) {
    die('Préstamo no encontrado');
}

// Generar un token CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Token CSRF único
}
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Préstamo</title>
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
        <h1 class="mb-4">Editar Préstamo</h1>

        <!-- Mostrar alertas si existen -->
        <?php if (isset($_SESSION['alerta'])): ?>
            <div class="alert alert-<?php echo $_SESSION['alerta']['tipo']; ?> alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['alerta']['mensaje']; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php unset($_SESSION['alerta']); ?>
        <?php endif; ?>

        <form id="prestamoForm" action="procesar_editar_prestamo.php" method="POST">
            <input type="hidden" name="id_prestamo" value="<?php echo $prestamo['id_prestamo']; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <div class="form-group">
                <label for="cliente">Cliente</label>
                <select id="cliente" name="id_cliente" class="form-control">
                    <?php
                    $clientes = getAllClientes();
                    foreach ($clientes as $cliente) {
                        $selected = ($cliente['id_cliente'] == $prestamo['id_cliente']) ? 'selected' : '';
                        echo "<option value='{$cliente['id_cliente']}' $selected>{$cliente['nombres']} {$cliente['apellidos']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="monto">Monto</label>
                <input type="number" id="monto" name="monto" class="form-control" value="<?php echo $prestamo['monto_inicial']; ?>" required>
            </div>

            <div class="form-group">
                <label for="fecha_desembolso">Fecha de Desembolso</label>
                <input type="date" id="fecha_desembolso" name="fecha_desembolso" class="form-control" value="<?php echo $prestamo['inicio_fecha']; ?>" required>
            </div>

            <div class="form-group">
                <label for="duracion">Duración del Crédito (días)</label>
                <select id="duracion" name="duracion" class="form-control" required>
                    <option value="20" <?= $prestamo['duracion'] == 20 ? 'selected' : '' ?>>20 días</option>
                    <option value="24" <?= $prestamo['duracion'] == 24 ? 'selected' : '' ?>>24 días</option>
                </select>
            </div>

            <div class="form-group">
                <label for="fecha_vencimiento">Fecha de Vencimiento</label>
                <input type="text" id="fecha_vencimiento" name="fecha_vencimiento" class="form-control" readonly>
            </div>

            <button type="submit" class="btn btn-success btn-block">Guardar</button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    // Función para calcular la fecha de vencimiento
    function calcularFechaVencimiento() {
        const fechaDesembolso = new Date(document.getElementById('fecha_desembolso').value);
        const duracion = parseInt(document.getElementById('duracion').value);

        if (!isNaN(fechaDesembolso) && !isNaN(duracion)) {
            let fechaVencimiento = new Date(fechaDesembolso);
            let dias = 0;

            while (dias < duracion) {
                fechaVencimiento.setDate(fechaVencimiento.getDate() + 1);
                if (fechaVencimiento.getDay() !== 0) { // Excluir domingos
                    dias++;
                }
            }

            document.getElementById('fecha_vencimiento').value = fechaVencimiento.toISOString().slice(0, 10);
        }
    }

    document.getElementById('fecha_desembolso').addEventListener('change', calcularFechaVencimiento);
    document.getElementById('duracion').addEventListener('change', calcularFechaVencimiento);
    calcularFechaVencimiento();
</script>

</body>
</html>
