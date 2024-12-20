-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-11-2024 a las 19:17:31
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
-- Base de datos: `pw2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `color` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id`, `nombre`, `color`) VALUES
(1, 'Ciencia', '#1E86ACFF'),
(2, 'Geografia', '#8FBF8FFF'),
(3, 'Historia', '#ede9b4'),
(4, 'Deportes', '#e8968b\n\n'),
(5, 'Arte', '#5589c4');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partida`
--

CREATE TABLE `partida` (
  `idPartida` int(11) NOT NULL,
  `idUsuario` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `puntaje` int(11) DEFAULT 0,
  `preguntaActual` int(11) NOT NULL DEFAULT 0,
  `partidaFinalizada` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `partida`
--

INSERT INTO `partida` (`idPartida`, `idUsuario`, `fecha`, `puntaje`, `preguntaActual`, `partidaFinalizada`) VALUES
(1, 1, '2024-11-01', 0, 9, 1),
(2, 1, '2024-10-16', 0, 12, 1),
(3, 1, '2024-11-16', 2, 27, 1),
(4, 1, '2024-05-16', 1, 4, 1),
(5, 1, '2024-11-16', 3, 8, 1),
(6, 1, '2023-11-16', 3, 4, 1),
(7, 9, '2023-11-16', 0, 11, 1),
(8, 1, '2023-11-19', 1, 7, 1),
(9, 1, '2024-11-19', 0, 5, 1),
(10, 1, '2024-11-19', 0, 6, 1),
(11, 1, '2024-11-21', 1, 1, 1),
(12, 1, '2024-11-21', 0, 9, 1),
(13, 1, '2024-09-21', 0, 27, 1),
(14, 2, '2024-11-24', 3, 11, 1),
(15, 2, '2024-11-24', 0, 2, 1),
(16, 2, '2024-11-24', 0, 4, 1),
(17, 2, '2024-11-24', 4, 7, 1),
(18, 3, '2024-11-24', 0, 11, 1),
(19, 3, '2024-11-24', 9, 10, 1),
(20, 3, '2024-11-24', 0, 7, 0),
(21, 3, '2024-11-24', 4, 11, 0),
(22, 3, '2024-11-24', 14, 11, 1),
(23, 3, '2024-11-24', 7, 6, 0),
(24, 2, '2024-11-24', 4, 12, 1),
(25, 2, '2024-11-24', 4, 9, 1),
(26, 1, '2024-11-25', 0, 4, 0),
(27, 2, '2024-11-25', 4, 3, 1),
(28, 2, '2024-11-25', 0, 12, 1),
(29, 2, '2024-11-25', 0, 11, 1),
(30, 2, '2024-11-25', 0, 27, 1),
(31, 2, '2024-11-25', 0, 10, 1),
(32, 2, '2024-11-25', 1, 4, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pregunta`
--

CREATE TABLE `pregunta` (
  `idPregunta` int(11) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `categoria` int(11) NOT NULL,
  `correcto` int(11) NOT NULL DEFAULT 0,
  `incorrecto` int(11) NOT NULL DEFAULT 0,
  `fechaCreacion` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pregunta`
--

INSERT INTO `pregunta` (`idPregunta`, `descripcion`, `categoria`, `correcto`, `incorrecto`, `fechaCreacion`) VALUES
(1, '¿Cuál es el planeta más grande del sistema solar?', 1, 12, 1, '2024-11-16'),
(2, '¿Cuál es el río más largo del mundo?', 2, 8, 3, '2024-09-16'),
(3, '¿Cuál es el planeta más cercano al Sol?', 1, 14, 6, '2020-11-16'),
(4, '¿Qué gas es necesario para la respiración humana?', 1, 12, 8, '2020-11-16'),
(5, '¿Cuál es la capital de Japón?', 2, 16, 4, '2023-11-16'),
(6, '¿En qué continente se encuentra Egipto?', 2, 17, 3, '2024-11-23'),
(7, '¿Quién fue el primer presidente de los Estados Unidos?', 3, 15, 4, '2024-11-23'),
(8, '¿En qué año comenzó la Segunda Guerra Mundial?', 3, 15, 3, '2024-11-16'),
(9, '¿Cuántos jugadores tiene un equipo de fútbol en el campo?', 4, 15, 2, '2024-11-16'),
(10, '¿En qué deporte se utiliza una pelota de 3 agujeros?', 4, 16, 47, '2024-11-16'),
(11, '¿Quién pintó la Mona Lisa?', 5, 18, 55, '2024-11-16'),
(12, '¿A qué movimiento artístico pertenece el cuadro \"La noche estrellada\"?', 5, 15, 49, '2024-11-16'),
(27, 'En que parte se encuentra el obelisco', 2, 12, 51, '2024-11-16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pregunta_sugerida`
--

CREATE TABLE `pregunta_sugerida` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `categoria` int(11) NOT NULL,
  `estado` text NOT NULL,
  `fechaSugerida` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pregunta_sugerida`
--

INSERT INTO `pregunta_sugerida` (`id`, `descripcion`, `categoria`, `estado`, `fechaSugerida`) VALUES
(5, 'En que parte se encuentra el obelisco', 2, 'aprobada', '2020-11-16'),
(16, 'que ciudad es conocida como \"la feliz\"', 3, 'rechazada', '2024-11-22'),
(18, '¿Donde queda el Aconcagua?', 2, 'pendiente', '2024-11-25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportes_preguntas`
--

CREATE TABLE `reportes_preguntas` (
  `idReporte` int(11) NOT NULL,
  `idPregunta` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `motivo` text NOT NULL,
  `estado` varchar(20) NOT NULL,
  `fechaReporte` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reportes_preguntas`
--

INSERT INTO `reportes_preguntas` (`idReporte`, `idPregunta`, `idUsuario`, `motivo`, `estado`, `fechaReporte`) VALUES
(1, 9, 8, 'No me gusta el futbol', 'pendiente', '2024-11-16'),
(5, 8, 8, 'me parece ofensivo', 'pendiente', '2020-11-16'),
(6, 2, 8, 'me parece que esta mal', 'pendiente', '2024-09-16'),
(7, 2, 2, 'no me gusta', 'rechazada', '2024-11-24'),
(8, 4, 2, 'saf', 'pendiente', '2024-11-24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `responde`
--

CREATE TABLE `responde` (
  `idUsuario` int(11) NOT NULL,
  `idPregunta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `responde`
--

INSERT INTO `responde` (`idUsuario`, `idPregunta`) VALUES
(3, 10),
(3, 27),
(3, 12),
(3, 11),
(3, 4),
(3, 2),
(3, 3),
(3, 9),
(3, 8),
(3, 7),
(3, 1),
(2, 12),
(2, 11),
(2, 27),
(2, 10),
(2, 3),
(2, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuesta`
--

CREATE TABLE `respuesta` (
  `idRespuesta` int(11) NOT NULL,
  `idPregunta` int(11) DEFAULT NULL,
  `textoRespuesta` varchar(255) DEFAULT NULL,
  `esCorrecta` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `respuesta`
--

INSERT INTO `respuesta` (`idRespuesta`, `idPregunta`, `textoRespuesta`, `esCorrecta`) VALUES
(1, 1, 'Marte', 0),
(2, 1, 'Tierra', 0),
(3, 1, 'Júpiter', 1),
(4, 1, 'Venus', 0),
(5, 2, 'Amazonas', 0),
(6, 2, 'Nilo', 1),
(7, 2, 'Yangtsé', 0),
(8, 2, 'Misisipi', 0),
(9, 3, 'Mercurio', 1),
(10, 3, 'Venus', 0),
(11, 3, 'Tierra', 0),
(12, 3, 'Marte', 0),
(13, 4, 'Oxígeno', 1),
(14, 4, 'Hidrógeno', 0),
(15, 4, 'Dióxido de carbono', 0),
(16, 4, 'Metano', 0),
(17, 5, 'Tokio', 1),
(18, 5, 'Osaka', 0),
(19, 5, 'Kioto', 0),
(20, 5, 'Nara', 0),
(21, 6, 'África', 1),
(22, 6, 'Asia', 0),
(23, 6, 'América', 0),
(24, 6, 'Europa', 0),
(25, 7, 'George Washington', 1),
(26, 7, 'Abraham Lincoln', 0),
(27, 7, 'John Adams', 0),
(28, 7, 'Thomas Jefferson', 0),
(29, 8, '1939', 1),
(30, 8, '1914', 0),
(31, 8, '1945', 0),
(32, 8, '1929', 0),
(33, 9, '11', 1),
(34, 9, '10', 0),
(35, 9, '12', 0),
(36, 9, '9', 0),
(37, 10, 'Boliche', 1),
(38, 10, 'Golf', 0),
(39, 10, 'Baloncesto', 0),
(40, 10, 'Hockey', 0),
(41, 11, 'Leonardo da Vinci', 1),
(42, 11, 'Pablo Picasso', 0),
(43, 11, 'Vincent van Gogh', 0),
(44, 11, 'Claude Monet', 0),
(45, 12, 'Postimpresionismo', 1),
(46, 12, 'Renacimiento', 0),
(47, 12, 'Barroco', 0),
(48, 12, 'Cubismo', 0),
(49, 26, 'sf', 0),
(50, 26, 'sf', 0),
(51, 26, 'dsf', 1),
(52, 26, 'dsf', 0),
(53, 27, 'CABA', 1),
(54, 27, 'Neuquen', 0),
(55, 27, 'Rio Negro', 0),
(56, 27, 'Formosa', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuesta_sugerida`
--

CREATE TABLE `respuesta_sugerida` (
  `id` int(11) NOT NULL,
  `idPreguntaSugerida` int(11) NOT NULL,
  `textoRespuesta` varchar(255) NOT NULL,
  `esCorrecta` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `respuesta_sugerida`
--

INSERT INTO `respuesta_sugerida` (`id`, `idPreguntaSugerida`, `textoRespuesta`, `esCorrecta`) VALUES
(5, 18, 'CABA', 0),
(6, 18, 'Mendoza', 1),
(7, 18, 'Rio Negro', 0),
(8, 18, 'Formosa', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id` int(11) NOT NULL,
  `nombre` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id`, `nombre`) VALUES
(1, 'admin'),
(2, 'editor'),
(3, 'normal');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sexo`
--

CREATE TABLE `sexo` (
  `id` int(11) NOT NULL,
  `nombre` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sexo`
--

INSERT INTO `sexo` (`id`, `nombre`) VALUES
(1, 'Masculino'),
(2, 'Femenino'),
(3, 'Prefiero no decirlo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `mail` varchar(40) NOT NULL,
  `password` varchar(65) NOT NULL,
  `nombreUsuario` varchar(20) NOT NULL,
  `fechaNacimiento` date NOT NULL,
  `nombreCompleto` varchar(30) NOT NULL,
  `foto` varchar(50) NOT NULL,
  `latitud` decimal(10,8) DEFAULT NULL,
  `longitud` decimal(10,8) DEFAULT NULL,
  `idSexo` int(11) NOT NULL,
  `token_verificacion` varchar(100) DEFAULT NULL,
  `cuenta_verificada` char(1) NOT NULL DEFAULT 'I',
  `rol_fk` int(11) NOT NULL DEFAULT 3,
  `preguntasEntregadas` int(11) NOT NULL DEFAULT 0,
  `respuestasCorrectas` int(11) NOT NULL DEFAULT 0,
  `fechaRegistro` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `mail`, `password`, `nombreUsuario`, `fechaNacimiento`, `nombreCompleto`, `foto`, `latitud`, `longitud`, `idSexo`, `token_verificacion`, `cuenta_verificada`, `rol_fk`, `preguntasEntregadas`, `respuestasCorrectas`, `fechaRegistro`) VALUES
(1, 'nico@caba.com', '$2y$10$71GYf6s2LofwM2GtKIKpZOMlewWP8egyan6vvFMtTWqY1w797/VpW', 'BigPaik', '1997-11-27', 'Nicolas Caba', 'Galactus.webp', -34.64789601, -58.61755371, 1, NULL, 'A', 3, 28, 13, '2024-11-18'),
(2, 'nadia@gmail.com', '$2y$10$xn4JC2FimaWuMRBbUcpRyeqmfKacN5Mf9YLT5.NNG0Ry9bfQqi8ai', 'Nadia', '2000-08-09', 'Nadia Spinelli', 'avatar.jpg', 0.00000000, 0.00000000, 1, NULL, 'A', 3, 52, 40, '2022-11-18'),
(3, 'leverattomariag@gmail.com', '$2y$10$yTJFgcwQRz.QEjEX7WmIAO0sPEfy/AaTOBFT4Z1s5r8b92a7g9/wy', 'gabi', '2000-02-20', 'Maria Gabriela Leveratto', 'avatar.jpg', 0.00000000, 0.00000000, 2, NULL, 'A', 3, 38, 34, '2024-01-01'),
(4, 'admin@preguntones.com', '$2y$10$/MlVhHRMNPUmOv7YhIwarOjE./iist0xGU1JbRr3eC/hqEv9iCY.q', '111111', '2000-02-20', 'Admin', 'avatar.jpg', 0.00000000, 0.00000000, 3, NULL, 'A', 1, 0, 0, '2024-11-18'),
(5, 'editor@gmail.com', '$2y$10$BGjKYi2Wr.Wi.mutabU0HuyjjjIeqzWMC1R2n6g.TfxqVz1VVo3U.', 'Editor', '2000-02-20', 'Editor', 'avatar.jpg', 0.00000000, 0.00000000, 3, NULL, 'A', 2, 0, 0, '2024-11-18'),
(9, 'test@map.com', '$2y$10$cjBB/uqDvpL.XQZLdAksJeTomN04.q26DdAoyINVWJ3q9CcX.pUxC', 'TestMap', '2222-02-22', 'Nicolas Caba', '', -34.65126507, -58.62204909, 1, NULL, 'A', 3, 1, 0, '2024-10-25'),
(10, 'usuario@1.com', '$2y$10$jFmjaE7C.pgS9pMwfZhEW.bKQhFFgbl5yJFVRr/z17w6NboHg7pJW', 'usuario1', '2222-02-22', 'Usuario1', '', -34.64881393, -58.43817466, 1, '821011171b', 'I', 3, 0, 0, '2024-11-20'),
(11, 'sofia@gmail.com', '$2y$10$UtPPY0znr8o9ll80RHNVduEQQHasHVP2K3OBejPg.a19zYwKYZ3ty', 'sofi', '2004-10-09', 'Sofia', '', 42.01658300, 1.80710867, 1, NULL, 'A', 3, 0, 0, '2024-11-23'),
(12, 'usuario2@gmail.com', '$2y$10$3wiUdOgGLeWHlmwQHbqCpOcc3qOOP2OVPaKpEMSFHXRY5lFDRz9Eu', 'Usuario2', '1900-10-09', 'Usuario2', '', 42.01658300, 1.80710867, 1, NULL, 'A', 3, 0, 0, '2024-11-23'),
(13, 'sdf@cfd.com', '$2y$10$MTf77Ln4wpu8XnOy4e7iMeQy0a7zbxdJlvHCGdYu7MRX82ssoV8aC', 'sfsf', '3223-02-23', 'sdf', 'icono.png', -34.67587916, -58.39968170, 3, '53af821498', 'I', 3, 0, 0, '2024-11-24'),
(14, 'nadia.belen98@gmail.com', '$2y$10$Uxi5ChADwj97cE5hPSXguOXZFWoPK/L9YpAYDx9RX4gE0shuQZjza', 'hola', '3333-02-23', 'sdf', 'avatar.jpg', NULL, NULL, 1, '1ea487098b', 'I', 3, 0, 0, '2024-11-24'),
(15, 'dsf@sf.com', '$2y$10$vtFatWcI7xzJSP84mkB59OGpu/9S8wHDYjrru3Ksq.34bDCAbzeiu', 'dfsf', '3333-03-31', 'dsfs', 'avatar.jpg', NULL, NULL, 1, 'ed96ce145a', 'I', 3, 0, 0, '2024-11-24');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `partida`
--
ALTER TABLE `partida`
  ADD PRIMARY KEY (`idPartida`);

--
-- Indices de la tabla `pregunta`
--
ALTER TABLE `pregunta`
  ADD PRIMARY KEY (`idPregunta`);

--
-- Indices de la tabla `pregunta_sugerida`
--
ALTER TABLE `pregunta_sugerida`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reportes_preguntas`
--
ALTER TABLE `reportes_preguntas`
  ADD PRIMARY KEY (`idReporte`);

--
-- Indices de la tabla `respuesta`
--
ALTER TABLE `respuesta`
  ADD PRIMARY KEY (`idRespuesta`);

--
-- Indices de la tabla `respuesta_sugerida`
--
ALTER TABLE `respuesta_sugerida`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sexo`
--
ALTER TABLE `sexo`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Sexo_Usuario_FK` (`idSexo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `partida`
--
ALTER TABLE `partida`
  MODIFY `idPartida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `pregunta`
--
ALTER TABLE `pregunta`
  MODIFY `idPregunta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `pregunta_sugerida`
--
ALTER TABLE `pregunta_sugerida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `reportes_preguntas`
--
ALTER TABLE `reportes_preguntas`
  MODIFY `idReporte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `respuesta`
--
ALTER TABLE `respuesta`
  MODIFY `idRespuesta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de la tabla `respuesta_sugerida`
--
ALTER TABLE `respuesta_sugerida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `sexo`
--
ALTER TABLE `sexo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `Sexo_Usuario_FK` FOREIGN KEY (`idSexo`) REFERENCES `sexo` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
