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
        $data['preguntasYRespuestas'] = $this->model->showPreguntaRandom($_SESSION['id']);
        $_SESSION['idPregunta'] = $data['preguntasYRespuestas']['idPregunta'];
        $this->presenter->show('partida', $data);
    }

    public function validarRespuesta() {
        $idRespuestaSeleccionada = $_POST['respuesta'];
        $idPartida = $_SESSION['idPartida'];
        $idPregunta = $_SESSION['idPregunta'];
        $idUsuario = $_SESSION['id'];

        $esCorrecta = $this->model->validarRespuesta($idRespuestaSeleccionada, $idPartida, $idPregunta, $idUsuario);

        if($esCorrecta) {
            $this->showPregunta();
        }else{
            $this->cerrarPartida();
        }

    }

    public function crearPartida(){
        $idUsuario = $_SESSION['id'];
        $_SESSION['idPartida'] = $this->model->crearPartida($idUsuario);
        Redirecter::redirect('/partida/showPregunta');
    }

    private function cerrarPartida(){
        Redirecter::redirect('/usuario/showLobby');
    }

}