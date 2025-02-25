<?php
session_start(); // Asegurar que la sesión está activa
include '../includes/header.php';
include '../includes/functions.php';

// Verificar que haya una cuenta seleccionada
if (!isset($_SESSION['id_cuenta'])) {
    echo "<script>
            alert('No tienes una cuenta seleccionada.');
            window.location.href = '../seleccionar_cuenta.php';
          </script>";
    exit();
}

// Obtener clientes solo de la cuenta activa
$clientes = getAllClientes();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Clientes</title>
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
    <h1>Listado de Clientes</h1>
    <a href="cliente/agregar_cliente.php" class="btn btn-custom btn-success mb-3">Agregar Cliente</a>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Dirección Casa</th>
                <th>Dirección Negocio</th>
                <th>Teléfono</th>
                <th>Cédula</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $cliente): ?>
                <tr>
                  
                    <td><?php echo $cliente['nombres']; ?></td>
                    <td><?php echo $cliente['apellidos']; ?></td>
                    <td><?php echo $cliente['direccion_casa']; ?></td>
                    <td><?php echo $cliente['direccion_negocio']; ?></td>
                    <td><?php echo $cliente['telefono']; ?></td>
                    <td><?php echo $cliente['cedula']; ?></td>
                    <td class="action-buttons">
                        <a href="cliente/editar_cliente.php?id=<?php echo $cliente['id_cliente']; ?>" class="btn btn-warning btn-custom btn-sm"><i class="fas fa-edit"></i> Editar</a>
                        <a href="cliente/detalles.php?id=<?php echo $cliente['id_cliente']; ?>" class="btn btn-secondary btn-custom btn-sm">
    <i class="fas fa-info-circle"></i> Ver Detalles
</a>

                        <button onclick="confirmarEliminacion(<?php echo $cliente['id_cliente']; ?>)" class="btn btn-danger btn-custom btn-sm"><i class="fas fa-trash-alt"></i> Eliminar</button>
                    </td>
                </tr>
            <?php endforeach; ?>    
        </tbody>
    </table>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
function confirmarEliminacion(id_cliente) {
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
                url: 'cliente/eliminar_cliente.php', // Asegúrate de que la URL sea correcta
                type: 'POST',
                data: { id_cliente: id_cliente },
                success: function(response) {
                    console.log('Respuesta del servidor:', response);

                    if (response.trim() === 'success') {
                        Swal.fire(
                            'Eliminado con éxito',
                            'El cliente ha sido eliminado.',
                            'success'
                        ).then(() => {
                            location.reload(); // Recarga la página para actualizar la lista de clientes
                        });
                    } else if (response.trim() === 'constraint_error') {
                        Swal.fire(
                            'Error!',
                            'No se puede eliminar el cliente ya que tiene relaciones en el sistema.',
                            'error'
                        );
                    } else {
                        Swal.fire(
                            'Error!',
                            'Ocurrió un error al intentar eliminar el cliente: ' + response,
                            'error'
                        );
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire(
                        'Error!',
                        'Error de comunicación con el servidor. ' + error,
                        'error'
                    );
                }
            });
        }
    });
}
</script>

</body>
</html>
