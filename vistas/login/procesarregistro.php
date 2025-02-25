<?php
include '../../Conexion/conexion.php';
include '../../includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $contacto = $_POST['contacto'];
    $cedula = trim($_POST['cedula']); // Elimina los espacios antes y después de la cédula
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Crea un hash seguro de la contraseña
    $role_id = $_POST['role_id'];

    // Validación de formato de cédula
    $cedula = preg_replace('/\D/', '', $cedula); // Elimina todos los caracteres no numéricos

    if (empty($cedula)) {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La cédula no puede estar vacía.'
                }).then(() => {
                    window.location.href = 'login.php'; // Redirige al login
                });
              </script>";
        exit();
    }

    // Verificar si el formato de la cédula es válido (solo números entre 8 y 15 dígitos)
    if (!preg_match('/^[0-9]{8,15}$/', $cedula)) {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Formato de cédula inválido'
                }).then(() => {
                    window.location.href = 'login.php'; // Redirige al login
                });
              </script>";
        exit();
    }

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
                        timer: 1200,
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
