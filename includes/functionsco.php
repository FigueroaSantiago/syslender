<?php
// Conexión a la base de datos
include 'db.php';

/**
 * Obtener el total de clientes en la ruta asignada al cobrador.
 */
function getClientesEnRuta($conn, $id_rol_user) {
    try {
        // Consultar el ID de la ruta asociada al cobrador en rol_user
        $sqlRuta = 'SELECT id_ruta FROM ruta WHERE id_rol_user = ?';
        $stmtRuta = $conn->prepare($sqlRuta);
        $stmtRuta->bind_param('i', $id_rol_user);
        $stmtRuta->execute();
        $resultRuta = $stmtRuta->get_result();

        // Verificar si hay una ruta asociada
        $ruta = $resultRuta->fetch_assoc();
        if (!$ruta || !isset($ruta['id_ruta'])) {
            return 0; // Si no hay ruta asociada, retornar un valor predeterminado
        }

        $id_ruta = $ruta['id_ruta'];

        // Consultar los clientes de la ruta obtenida
        $sqlClientes = 'SELECT COUNT(*) AS total FROM cliente WHERE id_ruta = ?';
        $stmtClientes = $conn->prepare($sqlClientes);
        $stmtClientes->bind_param('i', $id_ruta);
        $stmtClientes->execute();
        $resultClientes = $stmtClientes->get_result();

        // Obtener el total de clientes
        $resultado = $resultClientes->fetch_assoc();
        return $resultado ? (int)$resultado['total'] : 0;

    } catch (mysqli_sql_exception $e) {
        // Manejo de errores
        error_log('Error al obtener clientes en la ruta: ' . $e->getMessage());
        return 0;
    }
}

/**
 * Obtener el total de préstamos activos de los clientes en la ruta del cobrador.
 */
function getPrestamosActivosCobrador($conn, $id_rol_user) {
    $sql = "SELECT COUNT(prestamo.id_prestamo) AS total
            FROM prestamo
            INNER JOIN cliente ON prestamo.id_cliente = cliente.id_cliente
            INNER JOIN ruta ON cliente.id_ruta = ruta.id_ruta
            WHERE ruta.id_rol_user = ? AND prestamo.estado = 'activo'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_rol_user);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    return $result['total'] ?? 0;
}

/**
 * Obtener el total de gastos diarios realizados por el cobrador.
 */
function getGastosPorCobrador($conn, $id_rol_user) {
    $sql = "SELECT SUM(gastos.monto) AS total
            FROM gastos
            WHERE gastos.id_rol_user = ? AND DATE(gastos.fecha) = CURDATE()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_rol_user);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    return $result['total'] ?? 0;
}

/**
 * Obtener los préstamos por estado relacionados con los clientes en la ruta del cobrador.
 */
function getLoansByStatusCobrador($conn, $id_rol_user) {
    $sql = "SELECT prestamo.estado, COUNT(prestamo.id_prestamo) AS total
            FROM prestamo
            INNER JOIN cliente ON prestamo.id_cliente = cliente.id_cliente
            INNER JOIN ruta ON cliente.id_ruta = ruta.id_ruta
            WHERE ruta.id_rol_user = ?
            GROUP BY prestamo.estado";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_rol_user);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[$row['estado']] = $row['total'];
    }

    return $data;
}

/**
 * Obtener el resumen de clientes, préstamos y gastos por cobrador.
 */
function getResumenPrestamosCobrador($conn, $id_rol_user) {
    $sql = "SELECT 
                cliente.nombres AS cliente, 
                prestamo.saldo_actual AS prestamo,  
                prestamo.inicio_fecha AS fecha
            FROM cliente
            INNER JOIN prestamo ON prestamo.id_cliente = cliente.id_cliente
            INNER JOIN ruta ON cliente.id_ruta = ruta.id_ruta
            LEFT JOIN gastos ON gastos.id_rol_user = ?
            WHERE ruta.id_rol_user = ?
            ORDER BY prestamo.inicio_fecha DESC 
            LIMIT 6";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_rol_user, $id_rol_user);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    return $data;
}
