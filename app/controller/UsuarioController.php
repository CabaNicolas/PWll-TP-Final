<?php

class UsuarioController
{

    private $model;
    private $presenter;
    private $fileEmailSender;

    public function __construct($model, $presenter, $fileEmailSender)
    {
        $this->model = $model;
        $this->presenter = $presenter;
        $this->fileEmailSender = $fileEmailSender;
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

        if(isset($_SESSION['mail'])){
            $this->logout();
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
        $data['nombreUsuario'] = $_SESSION['username'];
        $data['mail'] = $_SESSION['mail'];
        $idUsuario = $_SESSION['id'];
        $data['puntajeMaximo'] = $this->model->puntajeMaximoDeUsuario($idUsuario);
        if(isset($_SESSION['mensajeExito'])){
            $data['mensajeExito'] = $_SESSION['mensajeExito'];
            unset($_SESSION['mensajeExito']);
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

            $errores = $this->model->validarDatosRegistro($username, $mail, $password, $password2, $name, $date, $sex, $foto);

            if(!empty($errores)){
                $_SESSION['datosTemporalesDeRegistro'] = $this->guardarDatosTemporales($mail, $password, $password2, $username, $name, $date, $sex);
                $_SESSION['error_messages'] = $errores;
                Redirecter::redirect('/usuario/showRegistro');
            }

            $resultado = $this->model->guardarUsuario($username, $mail, $password, $name, $date, $sex, $foto);

            if ($resultado['exito']) {
                $token = $this->model->crearToken();
                $this->model->guardarTokenDeVerificacion($mail, $token);


                $idUsuario = $this->model->obtenerIdUsuarioConEmail($mail);
                $enlaceValidacion = $this->model->crearEnlaceValidacion($idUsuario, $token);


                $emailSender = $this->fileEmailSender;
                $mensaje = $this->model->crearMensajeEmail($enlaceValidacion);
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

        $_SESSION['rol'] = $this->model->obtenerRol($id);

        if($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'editor'){
            $this->setearLaSessionDelUsuario($mail, $id);
            $this->redirigirUsuario();
        }

        if($resultado['exito'] && $this->model->estadoDeCuenta($id) == 'A'){
            $this->setearLaSessionDelUsuario($mail, $id);
            Redirecter::redirect('/usuario/showLobby');
        }
        else{
            $_SESSION['error_message'] = 'Valide su cuenta para poder iniciar sesión';
            Redirecter::redirect('/usuario/showLogin');
        }
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
        $data['mail'] = $_SESSION['mail'];
        if(isset($_SESSION['cambios'])){
            $data['cambios'] = $_SESSION['cambios'];
            unset($_SESSION['cambios']);
        }

        $this->presenter->show('perfil', $data);

    }

    public function showEditarPerfil()
    {
        $data['usuario'] = $this->model->mostrarDatosUsuario($_SESSION['mail']);
        $data['mail'] = $_SESSION['mail'];
        
        if(isset($_SESSION['cambios'])){
            $data['cambios'] = $_SESSION['cambios'];
            unset($_SESSION['cambios']);

        }

        if (isset($_SESSION['error_messages'])) {
            $data['error_messages'] = $_SESSION['error_messages'];
            unset($_SESSION['error_messages']);
        }


        $this->presenter->show('editarPerfil', $data);
    }

    public function actualizarPerfil()
    {
        $username = $_POST['nombreUsuario'];
        $mail = $_POST['mail'];
        $password = $_POST['password'];
        $password2 = $_POST['password2'];
        $name = $_POST['nombreCompleto'];
        $date = $_POST['fechaNacimiento'];
        $sex = $_POST['sex'];
        $foto = $_FILES['foto'];
        $mailActual = $_SESSION['mail'];
        $usernameActual = $_SESSION['username'];

        $errores = $this->model->validarEditarPerfil($username, $mail, $password, $password2, $name, $date, $sex, $foto, $usernameActual, $mailActual);

        if(!empty($errores)){
            $_SESSION['error_messages'] = $errores;
           // $_SESSION['cambios'] = $resultado['mensaje'];
            Redirecter::redirect('/usuario/showEditarPerfil');
        } else {
            $resultado = $this->model->actualizarDatosPerfil($username, $mail, $name, $date, $sex, $foto, $password,$mailActual);
            if ($resultado['exito']) {
                $_SESSION['mail'] = $mail;
                $_SESSION['username'] = $username;
                $_SESSION['cambios'] = $resultado['mensaje'];
            }
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


    public function showRankingUsuarios() {

        $data['mail'] = $_SESSION['mail'];
        $nombreUsuario = $_SESSION['username'];

        $data['ranking'] = $this->model->obtenerRankingUsuarios();
        $data['partidas'] = $this->model->verPartidasPorUsuario($nombreUsuario);

        $this->presenter->show('rankingUsuarios', $data);
    }

    public function showPerfilUsuario() {

        $nombreUsuario = isset($_POST['nombreUsuario']) ? $_POST['nombreUsuario'] : null;
        $data['mail'] = $_SESSION['mail'];

        $data['perfil'] = $this->model->verPerfilUsuario($nombreUsuario);
        $data['partidas'] = $this->model->verPartidasPorUsuario($nombreUsuario);

        $this->presenter->show('usuarioPerfil', $data);
    }

    public function showVistaEditor()
    {
        $this->presenter->show('vistaEditor');
    }

    private function setearLaSessionDelUsuario($mail, $id){
        $_SESSION['mail'] = $mail;
        $_SESSION['id'] = $id;
        $_SESSION['username'] = $this->model->obtenerNombreUsuario($mail);
    }

    private function redirigirUsuario(){
        if($_SESSION['rol'] == 'admin'){
            //Redirigir a la vista de admin
        }else{
            Redirecter::redirect("/usuario/showVistaEditor");
        }
    }
}