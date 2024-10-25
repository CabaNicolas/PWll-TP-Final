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

        if(isset($_SESSION['mensaje_verificacion'])){
            $data['mensaje_verificacion'] = $_SESSION['mensaje_verificacion'];
            unset($_SESSION['mensaje_verificacion']);
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
            $password2 = $_POST['password2'];
            $username = $_POST['username'];
            $name = $_POST['name'];
            $date = $_POST['date'];
            $sex = $_POST['sex'];
            $foto = $_FILES['foto'];

            $_SESSION['datosTemporalesDeRegistro'] = $this->guardarDatosTemporales($mail, $password, $password2, $username, $name, $date, $sex);

            $errores = $this->model->validarDatosRegistro($username, $mail, $password, $password2, $name, $date, $sex, $foto);

            if(!empty($errores)){
                $_SESSION['error_messages'] = $errores;
                Redirecter::redirect('/usuario/showRegistro');
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
                Redirecter::redirect('/usuario/showLogin');
            } else {
                $_SESSION['registro_fallido'] = $resultado['mensaje'];
                Redirecter::redirect('/usuario/showRegistro');
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
            Redirecter::redirect('/usuario/showLogin');
        }
        $resultado = $this->model->validarLogin($mail, $pass);

        if (!$resultado['exito']) {
            $_SESSION['error_message'] = $resultado['mensaje'];
            Redirecter::redirect('/usuario/showLogin');
        }

        if($resultado['exito'] && $this->model->estadoDeCuenta($id) == 'A'){
            $_SESSION['mail'] = $mail;
            $_SESSION['id'] = $id;
            Redirecter::redirect('/usuario/showLobby');
        }
        else{
            $_SESSION['error_message'] = 'Valide su cuenta para poder iniciar sesión';
            Redirecter::redirect('/usuario/showLogin');
        }
        exit();
    }

    public function logout()
    {
        //Destruir la sesión
        session_destroy();

        //Redirigir al login después de cerrar la sesión
        Redirecter::redirect('/usuario/showLogin');
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
            Redirecter::redirect('/usuario/showPerfil');
        } else {
            $_SESSION['cambios'] = $resultado['mensaje'];
            Redirecter::redirect('/usuario/showPerfil');
        }


    }

    public function validarCuenta() {
        if (isset($_GET['id']) && isset($_GET['token'])) {
            $id = $_GET['id'];
            $token = $_GET['token'];

            $resultado = $this->model->verificarToken($id, $token);

            if ($resultado['exito']) {
                $this->model->activarCuenta($id);
                $_SESSION['mensaje_verificacion'] = "Cuenta activada con éxito. Por favor, inicie sesión.";
            } else {
                $_SESSION['mensaje_verificacion'] = "La verificación falló. Intente nuevamente.";
            }
            Redirecter::redirect('/usuario/showLogin');
        }
    }

    private function guardarDatosTemporales($mail, $password, $password2, $username, $name, $date, $sex)
    {
        $datosTemporales = [];

        if(isset($mail)){
            $datosTemporales['mail'] = $mail;
        }

        if(isset($password)){
            $datosTemporales['password'] = $password;
        }

        if(isset($password2)){
            $datosTemporales['password2'] = $password2;
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