<?php
class PreguntaModel{

    private $database;

    public function __construct($database){
        $this->database = $database;
    }

    public function showPreguntaRandom($idUsuario) {
        $this->verificarQueElUsuarioContestoTodasLasPreguntas($idUsuario);

        $pregunta = $this->getPreguntaRandomQueTodaviaNoFueRespondidaPorElUsuario($idUsuario);

        $pregunta = $this->getRespuestas($pregunta);

        return $pregunta;
    }

    public function showPreguntaPorId($idPregunta) {
        $pregunta = $this->getPreguntaPorId($idPregunta);

        $pregunta = $this->getRespuestas($pregunta);

        return $pregunta;
    }

    public function validarRespuesta($idRespuestaSeleccionada, $idPregunta, $idUsuario) {
        $sql = "SELECT esCorrecta 
            FROM Respuesta 
            WHERE idRespuesta = " . $idRespuestaSeleccionada;


        $respuesta = $this->database->query($sql);
        $correcta = $respuesta[0]['esCorrecta'] == 1;

        $this->registrarResultadoDePregunta($idPregunta, $correcta);
        $this->registrarRespuesta($idPregunta, $idUsuario);

        return $correcta;
    }

    public function verificarSiLaPreguntaActualEstaRespondida($idPregunta, $idUsuario) {
        $sql = "SELECT 1 FROM responde WHERE idPregunta = " . $idPregunta . " AND idUsuario = " . $idUsuario;
        $result = $this->database->query($sql);
        return !empty($result);
    }

    public function registrarRespuesta($idPregunta, $idUsuario){
        $sql = "INSERT INTO responde (idPregunta, idUsuario) VALUES (" . $idPregunta . ", " . $idUsuario . ")";
        $this->database->add($sql);
    }

    public function obtenerCategorias(){
        $sql = "SELECT id, nombre FROM categoria";
        $result = $this->database->query($sql);
        return $result;
    }

    public function crearPregunta($consigna, $categoria, $respuestaCorrecta, $respuestas){
        $idPreguntaSugerida = $this->insertarRegistroEnTablaPreguntaSugerida($consigna, $categoria);
        $this->insertarRegistrosEnTablaRespuestaSugerida($respuestas, $idPreguntaSugerida, $respuestaCorrecta);
    }

    private function getPreguntaRandomQueTodaviaNoFueRespondidaPorElUsuario($idUsuario){
        $subconsulta = "(SELECT 1 FROM responde r WHERE r.idUsuario = " . $idUsuario . " AND r.idPregunta = p.idPregunta)";

        $sqlPregunta = "SELECT p.idPregunta, p.descripcion, c.nombre AS categoria, c.color
        FROM Pregunta p
        JOIN categoria c ON p.categoria = c.id
        WHERE NOT EXISTS" . $subconsulta . "
        ORDER BY RAND()
        LIMIT 1";
        $resultPregunta = $this->database->query($sqlPregunta);

        return $resultPregunta[0];
    }

    private function getRespuestas($pregunta){
        $sqlRespuestas = "SELECT idRespuesta, textoRespuesta FROM Respuesta WHERE idPregunta = " . $pregunta['idPregunta'];
        $resultRespuestas = $this->database->query($sqlRespuestas);

        $pregunta = $this->getArrayDeLaPreguntaConSusRespuestas($pregunta, $resultRespuestas);
        return $pregunta;
    }

    private function getArrayDeLaPreguntaConSusRespuestas($pregunta, $resultRespuestas){
        $pregunta['respuestas'] = [];
        foreach ($resultRespuestas as $row) {
            $pregunta['respuestas'][] = [
                'idRespuesta' => $row['idRespuesta'],
                'textoRespuesta' => $row['textoRespuesta']
            ];
        }
        return $pregunta;
    }

    private function getPreguntaPorId($idPregunta)
    {
        $sqlPregunta = "SELECT p.idPregunta, p.descripcion, c.nombre AS categoria, c.color
        FROM Pregunta p
        JOIN categoria c ON p.categoria = c.id
        WHERE p.idPregunta = " . $idPregunta;
        $resultPregunta = $this->database->query($sqlPregunta);

        $pregunta = $resultPregunta[0];
        return $pregunta;
    }

    private function registrarResultadoDePregunta($idPregunta, $correcta) {
        if($correcta) {
            $sql = "UPDATE pregunta SET correcto = correcto + 1 WHERE idPregunta = " . $idPregunta;
            $this->database->add($sql);
        }else{
            $sql = "UPDATE pregunta SET incorrecto = incorrecto + 1 WHERE idPregunta = " . $idPregunta;
            $this->database->add($sql);
        }
    }

    private function verificarQueElUsuarioContestoTodasLasPreguntas($idUsuario){
        $sql = "SELECT COUNT(*) AS cantidad
            FROM pregunta
            WHERE idPregunta NOT IN (SELECT idPregunta
                FROM responde
                WHERE idUsuario = " . $idUsuario . ")";
        $resultado = $this->database->query($sql);
        $contestoTodo = $resultado[0]['cantidad'] == 0;

        $this->resetearElHistorialDePreguntasContestadasPorElUsuario($contestoTodo, $idUsuario);
    }

    private function resetearElHistorialDePreguntasContestadasPorElUsuario($contestoTodo, $idUsuario){
        if ($contestoTodo) {
            $sql = "DELETE FROM responde WHERE idUsuario = " . $idUsuario;
            $this->database->add($sql);
        }
    }

    private function insertarRegistroEnTablaPreguntaSugerida($consigna, $categoria){
        $sql = "INSERT INTO pregunta_sugerida (descripcion, categoria, estado) 
            VALUES ('" . $consigna . "', " . $categoria . ", 'pendiente')";
        $this->database->add($sql);
        return $this->database->lastInsertId();
    }

    public function insertarRegistrosEnTablaRespuestaSugerida($respuestas, $idPreguntaSugerida, $respuestaCorrecta){
        foreach ($respuestas as $index => $respuesta) {
            $correcta = $index == $respuestaCorrecta - 1 ? 1 : 0;
            $sql = "INSERT INTO respuesta_sugerida (idPreguntaSugerida, textoRespuesta, esCorrecta) VALUES (" . $idPreguntaSugerida . ", '" . $respuesta . "', " . $correcta . ")";
            $this->database->add($sql);
        }
    }

    public function getPreguntasSugeridas()
    {
        $sqlPreguntas = "SELECT id, descripcion, categoria FROM Pregunta_sugerida WHERE estado = 'pendiente'";
        $resultPreguntas = $this->database->query($sqlPreguntas);


        $preguntasConRespuestas = [];


        foreach ($resultPreguntas as $pregunta) {

            $sqlRespuestas = "SELECT textoRespuesta, esCorrecta FROM Respuesta_sugerida WHERE idPreguntaSugerida = " . $pregunta['id'];
            $respuestas = $this->database->query($sqlRespuestas);

            $pregunta['respuestas'] = $respuestas;


            $preguntasConRespuestas[] = $pregunta;
        }

        return $preguntasConRespuestas;
    }

    public function aprobarPreguntaSugerida($idPreguntaSugerida)
    {
        $sqlPregunta = "SELECT descripcion, categoria FROM Pregunta_sugerida WHERE id = " . $idPreguntaSugerida;
        $resultPregunta = $this->database->query($sqlPregunta);
        $preguntaSugerida = is_array($resultPregunta) ? $resultPregunta[0] : $resultPregunta->fetch_assoc();

        if ($preguntaSugerida) {
            //Insertar la pregunta en la tabla Pregunta
            $sqlInsertPregunta = "INSERT INTO Pregunta (descripcion, categoria) VALUES ('" .
                $preguntaSugerida['descripcion'] . "', '" .
                $preguntaSugerida['categoria'] . "')";
            $this->database->add($sqlInsertPregunta);

            //Obtener el ID de la pregunta recién insertada
            $idPregunta = $this->database->lastInsertId();

            //Obtener las respuestas asociadas de "respuesta_sugerida"
            $sqlRespuestas = "SELECT textoRespuesta, esCorrecta FROM respuesta_sugerida WHERE idPreguntaSugerida = " . $idPreguntaSugerida;
            $respuestasSugeridas = $this->database->query($sqlRespuestas);

            //Insertar cada respuesta en la tabla `Respuesta` con el nuevo `idPregunta`
            foreach ($respuestasSugeridas as $respuesta) {
                $sqlInsertRespuesta = "INSERT INTO respuesta (idPregunta, textoRespuesta, esCorrecta) VALUES (" .
                    $idPregunta . ", '" .
                    $respuesta['textoRespuesta'] . "', " .
                    $respuesta['esCorrecta'] . ")";
                $this->database->add($sqlInsertRespuesta);
            }


            //Actualizar el estado de la pregunta sugerida a "aprobada"
            $sqlUpdate = "UPDATE Pregunta_sugerida SET estado = 'aprobada' WHERE id = " . $idPreguntaSugerida;
            $this->database->add($sqlUpdate);
        }
    }
    public function rechazarPreguntaSugerida($idPreguntaSugerida)
    {
        $sqlPregunta = "SELECT descripcion, categoria FROM Pregunta_sugerida WHERE id = " . $idPreguntaSugerida;
        $resultPregunta = $this->database->query($sqlPregunta);
        $preguntaSugerida = is_array($resultPregunta) ? $resultPregunta[0] : $resultPregunta->fetch_assoc();

        if ($preguntaSugerida) {
            //Cambiar estado a "Rechazada". No la eliminamos porque luego necesitamos los datos de esta tabla

            $sqlUpdate = "UPDATE Pregunta_sugerida SET estado = 'rechazada' WHERE id = " . $idPreguntaSugerida;
            $this->database->add($sqlUpdate);
        }
    }

    public function reportarPregunta($idPregunta, $idUsuario, $motivo) {
        $sql = "INSERT INTO reportes_preguntas (idPregunta, idUsuario, motivo, estado) VALUES (" . $idPregunta . ", " . $idUsuario . ", '" . $motivo ."' , 'pendiente')";
        $this->database->add($sql);

    }

    public function obtenerCantidadPreguntasActivas()
    {
        $sql = "SELECT count(idPregunta) as cantidadPreguntas
             FROM pregunta
             WHERE estado LIKE 'activa'";

        return $this->database->query($sql);
    }

    public function obtenerCantidadPreguntasCreadas()
    {
        $sql = "SELECT count(idPregunta) as cantidadPreguntas
             FROM pregunta";

        return $this->database->query($sql);
    }

    public function getPreguntasReportadas()
    {
        $sqlPreguntas = "SELECT idReporte, idPregunta, idUsuario, motivo FROM reportes_preguntas WHERE estado = 'pendiente'";
        $resultPreguntas = $this->database->query($sqlPreguntas);


        return $resultPreguntas;
    }

    public function rechazarPreguntaReportada($idReporte)
    {
        $sqlPregunta = "SELECT idPregunta, idUsuario, motivo FROM reportes_preguntas WHERE idReporte = " . $idReporte;
        $resultPregunta = $this->database->query($sqlPregunta);

        $preguntaReportada = is_array($resultPregunta) ? $resultPregunta[0] : $resultPregunta->fetch_assoc();

        if ($preguntaReportada) {
            $sqlUpdate = "UPDATE reportes_preguntas SET estado = 'rechazada' WHERE idReporte = " . $idReporte;
            $this->database->add($sqlUpdate);
        }
    }

    public function obtenerPreguntaReportada($idReporte)
    {
        $sqlPregunta = "SELECT p.idPregunta, p.descripcion, p.categoria 
                    FROM reportes_preguntas r
                    JOIN pregunta p ON r.idPregunta = p.idPregunta
                    WHERE r.idReporte = " . $idReporte;

        $resultPregunta = $this->database->query($sqlPregunta);
        $pregunta = is_array($resultPregunta) ? $resultPregunta[0] : $resultPregunta->fetch_assoc();

        $sqlRespuestas = "SELECT textoRespuesta, esCorrecta 
                      FROM respuesta 
                      WHERE idPregunta = " . $pregunta['idPregunta'];
        $pregunta['respuestas'] = $this->database->query($sqlRespuestas);

        return $pregunta;
    }



}
