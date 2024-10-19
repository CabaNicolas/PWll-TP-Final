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
    public function registrarUsuario() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mail = $_POST['mail'];
            $password = $_POST['password'];
            $username = $_POST['username'];
            $name = $_POST['name'];
            $date = $_POST['date'];
            $sex = $_POST['sex'];
            $foto = $_FILES['foto'];


            if (empty($mail) || !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['registro_fallido'] = "Por favor, ingresa un email válido.";
                header('Location: /usuario/showRegistro');
                exit();
            }

            if (empty($password) || strlen($password) < 6) {
                $_SESSION['registro_fallido'] = "La contraseña debe tener al menos 6 caracteres.";
                header('Location: /usuario/showRegistro');
                exit();
            }

            if (empty($username) || strlen($username) < 3) {
                $_SESSION['registro_fallido'] = "El nombre de usuario debe tener al menos 4 caracteres.";
                header('Location: /usuario/showRegistro');
                exit();
            }

            if (empty($name)) {
                $_SESSION['registro_fallido'] = "El nombre completo es obligatorio.";
                header('Location: /usuario/showRegistro');
                exit();
            }

            if (empty($date)) {
                $_SESSION['registro_fallido'] = "La fecha de nacimiento es obligatoria.";
                header('Location: /usuario/showRegistro');
                exit();
            } else {
                $fechaNacimiento = new DateTime($date);
                $hoy = new DateTime();
                $edad = $hoy->diff($fechaNacimiento)->y;

                if ($edad < 12) {
                    $_SESSION['registro_fallido'] = "Debes tener al menos 12 años para registrarte.";
                    header('Location: /usuario/showRegistro');
                    exit();
                }
            }

            if (empty($sex)) {
                $_SESSION['registro_fallido'] = "Debes seleccionar tu sexo.";
                header('Location: /usuario/showRegistro');
                exit();
            }

            //foto
            if ($foto['error'] === UPLOAD_ERR_OK) {
                $nombreFoto = $foto['name'];
                $archivoTemporal = $foto['tmp_name'];
                $dirDestino = $_SERVER['DOCUMENT_ROOT'] . '/public/Imagenes/';
                $dirFoto = $dirDestino . $nombreFoto;
                move_uploaded_file($archivoTemporal, $dirFoto);
            } else {
                $_SESSION['registro_fallido'] = "Error al subir la foto.";
                header('Location: /usuario/showRegistro');
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

        if (empty($mail) && empty($pass)) {
            $_SESSION['error_message'] = "Por favor, ingresa tu correo y contraseña.";
            header('Location: /usuario/showLogin');
            exit();
        } elseif (empty($mail)) {
            $_SESSION['error_message'] = "Por favor, ingresa tu correo.";
            header('Location: /usuario/showLogin');
            exit();
        } elseif (empty($pass)) {
            $_SESSION['error_message'] = "Por favor, ingresa tu contraseña.";
            header('Location: /usuario/showLogin');
            exit();
        }

        $usuarioValido = $this->model->validarUsuarioPorCorreo($mail);
        $credencialesValidas = $this->model->validarUsuario($mail, $pass);


        if (!$usuarioValido) {
            $_SESSION['error_message'] = "Correo incorrecto.";
            header('Location: /usuario/showLogin');
            exit();
        }


        if ($usuarioValido && !$credencialesValidas) {
            $_SESSION['error_message'] = "Contraseña incorrecta.";
            header('Location: /usuario/showLogin');
            exit();
        }


        if ($credencialesValidas) {
            $_SESSION['mail'] = $mail;
            $this->showLobby();
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
        $data = [];
        $this->presenter->show('perfil', $data);
    }

}