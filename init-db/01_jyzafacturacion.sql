-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-08-2026 a las 07:36:31
-- Versión del servidor: 10.6.19-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `jyzafacturacion`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajas`
--

CREATE TABLE `cajas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `monto_inicial` decimal(10,2) NOT NULL DEFAULT 0.00,
  `monto_cierre` decimal(10,2) DEFAULT NULL,
  `estado` enum('ABIERTA','CERRADA') NOT NULL DEFAULT 'ABIERTA',
  `observacion` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja_denominaciones`
--

CREATE TABLE `caja_denominaciones` (
  `id` int(11) NOT NULL,
  `caja_id` int(11) NOT NULL,
  `tipo_movimiento` varchar(20) DEFAULT NULL,
  `tipo` varchar(20) DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja_egresos`
--

CREATE TABLE `caja_egresos` (
  `id` int(11) NOT NULL,
  `caja_id` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `tipo` enum('GASTO','REEMBOLSO_ANULACION') NOT NULL DEFAULT 'GASTO',
  `descripcion` text DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja_ingresos`
--

CREATE TABLE `caja_ingresos` (
  `id` int(11) NOT NULL,
  `caja_id` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `descripcion` text DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja_movimientos`
--

CREATE TABLE `caja_movimientos` (
  `id` int(11) NOT NULL,
  `caja_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `metodo_pago` enum('EFECTIVO','YAPE','TARJETA','TRANSFERENCIA','PLIN','OTROS') NOT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  `monto_recibido` decimal(10,2) NOT NULL,
  `vuelto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_examenes`
--

CREATE TABLE `categorias_examenes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_productos`
--

CREATE TABLE `categorias_productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `ruc` varchar(11) NOT NULL,
  `razon_social` varchar(255) NOT NULL,
  `nombre_comercial` varchar(255) DEFAULT NULL,
  `direccion` varchar(255) NOT NULL,
  `ubigeo` varchar(6) NOT NULL DEFAULT '150101',
  `departamento` varchar(100) NOT NULL DEFAULT 'LIMA',
  `provincia` varchar(100) NOT NULL DEFAULT 'LIMA',
  `distrito` varchar(100) NOT NULL DEFAULT 'LIMA',
  `urbanizacion` varchar(100) DEFAULT '-',
  `cod_local` varchar(4) NOT NULL DEFAULT '0000',
  `sol_user` varchar(100) NOT NULL,
  `sol_pass` varchar(100) NOT NULL,
  `certificado_sunat` varchar(255) NOT NULL,
  `ambiente` enum('beta','produccion') NOT NULL DEFAULT 'beta',
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `daily_summaries`
--

CREATE TABLE `daily_summaries` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `correlativo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `ticket` varchar(100) DEFAULT NULL,
  `estado` varchar(30) DEFAULT 'GENERADO',
  `codigo_sunat` varchar(20) DEFAULT NULL,
  `descripcion_sunat` text DEFAULT NULL,
  `xml_path` varchar(255) DEFAULT NULL,
  `cdr_path` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `doctores`
--

CREATE TABLE `doctores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellido` varchar(255) NOT NULL,
  `especialidad` varchar(200) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `porcentaje_pago` decimal(5,2) NOT NULL DEFAULT 0.00,
  `modo_pago` enum('PORCENTAJE','FIJO') NOT NULL DEFAULT 'PORCENTAJE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `doctor_map`
--

CREATE TABLE `doctor_map` (
  `old_id` int(11) DEFAULT NULL,
  `new_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `doctor_tratamiento_tarifas`
--

CREATE TABLE `doctor_tratamiento_tarifas` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `tratamiento_id` int(11) NOT NULL,
  `monto_fijo` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `examenes`
--

CREATE TABLE `examenes` (
  `id` int(11) NOT NULL,
  `categoria_examen_id` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `muestra` varchar(150) DEFAULT NULL,
  `precio_convenio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `laboratorio_id` int(11) DEFAULT NULL,
  `comision_medico` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historias_clinicas`
--

CREATE TABLE `historias_clinicas` (
  `id` int(11) NOT NULL,
  `paciente_id` int(11) DEFAULT NULL,
  `dni` varchar(20) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `departamento_id` int(11) DEFAULT NULL,
  `sexo` varchar(2) DEFAULT NULL,
  `tipo_orden` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `como_entero` varchar(255) DEFAULT NULL,
  `obs_administrativas` varchar(255) DEFAULT NULL,
  `ocupacion` varchar(255) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `parentesco` varchar(100) DEFAULT NULL,
  `apoderado` varchar(100) DEFAULT NULL,
  `recomendado` varchar(100) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `paciente_id` int(11) DEFAULT NULL,
  `historia_clinica_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `caja_id` int(11) DEFAULT NULL,
  `tipo_doc` varchar(2) NOT NULL COMMENT '01=factura,03=boleta',
  `serie` varchar(10) DEFAULT NULL,
  `correlativo` int(11) DEFAULT NULL,
  `cliente_tipo_doc` varchar(2) DEFAULT NULL COMMENT '1=DNI,6=RUC',
  `cliente_numero` varchar(20) DEFAULT NULL,
  `cliente_nombre` varchar(255) DEFAULT NULL,
  `cliente_direccion` varchar(255) DEFAULT NULL,
  `cliente_email` varchar(150) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT 0.00,
  `igv` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) DEFAULT 0.00,
  `estado` varchar(30) DEFAULT 'BORRADOR',
  `forma_pago` varchar(10) NOT NULL DEFAULT 'CONTADO',
  `codigo_sunat` varchar(20) DEFAULT NULL,
  `descripcion_sunat` text DEFAULT NULL,
  `xml_path` varchar(255) DEFAULT NULL,
  `cdr_path` varchar(255) DEFAULT NULL,
  `hash_xml` varchar(255) DEFAULT NULL,
  `daily_summary_id` int(11) DEFAULT NULL,
  `enviado_sunat_at` datetime DEFAULT NULL,
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cliente_facturacion_id` int(11) DEFAULT NULL,
  `pdf_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invoice_cuotas`
--

CREATE TABLE `invoice_cuotas` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `numero_cuota` tinyint(3) UNSIGNED NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'PENDIENTE',
  `metodo_pago` varchar(20) DEFAULT NULL,
  `caja_id` int(11) DEFAULT NULL,
  `pagado_en` datetime DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invoice_distribuciones`
--

CREATE TABLE `invoice_distribuciones` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `tipo` enum('LABORATORIO','MATERIALES') NOT NULL,
  `laboratorio_id` int(11) DEFAULT NULL,
  `monto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `descripcion` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `tipo_item` varchar(20) DEFAULT NULL,
  `tratamiento_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `examen_id` int(11) DEFAULT NULL,
  `descripcion` varchar(255) NOT NULL,
  `nombre_sunat` varchar(250) DEFAULT NULL,
  `cantidad` decimal(10,2) NOT NULL DEFAULT 1.00,
  `valor_unitario` decimal(10,6) NOT NULL DEFAULT 0.000000,
  `precio_unitario` decimal(10,2) NOT NULL DEFAULT 0.00,
  `descuento` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `descuento_tipo` varchar(20) NOT NULL DEFAULT 'porcentaje',
  `base_igv` decimal(10,2) NOT NULL DEFAULT 0.00,
  `igv` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `codigo_producto` varchar(50) DEFAULT 'TRAT',
  `unidad` varchar(10) DEFAULT 'NIU',
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `laboratorios`
--

CREATE TABLE `laboratorios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes`
--

CREATE TABLE `pacientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellido` varchar(255) NOT NULL,
  `telefono_celular` varchar(22) DEFAULT NULL,
  `estado` varchar(1) DEFAULT 'A',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_doctores_historial`
--

CREATE TABLE `pagos_doctores_historial` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `fecha_desde` date NOT NULL,
  `fecha_hasta` date NOT NULL,
  `monto_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_comprobantes` int(11) NOT NULL DEFAULT 0,
  `observaciones` text DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_doctores_historial_movimientos`
--

CREATE TABLE `pagos_doctores_historial_movimientos` (
  `id` int(11) NOT NULL,
  `pago_historial_id` int(11) NOT NULL,
  `caja_movimiento_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `metodo_pago` varchar(20) NOT NULL,
  `base_doctor` decimal(10,2) NOT NULL DEFAULT 0.00,
  `monto_pagado` decimal(10,2) NOT NULL DEFAULT 0.00,
  `conceptos` text DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_laboratorios_historial`
--

CREATE TABLE `pagos_laboratorios_historial` (
  `id` int(11) NOT NULL,
  `laboratorio_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `fecha_desde` date NOT NULL,
  `fecha_hasta` date NOT NULL,
  `monto_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_comprobantes` int(11) NOT NULL DEFAULT 0,
  `observaciones` text DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_laboratorios_historial_distribuciones`
--

CREATE TABLE `pagos_laboratorios_historial_distribuciones` (
  `id` int(11) NOT NULL,
  `pago_historial_id` int(11) NOT NULL,
  `invoice_distribucion_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `monto_pagado` decimal(10,2) NOT NULL DEFAULT 0.00,
  `conceptos` text DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `controller` varchar(60) NOT NULL,
  `action` varchar(60) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `controller`, `action`, `descripcion`, `created`, `modified`) VALUES
(5, 'Campañas', 'add', 'Agregar campaña', '2026-01-25 18:44:18', '2026-01-25 18:48:09'),
(6, 'Campañas', 'edit', 'Editar campaña', '2026-01-25 18:44:18', '2026-01-25 18:48:09'),
(7, 'Campañas', 'index', 'Listar campañas', '2026-01-25 18:44:18', '2026-01-25 18:48:09'),
(8, 'Campañas', 'view', 'Ver campaña', '2026-01-25 18:44:18', '2026-01-25 18:48:09'),
(9, 'Categorias', 'add', 'Agregar categoría', '2026-01-25 18:36:52', '2026-01-25 18:48:09'),
(10, 'Categorias', 'edit', 'Editar categoría', '2026-01-25 18:36:52', '2026-01-25 18:48:09'),
(11, 'Categorias', 'index', 'Listar categorías', '2026-01-25 18:36:52', '2026-01-25 18:48:09'),
(12, 'Categorias', 'view', 'Ver categoría', '2026-01-25 18:36:52', '2026-01-25 18:48:09'),
(13, 'Citas', 'add', 'Agregar cita', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(14, 'Citas', 'edit', 'Editar cita', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(15, 'Citas', 'index', 'Listar citas', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(16, 'Citas', 'reportecitas', 'Ver reporte de citas', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(17, 'Citas', 'view', 'Ver cita', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(18, 'Consultas', 'add', 'Agregar consulta', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(19, 'Consultas', 'edit', 'Editar consulta', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(20, 'Consultas', 'index', 'Listar consultas', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(21, 'Consultas', 'view', 'Ver detalle de consulta', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(26, 'Departamentos', 'add', 'Agregar departamento', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(27, 'Departamentos', 'edit', 'Editar departamento', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(28, 'Departamentos', 'index', 'Listar departamentos', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(29, 'Departamentos', 'view', 'Ver departamento', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(30, 'Doctores', 'add', 'Agregar doctor', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(31, 'Doctores', 'edit', 'Editar doctor', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(32, 'Doctores', 'index', 'Listar doctores', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(33, 'Doctores', 'view', 'Ver doctor', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(34, 'Documentos', 'add', 'Agregar documento', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(35, 'Documentos', 'edit', 'Editar documento', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(36, 'Documentos', 'index', 'Listar documentos', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(37, 'Documentos', 'view', 'Ver documento', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(42, 'ExamenesFisicos', 'add', 'Agregar examen físico', '2026-01-25 18:36:52', '2026-01-25 18:48:09'),
(43, 'ExamenesFisicos', 'edit', 'Editar examen físico', '2026-01-25 18:36:52', '2026-01-25 18:48:09'),
(44, 'ExamenesFisicos', 'index', 'Listar exámenes físicos', '2026-01-25 18:36:52', '2026-01-25 18:48:09'),
(45, 'ExamenesFisicos', 'view', 'Ver examen físico', '2026-01-25 18:36:52', '2026-01-25 18:48:09'),
(46, 'HistoriasClinicas', 'add', 'Agregar historia clínica', '2026-01-25 18:36:52', '2026-01-25 18:48:09'),
(47, 'HistoriasClinicas', 'edit', 'Editar historia clínica', '2026-01-25 18:36:52', '2026-01-25 18:48:09'),
(48, 'HistoriasClinicas', 'index', 'Listar historias clínicas', '2026-01-25 18:36:52', '2026-01-25 18:48:09'),
(49, 'HistoriasClinicas', 'view', 'Ver historia clínica', '2026-01-25 18:36:52', '2026-01-25 18:48:09'),
(50, 'HorariosBloqueos', 'add', 'Agregar bloqueo de horarios', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(51, 'HorariosBloqueos', 'edit', 'Editar bloqueo de horarios', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(52, 'HorariosBloqueos', 'index', 'Listar bloqueos de horarios', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(53, 'HorariosBloqueos', 'view', 'Ver bloqueo de horarios', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(54, 'HorariosDoctores', 'add', 'Agregar horario doctor', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(55, 'HorariosDoctores', 'edit', 'Editar horario doctor', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(56, 'HorariosDoctores', 'editAll', 'Editar todos los horarios del doctor', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(57, 'HorariosDoctores', 'index', 'Listar horarios doctores', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(58, 'HorariosDoctores', 'view', 'Ver horario doctor', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(59, 'Medicamentos', 'add', 'Agregar medicamento', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(60, 'Medicamentos', 'edit', 'Editar medicamento', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(61, 'Medicamentos', 'index', 'Listar medicamentos', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(62, 'Medicamentos', 'view', 'Ver medicamento', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(67, 'Pacientes', 'add', 'Agregar paciente', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(68, 'Pacientes', 'edit', 'Editar paciente', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(69, 'Pacientes', 'index', 'Listar pacientes', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(70, 'Pacientes', 'view', 'Ver detalle de paciente', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(71, 'Permisos', 'add', 'Agregar permiso', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(72, 'Permisos', 'edit', 'Editar permiso', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(73, 'Permisos', 'index', 'Listar permisos', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(74, 'Permisos', 'view', 'Ver permiso', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(75, 'Procedimientos', 'add', 'Agregar procedimiento', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(76, 'Procedimientos', 'edit', 'Editar procedimiento', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(77, 'Procedimientos', 'index', 'Listar procedimientos', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(78, 'Procedimientos', 'view', 'Ver procedimiento', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(79, 'Productos', 'add', 'Agregar producto', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(80, 'Productos', 'edit', 'Editar producto', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(81, 'Productos', 'index', 'Listar productos', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(82, 'Productos', 'view', 'Ver producto', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(87, 'Recetas', 'add', 'Agregar receta', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(88, 'Recetas', 'edit', 'Editar receta', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(89, 'Recetas', 'index', 'Listar recetas', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(90, 'Recetas', 'view', 'Ver receta', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(91, 'Roles', 'add', 'Agregar rol', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(92, 'Roles', 'createWithDefaults', 'Crear rol con permisos por defecto', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(93, 'Roles', 'edit', 'Editar rol', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(94, 'Roles', 'index', 'Listar roles', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(95, 'Roles', 'view', 'Ver rol', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(100, 'Tratamientos', 'add', 'Agregar tratamiento', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(101, 'Tratamientos', 'edit', 'Editar tratamiento', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(102, 'Tratamientos', 'index', 'Listar tratamientos', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(103, 'Tratamientos', 'view', 'Ver tratamiento', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(104, 'Users', 'add', 'Agregar usuario', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(105, 'Users', 'edit', 'Editar usuario', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(106, 'Users', 'index', 'Listar usuarios', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(107, 'Users', 'permisos', 'Gestionar permisos de usuario', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(108, 'Users', 'view', 'Ver usuario', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(109, 'ViasAdministracion', 'add', 'Agregar vía de administración', '2026-01-25 18:36:52', '2026-01-25 18:48:09'),
(110, 'ViasAdministracion', 'edit', 'Editar vía de administración', '2026-01-25 18:36:52', '2026-01-25 18:48:09'),
(111, 'ViasAdministracion', 'index', 'Listar vías de administración', '2026-01-25 18:36:52', '2026-01-25 18:48:09'),
(112, 'ViasAdministracion', 'view', 'Ver vía de administración', '2026-01-25 18:36:52', '2026-01-25 18:48:09'),
(113, 'VistaConsultasProcedimientos', 'index', 'Listar consultas procedimientos', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(114, 'VistaConsultasProcedimientos', 'view', 'Ver consultas procedimientos', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(115, 'VistaRecetasDepartamentos', 'index', 'Listar recetas por departamentos', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(116, 'VistaRecetasDepartamentos', 'view', 'Ver recetas por departamentos', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(117, 'VistaReporteConsultasDoctores', 'index', 'Listar reporte de consultas por doctores', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(118, 'VistaReporteConsultasDoctores', 'view', 'Ver reporte de consultas por doctores', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(119, 'VistaReportePacientes', 'index', 'Listar reporte de pacientes', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(120, 'VistaReportePacientes', 'view', 'Ver reporte de pacientes', '2026-01-25 18:36:52', '2026-01-25 18:36:52'),
(123, 'Users', 'toggleStatus', 'Cambiar estado de usuario (Activo/Inactivo)', '2026-01-25 19:10:13', '2026-01-25 19:10:13'),
(124, 'Citas', 'citaDiaria', 'Ver citas del dia', '2026-01-25 20:09:28', '2026-01-31 12:12:59'),
(125, 'RecetasMedicamentos', 'index', 'Listar recetas medicamentos', '2026-01-25 20:09:33', '2026-01-25 20:09:33'),
(126, 'RecetasMedicamentos', 'add', 'Agregar receta medicamento', '2026-01-25 20:09:33', '2026-01-25 20:09:33'),
(127, 'RecetasMedicamentos', 'edit', 'Editar receta medicamento', '2026-01-25 20:09:33', '2026-01-25 20:09:33'),
(128, 'RecetasMedicamentos', 'view', 'Ver receta medicamento', '2026-01-25 20:09:33', '2026-01-25 20:09:33'),
(129, 'FormasFarmaceuticas', 'index', 'Listar formas farmaceuticas', '2026-01-25 20:09:38', '2026-01-25 20:09:38'),
(130, 'FormasFarmaceuticas', 'add', 'Agregar forma farmaceutica', '2026-01-25 20:09:38', '2026-01-25 20:09:38'),
(131, 'FormasFarmaceuticas', 'edit', 'Editar forma farmaceutica', '2026-01-25 20:09:38', '2026-01-25 20:09:38'),
(132, 'FormasFarmaceuticas', 'view', 'Ver forma farmaceutica', '2026-01-25 20:09:38', '2026-01-25 20:09:38'),
(177, 'Citas', 'marcarHoraLlegada', 'Marcar hora de llegada en cita', '2026-01-31 12:00:26', '2026-01-31 12:00:26'),
(178, 'Citas', 'restablecerCita', 'Restablecer estado de cita', '2026-01-31 12:00:26', '2026-01-31 12:00:26'),
(179, 'Citas', 'changeStatus', 'Cambiar estado de cita', '2026-01-31 12:00:26', '2026-01-31 12:00:26'),
(180, 'Presupuestos', 'add', 'Agregar presupuesto', '2026-01-31 12:00:26', '2026-01-31 12:12:24'),
(181, 'Presupuestos', 'edit', 'Editar presupuesto', '2026-01-31 12:00:26', '2026-01-31 12:12:27'),
(182, 'Presupuestos', 'view', 'Ver presupuesto', '2026-01-31 12:00:26', '2026-01-31 12:12:29'),
(183, 'Presupuestos', 'index', 'Listar presupuestos', '2026-01-31 12:00:26', '2026-01-31 12:12:31'),
(193, 'Cajas', 'add', 'Agregar caja', '2026-02-23 02:09:05', '2026-02-23 02:09:05'),
(194, 'Cajas', 'index', 'Listar cajas', '2026-02-23 02:09:05', '2026-02-23 02:09:05'),
(195, 'Cajas', 'view', 'Ver caja', '2026-02-23 02:09:05', '2026-02-23 02:09:05'),
(196, 'Cajas', 'abrirCaja', 'Abrir caja', '2026-02-23 02:09:05', '2026-02-23 02:09:05'),
(197, 'Cajas', 'cerrarCaja', 'Cerrar caja', '2026-02-23 02:09:05', '2026-02-23 02:09:05'),
(198, 'Cajas', 'reportePdf', 'Descargar reporte PDF de caja', '2026-02-23 02:09:05', '2026-02-23 02:09:05'),
(204, 'Pacientes', 'delete', 'Eliminar un paciente', '2026-03-05 11:15:26', '2026-03-05 11:15:26'),
(205, 'Recetas', 'exportPdf', 'Exportar receta a PDF', '2026-04-14 13:14:48', '2026-04-14 13:14:48'),
(218, 'Invoices', 'enviarResumenDiario', 'Enviar resumen diario a SUNAT', '2026-05-01 00:24:12', '2026-05-01 00:24:12'),
(219, 'CategoriasProductos', 'index', 'Ver listado de categorías de productos', '2026-05-06 01:42:17', '2026-05-06 01:42:17'),
(220, 'CategoriasProductos', 'add', 'Crear nueva categoría de producto', '2026-05-06 01:42:17', '2026-05-06 01:42:17'),
(221, 'CategoriasProductos', 'edit', 'Editar categoría de producto', '2026-05-06 01:42:17', '2026-05-06 01:42:17'),
(222, 'CategoriasProductos', 'delete', 'Eliminar categoría de producto', '2026-05-06 01:42:17', '2026-05-06 01:42:17'),
(228, 'Invoices', 'add', 'Crear nueva factura', '2026-05-06 01:53:04', '2026-05-06 01:53:04'),
(229, 'Invoices', 'view', 'Ver detalle de factura', '2026-05-06 01:53:04', '2026-05-06 01:53:04'),
(230, 'Invoices', 'index', 'Ver listado de facturas', '2026-05-06 01:53:04', '2026-05-06 01:53:04'),
(231, 'Invoices', 'emitir', 'Emitir factura', '2026-05-06 01:53:04', '2026-05-06 01:53:04'),
(232, 'Invoices', 'pdf', 'Generar PDF de factura', '2026-05-06 01:53:04', '2026-05-06 01:53:04'),
(233, 'Invoices', 'consultarResumen', 'Consultar resumen de facturas', '2026-05-06 01:53:04', '2026-05-06 01:53:04'),
(234, 'Recordatorios', 'add', 'Agregar recordatorio', '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(235, 'Recordatorios', 'edit', 'Editar recordatorio', '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(236, 'Recordatorios', 'index', 'Listar recordatorios', '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(237, 'Recordatorios', 'view', 'Ver recordatorio', '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(238, 'Recordatorios', 'delete', 'Eliminar recordatorio', '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(239, 'Recordatorios', 'getByPaciente', 'Obtener recordatorios por paciente', '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(240, 'Ingresos', 'index', 'Listar ingresos', '2026-06-09 03:02:29', '2026-06-09 03:02:29'),
(241, 'Ingresos', 'view', 'Ver detalle de ingreso', '2026-06-09 03:02:29', '2026-06-09 03:02:29'),
(242, 'Ingresos', 'add', 'Agregar ingreso', '2026-06-09 03:02:29', '2026-06-09 03:02:29'),
(243, 'Egresos', 'index', 'Listar egresos', '2026-06-09 03:02:29', '2026-06-09 03:02:29'),
(244, 'Egresos', 'view', 'Ver detalle de egreso', '2026-06-09 03:02:29', '2026-06-09 03:02:29'),
(245, 'Egresos', 'add', 'Agregar egreso', '2026-06-09 03:02:29', '2026-06-09 03:02:29'),
(246, 'PaquetesPagos', 'add', 'Agregar paquete de pago', '2026-05-31 00:00:00', '2026-05-31 00:00:00'),
(247, 'PaquetesPagos', 'edit', 'Editar paquete de pago', '2026-05-31 00:00:00', '2026-05-31 00:00:00'),
(248, 'PaquetesPagos', 'index', 'Listar paquetes de pago', '2026-05-31 00:00:00', '2026-05-31 00:00:00'),
(249, 'PaquetesPagos', 'view', 'Ver paquete de pago', '2026-05-31 00:00:00', '2026-05-31 00:00:00'),
(250, 'PaquetesPagos', 'delete', 'Eliminar paquete de pago', '2026-05-31 00:00:00', '2026-05-31 00:00:00'),
(251, 'PaquetesPagos', 'agregarCuotas', 'Agregar cuotas a paquete', '2026-05-31 00:00:00', '2026-05-31 00:00:00'),
(252, 'PaquetesPagos', 'registrarPago', 'Registrar pago de cuota', '2026-05-31 00:00:00', '2026-05-31 00:00:00'),
(253, 'RecordatorioControles', 'add', 'add', '2026-05-23 11:44:46', '2026-05-23 11:44:46'),
(254, 'RecordatorioControles', 'edit', 'edit', '2026-05-23 11:44:46', '2026-05-23 11:44:46'),
(255, 'RecordatorioControles', 'index', 'index', '2026-05-23 11:45:04', '2026-05-23 11:45:04'),
(256, 'RecordatorioControles', 'view', 'view', '2026-05-23 11:45:04', '2026-05-23 11:45:04'),
(257, 'RecordatorioControles', 'delete', 'delete', '2026-05-23 12:26:56', '2026-05-23 12:26:56'),
(258, 'RecordatorioControles', 'reportes', 'reportes', '2026-05-23 12:31:37', '2026-05-23 12:31:37'),
(259, 'Reportes', 'index', 'Ver reportes de comprobantes', '2026-06-15 02:05:41', '2026-06-15 02:05:41'),
(260, 'Laboratorios', 'index', 'Listar laboratorios', '2026-07-28 21:28:43', '2026-07-28 21:28:43'),
(261, 'Laboratorios', 'view', 'Ver detalle de laboratorio', '2026-07-28 21:28:43', '2026-07-28 21:28:43'),
(262, 'Laboratorios', 'add', 'Agregar laboratorio', '2026-07-28 21:28:43', '2026-07-28 21:28:43'),
(263, 'Laboratorios', 'edit', 'Editar laboratorio', '2026-07-28 21:28:43', '2026-07-28 21:28:43'),
(264, 'Laboratorios', 'delete', 'Eliminar laboratorio', '2026-07-28 21:28:43', '2026-07-28 21:28:43'),
(265, 'PagosDoctores', 'index', 'Ver reporte de pagos a doctores', '2026-07-28 22:37:46', '2026-07-28 22:37:46'),
(266, 'PagosDoctores', 'exportPdf', 'Exportar reporte de pagos a doctores (PDF)', '2026-07-28 22:37:46', '2026-07-28 22:37:46'),
(267, 'PagosDoctores', 'exportarExcel', 'Exportar reporte de pagos a doctores (Excel)', '2026-07-28 22:37:46', '2026-07-28 22:37:46'),
(269, 'PagosDoctores', 'historial', 'Ver historial de pagos a doctores', '2026-07-28 22:37:46', '2026-07-28 22:37:46'),
(270, 'PagosDoctores', 'historialDetalle', 'Ver detalle de un pago a doctor', '2026-07-28 22:37:46', '2026-07-28 22:37:46'),
(271, 'PagosDoctores', 'registrarPago', 'Ver pantalla de registro de pago a doctor', '2026-07-28 23:24:15', '2026-07-28 23:24:15'),
(272, 'PagosDoctores', 'guardarPago', 'Guardar pago a doctor', '2026-07-28 23:24:15', '2026-07-28 23:24:15'),
(275, 'Examenes', 'index', 'Listar ex├ímenes', '2026-07-30 23:13:14', '2026-07-30 23:13:14'),
(276, 'Examenes', 'view', 'Ver detalle de examen', '2026-07-30 23:13:14', '2026-07-30 23:13:14'),
(277, 'Examenes', 'add', 'Agregar examen', '2026-07-30 23:13:14', '2026-07-30 23:13:14'),
(278, 'Examenes', 'edit', 'Editar examen', '2026-07-30 23:13:14', '2026-07-30 23:13:14'),
(279, 'Examenes', 'delete', 'Eliminar examen', '2026-07-30 23:13:14', '2026-07-30 23:13:14'),
(280, 'CategoriasExamenes', 'index', 'Listar categor├¡as de ex├ímenes', '2026-07-30 23:13:14', '2026-07-30 23:13:14'),
(281, 'CategoriasExamenes', 'view', 'Ver detalle de categor├¡a de examen', '2026-07-30 23:13:14', '2026-07-30 23:13:14'),
(282, 'CategoriasExamenes', 'add', 'Agregar categor├¡a de examen', '2026-07-30 23:13:14', '2026-07-30 23:13:14'),
(283, 'CategoriasExamenes', 'edit', 'Editar categor├¡a de examen', '2026-07-30 23:13:14', '2026-07-30 23:13:14'),
(284, 'CategoriasExamenes', 'delete', 'Eliminar categor├¡a de examen', '2026-07-30 23:13:14', '2026-07-30 23:13:14'),
(285, 'Tratamientos', 'delete', 'Desactivar tratamiento', '2026-07-30 23:45:21', '2026-07-30 23:45:21'),
(286, 'Tratamientos', 'reactivar', 'Reactivar tratamiento', '2026-07-30 23:45:21', '2026-07-30 23:45:21'),
(287, 'Examenes', 'reactivar', 'Reactivar examen', '2026-07-30 23:49:28', '2026-07-30 23:49:28'),
(288, 'CategoriasExamenes', 'reactivar', 'Reactivar categor├¡a de examen', '2026-07-30 23:53:39', '2026-07-30 23:53:39'),
(289, 'PagosLaboratorios', 'index', 'Ver reporte de pagos a laboratorios', '2026-08-08 11:37:26', '2026-08-08 11:37:26'),
(290, 'PagosLaboratorios', 'registrarPago', 'Ver pantalla de registro de pago a laboratorio', '2026-08-08 11:37:26', '2026-08-08 11:37:26'),
(291, 'PagosLaboratorios', 'guardarPago', 'Guardar pago a laboratorio', '2026-08-08 11:37:26', '2026-08-08 11:37:26'),
(292, 'PagosLaboratorios', 'historial', 'Ver historial de pagos a laboratorios', '2026-08-08 11:37:26', '2026-08-08 11:37:26'),
(293, 'PagosLaboratorios', 'historialDetalle', 'Ver detalle de un pago a laboratorio', '2026-08-08 11:37:26', '2026-08-08 11:37:26'),
(294, 'Proveedores', 'index', 'Listar proveedores', '2026-08-08 12:23:29', '2026-08-08 12:23:29'),
(295, 'Proveedores', 'view', 'Ver detalle de proveedor', '2026-08-08 12:23:29', '2026-08-08 12:23:29'),
(296, 'Proveedores', 'add', 'Agregar proveedor', '2026-08-08 12:23:29', '2026-08-08 12:23:29'),
(297, 'Proveedores', 'edit', 'Editar proveedor', '2026-08-08 12:23:29', '2026-08-08 12:23:29'),
(298, 'Proveedores', 'delete', 'Eliminar proveedor', '2026-08-08 12:23:29', '2026-08-08 12:23:29'),
(299, 'PanelInventario', 'index', 'Ver panel de inventario', '2026-08-08 12:48:04', '2026-08-08 12:48:04'),
(300, 'PanelInventario', 'exportarExcel', 'Exportar reporte de inventario (Excel)', '2026-08-08 12:48:04', '2026-08-08 12:48:04'),
(301, 'PanelInventario', 'exportarPdf', 'Exportar reporte de inventario (PDF)', '2026-08-08 12:48:04', '2026-08-08 12:48:04'),
(302, 'PanelInventario', 'reporteGanancias', 'Ver reporte de ganancia por ventas de productos', '2026-08-08 13:02:36', '2026-08-08 13:02:36'),
(303, 'PanelInventario', 'exportarGananciasExcel', 'Exportar reporte de ganancia de productos (Excel)', '2026-08-08 13:02:36', '2026-08-08 13:02:36'),
(304, 'PanelInventario', 'exportarGananciasPdf', 'Exportar reporte de ganancia de productos (PDF)', '2026-08-08 13:02:36', '2026-08-08 13:02:36'),
(305, 'PagosDoctores', 'pdfPago', 'Descargar comprobante PDF de un pago a doctor', '2026-08-18 23:22:49', '2026-08-18 23:22:49'),
(306, 'PagosLaboratorios', 'pdfPago', 'Descargar comprobante PDF de un pago a laboratorio', '2026-08-18 23:28:07', '2026-08-18 23:28:07'),
(307, 'Finanzas', 'index', 'Ver panel consolidado de finanzas (ingresos, egresos, pagos)', '2026-08-18 23:45:16', '2026-08-18 23:45:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `phinxlog`
--

CREATE TABLE `phinxlog` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `breakpoint` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presupuestos`
--

CREATE TABLE `presupuestos` (
  `id` int(11) NOT NULL,
  `historia_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `tipo_de_cambio` decimal(10,2) DEFAULT NULL,
  `nombre_apellido` text DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presupuestos_invoices`
--

CREATE TABLE `presupuestos_invoices` (
  `id` int(11) NOT NULL,
  `presupuesto_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `monto_facturado` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presupuestos_tratamientos`
--

CREATE TABLE `presupuestos_tratamientos` (
  `id` int(11) NOT NULL,
  `presupuesto_id` int(11) NOT NULL,
  `tratamiento_id` int(11) DEFAULT NULL,
  `tipo_item` varchar(20) NOT NULL DEFAULT 'tratamiento',
  `producto_id` int(11) DEFAULT NULL,
  `examen_id` int(11) DEFAULT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `modified` datetime NOT NULL,
  `created` datetime NOT NULL,
  `observaciones` text DEFAULT NULL,
  `descuento` int(11) DEFAULT NULL,
  `descuento_tipo` varchar(20) NOT NULL DEFAULT 'porcentaje'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `categoria_producto_id` int(11) NOT NULL,
  `proveedor_id` int(11) DEFAULT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `codigo` varchar(50) DEFAULT NULL,
  `codigo_sunat` varchar(50) DEFAULT NULL,
  `unidad` varchar(10) NOT NULL DEFAULT 'NIU',
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio_compra` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock_minimo` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `created`, `modified`) VALUES
(1, 'Admin', 'Administrador del sistema con todos los permisos', '2026-01-25 08:34:45', '2026-01-25 19:33:58'),
(2, 'Doctor', 'Medico con acceso a pacientes, consultas y citas', '2026-01-25 08:43:40', '2026-01-25 08:57:57'),
(3, 'Recepcion', 'Recepcion', '2026-02-24 11:55:53', '2026-02-24 11:55:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles_permisos`
--

CREATE TABLE `roles_permisos` (
  `id` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `permiso_id` int(11) NOT NULL,
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles_permisos`
--

INSERT INTO `roles_permisos` (`id`, `rol_id`, `permiso_id`, `created`, `modified`) VALUES
(5, 1, 5, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(6, 1, 6, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(7, 1, 7, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(8, 1, 8, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(9, 1, 9, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(10, 1, 10, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(11, 1, 11, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(12, 1, 12, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(13, 1, 13, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(14, 1, 14, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(15, 1, 15, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(16, 1, 16, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(17, 1, 17, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(18, 1, 18, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(19, 1, 19, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(20, 1, 20, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(21, 1, 21, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(26, 1, 26, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(27, 1, 27, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(28, 1, 28, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(29, 1, 29, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(30, 1, 30, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(31, 1, 31, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(32, 1, 32, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(33, 1, 33, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(34, 1, 34, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(35, 1, 35, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(36, 1, 36, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(37, 1, 37, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(42, 1, 42, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(43, 1, 43, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(44, 1, 44, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(45, 1, 45, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(46, 1, 46, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(47, 1, 47, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(48, 1, 48, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(49, 1, 49, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(50, 1, 50, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(51, 1, 51, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(52, 1, 52, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(53, 1, 53, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(54, 1, 54, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(55, 1, 55, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(56, 1, 56, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(57, 1, 57, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(58, 1, 58, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(59, 1, 59, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(60, 1, 60, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(61, 1, 61, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(62, 1, 62, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(67, 1, 67, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(68, 1, 68, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(69, 1, 69, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(70, 1, 70, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(71, 1, 71, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(72, 1, 72, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(73, 1, 73, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(74, 1, 74, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(75, 1, 75, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(76, 1, 76, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(77, 1, 77, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(78, 1, 78, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(79, 1, 79, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(80, 1, 80, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(81, 1, 81, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(82, 1, 82, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(87, 1, 87, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(88, 1, 88, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(89, 1, 89, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(90, 1, 90, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(91, 1, 91, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(92, 1, 92, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(93, 1, 93, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(94, 1, 94, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(95, 1, 95, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(100, 1, 100, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(101, 1, 101, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(102, 1, 102, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(103, 1, 103, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(104, 1, 104, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(105, 1, 105, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(106, 1, 106, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(107, 1, 107, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(108, 1, 108, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(109, 1, 109, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(110, 1, 110, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(111, 1, 111, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(112, 1, 112, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(113, 1, 113, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(114, 1, 114, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(115, 1, 115, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(116, 1, 116, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(117, 1, 117, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(118, 1, 118, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(119, 1, 119, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(120, 1, 120, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(123, 1, 123, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(124, 1, 124, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(125, 1, 125, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(126, 1, 126, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(127, 1, 127, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(128, 1, 128, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(129, 1, 129, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(130, 1, 130, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(131, 1, 131, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(132, 1, 132, '2026-01-25 20:16:03', '2026-01-25 20:16:03'),
(256, 2, 13, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(257, 2, 14, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(258, 2, 15, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(259, 2, 16, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(260, 2, 17, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(261, 2, 18, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(262, 2, 19, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(263, 2, 20, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(264, 2, 21, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(273, 2, 42, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(274, 2, 43, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(275, 2, 44, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(276, 2, 45, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(277, 2, 46, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(278, 2, 47, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(279, 2, 48, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(280, 2, 49, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(281, 2, 59, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(282, 2, 60, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(283, 2, 61, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(284, 2, 62, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(289, 2, 75, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(290, 2, 76, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(291, 2, 77, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(292, 2, 78, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(293, 2, 79, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(294, 2, 80, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(295, 2, 81, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(296, 2, 82, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(297, 2, 87, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(298, 2, 88, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(299, 2, 89, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(300, 2, 90, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(301, 2, 100, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(302, 2, 101, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(303, 2, 102, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(304, 2, 103, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(305, 2, 124, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(306, 2, 125, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(307, 2, 126, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(308, 2, 127, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(309, 2, 128, '2026-01-25 20:16:08', '2026-01-25 20:16:08'),
(310, 1, 179, '2026-01-31 12:00:26', '2026-01-31 12:00:26'),
(312, 1, 177, '2026-01-31 12:00:26', '2026-01-31 12:00:26'),
(313, 1, 178, '2026-01-31 12:00:26', '2026-01-31 12:00:26'),
(317, 2, 179, '2026-01-31 12:00:26', '2026-01-31 12:00:26'),
(318, 2, 177, '2026-01-31 12:00:26', '2026-01-31 12:00:26'),
(319, 2, 178, '2026-01-31 12:00:26', '2026-01-31 12:00:26'),
(320, 1, 180, '2026-01-31 12:00:26', '2026-01-31 12:00:26'),
(322, 1, 181, '2026-01-31 12:00:26', '2026-01-31 12:00:26'),
(323, 1, 183, '2026-01-31 12:00:26', '2026-01-31 12:00:26'),
(324, 1, 182, '2026-01-31 12:00:26', '2026-01-31 12:00:26'),
(327, 2, 180, '2026-01-31 12:00:26', '2026-01-31 12:00:26'),
(328, 2, 181, '2026-01-31 12:00:26', '2026-01-31 12:00:26'),
(329, 2, 183, '2026-01-31 12:00:26', '2026-01-31 12:00:26'),
(330, 2, 182, '2026-01-31 12:00:26', '2026-01-31 12:00:26'),
(342, 1, 196, '2026-02-23 02:09:51', '2026-02-23 02:09:51'),
(343, 1, 193, '2026-02-23 02:09:51', '2026-02-23 02:09:51'),
(344, 1, 197, '2026-02-23 02:09:51', '2026-02-23 02:09:51'),
(345, 1, 194, '2026-02-23 02:09:51', '2026-02-23 02:09:51'),
(346, 1, 198, '2026-02-23 02:09:51', '2026-02-23 02:09:51'),
(347, 1, 195, '2026-02-23 02:09:51', '2026-02-23 02:09:51'),
(349, 3, 13, '2026-02-24 11:55:53', '2026-02-24 11:55:53'),
(350, 3, 179, '2026-02-24 11:55:53', '2026-02-24 11:55:53'),
(351, 3, 124, '2026-02-24 11:55:53', '2026-02-24 11:55:53'),
(352, 3, 14, '2026-02-24 11:55:53', '2026-02-24 11:55:53'),
(353, 3, 15, '2026-02-24 11:55:53', '2026-02-24 11:55:53'),
(354, 3, 177, '2026-02-24 11:55:53', '2026-02-24 11:55:53'),
(355, 3, 16, '2026-02-24 11:55:53', '2026-02-24 11:55:53'),
(356, 3, 178, '2026-02-24 11:55:53', '2026-02-24 11:55:53'),
(357, 3, 17, '2026-02-24 11:55:53', '2026-02-24 11:55:53'),
(358, 3, 18, '2026-02-24 11:55:53', '2026-02-24 11:55:53'),
(359, 3, 19, '2026-02-24 11:55:53', '2026-02-24 11:55:53'),
(360, 3, 20, '2026-02-24 11:55:53', '2026-02-24 11:55:53'),
(361, 3, 21, '2026-02-24 11:55:53', '2026-02-24 11:55:53'),
(362, 3, 30, '2026-02-24 11:55:53', '2026-02-24 11:55:53'),
(363, 3, 31, '2026-02-24 11:55:53', '2026-02-24 11:55:53'),
(364, 3, 32, '2026-02-24 11:55:53', '2026-02-24 11:55:53'),
(365, 3, 33, '2026-02-24 11:55:53', '2026-02-24 11:55:53'),
(366, 3, 115, '2026-02-24 11:55:54', '2026-02-24 11:55:54'),
(367, 3, 116, '2026-02-24 11:55:54', '2026-02-24 11:55:54'),
(368, 3, 117, '2026-02-24 11:55:54', '2026-02-24 11:55:54'),
(369, 3, 118, '2026-02-24 11:55:54', '2026-02-24 11:55:54'),
(370, 3, 119, '2026-02-24 11:55:54', '2026-02-24 11:55:54'),
(371, 3, 120, '2026-02-24 11:55:54', '2026-02-24 11:55:54'),
(374, 1, 205, '2026-04-14 13:17:33', '2026-04-14 13:17:33'),
(375, 2, 205, '2026-04-14 13:17:33', '2026-04-14 13:17:33'),
(376, 1, 218, '2026-05-01 00:24:12', '2026-05-01 00:24:12'),
(386, 1, 234, '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(387, 1, 235, '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(388, 1, 236, '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(389, 1, 237, '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(390, 1, 238, '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(391, 1, 239, '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(392, 1, 219, '2026-05-06 01:42:17', '2026-05-06 01:42:17'),
(393, 1, 220, '2026-05-06 01:42:17', '2026-05-06 01:42:17'),
(394, 1, 221, '2026-05-06 01:42:17', '2026-05-06 01:42:17'),
(395, 1, 222, '2026-05-06 01:42:17', '2026-05-06 01:42:17'),
(396, 1, 228, '2026-05-06 01:53:04', '2026-05-06 01:53:04'),
(397, 1, 229, '2026-05-06 01:53:04', '2026-05-06 01:53:04'),
(398, 1, 230, '2026-05-06 01:53:04', '2026-05-06 01:53:04'),
(399, 1, 231, '2026-05-06 01:53:04', '2026-05-06 01:53:04'),
(400, 1, 232, '2026-05-06 01:53:04', '2026-05-06 01:53:04'),
(401, 1, 233, '2026-05-06 01:53:04', '2026-05-06 01:53:04'),
(402, 1, 240, '2026-06-15 02:05:41', '2026-06-15 02:05:41'),
(403, 1, 262, '2026-07-28 22:37:14', '2026-07-28 22:37:14'),
(404, 1, 264, '2026-07-28 22:37:14', '2026-07-28 22:37:14'),
(405, 1, 263, '2026-07-28 22:37:14', '2026-07-28 22:37:14'),
(406, 1, 260, '2026-07-28 22:37:14', '2026-07-28 22:37:14'),
(407, 1, 261, '2026-07-28 22:37:14', '2026-07-28 22:37:14'),
(410, 1, 267, '2026-07-28 22:37:46', '2026-07-28 22:37:46'),
(411, 1, 266, '2026-07-28 22:37:46', '2026-07-28 22:37:46'),
(412, 1, 269, '2026-07-28 22:37:46', '2026-07-28 22:37:46'),
(413, 1, 270, '2026-07-28 22:37:46', '2026-07-28 22:37:46'),
(414, 1, 265, '2026-07-28 22:37:46', '2026-07-28 22:37:46'),
(417, 1, 272, '2026-07-28 23:24:15', '2026-07-28 23:24:15'),
(418, 1, 271, '2026-07-28 23:24:15', '2026-07-28 23:24:15'),
(422, 1, 282, '2026-07-30 23:17:46', '2026-07-30 23:17:46'),
(423, 1, 284, '2026-07-30 23:17:46', '2026-07-30 23:17:46'),
(424, 1, 283, '2026-07-30 23:17:46', '2026-07-30 23:17:46'),
(425, 1, 280, '2026-07-30 23:17:46', '2026-07-30 23:17:46'),
(426, 1, 281, '2026-07-30 23:17:46', '2026-07-30 23:17:46'),
(427, 1, 277, '2026-07-30 23:17:46', '2026-07-30 23:17:46'),
(428, 1, 279, '2026-07-30 23:17:46', '2026-07-30 23:17:46'),
(429, 1, 278, '2026-07-30 23:17:46', '2026-07-30 23:17:46'),
(430, 1, 275, '2026-07-30 23:17:46', '2026-07-30 23:17:46'),
(431, 1, 276, '2026-07-30 23:17:46', '2026-07-30 23:17:46'),
(437, 1, 285, '2026-07-30 23:45:21', '2026-07-30 23:45:21'),
(438, 1, 286, '2026-07-30 23:45:21', '2026-07-30 23:45:21'),
(440, 1, 287, '2026-07-30 23:49:28', '2026-07-30 23:49:28'),
(441, 1, 288, '2026-07-30 23:53:39', '2026-07-30 23:53:39'),
(442, 1, 305, '2026-08-18 23:22:53', '2026-08-18 23:22:53'),
(443, 1, 291, '2026-08-18 23:28:34', '2026-08-18 23:28:34'),
(444, 1, 292, '2026-08-18 23:28:34', '2026-08-18 23:28:34'),
(445, 1, 293, '2026-08-18 23:28:34', '2026-08-18 23:28:34'),
(446, 1, 289, '2026-08-18 23:28:34', '2026-08-18 23:28:34'),
(447, 1, 306, '2026-08-18 23:28:34', '2026-08-18 23:28:34'),
(448, 1, 290, '2026-08-18 23:28:34', '2026-08-18 23:28:34'),
(450, 1, 307, '2026-08-18 23:45:21', '2026-08-18 23:45:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `data` text DEFAULT NULL,
  `expires` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tratamientos`
--

CREATE TABLE `tratamientos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `descripcion` varchar(300) DEFAULT NULL,
  `costo` decimal(10,2) NOT NULL,
  `monto_fijo_pago` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `estado_user` varchar(1) DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `rol_id`, `created`, `modified`, `doctor_id`, `estado_user`) VALUES
(1, 'devsDK', '$2y$10$RIQL.QrTKnH4ycRL8WjXy.s/z1Z6aFjwNMd.YUBdcyFoMjiwJvyDm', 1, '2026-01-16 21:56:06', '2025-06-14 23:50:06', NULL, 'A');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_permisos`
--

CREATE TABLE `usuarios_permisos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `permiso_id` int(11) NOT NULL,
  `allow` tinyint(1) NOT NULL DEFAULT 1,
  `created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_consultas_procedimientos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_consultas_procedimientos` (
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_pacientes_campanas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_pacientes_campanas` (
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_reporte_pacientes`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_reporte_pacientes` (
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_reporte_productos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_reporte_productos` (
`producto_id` int(11)
,`nombre` varchar(150)
,`codigo` varchar(50)
,`categoria_id` int(11)
,`categoria_nombre` varchar(120)
,`proveedor_id` int(11)
,`proveedor_nombre` varchar(150)
,`proveedor_whatsapp` varchar(20)
,`proveedor_email` varchar(150)
,`precio_compra` decimal(10,2)
,`precio_venta` decimal(10,2)
,`margen_unitario` decimal(11,2)
,`margen_porcentaje` decimal(17,2)
,`stock` decimal(10,2)
,`stock_minimo` decimal(10,2)
,`valor_inventario` decimal(20,4)
,`estado_stock` varchar(7)
,`activo` tinyint(1)
,`created` datetime
,`modified` datetime
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_consultas_procedimientos`
--
DROP TABLE IF EXISTS `vista_consultas_procedimientos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_consultas_procedimientos`  AS SELECT `h`.`dni` AS `dni`, `p`.`nombre` AS `nombre`, `p`.`apellido` AS `apellido`, `c`.`motivo` AS `motivo`, NULL AS `procedimiento`, `c`.`modified` AS `fecha_registro`, `d`.`nombre` AS `doctor_nombre`, `d`.`apellido` AS `doctor_apellido` FROM (((`consultas` `c` join `historias_clinicas` `h` on(`c`.`historia_id` = `h`.`id`)) join `pacientes` `p` on(`h`.`paciente_id` = `p`.`id`)) left join `doctores` `d` on(`c`.`doctor_id` = `d`.`id`))union all select `h`.`dni` AS `dni`,`p`.`nombre` AS `nombre`,`p`.`apellido` AS `apellido`,NULL AS `motivo`,`pr`.`procedimiento` AS `procedimiento`,`pr`.`modified` AS `fecha_registro`,`d`.`nombre` AS `doctor_nombre`,`d`.`apellido` AS `doctor_apellido` from (((`procedimientos` `pr` join `historias_clinicas` `h` on(`pr`.`historia_id` = `h`.`id`)) join `pacientes` `p` on(`h`.`paciente_id` = `p`.`id`)) left join `doctores` `d` on(`pr`.`doctor_id` = `d`.`id`))  ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_pacientes_campanas`
--
DROP TABLE IF EXISTS `vista_pacientes_campanas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_pacientes_campanas`  AS SELECT `p`.`id` AS `paciente_id`, `p`.`nombre` AS `nombre`, `p`.`apellido` AS `apellido`, `hc`.`dni` AS `dni`, `hc`.`ocupacion` AS `ocupacion`, `hc`.`fecha_nacimiento` AS `fecha_nacimiento`, `cpn`.`id` AS `campana_id`, `cpn`.`nombre` AS `nombre_campana`, `ct`.`fecha_hora` AS `fecha_consulta` FROM (((`pacientes` `p` join `historias_clinicas` `hc` on(`hc`.`paciente_id` = `p`.`id`)) join `citas` `ct` on(`ct`.`paciente_id` = `p`.`id`)) join `campañas` `cpn` on(`cpn`.`id` = `ct`.`campana_id`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_reporte_pacientes`
--
DROP TABLE IF EXISTS `vista_reporte_pacientes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_reporte_pacientes`  AS SELECT `p`.`id` AS `paciente_id`, `p`.`nombre` AS `nombre_paciente`, `p`.`apellido` AS `apellido_paciente`, `p`.`created` AS `modified`, `u`.`id` AS `usuario_id`, `u`.`username` AS `nombre_usuario`, `d`.`id` AS `departamento_id`, `d`.`nombre` AS `nombre_departamento` FROM ((((`pacientes` `p` join `historias_clinicas` `hc` on(`hc`.`paciente_id` = `p`.`id`)) join `departamentos` `d` on(`hc`.`departamento_id` = `d`.`id`)) join `citas` `c` on(`c`.`paciente_id` = `p`.`id`)) join `users` `u` on(`c`.`user_id` = `u`.`id`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_reporte_productos`
--
DROP TABLE IF EXISTS `vista_reporte_productos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_reporte_productos`  AS SELECT `p`.`id` AS `producto_id`, `p`.`nombre` AS `nombre`, `p`.`codigo` AS `codigo`, `p`.`categoria_producto_id` AS `categoria_id`, `cp`.`nombre` AS `categoria_nombre`, `p`.`proveedor_id` AS `proveedor_id`, `pr`.`nombre` AS `proveedor_nombre`, `pr`.`whatsapp` AS `proveedor_whatsapp`, `pr`.`email` AS `proveedor_email`, `p`.`precio_compra` AS `precio_compra`, `p`.`precio` AS `precio_venta`, `p`.`precio`- `p`.`precio_compra` AS `margen_unitario`, CASE WHEN `p`.`precio_compra` > 0 THEN round((`p`.`precio` - `p`.`precio_compra`) / `p`.`precio_compra` * 100,2) ELSE 0 END AS `margen_porcentaje`, `p`.`stock` AS `stock`, `p`.`stock_minimo` AS `stock_minimo`, `p`.`stock`* `p`.`precio_compra` AS `valor_inventario`, CASE WHEN `p`.`stock` <= 0 THEN 'AGOTADO' WHEN `p`.`stock_minimo` > 0 AND `p`.`stock` <= `p`.`stock_minimo` THEN 'BAJO' ELSE 'NORMAL' END AS `estado_stock`, `p`.`estado` AS `activo`, `p`.`created` AS `created`, `p`.`modified` AS `modified` FROM ((`productos` `p` left join `categorias_productos` `cp` on(`cp`.`id` = `p`.`categoria_producto_id`)) left join `proveedores` `pr` on(`pr`.`id` = `p`.`proveedor_id`)) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cajas`
--
ALTER TABLE `cajas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `caja_denominaciones`
--
ALTER TABLE `caja_denominaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `caja_egresos`
--
ALTER TABLE `caja_egresos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `caja_ingresos`
--
ALTER TABLE `caja_ingresos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `caja_movimientos`
--
ALTER TABLE `caja_movimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_caja_movimientos_caja` (`caja_id`),
  ADD KEY `fk_caja_movimientos_invoice` (`invoice_id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `categorias_examenes`
--
ALTER TABLE `categorias_examenes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `categorias_productos`
--
ALTER TABLE `categorias_productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_companies_ruc` (`ruc`);

--
-- Indices de la tabla `daily_summaries`
--
ALTER TABLE `daily_summaries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_daily_summaries_company` (`company_id`);

--
-- Indices de la tabla `doctores`
--
ALTER TABLE `doctores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `doctor_tratamiento_tarifas`
--
ALTER TABLE `doctor_tratamiento_tarifas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_dtt_doctor_tratamiento` (`doctor_id`,`tratamiento_id`),
  ADD KEY `fk_dtt_tratamiento` (`tratamiento_id`);

--
-- Indices de la tabla `examenes`
--
ALTER TABLE `examenes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_examenes_categoria` (`categoria_examen_id`),
  ADD KEY `laboratorio_id` (`laboratorio_id`);

--
-- Indices de la tabla `historias_clinicas`
--
ALTER TABLE `historias_clinicas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD KEY `fk_historia_paciente` (`paciente_id`),
  ADD KEY `departamento_id` (`departamento_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_invoice_numero` (`company_id`,`serie`,`correlativo`),
  ADD KEY `fk_invoices_company` (`company_id`),
  ADD KEY `fk_invoices_paciente` (`paciente_id`),
  ADD KEY `fk_invoices_historia` (`historia_clinica_id`),
  ADD KEY `fk_invoices_daily_summary` (`daily_summary_id`),
  ADD KEY `fk_invoices_doctor` (`doctor_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_invoices_caja_id` (`caja_id`);

--
-- Indices de la tabla `invoice_cuotas`
--
ALTER TABLE `invoice_cuotas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_invoice_id` (`invoice_id`);

--
-- Indices de la tabla `invoice_distribuciones`
--
ALTER TABLE `invoice_distribuciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_invoice_distribuciones_invoice` (`invoice_id`),
  ADD KEY `fk_invoice_distribuciones_laboratorio` (`laboratorio_id`);

--
-- Indices de la tabla `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_invoice_items_invoice` (`invoice_id`),
  ADD KEY `fk_invoice_items_tratamiento` (`tratamiento_id`),
  ADD KEY `fk_invoice_items_examen` (`examen_id`);

--
-- Indices de la tabla `laboratorios`
--
ALTER TABLE `laboratorios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pagos_doctores_historial`
--
ALTER TABLE `pagos_doctores_historial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pagos_doctores_historial_doctor` (`doctor_id`),
  ADD KEY `fk_pagos_doctores_historial_user` (`user_id`);

--
-- Indices de la tabla `pagos_doctores_historial_movimientos`
--
ALTER TABLE `pagos_doctores_historial_movimientos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pdhm_caja_movimiento` (`caja_movimiento_id`),
  ADD KEY `fk_pdhm_pago_historial` (`pago_historial_id`),
  ADD KEY `fk_pdhm_invoice` (`invoice_id`);

--
-- Indices de la tabla `pagos_laboratorios_historial`
--
ALTER TABLE `pagos_laboratorios_historial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_plh_laboratorio` (`laboratorio_id`),
  ADD KEY `fk_plh_user` (`user_id`);

--
-- Indices de la tabla `pagos_laboratorios_historial_distribuciones`
--
ALTER TABLE `pagos_laboratorios_historial_distribuciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_plhd_invoice_distribucion` (`invoice_distribucion_id`),
  ADD KEY `fk_plhd_pago_historial` (`pago_historial_id`),
  ADD KEY `fk_plhd_invoice` (`invoice_id`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_permiso` (`controller`,`action`);

--
-- Indices de la tabla `phinxlog`
--
ALTER TABLE `phinxlog`
  ADD PRIMARY KEY (`version`);

--
-- Indices de la tabla `presupuestos`
--
ALTER TABLE `presupuestos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paciente_id` (`historia_id`);

--
-- Indices de la tabla `presupuestos_invoices`
--
ALTER TABLE `presupuestos_invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_presupuestos_invoices_presupuesto` (`presupuesto_id`),
  ADD KEY `fk_presupuestos_invoices_invoice` (`invoice_id`);

--
-- Indices de la tabla `presupuestos_tratamientos`
--
ALTER TABLE `presupuestos_tratamientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `presupuesto_id` (`presupuesto_id`),
  ADD KEY `tratamiento_id` (`tratamiento_id`),
  ADD KEY `fk_presupuestos_tratamientos_producto` (`producto_id`),
  ADD KEY `fk_presupuestos_tratamientos_examen` (`examen_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_productos_categoria` (`categoria_producto_id`),
  ADD KEY `proveedor_id` (`proveedor_id`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `roles_permisos`
--
ALTER TABLE `roles_permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_rol_permiso` (`rol_id`,`permiso_id`),
  ADD KEY `fk_rp_permiso` (`permiso_id`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tratamientos`
--
ALTER TABLE `tratamientos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rol_id` (`rol_id`);

--
-- Indices de la tabla `usuarios_permisos`
--
ALTER TABLE `usuarios_permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_usuario_permiso` (`usuario_id`,`permiso_id`),
  ADD KEY `fk_up_permiso` (`permiso_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cajas`
--
ALTER TABLE `cajas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `caja_denominaciones`
--
ALTER TABLE `caja_denominaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `caja_egresos`
--
ALTER TABLE `caja_egresos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `caja_ingresos`
--
ALTER TABLE `caja_ingresos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `caja_movimientos`
--
ALTER TABLE `caja_movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias_examenes`
--
ALTER TABLE `categorias_examenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias_productos`
--
ALTER TABLE `categorias_productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `daily_summaries`
--
ALTER TABLE `daily_summaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `doctores`
--
ALTER TABLE `doctores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `doctor_tratamiento_tarifas`
--
ALTER TABLE `doctor_tratamiento_tarifas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `examenes`
--
ALTER TABLE `examenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historias_clinicas`
--
ALTER TABLE `historias_clinicas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `invoice_cuotas`
--
ALTER TABLE `invoice_cuotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `invoice_distribuciones`
--
ALTER TABLE `invoice_distribuciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `laboratorios`
--
ALTER TABLE `laboratorios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos_doctores_historial`
--
ALTER TABLE `pagos_doctores_historial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos_doctores_historial_movimientos`
--
ALTER TABLE `pagos_doctores_historial_movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos_laboratorios_historial`
--
ALTER TABLE `pagos_laboratorios_historial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos_laboratorios_historial_distribuciones`
--
ALTER TABLE `pagos_laboratorios_historial_distribuciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=308;

--
-- AUTO_INCREMENT de la tabla `presupuestos`
--
ALTER TABLE `presupuestos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `presupuestos_invoices`
--
ALTER TABLE `presupuestos_invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `presupuestos_tratamientos`
--
ALTER TABLE `presupuestos_tratamientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `roles_permisos`
--
ALTER TABLE `roles_permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=451;

--
-- AUTO_INCREMENT de la tabla `tratamientos`
--
ALTER TABLE `tratamientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios_permisos`
--
ALTER TABLE `usuarios_permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `caja_movimientos`
--
ALTER TABLE `caja_movimientos`
  ADD CONSTRAINT `fk_caja_movimientos_caja` FOREIGN KEY (`caja_id`) REFERENCES `cajas` (`id`),
  ADD CONSTRAINT `fk_caja_movimientos_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`);

--
-- Filtros para la tabla `daily_summaries`
--
ALTER TABLE `daily_summaries`
  ADD CONSTRAINT `fk_daily_summaries_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `doctor_tratamiento_tarifas`
--
ALTER TABLE `doctor_tratamiento_tarifas`
  ADD CONSTRAINT `fk_dtt_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctores` (`id`),
  ADD CONSTRAINT `fk_dtt_tratamiento` FOREIGN KEY (`tratamiento_id`) REFERENCES `tratamientos` (`id`);

--
-- Filtros para la tabla `examenes`
--
ALTER TABLE `examenes`
  ADD CONSTRAINT `examenes_ibfk_laboratorio` FOREIGN KEY (`laboratorio_id`) REFERENCES `laboratorios` (`id`),
  ADD CONSTRAINT `fk_examenes_categoria` FOREIGN KEY (`categoria_examen_id`) REFERENCES `categorias_examenes` (`id`);

--
-- Filtros para la tabla `historias_clinicas`
--
ALTER TABLE `historias_clinicas`
  ADD CONSTRAINT `historias_clinicas_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `historias_clinicas_ibfk_2` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `historias_clinicas_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `fk_invoices_caja_id` FOREIGN KEY (`caja_id`) REFERENCES `cajas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_invoices_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `fk_invoices_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_invoices_historia` FOREIGN KEY (`historia_clinica_id`) REFERENCES `historias_clinicas` (`id`),
  ADD CONSTRAINT `fk_invoices_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`),
  ADD CONSTRAINT `fk_invoices_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `invoice_cuotas`
--
ALTER TABLE `invoice_cuotas`
  ADD CONSTRAINT `fk_invoice_cuotas_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `invoice_distribuciones`
--
ALTER TABLE `invoice_distribuciones`
  ADD CONSTRAINT `fk_invoice_distribuciones_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`),
  ADD CONSTRAINT `fk_invoice_distribuciones_laboratorio` FOREIGN KEY (`laboratorio_id`) REFERENCES `laboratorios` (`id`);

--
-- Filtros para la tabla `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `fk_invoice_items_examen` FOREIGN KEY (`examen_id`) REFERENCES `examenes` (`id`),
  ADD CONSTRAINT `fk_invoice_items_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_invoice_items_tratamiento` FOREIGN KEY (`tratamiento_id`) REFERENCES `tratamientos` (`id`);

--
-- Filtros para la tabla `pagos_doctores_historial`
--
ALTER TABLE `pagos_doctores_historial`
  ADD CONSTRAINT `fk_pagos_doctores_historial_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctores` (`id`),
  ADD CONSTRAINT `fk_pagos_doctores_historial_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `pagos_doctores_historial_movimientos`
--
ALTER TABLE `pagos_doctores_historial_movimientos`
  ADD CONSTRAINT `fk_pdhm_caja_movimiento` FOREIGN KEY (`caja_movimiento_id`) REFERENCES `caja_movimientos` (`id`),
  ADD CONSTRAINT `fk_pdhm_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`),
  ADD CONSTRAINT `fk_pdhm_pago_historial` FOREIGN KEY (`pago_historial_id`) REFERENCES `pagos_doctores_historial` (`id`);

--
-- Filtros para la tabla `pagos_laboratorios_historial`
--
ALTER TABLE `pagos_laboratorios_historial`
  ADD CONSTRAINT `fk_plh_laboratorio` FOREIGN KEY (`laboratorio_id`) REFERENCES `laboratorios` (`id`),
  ADD CONSTRAINT `fk_plh_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `pagos_laboratorios_historial_distribuciones`
--
ALTER TABLE `pagos_laboratorios_historial_distribuciones`
  ADD CONSTRAINT `fk_plhd_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`),
  ADD CONSTRAINT `fk_plhd_invoice_distribucion` FOREIGN KEY (`invoice_distribucion_id`) REFERENCES `invoice_distribuciones` (`id`),
  ADD CONSTRAINT `fk_plhd_pago_historial` FOREIGN KEY (`pago_historial_id`) REFERENCES `pagos_laboratorios_historial` (`id`);

--
-- Filtros para la tabla `presupuestos`
--
ALTER TABLE `presupuestos`
  ADD CONSTRAINT `presupuestos_ibfk_1` FOREIGN KEY (`historia_id`) REFERENCES `historias_clinicas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `presupuestos_invoices`
--
ALTER TABLE `presupuestos_invoices`
  ADD CONSTRAINT `fk_presupuestos_invoices_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`),
  ADD CONSTRAINT `fk_presupuestos_invoices_presupuesto` FOREIGN KEY (`presupuesto_id`) REFERENCES `presupuestos` (`id`);

--
-- Filtros para la tabla `presupuestos_tratamientos`
--
ALTER TABLE `presupuestos_tratamientos`
  ADD CONSTRAINT `fk_presupuestos_tratamientos_examen` FOREIGN KEY (`examen_id`) REFERENCES `examenes` (`id`),
  ADD CONSTRAINT `fk_presupuestos_tratamientos_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  ADD CONSTRAINT `presupuestos_tratamientos_ibfk_1` FOREIGN KEY (`presupuesto_id`) REFERENCES `presupuestos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `presupuestos_tratamientos_ibfk_2` FOREIGN KEY (`tratamiento_id`) REFERENCES `tratamientos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_productos_categoria` FOREIGN KEY (`categoria_producto_id`) REFERENCES `categorias_productos` (`id`),
  ADD CONSTRAINT `fk_productos_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`);

--
-- Filtros para la tabla `roles_permisos`
--
ALTER TABLE `roles_permisos`
  ADD CONSTRAINT `roles_permisos_ibfk_1` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `roles_permisos_ibfk_2` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios_permisos`
--
ALTER TABLE `usuarios_permisos`
  ADD CONSTRAINT `usuarios_permisos_ibfk_1` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usuarios_permisos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
