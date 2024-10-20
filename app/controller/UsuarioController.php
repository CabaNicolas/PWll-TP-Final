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
    public function showLogin()
    {
        $data = [];

        if (isset($_SESSION['registro_exitoso'])) {
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
            'error_message' => isset($_SESSION['registro_fallido']) ? $_SESSION['registro_fallido'] : null
        );

        unset($_SESSION['registro_fallido']);

        $this->presenter->show('registro', $data);
    }

    public function showLobby()
    {
        if (isset($_SESSION['mail'])) {
            $data['mail'] = $_SESSION['mail'];
        }
        $this->presenter->show('lobby', $data);

    }

    public function registrarUsuario()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mail = $_POST['mail'];
            $password = $_POST['password'];
            $username = $_POST['username'];
            $name = $_POST['name'];
            $date = $_POST['date'];
            $sex = $_POST['sex'];
            $foto = $_FILES['foto'];


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

        $validation = $this->model->validarUsuario($mail, $pass);

        if ($validation) {
            $_SESSION['mail'] = $mail;

            $this->showLobby();

            exit();
        } else {

            header('location: /login');
            exit();
        }


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
        $nombre = $_POST['nombre'];
        $mail = $_POST['mail'];
        $fechaNacimiento = $_POST['fechaNacimiento'];
        $sexo = $_POST['sex'];
        $foto = $_FILES['foto'];
        $password = $_POST['password'];
        $resultado=$this->model->actualizarDatosPerfil($mail, $nombre, $fechaNacimiento, $sexo, $foto, $password);
        if ($resultado['exito']) {
            $_SESSION['cambios'] = $resultado['mensaje'];
            header('Location: /usuario/showPerfil');
            exit();
        } else {
            $_SESSION['cambios'] = $resultado['mensaje'];
            header('Location: /usuario/showPerfil');
            exit();
        }


    }




}