-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-02-2025 a las 23:08:56
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
(22, '29999718000', '2025-02-25');

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
(71, 'Maria José', 'Jimenez', 2, 'manila', 'manila', 321654852, 2147483647, 35, 1),
(72, 'asdas', 'asdas', 1, 'asdas', 'asda', 213123213, 12343212, 35, 1);

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
(1, '', 'cuenta001', '', '', '2025-02-18', '2025-02-28', 'activa'),
(2, 'CC002', 'cueta002', 'ECONTECT COL SAS', '1234543DFDD', '2025-02-18', '2025-03-15', 'activa'),
(5, 'CC003', 'cuenta003', 'Clinica CLEO', '46sdfasd564', '2025-02-26', '2025-02-28', 'activa'),
(6, 'CC003', 'cuenta003', 'Clinica CLEO', '46sdfasd564', '2025-02-26', '2025-02-28', 'activa'),
(7, 'cuenta003', 'cuenta003', 'cuenta003', 'cuenta003', '2025-02-26', '2025-02-28', 'activa'),
(8, 'cuenta005', 'cuenta005', 'cuenta003', 'cuenta003', '2025-02-26', '2025-02-28', 'activa'),
(9, 'cuenta005', 'cuenta005', 'cuenta005', 'cuenta005', '2025-02-26', '2025-02-28', 'activa'),
(10, 'cuenta006', 'cuenta006', 'cuenta006', 'cuenta006', '2025-02-26', '2025-03-26', 'activa'),
(11, 'cuenta007', 'cuenta007', 'cuenta007', 'cuenta007', '2025-02-26', '2025-03-01', 'activa'),
(12, 'cuentadeTito', 'cuentadeTito', 'cuentadeTito', 'cuentadeTito', '2025-02-26', '2025-03-01', 'activa'),
(13, 'CuentadeLosCARNALES', 'CuentadeLosCARNALES', 'CuentadeLosCARNALES', 'CuentadeLosCARNALES', '2025-02-26', '2025-03-08', 'activa'),
(14, 'CuentadeNVISTA', 'CuentadeNVISTA', 'CuentadeNVISTA', 'CuentadeNVISTA', '2025-02-26', '2025-02-27', 'activa'),
(15, 'CuentadeNVISTA1', 'CuentadeNVISTA1', 'CuentadeNVISTA1', 'CuentadeNVISTA1', '2025-02-26', '2025-02-28', 'activa'),
(16, 'CuentaSelect2', 'CuentaSelect2', 'CuentaSelect2', 'CuentaSelect2', '2025-02-26', '2025-03-01', 'activa');

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
(88, 2),
(118, 8),
(119, 8),
(120, 1),
(121, 8),
(125, 8),
(134, 13),
(134, 16);

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

--
-- Volcado de datos para la tabla `cuota_prestamo`
--

INSERT INTO `cuota_prestamo` (`id_cuota`, `id_prestamo`, `numero_cuota`, `fecha_cuota`, `valor_cuota`, `estado`, `saldo_pendiente`, `fecha_pago`, `id_cuenta`) VALUES
(2172, 151, 1, '2025-02-26', 0.00, 'pago', 0.00, NULL, 1),
(2173, 151, 2, '2025-02-27', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2174, 151, 3, '2025-02-28', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2175, 151, 4, '2025-03-01', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2176, 151, 5, '2025-03-03', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2177, 151, 6, '2025-03-04', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2178, 151, 7, '2025-03-05', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2179, 151, 8, '2025-03-06', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2180, 151, 9, '2025-03-07', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2181, 151, 10, '2025-03-08', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2182, 151, 11, '2025-03-10', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2183, 151, 12, '2025-03-11', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2184, 151, 13, '2025-03-12', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2185, 151, 14, '2025-03-13', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2186, 151, 15, '2025-03-14', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2187, 151, 16, '2025-03-15', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2188, 151, 17, '2025-03-17', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2189, 151, 18, '2025-03-18', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2190, 151, 19, '2025-03-19', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2191, 151, 20, '2025-03-20', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2192, 152, 1, '2025-02-26', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2193, 152, 2, '2025-02-27', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2194, 152, 3, '2025-02-28', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2195, 152, 4, '2025-03-01', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2196, 152, 5, '2025-03-03', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2197, 152, 6, '2025-03-04', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2198, 152, 7, '2025-03-05', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2199, 152, 8, '2025-03-06', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2200, 152, 9, '2025-03-07', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2201, 152, 10, '2025-03-08', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2202, 152, 11, '2025-03-10', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2203, 152, 12, '2025-03-11', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2204, 152, 13, '2025-03-12', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2205, 152, 14, '2025-03-13', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2206, 152, 15, '2025-03-14', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2207, 152, 16, '2025-03-15', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2208, 152, 17, '2025-03-17', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2209, 152, 18, '2025-03-18', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2210, 152, 19, '2025-03-19', 18000.00, 'sin cobrar', 0.00, NULL, 1),
(2211, 152, 20, '2025-03-20', 18000.00, 'sin cobrar', 0.00, NULL, 1);

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
(4, 'otros'),
(5, 'trans');

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
(131, 23, '', 300000.00, '2025-02-25 16:43:50', 'Desembolso de préstamo al cliente'),
(132, 23, '', 300000.00, '2025-02-25 17:07:37', 'Desembolso de préstamo al cliente'),
(133, 23, 'pago', 18000.00, '2025-02-25 17:22:00', 'Pago de cuota o abono (ID Cuota: 2172)');

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

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id_pago`, `id_prestamo`, `id_cuotas`, `monto`, `fecha`, `observacion`, `tipo_pago`) VALUES
(241, 151, 2172, 18000.00, '2025-02-25 22:22:00', '', 'pago');

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
-- Volcado de datos para la tabla `prestamo`
--

INSERT INTO `prestamo` (`id_prestamo`, `id_rol_user`, `id_cliente`, `monto_inicial`, `saldo_actual`, `interes_total`, `inicio_fecha`, `Vencimiento_fecha`, `duracion`, `estado`, `creado_por`, `fecha_creacion`, `fecha_modificacion`, `id_garantias`, `monto_cuota`) VALUES
(151, 87, 71, 300000.00, 342000.00, 60000.00, '2025-02-25', '2025-03-21', 20, 'activo', 87, '2025-02-25 21:43:50', '2025-02-25 22:22:00', NULL, 18000.00),
(152, 84, 71, 300000.00, 360000.00, 60000.00, '2025-02-25', '2025-03-21', 20, 'activo', 84, '2025-02-25 22:07:37', NULL, NULL, 18000.00);

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
(84, 1, 87),
(85, 1, 88),
(96, 1, 99),
(111, 1, 114),
(112, 1, 115),
(113, 1, 116),
(114, 1, 117),
(115, 1, 118),
(116, 1, 119),
(117, 1, 120),
(118, 1, 121),
(122, 1, 125),
(128, 1, 131),
(129, 1, 132),
(130, 1, 133),
(131, 1, 134),
(83, 2, 86),
(87, 2, 90),
(89, 2, 92),
(93, 2, 96),
(94, 2, 97),
(95, 2, 98),
(97, 2, 100),
(98, 2, 101),
(119, 2, 122),
(120, 2, 123),
(121, 2, 124),
(123, 2, 126),
(124, 2, 127),
(125, 2, 128),
(126, 2, 129),
(127, 2, 130),
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
(85, 'Santiago', 'Figueroa', '3134369400', 1106770953, '$2y$10$jWvOHcJWaFgroOv4U5E8yOsOFHMbORtAqZClyn2QHvfTM2a0zo4Ia', '2025-02-26 20:29:52', 'activo', NULL),
(86, 'Rodas', 'Rodas', '230', 213132321, '$2y$10$CMxp77EfU1hNyIqFFy0OJefRb/tBCsQ/LJMdgZvcZ2NHKRZbbbHU6', '2025-02-25 22:20:22', 'activo', NULL),
(87, 'Mariana', 'Ortiz', '3165452311', 123432546, '$2y$10$ttrm8jSIhi/vAgG.HCaue.RNTINEuDXLXuTFSourS8bKdKKxwmTi.', '2025-02-26 16:39:54', 'activo', NULL),
(88, 'Mafe', 'Oliveros', '2315614653', 123456258, '$2y$10$GzSewEwB5UUsA0QsEB0/qepzx0omi4pGnaadrUhk5LTmr7IzPBTgO', NULL, 'activo', NULL),
(90, 'Sandra', 'Figueroa', '5421353', 45632541, '$2y$10$0msCmbrhp0WhJUgTYpKU6uxTi.jw7o545MZJQvbLv8ghQJT/PPiEq', '2025-02-25 21:52:23', 'activo', 1),
(92, 'Maira', 'Figueroa', '1234564', 65416523, '$2y$10$DrEbgxXstzCmaZzy9G4I.Om.i286MSkVzn4EyDZDR1FwcZ80IG7n2', '2025-02-25 02:22:53', 'activo', 1),
(96, 'Sebastian', 'Sossa', '981654135', 654147852, '$2y$10$gyjquK.FzBeyQOMlKTSpleX3afAGe.joYdzxbO5He4qiqAei5d1Ii', '2025-02-25 19:46:09', 'activo', 1),
(97, 'Valentina', 'Mor', '85463156', 147852369, '$2y$10$3WuDIrcM9aY5K9oPUbO0F.JHi6Mc9Q.2fzKN45jMuNJjdhbTFPwzC', '2025-02-25 13:48:39', 'activo', 2),
(98, 'Juan Esteban', 'Sossa', '7894654', 654852159, '$2y$10$Voo2Cp4rL17yeIB0ra7BBeSlSDwARp0cloHF9x/I4cmwt/Fq0l/62', NULL, 'activo', 1),
(99, 'JohanG', 'GG', '12323145', 1234569512, '$2y$10$cIS.Wqindy4ARhoy04VhGeYdJsTQnxICI68wL0NeyXgCDYaSn9LZm', NULL, 'activo', NULL),
(100, 'JOHANCG', 'CG', '32146789', 951357456, '$2y$10$BI/NVlIpmFTHzeDHT97yIO2eFEGs80vWY3laeUsBRDqTwcJVQnFCW', NULL, 'activo', 1),
(101, 'Stiven', 'Rodas Rodas', '321654852', 753159654, '$2y$10$T7bLHeSdDOuqrCuV46GUIe7NdRiVDc1G4izZyr4ssk3Zg6pBCmUlq', NULL, 'activo', 1),
(114, 'Admin1', 'Admin1', '456789321', 951357486, '$2y$10$a0oE0F489AhZ0OjUvtiAM.YINzqh3H.gEtDP/aCuZ8wqHunrSx1Be', NULL, 'activo', NULL),
(115, 'Admin2', 'Admin2', '654852159', 951265479, '$2y$10$SbZAvlnQAAbHTTFdmKBoN.Fr0Xe3WuKl4PajF.5wjJvoogRzJ8B/W', NULL, 'activo', NULL),
(116, 'Admin3', 'ADMIN3', '321987864', 654951749, '$2y$10$kQXtjtmzV3Fx4y3tXdEFEeXCh9Q15lPFgL2/s9xFDev7HcAXO4aeW', NULL, 'activo', NULL),
(117, 'Admin4', 'Admin4', '654851649', 351694875, '$2y$10$JvUn2s.V399d.XNGeh201O9ATogJgNYJEmSLg/wIzs0BCqwVmJRQi', NULL, 'activo', NULL),
(118, 'Admin5', 'Admin5', '654965481', 951753846, '$2y$10$bnlA7ripM72USVx96n.FuefDwDVgwNBv3cPpLM0WcGXQ92g8aowxy', NULL, 'activo', NULL),
(119, 'Admin6', 'Admin6', '456846913', 654963486, '$2y$10$SDn77yHb/CeitP6diYxjEe/uFvx1fi4wYgeyW/Kqmf2dHBzB60UQe', NULL, 'activo', NULL),
(120, 'Admin7', 'Admin7', '987159874', 674913854, '$2y$10$kK/fjZEgSrZBFLu1f9h7uu/mAzjzp/MI0doVWecXH2d76B.c9Mqwe', NULL, 'activo', NULL),
(121, 'Fernando', 'Burbano', '9468264613', 9768425234, '$2y$10$IOOc24/94q.go1FVaHVTEO0OnXrcDpGylzBWs/N.qxf7czpfB3YnC', '2025-02-26 16:09:26', 'activo', NULL),
(122, 'Anthony', 'Zambrano', '65497549', 95176842, '$2y$10$404vKijt8sD.e37eP0Z6JOJRGciO1qsj4rjvqwWknHa2HNCGGM6BO', NULL, 'activo', 8),
(123, 'Shaggy', 'G', '654789123', 951768426, '$2y$10$nakebENWLt2oEJsf3ZZsb.gIDOv8lXlmMNe1EocvdUgqyyTKWdwqO', NULL, 'activo', 8),
(124, 'Akon', 'Akon', '654852195', 915735465, '$2y$10$nLfw8TmetmniLLgJIqPbcOAFmQQZ4N5VCRoJIIvBAt26OxmXK/wBu', NULL, 'activo', 8),
(125, 'Aangie', 'K', '654978216', 39486276, '$2y$10$CKtt.5OC8ufLe3kkbPShYu3D5ww51qv1PWhOzl8MIyDac7Q7HKI1C', NULL, 'activo', NULL),
(126, 'Angiek', 'k', '654987549', 321987462, '$2y$10$u.5ZTVfz/HJU8cnHZpv4cefFJtP7PFQJWCh9DJJTEGQPnAkG36icC', NULL, 'activo', 2),
(127, 'Dr', 'Dree', '654951684', 153486951, '$2y$10$vfOO7V8Ck1Wz33fyUW8X1.2KswjS1TKmrGeZmgxzT3cJxYsFfjDLa', NULL, 'activo', 8),
(128, 'Dr', 'Dree', '321654956', 1239876489, '$2y$10$B5y10oxq02RvF9NfCB4amOa2fqdgfx8jLI4n.bkRdautgba7uLFOm', NULL, 'activo', 1),
(129, 'Damian', 'Marley', '684258491', 349816754, '$2y$10$jAhAhYrIHtQ3B/I7DY3uLeec97pex2OLCxXo5J8r.OL/97HYQJquK', NULL, 'activo', 1),
(130, 'Natanael', 'Cano', '684741972', 349862179, '$2y$10$QbQox1pyk0HbZmlDJTDtz.ThF1S88OtKphEdc0pbu5czR7DISWxvu', '2025-02-26 16:44:02', 'activo', 1),
(131, 'Fuerza', 'Regida', '348754215', 976481537, '$2y$10$wtLO4eHCofN.1VMjTWJiYurkUdFa1rA2jGExbGAyOsafsWmRIYeOS', NULL, 'activo', NULL),
(132, 'Junior', 'H', '674931485', 379154682, '$2y$10$k8wrXao.J3ixfS4Vf3Viseu4qsTuqwdaZ1Z/AtQQPgjiLjXXmDDte', '2025-02-26 16:53:26', 'activo', NULL),
(133, 'Tito', 'Double', '349846517', 349846517, '$2y$10$3MiSxpdNMO8eoSD.Rp4N1eyFT5kPJhb9.gHFnWdx3bSbxOYv8TAUC', NULL, 'activo', NULL),
(134, 'Los Dos', 'Carnales', '954786135', 954786135, '$2y$10$Wima1F2QxLcmkB7e6HoTu.a1osWr5tkjZgsEMp09UEGdBi92baZlG', NULL, 'activo', NULL);

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
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id_configuracion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `cuadres_diarios`
--
ALTER TABLE `cuadres_diarios`
  MODIFY `id_cuadre` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `cuentas`
--
ALTER TABLE `cuentas`
  MODIFY `id_cuenta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `cuota_prestamo`
--
ALTER TABLE `cuota_prestamo`
  MODIFY `id_cuota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2212;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `id_gastos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `genero`
--
ALTER TABLE `genero`
  MODIFY `id_genero` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `movimientos_base`
--
ALTER TABLE `movimientos_base`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=242;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id_permisos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `prestamo`
--
ALTER TABLE `prestamo`
  MODIFY `id_prestamo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

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
  MODIFY `id_rol_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

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
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

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
