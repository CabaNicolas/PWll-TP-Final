-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-10-2024 a las 19:18:32
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
                                                      (2, 'Geografia', '#8FBF8FFF');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partida`
--

CREATE TABLE `partida` (
                           `idPartida` int(11) NOT NULL,
                           `idUsuario` int(11) DEFAULT NULL,
                           `fecha` date DEFAULT NULL,
                           `puntaje` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



--
-- Estructura de tabla para la tabla `pregunta`
--

CREATE TABLE `pregunta` (
                            `idPregunta` int(11) NOT NULL,
                            `descripcion` varchar(255) DEFAULT NULL,
                            `categoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pregunta`
--

INSERT INTO `pregunta` (`idPregunta`, `descripcion`, `categoria`) VALUES
                                                                      (1, '¿Cuál es el planeta más grande del sistema solar?', 1),
                                                                      (2, '¿Cuál es el río más largo del mundo?', 2);

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
                                                                                          (8, 2, 'Misisipi', 0);

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
                           `password` varchar(20) NOT NULL,
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
    (1, 'leverattomariag@gmail.com', '123123', 'usuario2', '2000-02-20', 'Nombre Apellido', 'WhatsApp Image 2023-04-15 at 07.54.21.jpeg', 2, NULL, 'A');

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
-- Indices de la tabla `respuesta`
--
ALTER TABLE `respuesta`
    ADD PRIMARY KEY (`idRespuesta`);

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
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `partida`
--
ALTER TABLE `partida`
    MODIFY `idPartida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `pregunta`
--
ALTER TABLE `pregunta`
    MODIFY `idPregunta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `respuesta`
--
ALTER TABLE `respuesta`
    MODIFY `idRespuesta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
