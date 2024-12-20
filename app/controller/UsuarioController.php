<?php

class UsuarioController
{

    private $model;
    private $presenter;
    private $fileEmailSender;
    private $partidaModel;
    private $preguntaModel;

    public function __construct($model, $partidaModel, $preguntaModel, $presenter, $fileEmailSender)
    {
        $this->model = $model;
        $this->partidaModel = $partidaModel;
        $this->preguntaModel = $preguntaModel;
        $this->presenter = $presenter;
        $this->fileEmailSender = $fileEmailSender;
    }


    public function showLogin() {
        $data = [];

        if(isset($_SESSION['registro_exitoso'])){
            $data['registro_exitoso'] = $_SESSION['registro_exitoso'];
            unset($_SESSION['registro_exitoso']);
        }

        if (isset($_SESSION['error_message'])) {
            $data['error_message'] = $_SESSION['error_message'];
            unset($_SESSION['error_message']);
        }

        if(isset($_SESSION['mensaje_verificacion'])){
            $data['mensaje_verificacion'] = $_SESSION['mensaje_verificacion'];
            unset($_SESSION['mensaje_verificacion']);
        }

        if(isset($_SESSION['mail'])){
            $this->logout();
        }

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

        if(isset($_SESSION['registro_fallido'])){
            $data['registro_fallido'] = $_SESSION['registro_fallido'];
            unset($_SESSION['registro_fallido']);
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
            $lat = $_POST['lat'];
            $long = $_POST['long'];

            $errores = $this->model->validarDatosRegistro($username, $mail, $password, $password2, $name, $date, $sex, $foto);

            if(!empty($errores)){
                $_SESSION['datosTemporalesDeRegistro'] = $this->guardarDatosTemporales($mail, $password, $password2, $username, $name, $date, $sex);
                $_SESSION['error_messages'] = $errores;
                Redirecter::redirect('/usuario/showRegistro');
            }

            $resultado = $this->model->guardarUsuario($username, $mail, $password, $name, $date, $sex, $foto, $lat, $long);

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
        session_destroy();

        Redirecter::redirect('/usuario/showLogin');
    }

    public function showPerfil()
    {

        $data['usuario'] = $this->model->mostrarDatosUsuario($_SESSION['mail']);
        $data['mail'] = $_SESSION['mail'];

        $data['partidas'] = $this->model->verPartidasPorUsuario($_SESSION['username']);

        if (isset($_SESSION['cambios'])) {
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
        $lat = $_POST['lat'];
        $long = $_POST['long'];

        $errores = $this->model->validarEditarPerfil($username, $mail, $password, $password2, $name, $date, $sex, $foto, $usernameActual, $mailActual);

        if(!empty($errores)){
            $_SESSION['error_messages'] = $errores;
            Redirecter::redirect('/usuario/showEditarPerfil');
        } else {
            $resultado = $this->model->actualizarDatosPerfil($username, $mail, $name, $date, $sex, $foto, $password,$mailActual, $lat, $long);
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

        $nombreUsuario = isset($_GET['usuario']) ? $_GET['usuario'] : null;
        $data['mail'] = $_SESSION['mail'];

        $data['perfil'] = $this->model->verPerfilUsuario($nombreUsuario);
        $data['partidas'] = $this->model->verPartidasPorUsuario($nombreUsuario);

        $enlacePerfil = "http://localhost/usuario/showPerfilUsuario?usuario=" . $nombreUsuario;
        $nombreArchivoQR = QR::generarQr($enlacePerfil);
        $data['qr_url'] = '/public/qr_codes/' . $nombreArchivoQR;

        $this->presenter->show('usuarioPerfil', $data);
    }

    public function showVistaEditor()
    {
        $data['mail'] = $_SESSION['mail'];
        $data['username'] = $_SESSION['username'];
        $this->presenter->show('vistaEditor', $data);
    }

    private function setearLaSessionDelUsuario($mail, $id){
        $_SESSION['mail'] = $mail;
        $_SESSION['id'] = $id;
        $_SESSION['username'] = $this->model->obtenerNombreUsuario($mail);
    }

    private function redirigirUsuario(){
        if($_SESSION['rol'] == 'admin'){
            Redirecter::redirect("/usuario/showAdministrador");
        }else{
            Redirecter::redirect("/usuario/showVistaEditor");
        }
    }

    public function showAdministrador(){
        $data['mail'] = $_SESSION['mail'];
        $data['username'] = $_SESSION['username'];

        $filtroDeFecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';

        $data = $this->estadisticasDeJuego($data, $filtroDeFecha);

        $data = $this->estadisticasDePreguntasGenerales($data, $filtroDeFecha);
        $data = $this->estadisticasDePreguntasSugeridas($data, $filtroDeFecha);
        $data = $this->estadisticasDePreguntasReportadas($data, $filtroDeFecha);
        $data = $this->estadisticasDeUsuariosPorSexo($data, $filtroDeFecha);
        $data = $this->estadisticasDeUsuariosPorEdad($data, $filtroDeFecha);


        $this->presenter->show('administrador', $data);
    }

    public function estadisticasDeJuego($data, $filtroDeFecha){

        if($filtroDeFecha != ''){
            $filtroDeFecha = $this->obtenerFechaInicio($filtroDeFecha);
            $data['cantidadJugadores'] = $this->model->obtenerCantidadJugadoresPorFecha($filtroDeFecha)[0]['cantidadJugadores'];
            $data['cantidadPartidas'] = $this->partidaModel->obtenerCantidadPartidasPorFecha($filtroDeFecha)[0]['cantidadPartidas'];
            //$data['cantidadPreguntasCorrectas'] = $this->model->obtenerPreguntasCorrectasPorUsuarioPorFecha($filtroDeFecha)[0]['cantidadPreguntasCorrectas'];
            $data['cantidadPreguntasCorrectas'] = $this->model->obtenerPreguntasCorrectasPorUsuario()[0]['cantidadPreguntasCorrectas'];

        }else{
            $data['cantidadJugadores'] = $this->model->obtenerCantidadJugadores()[0]['cantidadJugadores'];
            $data['cantidadPartidas'] = $this->partidaModel->obtenerCantidadPartidasJugadas()[0]['cantidadPartidas'];
            $data['cantidadPreguntasCorrectas'] = $this->model->obtenerPreguntasCorrectasPorUsuario()[0]['cantidadPreguntasCorrectas'];

        }


        $datosGrafico = [
            'etiquetas' => ['Jugadores', 'Partidas', 'Preguntas Correctas (%)'],
            'valores' => [$data['cantidadJugadores'], $data['cantidadPartidas'], $data['cantidadPreguntasCorrectas']],
            'tituloDelGrafico' => 'Estadisticas del juego',
            'tituloDeX' => 'Juego',
            'tituloDeY' => 'Cantidades',
        ];

        $data['graficoJuego'] = GraphHelper::generarBarplot($datosGrafico);

        return $data;
    }

    public function estadisticasDePreguntasGenerales($data, $filtroDeFecha = '')
    {if ($filtroDeFecha != '') {
        $filtroDeFecha = $this->obtenerFechaInicio($filtroDeFecha);

        $data['cantidadPreguntasActivas'] = $this->preguntaModel->obtenerCantidadPreguntasActivasPorFecha($filtroDeFecha)[0]['cantidadPreguntas'];
        $data['cantidadPreguntasSugeridas'] = $this->preguntaModel->obtenerCantidadPreguntasSugeridasPorFecha($filtroDeFecha)[0]['cantidadPreguntas'];
        $data['cantidadPreguntasReportadas'] = $this->preguntaModel->obtenerCantidadPreguntasReportadasPorFecha($filtroDeFecha)[0]['cantidadPreguntas'];
    } else {
        $data['cantidadPreguntasActivas'] = $this->preguntaModel->obtenerCantidadPreguntasActivas()[0]['cantidadPreguntas'];
        $data['cantidadPreguntasSugeridas'] = $this->preguntaModel->obtenerCantidadPreguntasSugeridas()[0]['cantidadPreguntas'];
        $data['cantidadPreguntasReportadas'] = $this->preguntaModel->obtenerCantidadPreguntasReportadas()[0]['cantidadPreguntas'];
    }

        $datosGrafico = [
            'etiquetas' => ['Preguntas Activas', 'Preguntas Sugeridas', 'Preguntas Reportadas'],
            'valores' => [
                $data['cantidadPreguntasActivas'],
                $data['cantidadPreguntasSugeridas'],
                $data['cantidadPreguntasReportadas']
            ],
            'tituloDelGrafico' => 'Estadísticas de preguntas generales',
            'tituloDeX' => 'Preguntas',
            'tituloDeY' => 'Cantidades',
        ];

        $data['graficoPreguntasGenerales'] = GraphHelper::generarBarplot($datosGrafico);

        return $data;
    }

    public function estadisticasDePreguntasSugeridas($data, $filtroDeFecha = '')
    {
        if ($filtroDeFecha != '') {
            $filtroDeFecha = $this->obtenerFechaInicio($filtroDeFecha);
            $data['cantidadSugeridasAprobadas'] = $this->preguntaModel->obtenerCantidadSugeridasAprobadasPorFecha($filtroDeFecha)[0]['cantidadPreguntas'];
            $data['cantidadSugeridasRechazadas'] = $this->preguntaModel->obtenerCantidadSugeridasRechazadasPorFecha($filtroDeFecha)[0]['cantidadPreguntas'];
            $data['cantidadSugeridasPendientes'] = $this->preguntaModel->obtenerCantidadSugeridasPendientesPorFecha($filtroDeFecha)[0]['cantidadPreguntas'];
        } else {
            $data['cantidadSugeridasAprobadas'] = $this->preguntaModel->obtenerCantidadSugeridasAprobadas()[0]['cantidadPreguntas'];
            $data['cantidadSugeridasRechazadas'] = $this->preguntaModel->obtenerCantidadSugeridasRechazadas()[0]['cantidadPreguntas'];
            $data['cantidadSugeridasPendientes'] = $this->preguntaModel->obtenerCantidadSugeridasPendientes()[0]['cantidadPreguntas'];
        }

        $datosGrafico = [
            'etiquetas' => ['Aprobadas', 'Rechazadas', 'Pendientes'],
            'valores' => [
                $data['cantidadSugeridasAprobadas'],
                $data['cantidadSugeridasRechazadas'],
                $data['cantidadSugeridasPendientes']
            ],
            'tituloDelGrafico' => 'Preguntas Sugeridas',
        ];

        $data['graficoPreguntasSugeridas'] = GraphHelper::generarPieplot($datosGrafico);

        return $data;
    }
    public function estadisticasDePreguntasReportadas($data, $filtroDeFecha = '')
    {
        if ($filtroDeFecha != '') {
            $filtroDeFecha = $this->obtenerFechaInicio($filtroDeFecha);
            $data['cantidadReportadasAprobadas'] = $this->preguntaModel->obtenerCantidadReportadasAprobadasPorFecha($filtroDeFecha)[0]['cantidadPreguntas'];
            $data['cantidadReportadasRechazadas'] = $this->preguntaModel->obtenerCantidadReportadasRechazadasPorFecha($filtroDeFecha)[0]['cantidadPreguntas'];
            $data['cantidadReportadasPendientes'] = $this->preguntaModel->obtenerCantidadReportadasPendientesPorFecha($filtroDeFecha)[0]['cantidadPreguntas'];
        } else {
            $data['cantidadReportadasAprobadas'] = $this->preguntaModel->obtenerCantidadReportadasAprobadas()[0]['cantidadPreguntas'];
            $data['cantidadReportadasRechazadas'] = $this->preguntaModel->obtenerCantidadReportadasRechazadas()[0]['cantidadPreguntas'];
            $data['cantidadReportadasPendientes'] = $this->preguntaModel->obtenerCantidadReportadasPendientes()[0]['cantidadPreguntas'];
        }

        $datosGrafico = [
            'etiquetas' => ['Aprobadas', 'Rechazadas', 'Pendientes'],
            'valores' => [
                $data['cantidadReportadasAprobadas'],
                $data['cantidadReportadasRechazadas'],
                $data['cantidadReportadasPendientes']
            ],
            'tituloDelGrafico' => 'Preguntas Reportadas',
        ];

        $data['graficoPreguntasReportadas'] = GraphHelper::generarPieplot($datosGrafico);

        return $data;
    }

    public function estadisticasDeUsuariosPorSexo($data, $filtroDeFecha = '')
    {
        if ($filtroDeFecha != '') {
            $filtroDeFecha = $this->obtenerFechaInicio($filtroDeFecha);
            $data['cantidadUsuariosPorSexo'] = $this->model->obtenerCantidadUsuariosPorSexoPorFecha($filtroDeFecha);
        } else {
            $data['cantidadUsuariosPorSexo'] = $this->model->obtenerCantidadUsuariosPorSexo();
        }

        $datosGrafico = [
            'etiquetas' => array_column($data['cantidadUsuariosPorSexo'], 'sexo'),
            'valores' => array_column($data['cantidadUsuariosPorSexo'], 'cantidad'),
            'tituloDelGrafico' => 'Usuarios por Sexo',
        ];

        $data['graficoUsuariosPorSexo'] = GraphHelper::generarPieplot($datosGrafico);

        return $data;
    }

    public function estadisticasDeUsuariosPorEdad($data, $filtroDeFecha = '') {
        if (!empty($filtroDeFecha)) {
            $fechaInicio = $this->obtenerFechaInicio($filtroDeFecha);
            $data['cantidadUsuariosPorEdad'] = $this->model->obtenerCantidadUsuariosPorEdadPorFecha($fechaInicio);
        } else {
            $data['cantidadUsuariosPorEdad'] = $this->model->obtenerCantidadUsuariosPorEdad();
        }

        $datosGrafico = [
            'etiquetas' => array_column($data['cantidadUsuariosPorEdad'], 'grupoEdad'),
            'valores' => array_column($data['cantidadUsuariosPorEdad'], 'cantidad'),
            'tituloDelGrafico' => 'Cantidad de Usuarios por Grupo de Edad',
        ];

        $data['graficoUsuariosPorEdad'] = GraphHelper::generarPieplot($datosGrafico);

        return $data;
    }


    public function generarEstadisticasPDF(){
        $html = isset($_POST['html']) ? $_POST['html'] : '';

        $styles = " <style> 
                        .tabla-estadisticas { 
                            width: 100%; 
                            border-collapse: collapse;
                            margin-bottom: 1.5em; 
                        } 
                        .tabla-estadisticas th, 
                        .tabla-estadisticas td { 
                            border: 1px solid #000; 
                            padding: 8px; 
                            text-align: left; 
                        } 
                        .tabla-estadisticas th { 
                            background-color: #f2f2f2; 
                        } 
                    </style> ";
        $baseURL = 'http://localhost/';
        $html = str_replace('src="/public/graficos/', 'src="' . $baseURL . 'public/graficos/', $html);
        $html = $styles . $html;

        PDFHelper::generarPDF($html);

        Redirecter::redirect("/usuario/showAdministrador");
    }

    private function obtenerFechaInicio($filtroDeFecha){
        switch($filtroDeFecha){
            case 'ultimo_mes':
                return date('Y-m-d', strtotime('-1 month'));
                break;
            case 'ultimo_año':
                return date('Y-m-d', strtotime('-1 year'));
                break;
            case 'ultima_semana':
            default:
                return date('Y-m-d', strtotime('-1 week'));
                break;
        }
    }
}