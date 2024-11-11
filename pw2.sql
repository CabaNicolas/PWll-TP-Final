-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-11-2024 a las 16:49:54
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
(3, 'Historia', '#FF5733'),
(4, 'Deportes', '#33FF57'),
(5, 'Arte', '#5733FF');

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
(1, 3, '2024-11-10', 3, 10, 1),
(2, 3, '2024-11-10', 17, 12, 0),
(3, 3, '2024-11-10', 0, 0, 1),
(4, 3, '2024-11-10', 0, 10, 0),
(5, 3, '2024-11-10', 6, 6, 1),
(6, 3, '2024-11-10', 0, 0, 0),
(7, 3, '2024-11-10', 0, 0, 1),
(8, 3, '2024-11-10', 1, 4, 1),
(9, 3, '2024-11-10', 0, 7, 1),
(10, 3, '2024-11-10', 2, 11, 1),
(11, 2, '2024-11-11', 1, 1, 1),
(12, 2, '2024-11-11', 0, 27, 1);

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
  `estado` varchar(50) NOT NULL DEFAULT 'activa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pregunta`
--

INSERT INTO `pregunta` (`idPregunta`, `descripcion`, `categoria`, `correcto`, `incorrecto`, `estado`) VALUES
(1, '¿Cuál es el planeta más grande del sistema solar?', 1, 4, 0, 'activa'),
(2, '¿Cuál es el río más largo del mundo?', 2, 5, 0, 'activa'),
(3, '¿Cuál es el planeta más cercano al Sol?', 1, 5, 3, 'activa'),
(4, '¿Qué gas es necesario para la respiración humana?', 1, 3, 1, 'activa'),
(5, '¿Cuál es la capital de Japón?', 2, 4, 1, 'activa'),
(6, '¿En qué continente se encuentra Egipto?', 2, 5, 2, 'activa'),
(7, '¿Quién fue el primer presidente de los Estados Unidos?', 3, 4, 1, 'activa'),
(8, '¿En qué año comenzó la Segunda Guerra Mundial?', 3, 4, 0, 'activa'),
(9, '¿Cuántos jugadores tiene un equipo de fútbol en el campo?', 4, 4, 0, 'activa'),
(10, '¿En qué deporte se utiliza una pelota de 3 agujeros?', 4, 2, 3, 'activa'),
(11, '¿Quién pintó la Mona Lisa?', 5, 6, 0, 'activa'),
(12, '¿A qué movimiento artístico pertenece el cuadro \"La noche estrellada\"?', 5, 3, 3, 'activa'),
(27, 'En que parte se encuentra el obelisco', 2, 5, 1, 'activa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pregunta_sugerida`
--

CREATE TABLE `pregunta_sugerida` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `categoria` int(11) NOT NULL,
  `estado` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pregunta_sugerida`
--

INSERT INTO `pregunta_sugerida` (`id`, `descripcion`, `categoria`, `estado`) VALUES
(1, '¿De que color era el caballo blanco de San Martin al curzar la cordillera?', 3, 'pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportes_preguntas`
--

CREATE TABLE `reportes_preguntas` (
  `idReporte` int(11) NOT NULL,
  `idPregunta` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `motivo` text NOT NULL,
  `estado` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reportes_preguntas`
--

INSERT INTO `reportes_preguntas` (`idReporte`, `idPregunta`, `idUsuario`, `motivo`, `estado`) VALUES
(3, 1, 2, 'La descripcion esta mal', 'rechazada'),
(4, 27, 2, 'La descripcion esta mal escrita', 'pendiente');

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
(3, 3),
(3, 9),
(3, 6),
(3, 10),
(3, 27),
(3, 4),
(3, 7),
(3, 8),
(3, 2),
(3, 11),
(2, 11),
(2, 1),
(2, 27);

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
(1, 1, 'Gris', 0),
(2, 1, 'Negro', 0),
(3, 1, 'Blanco', 1),
(4, 1, 'Marón', 0);

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
  `idSexo` int(11) NOT NULL,
  `token_verificacion` varchar(100) DEFAULT NULL,
  `cuenta_verificada` char(1) NOT NULL DEFAULT 'I',
  `rol_fk` int(11) NOT NULL DEFAULT 3
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `mail`, `password`, `nombreUsuario`, `fechaNacimiento`, `nombreCompleto`, `foto`, `idSexo`, `token_verificacion`, `cuenta_verificada`, `rol_fk`) VALUES
(1, 'nico@caba.com', '$2y$10$a6xiV/zP6M162QNYLMNhz.zaWbcw/MxnfqMMueYsBcNCBm8C7mJwq', 'BigPaik', '1997-11-27', 'Nicolas Caba', 'Galactus.webp', 1, NULL, 'A', 2),
(2, 'nadia@gmail.com', '$2y$10$K42yPSwoNqHkuHfkZ591ReMlG9L1.YRdFV.paZ3.4AclCgtnXZ6yC', 'Nadi', '2000-08-09', 'Nadia Spinelli', 'avatar.jpg', 2, NULL, 'A', 3),
(3, 'leverattomariag@gmail.com', '$2y$10$yTJFgcwQRz.QEjEX7WmIAO0sPEfy/AaTOBFT4Z1s5r8b92a7g9/wy', 'gabi', '2000-02-20', 'Maria Gabriela Leveratto', 'avatar.jpg', 1, NULL, 'A', 3),
(4, 'admin@preguntones.com', '$2y$10$/MlVhHRMNPUmOv7YhIwarOjE./iist0xGU1JbRr3eC/hqEv9iCY.q', '111111', '2000-02-20', 'Admin', 'avatar.jpg', 3, NULL, 'A', 1),
(5, 'editor@gmail.com', '$2y$10$BGjKYi2Wr.Wi.mutabU0HuyjjjIeqzWMC1R2n6g.TfxqVz1VVo3U.', 'Editor', '2000-02-20', 'Editor', 'avatar.jpg', 3, NULL, 'A', 2);

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
  MODIFY `idPartida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `pregunta`
--
ALTER TABLE `pregunta`
  MODIFY `idPregunta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `pregunta_sugerida`
--
ALTER TABLE `pregunta_sugerida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `reportes_preguntas`
--
ALTER TABLE `reportes_preguntas`
  MODIFY `idReporte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `respuesta`
--
ALTER TABLE `respuesta`
  MODIFY `idRespuesta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de la tabla `respuesta_sugerida`
--
ALTER TABLE `respuesta_sugerida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
