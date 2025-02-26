<?php
include '../../includes/header2.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta</title>
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
            <h2 class="mb-4">Crear Nueva Cuenta</h2>
            <form action="procesar_creacion_cuenta.php" method="POST">
                <div class="form-group">
                    <label>Código de Cuenta:</label>
                    <input type="text" name="cod_cuenta" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Nombre de la Cuenta:</label>
                    <input type="text" name="nombre_cuenta" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Empresa:</label>
                    <input type="text" name="empresa" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Código SIRC:</label>
                    <input type="text" name="cod_sirc" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Fecha de Vencimiento:</label>
                    <input type="date" name="fecha_vencimiento" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Estado:</label>
                    <select name="estado" class="form-control">
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success btn-block">Crear Cuenta</button>
            </form>
        </div>
    </div>
    <?php if (isset($_SESSION['response'])) : ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: '<?php echo $_SESSION['response']['status']; ?>',
            title: '<?php echo ucfirst($_SESSION['response']['status']); ?>',
            text: '<?php echo $_SESSION['response']['message']; ?>'
        }).then(() => {
            window.location.href = 'crear_cuenta.php';
        });
    </script>
    <?php unset($_SESSION['response']); ?>
<?php endif; ?>

</body>
</html>
