<?php
include '../../includes/functions.php';
header('Content-Type: application/json');

$response = ['existe' => false, 'error' => null];

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $id_cobrador = $data['id_cobrador'] ?? null;
    $fecha = $data['fecha'] ?? null;

    if (!$id_cobrador || !$fecha) {
        throw new Exception('Los datos enviados son incompletos.');
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM asignacion_base WHERE id_cobrador = ? AND fecha_asignacion = ?");
    $stmt->execute([$id_cobrador, $fecha]);
    $response['existe'] = $stmt->fetchColumn() > 0;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
} catch (PDOException $e) {
    $response['error'] = 'Error en la base de datos: ' . $e->getMessage();
}

echo json_encode($response);
