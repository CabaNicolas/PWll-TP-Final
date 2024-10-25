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


    public function showPartida() {
        $data['preguntasYRespuestas'] = $this->model->showPreguntaRandom();

        $this->presenter->show('partida', $data);
    }

    public function validarRespuesta() {
        $idRespuestaSeleccionada = $_POST['respuesta'];


        $esCorrecta = $this->model->validarRespuesta($idRespuestaSeleccionada);

        if($esCorrecta) {
            //sleep(2);
            $this->showPartida();
        }else{
            Redirecter::redirect('/usuario/showLobby');
        }

    }

    public function crearPartida(){
        $idUsuario = $_SESSION['id'];
        $this->model->crearPartida($idUsuario);
        Redirecter::redirect('/partida/showPartida');
    }

}