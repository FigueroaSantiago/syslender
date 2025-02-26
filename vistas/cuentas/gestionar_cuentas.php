<?php
session_start();
include '../../Conexion/conexion.php';
include '../../includes/header2.php';

// Verificar si el usuario es Gestor (rol 18)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 18) {
    die("Acceso denegado.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['crear_cuenta'])) {
    $cod_cuenta = $_POST['cod_cuenta'];
    $nombre_cuenta = $_POST['nombre_cuenta'];
    $empresa = $_POST['empresa'];
    $cod_sirc = $_POST['cod_sirc'];
    $fecha_vencimiento = $_POST['fecha_vencimiento'];
    $id_admin = $_POST['id_admin']; // Administrador seleccionado
    $estado = "activa"; // Estado por defecto

    try {
        $conn->begin_transaction();

        // Insertar la nueva cuenta
        $sql = "INSERT INTO cuentas (cod_cuenta, nombre, empresa, cod_sirc, fecha_creacion, fecha_vencimiento, estado) 
                VALUES (?, ?, ?, ?, CURDATE(), ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $cod_cuenta, $nombre_cuenta, $empresa, $cod_sirc, $fecha_vencimiento, $estado);
        $stmt->execute();
        $id_cuenta = $conn->insert_id;

        // Asociar la cuenta al administrador en cuenta_admin
        $sql = "INSERT INTO cuenta_admin (id_admin, id_cuenta) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id_admin, $id_cuenta);
        $stmt->execute();

        $conn->commit();
        $_SESSION['response'] = ['status' => 'success', 'message' => 'Cuenta creada y asociada correctamente.'];

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['response'] = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
    }

    header("Location: gestionar_cuentas.php");
    exit();
}



// 🔹 Procesar creación de nueva cuenta
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['crear_cuenta'])) {
    $cod_cuenta = $_POST['cod_cuenta'];
    $nombre_cuenta = $_POST['nombre_cuenta'];
    $empresa = $_POST['empresa'];
    $cod_sirc = $_POST['cod_sirc'];
    $fecha_vencimiento = $_POST['fecha_vencimiento'];
    $estado = "activa"; // Estado por defecto

    $sql = "INSERT INTO cuentas (cod_cuenta, nombre, empresa, cod_sirc, fecha_creacion, fecha_vencimiento, estado) 
            VALUES (?, ?, ?, ?, CURDATE(), ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $cod_cuenta, $nombre_cuenta, $empresa, $cod_sirc, $fecha_vencimiento, $estado);

    if ($stmt->execute()) {
        $_SESSION['response'] = ['status' => 'success', 'message' => 'Cuenta creada correctamente.'];
    } else {
        $_SESSION['response'] = ['status' => 'error', 'message' => 'Error al crear la cuenta.'];
    }
    header("Location: gestionar_cuentas.php");
    exit();
}

// 🔹 Obtener la lista de cuentas
$sql = "SELECT id_cuenta, nombre, fecha_vencimiento, estado FROM cuentas";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Cuentas</title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
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
        .select2-container {
    z-index: 9999 !important;
}

    </style>
        <!-- 🔹 Cargar Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
</head>

<body>
    <div class="container">
      
            <h2 class="mb-4">Gestión de Cuentas</h2>

            <!-- 🔹 Botón para abrir el modal de creación -->
            <button class="btn btn-success mb-3" data-toggle="modal" data-target="#crearCuentaModal">Crear Nueva Cuenta</button>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Fecha Vencimiento</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?= $row['id_cuenta'] ?></td>
                            <td><?= htmlspecialchars($row['nombre']) ?></td>
                            <td><?= $row['fecha_vencimiento'] ?></td>
                            <td><?= ucfirst($row['estado']) ?></td>
                            <td class='action-buttons'>
                                <form method="POST" action="gestionar_cuentas.php">
                                    <input type="hidden" name="id_cuenta" value="<?= $row['id_cuenta'] ?>">
                                    <input type="date" name="fecha_vencimiento" value="<?= $row['fecha_vencimiento'] ?>" required>
                                    <button type="submit" class="btn btn-primary btn-sm">Actualizar</button>
                                </form>
                                <button onclick='cambiarEstadoCuenta(<?= $row["id_cuenta"] ?>, "<?= $row["estado"] ?>")' 
                                    class='btn btn-<?= $row["estado"] == "activa" ? "danger" : "success" ?> btn-custom btn-sm'>
                                    <i class='fas fa-power-off'></i> <?= $row["estado"] == "activa" ? "Desactivar" : "Activar" ?>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
  
    </div>

 <!-- 🔹 Modal para crear una nueva cuenta -->
<div class="modal fade" id="crearCuentaModal" tabindex="-1" role="dialog" aria-labelledby="crearCuentaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="crearCuentaModalLabel">Crear Nueva Cuenta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="gestionar_cuentas.php">
                    <input type="hidden" name="crear_cuenta" value="1">

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
                        <label>Seleccionar Administrador:</label>
                        <select name="id_admin" id="admin_select" class="form-control" required></select>
                    </div>

                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function cambiarEstadoCuenta(id_cuenta, estado_actual) {
        Swal.fire({
            title: estado_actual === "activa" ? "¿Desactivar cuenta?" : "¿Activar cuenta?",
            text: estado_actual === "activa" 
                ? "La cuenta quedará inactiva y no podrá ser utilizada." 
                : "La cuenta será activada nuevamente.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: estado_actual === "activa" ? "#d33" : "#28a745",
            cancelButtonColor: "#3085d6",
            confirmButtonText: estado_actual === "activa" ? "Sí, desactivar" : "Sí, activar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "activar_desactivar_cuenta.php",
                    type: "POST",
                    data: { id_cuenta: id_cuenta },
                    success: function(response) {
                        response = response.trim();

                        if (response === "success") {
                            Swal.fire({
                                title: "¡Éxito!",
                                text: estado_actual === "activa" ? "Cuenta desactivada con éxito." : "Cuenta activada con éxito.",
                                icon: "success"
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire("Error", "No se pudo actualizar el estado de la cuenta.", "error");
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

<script>
$(document).ready(function() {
    console.log("✅ Select2 cargado correctamente");

    $('#admin_select').select2({
        placeholder: "Buscar Administrador...",
        allowClear: true,
        width: '100%',
        dropdownParent: $("#crearCuentaModal"),  // 🔹 Forzar Select2 dentro del modal
        ajax: {
            url: "buscar_admins.php",
            type: "POST",
            dataType: "json",
            delay: 250,
            data: function(params) {
                console.log("🔹 Enviando búsqueda:", params.term);
                return { searchTerm: params.term };
            },
            processResults: function(data) {
                console.log("🔹 Datos obtenidos:", data);
                return { results: data };
            },
            cache: true
        },
        minimumInputLength: 1  // 🔹 Cambiar a 1 para buscar con un solo carácter
    });

    // 🔹 Asegurar que se reinicia Select2 al abrir el modal
    $('#crearCuentaModal').on('shown.bs.modal', function () {
        $('#admin_select').select2('open');
    });
});

</script>

    <!-- 🔹 Alertas -->
    <script>
        <?php if (isset($_SESSION['response'])) : ?>
            Swal.fire({
                icon: '<?php echo $_SESSION['response']['status']; ?>',
                title: '<?php echo ucfirst($_SESSION['response']['status']); ?>',
                text: '<?php echo $_SESSION['response']['message']; ?>'
            });
            <?php unset($_SESSION['response']); ?>
        <?php endif; ?>
    </script>

    <!-- Scripts de Bootstrap -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
