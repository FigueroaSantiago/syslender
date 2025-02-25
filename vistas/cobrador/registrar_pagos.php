
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Pago</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Registrar Pago</h1>
    <form action="procesar_registrar_pago.php" method="POST">
        <div class="form-group">
            <label for="prestamo">Préstamo</label>
            <select id="prestamo" name="id_prestamo" class="form-control">
                <?php
                $prestamos = obtenerPrestamosPorCobrador($_SESSION['user_id']); // Asumiendo que existe esta función
                foreach ($prestamos as $prestamo) {
                    echo "<option value='{$prestamo['id']}'>{$prestamo['monto']} - {$prestamo['fecha_vencimiento']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="monto">Monto</label>
            <input type="number" id="monto" name="monto" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="estado">Estado</label>
            <select id="estado" name="estado" class="form-control">
                <option value="pago_completo">Pago Completo</option>
                <option value="no_pago">No Pago</option>
                <option value="abono">Abono</option>
            </select>
        </div>
        <div class="form-group">
            <label for="observacion">Observación</label>
            <input type="text" id="observacion" name="observacion" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Registrar</button>
    </form>
</div>
</body>
</html>
