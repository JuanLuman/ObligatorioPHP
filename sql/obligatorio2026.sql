-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 14, 2026 at 01:20 AM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `obligatorio2026`
--

-- --------------------------------------------------------

--
-- Table structure for table `equipos`
--

DROP TABLE IF EXISTS `equipos`;
CREATE TABLE IF NOT EXISTS `equipos` (
  `id_equipo` int NOT NULL AUTO_INCREMENT,
  `codigo_inventario` varchar(50) NOT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `modelo` varchar(50) DEFAULT NULL,
  `anio_adquisicion` year DEFAULT NULL,
  `valor_estimado` decimal(10,2) DEFAULT NULL,
  `tipo_equipo` varchar(50) DEFAULT NULL,
  `estado` enum('Disponible','Prestado','Mantenimiento','Baja') DEFAULT 'Disponible',
  `id_sucursal` int NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_equipo`),
  UNIQUE KEY `codigo_inventario` (`codigo_inventario`),
  KEY `id_sucursal` (`id_sucursal`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `equipos`
--

INSERT INTO `equipos` (`id_equipo`, `codigo_inventario`, `marca`, `modelo`, `anio_adquisicion`, `valor_estimado`, `tipo_equipo`, `estado`, `id_sucursal`, `foto`) VALUES
(1, 'EQ-001', 'Dell', 'Latitude 5520', '2022', 1200.00, 'Laptop', 'Prestado', 1, 'Dell.jpg'),
(2, 'EQ-002', 'HP', 'LaserJet Pro M404', '2021', 450.00, 'Impresora', 'Disponible', 1, 'Impresora.jpg'),
(3, 'EQ-003', 'Lenovo', 'ThinkPad X1 Carbon', '2023', 1800.00, 'Laptop', 'Prestado', 2, 'lenovo.jpg'),
(4, 'EQ-004', 'Logitech', 'MX Master 3', '2024', 99.90, 'Periferico', 'Disponible', 1, 'EQ-004_1783831330.jpg'),
(7, 'EQ-005', 'HP', 'NUEVO', '2014', 10000.00, 'Laptop', 'Disponible', 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `prestamos`
--

DROP TABLE IF EXISTS `prestamos`;
CREATE TABLE IF NOT EXISTS `prestamos` (
  `id_prestamo` int NOT NULL AUTO_INCREMENT,
  `id_equipo` int NOT NULL,
  `id_funcionario` int NOT NULL,
  `fecha_prestamo` date NOT NULL,
  `fecha_devolucion_prevista` date NOT NULL,
  `fecha_devolucion_real` date DEFAULT NULL,
  `observaciones` text,
  `estado` enum('activo','devuelto') NOT NULL DEFAULT 'activo',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_prestamo`),
  KEY `id_equipo` (`id_equipo`),
  KEY `id_funcionario` (`id_funcionario`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `prestamos`
--

INSERT INTO `prestamos` (`id_prestamo`, `id_equipo`, `id_funcionario`, `fecha_prestamo`, `fecha_devolucion_prevista`, `fecha_devolucion_real`, `observaciones`, `estado`, `created_at`) VALUES
(1, 1, 11111111, '2026-06-01', '2026-06-15', '2026-06-14', 'Devuelto antes de tiempo', 'devuelto', '2026-06-18 22:31:10'),
(2, 2, 11111111, '2026-06-05', '2026-06-20', '2026-07-13', 'PrÃ©stamo activo, sala de reuniones', 'devuelto', '2026-06-18 22:31:10'),
(3, 3, 33333333, '2026-06-10', '2026-06-25', NULL, 'PrÃ©stamo activo, trabajo remoto', 'devuelto', '2026-06-18 22:31:10'),
(4, 1, 22222222, '2026-07-11', '2026-07-18', '2026-07-12', 'Prueba de flujo completo', 'devuelto', '2026-07-12 01:23:23'),
(5, 1, 11111111, '2026-07-08', '2026-07-09', NULL, 'Color blanco', 'activo', '2026-07-12 01:27:46'),
(6, 4, 44444444, '2026-07-13', '2026-07-18', '2026-07-13', 'Prueba devolucion admin', 'devuelto', '2026-07-13 10:38:46');

-- --------------------------------------------------------

--
-- Table structure for table `sucursales`
--

DROP TABLE IF EXISTS `sucursales`;
CREATE TABLE IF NOT EXISTS `sucursales` (
  `id_sucursal` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `direccion` varchar(200) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_sucursal`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sucursales`
--

INSERT INTO `sucursales` (`id_sucursal`, `nombre`, `direccion`, `telefono`) VALUES
(1, 'Casa Central', 'Av. 18 de Julio 1234, Montevideo', '29001234'),
(2, 'Sucursal Norte', 'Bulevar Artigas 567, Montevideo', '29005678'),
(4, 'Sucursal Sur', 'Guanahani 2013', '25402558');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `ci` varchar(10) NOT NULL,
  `primer_nombre` varchar(50) NOT NULL,
  `segundo_nombre` varchar(50) DEFAULT NULL,
  `primer_apellido` varchar(50) NOT NULL,
  `segundo_apellido` varchar(50) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `password` varchar(32) NOT NULL,
  `tipo_usuario` enum('funcionario','administrador') NOT NULL,
  `id_sucursal` int DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `ci` (`ci`),
  UNIQUE KEY `email` (`email`),
  KEY `id_sucursal` (`id_sucursal`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `ci`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `fecha_nacimiento`, `email`, `foto`, `password`, `tipo_usuario`, `id_sucursal`, `activo`) VALUES
(1, '12345678', 'Admin', NULL, 'Test', '', NULL, 'admin@test.com', NULL, '21232f297a57a5a743894a0e4a801fc3', 'administrador', NULL, 1),
(2, '49162394', 'Juan', 'Alfredo', 'Gutierrez', 'Luman', '0000-00-00', 'jgutierrez@gmail.com', NULL, '16d7a4fca7442dda3ad93c9a726597e4', 'administrador', 1, 1),
(3, '11111111', 'Carlos', '', 'Garcia', '', NULL, 'cgarcia@techrent.com', NULL, '16d7a4fca7442dda3ad93c9a726597e4', 'funcionario', 1, 1),
(4, '22222222', 'Maria', NULL, 'Lopez', NULL, NULL, 'mlopez@techrent.com', NULL, '16d7a4fca7442dda3ad93c9a726597e4', 'funcionario', 1, 1),
(5, '33333333', 'Pedro', NULL, 'Martinez', NULL, NULL, 'pmartinez@techrent.com', NULL, '16d7a4fca7442dda3ad93c9a726597e4', 'funcionario', 2, 1),
(6, '44444444', 'Laura', '', 'Fernandez', '', '1995-03-20', 'lfernandez@techrent.com', '', '991f553932d70328ba12c29f00e0570f', 'funcionario', 2, 1),
(9, '49162395', 'Juan', 'Carlos', 'Gimenez', 'Lopez', '2026-07-01', 'jcarlos@gmail.com', '', '16d7a4fca7442dda3ad93c9a726597e4', 'funcionario', 4, 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
