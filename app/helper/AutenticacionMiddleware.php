<?php

class AutenticacionMiddleware{

    private $rutasPermitidas = [
        'usuario/showLogin',
        'usuario/showRegistro',
        'usuario/registrarUsuario',
        'usuario/validarCuenta',
        'usuario/login',
    ];

    private $rutasPermitidasPorRol = [
        'normal' => [
          'usuario/showLobby',
          'usuario/showPerfil',
          'usuario/showEditarPerfil',
          'usuario/actualizarPerfil',
          'partida/crearPartida',
          'partida/showPregunta',
          'partida/validarRespuesta',
          'partida/cerrarPartida',
          'pregunta/showReporte',
          'pregunta/guardarReporte',
          'partida/preguntaInvalidadaPorExpiracionDeTiempo',
          'usuario/showRankingUsuarios',
          'usuario/showPerfilUsuario',
          'pregunta/showCrear',
          'pregunta/crearPregunta',
        ],
        'editor' => [
            'usuario/showVistaEditor',
            'pregunta/showPreguntasSugeridas',
            'pregunta/showPreguntasReportadas',
            'pregunta/aprobarPreguntaSugerida',
        ],
        'admin' => [
            '',
        ]
    ];
    public function procesarSolicitud($controller, $methodName, $controllerName)
    {
        $rutaActual = $controllerName . '/' . $methodName;
        if(isset($_SESSION['rol'])) {
            $rol = $_SESSION['rol'];
        }

        if (!in_array($rutaActual, $this->rutasPermitidas)) {
            if (!isset($_SESSION['rol']) || !in_array($rutaActual, $this->rutasPermitidasPorRol[$rol])){
                header('Location: /usuario/showLogin');
                exit;
            }
        }
        return array($controller, $methodName);
    }
}