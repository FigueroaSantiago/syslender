<?php
// no_permitido.php

session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso no permitido</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }

        .message-container {
            text-align: center;
            padding: 40px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .message-container h1 {
            color: #dc3545;
        }

        .message-container p {
            margin-top: 20px;
        }

        .message-container a {
            margin-top: 30px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
        }

        .message-container a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="message-container">
    <h1>Acceso denegado</h1>
    <p>No tienes permiso para acceder a esta página.</p>
    <a href="login/login.php" class="btn-success">Volver al inicio</a>
</div>

</body>
</html>
