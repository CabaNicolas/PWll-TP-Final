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

            $resultado = $this->model->guardarUsuario($username, $mail, $password, $name, $date, $sex, $foto);

            if ($resultado['exito']) {
                $token = substr(bin2hex(random_bytes(5)), 0, 10);
                $this->model->guardarTokenDeVerificacion($mail, $token);


                $idUsuario = $this->model->obtenerIdUsuarioConEmail($mail);
                $enlaceValidacion = "http://localhost/usuario/validarCuenta?id=$idUsuario&token=$token";


                $emailSender = new FileEmailSender();
                $mensaje = "Hola! Hacé click en el siguiente enlace para validar tu cuenta: <a href='$enlaceValidacion'>Click aquí</a>";
                $emailSender->sendEmail($mail, "Validación de cuenta", $mensaje);

                $_SESSION['registro_exitoso'] = $resultado['mensaje'] . ". Revisa tu email para validar tu cuenta."."--Andá a PWll-TP-Final/app/emails.txt--";
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
        $id = $this->model->obtenerIdUsuarioConEmail($mail);

        if ($id === null) {
            $_SESSION['error_message'] = 'No se encontró el usuario.';
            header('Location: /usuario/showLogin');
            exit();
        }
        $resultado = $this->model->validarLogin($mail, $pass);

        if (!$resultado['exito']) {
            $_SESSION['error_message'] = $resultado['mensaje'];
            header('Location: /usuario/showLogin');
            exit();
        }

        if($resultado['exito'] && $this->model->estadoDeCuenta($id) == 'A'){
            $_SESSION['mail'] = $mail;
            header ('Location: /usuario/showLobby');
        }
        else{
            $_SESSION['error_message'] = 'Valide su cuenta para poder iniciar sesión';
            header('Location: /usuario/showLogin');
        }
        exit();
    }

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

        $this->presenter->show('perfil', $data);

    }

    public function showEditarPerfil()
    {
        $data['usuario'] = $this->model->mostrarDatosUsuario($_SESSION['mail']);

        if(isset($_SESSION['cambios'])){
            $data['cambios'] = $_SESSION['cambios'];
            unset($_SESSION['cambios']);
        }

        $this->presenter->show('editarPerfil', $data);
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

    public function validarCuenta() {
        if (isset($_GET['id']) && isset($_GET['token'])) {
            $id = $_GET['id'];
            $token = $_GET['token'];

            $resultado = $this->model->verificarToken($id, $token);
            $_SESSION['exito'];

            if ($resultado['exito']) {
                $this->model->activarCuenta($id);
                $_SESSION['mensaje_verificacion'] = "Cuenta activada con éxito. Por favor, inicie sesión.";
            } else {
                $_SESSION['mensaje_verificacion'] = "La verificación falló. Intente nuevamente.";
            }
            header('Location: /usuario/showLogin');
            exit();
        }
    }



}