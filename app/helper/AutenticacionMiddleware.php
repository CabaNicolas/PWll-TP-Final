<?php

class AutenticacionMiddleware{

    private $rutasPermitidas = [
        'usuario/showLogin',
        'usuario/showRegistro',
        'usuario/registrarUsuario',
        'usuario/validarCuenta',
        'usuario/login',
    ];
    public function procesarSolicitud($controller, $methodName, $controllerName)
    {
        $rutaActual = $controllerName . '/' . $methodName;
        if (!in_array($rutaActual, $this->rutasPermitidas)) {
            if (!isset($_SESSION['mail'])) {
                header('Location: /usuario/showLogin');
                exit;
            }
        }
        return array($controller, $methodName);
    }
}