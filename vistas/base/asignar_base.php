<?php
include '../../includes/header2.php';
include '../../includes/functions.php';

// Obtener todos los cobradores
$cobradores = $pdo->query("
    SELECT ru.id_rol_user, u.nombre 
    FROM rol_user ru
    JOIN user u ON ru.id_user = u.id_user
    JOIN rol r ON ru.id_rol = r.id_rol
    WHERE r.rol = 'cobrador'
")->fetchAll(PDO::FETCH_ASSOC);

$error = null; // Variable para manejar errores

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $id_cobrador = $_POST['id_cobrador'];
        $base_cantidad = $_POST['base'];
        $fecha = $_POST['fecha'];

        // Verificar si ya tiene asignación
        $stmt_verificar = $pdo->prepare("
            SELECT COUNT(*) 
            FROM asignacion_base 
            WHERE id_cobrador = ? 
        ");
        $stmt_verificar->execute([$id_cobrador]);
        $ya_asignado = $stmt_verificar->fetchColumn();

        if ($ya_asignado > 0) {
            throw new Exception('El cobrador ya tiene una base asignada');
        }

        // Insertar base
        $stmt_base = $pdo->prepare("INSERT INTO base (base, fecha) VALUES (?, ?)");
        $stmt_base->execute([$base_cantidad, $fecha]);

        $id_base = $pdo->lastInsertId();

        // Insertar asignación
        $stmt_asignacion = $pdo->prepare("INSERT INTO asignacion_base (id_cobrador, id_base, fecha_asignacion) VALUES (?, ?, ?)");
        $stmt_asignacion->execute([$id_cobrador, $id_base, $fecha]);

        echo "<script>
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: 'La base se ha asignado correctamente.',
            }).then(() => {
                window.location.href = '../base.php';
            });
        </script>";
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
    } catch (PDOException $e) {
        $error = 'Error en la base de datos: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Base a Cobrador</title>
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
        <h1 class="text-center mb-4">Asignar Base a Cobrador</h1>

        <form id="form-asignar" method="POST">
            <div class="mb-3">
                <label for="id_cobrador" class="form-label">Cobrador</label>
                <select name="id_cobrador" id="id_cobrador" class="form-select" required>
                    <option value="" disabled selected>Seleccione un cobrador</option>
                    <?php foreach ($cobradores as $cobrador): ?>
                        <option value="<?= $cobrador['id_rol_user'] ?>"><?= htmlspecialchars($cobrador['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="base" class="form-label">Cantidad de Base</label>
                <input type="number" name="base" id="base" class="form-control" placeholder="Ingrese cantidad de base" required>
            </div>

            <div class="mb-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" name="fecha" id="fecha" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success">Asignar Base</button>
        </form>
    </div>
</div>

<script>
document.getElementById('form-asignar').addEventListener('submit', async function (e) {
    e.preventDefault();

    const idCobrador = document.getElementById('id_cobrador').value;
    const base = document.getElementById('base').value;
    const fecha = document.getElementById('fecha').value;

    if (!idCobrador || !base || !fecha) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos incompletos',
            text: 'Por favor, complete todos los campos.',
        });
        return;
    }

    this.submit();
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<?php if ($error): ?>
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
