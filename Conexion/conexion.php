<?php
$servername = "localhost"; //Servidor
$username = "root"; // Reemplaza con tu usuario
$password = ""; // Reemplaza con tu contraseña del usuario
$dbname = "gucobro"; // Nombre de Base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);




