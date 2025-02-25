<?php
// Obtener el archivo actual para resaltar el enlace activo
$current_page = basename($_SERVER['PHP_SELF']);
$base_url = $_SERVER['DOCUMENT_ROOT'] . "/gucobrobr";  // Ruta absoluta a la raíz del proyecto
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/estilo_barra.css">
    <title>Gucobro Dashboard</title>
    <style>
        .main-content {
            margin-top: 80px;
        }

        /* Navbar estilos */
        .navbar {
            background-color: #333333; /* Fondo oscuro para el navbar */
        }

        .navbar-dark .navbar-nav .nav-link {
            color: #ffffff; /* Texto blanco para los enlaces */
        }

        .navbar-dark .navbar-nav .nav-link:hover {
            color: #198754; /* Resaltar en verde en hover */
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffffff; /* Texto blanco para la marca */
        }

        .navbar-brand .logo {
            max-height: 40px;
            margin-right: 10px;
        }

        .navbar-nav .nav-item {
            margin: 0 10px;
        }

        /* Enlace activo */
        .active-link {
            color: #198754 !important; /* Verde para el enlace activo */
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header (Barra de Navegación) -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="/gucobrobr/vistas/dashboard2.php">
                <img src="../assets/img/image.png" alt="Logo Gucobro" class="logo"> Gucobro
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'clientesco.php') ? 'active-link' : ''; ?>" href="/gucobrobr/vistas/clientesco.php"><i class="fas fa-address-book"></i> Clientes</a>
                    </li>
                    <li class="nav-item">

                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'pagosco.php') ? 'active-link' : ''; ?>" href="/gucobrobr/vistas/pagosco.php"><i class="fas fa-money-bill-wave"></i> Pagos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'gastos.php') ? 'active-link' : ''; ?>" href="/gucobrobr/vistas/gastos/solicitar_gastos.php"><i class="fas fa-dollar-sign"></i> Gastos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'cuadre.php') ? 'active-link' : ''; ?>" href="/gucobrobr/vistas/cuadre.php"><i class="fas fa-file-invoice-dollar"></i> Cuadre</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'perfilco.php') ? 'active-link' : ''; ?>" href="/gucobrobr/vistas/perfilco.php"><i class="fas fa-user-alt"></i> Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'cerrarsesion.php') ? 'active-link' : ''; ?>" href="/gucobrobr/vistas/login/cerrarsesion.php"><i class="fas fa-sign-out-alt"></i> Salir</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="main-content">

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
