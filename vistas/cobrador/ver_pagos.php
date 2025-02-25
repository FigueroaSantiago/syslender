
<?php
 include '../../includes/header.php'; 
 if (isset($_GET['id_prestamo'])) {
    $id_prestamo = $_GET['id_prestamo'];
} else {
    // Manejo del error si no se proporciona 'id_prestamo'
    echo "Error: id_prestamo no está definido.";
    exit;
}

 ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Pagos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Pagos del Préstamo</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Observación</th>
                <th>Registrado Por</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $id_prestamo = $_GET['id_prestamo']; // Asumimos que el ID del préstamo se pasa como parámetro GET
            $pagos = obtenerPagosPorPrestamo($id_prestamo);
            foreach ($pagos as $pago) {
                echo "<tr>
                    <td>{$pago['fecha_pago']}</td>
                    <td>{$pago['monto']}</td>
                    <td>{$pago['estado']}</td>
                    <td>{$pago['observacion']}</td>
                    <td>{$pago['registrado_por']}</td>
                </tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</body>
</html>
