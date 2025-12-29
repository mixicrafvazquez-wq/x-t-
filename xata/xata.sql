-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-12-2025 a las 19:06:25
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
-- Base de datos: `xata`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entradas_produccion`
--

CREATE TABLE `entradas_produccion` (
  `id` int(11) NOT NULL,
  `productor_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL COMMENT 'En kilogramos',
  `fecha_entrega` date NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `entradas_produccion`
--

INSERT INTO `entradas_produccion` (`id`, `productor_id`, `producto_id`, `cantidad`, `fecha_entrega`, `fecha_registro`, `notas`) VALUES
(1, 1, 1, 50.00, '2025-12-01', '2025-12-28 02:10:21', 'Primera entrega del mes'),
(2, 1, 2, 30.00, '2025-12-01', '2025-12-28 02:10:21', 'Cosecha matutina'),
(3, 2, 5, 40.00, '2025-12-05', '2025-12-28 02:10:21', 'Calidad premium'),
(4, 3, 3, 60.00, '2025-12-08', '2025-12-28 02:10:21', 'Entrega semanal'),
(5, 3, 4, 25.00, '2025-12-08', '2025-12-28 02:10:21', 'Producto de calidad'),
(6, 4, 1, 45.00, '2025-12-10', '2025-12-28 02:10:21', 'Segunda entrega');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productores`
--

CREATE TABLE `productores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `localidad` varchar(100) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `tipo_producto` varchar(100) NOT NULL,
  `produccion_promedio_mensual` decimal(10,2) NOT NULL COMMENT 'En kilogramos',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productores`
--

INSERT INTO `productores` (`id`, `nombre`, `apellidos`, `localidad`, `telefono`, `email`, `tipo_producto`, `produccion_promedio_mensual`, `fecha_registro`, `activo`) VALUES
(1, 'Juan', 'Pérez García', 'San Miguel Tlacotepec', '5551234567', NULL, 'Tuna Verde, Tuna Roja', 150.00, '2025-12-28 02:10:21', 1),
(2, 'María', 'López Hernández', 'Santa María del Monte', '5559876543', NULL, 'Xoconostle', 80.00, '2025-12-28 02:10:21', 1),
(3, 'Pedro', 'Martínez Sánchez', 'San Juan Teotihuacán', '5556543210', NULL, 'Tuna Amarilla, Tuna Bonda', 120.00, '2025-12-28 02:10:21', 1),
(4, 'Rosa', 'González Ramírez', 'San Miguel Tlacotepec', '5552345678', NULL, 'Tuna Verde', 100.00, '2025-12-28 02:10:21', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('fruta_fresca','procesado') NOT NULL,
  `categoria` varchar(50) NOT NULL COMMENT 'tuna_verde, tuna_roja, tuna_amarilla, tuna_bonda, xoconostle, mermelada, salsa, dulce, deshidratado',
  `precio` decimal(10,2) NOT NULL,
  `disponibilidad` decimal(10,2) NOT NULL,
  `unidad_medida` enum('kg','piezas','unidad') NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `tipo`, `categoria`, `precio`, `disponibilidad`, `unidad_medida`, `imagen`, `fecha_creacion`, `fecha_actualizacion`, `activo`) VALUES
(1, 'Tuna Verde', 'Tuna verde fresca de primera calidad', 'fruta_fresca', 'tuna_verde', 25.00, 200.00, 'kg', '6952bf12aa130.jpg', '2025-12-28 02:10:21', '2025-12-29 17:49:06', 1),
(2, 'Tuna Roja', 'Tuna roja dulce y jugosa', 'fruta_fresca', 'tuna_roja', 30.00, 150.00, 'kg', '6952becd42ca6.jpg', '2025-12-28 02:10:21', '2025-12-29 17:47:57', 1),
(3, 'Tuna Amarilla', 'Tuna amarilla de sabor suave', 'fruta_fresca', 'tuna_amarilla', 28.00, 100.00, 'kg', '6952be99d963c.jpg', '2025-12-28 02:10:21', '2025-12-29 17:47:05', 1),
(4, 'Tuna Blanca', 'Tuna blanca de tamaño grande', 'fruta_fresca', 'tuna_blanca', 32.00, 80.00, 'kg', '6952be08ba9e0.jpg', '2025-12-28 02:10:21', '2025-12-29 17:51:08', 1),
(5, 'Xoconostle', 'Xoconostle fresco para preparación de alimentos', 'fruta_fresca', 'xoconostle', 35.00, 120.00, 'kg', '6952bd39a2276.jpg', '2025-12-28 02:10:21', '2025-12-29 17:41:13', 1),
(6, 'Mermelada de Tuna Roja', 'Mermelada artesanal de tuna roja', 'procesado', 'mermelada', 85.00, 50.00, 'unidad', '6952bcdf670be.jpg', '2025-12-28 02:10:21', '2025-12-29 17:39:43', 1),
(7, 'Mermelada de Tuna Verde', 'Mermelada artesanal de tuna verde', 'procesado', 'mermelada', 80.00, 45.00, 'unidad', '6952bc9231aca.jpg', '2025-12-28 02:10:21', '2025-12-29 17:38:26', 1),
(8, 'Salsa de Xoconostle', 'Salsa picante de xoconostle', 'procesado', 'salsa', 65.00, 60.00, 'unidad', '6952bc4e669e6.jpg', '2025-12-28 02:10:21', '2025-12-29 17:37:18', 1),
(9, 'Queso de Tuna', 'Queso de tuna', 'procesado', 'queso', 95.00, 30.00, 'kg', '6952bbde370c2.jpeg', '2025-12-28 02:10:21', '2025-12-29 17:50:29', 1),
(10, 'Tuna Deshidratada', 'Tuna deshidratada natural sin azúcar', 'procesado', 'deshidratado', 120.00, 25.00, 'unidad', '6952bc215292d.jpg', '2025-12-28 02:10:21', '2025-12-29 17:36:33', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_admin`
--

CREATE TABLE `usuarios_admin` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('super_admin','admin') DEFAULT 'admin',
  `nombre_completo` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_acceso` datetime DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios_admin`
--

INSERT INTO `usuarios_admin` (`id`, `usuario`, `password`, `rol`, `nombre_completo`, `email`, `fecha_creacion`, `ultimo_acceso`, `activo`) VALUES
(1, 'admin', '81dc9bdb52d04dc20036dbd8313ed055', 'super_admin', 'Administrador Principal', 'admin@xata.com', '2025-12-28 02:10:21', '2025-12-29 12:05:48', 1),
(3, 'Litzy', '8a9cac3843b302e411c9a1a3fe299bcd', 'admin', 'Litzy Yanara', 'litzy@gmail.com', '2025-12-29 18:04:19', '2025-12-29 12:05:03', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fecha_venta` datetime DEFAULT current_timestamp(),
  `metodo_pago` enum('efectivo','tarjeta','transferencia') DEFAULT 'efectivo',
  `notas` text DEFAULT NULL,
  `usuario_registro` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `producto_id`, `cantidad`, `precio_unitario`, `total`, `fecha_venta`, `metodo_pago`, `notas`, `usuario_registro`) VALUES
(1, 1, 25.50, 25.00, 637.50, '2025-12-01 10:30:00', 'efectivo', NULL, NULL),
(2, 2, 15.00, 30.00, 450.00, '2025-12-02 14:20:00', 'tarjeta', NULL, NULL),
(3, 6, 10.00, 85.00, 850.00, '2025-12-05 11:15:00', 'transferencia', NULL, NULL),
(4, 7, 8.00, 80.00, 640.00, '2025-12-08 16:45:00', 'efectivo', NULL, NULL),
(5, 3, 20.00, 28.00, 560.00, '2025-12-10 09:30:00', 'tarjeta', NULL, NULL),
(6, 5, 12.00, 35.00, 420.00, '2025-12-12 13:20:00', 'efectivo', NULL, NULL),
(7, 8, 15.00, 65.00, 975.00, '2025-12-15 10:50:00', 'transferencia', NULL, NULL),
(8, 9, 5.00, 95.00, 475.00, '2025-12-18 15:30:00', 'efectivo', NULL, NULL),
(9, 1, 30.00, 25.00, 750.00, '2025-12-20 11:00:00', 'tarjeta', NULL, NULL),
(10, 10, 8.00, 120.00, 960.00, '2025-12-22 14:15:00', 'transferencia', NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `entradas_produccion`
--
ALTER TABLE `entradas_produccion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_entradas_fecha` (`fecha_entrega`),
  ADD KEY `idx_entradas_productor` (`productor_id`),
  ADD KEY `idx_entradas_producto` (`producto_id`);

--
-- Indices de la tabla `productores`
--
ALTER TABLE `productores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_productores_activo` (`activo`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_productos_tipo` (`tipo`),
  ADD KEY `idx_productos_categoria` (`categoria`),
  ADD KEY `idx_productos_activo` (`activo`);

--
-- Indices de la tabla `usuarios_admin`
--
ALTER TABLE `usuarios_admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_registro` (`usuario_registro`),
  ADD KEY `idx_fecha_venta` (`fecha_venta`),
  ADD KEY `idx_producto` (`producto_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `entradas_produccion`
--
ALTER TABLE `entradas_produccion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `productores`
--
ALTER TABLE `productores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `usuarios_admin`
--
ALTER TABLE `usuarios_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `entradas_produccion`
--
ALTER TABLE `entradas_produccion`
  ADD CONSTRAINT `entradas_produccion_ibfk_1` FOREIGN KEY (`productor_id`) REFERENCES `productores` (`id`),
  ADD CONSTRAINT `entradas_produccion_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`usuario_registro`) REFERENCES `usuarios_admin` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
