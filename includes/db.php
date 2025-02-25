<?php


// includes/db.php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=gucobro', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}



$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gucobro";

$conn = new mysqli($servername, $username, $password, $dbname);