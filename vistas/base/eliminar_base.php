<?php
include '../../includes/header2.php';
include '../../includes/functions.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Verifica si se envió la confirmación de eliminación
    if (isset($_POST['confirmar_eliminacion'])) {
        try {
            // Eliminar base
            $stmt = $pdo->prepare("DELETE FROM base WHERE id_base = ?");
            $stmt->execute([$id]);

            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: '¡Eliminado!',
                    text: 'La base se ha eliminado correctamente.',
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
    <title>Eliminar Base</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡Esta acción no se puede deshacer!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Enviar el formulario de confirmación automáticamente
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = ''; // Mismo script PHP
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'confirmar_eliminacion';
            input.value = '1';
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        } else {
            // Redirigir si se cancela
            window.location.href = '../base.php';
        }
    });
});
</script>

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
