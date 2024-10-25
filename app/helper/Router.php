<?php

class Router
{
    private $defaultController;
    private $defaultMethod;
    private $configuration;
    private $middleware;

    public function __construct($configuration, $defaultController, $defaultMethod, $middleware)
    {
        $this->defaultController = $defaultController;
        $this->defaultMethod = $defaultMethod;
        $this->configuration = $configuration;
        $this->middleware = $middleware;
    }

    public function route($controllerName, $methodName)
    {
        $controller = $this->getControllerFrom($controllerName);
        if ($this->middleware) {
            $array = $this->middleware->procesarSolicitud($controller, $methodName, $controllerName);
            $controller = $array[0];
            $methodName = $array[1];
            $this->executeMethodFromController($controller, $methodName);
        } else {
            $this->executeMethodFromController($controller, $methodName);
        }
    }

    private function getControllerFrom($module)
    {
        $controllerName = 'get' . ucfirst($module) . 'Controller';
        $validController = method_exists($this->configuration, $controllerName) ? $controllerName : $this->defaultController;
        return call_user_func(array($this->configuration, $validController));
    }

    private function executeMethodFromController($controller, $method)
    {
        $validMethod = method_exists($controller, $method) ? $method : $this->defaultMethod;
        call_user_func(array($controller, $validMethod));
    }
}