-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-11-2024 a las 17:13:31
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
(1, 9, '2024-11-09', 0, 9, 0),
(2, 9, '2024-11-09', 0, 0, 0),
(3, 9, '2024-11-09', 0, 0, 1),
(4, 9, '2024-11-10', 4, 25, 1),
(5, 9, '2024-11-10', 1, 7, 0),
(6, 9, '2024-11-10', 1, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pregunta`
--

CREATE TABLE `pregunta` (
  `idPregunta` int(11) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `categoria` int(11) NOT NULL,
  `correcto` int(11) NOT NULL DEFAULT 0,
  `incorrecto` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pregunta`
--

INSERT INTO `pregunta` (`idPregunta`, `descripcion`, `categoria`, `correcto`, `incorrecto`) VALUES
(1, '¿Cuál es el planeta más grande del sistema solar?', 1, 1, 0),
(2, '¿Cuál es el río más largo del mundo?', 2, 0, 0),
(3, '¿Cuál es el planeta más cercano al Sol?', 1, 2, 0),
(4, '¿Qué gas es necesario para la respiración humana?', 1, 0, 1),
(5, '¿Cuál es la capital de Japón?', 2, 1, 0),
(6, '¿En qué continente se encuentra Egipto?', 2, 0, 1),
(7, '¿Quién fue el primer presidente de los Estados Unidos?', 3, 1, 1),
(8, '¿En qué año comenzó la Segunda Guerra Mundial?', 3, 0, 0),
(9, '¿Cuántos jugadores tiene un equipo de fútbol en el campo?', 4, 0, 0),
(10, '¿En qué deporte se utiliza una pelota de 3 agujeros?', 4, 1, 0),
(11, '¿Quién pintó la Mona Lisa?', 5, 2, 0),
(12, '¿A qué movimiento artístico pertenece el cuadro \"La noche estrellada\"?', 5, 2, 0),
(25, 'Hola como estas', 2, 0, 0),
(26, 'holissssssss', 1, 1, 0),
(27, 'En que parte se encuentra el obelisco', 2, 0, 0);

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
(5, 'En que parte se encuentra el obelisco', 2, 'aprobada'),
(15, '¿Donde queda el Aconcagua?', 2, 'pendiente'),
(16, 'que ciudad es conocida como \"la feliz\"', 3, 'rechazada');

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
(9, 9),
(9, 1),
(9, 10),
(9, 11),
(9, 5),
(9, 25),
(9, 26),
(9, 7),
(9, 7);

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
(5, 2, 'Hola', 0),
(6, 2, 'Hola2', 0),
(7, 2, 'Hola3', 0),
(8, 2, 'Hola4', 1),
(9, 3, 'sd', 0),
(10, 3, 'sd', 0),
(11, 3, 'sd', 0),
(12, 3, 'sd', 1),
(13, 4, 'sf', 0),
(14, 4, 'sf', 0),
(15, 4, 'dsf', 1),
(16, 4, 'dsf', 0),
(17, 5, 'CABA', 1),
(18, 5, 'Neuquen', 0),
(19, 5, 'Rio Negro', 0),
(20, 5, 'Formosa', 0),
(21, 6, 'df', 1),
(22, 6, 'dsf', 0),
(23, 6, 'sd', 0),
(24, 6, 'df', 0),
(25, 7, '3', 0),
(26, 7, '3', 1),
(27, 7, '3', 0),
(28, 7, '3', 0),
(29, 8, '3', 0),
(30, 8, '3', 1),
(31, 8, '3', 0),
(32, 8, '3', 0),
(33, 9, 'as', 0),
(34, 9, 'as', 0),
(35, 9, 'as', 1),
(36, 9, 'as', 0),
(37, 10, 'as', 0),
(38, 10, 'as', 0),
(39, 10, 'as', 0),
(40, 10, 'as', 1),
(41, 11, 'as', 0),
(42, 11, 'as', 1),
(43, 11, 'as', 0),
(44, 11, 'as', 0),
(45, 12, 'CABA', 0),
(46, 12, 'Neuquen', 1),
(47, 12, 'Rio Negro', 0),
(48, 12, 'Formosa', 0),
(49, 13, 'asd', 0),
(50, 13, 'asd', 1),
(51, 13, 'as', 0),
(52, 13, 'as', 0),
(53, 14, 'CABA', 0),
(54, 14, 'sas', 0),
(55, 14, 'as', 1),
(56, 14, 'sa', 0),
(57, 15, 'Jujuy', 0),
(58, 15, 'Buenos Aires', 0),
(59, 15, 'Mendoza', 0),
(60, 15, 'Neuquen', 1),
(61, 16, 'san clemente', 0),
(62, 16, 'san bernardo', 1),
(63, 16, 'santa teresita', 0),
(64, 16, 'las toninas', 0);

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
  `cuenta_verificada` char(1) NOT NULL DEFAULT 'I'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `mail`, `password`, `nombreUsuario`, `fechaNacimiento`, `nombreCompleto`, `foto`, `idSexo`, `token_verificacion`, `cuenta_verificada`) VALUES
(8, 'nico@caba.com', '$2y$10$a6xiV/zP6M162QNYLMNhz.zaWbcw/MxnfqMMueYsBcNCBm8C7mJwq', 'BigPaik', '1997-11-27', 'Nicolas Caba', 'Galactus.webp', 1, NULL, 'A'),
(9, 'nadia@gmail.com', '$2y$10$K42yPSwoNqHkuHfkZ591ReMlG9L1.YRdFV.paZ3.4AclCgtnXZ6yC', 'Nadi', '2000-08-09', 'Nadia Spinelli', 'avatar.jpg', 2, NULL, 'A');

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
  MODIFY `idPartida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `pregunta`
--
ALTER TABLE `pregunta`
  MODIFY `idPregunta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `pregunta_sugerida`
--
ALTER TABLE `pregunta_sugerida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `respuesta`
--
ALTER TABLE `respuesta`
  MODIFY `idRespuesta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT de la tabla `respuesta_sugerida`
--
ALTER TABLE `respuesta_sugerida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de la tabla `sexo`
--
ALTER TABLE `sexo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
