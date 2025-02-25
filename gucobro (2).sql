-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-02-2025 a las 05:05:41
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gucobro`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignacion_base`
--

CREATE TABLE `asignacion_base` (
  `id_asignacion` int(11) NOT NULL,
  `id_cobrador` int(11) NOT NULL,
  `id_base` int(11) NOT NULL,
  `fecha_asignacion` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asignacion_base`
--

INSERT INTO `asignacion_base` (`id_asignacion`, `id_cobrador`, `id_base`, `fecha_asignacion`) VALUES
(21, 19, 20, '2024-11-27'),
(22, 80, 21, '2025-02-13'),
(23, 83, 22, '2025-02-13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `base`
--

CREATE TABLE `base` (
  `id_base` int(11) NOT NULL,
  `base` varchar(255) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `base`
--

INSERT INTO `base` (`id_base`, `base`, `fecha`) VALUES
(20, '4176', '2024-11-27'),
(21, '2999400000', '2025-02-13'),
(22, '3000000', '2025-02-13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(11) NOT NULL,
  `nombres` varchar(50) NOT NULL,
  `apellidos` varchar(50) NOT NULL,
  `id_genero` int(11) NOT NULL,
  `direccion_casa` varchar(250) NOT NULL,
  `direccion_negocio` varchar(250) NOT NULL,
  `telefono` bigint(250) NOT NULL,
  `cedula` int(25) NOT NULL,
  `id_ruta` int(11) NOT NULL,
  `id_cuenta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `nombres`, `apellidos`, `id_genero`, `direccion_casa`, `direccion_negocio`, `telefono`, `cedula`, `id_ruta`, `id_cuenta`) VALUES
(67, 'Maria Jose', 'Jimenez', 2, 'manila', 'manila', 315163215, 123456852, 1, 1),
(68, 'Tatiana', 'Llanos', 2, 'Villamaria', 'Villamaria', 84951632, 12345685, 1, 1),
(69, 'Johan', 'Gutierrez', 1, 'manila', 'manila', 135162, 123456854, 1, 2),
(70, 'Stiven', 'Rodas', 1, 'manila', 'manila', 21345626, 654612354, 1, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id_configuracion` int(11) NOT NULL,
  `tasa_interes` decimal(5,2) NOT NULL DEFAULT 20.00,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id_configuracion`, `tasa_interes`, `fecha_actualizacion`) VALUES
(1, 20.00, '2024-10-24 00:21:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuadres_diarios`
--

CREATE TABLE `cuadres_diarios` (
  `id_cuadre` int(11) NOT NULL,
  `id_cobrador` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `base_inicial` bigint(250) NOT NULL,
  `total_pagos` decimal(10,2) NOT NULL,
  `total_gastos` decimal(10,2) NOT NULL,
  `total_prestamos` decimal(10,2) NOT NULL,
  `saldo_final` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','aprobado','rechazado') DEFAULT 'pendiente',
  `observaciones` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_cuenta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas`
--

CREATE TABLE `cuentas` (
  `id_cuenta` int(11) NOT NULL,
  `cod_cuenta` varchar(100) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `empresa` varchar(100) NOT NULL,
  `cod_sirc` varchar(100) NOT NULL,
  `fecha_creacion` date NOT NULL DEFAULT curdate(),
  `fecha_vencimiento` date NOT NULL,
  `estado` enum('activa','inactiva') DEFAULT 'activa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cuentas`
--

INSERT INTO `cuentas` (`id_cuenta`, `cod_cuenta`, `nombre`, `empresa`, `cod_sirc`, `fecha_creacion`, `fecha_vencimiento`, `estado`) VALUES
(1, '', 'cuenta001', '', '', '2025-02-18', '2025-02-24', 'inactiva'),
(2, 'CC002', 'cueta002', 'ECONTECT COL SAS', '1234543DFDD', '2025-02-18', '2025-02-26', 'activa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuenta_admin`
--

CREATE TABLE `cuenta_admin` (
  `id_admin` int(11) NOT NULL,
  `id_cuenta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cuenta_admin`
--

INSERT INTO `cuenta_admin` (`id_admin`, `id_cuenta`) VALUES
(87, 1),
(87, 2),
(88, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuota_prestamo`
--

CREATE TABLE `cuota_prestamo` (
  `id_cuota` int(11) NOT NULL,
  `id_prestamo` int(11) NOT NULL,
  `numero_cuota` int(11) NOT NULL,
  `fecha_cuota` date NOT NULL,
  `valor_cuota` decimal(10,2) NOT NULL,
  `estado` enum('pago','abono','no pago','sin cobrar') DEFAULT 'sin cobrar',
  `saldo_pendiente` decimal(10,2) DEFAULT 0.00,
  `fecha_pago` date DEFAULT NULL,
  `id_cuenta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos`
--

CREATE TABLE `gastos` (
  `id_gastos` int(11) NOT NULL,
  `id_rol_user` int(11) NOT NULL,
  `id_tipo_gasto` int(11) NOT NULL,
  `monto` int(200) NOT NULL,
  `estado` enum('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
  `fecha` date NOT NULL,
  `comentarios` varchar(250) NOT NULL,
  `aprobado_por` int(11) DEFAULT NULL,
  `creado_por` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `gastos`
--

INSERT INTO `gastos` (`id_gastos`, `id_rol_user`, `id_tipo_gasto`, `monto`, `estado`, `fecha`, `comentarios`, `aprobado_por`, `creado_por`, `fecha_creacion`) VALUES
(32, 19, 3, 23444, 'aprobado', '0000-00-00', 'werty', NULL, 19, '2024-11-27 21:19:42'),
(33, 19, 2, 23000, 'aprobado', '0000-00-00', '123', NULL, 19, '2024-11-27 21:24:22'),
(34, 19, 4, 500, 'pendiente', '0000-00-00', 'gomita', NULL, 19, '2024-11-27 21:25:59'),
(35, 19, 3, 2000, 'pendiente', '0000-00-00', '12333', NULL, 19, '2024-11-27 21:28:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `genero`
--

CREATE TABLE `genero` (
  `id_genero` int(11) NOT NULL,
  `genero` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `genero`
--

INSERT INTO `genero` (`id_genero`, `genero`) VALUES
(1, 'masculino'),
(2, 'feminino'),
(3, 'otros'),
(4, 'otros');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_base`
--

CREATE TABLE `movimientos_base` (
  `id_movimiento` int(11) NOT NULL,
  `id_asignacion_base` int(11) DEFAULT NULL,
  `tipo_movimiento` enum('préstamo','pago','gasto') DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `movimientos_base`
--

INSERT INTO `movimientos_base` (`id_movimiento`, `id_asignacion_base`, `tipo_movimiento`, `monto`, `fecha`, `descripcion`) VALUES
(107, 21, '', 2000.00, '2024-11-27 16:18:27', 'Desembolso de préstamo al cliente'),
(108, 21, 'pago', 120.00, '2024-11-27 16:18:44', 'Pago de cuota o abono (ID Cuota: 2093)'),
(109, 21, 'gasto', 23444.00, '2024-11-27 16:21:54', 'Aprobación de gasto por administrador.'),
(110, 21, 'gasto', 500.00, '2024-11-27 16:26:50', 'Aprobación de gasto por administrador.'),
(111, 22, '', 300000.00, '2025-02-13 20:50:30', 'Desembolso de préstamo al cliente'),
(112, 22, '', 300000.00, '2025-02-13 21:16:43', 'Desembolso de préstamo al cliente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `id_prestamo` int(11) DEFAULT NULL,
  `id_cuotas` int(11) DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacion` varchar(255) DEFAULT NULL,
  `tipo_pago` enum('pago','no_pago','abono') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id_permisos` int(11) NOT NULL,
  `nombre` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id_permisos`, `nombre`) VALUES
(1, 'Ver Reportes'),
(2, 'Gestionar Usuarios'),
(3, 'Modificar Préstamos'),
(4, 'ingresar clientes'),
(5, 'registrar pagos'),
(6, 'registrar gastos'),
(7, 'verificar cuadre'),
(8, 'acceso total al sistema');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamo`
--

CREATE TABLE `prestamo` (
  `id_prestamo` int(11) NOT NULL,
  `id_rol_user` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `monto_inicial` decimal(10,2) NOT NULL,
  `saldo_actual` decimal(10,2) NOT NULL,
  `interes_total` decimal(10,2) DEFAULT NULL,
  `inicio_fecha` date NOT NULL,
  `Vencimiento_fecha` date NOT NULL,
  `duracion` int(20) NOT NULL,
  `estado` enum('activo','finalizado','penalizado') DEFAULT 'activo',
  `creado_por` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_modificacion` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `id_garantias` int(11) DEFAULT NULL,
  `monto_cuota` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `prestamo`
--
DELIMITER $$
CREATE TRIGGER `before_insert_prestamos` BEFORE INSERT ON `prestamo` FOR EACH ROW BEGIN
    SET NEW.monto_cuota = NEW.monto_inicial * (1 + (
        SELECT tasa_interes FROM configuracion 
        ORDER BY id_configuracion DESC LIMIT 1) / 100
    ) / NEW.duracion;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `rol`) VALUES
(1, 'administrador'),
(2, 'cobrador'),
(18, 'gestor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_permisos`
--

CREATE TABLE `rol_permisos` (
  `id_permisosroles` int(11) NOT NULL,
  `id_permiso` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol_permisos`
--

INSERT INTO `rol_permisos` (`id_permisosroles`, `id_permiso`, `id_rol`) VALUES
(53, 1, 18),
(48, 4, 2),
(49, 5, 2),
(50, 6, 2),
(51, 7, 2),
(52, 8, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_user`
--

CREATE TABLE `rol_user` (
  `id_rol_user` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol_user`
--

INSERT INTO `rol_user` (`id_rol_user`, `id_rol`, `id_user`) VALUES
(1, 1, 12),
(84, 1, 87),
(85, 1, 88),
(19, 2, 22),
(80, 2, 83),
(83, 2, 86),
(87, 2, 90),
(89, 2, 92),
(93, 2, 96),
(94, 2, 97),
(95, 2, 98),
(58, 18, 61),
(82, 18, 85);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ruta`
--

CREATE TABLE `ruta` (
  `id_ruta` int(11) NOT NULL,
  `nombre_ruta` varchar(255) NOT NULL,
  `ciudad` varchar(250) NOT NULL,
  `descripcion` varchar(250) NOT NULL,
  `id_rol_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ruta`
--

INSERT INTO `ruta` (`id_ruta`, `nombre_ruta`, `ciudad`, `descripcion`, `id_rol_user`) VALUES
(1, 'ruta 1', 'pereira', 'armero', 80),
(32, 'ruta transversal', 'cali cielo', 'no se', 58),
(33, 'dorada', 'caldas', 'cualquier cosa', 19),
(35, 'Manizales', 'Manizales', 'Manizales', 83);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_gastos`
--

CREATE TABLE `tipo_gastos` (
  `id_tipo_gasto` int(11) NOT NULL,
  `descripcion` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_gastos`
--

INSERT INTO `tipo_gastos` (`id_tipo_gasto`, `descripcion`) VALUES
(1, 'gasolina'),
(2, 'arriendo'),
(3, 'daños de moto'),
(4, 'otros');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `contacto` varchar(50) NOT NULL,
  `cedula` bigint(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `ultimo_login` timestamp NULL DEFAULT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `id_cuenta` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`id_user`, `nombre`, `apellido`, `contacto`, `cedula`, `password`, `ultimo_login`, `estado`, `id_cuenta`) VALUES
(12, 'johan', 'gutierrez', '3128450864', 1106770339, '$2y$10$Tb87er5efP9Fj09CCscv5.SVo17U9q8MT5hL1ccQlgr8e9qlNkL72', '2024-11-27 21:47:17', 'inactivo', NULL),
(22, 'diego alejandro ', 'monroy', '3120875983', 1106770338, '$2y$10$GGPn3Nxwu6/Fd1.0llxjQ.acNuJK30v5ZduW5DMhIEpQd1j5Tflx2', '2024-11-27 21:13:26', 'activo', NULL),
(61, 'santiago', 'figueroa', '3134930094', 1106770330, '$2y$10$o6LyT1ogMIwVib6GvBmXTuUV5fiDb33Dktr1s5WheRK6WUsXYwnQ6', '2024-11-25 16:26:46', 'activo', NULL),
(83, 'alberto', 'ingeniero', '12345678', 1234567890, '$2y$10$PtYi0YHyMIcrslegihxdZuUTrLUKlvH.XOOBdT.xHWMxikHQQjz9K', NULL, 'activo', NULL),
(85, 'Santiago', 'Figueroa', '3134369400', 1106770953, '$2y$10$jWvOHcJWaFgroOv4U5E8yOsOFHMbORtAqZClyn2QHvfTM2a0zo4Ia', '2025-02-25 02:21:48', 'activo', NULL),
(86, 'Rodas', 'Rodas', '230', 213132321, '$2y$10$CMxp77EfU1hNyIqFFy0OJefRb/tBCsQ/LJMdgZvcZ2NHKRZbbbHU6', '2025-02-19 00:52:27', 'activo', NULL),
(87, 'Mariana', 'Ortiz', '3165452311', 123432546, '$2y$10$ttrm8jSIhi/vAgG.HCaue.RNTINEuDXLXuTFSourS8bKdKKxwmTi.', '2025-02-25 03:58:04', 'activo', NULL),
(88, 'Mafe', 'Oliveros', '2315614653', 123456258, '$2y$10$GzSewEwB5UUsA0QsEB0/qepzx0omi4pGnaadrUhk5LTmr7IzPBTgO', NULL, 'activo', NULL),
(90, 'Sandra', 'Figueroa', '5421353', 45632541, '$2y$10$0msCmbrhp0WhJUgTYpKU6uxTi.jw7o545MZJQvbLv8ghQJT/PPiEq', '2025-02-25 03:55:22', 'activo', 1),
(92, 'Maira', 'Figueroa', '1234564', 65416523, '$2y$10$DrEbgxXstzCmaZzy9G4I.Om.i286MSkVzn4EyDZDR1FwcZ80IG7n2', '2025-02-25 02:22:53', 'activo', 1),
(96, 'Sebastian', 'Sossa', '981654135', 654147852, '$2y$10$gyjquK.FzBeyQOMlKTSpleX3afAGe.joYdzxbO5He4qiqAei5d1Ii', NULL, 'activo', 1),
(97, 'Valentina', 'Mor', '85463156', 147852369, '$2y$10$3WuDIrcM9aY5K9oPUbO0F.JHi6Mc9Q.2fzKN45jMuNJjdhbTFPwzC', NULL, 'activo', 2),
(98, 'Juan Esteban', 'Sossa', '7894654', 654852159, '$2y$10$Voo2Cp4rL17yeIB0ra7BBeSlSDwARp0cloHF9x/I4cmwt/Fq0l/62', NULL, 'activo', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asignacion_base`
--
ALTER TABLE `asignacion_base`
  ADD PRIMARY KEY (`id_asignacion`),
  ADD KEY `id_cobrador` (`id_cobrador`),
  ADD KEY `id_base` (`id_base`);

--
-- Indices de la tabla `base`
--
ALTER TABLE `base`
  ADD PRIMARY KEY (`id_base`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`),
  ADD KEY `id_genero` (`id_genero`),
  ADD KEY `id_ruta` (`id_ruta`),
  ADD KEY `id_cuenta` (`id_cuenta`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id_configuracion`);

--
-- Indices de la tabla `cuadres_diarios`
--
ALTER TABLE `cuadres_diarios`
  ADD PRIMARY KEY (`id_cuadre`),
  ADD KEY `id_cobrador` (`id_cobrador`),
  ADD KEY `id_cuenta` (`id_cuenta`);

--
-- Indices de la tabla `cuentas`
--
ALTER TABLE `cuentas`
  ADD PRIMARY KEY (`id_cuenta`);

--
-- Indices de la tabla `cuenta_admin`
--
ALTER TABLE `cuenta_admin`
  ADD PRIMARY KEY (`id_admin`,`id_cuenta`),
  ADD KEY `id_cuenta` (`id_cuenta`);

--
-- Indices de la tabla `cuota_prestamo`
--
ALTER TABLE `cuota_prestamo`
  ADD PRIMARY KEY (`id_cuota`),
  ADD KEY `id_prestamo` (`id_prestamo`),
  ADD KEY `id_cuenta` (`id_cuenta`);

--
-- Indices de la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD PRIMARY KEY (`id_gastos`),
  ADD KEY `id_rol_user` (`id_rol_user`,`id_tipo_gasto`),
  ADD KEY `id_tipo_gasto` (`id_tipo_gasto`),
  ADD KEY `aprobado_por` (`aprobado_por`),
  ADD KEY `creado_por` (`creado_por`);

--
-- Indices de la tabla `genero`
--
ALTER TABLE `genero`
  ADD PRIMARY KEY (`id_genero`);

--
-- Indices de la tabla `movimientos_base`
--
ALTER TABLE `movimientos_base`
  ADD PRIMARY KEY (`id_movimiento`),
  ADD KEY `id_asignacion_base` (`id_asignacion_base`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_cuotas` (`id_cuotas`),
  ADD KEY `idx_pagos_prestamo` (`id_prestamo`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id_permisos`);

--
-- Indices de la tabla `prestamo`
--
ALTER TABLE `prestamo`
  ADD PRIMARY KEY (`id_prestamo`),
  ADD KEY `id_rol_user` (`id_rol_user`,`id_cliente`),
  ADD KEY `id_garantias` (`id_garantias`),
  ADD KEY `idx_prestamo_cliente` (`id_cliente`),
  ADD KEY `creado_por` (`creado_por`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  ADD PRIMARY KEY (`id_permisosroles`),
  ADD KEY `id_permisos` (`id_permiso`,`id_rol`),
  ADD KEY `id_roles` (`id_rol`),
  ADD KEY `id_rol` (`id_rol`),
  ADD KEY `id_permiso` (`id_permiso`);

--
-- Indices de la tabla `rol_user`
--
ALTER TABLE `rol_user`
  ADD PRIMARY KEY (`id_rol_user`),
  ADD KEY `id_rol` (`id_rol`,`id_user`),
  ADD KEY `id_user` (`id_user`);

--
-- Indices de la tabla `ruta`
--
ALTER TABLE `ruta`
  ADD PRIMARY KEY (`id_ruta`),
  ADD KEY `id_cobrador` (`id_rol_user`),
  ADD KEY `idx_rutas_cobrador` (`id_rol_user`);

--
-- Indices de la tabla `tipo_gastos`
--
ALTER TABLE `tipo_gastos`
  ADD PRIMARY KEY (`id_tipo_gasto`);

--
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `id_cuenta` (`id_cuenta`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asignacion_base`
--
ALTER TABLE `asignacion_base`
  MODIFY `id_asignacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `base`
--
ALTER TABLE `base`
  MODIFY `id_base` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id_configuracion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `cuadres_diarios`
--
ALTER TABLE `cuadres_diarios`
  MODIFY `id_cuadre` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `cuentas`
--
ALTER TABLE `cuentas`
  MODIFY `id_cuenta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `cuota_prestamo`
--
ALTER TABLE `cuota_prestamo`
  MODIFY `id_cuota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2161;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `id_gastos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `genero`
--
ALTER TABLE `genero`
  MODIFY `id_genero` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `movimientos_base`
--
ALTER TABLE `movimientos_base`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id_permisos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `prestamo`
--
ALTER TABLE `prestamo`
  MODIFY `id_prestamo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  MODIFY `id_permisosroles` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT de la tabla `rol_user`
--
ALTER TABLE `rol_user`
  MODIFY `id_rol_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT de la tabla `ruta`
--
ALTER TABLE `ruta`
  MODIFY `id_ruta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `tipo_gastos`
--
ALTER TABLE `tipo_gastos`
  MODIFY `id_tipo_gasto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asignacion_base`
--
ALTER TABLE `asignacion_base`
  ADD CONSTRAINT `asignacion_base_ibfk_1` FOREIGN KEY (`id_cobrador`) REFERENCES `rol_user` (`id_rol_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `asignacion_base_ibfk_2` FOREIGN KEY (`id_base`) REFERENCES `base` (`id_base`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `cliente_ibfk_2` FOREIGN KEY (`id_genero`) REFERENCES `genero` (`id_genero`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cliente_ibfk_3` FOREIGN KEY (`id_ruta`) REFERENCES `ruta` (`id_ruta`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cliente_ibfk_4` FOREIGN KEY (`id_cuenta`) REFERENCES `cuentas` (`id_cuenta`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cuadres_diarios`
--
ALTER TABLE `cuadres_diarios`
  ADD CONSTRAINT `cuadres_diarios_ibfk_1` FOREIGN KEY (`id_cobrador`) REFERENCES `rol_user` (`id_rol_user`),
  ADD CONSTRAINT `cuadres_diarios_ibfk_2` FOREIGN KEY (`id_cuenta`) REFERENCES `cuentas` (`id_cuenta`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cuenta_admin`
--
ALTER TABLE `cuenta_admin`
  ADD CONSTRAINT `cuenta_admin_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `cuenta_admin_ibfk_2` FOREIGN KEY (`id_cuenta`) REFERENCES `cuentas` (`id_cuenta`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cuota_prestamo`
--
ALTER TABLE `cuota_prestamo`
  ADD CONSTRAINT `cuota_prestamo_ibfk_1` FOREIGN KEY (`id_prestamo`) REFERENCES `prestamo` (`id_prestamo`),
  ADD CONSTRAINT `cuota_prestamo_ibfk_2` FOREIGN KEY (`id_cuenta`) REFERENCES `cuentas` (`id_cuenta`) ON DELETE CASCADE;

--
-- Filtros para la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD CONSTRAINT `gastos_ibfk_1` FOREIGN KEY (`id_rol_user`) REFERENCES `rol_user` (`id_rol_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gastos_ibfk_2` FOREIGN KEY (`id_tipo_gasto`) REFERENCES `tipo_gastos` (`id_tipo_gasto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gastos_ibfk_3` FOREIGN KEY (`aprobado_por`) REFERENCES `rol_user` (`id_rol_user`) ON DELETE SET NULL,
  ADD CONSTRAINT `gastos_ibfk_4` FOREIGN KEY (`creado_por`) REFERENCES `rol_user` (`id_rol_user`) ON DELETE SET NULL;

--
-- Filtros para la tabla `movimientos_base`
--
ALTER TABLE `movimientos_base`
  ADD CONSTRAINT `movimientos_base_ibfk_1` FOREIGN KEY (`id_asignacion_base`) REFERENCES `asignacion_base` (`id_asignacion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_prestamo`) REFERENCES `prestamo` (`id_prestamo`),
  ADD CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`id_cuotas`) REFERENCES `cuota_prestamo` (`id_cuota`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `prestamo`
--
ALTER TABLE `prestamo`
  ADD CONSTRAINT `prestamo_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prestamo_ibfk_2` FOREIGN KEY (`id_rol_user`) REFERENCES `rol_user` (`id_rol_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prestamo_ibfk_5` FOREIGN KEY (`creado_por`) REFERENCES `rol_user` (`id_rol_user`) ON DELETE SET NULL;

--
-- Filtros para la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  ADD CONSTRAINT `rol_permisos_ibfk_1` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id_permisos`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rol_permisos_ibfk_2` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `rol_user`
--
ALTER TABLE `rol_user`
  ADD CONSTRAINT `rol_user_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rol_user_ibfk_2` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `ruta`
--
ALTER TABLE `ruta`
  ADD CONSTRAINT `ruta_ibfk_1` FOREIGN KEY (`id_rol_user`) REFERENCES `rol_user` (`id_rol_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`id_cuenta`) REFERENCES `cuentas` (`id_cuenta`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
