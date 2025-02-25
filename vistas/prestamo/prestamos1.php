<?php
session_start();
include '../../includes/header2.php';

// Verifica si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'gucobro');

// Verificar si la conexión fue exitosa
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Recuperar filtros del formulario
$search = $_POST['search'] ?? '';
$filtro_estado = $_POST['filtro_estado'] ?? '';
$filtro_ciudad = $_POST['filtro_ciudad'] ?? '';
$filtro_cobrador = $_POST['filtro_cobrador'] ?? '';
$filtro_monto_minimo = $_POST['filtro_monto_minimo'] ?? '';
$filtro_monto_maximo = $_POST['filtro_monto_maximo'] ?? '';

// Construir el tipo de parámetros y las variables para la consulta
$params = [];
$typeString = ''; // Definir tipo de parámetros según los filtros

$search_term = "%$search%";
$params[] = $search_term;
$typeString .= 's'; // String para búsqueda

// Si hay filtro de estado, agregarlo
if ($filtro_estado != '') {
    $params[] = $filtro_estado;
    $typeString .= 's'; // String para estado
}

// Si hay filtro de ciudad, agregarlo
if ($filtro_ciudad != '') {
    $params[] = $filtro_ciudad;
    $typeString .= 's'; // String para ciudad
}

// Si hay filtro de cobrador, agregarlo
if ($filtro_cobrador != '') {
    $params[] = $filtro_cobrador;
    $typeString .= 'i'; // Integer para ID de cobrador
}

// Si hay filtros de monto mínimo y máximo, agregar
if ($filtro_monto_minimo != '' && $filtro_monto_maximo != '') {
    $params[] = $filtro_monto_minimo;
    $params[] = $filtro_monto_maximo;
    $typeString .= 'ii'; // Integer para montos mínimo y máximo
}

// Preparar la consulta SQL
$sql = "SELECT 
            cliente.id_cliente, 
            cliente.nombres AS nombre_cliente, 
            cliente.direccion_casa AS direccion, 
            cliente.telefono, 
            COUNT(prestamo.id_prestamo) AS prestamos_activos, 
            SUM(prestamo.saldo_actual) AS total_prestamos, 
            prestamo.estado, 
            rol_user.id_user AS id_cobrador,
            user.nombre AS nombre_cobrador
        FROM 
            cliente
        LEFT JOIN 
            prestamo ON cliente.id_cliente = prestamo.id_cliente
        LEFT JOIN 
            rol_user ON prestamo.id_rol_user = rol_user.id_rol_user
        LEFT JOIN 
            user ON rol_user.id_user = user.id_user
        WHERE 
            cliente.nombres LIKE ?";

// Agregar las condiciones de los filtros
if ($filtro_estado != '') {
    $sql .= " AND prestamo.estado = ?";
}

if ($filtro_ciudad != '') {
    $sql .= " AND cliente.direccion_casa LIKE ?";
}

if ($filtro_cobrador != '') {
    $sql .= " AND rol_user.id_user = ?";
}

if ($filtro_monto_minimo != '' && $filtro_monto_maximo != '') {
    $sql .= " AND SUM(prestamo.saldo_actual) BETWEEN ? AND ?";
}

$sql .= " GROUP BY cliente.id_cliente";

// Preparar y ejecutar la consulta con los parámetros dinámicos
$stmt = $conn->prepare($sql);

// Vincular los parámetros
$stmt->bind_param($typeString, ...$params);
$stmt->execute();
$result = $stmt->get_result();


// Recuperar todos los cobradores para el filtro
$cobradores_result = $conn->query("SELECT id_user, nombre FROM user");
$cobradores = $cobradores_result->fetch_all(MYSQLI_ASSOC);

// Recuperar todas las ciudades para el filtro
$ciudades_result = $conn->query("SELECT DISTINCT direccion_casa FROM cliente");
$ciudades = $ciudades_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes y Préstamos</title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Estilo general de la página */
        body {
            background-color: #e9ecef;
            margin-top: 50px;
        }
        .container {
            margin-top: 50px;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        header {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            margin: 0;
        }

        header a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        header a:hover {
            color: #0056b3;
        }

        header form {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        header input[type="text"], 
        header select, 
        header input[type="number"] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 200px;
            margin-bottom: 10px;
        }

        header button {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            margin-bottom: 10px;
        }

        header button:hover {
            background-color: #0056b3;
        }

        /* Estilo de la tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .action-icons a {
            margin: 0 5px;
            color: #007bff;
            text-decoration: none;
        }

        .action-icons a:hover {
            color: #0056b3;
        }

        footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #ddd;
            text-align: center;
        }

        /* Responsividad */
        @media (max-width: 768px) {
            header form {
                flex-direction: column;
            }

            header input[type="text"], 
            header select, 
            header input[type="number"] {
                width: 100%;
                margin-bottom: 10px;
            }

            header button {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <header>
        <h1><i class="fas fa-users"></i> Clientes y Préstamos</h1>
        <a href="dashboard.php" class="text-success"><i class="fas fa-tachometer-alt text-success"></i> Volver al Dashboard</a>
        <form method="POST" action="prestamos1.php">
            <input type="text" name="search" placeholder="Buscar cliente..." value="<?php echo htmlspecialchars($search); ?>">
            <select name="filtro_estado">
                <option value="">Estado del Préstamo</option>
                <option value="activo" <?php echo $filtro_estado == 'activo' ? 'selected' : ''; ?>>Activo</option>
                <option value="finalizado" <?php echo $filtro_estado == 'finalizado' ? 'selected' : ''; ?>>Finalizado</option>
                <option value="penalizado" <?php echo $filtro_estado == 'penalizado' ? 'selected' : ''; ?>>Penalizado</option>
            </select>
            <select name="filtro_ciudad">
                <option value="">Ciudad/Ruta</option>
                <?php foreach ($ciudades as $ciudad): ?>
                <option value="<?php echo htmlspecialchars($ciudad['direccion_casa']); ?>" <?php echo $filtro_ciudad == $ciudad['direccion_casa'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($ciudad['direccion_casa']); ?>
                </option>
                <?php endforeach; ?>
            </select>
            <select name="filtro_cobrador">
                <option value="">Cobrador</option>
                <?php foreach ($cobradores as $cobrador): ?>
                <option value="<?php echo htmlspecialchars($cobrador['id_user']); ?>" <?php echo $filtro_cobrador == $cobrador['id_user'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cobrador['nombre']); ?>
                </option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="filtro_monto_minimo" placeholder="Monto mínimo" value="<?php echo htmlspecialchars($filtro_monto_minimo); ?>">
            <input type="number" name="filtro_monto_maximo" placeholder="Monto máximo" value="<?php echo htmlspecialchars($filtro_monto_maximo); ?>">
            <button type="submit" class="btn-success ">Buscar</button>
        </form>
    </header>

    <table>
        <thead>
            <tr>
                <th>Nombre Cliente</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Estado del Préstamo</th>
                <th>Prestamos Activos</th>
                <th>Total Monto Préstamos</th>
                <th>Cobrador</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['nombre_cliente']); ?></td>
                <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                <td><?php echo htmlspecialchars($row['direccion']); ?></td>
                <td><?php echo htmlspecialchars($row['estado']); ?></td>
                <td><?php echo htmlspecialchars($row['prestamos_activos']); ?></td>
                <td><?php echo htmlspecialchars($row['total_prestamos']); ?></td>
                <td><?php echo htmlspecialchars($row['nombre_cobrador']); ?></td>
                <td class="action-icons">
                    <a href="cliente_detalle.php?id=<?php echo $row['id_cliente']; ?>"><i class="fas fa-eye"></i></a>
                    <a href="cliente_editar.php?id=<?php echo $row['id_cliente']; ?>"><i class="fas fa-edit"></i></a>
                    <a href="cliente_eliminar.php?id=<?php echo $row['id_cliente']; ?>"><i class="fas fa-trash-alt"></i></a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <footer>
        <p>&copy; 2024 - GuCobro | Todos los derechos reservados</p>
    </footer>
</div>
</body>
</html>

