-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 17-02-2025 a las 22:43:08
-- Versión del servidor: 8.0.40-cll-lve
-- Versión de PHP: 8.3.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ttxicknh_elsol_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parametros`
--

CREATE TABLE `parametros` (
  `id` int NOT NULL,
  `tipo` char(1) NOT NULL,
  `codigo` varchar(5) NOT NULL,
  `abreviatura` varchar(10) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `padre` int NOT NULL,
  `par_estado` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `parametros`
--

INSERT INTO `parametros` (`id`, `tipo`, `codigo`, `abreviatura`, `descripcion`, `padre`, `par_estado`) VALUES
(1, 'C', 'EST', '', 'Estados', 0, 1),
(2, 'D', '1', '', 'Habilitado', 1, 1),
(3, 'D', '0', '', 'Deshabilitado', 1, 1),
(4, 'C', 'MP', '', 'Medios de Pago', 0, 1),
(5, 'D', '1', '', 'Efectivo', 4, 1),
(6, 'D', '2', '', 'Ticket depósito', 4, 2),
(7, 'D', '3', '', 'Crédito', 4, 1),
(8, 'C', 'TC', '', 'Tipos de Comprobante', 0, 1),
(9, 'D', '1', '', 'Factura', 8, 1),
(10, 'D', '2', '', 'Boleta de venta', 8, 1),
(11, 'D', '5', '', 'Ticket de venta', 8, 1),
(12, 'C', 'TD', '', 'Tipos de Documento', 0, 1),
(13, 'D', '1', '', 'DNI', 12, 1),
(14, 'D', '6', '', 'RUC', 12, 1),
(15, 'C', 'PER', '', 'Perfiles', 0, 1),
(16, 'D', '1', '', 'Ventas', 15, 1),
(17, 'D', '2', '', 'Caja', 15, 1),
(18, 'C', 'TI', '', 'Tipo de Ingreso', 0, 1),
(19, 'D', '1', '', 'Jicamarca', 18, 1),
(20, 'D', '2', '', 'Alquiler', 18, 1),
(21, 'D', '3', '', 'Pucallpa', 18, 1),
(22, 'D', '4', '', 'Iquitos', 18, 1),
(23, 'D', '5', '', 'Otros', 18, 1),
(24, 'D', '3', '', 'Mantenimiento', 15, 1),
(25, 'D', '4', '', 'BCP', 4, 1),
(26, 'D', '5', '', 'BBVA', 4, 1),
(27, 'D', '6', '', 'MC', 4, 2),
(28, 'D', '7', '', 'Visa', 4, 2),
(29, 'C', 'UM', '', 'Unidad de medida', 0, 1),
(30, 'D', '1', 'NIU', 'Unidades (Bienes)', 29, 1),
(31, 'D', '2', 'KG', 'Kilogramos', 29, 1),
(32, 'D', '3', 'PT', 'Pies cúbicos', 29, 1),
(33, 'D', '3', '', 'Nota de débito', 8, 1),
(34, 'D', '4', 'ZZ', 'Unidades (Servicios)', 29, 1),
(35, 'D', '8', '', 'Tarjeta de crédito', 4, 1),
(36, 'D', '9', '', 'Tarjeta de débito', 4, 1),
(37, 'D', '10', '', 'Yape', 4, 1),
(38, 'C', 'CPAGO', '', 'Condición de Pago', 0, 1),
(39, 'D', '1', 'CON', 'Contado', 38, 1),
(40, 'D', '2', 'CRE', 'Crédito', 38, 1),
(41, 'D', '4', '', 'Carnet de extranjería', 12, 1),
(42, 'C', 'DET', '', 'Aplica detracción', 0, 1),
(43, 'D', '0', 'false', 'No', 42, 1),
(44, 'D', '1', 'true', 'Sí', 42, 1),
(45, 'C', 'MPDET', '', 'Medio pago detracción', 0, 1),
(46, 'D', '1', '', '001 - Depósito en cuenta', 45, 1),
(47, 'D', '2', '', '002 - Giro', 45, 2),
(48, 'D', '3', '', '003 - Transferencia de fondos', 45, 1),
(49, 'D', '4', '', '004 - Orden de pago', 45, 1),
(50, 'D', '5', '', '005 - Tarjeta de débito', 45, 1),
(51, 'D', '6', '', '006 - Tarjeta de crédito emitida en el país por una empresa del sistema financiero', 45, 1),
(52, 'D', '7', '', '007 - Cheques con la cláusula de NO NEGOCIABLE, INTRANSFERIBLES, NO A LA ORDEN u otra equivalente, a', 45, 2),
(53, 'D', '8', '', '008 - Efectivo, por operaciones en las que no existe obligación de utilizar medio de pago', 45, 2),
(54, 'D', '9', '', '009 - Efectivo, en los demás casos', 45, 2),
(55, 'D', '10', '', '010 - Medios de pago usados en comercio exterior', 45, 2),
(56, 'D', '11', '', '011 - Documentos emitidos por las EDPYMES y las cooperativas de ahorro y crédito no autorizadas a ca', 45, 2),
(57, 'D', '12', '', '012 - Tarjeta de crédito emitida en el país o en el exterior por una empresa no perteneciente al sis', 45, 2),
(58, 'D', '13', '', '013 - Tarjetas de crédito emitidas en el exterior por empresas bancarias o financieras no domiciliad', 45, 2),
(59, 'D', '14', '', '101 - Transferencias – Comercio exterior', 45, 2),
(60, 'D', '15', '', '102 - Cheques bancarios - Comercio exterior', 45, 2),
(61, 'D', '16', '', '103 - Orden de pago simple - Comercio exterior', 45, 2),
(62, 'D', '17', '', '104 - Orden de pago documentario - Comercio exterior', 45, 2),
(63, 'D', '18', '', '105 - Remesa simple - Comercio exterior', 45, 2),
(64, 'D', '19', '', '106 - Remesa documentaria - Comercio exterior', 45, 2),
(65, 'D', '20', '', '107 - Carta de crédito simple - Comercio exterior', 45, 2),
(66, 'D', '21', '', '108 - Carta de crédito documentario - Comercio exterior', 45, 2),
(67, 'D', '22', '', '999 - Otros medios de pago', 45, 1),
(68, 'C', 'BSDET', '', 'Bienes y/o servicios', 0, 1),
(69, 'D', '8', '', '008 Madera/triplay', 68, 1),
(70, 'C', '20', '', '022 Otros servicios empresariales', 68, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `parametros`
--
ALTER TABLE `parametros`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `parametros`
--
ALTER TABLE `parametros`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
