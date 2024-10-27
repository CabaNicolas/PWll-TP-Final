<?php
include_once("helper/MysqlDatabase.php");
include_once("helper/IncludeFilePresenter.php");
include_once("helper/Router.php");
include_once("helper/MustachePresenter.php");
include_once('helper/FileEmailSender.php');
include_once('helper/AutenticacionMiddleware.php');
include_once('helper/Redirecter.php');

include_once('controller/UsuarioController.php');
include_once('controller/PartidaController.php');

include_once('model/UsuarioModel.php');
include_once('model/PartidaModel.php');

include_once('vendor/mustache/src/Mustache/Autoloader.php');


class Configuration
{
    public function __construct()
    {
    }


    public function getUsuarioController(){
        return new UsuarioController($this->getUsuarioModel(), $this->getPresenter(), $this->getFileEmailSender());
    }
    public function getFileEmailSender(){
        return new FileEmailSender();
    }
    public function getPartidaController(){
        return new PartidaController($this->getPartidaModel(), $this->getPresenter());
    }

    private function getUsuarioModel()
    {
        return new UsuarioModel($this->getDatabase());
    }
    private function getPartidaModel()
    {
        return new PartidaModel($this->getDatabase());
    }

    private function getMiddleware()
    {
        return new AutenticacionMiddleware();
    }

    private function getPresenter()
    {
        return new MustachePresenter("./view");
    }

    private function getDatabase()
    {
        $config = parse_ini_file('configuration/config.ini');
        return new MysqlDatabase(
            $config['host'],
            $config['port'],
            $config['user'],
            $config['password'],
            $config["database"]
        );
    }

    public function getRouter()
    {
        return new Router($this, "getUsuarioController", "showLogin", $this->getMiddleware());
    }

}