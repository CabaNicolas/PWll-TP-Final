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

}