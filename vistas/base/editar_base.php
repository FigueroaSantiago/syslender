<?php
include '../../includes/header2.php';
include '../../includes/functions.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Obtener la base existente
    $stmt = $pdo->prepare("SELECT * FROM base WHERE id_base = ?");
    $stmt->execute([$id]);
    $base = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nueva_base = $_POST['base'];
        $nueva_fecha = $_POST['fecha'];

        try {
            // Actualizar base
            $stmt = $pdo->prepare("UPDATE base SET base = ?, fecha = ? WHERE id_base = ?");
            $stmt->execute([$nueva_base, $nueva_fecha, $id]);

            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'La base se ha actualizado correctamente.',
                }).then(() => {
                    window.location.href = '../base.php';
                });
            </script>";
            exit();
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Base</title>
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
        <h1 class="text-center mb-4">Editar Base</h1>

        <form id="form-editar" method="POST">
            <div class="mb-3">
                <label for="base" class="form-label">Cantidad de Base</label>
                <input type="number" name="base" class="form-control" id="base" value="<?= $base['base'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" id="fecha" value="<?= $base['fecha'] ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Actualizar Base</button>
        </form>
    </div>
</div>

<script>
document.getElementById('form-editar').addEventListener('submit', function (e) {
    e.preventDefault();

    const base = document.getElementById('base').value;
    const fecha = document.getElementById('fecha').value;

    // Validar campos
    if (!base || !fecha) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos incompletos',
            text: 'Por favor, complete todos los campos.',
        });
        return;
    }

    // Confirmación antes de enviar el formulario
    Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Deseas guardar los cambios realizados en la base?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, actualizar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Actualizando...',
                text: 'Espere un momento.',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                document.getElementById('form-editar').submit();
            });
        }
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<?php if (isset($error)): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: <?= json_encode($error) ?>,
});
</script>
<?php endif; ?>
</body>
</html>
