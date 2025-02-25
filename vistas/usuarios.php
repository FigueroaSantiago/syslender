<?php
include '../includes/header.php';

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Usuarios</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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
        <h1>Listado de Usuarios</h1>
        <a href="usuario/agregar_usuarios.php" class="btn btn-custom btn-success mb-3">Agregar Usuario</a>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Contacto</th>
                    <th>Rol</th>
                    <th>Cédula</th>
                    <th>ultimo login</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include '../includes/functions.php';
                
                $usuarios = getAllUsuarios();
                foreach ($usuarios as $usuario) {
                    echo "<tr>
                        <td>{$usuario['nombre']} {$usuario['apellido']}</td>
                        <td>{$usuario['contacto']}</td>
                        <td>{$usuario['rol_name']}</td>
                        <td>{$usuario['cedula']}</td>
                        <td>{$usuario['ultimo_login']}</td>
                        <td class='action-buttons'>
                            <a href='usuario/editar_usuario.php?id={$usuario['id_user']}' class='btn btn-warning btn-custom btn-sm'><i class='fas fa-edit'></i> Editar</a>

                            <button onclick='cambiarEstadoUsuario({$usuario['id_user']}, \"{$usuario['estado']}\")' 
                                class='btn btn-" . ($usuario['estado'] == 'activo' ? 'danger' : 'success') . " btn-custom btn-sm'>
                                <i class='fas fa-power-off'></i> " . ($usuario['estado'] == 'activo' ? 'Desactivar' : 'Activar') . "
                            </button>

                            <a href='usuario/detalles.php?id={$usuario['id_user']}' class='btn btn-secondary btn-custom btn-sm'><i class='fas fa-info-circle'></i> Ver Detalles</a>
                        </td>

                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function cambiarEstadoUsuario(id_user, estado_actual) {
            Swal.fire({
                title: estado_actual === "activo" ? "¿Desactivar usuario?" : "¿Activar usuario?",
                text: estado_actual === "activo" ?
                    "El usuario será desactivado y no podrá iniciar sesión." :
                    "El usuario será activado nuevamente.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: estado_actual === "activo" ? "#d33" : "#28a745",
                cancelButtonColor: "#3085d6",
                confirmButtonText: estado_actual === "activo" ? "Sí, desactivar" : "Sí, activar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "usuario/activar_desactivar_usuario.php",
                        type: "POST",
                        data: {
                            id_user: id_user
                        },
                        success: function(response) {
                            response = response.trim();

                            if (response === "success") {
                                Swal.fire({
                                    title: "¡Éxito!",
                                    text: estado_actual === "activo" ? "Usuario desactivado con éxito." : "Usuario activado con éxito.",
                                    icon: "success"
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire("Error", "No se pudo actualizar el estado del usuario.", "error");
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire("Error", "Ocurrió un problema con la solicitud.", "error");
                        }
                    });
                }
            });
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        function confirmarEliminacion(id_user) {
            $.ajax({
                url: 'usuario/eliminar_usuario.php',
                type: 'POST',
                data: {
                    id_user: id_user
                },
                success: function(response) {
                    response = response.trim(); // Elimina caracteres extra como espacios o saltos de línea
                    console.log('Respuesta del servidor:', response);

                    if (response === 'success') {
                        Swal.fire(
                            'Eliminado',
                            'El usuario ha sido eliminado con éxito.',
                            'success'
                        ).then(() => {
                            location.reload(); // Recargar página tras éxito
                        });
                    } else if (response === 'admin_error') {
                        Swal.fire(
                            'Error',
                            'No se puede eliminar a un administrador.',
                            'error'
                        );
                    } else if (response === 'ruta_error') {
                        Swal.fire(
                            'Error',
                            'No se puede eliminar a un usuario con rutas asignadas.',
                            'error'
                        );
                    } else if (response === 'db_error') {
                        Swal.fire(
                            'Error',
                            'Hubo un problema al eliminar el usuario.',
                            'error'
                        );
                    } else {
                        Swal.fire(
                            'Error',
                            'Respuesta inesperada del servidor: ' + response,
                            'error'
                        );
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error del servidor:', status, error);
                    Swal.fire(
                        'Error',
                        'Hubo un problema de comunicación con el servidor.',
                        'error'
                    );
                }
            });
        }
    </script>

</body>

</html>