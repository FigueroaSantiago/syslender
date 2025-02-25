<?php
include 'db.php';



// Definición de la función getCount
function getCount($table)
{
    global $conn;
    $sql = "SELECT COUNT(*) as count FROM $table";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['count'];
}


function getActiveLoans()
{
    global $conn;
    $sql = "SELECT COUNT(*) as count FROM prestamo WHERE fecha_vencimiento >= CURDATE()";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['count'];
}

function getTotalExpenses()
{
    global $conn;
    $sql = "SELECT SUM(monto) as total FROM gastos";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'];
}



require_once 'config.php';

function getAllClientes()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start(); // Inicia sesión solo si no está activa
    }

    if (!isset($_SESSION['id_cuenta'])) {
        die("Error: No hay una cuenta seleccionada.");
    }

    global $pdo; // Conexión a la base de datos
    $id_cuenta = $_SESSION['id_cuenta'];

    $sql = "SELECT * FROM cliente WHERE id_cuenta = :id_cuenta";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id_cuenta' => $id_cuenta]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}






function getClienteById($cliente_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM cliente WHERE id_cliente = ?");
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function agregarCliente($nombres, $apellidos, $id_genero, $direccion_casa, $direccion_negocio, $telefono, $cedula, $id_ruta, $id_cuenta)
{
    global $pdo; // Conexión a la base de datos

    $sql = "INSERT INTO cliente (nombres, apellidos, id_genero, direccion_casa, direccion_negocio, telefono, cedula, id_ruta, id_cuenta) 
            VALUES (:nombres, :apellidos, :id_genero, :direccion_casa, :direccion_negocio, :telefono, :cedula, :id_ruta, :id_cuenta)";

    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        'nombres' => $nombres,
        'apellidos' => $apellidos,
        'id_genero' => $id_genero,
        'direccion_casa' => $direccion_casa,
        'direccion_negocio' => $direccion_negocio,
        'telefono' => $telefono,
        'cedula' => $cedula,
        'id_ruta' => $id_ruta,
        'id_cuenta' => $id_cuenta
    ]);
}


function actualizarCliente($id_cliente, $nombres, $apellidos, $id_genero, $direccion_casa, $direccion_negocio, $telefono, $cedula)
{
    global $pdo;
    $stmt = $pdo->prepare('UPDATE cliente SET nombres = :nombres, apellidos = :apellidos, id_genero = :id_genero, direccion_casa = :direccion_casa, direccion_negocio = :direccion_negocio, telefono = :telefono, cedula = :cedula WHERE id_cliente = :id_cliente');
    $stmt->execute([
        'nombres' => $nombres,
        'apellidos' => $apellidos,
        'id_genero' => $id_genero,
        'direccion_casa' => $direccion_casa,
        'direccion_negocio' => $direccion_negocio,
        'telefono' => $telefono,
        'cedula' => $cedula,

        'id_cliente' => $id_cliente
    ]);
}

function eliminarCliente($id_cliente)
{
    global $conn;

    try {
        $query = "DELETE FROM cliente WHERE id_cliente = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id_cliente);
        $stmt->execute();

        return true; // Eliminación exitosa
    } catch (mysqli_sql_exception $e) {
        if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
            return false; // Restricción por clave foránea
        }
        throw $e; // Otros errores
    }
}





function getGeneros()
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM genero');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getGarantias()
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM garaNtias');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllPrestamos()
{
    global $pdo;
    // Ajustar la consulta para la estructura actual de la tabla prestamo
    $stmt = $pdo->prepare('
        SELECT 
            p.id_prestamo, 
            c.nombres AS cliente, 
            p.monto_inicial AS principal_prestamo, 
            p.saldo_actual,
            p.interes_total,
            p.inicio_fecha ,
            p.vencimiento_fecha,
            DATE_ADD(p.inicio_fecha, INTERVAL p.duracion DAY) AS fecha_vencimiento,
            p.monto_cuota,
            p.estado,
            p.duracion,
            p.inicio_fecha,
            p.id_rol_user

        FROM 
            prestamo p
        JOIN 
            cliente c 
        ON 
            p.id_cliente = c.id_cliente
    ');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getAllRoles()
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT id_rol, rol FROM rol');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// funciones.php

// Conexión a la base de datos (asegúrate de usar tu propia configuración)
function obtenerConexion()
{
    $conexion = new mysqli("localhost", "root", "", "gucobro");
    if ($conexion->connect_error) {
        die("Conexión fallida: " . $conexion->connect_error);
    }
    return $conexion;
}


function obtenerConexion1()
{
    try {
        // Aquí debes usar tu propia configuración de base de datos
        $conexion = new PDO('mysql:host=localhost;dbname=gucobro', 'root', '');
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conexion;
    } catch (PDOException $e) {
        echo 'Error de conexión: ' . $e->getMessage();
        return null;
    }
}


// Verifica si el rol ya existe en la base de datos
function rolExiste($rol)
{
    // Obtener conexión
    $conexion = obtenerConexion();

    // Escapa el valor para prevenir inyección SQL
    $rol = $conexion->real_escape_string($rol);

    // Consulta SQL para verificar si el rol ya existe
    $query = "SELECT id_rol FROM rol WHERE rol = '$rol'";

    // Ejecutar la consulta
    $resultado = $conexion->query($query);

    // Verifica si ya existe un rol con ese nombre
    if ($resultado && $resultado->num_rows > 0) {
        return true; // El rol ya existe
    }

    return false; // El rol no existe
}
// Agregar rol a la base de datos
function agregarRol($rol)
{
    $conexion = obtenerConexion();

    // Escapar el valor del rol para prevenir inyección SQL
    $rol = $conexion->real_escape_string($rol);

    // Consulta SQL para insertar el nuevo rol
    $query = "INSERT INTO rol (rol) VALUES ('$rol')";

    // Ejecutar la consulta
    if ($conexion->query($query) === TRUE) {
        return true; // Rol agregado correctamente
    } else {
        return false; // Error al agregar el rol
    }
}



function getPrestamoById($id_prestamo)
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM prestamo WHERE id_prestamo = :id_prestamo');
    $stmt->execute(['id_prestamo' => $id_prestamo]);
    return $stmt->fetch();
}

function agregarPrestamo($id_cliente, $monto, $fecha_desembolso, $fecha_vencimiento,  $id_rol_user)
{
    global $pdo;

    // Verificar que id_rol_user existe en la tabla rol_user
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM rol_user WHERE id_rol_user = :id_rol_user');
    $stmt->execute([':id_rol_user' => $id_rol_user]);
    if ($stmt->fetchColumn() == 0) {
        throw new Exception('id_rol_user no existe en la tabla rol_user');
    }

    // Verificar que id_cliente existe en la tabla cliente
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM cliente WHERE id_cliente = :id_cliente');
    $stmt->execute([':id_cliente' => $id_cliente]);
    if ($stmt->fetchColumn() == 0) {
        throw new Exception('id_cliente no existe en la tabla cliente');
    }


    // Si todas las verificaciones pasan, proceder con la inserción
    $sql = "INSERT INTO prestamo (id_cliente, monto, fecha_desembolso, fecha_vencimiento, id_rol_user) 
            VALUES (:id_cliente, :monto, :fecha_desembolso, :fecha_vencimiento, :id_rol_user )";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id_cliente' => $id_cliente,
        ':monto' => $monto,
        ':fecha_desembolso' => $fecha_desembolso,
        ':fecha_vencimiento' => $fecha_vencimiento,
        ':id_rol_user' => $id_rol_user,

    ]);
}

function calcularFechaVencimiento($fecha_inicio, $duracion)
{
    $fecha = date('Y-m-d', strtotime($fecha_inicio . ' +1 day'));
    $dias_agregados = 0;

    while ($dias_agregados < $duracion) {
        if (date('w', strtotime($fecha)) != 0) { // Excluir domingos
            $dias_agregados++;
        }
        $fecha = date('Y-m-d', strtotime($fecha . ' +1 day'));
    }
    return $fecha;
}

function generarCuotas($id_prestamo, $fecha_inicio, $duracion, $monto_inicial, $interes_total, $id_cuenta)
{
    global $conn;

    // Calcula el monto de cada cuota
    $monto_cuota = round($monto_inicial / $duracion, 2) + round($interes_total / $duracion, 2);

    $dias_creados = 0;

    // Comienza a contar desde el día siguiente a la fecha de inicio
    $fecha = date('Y-m-d', strtotime($fecha_inicio . ' +1 day'));

    // Generación de cuotas solo en días lunes a sábado (excluyendo domingos)
    while ($dias_creados < $duracion) {
        // Verifica si el día no es domingo
        $dia_semana = date('w', strtotime($fecha));

        if ($dia_semana != 0) { // Si no es domingo, genera cuota
            $numero_cuota = $dias_creados + 1;

            // Inserta la cuota en la base de datos
            $stmt = $conn->prepare("
                INSERT INTO cuota_prestamo (id_prestamo, numero_cuota, fecha_cuota, valor_cuota, estado, id_cuenta) 
                VALUES (?, ?, ?, ?, 'sin cobrar', ?)
            ");
            $stmt->bind_param("iisdi", $id_prestamo, $numero_cuota, $fecha, $monto_cuota, $id_cuenta);
            $stmt->execute();

            // Incrementa el contador de días creados
            $dias_creados++;
        }

        // Avanza al siguiente día
        $fecha = date('Y-m-d', strtotime($fecha . ' +1 day'));
    }
}





function actualizarPrestamo($id_prestamo, $id_cliente, $monto, $fecha_desembolso, $fecha_vencimiento, $id_cuotas)
{
    global $conn; // Asegúrate de que $conn es una instancia de mysqli

    // Prepara la consulta SQL con marcadores de posición
    $sql = "UPDATE prestamo 
            SET id_cliente = ?, 
                principal_prestamo = ?, 
                fecha_desembolso = ?, 
                fecha_vencimiento = ?, 
                id_cuotas = ? 
            WHERE id_prestamo = ?";

    // Prepara la sentencia
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Error en la preparación de la sentencia: ' . $conn->error);
    }

    // Enlaza los parámetros a la sentencia
    $stmt->bind_param('issssi', $id_cliente, $monto, $fecha_desembolso, $fecha_vencimiento, $numero_cuotas, $id_prestamo);

    // Ejecuta la sentencia
    $result = $stmt->execute();

    if ($result === false) {
        die('Error al ejecutar la sentencia: ' . $stmt->error);
    }

    return $result;
}



function eliminarPrestamo($id_prestamo)
{
    global $pdo;
    $stmt = $pdo->prepare('DELETE FROM prestamo WHERE id_prestamo = :id_prestamo');
    $stmt->execute(['id_prestamo' => $id_prestamo]);
}


function getAllUsuarios()
{
    global $conn;

    // Asegurar sesión activa
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verificar si las variables de sesión están definidas
    $user_role = $_SESSION['role_id'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null;
    $id_cuenta = $_SESSION['id_cuenta'] ?? null;

    if (!$user_role || !$user_id) {
        return []; // Si no hay usuario logueado, retornar vacío
    }

    if ($user_role == 18) {
        // Si es Gestor, obtiene todos los usuarios
        $sql = "SELECT u.id_user, u.nombre, u.apellido, u.contacto, u.cedula, u.ultimo_login, u.estado, 
                    r.rol AS rol_name
                FROM user u
                JOIN rol_user ru ON u.id_user = ru.id_user
                JOIN rol r ON ru.id_rol = r.id_rol";
        $stmt = $conn->prepare($sql);
    } elseif ($user_role == 1 && $id_cuenta) {
        // Si es Administrador, obtiene los cobradores de su cuenta activa
        $sql = "SELECT u.id_user, u.nombre, u.apellido, u.contacto, u.cedula, u.ultimo_login, u.estado, 
                    r.rol AS rol_name
                FROM user u
                JOIN rol_user ru ON u.id_user = ru.id_user
                JOIN rol r ON ru.id_rol = r.id_rol
                WHERE ru.id_rol = 2 AND u.id_cuenta = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_cuenta);
    } else {
        return [];
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return []; // Si la consulta no devuelve resultados, devuelve un array vacío
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}





function getUsuarioById($id_user)
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM user WHERE id_user = :id_user');
    $stmt->execute(['id_user' => $id_user]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function agregarUsuario($nombre, $apellido, $contacto, $cedula, $password)
{
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO user (nombre, apellido, contacto, cedula, password) VALUES (:nombre, :apellido, :contacto, :cedula, :password)');
    $stmt->execute([
        'nombre' => $nombre,
        'apellido' => $apellido,
        'contacto' => $contacto,
        'cedula' => $cedula,
        'password' => $password
    ]);
}

function actualizarUsuario($id_user, $nombre, $apellido, $contacto, $cedula, $password)
{
    global $pdo;

    try {
        // Verifica si se debe actualizar la contraseña o no
        if (!empty($password)) {
            $stmt = $pdo->prepare('
                UPDATE user
                SET nombre = :nombre, apellido = :apellido, contacto = :contacto, cedula = :cedula, password = :password
                WHERE id_user = :id_user
            ');
            $result = $stmt->execute([
                'id_user' => $id_user,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'contacto' => $contacto,
                'cedula' => $cedula,
                'password' => $password
            ]);
        } else {
            $stmt = $pdo->prepare('
                UPDATE user
                SET nombre = :nombre, apellido = :apellido, contacto = :contacto, cedula = :cedula
                WHERE id_user = :id_user
            ');
            $result = $stmt->execute([
                'id_user' => $id_user,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'contacto' => $contacto,
                'cedula' => $cedula
            ]);
        }

        // Si se ejecuta correctamente, retorna true
        return $result;
    } catch (PDOException $e) {
        // Maneja los errores y retorna false
        return false;
    }
}

// functions.php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=gucobro', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Error de conexión: ' . $e->getMessage();
}


// En functions.php
function eliminarUsuario($id_user)
{
    global $pdo;
    try {
        // Verificar si el usuario existe
        $stmt = $pdo->prepare("SELECT * FROM user WHERE id_user = :id_user");
        $stmt->execute(['id_user' => $id_user]);

        if ($stmt->rowCount() == 0) {
            echo 'error: ID no encontrado'; // ID no encontrado
            return;
        }

        // Intentar eliminar el usuario
        $stmt = $pdo->prepare("DELETE FROM user WHERE id_user = :id_user");
        $stmt->execute(['id_user' => $id_user]);

        if ($stmt->rowCount() > 0) {
            echo 'success'; // Mensaje de éxito
        } else {
            echo 'error: No se eliminó ningún registro, ID no encontrado.'; // Mensaje si no se eliminó
        }
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
            echo 'constraint_error'; // Error de clave foránea
        } else {
            echo 'error: ' . $e->getMessage(); // Otros errores
        }
    } catch (Exception $e) {
        echo 'error: ' . $e->getMessage(); // Errores generales
    }
}
function cobradorTieneRuta($id_rol_user)
{
    global $conn; // Asegúrate de usar la conexión a la base de datos correcta
    $query = "SELECT COUNT(*) as total FROM ruta WHERE id_rol_user = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id_rol_user);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total'] > 0;
}
function cobradorTieneRutas($id_rol_user, $id_ruta)
{
    global $conn;

    // Verificar la conexión
    if ($conn->connect_errno) {
        die("Conexión cerrada o inválida.");
    }

    // Consulta para verificar rutas del rol, excluyendo la actual
    $query = "
        SELECT COUNT(*) 
        FROM ruta 
        WHERE id_rol_user = ? AND id_ruta != ?
    ";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error al preparar la consulta: " . $conn->error);
    }

    // Pasar los parámetros
    $stmt->bind_param("ii", $id_rol_user, $id_ruta_actual);
    $stmt->execute();

    // Obtener el resultado
    $stmt->bind_result($count);
    $stmt->fetch();

    // Liberar recursos
    $stmt->close();

    // Devolver true si ya tiene otra ruta asignada, excluyendo la actual
    return $count > 0;
}

function nombreRutaExiste($nombre_ruta, $id_ruta)
{
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) FROM ruta WHERE nombre_ruta = ? AND id_ruta != ?");
    $stmt->bind_param("si", $nombre_ruta, $id_ruta);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    // Retorna true si ya existe una ruta con el mismo nombre
    return $count > 0;
}

function obtenerRutaPorId($id_ruta)
{
    global $pdo; // Asegúrate de que $pdo es tu variable de conexión PDO
    try {
        $stmt = $pdo->prepare('SELECT * FROM ruta WHERE id_ruta = :id_ruta');
        $stmt->execute(['id_ruta' => $id_ruta]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
        return null;
    }
}


// Agrega un nuevo rol

function getRolById($id_rol)
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM rol WHERE id_rol = :id_rol');
    $stmt->execute(['id_rol' => $id_rol]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}



function actualizarRol($id_rol, $rol)
{
    global $pdo;
    try {
        $stmt = $pdo->prepare('UPDATE rol SET rol = :rol WHERE id_rol = :id_rol');
        $stmt->execute(['id_rol' => $id_rol, 'rol' => $rol]);
        return $stmt->rowCount() > 0; // Devuelve true si se actualizó
    } catch (PDOException $e) {
        return false; // Manejo de errores
    }
}
function eliminarRuta($id_ruta)
{
    global $pdo;
    try {
        // Verificar si la ruta existe
        $stmt = $pdo->prepare("SELECT * FROM ruta WHERE id_ruta = :id_ruta");
        $stmt->execute(['id_ruta' => $id_ruta]);

        if ($stmt->rowCount() == 0) {
            return 'error: ID no encontrado'; // ID no encontrado
        }

        // Intentar eliminar la ruta
        $stmt = $pdo->prepare("DELETE FROM ruta WHERE id_ruta = :id_ruta");
        $stmt->execute(['id_ruta' => $id_ruta]);

        if ($stmt->rowCount() > 0) {
            return 'success'; // Mensaje de éxito
        } else {
            return 'error: No se eliminó ningún registro, ID no encontrado.'; // Mensaje si no se eliminó
        }
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
            return 'constraint_error'; // Error de clave foránea
        } else {
            return 'error: ' . $e->getMessage(); // Otros errores
        }
    } catch (Exception $e) {
        return 'error: ' . $e->getMessage(); // Errores generales
    }
}



function eliminarRol($id_rol)
{
    global $pdo;
    $stmt = $pdo->prepare('DELETE FROM rol WHERE id_rol = :id_rol');
    return $stmt->execute(['id_rol' => $id_rol]);
}

// Obtiene todos los permisos
function getPermisos()
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM permisos');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Asigna permisos a un rol
function asignarPermisos($id_rol, $permisos)
{
    global $pdo; // Asegúrate de que $pdo está disponible

    // Primero, limpiamos los permisos existentes
    $sql = "DELETE FROM rol_permisos WHERE id_rol = :id_rol";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_rol', $id_rol);

    // Ejecutar la eliminación
    if (!$stmt->execute()) {
        return false; // Error al eliminar permisos existentes
    }

    // Ahora insertamos los nuevos permisos
    $sql = "INSERT INTO rol_permisos (id_rol, id_permiso) VALUES (:id_rol, :id_permiso)";
    $stmt = $pdo->prepare($sql);

    foreach ($permisos as $id_permiso) {
        $stmt->bindParam(':id_rol', $id_rol);
        $stmt->bindParam(':id_permiso', $id_permiso);
        if (!$stmt->execute()) {
            return false; // Error al insertar permisos
        }
    }

    return true; // Todo ha ido bien
}

function getPermisosAsignados($id_rol)
{
    // Asumiendo que ya tienes una conexión a la base de datos llamada $conn
    global $conn;

    // Consulta SQL para obtener los permisos asignados a un rol
    $query = "SELECT id_permiso FROM rol_permisos WHERE id_rol = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_rol);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $permisos_asignados = [];

        // Almacena cada permiso asignado en un array
        while ($row = $result->fetch_assoc()) {
            $permisos_asignados[] = $row['id_permiso'];
        }

        $stmt->close();
        return $permisos_asignados;
    } else {
        // Manejo de errores en caso de que la consulta falle
        return false;
    }
}




function obtenerPermisosUsuario($id_usuario)
{
    $conexion = obtenerConexion1();

    // Obtener los permisos del rol del usuario
    $query = "
        SELECT p.nombre 
        FROM permisos p
        INNER JOIN rol_permisos rp ON p.id_permisos = rp.id_permiso
        INNER JOIN rol_user ru ON ru.id_rol = rp.id_rol
        WHERE ru.id_user = :id_usuario
    ";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT); // Vincula correctamente el parámetro
    $stmt->execute();
    $permisos_rol = $stmt->fetchAll(PDO::FETCH_COLUMN); // Obtener permisos del rol

    // Obtener permisos adicionales del usuario
    $query = "SELECT permisos_adicionales FROM rol_user WHERE id_user = :id_usuario";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT); // Vincula correctamente el parámetro
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $permisos_adicionales = json_decode($row['permisos_adicionales'] ?? '[]', true); // Permisos adicionales del usuario

    // Unir ambos permisos (del rol y los adicionales)
    return array_merge($permisos_rol, $permisos_adicionales);
}


// Verificar si el usuario tiene permiso para agregar un préstamo
//$id_usuario = $_SESSION['user_id'];  // Obtén el ID del usuario
//$permisos_usuario = obtenerPermisosUsuario($id_usuario); // Obtén los permisos del usuario

//if (in_array('agregar_prestamo', $permisos_usuario)) {
//  echo "<li><a href='agregar_prestamo.php'>Agregar Préstamo</a></li>";
//}




// Obtiene todos los gastos
function getAllGastos()
{
    global $pdo;
    $stmt = $pdo->query('SELECT * FROM gastos');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtiene un gasto por ID
function getGastoById($id_gasto)
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM gastos WHERE id_gastos = :id_gastos');
    $stmt->execute(['id_gastos' => $id_gasto]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Agrega un nuevo gasto
function agregarGasto($tipo_gasto, $precio)
{
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO gastos (tipo_gasto, precio) VALUES (:tipo_gasto, :precio)');
    $stmt->execute(['tipo_gasto' => $tipo_gasto, 'precio' => $precio]);
}

// Actualiza un gasto
function actualizarGasto($id_gasto, $tipo_gasto, $precio)
{
    global $pdo;
    $stmt = $pdo->prepare('UPDATE gastos SET tipo_gasto = :tipo_gasto, precio = :precio WHERE id_gasto = :id_gasto');
    $stmt->execute(['tipo_gasto' => $tipo_gasto, 'precio' => $precio, 'id_gasto' => $id_gasto]);
}

// Elimina un gasto
function eliminarGasto($id_gasto)
{
    global $pdo;
    $stmt = $pdo->prepare('DELETE FROM gastos WHERE id_gasto = :id_gasto');
    $stmt->execute(['id_gasto' => $id_gasto]);
}

function getAllRutas()
{
    global $pdo;

    try {
        $sql = '
            SELECT 
                r.id_ruta,
                r.nombre_ruta,
                r.ciudad,
                r.descripcion,
                CONCAT(u.nombre, " ", u.apellido) AS nombre_usuario
            FROM 
                ruta r
            LEFT JOIN 
                rol_user ru ON r.id_rol_user = ru.id_rol_user
            LEFT JOIN 
                user u ON ru.id_user = u.id_user
        ';

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
        return [];
    }
}

function agregarRuta($nombre_ruta, $ciudad, $description, $id_rol_user)
{
    // Conexión a la base de datos
    $conexion = new mysqli('localhost', 'root', '', 'gucobro');

    // Verificar la conexión
    if ($conexion->connect_error) {
        return false; // O maneja el error según necesites
    }

    // Preparar la consulta SQL para evitar inyecciones
    $stmt = $conexion->prepare("INSERT INTO ruta (nombre_ruta, ciudad, descripcion, id_rol_user) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $nombre_ruta, $ciudad, $description, $id_rol_user);

    // Ejecutar la consulta
    $resultado = $stmt->execute();

    // Cerrar la conexión
    $stmt->close();
    $conexion->close();

    return $resultado; // Devuelve verdadero si se agregó correctamente, falso en caso contrario
}



function editarRuta($id_ruta, $nombre_ruta, $ciudad, $description, $id_rol_user)
{
    global $pdo;
    try {
        $sql = 'UPDATE ruta SET nombre_ruta = :nombre_ruta, ciudad = :ciudad, descripcion = :description, id_rol_user = :id_rol_user WHERE id_ruta = :id_ruta';
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'id_ruta' => $id_ruta,
            'nombre_ruta' => $nombre_ruta,
            'ciudad' => $ciudad,
            'description' => $description,
            'id_rol_user' => $id_rol_user
        ]);

        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        echo "<script>console.error('Error: " . addslashes($e->getMessage()) . "');</script>";
        return false;
    }
}
function getAllGeneros()
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM genero');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getGeneroById($id)
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM genero WHERE id = :id');
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function agregarGenero($nombre_genero)
{
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO genero (genero) VALUES (:nombre_genero)');
    $stmt->execute(['nombre_genero' => $nombre_genero]);
}

function editarGenero($id, $nombre_genero)
{
    global $pdo;
    $stmt = $pdo->prepare('UPDATE genero SET nombre_genero = :nombre_genero WHERE id = :id');
    $stmt->execute(['nombre_genero' => $nombre_genero, 'id' => $id]);
}

function eliminarGenero($id)
{
    global $pdo;
    $stmt = $pdo->prepare('DELETE FROM genero WHERE id = :id');
    $stmt->execute(['id' => $id]);
}

function getAllGarantias()
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM garantias');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getGarantiaById($id)
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM garantia WHERE id = :id');
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function agregarGarantia($descripcion, $soporte, $id_cliente)
{
    global $conn;
    $sql = "INSERT INTO garatias (descripcion, soporte, id_cliente) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $descripcion, $soporte, $id_cliente);
    $stmt->execute();
}
function editarGarantia($id, $descripcion, $soporte)
{
    global $pdo;
    $stmt = $pdo->prepare('UPDATE garantia SET descripcion = :descripcion, soporte = :soporte WHERE id = :id');
    $stmt->execute(['descripcion' => $descripcion, 'soporte' => $soporte, 'id' => $id]);
}

function eliminarGarantia($id)
{
    global $pdo;
    $stmt = $pdo->prepare('DELETE FROM garantia WHERE id = :id');
    $stmt->execute(['id' => $id]);
}


// functions.php

include 'config.php';

function obtenerPagosPorPrestamo($id_prestamo)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM pagos WHERE id_prestamo = :id_prestamo ORDER BY fecha ASC");
    $stmt->execute(['id_prestamo' => $id_prestamo]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function registrarPago($datos)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO pagos (id_prestamo, fecha_pago, monto, estado, observacion, registrado_por, fecha_registro) VALUES (:id_prestamo, :fecha_pago, :monto, :estado, :observacion, :registrado_por, NOW())");
    $stmt->execute($datos);
}

function actualizarEstadoPrestamo($id_prestamo, $estado)
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE prestamos SET estado = :estado WHERE id = :id_prestamo");
    $stmt->execute(['estado' => $estado, 'id_prestamo' => $id_prestamo]);
}

function calcularMulta($id_prestamo, $dias_no_pago)
{
    global $pdo;
    if ($dias_no_pago >= 3) {
        $multa = 100; // Ejemplo de valor de la multa
        $stmt = $pdo->prepare("UPDATE prestamos SET monto = monto + :multa WHERE id = :id_prestamo");
        $stmt->execute(['multa' => $multa, 'id_prestamo' => $id_prestamo]);
    }
}

function ajustarFechaPago($fecha)
{
    $dias_no_laborables = ['Sunday']; // Ejemplo de días no laborables

    $dia_semana = date('l', strtotime($fecha));
    while (in_array($dia_semana, $dias_no_laborables)) {
        $fecha = date('Y-m-d', strtotime($fecha . ' +1 day'));
        $dia_semana = date('l', strtotime($fecha));
    }

    return $fecha;
}


function getCounts($table)
{
    global $conn;
    $sql = "SELECT COUNT(*) AS count FROM $table";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['count'];
}

function getprestamosactivos()
{
    global $conn;
    $sql = "SELECT COUNT(*) AS count FROM prestamo WHERE estado = 'activo'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['count'];
}

function getTotalgastos()
{
    global $conn;
    // Asumiendo que 'estado' es el campo que determina si el préstamo está activo
    $sql = "SELECT SUM(saldo_actual) AS total FROM prestamo WHERE estado = 'activo'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'];
}


function getTotalPayments()
{
    global $conn;
    $sql = "SELECT SUM(monto) AS total FROM pagos";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'];
}

function getLoansByStatus()
{
    global $conn;
    $sql = "SELECT estado, COUNT(*) AS count FROM prestamo GROUP BY estado";
    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[$row['estado']] = $row['count'];
    }
    return $data;
}

function getExpensesByType()
{
    global $conn;
    // Ajustamos la consulta para unir la tabla gastos con la tabla tipo_gasto
    $sql = "SELECT tipo_gastos.descripcion AS tipo, SUM(gastos.monto) AS total 
            FROM gastos 
            JOIN tipo_gastos ON gastos.id_tipo_gasto = tipo_gastos.id_tipo_gasto 
            GROUP BY tipo_gastos.descripcion";
    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[$row['tipo']] = $row['total'];
    }
    return $data;
}

function getMonthlyIncomeAndExpenses()
{
    global $conn;
    $sql = "SELECT 
                MONTH(fecha) AS mes, 
                SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) AS ingresos,
                SUM(CASE WHEN tipo = 'gasto' THEN monto ELSE 0 END) AS gastos
            FROM transacciones 
            GROUP BY MONTH(fecha)";
    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function getAlerts()
{
    global $conn;
    $sql = "SELECT mensaje FROM alertas WHERE activo = 1";
    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row['mensaje'];
    }
    return $data;
}



function getRolesDeCobrador()
{
    global $pdo;

    try {
        $sql = '
           SELECT 
               ru.id_rol_user AS id_rol_user,
               CONCAT(u.nombre, " ", u.apellido, " (", u.cedula, ")") AS nombre_completo,
               r.rol AS rol
           FROM 
               rol_user ru
           JOIN 
               user u ON ru.id_user = u.id_user
           JOIN 
               rol r ON ru.id_rol = r.id_rol
           WHERE 
               r.rol = :cobrador;
        ';

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['cobrador' => 'cobrador']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
        return [];
    }
}







function getClientesByRuta($id_ruta)
{
    // Conectar a la base de datos usando PDO
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=gucobro', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Preparar la consulta SQL para obtener clientes por id_ruta
        $stmt = $pdo->prepare('SELECT nombres FROM cliente WHERE id_ruta = :id_ruta');
        $stmt->bindParam(':id_ruta', $id_ruta, PDO::PARAM_INT);
        $stmt->execute();

        // Obtener todos los resultados como un array asociativo
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $clientes;
    } catch (PDOException $e) {
        // Manejo de errores: retornar un array vacío o loggear el error
        error_log('Error al obtener los clientes: ' . $e->getMessage());
        return [];
    }
}
function getClientesByRutaForUser($pdo, $id_ruta)
{
    try {
        // Consultar los clientes de la ruta obtenida
        $stmt = $pdo->prepare('
            SELECT 
                nombres, 
                apellidos, 
                direccion_casa, 
                direccion_negocio, 
                telefono, 
                cedula, 
                id_cliente 
            FROM 
                cliente 
            WHERE 
                id_ruta = :id_ruta
        ');
        $stmt->bindParam(':id_ruta', $id_ruta, PDO::PARAM_INT);
        $stmt->execute();

        // Obtener todos los resultados como un array asociativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Manejo de errores de base de datos
        error_log('Error al obtener los clientes: ' . $e->getMessage());
        return [];
    }
}




function getDatabaseConnection()
{
    // Configura tus credenciales de base de datos
    $host = 'localhost'; // Cambia esto si tu base de datos está en otro servidor
    $username = 'root';  // Cambia esto a tu nombre de usuario de base de datos
    $password = '';      // Cambia esto a tu contraseña de base de datos
    $database = 'gucobro'; // Cambia esto al nombre de tu base de datos

    // Crea una nueva conexión
    $conn = new mysqli($host, $username, $password, $database);

    // Verifica si hay errores en la conexión
    if ($conn->connect_error) {
        die('Error de conexión: ' . $conn->connect_error);
    }

    return $conn;
}




function getPermissionsByRoleId($roleId)
{
    // Supongamos que tienes una tabla 'permisos' y una tabla intermedia 'roles_permisos' que asocia roles con permisos
    $pdo = new PDO('mysql:host=localhost;dbname=gucobro', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT p.nombre 
              FROM permisos p 
              INNER JOIN rol_permisos rp ON p.id_permisos = rp.id_permiso
              WHERE rp.id_rol = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$roleId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



function calcularBaseDisponible($id_asignacion_base, $pdo)
{
    $stmt = $pdo->prepare("
        SELECT 
            (SELECT base FROM base WHERE id_base = ?) +
            IFNULL(SUM(CASE WHEN tipo_movimiento = 'pago' THEN monto ELSE 0 END), 0) -
            IFNULL(SUM(CASE WHEN tipo_movimiento IN ('préstamo', 'gasto') THEN monto ELSE 0 END), 0) AS base_disponible
        FROM movimientos_base
        WHERE id_asignacion_base = ?
    ");
    $stmt->execute([$id_asignacion_base, $id_asignacion_base]);
    return $stmt->fetchColumn();
}

function conectarBD()
{
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $dbname = 'gucobro';

    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    return $conn;
}



function esAdministrador($id_user)
{
    $conn = conectarBD(); // Conexión a la base de datos
    $query = "SELECT COUNT(*) AS total 
FROM rol_user ru
JOIN rol r ON ru.id_rol = r.id_rol
WHERE ru.id_user = ? AND r.rol = 'administrador'";;
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        error_log("Error en la preparación de la consulta: " . $conn->error);
        return false;
    }

    $stmt->bind_param('i', $id_user);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    $stmt->close();
    $conn->close(); // Cierra la conexión después de usarla

    return $result['total'] > 0; // Retorna true si el usuario es administrador
}

function tieneRutaAsignada($id_user)
{
    $conn = conectarBD();
    $query = "SELECT COUNT(*) AS total 
              FROM ruta r
              JOIN rol_user ru ON r.id_rol_user = ru.id_rol_user
              WHERE ru.id_user = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log("Error en la consulta: " . $conn->error);
        return false;
    }
    $stmt->bind_param('i', $id_user);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $result['total'] > 0; // Retorna true si tiene rutas asignadas
}
