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
        $preguntaYRespuestas = $this->model->showPreguntaRandom();


        if (!empty($preguntaYRespuestas)) {
            $data['preguntasYRespuestas'] = [
                'descripcion' => $preguntaYRespuestas['descripcion'],
                'categoria' => $preguntaYRespuestas['categoria'],
                'respuestas' => $preguntaYRespuestas['respuestas']
            ];
        } else {
            $data['preguntasYRespuestas'] = [];
        }

        $this->presenter->show('partida', $data);
    }

}