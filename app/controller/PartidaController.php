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
        $data['preguntasYRespuestas'] = $this->model->showPreguntaRandom();
        $this->presenter->show('partida', $data);
    }

    public function validarRespuesta() {
        $idRespuestaSeleccionada = $_POST['respuesta'];


        $esCorrecta = $this->model->validarRespuesta($idRespuestaSeleccionada);

        if($esCorrecta) {
            $_SESSION['puntaje'] += 1;
            $this->showPregunta();
        }else{
            $this->cerrarPartida();
        }

    }

    public function crearPartida(){
        $idUsuario = $_SESSION['id'];
        $_SESSION['idPartida'] = $this->model->crearPartida($idUsuario);
        $_SESSION['puntaje'] = 0;
        Redirecter::redirect('/partida/showPregunta');
    }

    private function cerrarPartida(){
        $idPartida = $_SESSION['idPartida'];
        $puntaje = $_SESSION['puntaje'];
        $this->model->cerrarPartida($idPartida, $puntaje);
        unset($_SESSION['idPartida']);
        unset($_SESSION['puntaje']);
        Redirecter::redirect('/usuario/showLobby');
    }

}