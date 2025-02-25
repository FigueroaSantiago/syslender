<?php
// Obtener el archivo actual para resaltar el enlace activo
$current_page = basename($_SERVER['PHP_SELF']);
$base_url = $_SERVER['DOCUMENT_ROOT'] . "/gucobrobr";  // Ruta absoluta a la raíz del proyecto
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
        /* Estilos para mantener el diseño consistente */
        .main-content {
            margin-top: 80px;
        }

        @media (min-width: 992px) {
            .dropdown:hover .dropdown-menu {
                display: block;
            }
        }

        /* Navbar estilos */
        .navbar {
            background: linear-gradient(to left, #5271FF, #1F2A5C);
            /* Fondo oscuro para el navbar */
        }

        .navbar-dark .navbar-nav .nav-link {
            color: #ffffff;
            /* Texto blanco para los enlaces */
        }

        /* Hover: un tono azul claro */
        .navbar-nav .nav-item .nav-link:hover {
            color:rgb(57, 58, 61);
            /* Azul claro para destacar */
        }

        .navbar-brand {
            font-size: 10rem;
            font-weight: bold;
            color: #ffffff;
            /* Texto blanco para la marca */
        }

        .navbar-brand .logo {
            max-height: 40px;
            margin-right: 10px;
        }

        .navbar-nav .nav-item {
            margin: 0 10px;
        }

        .navbar-nav .nav-item .nav-link {
            color: white;
            /* Color normal de los enlaces */
            transition: color 0.3s ease, background 0.3s ease;
        }

        /* Enlace activo */
        .active-link {
            color:rgb(0, 0, 0) !important;
            /* Verde para el enlace activo */
            font-weight: bold;
        }

        .dropdown-menu {
            background-color: #f1f1f1;
            /* Fondo gris claro para el menú desplegable */
        }

        .dropdown-item:hover {
            background-color: #198754;
            /* Fondo verde claro para hover */
            color: #ffffff;
        }

        .dropdown-item.active-link {
            color: #ffffff;
            /* Color blanco para el texto activo en el dropdown */
            /* Fondo verde para el item activo */
        }

        /* Estilos responsive */
        @media (max-width: 992px) {
            .navbar-toggler {
                display: block;
            }
        }
    </style>
</head>

<body>
    <!-- Header (Barra de Navegación) -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="/gucobrobr/vistas/dashboard.php">
                <img src="../assets/img/logo-syslender-copy2.png" alt="Logo Gucobro" class="logo"> Syslender
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <!-- Gestión de Usuarios y Roles (Solo para Admin) -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo ($current_page == 'usuarios.php' || $current_page == 'roles.php' || $current_page == 'rutas.php' || $current_page == 'clientes.php') ? 'active-link' : ''; ?>" href="#" id="usuariosDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-users-cog"></i> Gestión Usuarios
                        </a>
                        <div class="dropdown-menu" aria-labelledby="usuariosDropdown">
                            <a class="dropdown-item <?php echo ($current_page == 'usuarios.php') ? 'active-link' : ''; ?>" href="/syslender/vistas/usuarios.php"><i class="fas fa-user"></i> Usuarios</a>
                            <a class="dropdown-item <?php echo ($current_page == 'roles.php') ? 'active-link' : ''; ?>" href="/syslender/vistas/roles.php"><i class="fas fa-user-tag"></i> Roles</a>
                            <a class="dropdown-item <?php echo ($current_page == 'rutas.php') ? 'active-link' : ''; ?>" href="/syslender/vistas/rutas.php"><i class="fas fa-route"></i> Rutas</a>
                            <a class="dropdown-item <?php echo ($current_page == 'clientes.php') ? 'active-link' : ''; ?>" href="/syslender/vistas/clientes.php"><i class="fas fa-address-book"></i> Clientes</a>
                        </div>
                    </li>


                    <!-- Gestión de Préstamos -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo ($current_page == 'prestamos.php' || $current_page == 'prestamos1.php' || $current_page == 'ver_prestamos.php' || $current_page == 'garantias.php' || $current_page == 'historial.php') ? 'active-link' : ''; ?>" href="#" id="prestamosDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-hand-holding-usd"></i> Préstamos
                        </a>
                        <div class="dropdown-menu" aria-labelledby="prestamosDropdown">
                            <a class="dropdown-item <?php echo ($current_page == 'prestamos.php') ? 'active-link' : ''; ?>" href="/syslender/vistas/prestamos.php"><i class="fas fa-file-invoice-dollar"></i> Gestión Préstamos</a>
                            <!--  <a class="dropdown-item <?php echo ($current_page == 'prestamos1.php') ? 'active-link' : ''; ?>" href="/syslender/vistas/prestamo/prestamos1.php"><i class="fas fa-users"></i> Clientes y Préstamos</a>    Gestión de Préstamos -->
                            <a class="dropdown-item <?php echo ($current_page == 'historial.php') ? 'active-link' : ''; ?>" href="/syslender/vistas/historial.php"><i class="fas fa-history"></i> Historial Préstamos</a>
                        </div>
                    </li>

                    <!-- Reportes y Finanzas -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo ($current_page == 'pagos.php' || $current_page == 'cuadre.php') ? 'active-link' : ''; ?>" href="#" id="finanzasDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-chart-line"></i> Pagos
                        </a>
                        <div class="dropdown-menu" aria-labelledby="finanzasDropdown">
                            <a class="dropdown-item <?php echo ($current_page == 'pagos.php') ? 'active-link' : ''; ?>" href="/syslender/vistas/pagos.php"><i class="fas fa-money-bill-wave"></i> Pagos</a>

                            <a class="dropdown-item <?php echo ($current_page == 'cuadre.php') ? 'active-link' : ''; ?>" href="/syslender/vistas/cuadre_diario.php"><i class="fas fa-file-invoice-dollar"></i> Cuadre</a>

                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo ($current_page == 'reportes.php' || $current_page == 'reportespagos.php' || $current_page == 'reportesgastos.php') ? 'active-link' : ''; ?>" href="#" id="finanzasDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-chart-line"></i> Reportes
                        </a>
                        <div class="dropdown-menu" aria-labelledby="finanzasDropdown">

                            <a class="dropdown-item <?php echo ($current_page == 'reportes.php') ? 'active-link' : ''; ?>" href="/syslender/vistas/reportes.php"><i class="fas fa-chart-bar"></i> Reportes Préstamos</a>
                            <a class="dropdown-item <?php echo ($current_page == 'reportespagos.php') ? 'active-link' : ''; ?>" href="/syslender/vistas/reportespagos.php"><i class="fas fa-chart-pie"></i> Reportes Pagos</a>
                            <a class="dropdown-item <?php echo ($current_page == 'reportesgastos.php') ? 'active-link' : ''; ?>" href="/syslender/vistas/reportesgastos.php"><i class="fas fa-file-alt"></i> Reportes Gastos</a>

                        </div>
                        </>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo ($current_page == 'perfil.php' || $current_page == 'base.php' || $current_page == 'config.php') ? 'active-link' : ''; ?>" href="#" id="configuracionDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-cog"></i> Configuración
                        </a>
                        <div class="dropdown-menu" aria-labelledby="configuracionDropdown">
                            <a class="dropdown-item <?php echo ($current_page == 'perfil.php') ? 'active-link' : ''; ?>" href="/syslender/vistas/perfil.php"><i class="fas fa-user-alt"></i> Perfil</a>
                            <a class="dropdown-item <?php echo ($current_page == 'base.php') ? 'active-link' : ''; ?>" href="/syslender/vistas/base.php"><i class="fas fa-database"></i> Bases</a>
                            <a class="dropdown-item <?php echo ($current_page == 'config.php') ? 'active-link' : ''; ?>" href="/syslender/vistas/config.php"><i class="fas fa-cogs"></i> Configuraciónes</a>
                        </div>
                    </li>

                    <!-- Cerrar Sesión -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'cerrarsesion.php') ? 'active-link' : ''; ?>" href="/syslender/vistas/login/cerrarsesion.php"><i class="fas fa-sign-out-alt"></i> Salir</a>
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