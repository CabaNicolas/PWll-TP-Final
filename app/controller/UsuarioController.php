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
            'error_message' => isset($_SESSION['error_message']) ? $_SESSION['error_message'] : null
        );

        unset($_SESSION['error_message']);

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


            $errores = $this->model->validarDatosRegistro($username, $mail, $password, $name, $date, $sex, $foto);

            if(!empty($errores)){
                $_SESSION['error_message'] = implode(", ", $errores);
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
        $data = [];
        $this->presenter->show('perfil', $data);
    }

}