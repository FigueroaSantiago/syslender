<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login/login.php");
    exit();
}

include '../../includes/headerco.php';
include '../../includes/db.php';

// Obtener los parámetros de la URL
$id_cuota = $_GET['id_cuota'] ?? null;
$id_prestamo = $_GET['id_prestamo'] ?? null;




// Simular datos del cliente (esto debería venir de la base de datos)
$cliente_nombre = "Juan Pérez";  // Este dato debería venir de la base de datos según el ID del préstamo
$numero_cuota = $id_cuota;      // Número de la cuota
$numero_prestamo = $id_prestamo;  // Número del préstamo

if (!$id_cuota || !$id_prestamo) {
    echo "Datos inválidos.";
    exit();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Ruta</title>

    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f4f7fa;
            font-family: 'Arial', sans-serif;
        }

        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 30px;
        }

        .form-group label {
            font-weight: bold;
        }

        .btn-success {
            border-radius: 25px;
            padding: 10px 30px;
            font-size: 1rem;
            transition: background-color 0.3s, transform 0.2s;
        }

        .btn-success:hover {
            transform: scale(1.05);
        }

        .form-control {
            border-radius: 25px;
        }

        .error {
            color: red;
            font-size: 0.9em;
        }

        .input-group-text {
            background-color: #28a745;
            color: white;
        }
    </style>
</head>
<body>
<div class="container">
<div class="form-container">
    <!-- Título y datos del cliente -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Registrar Pago de Cuota</h3>
                    <p><strong>Cliente:</strong> <?= htmlspecialchars($cliente_nombre) ?></p>
                    <p><strong>Prestamo N°:</strong> <?= htmlspecialchars($numero_prestamo) ?></p>
                    <p><strong>Cuota N°:</strong> <?= htmlspecialchars($numero_cuota) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario para registrar el pago -->
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="procesar_pagoco.php">
                        <input type="hidden" name="id_cuota" value="<?= htmlspecialchars($id_cuota) ?>">
                        <input type="hidden" name="id_prestamo" value="<?= htmlspecialchars($id_prestamo) ?>">

                        <!-- Estado de Pago -->
                        <div class="form-group">
                            <label for="estado_pago" class="form-label">Estado de Pago</label>
                            <select class="form-select" name="estado_pago" id="estado_pago" onchange="toggleAbonoField()">
                                <option value="pago">Pago Completo</option>
                                <option value="no_pago">No Pago</option>
                                <option value="abono">Abono</option>
                            </select>
                        </div>

                        <!-- Monto del Abono (se muestra solo si se selecciona "Abono") -->
                        <div class="form-group" id="abono-container" style="display: none;">
                            <label for="abono" class="form-label">Monto del Abono</label>
                            <input type="number" class="form-control" name="abono" id="abono" step="0.01" min="0" placeholder="Ingrese el monto del abono">
                        </div>

                        <!-- Observación -->
                        <div class="form-group">
                            <label for="observacion" class="form-label">Observación</label>
                            <textarea class="form-control" name="observacion" id="observacion" rows="3" placeholder="Agregar observación (opcional)"></textarea>
                        </div>

                        <!-- Botón de Enviar -->
                        <button type="submit"  class="btn btn-success btn-block">Registrar Pago</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</div>


<script>
    // Función para mostrar/ocultar el campo del abono según la selección del estado de pago
    function toggleAbonoField() {
        var estadoPago = document.getElementById('estado_pago').value;
        var abonoContainer = document.getElementById('abono-container');

        // Si se selecciona "abono", mostrar el campo
        if (estadoPago === 'abono') {
            abonoContainer.style.display = 'block';
        } else {
            abonoContainer.style.display = 'none';
        }
    }

    // Inicializar la visibilidad del campo 'Abono' según el valor actual del select
    document.addEventListener('DOMContentLoaded', function () {
        toggleAbonoField();  // Llamar a la función para establecer el estado inicial
    });
</script>

<?php include '../../includes/footer.php'; ?>
