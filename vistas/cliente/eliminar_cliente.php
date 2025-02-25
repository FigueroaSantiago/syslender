<?php
session_start();
include '../../includes/functions.php';

// Verifica si se recibe una solicitud POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_cliente = $_POST['id_cliente'];

    // Llamada a la función de eliminación
    $resultado = eliminarCliente($id_cliente);

    // Verifica el resultado de la eliminación
    if ($resultado === true) {
        $_SESSION['mensaje_exito'] = "Cliente eliminado correctamente.";
        echo 'success';
    } else {
        $_SESSION['error_eliminar'] = "No se puede eliminar el cliente ya que tiene préstamos registrados.";
        echo 'constraint_error';
    }
} else {
    echo 'invalid_request';
}
?>
