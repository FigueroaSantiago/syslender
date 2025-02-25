<?php
include '../../Conexion/conexion.php';
include '../../includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $contacto = $_POST['contacto'];
    $cedula = $_POST['cedula'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Crea un hash seguro de la contraseña
    $role_id = $_POST['role_id'];

    // Verificar si el role_id existe en la tabla roles
    $sql = "SELECT id_rol FROM rol WHERE id_rol = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $role_id); // Asigna el valor de role_id al parámetro en la consulta
    $stmt->execute(); // Ejecuta la consulta
    $result = $stmt->get_result(); // Obtiene el resultado de la consulta

    if ($result->num_rows > 0) { // Verifica si se encontró el role_id
        // Verificar si la cédula ya existe
        $sql = "SELECT id_user FROM user WHERE cedula = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $cedula); // Asigna el valor de cedula al parámetro en la consulta
        $stmt->execute(); // Ejecuta la consulta
        $result = $stmt->get_result(); // Obtiene el resultado de la consulta

        if ($result->num_rows == 0) { // Verifica si no se encontró la cédula (es decir, si es única)
            // Insertar usuario si la cédula no existe
            $sql = "INSERT INTO user (nombre, apellido, contacto, cedula, password) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $nombre, $apellido, $contacto, $cedula, $password); // Asigna los valores a los parámetros en la consulta

            if ($stmt->execute() === TRUE) { // Verifica si la inserción del usuario fue exitosa
                // Obtener el ID del usuario insertado
                $user_id = $conn->insert_id;

                // Insertar en la tabla rol_user
                $sql = "INSERT INTO rol_user (id_user, id_rol) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $user_id, $role_id); // Asigna los valores a los parámetros en la consulta

                if ($stmt->execute() === TRUE) { // Verifica si la inserción en rol_user fue exitosa
                    echo "<script>
                            Swal.fire({
                                title: '¡Éxito!',
                                text: 'Usuario registrado exitosamente',
                                icon: 'success'
                            }).then(() => {
                                window.location.href = 'login.php'; // Redirige al login
                            });
                          </script>";
                } else {
                    echo "<script>
                            Swal.fire({
                                title: 'Error',
                                text: 'Error al asignar el rol: " . $stmt->error . "',
                                icon: 'error'
                            }).then(() => {
                                window.location.href = 'registro.php'; // Redirige al registro
                            });
                          </script>";
                }
            } else {
                echo "<script>
                        Swal.fire({
                            title: 'Error',
                            text: 'Error al registrar el usuario: " . $stmt->error . "',
                            icon: 'error'
                        }).then(() => {
                            window.location.href = 'registro.php'; // Redirige al registro
                        });
                      </script>";
            }
        } else {
            echo "<script>
                    Swal.fire({
                        title: 'Error',
                        text: 'La cédula ya está en uso.',
                        icon: 'error',
                        timer: 1000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = 'registro.php'; // Redirige al registro
                    });
                  </script>";
        }
    } else {
        echo "<script>
                Swal.fire({
                    title: 'Error',
                    text: 'El role_id proporcionado no existe.',
                    icon: 'error'
                }).then(() => {
                    window.location.href = 'registro.php'; // Redirige al registro
                });
              </script>";
    }

    $stmt->close(); // Cierra el statement
    $conn->close(); // Cierra la conexión a la base de datos
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
  <title>Gucobro Property Management</title>
  <link rel="stylesheet" href="../../assets/css/registro.css">
  <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
  <!-- Incluye SweetAlert2 desde CDN -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
<div class="wrapper fadeInDown">
  <div id="formContent">
    <div class="fadeIn first text-center">
      <img src="../../assets/img/fondobr.png" id="icon" alt="User Icon" class="mb-3" />
      <h1>GUCOBRO</h1>
      <h2>CREDIT MANAGEMENT</h2>
    </div>
    <br><br>
    <form id="registroForm">
      <div class="row">
        <div class="form-group col-md-6 fadeIn second">
          <input type="text" id="nombre" class="form-control" name="nombre" placeholder="Nombre" required>
        </div>
        <div class="form-group col-md-6 fadeIn second">
          <input type="text" id="apellido" class="form-control" name="apellido" placeholder="Apellido" required>
        </div>
      </div>
      <div class="row">
        <div class="form-group col-md-6 fadeIn third">
          <input type="text" id="contacto" class="form-control" name="contacto" placeholder="Contacto" required>
        </div>
        <div class="form-group col-md-6 fadeIn third">
          <input type="text" id="cedula" class="form-control" name="cedula" placeholder="Cédula" required>
        </div>
      </div>
      <div class="row">
        <div class="form-group col-md-6 fadeIn fourth">
        <select name="role_id" id="role_id" class="form-control" required>
        <option value="" disabled selected>Selecciona un rol</option>
        <?php
        $roles = getAllRoles();
        foreach ($roles as $rol) {
            echo "<option value='{$rol['id_rol']}'>{$rol['rol']}</option>";
        }
        ?>
    </select>
        </div>
        <div class="form-group col-md-6 fadeIn fourth">
          <input type="password" id="password" class="form-control" name="password" placeholder="Contraseña" required>
        </div>
      </div>
      <br>
      <div class="form-group fadeIn fourth">
        <button type="submit" class="btn btn-success btn-block">Registrar</button>
      </div>
      <br>
    </form>
    <div id="formFooter">
      <a class="underlineHover text-success" href="login.php">¿Ya tienes una cuenta? Inicia sesión</a>
    </div>
  </div>
</div>

<!-- AJAX Script -->
<script>
  $(document).ready(function() {
    $('#registroForm').on('submit', function(e) {
      e.preventDefault(); // Evita el comportamiento por defecto del formulario

      // Enviar el formulario usando AJAX
      $.ajax({
        type: 'POST',
        url: 'registro.php',
        data: $(this).serialize(), // Envía todos los datos del formulario
        success: function(response) {
          $('body').append(response); // Añadir la respuesta del servidor al DOM, que incluye las alertas
        },
        error: function() {
          Swal.fire({
            title: 'Error',
            text: 'Ocurrió un problema al enviar el formulario',
            icon: 'error'
          });
        }
      });
    });
  });
</script>
</body>
</html>
