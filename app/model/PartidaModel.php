<?php

class PartidaModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function showPreguntaRandom($idUsuario) {
        $this->verificarQueElUsuarioContestoTodasLasPreguntas($idUsuario);

        $subconsulta = "(SELECT 1 FROM responde r WHERE r.idUsuario = " . $idUsuario . " AND r.idPregunta = p.idPregunta)";

        $sqlPregunta = "SELECT p.idPregunta, p.descripcion, c.nombre AS categoria, c.color
        FROM Pregunta p
        JOIN categoria c ON p.categoria = c.id
        WHERE NOT EXISTS" . $subconsulta . "
        ORDER BY RAND()
        LIMIT 1";
        $resultPregunta = $this->database->query($sqlPregunta);

        $pregunta = $resultPregunta[0];

        $sqlRespuestas = "SELECT idRespuesta, textoRespuesta FROM Respuesta WHERE idPregunta = " . $pregunta['idPregunta'];
        $resultRespuestas = $this->database->query($sqlRespuestas);

        //Agrupar las respuestas bajo sus preguntas respectivas
        $pregunta['respuestas'] = [];
        foreach ($resultRespuestas as $row) {
            $pregunta['respuestas'][] = [
                'idRespuesta' => $row['idRespuesta'],
                'textoRespuesta' => $row['textoRespuesta']
            ];
        }

        return $pregunta;
    }

    public function crearPartida($idUsuario) {
        $sql = "INSERT INTO partida (idUsuario, fecha) VALUES (" . $idUsuario . " , NOW())";
        $this->database->add($sql);
        $idPartida = $this->database->lastInsertId();
        return $idPartida;
    }

    public function sumarPuntaje($idPartida) {
        $sql = "UPDATE partida SET puntaje = puntaje + 1 WHERE idPartida = " . $idPartida;
        $this->database->add($sql);
    }


    public function validarRespuesta($idRespuestaSeleccionada, $idPartida, $idPregunta, $idUsuario) {
        $sql = "SELECT esCorrecta 
            FROM Respuesta 
            WHERE idRespuesta = " . $idRespuestaSeleccionada;


        $respuesta = $this->database->query($sql);
        $correcta = $respuesta[0]['esCorrecta'] == 1;

        if($correcta) {
            $this->sumarPuntaje($idPartida);
        }

        $this->registrarResultadoDePregunta($idPregunta, $correcta);
        $this->registrarRespuesta($idPregunta, $idUsuario);

        return $correcta;
    }

    public function registrarResultadoDePregunta($idPregunta, $esCorrecta) {
        if($esCorrecta) {
            $sql = "UPDATE pregunta SET correcto = correcto + 1 WHERE idPregunta = " . $idPregunta;
            $this->database->add($sql);
        }else{
            $sql = "UPDATE pregunta SET incorrecto = incorrecto + 1 WHERE idPregunta = " . $idPregunta;
            $this->database->add($sql);
        }
    }

    private function registrarRespuesta($idPregunta, $idUsuario){
        $sql = "INSERT INTO responde (idPregunta, idUsuario) VALUES (" . $idPregunta . ", " . $idUsuario . ")";
        $this->database->add($sql);
    }

    private function verificarQueElUsuarioContestoTodasLasPreguntas($idUsuario){
        $sql = "SELECT COUNT(*) AS cantidad
            FROM pregunta
            WHERE idPregunta NOT IN (SELECT idPregunta
                FROM responde
                WHERE idUsuario = " . $idUsuario . ")";
        $resultado = $this->database->query($sql);
        $contestoTodo = $resultado[0]['cantidad'] == 0;

        if($contestoTodo){
            $sql = "DELETE FROM responde WHERE idUsuario = " . $idUsuario;
            $this->database->add($sql);
        }
    }


}

