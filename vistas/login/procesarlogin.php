<?php
session_start();
include '../../Conexion/conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Login</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula = trim($_POST['cedula']);
    $password = $_POST['password'];

    if (!preg_match('/^[0-9]{8,15}$/', $cedula)) {
        echo "<script>
                Swal.fire({icon: 'error', title: 'Error', text: 'Formato de cédula inválido.'})
                .then(() => { window.location.href = 'login.php'; });
              </script>";
        exit();
    }

    // 🔹 Buscar el usuario por cédula y verificar su estado
    $sql = "SELECT id_user, password, estado FROM user WHERE cedula = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $cedula);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // 🚨 Verificar si el usuario está inactivo
        if ($row['estado'] == 'inactivo') {
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Acceso denegado',
                        text: 'Tu cuenta está inactiva. Contacta con el administrador.'
                    }).then(() => { window.location.href = 'login.php'; });
                  </script>";
            exit();
        }

        // 🔹 Si el usuario está activo, verificar la contraseña
        if (password_verify($password, $row['password'])) {
            $user_id = $row['id_user'];

            // 🔹 Actualizar último login
            $update_sql = "UPDATE user SET ultimo_login = NOW() WHERE id_user = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $user_id);
            $update_stmt->execute();

            // 🔹 Obtener el rol del usuario
            $sql = "SELECT id_rol FROM rol_user WHERE id_user = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $role_row = $result->fetch_assoc();
                $role_id = $role_row['id_rol'];

                // ✅ Guardar en sesión
                $_SESSION['user_id'] = $user_id;
                $_SESSION['role_id'] = $role_id;

                // 🚀 SOLO LOS ADMINISTRADORES SELECCIONAN CUENTAS
                if ($role_id == 1) {
                    $sql = "SELECT c.id_cuenta, c.nombre FROM cuenta_admin ca
                    INNER JOIN cuentas c ON ca.id_cuenta = c.id_cuenta
                    WHERE ca.id_admin = ? AND c.estado = 'activa'";            
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $cuentas = $result->fetch_all(MYSQLI_ASSOC);

                    if (count($cuentas) > 1) {
                        $_SESSION['cuentas'] = $cuentas;
                        echo "<script>window.location.href = 'seleccionar_cuenta.php';</script>";
                        exit();
                    } elseif (count($cuentas) == 1) {
                        $_SESSION['id_cuenta'] = $cuentas[0]['id_cuenta'];
                        echo "<script>window.location.href = '../dashboard.php';</script>";
                        exit();
                    } else {
                        echo "<script>
                                Swal.fire({icon: 'error', title: 'Error', text: 'No tienes cuentas asignadas.'})
                                .then(() => { window.location.href = 'login.php'; });
                              </script>";
                        exit();
                    }
                } else {
                    // 🔥 Si es Gestor (18) o Cobrador (3), acceden directamente a su dashboard
                    echo "<script>
                            Swal.fire({
                                icon: 'success', title: 'Bienvenido', text: 'Inicio de sesión exitoso', timer: 2000, showConfirmButton: false
                            }).then(() => {
                                window.location.href = '../dashboard.php';
                            });
                          </script>";
                    exit();
                }
            } else {
                echo "<script>
                        Swal.fire({icon: 'error', title: 'Error', text: 'Rol no encontrado.'})
                        .then(() => { window.location.href = 'login.php'; });
                      </script>";
            }
        } else {
            echo "<script>
                    Swal.fire({icon: 'error', title: 'Error', text: 'Credenciales incorrectas.'})
                    .then(() => { window.location.href = 'login.php'; });
                  </script>";
        }
    } else {
        echo "<script>
                Swal.fire({icon: 'error', title: 'Error', text: 'Usuario no encontrado.'})
                .then(() => { window.location.href = 'login.php'; });
              </script>";
    }
}

$conn->close();
?>
</body>
</html>
