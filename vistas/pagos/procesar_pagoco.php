<?php
require_once "../../Conexion/conexion.php";

// Aquí configuras la respuesta predeterminada
$response = ["status" => "error", "message" => "Error desconocido"];    

try {
    // Validar los datos de entrada
    $id_cuota = isset($_POST['id_cuota']) ? intval($_POST['id_cuota']) : null;
    $id_prestamo = isset($_POST['id_prestamo']) ? intval($_POST['id_prestamo']) : null;
    $estado_pago = isset($_POST['estado_pago']) ? $_POST['estado_pago'] : null;
    $abono = isset($_POST['abono']) ? floatval($_POST['abono']) : 0;
    $observacion = isset($_POST['observacion']) ? trim($_POST['observacion']) : "";

    // Verificar que todos los datos necesarios se han recibido
    if (!$id_cuota || !$id_prestamo || !$estado_pago) {
        $response['message'] = "Datos incompletos enviados al servidor.";
        echo json_encode($response);
        exit;
    }

    // Iniciar una transacción
    $conn->begin_transaction();

    // Obtener detalles de la cuota
    $sql_cuota = "SELECT valor_cuota, estado, fecha_cuota, saldo_pendiente FROM cuota_prestamo WHERE id_cuota = ? AND id_prestamo = ?";
    $stmt_cuota = $conn->prepare($sql_cuota);
    $stmt_cuota->bind_param("ii", $id_cuota, $id_prestamo);
    $stmt_cuota->execute();
    $resultado_cuota = $stmt_cuota->get_result();

    // Verificar si se encontró la cuota    
    if ($resultado_cuota->num_rows === 0) {
        throw new Exception("No se encontró la cuota número $id_cuota para el préstamo $id_prestamo.");
    }

    $cuota = $resultado_cuota->fetch_assoc();
    $monto_cuota = floatval($cuota['valor_cuota']);
    $estado_cuota = $cuota['estado'];
    $saldo_cuota = floatval($cuota['valor_cuota']);
    $fecha_cuota = $cuota['fecha_cuota'];

    // Validar la fecha de cobro
    $fecha_actual = new DateTime();
    $fecha_cuota_obj = new DateTime($fecha_cuota);
    $intervalo_permitido = (int)$fecha_actual->diff($fecha_cuota_obj)->format("%r%a");
    
  //  if ($intervalo_permitido < -1 || $intervalo_permitido > 1) {
    //    throw new Exception("El pago solo puede realizarse un día antes, el día exacto o un día después de la fecha de la cuota.");
   // }
    

    // Verificar si la cuota ya ha sido pagada o abonada
    if ($estado_cuota === "pago" || $estado_cuota === "no pago" || $estado_cuota === "abono") {
        throw new Exception("La cuota ya tiene un estado de $estado_cuota y no se puede volver a registrar.");
    }
    
    // Lógica para determinar el monto a registrar y cómo ajustar las cuotas futuras
    if ($estado_pago === "pago") {
        $monto = $monto_cuota;
        $sql_update_cuota = "UPDATE cuota_prestamo SET estado = 'pago', valor_cuota = 0 WHERE id_cuota = ?";
        $stmt_update_cuota = $conn->prepare($sql_update_cuota);
        $stmt_update_cuota->bind_param("i", $id_cuota);
        $stmt_update_cuota->execute(); // PAGO COMPLETO
    }  elseif ($estado_pago === "abono") {
        if ($abono <= 0) {
            throw new Exception("El monto del abono debe ser mayor a cero.");
        }
        $monto = $abono; // ABONO PARCIAL


        if ($monto < $saldo_cuota) {
            // Si el abono es menor que el saldo pendiente, calcular el saldo restante y ajustarlo a la siguiente cuota
            $saldo_restante = $saldo_cuota - $monto;

// Obtener la siguiente cuota
$sql_siguiente_cuota = "
SELECT id_cuota, valor_cuota
FROM cuota_prestamo
WHERE id_prestamo = ? 
  AND estado NOT IN ('pago', 'abono', 'no pago')  -- Excluimos las cuotas pagadas, abonadas y no pagadas
  AND id_cuota > ?  -- Aquí deberías pasar el ID de la última cuota procesada
  AND fecha_cuota > CURRENT_DATE  -- Aseguramos que la fecha sea posterior a hoy
ORDER BY fecha_cuota ASC
LIMIT 1;
";
  $stmt_siguiente_cuota = $conn->prepare($sql_siguiente_cuota);
            $stmt_siguiente_cuota->bind_param("ii", $id_prestamo, $id_cuota);
            $stmt_siguiente_cuota->execute();
            $resultado_siguiente_cuota = $stmt_siguiente_cuota->get_result();

            if ($resultado_siguiente_cuota->num_rows > 0) {
                $siguiente_cuota = $resultado_siguiente_cuota->fetch_assoc();
                $id_siguiente_cuota = $siguiente_cuota['id_cuota'];
                $valor_siguiente_cuota = floatval($siguiente_cuota['valor_cuota']);

                // Actualizar el valor de la siguiente cuota sumando el saldo restante
                $nuevo_valor_siguiente_cuota = $valor_siguiente_cuota + $saldo_restante;
                $sql_update_siguiente_cuota = "UPDATE cuota_prestamo SET valor_cuota = ? WHERE id_cuota = ?";
                $stmt_update_siguiente_cuota = $conn->prepare($sql_update_siguiente_cuota);
                $stmt_update_siguiente_cuota->bind_param("di", $nuevo_valor_siguiente_cuota, $id_siguiente_cuota);
                $stmt_update_siguiente_cuota->execute();
            }
             // Actualizar el saldo de la cuota actual
            $sql_update_cuota = "UPDATE cuota_prestamo SET estado = 'abono', saldo_pendiente = ? WHERE id_cuota = ?";
            $stmt_update_cuota = $conn->prepare($sql_update_cuota);
            $stmt_update_cuota->bind_param("di", $saldo_restante, $id_cuota);
            $stmt_update_cuota->execute();
        } elseif ($monto >= $saldo_cuota) {
            // Si el abono es mayor o igual al saldo pendiente, marcar la cuota como pagada y manejar el excedente
            $excedente = $monto - $saldo_cuota;

            $sql_update_cuota = "UPDATE cuota_prestamo SET estado = 'pago', valor_cuota = 0 WHERE id_cuota = ?";
            $stmt_update_cuota = $conn->prepare($sql_update_cuota);
            $stmt_update_cuota->bind_param("i", $id_cuota);
            $stmt_update_cuota->execute();

            // Si hay excedente, aplicarlo a las siguientes cuotas
            while ($excedente > 0) {
                $sql_siguiente_cuota = "SELECT id_cuota, valor_cuota FROM cuota_prestamo WHERE id_prestamo = ? AND estado NOT IN ('pago', 'abono', 'no pago')  ORDER BY fecha_cuota ASC LIMIT 1";
                $stmt_siguiente_cuota = $conn->prepare($sql_siguiente_cuota);
                $stmt_siguiente_cuota->bind_param("i", $id_prestamo);
                $stmt_siguiente_cuota->execute();
                $resultado_siguiente_cuota = $stmt_siguiente_cuota->get_result();

                if ($resultado_siguiente_cuota->num_rows === 0) {
                    break; // No hay más cuotas pendientes
                }

                $siguiente_cuota = $resultado_siguiente_cuota->fetch_assoc();
                $id_siguiente_cuota = $siguiente_cuota['id_cuota'];
                $valor_siguiente_cuota = floatval($siguiente_cuota['valor_cuota']);

                if ($excedente >= $valor_siguiente_cuota) {
                    // El excedente cubre completamente la siguiente cuota
                    $excedente -= $valor_siguiente_cuota;
                    $sql_update_siguiente_cuota = "UPDATE cuota_prestamo SET estado = 'pago', valor_cuota = 0 WHERE id_cuota = ?";
                    $stmt_update_siguiente_cuota = $conn->prepare($sql_update_siguiente_cuota);
                    $stmt_update_siguiente_cuota->bind_param("i", $id_siguiente_cuota);
                    $stmt_update_siguiente_cuota->execute();
                } else {
                    // El excedente solo cubre parcialmente la siguiente cuota
                    $nuevo_valor_siguiente_cuota = $valor_siguiente_cuota - $excedente;
                    $sql_update_siguiente_cuota = "UPDATE cuota_prestamo SET valor_cuota = ? WHERE id_cuota = ?";
                    $stmt_update_siguiente_cuota = $conn->prepare($sql_update_siguiente_cuota);
                    $stmt_update_siguiente_cuota->bind_param("di", $nuevo_valor_siguiente_cuota, $id_siguiente_cuota);
                    $stmt_update_siguiente_cuota->execute();
                    $excedente = 0;
                }
            }
        }
    }  elseif ($estado_pago === "no_pago") {
        // Marcar cuota como no pagada
        $sql_update_cuota = "UPDATE cuota_prestamo SET estado = 'no pago' WHERE id_cuota = ?";
        $stmt_update_cuota = $conn->prepare($sql_update_cuota);
        $stmt_update_cuota->bind_param("i", $id_cuota);
        $stmt_update_cuota->execute();
    
        // Ajustar la siguiente cuota
        $sql_siguiente_cuota = "SELECT id_cuota, valor_cuota FROM cuota_prestamo WHERE id_prestamo = ? AND estado != 'pago' AND id_cuota > ? ORDER BY fecha_cuota ASC LIMIT 1";
        $stmt_siguiente_cuota = $conn->prepare($sql_siguiente_cuota);
        $stmt_siguiente_cuota->bind_param("ii", $id_prestamo, $id_cuota);
        $stmt_siguiente_cuota->execute();
        $resultado_siguiente_cuota = $stmt_siguiente_cuota->get_result();
    
        if ($resultado_siguiente_cuota->num_rows > 0) {
            $siguiente_cuota = $resultado_siguiente_cuota->fetch_assoc();
            $id_siguiente_cuota = $siguiente_cuota['id_cuota'];
            $valor_siguiente_cuota = floatval($siguiente_cuota['valor_cuota']);
    
            $nuevo_valor_cuota = $valor_siguiente_cuota + $monto_cuota;
            $sql_update_siguiente_cuota = "UPDATE cuota_prestamo SET valor_cuota = ? WHERE id_cuota = ?";
            $stmt_update_siguiente_cuota = $conn->prepare($sql_update_siguiente_cuota);
            $stmt_update_siguiente_cuota->bind_param("di", $nuevo_valor_cuota, $id_siguiente_cuota);
            $stmt_update_siguiente_cuota->execute();
        }
    
        // Registrar alerta de "no pago"
        $mensaje_alerta = "Cuota no pagada: $id_cuota del préstamo $id_prestamo.";
        $sql_alerta = "INSERT INTO alertas (id_prestamo, mensaje, fecha) VALUES (?, ?, ?)";
        $stmt_alerta = $conn->prepare($sql_alerta);
        $fecha_actual_str = $fecha_actual->format('Y-m-d H:i:s'); // Convertir a formato de fecha y hora
        $stmt_alerta->bind_param("iss", $id_prestamo, $mensaje_alerta, $fecha_actual_str);
        $stmt_alerta->execute();
    
        // Evitar modificar el saldo total
        $monto = 0; // Forzar monto a 0 para evitar actualizaciones incorrectas
    }
    
    else {
        throw new Exception("Estado de pago no reconocido.");
    }

    // Registrar el pago o abono
    $sql_pago = "INSERT INTO pagos (id_cuotas, id_prestamo, monto, tipo_pago, observacion, fecha) 
                 VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt_pago = $conn->prepare($sql_pago);
    $stmt_pago->bind_param("iisss", $id_cuota, $id_prestamo, $monto, $estado_pago, $observacion);
    $stmt_pago->execute();

    // Actualizar el saldo total del préstamo
    $sql_prestamo = "UPDATE prestamo SET saldo_actual = saldo_actual - ? WHERE id_prestamo = ?";
    $stmt_prestamo = $conn->prepare($sql_prestamo);
    $stmt_prestamo->bind_param("di", $monto, $id_prestamo);
    $stmt_prestamo->execute();


    //desde aqui empieza lo de base 
    $sql_cobrador = "
        SELECT r.id_rol_user 
        FROM prestamo p
        INNER JOIN cliente c ON p.id_cliente = c.id_cliente
        INNER JOIN ruta r ON c.id_ruta = r.id_ruta
        WHERE p.id_prestamo = ?";
    $stmt_cobrador = $conn->prepare($sql_cobrador);
    $stmt_cobrador->bind_param("i", $id_prestamo);
    $stmt_cobrador->execute();
    $resultado_cobrador = $stmt_cobrador->get_result();

    if ($resultado_cobrador->num_rows === 0) {
        throw new Exception("No se pudo determinar el cobrador asociado al préstamo $id_prestamo.");
    }

    $cobrador = $resultado_cobrador->fetch_assoc();
    $id_cobrador = intval($cobrador['id_rol_user']);

// Obtener el id_base asociado al cobrador (id_rol_user)
$sql_obtener_id_base = "SELECT id_base FROM asignacion_base WHERE id_cobrador = ?";
$stmt_obtener_id_base = $conn->prepare($sql_obtener_id_base);
$stmt_obtener_id_base->bind_param("i", $id_cobrador);
$stmt_obtener_id_base->execute();
$stmt_obtener_id_base->bind_result($id_base);
$stmt_obtener_id_base->fetch();
$stmt_obtener_id_base->close();

$stmt = $conn->prepare("SELECT id_asignacion FROM asignacion_base WHERE id_cobrador = ?");
$stmt->bind_param('i', $id_cobrador);
$stmt->execute();
$stmt->bind_result($id_asignacion_base);
$stmt->fetch();
$stmt->close();

// Actualizar el monto en la tabla base
$sql_actualizar_base = "UPDATE base SET base = base + ?, fecha = NOW() WHERE id_base = ?";
$stmt_actualizar_base = $conn->prepare($sql_actualizar_base);
$stmt_actualizar_base->bind_param("di", $monto, $id_base);
$stmt_actualizar_base->execute();

if (!$id_base) {
    throw new Exception("No se pudo obtener un id_base válido para el cobrador.");
}

// Registrar el movimiento en la tabla movimientos_base
$sql_historial_base = "
    INSERT INTO movimientos_base (id_asignacion_base, tipo_movimiento, monto, descripcion, fecha)
    VALUES (?, 'pago', ?, 'Pago de cuota o abono (ID Cuota: $id_cuota)', NOW())";
$stmt_historial_base = $conn->prepare($sql_historial_base);
$stmt_historial_base->bind_param("id", $id_asignacion_base, $monto);
$stmt_historial_base->execute();




   



    // Verificar si el pago está retrasado (más de 3 días)
    $fecha_actual = new DateTime();
    $fecha_vencimiento_obj = new DateTime($fecha_cuota);
    $diferencia = $fecha_actual->diff($fecha_vencimiento_obj);
    if ($diferencia->days > 3) {
        // Generar alerta si el pago tiene más de 3 días de atraso
        $mensaje_alerta = "Pago atrasado más de 3 días.";
        $sql_alerta = "INSERT INTO alertas (id_prestamo, mensaje, fecha) VALUES (?, ?, NOW())";
        $stmt_alerta = $conn->prepare($sql_alerta);
        $stmt_alerta->bind_param("is", $id_prestamo, $mensaje_alerta);
        $stmt_alerta->execute();
    }

    // Confirmar la transacción
    $conn->commit();

    // Respuesta de éxito
    $response['status'] = "success";
    $response['message'] = "Pago registrado exitosamente.";
} catch (Exception $e) {
    // Manejo de errores
    $conn->rollback(); // Si hay error, revertir los cambios
    $response['status'] = "error";
    $response['message'] = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Resultado del Pago</title>
</head>
<body>
<script>
    // Usar el estado y el mensaje de la respuesta de PHP
    <?php if ($response['status'] === "success"): ?>
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '<?php echo $response['message']; ?>',
            confirmButtonText: 'Aceptar'
        }).then(() => {
            window.location.href = '../pagosco.php'; // Cambia esta ruta según sea necesario
        });
    <?php else: ?>
        Swal.fire({
            icon: 'error',
            title: '¡Error!',
            text: '<?php echo $response['message']; ?>',
            confirmButtonText: 'Aceptar'
        }).then(() => {
            window.location.href = '../pagosco.php'; // Cambia esta ruta según sea necesario
        });
    <?php endif; ?>
</script>
</body>
</html>
