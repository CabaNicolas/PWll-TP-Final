<?php

class PartidaModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function showPreguntaRandom() {
        //Seleccionar una pregunta aleatoria
        $sqlPregunta = "SELECT idPregunta, descripcion, categoria FROM Pregunta ORDER BY RAND() LIMIT 1";
        $resultPregunta = $this->database->query($sqlPregunta);

        if (empty($resultPregunta)) {
            return [];
        }

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


    public function validarRespuesta($idRespuestaSeleccionada) {
        // Consulta SQL para verificar si la respuesta seleccionada es la correcta
        $sql = "SELECT esCorrecta 
            FROM Respuesta 
            WHERE idRespuesta = " . $idRespuestaSeleccionada;

        // Ejecutar la consulta
        $resultado = $this->database->query($sql);

        // Verificar si el resultado contiene datos
        if (!empty($resultado)) {
            // Acceder al primer resultado (en caso de que query devuelva un array)
            $respuesta = $resultado[0];

            // Verificar si la respuesta es correcta
            return $respuesta['esCorrecta'] == 1;
        }

        // Si no hay resultados, la respuesta no es correcta
        return false;
    }




}

