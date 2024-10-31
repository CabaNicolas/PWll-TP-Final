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
        if (isset($_SESSION['idPregunta'])) {
            $data['preguntasYRespuestas'] = $this->model->getPreguntaPorId($_SESSION['idPregunta']);
        } else {
            $data['preguntasYRespuestas'] = $this->model->showPreguntaRandom($_SESSION['id'], $_SESSION['idPartida']);
            $_SESSION['idPregunta'] = $data['preguntasYRespuestas']['idPregunta'];
        }
        $this->presenter->show('partida', $data);
    }


    public function validarRespuesta() {
        $idRespuestaSeleccionada = $_POST['respuesta'];
        $idPartida = $_SESSION['idPartida'];
        $idPregunta = $_SESSION['idPregunta'];
        $idUsuario = $_SESSION['id'];

        $esCorrecta = $this->model->validarRespuesta($idRespuestaSeleccionada, $idPartida, $idPregunta, $idUsuario);

        // Verificar que el usuario está respondiendo la pregunta correcta
        //if (!$this->model->elUsuarioRespondio($idPregunta, $idUsuario)) {
        //    // Si no es la pregunta correcta, redirige o muestra un mensaje de error
        //    Redirecter::redirect('/partida/showPregunta');
        //    return;
        //}
        if($esCorrecta) {
            $this->showPregunta();
        } else {
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