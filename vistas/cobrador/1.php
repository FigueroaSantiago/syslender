<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Base a Cobrador</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Asignar Base a un Cobrador</h2>
        <form id="assignBaseForm" action="/assign-base" method="POST">
            <div class="form-group">
                <label for="cobrador">Seleccionar Cobrador:</label>
                <select id="cobrador" name="cobrador" class="form-control" required>
                    <!-- Opciones cargadas dinámicamente -->
                </select>
            </div>
            <div class="form-group">
                <label for="base">Seleccionar Base:</label>
                <select id="base" name="base" class="form-control" required>
                    <!-- Opciones cargadas dinámicamente -->
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Asignar Base</button>
        </form>
    </div>

    <script>
    $(document).ready(function() {
        // Cargar opciones dinámicamente
        function loadOptions(url, selectId) {
            $.getJSON(url, function(data) {
                var options = '';
                $.each(data, function(key, value) {
                    options += '<option value="' + value.id + '">' + value.nombre + '</option>';
                });
                $(selectId).html(options);
            });
        }
        
        // URL de las APIs (ajustar según tu configuración)
        loadOptions('/api/cobradores', '#cobrador');
        loadOptions('/api/bases', '#base');
    });
    </script>
</body>
</html>
