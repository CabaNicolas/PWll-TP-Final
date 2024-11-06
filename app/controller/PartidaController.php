<?php

class PartidaController
{

    private $model;
    private $presenter;
    private $preguntaModel;
    public function __construct($model, $preguntaModel, $presenter)
    {
        $this->model = $model;
        $this->presenter = $presenter;
        $this->preguntaModel = $preguntaModel;
    }

    public function crearPartida(){
        $idUsuario = $_SESSION['id'];
        $_SESSION['idPartida'] = $this->model->crearPartida($idUsuario);
        Redirecter::redirect('/partida/showPregunta');
    }


    public function showPregunta() {

        $this->inicializarTiempo();

        $preguntaActualRespondida = $this->model->verificarQueElUsuarioContestoLaUltimaPreguntaAsignada($_SESSION['idPartida'], $_SESSION['id']);
        if ($preguntaActualRespondida) {
            $data['preguntasYRespuestas'] = $this->model->showPreguntaRandom($_SESSION['id'], $_SESSION['idPartida']);
            $_SESSION['idPregunta'] = $data['preguntasYRespuestas']['idPregunta'];
        } else if(isset($_SESSION['idPregunta'])) {
            $data['preguntasYRespuestas'] = $this->model->getPreguntaPorId($_SESSION['idPregunta'], $_SESSION['idPartida']);
        }else{
            $data['preguntasYRespuestas'] = $this->model->showPreguntaRandom($_SESSION['id'], $_SESSION['idPartida']);
            $_SESSION['idPregunta'] = $data['preguntasYRespuestas']['idPregunta'];
        }

        $data['mail'] = $_SESSION['mail'];
        $data['tiempo_restante'] = $this->tiempoRestante();
        $this->presenter->show('partida', $data);
    }

    public function validarRespuesta() {
        $idRespuestaSeleccionada = $_POST['respuesta'];
        $idPartida = $_SESSION['idPartida'];
        $idPregunta = $_SESSION['idPregunta'];
        $idUsuario = $_SESSION['id'];

        $esCorrecta = $this->model->validarRespuesta($idRespuestaSeleccionada, $idPartida, $idPregunta, $idUsuario);
        unset($_SESSION['tiempo_inicio']);
        echo json_encode(['correcta' => $esCorrecta, 'idRespuesta' => $idRespuestaSeleccionada]);
        exit;
    }


    public function cerrarPartida(){
        $this->model->cerrarPartida($_SESSION['idPartida']);
        unset($_SESSION['idPartida'], $_SESSION['idPregunta'], $_SESSION['tiempo_inicio']);
        Redirecter::redirect('/usuario/showLobby');
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


}