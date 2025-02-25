<?php
session_start();
include '../includes/header.php';
include '../includes/functions.php';

// Redirige si el usuario no está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$roles = getAllRoles();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Roles</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f4f6f9;
        }
        .role-card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin-bottom: 20px;
            transition: all 0.3s ease-in-out;
        }
        .role-card:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .role-header {
            background-color: #343a40;
            color: white;
            padding: 10px;
            border-radius: 10px 10px 0 0;
        }
        .role-actions a {
            margin-right: 10px;
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
        <h1 class="mb-4 text-center">Gestión de Roles</h1>

            <a href="rol/agregar_rol.php" class="btn btn-custom btn-success mb-3">Agregar Rol</a>


        <div class="row">
            <?php foreach ($roles as $rol): 
                // Obtener permisos para cada rol
                $permisos = getPermissionsByRoleId($rol['id_rol']); 
            ?>
            <div class="col-md-4">
                <div class="role-card card">
                    <div class="role-header text-center">
                        <h5><?php echo $rol['rol']; ?></h5>
                    </div>
                    <div class="card-body text-center">
                        <p>Permisos:</p>
                        <ul>
                            <?php foreach ($permisos as $permiso): ?>
                            <li><?php echo $permiso['nombre']; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="role-actions">
                            <a href="rol/asignarpermisos.php?id_rol=<?php echo $rol['id_rol']; ?>" class="btn btn-primary">Permisos</a>    
                            <a href="rol/editar_rol.php?id_rol=<?php echo $rol['id_rol']; ?>" class="btn btn-warning"><i class="fas fa-edit"></i> Editar</a>
                            <a href="javascript:void(0);" onclick="confirmarEliminacion(<?php echo $rol['id_rol']; ?>);" class="btn btn-danger btn-custom btn-sm">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
    function confirmarEliminacion(id_rol) {
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
                    url: 'rol/eliminar_rol.php', // Asegúrate de que esta URL es correcta
                    type: 'POST',
                    data: { id_rol: id_rol },
                    success: function(response) {
                        if (response === 'success') {
                            Swal.fire(
                                'Eliminado!',
                                'El rol ha sido eliminado.',
                                'success'
                            ).then(() => {
                                location.reload(); // Recarga la página para actualizar la lista de roles
                            });
                        } else if (response === 'constraint_error') {
                            Swal.fire(
                                'Error!',
                                'No se puede eliminar el rol ya que tiene relaciones en el sistema.',
                                'error'
                            );
                        } else {
                            Swal.fire(
                                'Error!',
                                'Ocurrió un error al intentar eliminar el rol.',
                                'error'
                            );
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown); // Para depuración
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
