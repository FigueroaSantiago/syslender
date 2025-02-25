<?php
session_start();
include '../../includes/db.php';

// Verifica si la sesión está iniciada y el rol es correcto
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 2) {
    die('Acceso denegado.');
}

// Validar datos enviados desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_rol_user = $_SESSION['rol_user_id'];
    $fecha = date('Y-m-d');
    
    // Validar y sanitizar datos
    $saldo_final = filter_var($_POST['saldo_final'], FILTER_VALIDATE_FLOAT);
    $observaciones = htmlspecialchars(trim($_POST['observaciones']));
    $base_inicial = filter_var($_POST['base_inicial'], FILTER_VALIDATE_FLOAT);
    $total_pagos = filter_var($_POST['total_pagos'], FILTER_VALIDATE_FLOAT);
    $total_gastos = filter_var($_POST['total_gastos'], FILTER_VALIDATE_FLOAT);
    $total_prestamos = filter_var($_POST['total_prestamos'], FILTER_VALIDATE_FLOAT);

    $estado = 'pendiente'; // Estado inicial del cuadre

    // Validar que no haya datos faltantes
    if ($saldo_final === false || $base_inicial === false || $total_pagos === false || 
        $total_gastos === false || $total_prestamos === false  ) {
        die('Datos inválidos o incompletos.');
    }

    try {
        // Insertar en la tabla cuadres_diarios
        $stmt = $pdo->prepare("
            INSERT INTO cuadres_diarios (
                id_cobrador, fecha, base_inicial, total_pagos, total_gastos, 
                total_prestamos, saldo_final, estado, observaciones
            ) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ? )
        ");
        $resultado = $stmt->execute([
            $id_rol_user, $fecha, $base_inicial, $total_pagos, $total_gastos, 
            $total_prestamos, $saldo_final, $estado, $observaciones
        ]);

        if ($resultado) {
            // Redirigir al usuario con un mensaje de éxito
            header("Location: cuadre_diario.php");
            exit;
        } else {
            throw new Exception('Error al guardar el cuadre diario.');
        }
    } catch (Exception $e) {
        die('Error: ' . $e->getMessage());
    }
} else {
    die('Método no permitido.');
}
