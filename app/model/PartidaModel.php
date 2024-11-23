<?php

class PartidaModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function crearPartida($idUsuario) {
        $sql = "INSERT INTO partida (idUsuario, fecha) VALUES (" . $idUsuario . " , NOW())";
        $this->database->add($sql);
        $idPartida = $this->database->lastInsertId();
        return $idPartida;
    }

    public function verificarSiElUsuarioTienePartidaAbierta($idUsuario) {
        $sql = "SELECT idPartida, preguntaActual FROM partida WHERE idUsuario = " . $idUsuario . " AND partidaFinalizada = 0 AND preguntaActual > 0 ORDER BY idPartida DESC LIMIT 1";
        $result = $this->database->query($sql);
        return $result;
    }

    public function registrarPreguntaActual($idPregunta, $idPartida) {
        $sql = "UPDATE partida SET preguntaActual = " . $idPregunta . " WHERE idPartida = " . $idPartida;
        $this->database->add($sql);
    }

    public function sumarPuntaje($idPartida) {
        $sql = "UPDATE partida SET puntaje = puntaje + 1 WHERE idPartida = " . $idPartida;
        $this->database->add($sql);
    }

    public function cerrarPartida($idPartida) {
        $sql = "UPDATE partida SET partidaFinalizada = 1 WHERE idPartida = " . $idPartida;
        $this->database->add($sql);
    }

    public function obtenerCantidadPartidasJugadas()
    {
        $sql = "SELECT count(idPartida) as cantidadPartidas
            FROM partida";
        return $this->database->query($sql);
    }

    //
    public function obtenerCantidadPartidasPorFecha($fecha)
    {
        $sql = "SELECT COUNT(idPartida) AS cantidadPartidas
            FROM partida
            WHERE fecha >= '$fecha'";

        return $this->database->query($sql);
    }
}

