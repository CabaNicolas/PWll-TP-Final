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

    public function reportarPregunta($idPregunta, $idUsuario, $motivo) {
        $sql = "INSERT INTO reportes_preguntas (idPregunta, idUsuario, motivo) VALUES (" . $idPregunta . ", " . $idUsuario . ", '" . $motivo ."')";
        $this->database->add($sql);
    }

}
