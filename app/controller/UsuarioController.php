<?php

class UsuarioController
{

    private $model;
    private $presenter;

    public function __construct($model, $presenter)
    {
        $this->model = $model;
        $this->presenter = $presenter;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    //Acción para mostrar la vista de login
    public function showLogin() {
        $data = [];

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
        if (isset($_SESSION['user'])) {
            $data['user'] = $_SESSION['user'];
        }
        $this->presenter->show('lobby', $data);

    }
    public function registrarUsuario() {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Intentar registrar el usuario
            $resultado = $this->model->guardarUsuario($username, $password);

            if ($resultado === "error") {
                // Guardar el mensaje de error en la sesión
                $_SESSION['error_message'] = "El usuario ya está registrado.";
                header('Location: /usuario/registro');
                exit();
            } elseif ($resultado) {
                // Si el registro es exitoso, redirigir al login sin mensaje de error

                header('Location: /usuario/login');
                exit();
            }


        }
        }
    public function login()
    {
        $user = $_POST['username'];
        $pass = $_POST['password'];

        $validation = $this->model->validarUsuario($user, $pass);

        if ($validation) {
            $_SESSION['user'] = $user;

            $this->showLobby();

            exit();
        }else{

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
}