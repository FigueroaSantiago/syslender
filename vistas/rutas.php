<?php
session_start();
include '../includes/header.php';
include '../includes/functions.php';

// Redirige si el usuario no está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Obtiene todas las rutas
$rutas = getAllRutas();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Rutas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Arial', sans-serif;
        }
        .card {
            margin-bottom: 20px;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-body {
            text-align: center;
        }
        .role-header {
            background-color: #343a40;
            color: white;
            padding: 10px;
            border-radius: 10px 10px 0 0;
        }
        .modal-content {
            border-radius: 20px;
        }
        .btn-custom {
            border-radius: 20px;
            transition: background-color 0.3s, transform 0.2s;
        }
        .btn-custom:hover {
            transform: scale(1.05);
        }
        .add-role-btn {
            background-color: #007bff;
            color: white;
            border-radius: 50px;
            padding: 10px 20px;
            text-decoration: none;
        }
        .add-role-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4 text-center">Listado de Rutas</h1>
   
            <a href="ruta/agregar_ruta.php" class="btn btn-custom btn-success mb-3">Agregar Ruta</a>
      

        <div class="row">
            <?php if (empty($rutas)): ?>
                <div class="col-12 text-center">
                    <p>No hay rutas disponibles.</p>
                </div>
            <?php else: ?>
                <?php foreach ($rutas as $ruta): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="role-header text-center">
                                <h5><?php echo htmlspecialchars($ruta['nombre_ruta']); ?></h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Ciudad: <?php echo htmlspecialchars($ruta['ciudad']); ?></p>
                                <p class="card-text">Cobrador: <?php echo htmlspecialchars($ruta['nombre_usuario']); ?></p>
                                <button class="btn btn-secondary btn-custom" data-toggle="modal" data-target="#modalRuta<?php echo $ruta['id_ruta']; ?>"><i class='fas fa-info-circle'></i> Ver Detalles</button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal con detalles de la ruta -->
                    <div class="modal fade" id="modalRuta<?php echo $ruta['id_ruta']; ?>" tabindex="-1" aria-labelledby="modalRutaLabel<?php echo $ruta['id_ruta']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalRutaLabel<?php echo $ruta['id_ruta']; ?>">Detalles de la Ruta: <?php echo htmlspecialchars($ruta['nombre_ruta']); ?></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Ciudad:</strong> <?php echo htmlspecialchars($ruta['ciudad']); ?></p>
                                    <p><strong>Cobrador:</strong> <?php echo htmlspecialchars($ruta['nombre_usuario']); ?></p>
                                    <p><strong>Clientes:</strong></p>
                                    <ul>
                                        <?php 
                                        $clientes = getClientesByRuta($ruta['id_ruta']); 
                                        foreach ($clientes as $cliente): 
                                        ?>
                                            <li><?php echo htmlspecialchars($cliente['nombres']); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <div class="modal-footer">
                                    <a href="ruta/editar_ruta.php?id=<?php echo htmlspecialchars($ruta['id_ruta']); ?>" class="btn btn-warning btn-custom"><i class="fas fa-edit"></i> Editar</a>
                                    <a href="#" class="btn btn-danger btn-custom" onclick="confirmarEliminacion(<?php echo htmlspecialchars($ruta['id_ruta']); ?>); return false;">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
function confirmarEliminacion(id_ruta) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "No podrás revertir esta acción.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
    url: 'ruta/eliminar_ruta.php',
    type: 'POST',
    data: { id_ruta: id_ruta },
    success: function(response) {
        response = response.trim(); // Recortamos espacios en blanco extra
        if (response === 'success') {
            Swal.fire(
                'Eliminado con éxito',
                'La ruta ha sido eliminada.',
                'success'
            ).then(() => {
                location.reload();
            });
        } else if (response === 'constraint_error') {
            Swal.fire(
                'Advertencia',
                'No se puede eliminar la ruta ya que tiene clientes asociados.',
                'warning'
            );
        } else {
            Swal.fire(
                'Error!',
                'Ocurrió un error al intentar eliminar la ruta.',
                'error'
            );
        }
    },
    error: function() {
        Swal.fire(
            'Error!',
            'Error de comunicación con el servidor.',
            'error'
        );
    }
});

        }
    });
}

    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
