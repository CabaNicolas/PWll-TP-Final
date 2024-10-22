<?php

class PartidaModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function showPreguntaRandom() {
        //Primero, seleccionar una pregunta aleatoria
        $sqlPregunta = "SELECT idPregunta, descripcion, categoria FROM Pregunta ORDER BY RAND() LIMIT 1";
        $resultPregunta = $this->database->query($sqlPregunta);

        if (empty($resultPregunta)) {
            return [];
        }

        $pregunta = $resultPregunta[0]; //Obtener la unica fila que se obtuvo en la consulta

        //Obtener todas las respuestas para la pregunta seleccionada
        $sqlRespuestas = "SELECT textoRespuesta FROM Respuesta WHERE idPregunta = " . $pregunta['idPregunta'];
        $resultRespuestas = $this->database->query($sqlRespuestas);

        //Agrupar las respuestas bajo sus preguntas respectivas
        $pregunta['respuestas'] = [];
        foreach ($resultRespuestas as $row) {
            $pregunta['respuestas'][] = $row['textoRespuesta'];
        }

        return $pregunta;
    }


}

