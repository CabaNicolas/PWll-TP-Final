<?php

class UsuarioController
{

    private $model;
    private $presenter;

    public function __construct($model, $presenter)
    {
        $this->model = $model;
        $this->presenter = $presenter;
    }

    //Acción para mostrar la vista de login
    public function showLogin() {
        $data = [];

        if(isset($_SESSION['registro_exitoso'])){
            $data['registro_exitoso'] = $_SESSION['registro_exitoso'];
            unset($_SESSION['registro_exitoso']);
        }

        //Pasar el mensaje de error si está en la sesión
        if (isset($_SESSION['error_message'])) {
            $data['error_message'] = $_SESSION['error_message'];
            // Eliminar el mensaje de la sesión para futuras visitas
            unset($_SESSION['error_message']);
        }

        // Mostrar la vista de login con los datos
        $this->presenter->show('login', $data);
    }

    public function showRegistro()
    {
        $data = array(
            'error_messages' => isset($_SESSION['error_messages']) ? $_SESSION['error_messages'] : null
        );
        unset($_SESSION['error_messages']);

        if(isset($_SESSION['datosTemporalesDeRegistro'])){
            $data['datosTemporalesDeRegistro'] = $_SESSION['datosTemporalesDeRegistro'];
            unset($_SESSION['datosTemporalesDeRegistro']);
        }

        $this->presenter->show('registro', $data);
    }
    public function showLobby()
    {
        if (isset($_SESSION['mail'])) {
            $data['mail'] = $_SESSION['mail'];
        }
        $this->presenter->show('lobby', $data);

    }
    public function registrarUsuario() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mail = $_POST['mail'];
            $password = $_POST['password'];
            $username = $_POST['username'];
            $name = $_POST['name'];
            $date = $_POST['date'];
            $sex = $_POST['sex'];
            $foto = $_FILES['foto'];

            $_SESSION['datosTemporalesDeRegistro'] = $this->guardarDatosTemporales($mail, $password, $username, $name, $date, $sex);

            $errores = $this->model->validarDatosRegistro($username, $mail, $password, $name, $date, $sex, $foto);

            if(!empty($errores)){
                $_SESSION['error_messages'] = $errores;
                header('location: /usuario/showRegistro');
                exit();
            }

            //registrar usuario
            $resultado = $this->model->guardarUsuario($username, $mail, $password, $name, $date, $sex, $foto);

            if ($resultado['exito']) {
                $_SESSION['registro_exitoso'] = $resultado['mensaje'];
                header('Location: /usuario/showLogin');
                exit();
            } else {
                $_SESSION['registro_fallido'] = $resultado['mensaje'];
                header('Location: /usuario/showRegistro');
                exit();
            }


        }
    }
    public function login()
    {
        $mail = $_POST['mail'];
        $pass = $_POST['password'];

        $resultado = $this->model->validarLogin($mail, $pass);

        if (!$resultado['exito']) {
            $_SESSION['error_message'] = $resultado['mensaje'];
            header('Location: /usuario/showLogin');
            exit();
        }

        $_SESSION['mail'] = $mail;

        $this->showLobby();
        header ('Location: /usuario/showLobby');
        exit();

    }

    //Método para cerrar sesión
    public function logout()
    {

        //Destruir la sesión
        session_destroy();

        //Redirigir al login después de cerrar la sesión
        header('Location: /usuario/login');
        exit();
    }

    public function showPerfil()
    {

        $data['usuario'] = $this->model->mostrarDatosUsuario($_SESSION['mail']);

        if(isset($_SESSION['cambios'])){
            $data['cambios'] = $_SESSION['cambios'];
            unset($_SESSION['cambios']);
        }

        $this->presenter->show('perfil', $data);

    }

    public function actualizar()
    {
        $nombreUsuario = $_POST['nombreUsuario'];
        $mail = $_POST['mail'];
        $nombreCompleto = $_POST ['nombreCompleto'];
        $fechaNacimiento = $_POST['fechaNacimiento'];
        $sexo = $_POST['sex'];
        $foto = $_FILES['foto'];
        $password = $_POST['password'];
        $resultado=$this->model->actualizarDatosPerfil($nombreUsuario, $mail, $nombreCompleto, $fechaNacimiento, $sexo, $foto, $password);
        if ($resultado['exito']) {
            $_SESSION['mail'] = $mail;
            $_SESSION['cambios'] = $resultado['mensaje'];
            header('Location: /usuario/showPerfil');
            exit();
        } else {
            $_SESSION['cambios'] = $resultado['mensaje'];
            header('Location: /usuario/showPerfil');
            exit();
        }


    }

    private function guardarDatosTemporales($mail, $password, $username, $name, $date, $sex)
    {
        $datosTemporales = [];

        if(isset($mail)){
            $datosTemporales['mail'] = $mail;
        }

        if(isset($password)){
            $datosTemporales['password'] = $password;
        }

        if(isset($username)){
            $datosTemporales['username'] = $username;
        }

        if(isset($name)){
            $datosTemporales['name'] = $name;
        }

        if(isset($date)){
            $datosTemporales['date'] = $date;
        }

        if(isset($sex)){
            $datosTemporales['sex'] = $sex;
        }

        return $datosTemporales;
    }


}