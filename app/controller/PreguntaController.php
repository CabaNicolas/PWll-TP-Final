<?php

class PreguntaController{

    private $model;
    private $presenter;
    public function __construct($model, $presenter) {
        $this->model = $model;
        $this->presenter = $presenter;
    }

    public function showCrear(){
        $data['categorias'] = $this->model->obtenerCategorias();
        $this->presenter->show('crearPregunta', $data);
    }

    public function showReporte(){
        $idPregunta = $_SESSION['idPregunta'];

        $data = [
            'idPregunta' => $idPregunta
        ];
        $this->presenter->show('reportarPregunta', $data);
    }

    public function crearPregunta()
    {
        $consigna = $_POST['consigna'] ?? null;
        $categoria = $_POST['categoria'] ?? null;
        $respuestas = [
            $_POST['respuesta1'] ?? null,
            $_POST['respuesta2'] ?? null,
            $_POST['respuesta3'] ?? null,
            $_POST['respuesta4'] ?? null,
        ];
        $respuestaCorrecta = $_POST['correcta'] ?? null;

        $data['error'] = $this->validarCampos($consigna, $categoria, $respuestaCorrecta, $respuestas);

        if (!empty($data['error'])) {
            $data['categorias'] = $this->model->obtenerCategorias();

            $this->presenter->show('crearPregunta', $data);
            return;
        }

        $this->model->crearPregunta($consigna, $categoria, $respuestaCorrecta, $respuestas);
        $_SESSION['mensajeExito'] = "Pregunta creada con exito";
        Redirecter::redirect('/usuario/showLobby');

        
    }

    public function validarCampos($consigna, $categoria, $respuestaCorrecta, $respuestas){
        if (!$consigna || !$categoria || !$respuestaCorrecta || in_array(null, $respuestas)) {
            $mensaje = "Debe completar todos los campos con (*)";
            return $mensaje;
        }
    }

    public function showPreguntasSugeridas(){

        $data['preguntasSugeridas'] = $this->model->getPreguntasSugeridas();
        $this->presenter->show('preguntasSugeridas', $data);

    }
    public function showPreguntasReportadas(){

        $this->presenter->show('preguntasReportadas');
    }

    public function aprobarPreguntaSugerida()
    {
        $idPreguntaSugerida = $_POST['idPreguntaSugerida'];
        $this->model->aprobarPreguntaSugerida($idPreguntaSugerida);

        Redirecter::redirect("/pregunta/showPreguntasSugeridas");
    }

    public function rechazarPreguntaSugerida()
    {
        $idPreguntaSugerida = $_POST['idPreguntaSugerida'];
        $this->model->rechazarPreguntaSugerida($idPreguntaSugerida);

        Redirecter::redirect("/pregunta/showPreguntasSugeridas");
    }

    public function guardarReporte(){
        $idPregunta = $_POST['idPregunta'];
        $motivo = $_POST['motivo'];
        $idUsuario = $_SESSION['id'];

        $this->model->reportarPregunta($idPregunta, $idUsuario, $motivo);

        Redirecter::redirect('/partida/preguntaInvalidadaPorExpiracionDeTiempo');
    }


}