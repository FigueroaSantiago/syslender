<?php
include '../includes/header.php';
include '../includes/functions.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración del Sistema</title>

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            font-size: 1.2rem;
        }
        .btn-block {
            display: block;
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            text-align: center;
        }
        .text-center {
            margin-bottom: 40px;
            color: #343a40;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center">Configuración del Sistema</h1>

    <div class="row g-4">
        <!-- Gestión de Usuarios -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-users-cog"></i> Gestión de Usuarios</span>
                </div>
                <div class="card-body text-center">
                    <a href="usuarios.php" class="btn btn-outline-primary btn-block"><i class="fas fa-users"></i> Gestionar Usuarios</a>
                    <a href="login/cambiar_contrasena.php" class="btn btn-outline-primary btn-block mt-2"><i class="fas fa-key"></i> Cambiar Contraseña</a>
                </div>
            </div>
        </div>

        <!-- Configuración de Intereses y Penalidades -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-percentage"></i> Intereses y Penalidades</span>
                </div>
                <div class="card-body text-center">
                    <a href="configurar_intereses.php" class="btn btn-outline-warning btn-block"><i class="fas fa-percentage"></i> Configurar Intereses</a>
                   
                </div>
            </div>
        </div>

        <!-- Asignación de Bases -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-hand-holding-usd"></i> Asignación de Bases</span>
                </div>
                <div class="card-body text-center">
                    <a href="base/asignar_base.php" class="btn btn-outline-success btn-block"><i class="fas fa-hand-holding-usd"></i> Asignar Base</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-4">
        <!-- Gestión de Rutas -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-map-marked-alt"></i> Gestión de Rutas</span>
                </div>
                <div class="card-body text-center">
                    <a href="rutas.php" class="btn btn-outline-info btn-block"><i class="fas fa-map-marked-alt"></i> Gestionar Rutas</a>
                </div>
            </div>
        </div>

        <!-- Respaldo de Base de Datos -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-database"></i> Respaldo de Datos</span>
                </div>
                <div class="card-body text-center">
                    <a href="respaldo_bd.php" class="btn btn-outline-danger btn-block"><i class="fas fa-database"></i> Respaldo y Restauración</a>
                </div>
            </div>
        </div>

        <!-- Gestión de Géneros -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-tags"></i> Gestión de Géneros</span>
                </div>
                <div class="card-body text-center">
                    <a href="genero.php" class="btn btn-outline-secondary btn-block"><i class="fas fa-tags"></i> Gestionar Géneros</a>
                </div>
            </div>
        </div>

        <!-- Asignación de Permisos -->

    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
