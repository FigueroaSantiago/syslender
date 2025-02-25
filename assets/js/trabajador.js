



function registrar() {
    var nombre = document.getElementById("nombre").value;
    var apellidos = document.getElementById("apellidos").value;
    var telefono = document.getElementById("telefono").value;
    var cargo = document.getElementById("cargo").value;
    var sueldo = document.getElementById("sueldo").value;
    var cedula = document.getElementById("cedula").value;
    var correo = document.getElementById("correo").value;
    var genero = document.getElementById("genero").value;
    var direccion = document.getElementById("direccion").value;

    // Verificar si algún campo está vacío
    if (nombre === "" || apellidos === "" || telefono === "" || cargo === "" || sueldo === "" || cedula === "" || correo === "" || genero === "" || direccion === "") {
        alert("Por favor, llena todos los campos.");
    } else {
        // Enviar datos al controlador mediante AJAX
        $.ajax({
            url: '../controlador/edito.php', // Ruta al archivo PHP que manejará la edición del trabajador
            method: 'POST', // Método HTTP utilizado para la solicitud
            data: { // Datos a enviar al servidor
                nombre: nombre, // Valor del campo "nombre"
                apellidos: apellidos, // Valor del campo "apellido"
                telefono: telefono, // Valor del campo "telefono"
                cargo: cargo, // Valor del campo "cargo"
                sueldo: sueldo, // Valor del campo "sueldo"
                cedula: cedula, // Valor del campo "cedula"
                correo: correo, // Valor del campo "correo"
                genero: genero, // Valor del campo "genero"
                direccion: direccion // Valor del campo "direccion"
            },
            success: function(response) { // Función que se ejecuta si la solicitud es exitosa
                // Manejar la respuesta del controlador
                alert(response); // Mostrar la respuesta al usuario en una alerta
                // Aquí puedes realizar otras acciones, como actualizar la interfaz de usuario o redirigir a otra página
            },
            error: function(xhr, status, error) { // Función que se ejecuta si hay un error en la solicitud
                // Manejar errores si es necesario
                console.error(error); // Mostrar el error en la consola del navegador para propósitos de depuración
                // Aquí puedes realizar otras acciones, como mostrar un mensaje de error al usuario
            }
        });
        
    }
}

















// Función para mostrar una alerta de confirmación antes de eliminar un trabajador
function eliminar() {
    var cedula = document.getElementById("cedula").value;
    if (cedula === "") {
        alert("Por favor, introduce la cédula del trabajador que deseas eliminar.");
    } else {
        // Mostrar una alerta de confirmación utilizando Bootstrap
        swal({
            title: "¿Estás seguro?",
            text: "Una vez eliminado, no podrás recuperar este trabajador.",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                // Si el usuario confirma la eliminación, mostrar un mensaje y realizar la acción
                swal("¡Trabajador eliminado correctamente!", {
                    icon: "success",
                });
                // Aquí puedes realizar la acción de eliminación
            } else {
                // Si el usuario cancela la eliminación, mostrar un mensaje
                swal("Operación cancelada.");
            }
        });
    }
}

// Función para registrar los datos del formulario mediante AJAX
function registrar() {
    var nombre = document.getElementById("nombre").value;
    var apellido = document.getElementById("apellidos").value;
    var telefono = document.getElementById("telefono").value;
    var cargo = document.getElementById("cargo").value;
    var sueldo = document.getElementById("sueldo").value;
    var cedula = document.getElementById("cedula").value;
    var correo = document.getElementById("correo").value;
    var genero = document.getElementById("genero").value;
    var direccion = document.getElementById("direccion").value;

    // Verificar si algún campo está vacío
    if (nombre === "" || apellido === "" || telefono === "" || cargo === "" || sueldo === "" || cedula === "" || correo === "" || genero === "" || direccion === "") {
        alert("Por favor, llena todos los campos.");
    } else {
        // Enviar datos al controlador mediante AJAX
        $.ajax({
            // Configuración de la solicitud AJAX...
        });
    }
}
