<?php

class PreguntaController {

    private $model;
    private $presenter;

    public function __construct($preguntaModel, $presenter) {
        $this->model = $preguntaModel;
        $this->presenter = $presenter;
    }

    public function showReporte(){
        $idPregunta = $_SESSION['idPregunta'];

        $data = [
            'idPregunta' => $idPregunta
        ];

        $this->presenter->show('reportarPregunta', $data);
    }

    public function guardarReporte(){
        $idPregunta = $_POST['idPregunta'];
        $motivo = $_POST['motivo'];
        $idUsuario = $_SESSION['id'];

        $this->model->reportarPregunta($idPregunta, $idUsuario, $motivo);

        Redirecter::redirect('/partida/preguntaInvalidada');
    }

}
