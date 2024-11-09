-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-11-2024 a las 18:16:10
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
(1, '¿Cuál es el planeta más grande del sistema solar?', 1, 0, 0),
(2, '¿Cuál es el río más largo del mundo?', 2, 0, 0),
(3, '¿Cuál es el planeta más cercano al Sol?', 1, 2, 0),
(4, '¿Qué gas es necesario para la respiración humana?', 1, 0, 1),
(5, '¿Cuál es la capital de Japón?', 2, 0, 0),
(6, '¿En qué continente se encuentra Egipto?', 2, 0, 1),
(7, '¿Quién fue el primer presidente de los Estados Unidos?', 3, 0, 1),
(8, '¿En qué año comenzó la Segunda Guerra Mundial?', 3, 0, 0),
(9, '¿Cuántos jugadores tiene un equipo de fútbol en el campo?', 4, 0, 0),
(10, '¿En qué deporte se utiliza una pelota de 3 agujeros?', 4, 0, 0),
(11, '¿Quién pintó la Mona Lisa?', 5, 1, 0),
(12, '¿A qué movimiento artístico pertenece el cuadro \"La noche estrellada\"?', 5, 2, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pregunta_sugerida`
--

CREATE TABLE `pregunta_sugerida` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `categoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `responde`
--

CREATE TABLE `responde` (
  `idUsuario` int(11) NOT NULL,
  `idPregunta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


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
(48, 12, 'Cubismo', 0);

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
(8, 'nico@caba.com', '$2y$10$a6xiV/zP6M162QNYLMNhz.zaWbcw/MxnfqMMueYsBcNCBm8C7mJwq', 'BigPaik', '1997-11-27', 'Nicolas Caba', 'Galactus.webp', 1, NULL, 'A');

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
  MODIFY `idPartida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de la tabla `pregunta`
--
ALTER TABLE `pregunta`
  MODIFY `idPregunta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `pregunta_sugerida`
--
ALTER TABLE `pregunta_sugerida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de la tabla `respuesta`
--
ALTER TABLE `respuesta`
  MODIFY `idRespuesta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT de la tabla `respuesta_sugerida`
--
ALTER TABLE `respuesta_sugerida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de la tabla `sexo`
--
ALTER TABLE `sexo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
