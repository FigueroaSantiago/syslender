<?php
session_start();
include '../../includes/header2.php';

include '../../Conexion/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Verificar si se pasa el ID del usuario por GET
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']); // Sanitizar el ID del usuario
    $cuentas = getCuentasByAdmin($user_id);


    // Consultar información del usuario
    $usuario = getUsuarioById($user_id);

    // Verificar si el usuario existe
    if (!$usuario) {
        echo "<p class='alert alert-danger'>Usuario no encontrado.</p>";
        exit();
    }

    // Consultar roles y permisos
    $roles = getRolesByUsuario($user_id);
    $permisos = getPermisosByUsuario($user_id);

    // Consultar base, ruta y clientes si es cobrador
    $base = getBaseByCobrador($user_id);
    $ruta = getRutaByCobrador($user_id);
    $clientes = getClientesByRuta($ruta['id_ruta'] ?? null);
} else {
    header('Location: usuarios.php');
    exit();
}

// Función para obtener los datos del usuario por ID
function getUsuarioById($user_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM user WHERE id_user = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Función para obtener las cuentas asociadas a un administrador
function getCuentasByAdmin($user_id)
{
    global $conn;
    $stmt = $conn->prepare("
        SELECT c.id_cuenta, c.nombre, c.estado, c.fecha_vencimiento 
        FROM cuenta_admin ca
        INNER JOIN cuentas c ON ca.id_cuenta = c.id_cuenta
        WHERE ca.id_admin = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


// Función para obtener roles de un usuario
function getRolesByUsuario($user_id)
{
    global $conn;
    $stmt = $conn->prepare("
        SELECT r.rol 
        FROM rol_user ru
        JOIN rol r ON ru.id_rol = r.id_rol
        WHERE ru.id_user = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $roles = [];
    while ($row = $result->fetch_assoc()) {
        $roles[] = $row['rol'];
    }
    return $roles;
}

// Función para obtener permisos de un usuario
function getPermisosByUsuario($user_id)
{
    global $conn;

    // Paso 1: Obtener los roles del usuario desde la tabla rol_user
    $stmt = $conn->prepare("
        SELECT r.id_rol
        FROM rol_user ru
        JOIN rol r ON ru.id_rol = r.id_rol
        WHERE ru.id_user = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Paso 2: Obtener los permisos para los roles del usuario
    $permisos = [];
    while ($row = $result->fetch_assoc()) {
        $id_rol = $row['id_rol'];

        // Obtener los permisos asociados a cada rol
        $stmt_permisos = $conn->prepare("
            SELECT p.nombre
            FROM rol_permisos rp
            JOIN permisos p ON rp.id_permiso = p.id_permisos
            WHERE rp.id_rol = ?
        ");
        $stmt_permisos->bind_param("i", $id_rol);
        $stmt_permisos->execute();
        $result_permisos = $stmt_permisos->get_result();

        while ($permiso = $result_permisos->fetch_assoc()) {
            $permisos[] = $permiso['nombre'];
        }
    }

    return $permisos;
}

// Función para obtener la base asignada al cobrador
function getBaseByCobrador($user_id)
{
    global $conn;

    // Consulta SQL para obtener la base
    $stmt = $conn->prepare("
        SELECT b.base 
        FROM base b
        JOIN asignacion_base ab ON b.id_base = ab.id_base
        JOIN rol_user ru ON ab.id_cobrador = ru.id_rol_user
        WHERE ru.id_user = ?
    ");

    // Enlazar el parámetro de entrada (user_id) al statement
    $stmt->bind_param("i", $user_id);

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener el resultado y devolver la base si existe
    $result = $stmt->get_result();

    // Si se encontró una fila, devolver la base
    if ($row = $result->fetch_assoc()) {
        return $row['base'];
    } else {
        // Si no se encontró nada, devolver null o un valor predeterminado
        return null;
    }
}


// Función para obtener la ruta asignada al cobrador
function getRutaByCobrador($user_id)
{
    global $conn;
    $stmt = $conn->prepare("
        SELECT r.id_ruta, r.nombre_ruta 
        FROM ruta r
        JOIN rol_user ru ON r.id_rol_user = ru.id_rol_user
        WHERE ru.id_user = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Función para obtener los clientes de una ruta
function getClientesByRuta($ruta_id)
{
    if (!$ruta_id) return [];
    global $conn;
    $stmt = $conn->prepare("
        SELECT c.nombres, c.apellidos, c.cedula
        FROM cliente c
        WHERE c.id_ruta = ?
    ");
    $stmt->bind_param("i", $ruta_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $clientes = [];
    while ($row = $result->fetch_assoc()) {
        $clientes[] = $row;
    }
    return $clientes;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Usuario</title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
    <div class="container -5">
        <h1>Detalles del Usuario</h1>

        <!-- Información General del Usuario -->
        <div class="card">
            <div class="card-header">Información General</div>
            <div class="card-body">
                <p><strong>Nombre Completo:</strong> <?php echo htmlspecialchars($usuario['nombre'] . " " . $usuario['apellido']); ?></p>
                <p><strong>Cedula:</strong> <?php echo htmlspecialchars($usuario['cedula']); ?></p>
            </div>
        </div>

        <!-- Cuentas asociadas -->
        <?php if (!empty($cuentas)) { ?>
            <div class="card mt-4">
                <div class="card-header">Cuentas Asociadas</div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Estado</th>
                                <th>Fecha de Vencimiento</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cuentas as $cuenta) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($cuenta['nombre']); ?></td>
                                    <td>
                                        <span class="badge <?php echo ($cuenta['estado'] == 'activa') ? 'badge-success' : 'badge-danger'; ?>">
                                            <?php echo ucfirst($cuenta['estado']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($cuenta['fecha_vencimiento']); ?></td>
                                    <td>
                                        <button onclick="toggleEstadoCuenta(<?php echo $cuenta['id_cuenta']; ?>, '<?php echo $cuenta['estado']; ?>')"
                                            class="btn btn-<?php echo ($cuenta['estado'] == 'activa') ? 'danger' : 'success'; ?> btn-sm">
                                            <?php echo ($cuenta['estado'] == 'activa') ? 'Desactivar' : 'Activar'; ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>


        <!-- Roles y Permisos -->
        <div class="card mt-4">
            <div class="card-header">Roles y Permisos</div>
            <div class="card-body">
                <p><strong>Roles:</strong> <?php echo htmlspecialchars(implode(", ", $roles)); ?></p>
                <p><strong>Permisos:</strong> <?php echo htmlspecialchars(implode(", ", $permisos)); ?></p>
            </div>
        </div>

        <!-- Información de Cobrador -->
        <?php if ($base && $ruta) { ?>
            <div class="card mt-4">
                <div class="card-header">Información de Cobrador</div>
                <div class="card-body">
                    <?php if ($base && is_array($base)) { ?>
                        <p><strong>Base:</strong> <?php echo htmlspecialchars($base['base']); ?></p>
                    <?php } ?>

                    <?php if ($ruta && is_array($ruta)) { ?>
                        <p><strong>Ruta:</strong> <?php echo htmlspecialchars($ruta['nombre_ruta']); ?></p>
                    <?php } ?>

                    <h5>Clientes de la Ruta:</h5>
                    <ul>
                        <?php foreach ($clientes as $cliente) { ?>
                            <li><?php echo htmlspecialchars($cliente['nombres'] . " " . $cliente['apellidos'] . " - " . $cliente['cedula']); ?></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        <?php } ?>
    </div>

    <script>
        function toggleEstadoCuenta(idCuenta, estadoActual) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Vas a " + (estadoActual === 'activa' ? 'desactivar' : 'activar') + " esta cuenta.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('toggle_cuenta.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams({
                                id_cuenta: idCuenta
                            })
                        })
                        .then(response => response.text())
                        .then(response => {
                            console.log("Respuesta del servidor:", response); // Log para depuración
                            if (response.trim() === 'success') {
                                Swal.fire(
                                    '¡Hecho!',
                                    'El estado de la cuenta ha sido actualizado.',
                                    'success'
                                ).then(() => {
                                    location.reload(); // Recargar la página
                                });
                            } else {
                                Swal.fire(
                                    'Error',
                                    'No se pudo actualizar el estado de la cuenta. Servidor respondió: ' + response,
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error('Error en la solicitud:', error);
                            Swal.fire(
                                'Error',
                                'Error en la comunicación con el servidor.',
                                'error'
                            );
                        });
                }
            });
        }
    </script>



    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>