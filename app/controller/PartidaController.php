<?php

class PartidaController
{

    private $partidaModel;
    private $presenter;
    private $preguntaModel;
    private $usuarioModel;
    public function __construct($usuarioModel, $partidaModel, $preguntaModel, $presenter)
    {
        $this->usuarioModel = $usuarioModel;
        $this->partidaModel = $partidaModel;
        $this->presenter = $presenter;
        $this->preguntaModel = $preguntaModel;
    }

    public function crearPartida(){
        $idUsuario = $_SESSION['id'];
        $_SESSION['idPartida'] = $this->partidaModel->crearPartida($idUsuario);
        Redirecter::redirect('/partida/showPregunta');
    }


    public function showPregunta() {

        $idUsuario = $_SESSION['id'];
        $idPartida = $_SESSION['idPartida'];

        $this->inicializarTiempo();

        $partidaAbierta = $this->partidaModel->verificarSiElUsuarioTienePartidaAbierta($idUsuario);
        $preguntaActualRespondida = $this->verificarSiLaPreguntaActualEstaRespondida($partidaAbierta, $idUsuario);


        if(!empty($partidaAbierta) && !$preguntaActualRespondida){
            $data['preguntasYRespuestas'] = $this->preguntaModel->showPreguntaPorId($partidaAbierta[0]['preguntaActual'], $partidaAbierta[0]['idPartida']);
        }else{
            $dificultadUsuario = $this->usuarioModel->obtenerDificultadUsuario($idUsuario);
            $data['preguntasYRespuestas'] = $this->preguntaModel->showPreguntaRandom($idUsuario, $dificultadUsuario);
            $this->partidaModel->registrarPreguntaActual($data['preguntasYRespuestas']['idPregunta'], $idPartida);
            $this->usuarioModel->aumentarLaCantidadDePreguntasMostradas($idUsuario);
        }
        $_SESSION['idPregunta'] = $data['preguntasYRespuestas']['idPregunta'];

        $data['mail'] = $_SESSION['mail'];
        $data['tiempo_restante'] = $this->tiempoRestante();
        $this->presenter->show('partida', $data);
    }

    public function validarRespuesta() {
        $idRespuestaSeleccionada = $_POST['respuesta'];
        $idPartida = $_SESSION['idPartida'];
        $idPregunta = $_SESSION['idPregunta'];
        $idUsuario = $_SESSION['id'];

        $esCorrecta = $this->preguntaModel->validarRespuesta($idRespuestaSeleccionada, $idPregunta, $idUsuario);

        $this->sumarPuntosSiLaRespuestaEsCorrecta($esCorrecta, $idPartida);

        if($esCorrecta){
            $this->usuarioModel->aumentarLaCantidadDeRespuestasCorrectas($idUsuario);
        }

        unset($_SESSION['tiempo_inicio']);

        echo json_encode(['correcta' => $esCorrecta, 'idRespuesta' => $idRespuestaSeleccionada]);
        exit;
    }


    public function cerrarPartida(){
        $this->partidaModel->cerrarPartida($_SESSION['idPartida']);
        unset($_SESSION['idPartida'], $_SESSION['idPregunta'], $_SESSION['tiempo_inicio']);
        Redirecter::redirect('/usuario/showLobby');
    }

    public function preguntaInvalidadaPorExpiracionDeTiempo(){
        $idPregunta = $_SESSION['idPregunta'];
        $idUsuario = $_SESSION['id'];
        $this->preguntaModel->registrarRespuesta($idPregunta, $idUsuario);
        $this->cerrarPartida();
    }

    private function inicializarTiempo()
    {
        if (!isset($_SESSION['tiempo_inicio'])) {
            $_SESSION['tiempo_inicio'] = time();
        }
    }

    private function tiempoTranscurrido(){
        return time() - $_SESSION['tiempo_inicio'];
    }

    private function tiempoRestante(){
        return $this->tiempoMaximo() - $this->tiempoTranscurrido();
    }

    private function tiempoMaximo(){
        return 10;
    }

    private function sumarPuntosSiLaRespuestaEsCorrecta($esCorrecta, $idPartida){
        if ($esCorrecta) {
            $this->partidaModel->sumarPuntaje($idPartida);
        }
    }

    private function verificarSiLaPreguntaActualEstaRespondida($partidaAbierta, $idUsuario){
        $preguntaActualRespondida = false;

        if (!empty($partidaAbierta)) {
            $preguntaActualRespondida = $this->preguntaModel->verificarSiLaPreguntaActualEstaRespondida($partidaAbierta[0]['preguntaActual'], $idUsuario);
        }
        return $preguntaActualRespondida;
    }


}