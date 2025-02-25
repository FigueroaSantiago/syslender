<?php
session_start();
include '../includes/header.php';

// Redirige si el usuario no está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Obtener alerta desde la sesión, si existe
$alerta = $_SESSION['alerta'] ?? null;
unset($_SESSION['alerta']);  // Eliminar la alerta de la sesión después de obtenerla
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Préstamos</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Listado de Préstamos</h1>
        <a href="prestamo/agregar_prestamo.php" class="btn btn-custom btn-success mb-3">Agregar Préstamo</a>
    </div>

    <!-- Barra de búsqueda -->
    <div class="input-group mb-4">
        <input type="text" id="buscar" class="form-control" placeholder="Buscar por Cliente, Monto, etc.">
        <div class="input-group-append">
            <span class="input-group-text"><i class="fa fa-search"></i></span>
        </div>
    </div>

    <!-- Tabla de préstamos -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Monto</th>
                <th>Intereses</th>
                <th>Monto Actual</th>
                <th>Fecha de Desembolso</th>
                <th>Fecha de Vencimiento</th>
                <th>Número de Cuotas</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tabla-prestamos">
            <?php
            include_once '../includes/functions.php';
            $prestamos = getAllPrestamos();
            foreach ($prestamos as $prestamo) {
                echo "<tr>
                    <td>{$prestamo['cliente']}</td>
                    <td>{$prestamo['principal_prestamo']}</td>
                    <td>{$prestamo['interes_total']}</td>
                    <td>{$prestamo['saldo_actual']}</td>
                    <td>{$prestamo['inicio_fecha']}</td>
                    <td>{$prestamo['vencimiento_fecha']}</td>
                    <td>{$prestamo['duracion']}</td>
                    <td>{$prestamo['estado']}</td>
                    <td class='action-buttons'>
                        <a href='prestamo/editar_prestamo.php?id={$prestamo['id_prestamo']}' class='btn btn-warning btn-custom btn-sm'><i class='fas fa-edit'></i> Editar</a>
                        <a href='prestamo/eliminar_prestamo.php?id={$prestamo['id_prestamo']}' class='btn btn-danger btn-custom btn-sm'><i class='fas fa-trash-alt'></i> Eliminar</a>
                    </td>
                    <td>{$prestamo['id_rol_user']}</td>
                </tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Script para mostrar la alerta si existe -->
<script>
    <?php if (isset($alerta)): ?>
        Swal.fire({
            icon: '<?php echo $alerta['tipo']; ?>',
            title: '<?php echo ucfirst($alerta['tipo']); ?>',
            text: '<?php echo $alerta['mensaje']; ?>'
        });
    <?php endif; ?>
</script>

<!-- Script para filtros -->
<script>
    document.getElementById('buscar').addEventListener('input', function() {
        let input = this.value.toLowerCase();
        let filas = document.querySelectorAll('#tabla-prestamos tr');

        filas.forEach(function(fila) {
            let texto = fila.textContent.toLowerCase();
            fila.style.display = texto.includes(input) ? '' : 'none';
        });
    });
</script>

</body>
</html>
