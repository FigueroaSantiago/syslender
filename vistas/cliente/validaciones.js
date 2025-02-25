document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("clienteForm");

    form.addEventListener("submit", function(e) {
        e.preventDefault();

        if (validarFormulario()) {
            form.submit();
        }
    });

    function validarFormulario() {
        const errores = [];
        const nombres = document.getElementById("nombres").value.trim();
        const apellidos = document.getElementById("apellidos").value.trim();
        const telefono = document.getElementById("telefono").value.trim();
        const cedula = document.getElementById("cedula").value.trim();

        const nombreError = validarCampoTexto(nombres, "Nombres");
        const apellidoError = validarCampoTexto(apellidos, "Apellidos");
        const telefonoError = validarTelefono(telefono);
        const cedulaError = validarCedula(cedula);

        if (nombreError !== true) errores.push(nombreError);
        if (apellidoError !== true) errores.push(apellidoError);
        if (telefonoError !== true) errores.push(telefonoError);
        if (cedulaError !== true) errores.push(cedulaError);

        if (errores.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Errores en el formulario',
                html: `<ul>${errores.map(error => `<li>${error}</li>`).join('')}</ul>`
            });
            return false;
        }
        return true;
    }

    function validarCampoTexto(valor, campo) {
        return /^[A-Za-z\s]+$/.test(valor.trim()) ? true : `Error en ${campo}: ingrese un valor válido.`;
    }

    function validarTelefono(telefono) {
        return /^\d{7,10}$/.test(telefono.trim()) ? true : 'Error en Teléfono: debe tener entre 7 y 10 dígitos.';
    }

    function validarCedula(cedula) {
        return /^\d+$/.test(cedula.trim()) ? true : 'Error en Cédula: ingrese solo números.';
    }
});
