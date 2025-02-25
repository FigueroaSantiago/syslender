<?php
session_start();
session_destroy();
include '../../Conexion/conexion.php';


?>

<!DOCTYPE html>
<html lang="es">

<head>
  <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
  <title>login</title>
  <link rel="stylesheet" href="../../assets/css/registro.css">

  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>

<body>
  <div class="wrapper fadeInDown">
    <div id="formContent">
      <div class="fadeIn first">
        <img src="../../assets/img/logo-syslender-copy2.png" id="icon" alt="User Icon" />
        <h1>SYS-LENDER</h1>
        <h4>Mejoramos tus finanzas</h4>
      </div>
      <br><br><br>
      <form action="procesarlogin.php" method="post">
        <div class="form-group mb-2 fadeIn second">
          <input type="text" id="cedula" class="form-control fadeIn second" name="cedula" placeholder="Cédula" required>

        </div>
        <div class="form-group mb-4 fadeIn third">
          <input type="password" id="password" class="form-control fadeIn third" name="password" placeholder="Contraseña" required>
        </div>
        <button type="submit" class="btn btn-custom mb-3 btn-block fadeIn fourth">Iniciar Sesión</button>
      </form>
      <div id="formFooter">
        <a class="underlineHover text-success" href="registro.php">Registrarme</a>
      </div>
    </div>
  </div>




  <script src="../../assets/js/bootstrap.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>