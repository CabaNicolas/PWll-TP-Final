<?php
class PreguntaModel{

    private $database;

    public function __construct($database){
        $this->database = $database;
    }

    public function showPreguntaRandom($idUsuario, $dificultadUsuario) {
        $this->verificarQueElUsuarioContestoTodasLasPreguntas($idUsuario);

        $pregunta = $this->getPreguntaRandomQueTodaviaNoFueRespondidaPorElUsuario($idUsuario, $dificultadUsuario);

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

    private function getPreguntaRandomQueTodaviaNoFueRespondidaPorElUsuario($idUsuario, $dificultadUsuario) {
        $subconsulta = "(SELECT 1 FROM responde r WHERE r.idUsuario = " . $idUsuario . " AND r.idPregunta = p.idPregunta)";
        $dificultadPregunta = "(SELECT (correcto / (correcto + incorrecto)) * 100 FROM Pregunta p2 WHERE p2.idPregunta = p.idPregunta AND (correcto + incorrecto) >= 5)";

        $sqlPregunta = "SELECT p.idPregunta, p.descripcion, c.nombre AS categoria, c.color, COALESCE($dificultadPregunta, 50) AS dificultad
        FROM Pregunta p
        JOIN categoria c ON p.categoria = c.id
        WHERE NOT EXISTS" . $subconsulta . "
        ORDER BY RAND()";
        $resultPregunta = $this->database->query($sqlPregunta);

        $resultPregunta = $this->emparejarDificultad($dificultadUsuario, $resultPregunta);

        return $resultPregunta;
    }

    private function getRespuestas($pregunta){
        $sqlRespuestas = "SELECT idRespuesta, textoRespuesta FROM Respuesta WHERE idPregunta = " . $pregunta['idPregunta'] . " ORDER BY RAND()";
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

    private function getPreguntaPorId($idPregunta){
        $dificultadPregunta = "(SELECT (correcto / (correcto + incorrecto)) * 100 FROM Pregunta p2 WHERE p2.idPregunta = p.idPregunta AND (correcto + incorrecto) >= 5)";

        $sqlPregunta = "SELECT p.idPregunta, p.descripcion, c.nombre AS categoria, c.color, COALESCE($dificultadPregunta, 50) AS dificultad
        FROM Pregunta p
        JOIN categoria c ON p.categoria = c.id
        WHERE p.idPregunta = " . $idPregunta;
        $resultPregunta = $this->database->query($sqlPregunta);
        $pregunta = $resultPregunta[0];

        if($pregunta['dificultad'] >= 0 && $pregunta['dificultad'] <= 30){
            $pregunta['dificultad'] = 'Dificil';
        }else if($pregunta['dificultad'] >= 70 && $pregunta['dificultad'] <= 100){
            $pregunta['dificultad'] = 'Facil';
        }else{
            $pregunta['dificultad'] = 'Media';
        }


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

            $sqlInsertPregunta = "INSERT INTO Pregunta (descripcion, categoria) VALUES ('" .
                $preguntaSugerida['descripcion'] . "', '" .
                $preguntaSugerida['categoria'] . "')";
            $this->database->add($sqlInsertPregunta);

            $idPregunta = $this->database->lastInsertId();


            $sqlRespuestas = "SELECT textoRespuesta, esCorrecta FROM respuesta_sugerida WHERE idPreguntaSugerida = " . $idPreguntaSugerida;
            $respuestasSugeridas = $this->database->query($sqlRespuestas);

            foreach ($respuestasSugeridas as $respuesta) {
                $sqlInsertRespuesta = "INSERT INTO respuesta (idPregunta, textoRespuesta, esCorrecta) VALUES (" .
                    $idPregunta . ", '" .
                    $respuesta['textoRespuesta'] . "', " .
                    $respuesta['esCorrecta'] . ")";
                $this->database->add($sqlInsertRespuesta);
            }


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
             FROM pregunta";

        return $this->database->query($sql);
    }

    ////
    public function obtenerCantidadPreguntasActivasPorFecha($fecha)
    {
        $sql = "SELECT count(idPregunta) as cantidadPreguntas
        FROM pregunta
        WHERE fechaCreacion >= ?";

        return $this->database->query($sql, [$fecha]);
    }
    public function obtenerCantidadPreguntasSugeridas()
    {
        $sql = "SELECT count(id) as cantidadPreguntas
             FROM pregunta_sugerida";

        return $this->database->query($sql);
    }

////
    public function obtenerCantidadPreguntasSugeridasPorFecha($fecha)
    {
        $sql = "SELECT count(id) as cantidadPreguntas
            FROM pregunta_sugerida
            WHERE fechaSugerida >= ?";

        return $this->database->query($sql, [$fecha]);
    }

    public function obtenerCantidadPreguntasReportadas()
    {
        $sql = "SELECT count(idReporte) as cantidadPreguntas
             FROM reportes_preguntas";

        return $this->database->query($sql);
    }

////
    public function obtenerCantidadPreguntasReportadasPorFecha($fecha)
    {
        $sql = "SELECT count(idReporte) as cantidadPreguntas
            FROM reportes_preguntas
            WHERE fechaReporte >= ?";

        return $this->database->query($sql, [$fecha]);
    }



    public function obtenerCantidadSugeridasAprobadas()
    {
        $sql = "SELECT count(id) as cantidadPreguntas
             FROM pregunta_sugerida WHERE estado = 'aprobada'";

        return $this->database->query($sql);
    }
    public function obtenerCantidadSugeridasRechazadas()
    {
        $sql = "SELECT count(id) as cantidadPreguntas
             FROM pregunta_sugerida WHERE estado = 'rechazada'";

        return $this->database->query($sql);
    }
    public function obtenerCantidadSugeridasPendientes()
    {
        $sql = "SELECT count(id) as cantidadPreguntas
             FROM pregunta_sugerida WHERE estado = 'pendiente'";

        return $this->database->query($sql);
    }


    public function obtenerCantidadSugeridasAprobadasPorFecha($fecha)
    {
        $sql = "SELECT COUNT(id) AS cantidadPreguntas
            FROM pregunta_sugerida
            WHERE estado = 'aprobada' AND fechaSugerida >= ?";
        return $this->database->query($sql, [$fecha]);
    }

    public function obtenerCantidadSugeridasRechazadasPorFecha($fecha)
    {
        $sql = "SELECT COUNT(id) AS cantidadPreguntas
            FROM pregunta_sugerida
            WHERE estado = 'rechazada' AND fechaSugerida >= ?";
        return $this->database->query($sql, [$fecha]);
    }

    public function obtenerCantidadSugeridasPendientesPorFecha($fecha)
    {
        $sql = "SELECT COUNT(id) AS cantidadPreguntas
            FROM pregunta_sugerida
            WHERE estado = 'pendiente' AND fechaSugerida >= ?";
        return $this->database->query($sql, [$fecha]);
    }


    public function obtenerCantidadReportadasAprobadas()
    {
        $sql = "SELECT count(idReporte) as cantidadPreguntas
             FROM reportes_preguntas WHERE estado = 'aprobada'";

        return $this->database->query($sql);
    }
    public function obtenerCantidadReportadasRechazadas()
    {
        $sql = "SELECT count(idReporte) as cantidadPreguntas
             FROM reportes_preguntas WHERE estado = 'rechazada'";

        return $this->database->query($sql);
    }
    public function obtenerCantidadReportadasPendientes()
    {
        $sql = "SELECT count(idReporte) as cantidadPreguntas
             FROM reportes_preguntas WHERE estado = 'pendiente'";

        return $this->database->query($sql);
    }

    public function obtenerCantidadReportadasAprobadasPorFecha($fecha)
    {
        $sql = "SELECT COUNT(idReporte) AS cantidadPreguntas
            FROM reportes_preguntas
            WHERE estado = 'aprobada' AND fechaReporte >= ?";
        return $this->database->query($sql, [$fecha]);
    }

    public function obtenerCantidadReportadasRechazadasPorFecha($fecha)
    {
        $sql = "SELECT COUNT(idReporte) AS cantidadPreguntas
            FROM reportes_preguntas
            WHERE estado = 'rechazada' AND fechaReporte >= ?";
        return $this->database->query($sql, [$fecha]);
    }

    public function obtenerCantidadReportadasPendientesPorFecha($fecha)
    {
        $sql = "SELECT COUNT(idReporte) AS cantidadPreguntas
            FROM reportes_preguntas
            WHERE estado = 'pendiente' AND fechaReporte >= ?";
        return $this->database->query($sql, [$fecha]);
    }

    public function getPreguntasReportadas()
    {
        $sqlPreguntas = "
            SELECT 
                rp.idReporte,
                rp.idPregunta,
                rp.idUsuario,
                u.nombreUsuario AS nombreUsuario,
                p.descripcion AS textoPregunta,
                rp.motivo 
            FROM 
                reportes_preguntas rp
            JOIN 
                usuario u ON rp.idUsuario = u.id
            JOIN 
                pregunta p ON rp.idPregunta = p.idPregunta
            WHERE 
                rp.estado = 'pendiente'
        ";

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

    public function aprobarPreguntaReportada($idReporte)
    {
        $sqlPregunta = "SELECT idPregunta FROM reportes_preguntas WHERE idReporte = " . $idReporte;
        $resultPregunta = $this->database->query($sqlPregunta);
        $preguntaReportada = is_array($resultPregunta) ? $resultPregunta[0] : $resultPregunta->fetch_assoc();

        if ($preguntaReportada) {
            $idPregunta = $preguntaReportada['idPregunta'];


            $sqlDeleteRespuestas = "DELETE FROM respuesta WHERE idPregunta = " . $idPregunta;
            $this->database->add($sqlDeleteRespuestas);


            $sqlDeletePregunta = "DELETE FROM pregunta WHERE idPregunta = " . $idPregunta;
            $this->database->add($sqlDeletePregunta);


            $sqlUpdate = "UPDATE reportes_preguntas SET estado = 'aprobada' WHERE idReporte = " . $idReporte;
            $this->database->add($sqlUpdate);
        }
    }

    private function emparejarDificultad($dificultadUsuario, $preguntas) {
        $rangos = [
            "Facil" => [70, 100],
            "Media" => [31, 69],
            "Dificil" => [0, 30]
        ];


        $prioridades = [
            "Facil" => ["Facil", "Media", "Dificil"],
            "Media" => ["Media", "Facil", "Dificil"],
            "Dificil" => ["Dificil", "Media", "Facil"]
        ];

        if($dificultadUsuario == "Principiante"){
            $preguntas[0]['dificultad'] = "Principiante";
            return $preguntas[0];
        }

        $nivelesBusqueda = $prioridades[$dificultadUsuario];
        foreach ($nivelesBusqueda as $nivel) {
            $rango = $rangos[$nivel];
            foreach ($preguntas as $pregunta) {
                $dificultad = $pregunta['dificultad'];
                if ($dificultad >= $rango[0] && $dificultad <= $rango[1]) {
                    $pregunta['dificultad'] = $nivel;
                    return $pregunta;
                }
            }
        }
    }


}
