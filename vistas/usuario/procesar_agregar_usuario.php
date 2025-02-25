<?php
include '../../Conexion/conexion.php';

// Inicializar la respuesta
$response = array('status' => 'error', 'message' => 'Error desconocido.');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $contacto = $_POST['contacto'];
    $cedula = $_POST['cedula'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role_id = $_POST['role_id'];

    // Verificar si el role_id existe en la tabla roles
    $sql = "SELECT id_rol FROM rol WHERE id_rol = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $role_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Verificar si la cédula ya existe
        $sql = "SELECT id_user FROM user WHERE cedula = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $cedula);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            // Insertar usuario si la cédula no existe
            $sql = "INSERT INTO user (nombre, apellido, contacto, cedula, password) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $nombre, $apellido, $contacto, $cedula, $password);

            if ($stmt->execute() === TRUE) {
                // Obtener el ID del usuario insertado
                $user_id = $conn->insert_id;

                // Insertar en la tabla rol_user
                $sql = "INSERT INTO rol_user (id_user, id_rol) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $user_id, $role_id);

                if ($stmt->execute() === TRUE) {
                    $response = array('status' => 'success', 'message' => 'Usuario registrado exitosamente.');
                } else {
                    $response = array('status' => 'error', 'message' => 'Error al asignar el rol: ' . $stmt->error);
                }
            } else {
                $response = array('status' => 'error', 'message' => 'Error al registrar el usuario: ' . $stmt->error);
            }
        } else {
            $response = array('status' => 'error', 'message' => 'La cédula ya está en uso.');
        }
    } else {
        $response = array('status' => 'error', 'message' => 'El Rol proporcionado no existe.');
    }

    $stmt->close();
    $conn->close();

    // Enviar respuesta JSON al cliente
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
