<?php
session_start();
include '../includes/header2.php';


// Redirige si el usuario no está autenticado
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

// Recuperar parámetros de búsqueda y filtros desde el formulario
$search = $_POST['search'] ?? '';
$filtro_estado = $_POST['filtro_estado'] ?? '';
$filtro_ciudad = $_POST['filtro_ciudad'] ?? '';
$filtro_cobrador = $_POST['filtro_cobrador'] ?? '';

// Construcción de la consulta SQL con filtros
$sql = "SELECT 
            cliente.id_cliente, 
            cliente.nombres AS nombre_cliente, 
            cliente.direccion_casa AS direccion, 
            cliente.telefono, 
            COUNT(prestamo.id_prestamo) AS prestamos_activos, 
            SUM(prestamo.principal_prestamo) AS total_prestamos, 
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
            cliente.nombres LIKE ? 
            AND (prestamo.estado = ? OR ? = '')
            AND (cliente.direccion_casa = ? OR ? = '')
            AND (rol_user.id_user = ? OR ? = '')
        GROUP BY 
            cliente.id_cliente";

// Preparar y ejecutar la consulta SQL
$stmt = $conn->prepare($sql);
$search_term = "%$search%";
$stmt->bind_param("sssissi", $search_term, $filtro_estado, $filtro_estado, $filtro_ciudad, $filtro_ciudad, $filtro_cobrador, $filtro_cobrador);
$stmt->execute();
$result = $stmt->get_result();

// Recuperar datos para filtros de cobrador y ciudad
$cobradores_result = $conn->query("SELECT id_user, nombre FROM user");
$cobradores = $cobradores_result->fetch_all(MYSQLI_ASSOC);

$ciudades_result = $conn->query("SELECT DISTINCT direccion_casa FROM cliente");
$ciudades = $ciudades_result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes y Préstamos - Administrador</title>
    <!-- Vinculación de Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
        }
        header form {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        header input[type="text"], header select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        header button {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }
        header button:hover {
            background-color: #0056b3;
        }
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
    </style>
</head>
<body>
<br><br><br>
<div class="container">
    <header>
        <h1><i class="fas fa-users"></i> Clientes y Préstamos</h1>
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Volver al Dashboard</a>
        <form method="POST" action="clientes_prestamos.php">
            <input type="text" name="search" placeholder="Buscar cliente..." value="<?php echo htmlspecialchars($search); ?>">
            <select name="filtro_estado">
                <option value="">Estado del Préstamo</option>
                <option value="activo" <?php echo $filtro_estado == 'activo' ? 'selected' : ''; ?>>Activo</option>
                <option value="finalizado" <?php echo $filtro_estado == 'finalizado' ? 'selected' : ''; ?>>Finalizado</option>
                <option value="penalizado" <?php echo $filtro_estado == 'penalizado' ? 'selected' : ''; ?>>Penalizado</option>
            </select>
            <select name="filtro_ciudad">
                <option value="">Ciudad</option>
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
            <button type="submit"><i class="fas fa-filter"></i> Filtrar</button>
        </form>
    </header>

    <table>
        <thead>
            <tr>
                <th>ID Cliente</th>
                <th>Nombre</th>
                <th>Dirección</th>
                <th>Teléfono</th>
                <th>Préstamos Activos</th>
                <th>Total Préstamos</th>
                <th>Estado Préstamo</th>
                <th>Cobrador</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id_cliente']); ?></td>
                <td><?php echo htmlspecialchars($row['nombre_cliente']); ?></td>
                <td><?php echo htmlspecialchars($row['direccion']); ?></td>
                <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                <td><?php echo htmlspecialchars($row['prestamos_activos']); ?></td>
                <td><?php echo htmlspecialchars($row['total_prestamos']); ?></td>
                <td><?php echo htmlspecialchars($row['estado']); ?></td>
                <td><?php echo htmlspecialchars($row['nombre_cobrador']); ?></td>
                <td class="action-icons">
                    <a href="#"><i class="fas fa-eye"></i></a>
                    <a href="#"><i class="fas fa-edit"></i></a>
                    <a href="#"><i class="fas fa-trash-alt"></i></a>
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
