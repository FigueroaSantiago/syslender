<?php
include '../includes/db.php';

$id_prestamo = isset($_GET['id_prestamo']) ? (int)$_GET['id_prestamo'] : 0;

if ($id_prestamo > 0) {
    $query_cuotas = "SELECT id_cuota, numero_cuota, fecha_cuota, valor_cuota, estado
                     FROM cuota_prestamo WHERE id_prestamo = ? ORDER BY numero_cuota";
    $stmt_cuotas = $conn->prepare($query_cuotas);
    $stmt_cuotas->bind_param("i", $id_prestamo);
    $stmt_cuotas->execute();
    $cuotas = $stmt_cuotas->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_cuotas->close();
    echo json_encode($cuotas);
} else {
    echo json_encode([]);
}
