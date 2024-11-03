<?php

class PartidaController
{

    private $model;
    private $presenter;

    public function __construct($model, $presenter)
    {
        $this->model = $model;
        $this->presenter = $presenter;
    }


    public function showPregunta() {
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
        $this->presenter->show('partida', $data);
    }

    public function validarRespuesta() {
        $idRespuestaSeleccionada = $_POST['respuesta'];
        $idPartida = $_SESSION['idPartida'];
        $idPregunta = $_SESSION['idPregunta'];
        $idUsuario = $_SESSION['id'];

        $esCorrecta = $this->model->validarRespuesta($idRespuestaSeleccionada, $idPartida, $idPregunta, $idUsuario);

        echo json_encode(['correcta' => $esCorrecta, 'idRespuesta' => $idRespuestaSeleccionada]);
        exit;
    }


    public function crearPartida(){
        $idUsuario = $_SESSION['id'];
        $_SESSION['idPartida'] = $this->model->crearPartida($idUsuario);
        Redirecter::redirect('/partida/showPregunta');
    }

    public function cerrarPartida(){
        Redirecter::redirect('/usuario/showLobby');
    }

    private function mostrarResultado($esCorrecta){
        $data['preguntasYRespuestas'] = $this->model->getPreguntaPorId($_SESSION['idPregunta'], $_SESSION['idPartida']);
        $data['mail'] = $_SESSION['mail'];

        $this->presenter->show('partida', $data);
    }

}