// Función para confirmar antes de eliminar
function confirmarEliminar(id) {
    swal({
        title: "¿Estás seguro?",
        text: "Una vez eliminado, no podrás recuperar este registro",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            window.location.href = "eliminar.php?cedula=" + cedula;
        } else {
            swal("El registro no ha sido eliminado");
        }
    });
}

// Función para confirmar antes de editar (opcional)
function confirmarEditar(cedula) {
    swal({
        title: "¿Quieres editar este registro?",
        text: "Serás redirigido a la página de edición",
        icon: "info",
        buttons: true,
        dangerMode: false,
    })
    .then((willEdit) => {
        if (willEdit) {
            // Aquí se abre un diálogo de edición, mostrando la cédula del empleado
            swal("Editar empleado", "Cédula: " + cedula, "info")
            .then(() => {
                // Después de que el usuario confirme la edición, se redirige a la página de edición
                window.location.href = "editar.php?cedula=" + cedula;
            });
        } else {
            swal("Edición cancelada");
        }
    });
}



document.getElementById('form').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('soporte_cedula');
    const fileSize = fileInput.files[0].size / 1024 / 1024; // Tamaño en MB
    if (fileSize > 10) { // Limitar a 5MB, por ejemplo
        alert('El archivo debe ser menor a 10MB');
        e.preventDefault();
    }
});
