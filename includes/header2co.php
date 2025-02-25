<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .navbar-custom {
            background-color: #2c2f33;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .navbar-custom .navbar-brand {
            color: #fff;
            font-weight: bold;
        }
        .navbar-custom .navbar-brand:hover {
            color: #ddd;
        }
        .back-button {
            color: #fff;
            font-size: 18px;
            margin-right: 10px;
        }
        .back-button:hover {
            color: #ddd;
        }
        .navbar-custom .nav-item a {
            color: #fff;
            font-size: 16px;
        }
        .navbar-custom .nav-item a:hover {
            color: #ddd;
        }
    </style>
    <title>Subpágina - Gucobro</title>
</head>
<body>

    <!-- Header personalizado con botón de volver -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container-fluid">
            <!-- Botón de volver -->
            <a href="javascript:history.back()" class="back-button">
                <i class="fas fa-arrow-left"></i>
            </a>
            <!-- Logo y nombre -->
            <a class="navbar-brand" href="#">Gucobro</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../dashboard2.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../perfilco.php">Perfil</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main class="container mt-5 pt-4">


    <!-- Scripts de Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
