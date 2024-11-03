<?php

class UsuarioModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function validarDatosRegistro($username, $mail, $password, $password2, $name, $date, $sex, $foto)
    {
        $errores = [];

        if(empty($mail) || !filter_var($mail, FILTER_VALIDATE_EMAIL)){
            $errores['mailInvalido'] = "Debes ingresar un mail valido";
        }
        if(empty($username) || strlen($username) < 4){
            $errores['usernameInvalido'] = "Debes ingresar un nombre de usuario mayor a 4 caracteres";
        }

        if(empty($password) || strlen($password) < 6){
            $errores['passwordInvalido'] = "La contraseña debe tener al menos 6 caracteres";
        }

        if($password != $password2){
            $errores['password2Invalido'] = "Las contraseñas no coinciden";
        }

        if(empty($name)){
            $errores['nameInvalido'] = "Debes ingresar tu nombre";
        }

        if (empty($date)) {
            $errores['dateInvalido'] = "La fecha de nacimiento es obligatoria.";
        } else {
            $fechaNacimiento = new DateTime($date);
            $hoy = new DateTime();
            $edad = $hoy->diff($fechaNacimiento)->y;

            if ($edad < 12) {
                $errores['dateInvalido'] = "Debes tener al menos 12 años para registrarte";
            }
        }

        if(empty($sex)){
            $errores['sexInvalido'] = "Debes seleccionar tu sexo";
        }

        if(isset($foto) && $foto['error'] !== UPLOAD_ERR_OK && $foto['size'] > 0){
            $errores['imagenInvalida'] = "Error al subir la imagen";
        }

        if (empty($mail) || empty($username) || empty($password) || empty($name) || empty($date) || empty($sex)) {
            $errores['camposVacios'] = "Completa todos los campos que se indican con (*)";
        }

        return $errores;

    }

    public function guardarUsuario($username, $mail, $password, $name, $date, $sex, $foto)
    {
        $nombreFoto = "";
        if(isset($foto)){
            $nombreFoto = $foto['name'];
            $archivoTemporal = $foto['tmp_name'];

            $dirDestino = $_SERVER['DOCUMENT_ROOT'] . '/public/images/';
            $dirFoto = $dirDestino . $nombreFoto;
            move_uploaded_file($archivoTemporal, $dirFoto);
        }

        $existe = $this->verificarExistenciaDeUsuario($username, $mail);
        $password = password_hash($password, PASSWORD_BCRYPT);

        if(!$existe) {
            $idSexo = "(SELECT id FROM sexo WHERE nombre LIKE '%" . $sex . "%')";
            $sql = "INSERT INTO usuario (mail, nombreUsuario, password, fechaNacimiento, nombreCompleto, foto, idSexo) VALUES ('" . $mail . "','" . $username . "','" . $password . "','" . $date . "','" . $name . "','" . $nombreFoto . "'," . $idSexo . ");";
            $this->database->add($sql);
            $resultado =[
                "exito" => true,
                "mensaje" => "Usuario registrado correctamente"
            ];
        } else{
          $resultado = [
              "exito" => false,
              "mensaje" => "El usuario ya existe"
          ];
        }

        return $resultado;
    }

    public function validarLogin($mail, $password)
    {
        if(empty($mail) && empty($password)){
            return ["exito"=>false, "mensaje"=>"Debes ingresar tu mail y contraseña"];
        }

        if (empty($mail)) {
            return ["exito" => false, "mensaje" => "Por favor, ingresa tu correo."];
        }

        if (empty($password)) {
            return ["exito" => false, "mensaje" => "Por favor, ingresa tu contraseña."];
        }

        if(!$this->validarUsuarioPorCorreo($mail)){
            return ["exito"=>false, "mensaje"=>"Correo incorrecto"];
        }

        if(!$this->validarUsuario($mail, $password)){
            return ["exito"=>false, "mensaje"=>"Contraseña incorrecta"];
        }
        return ["exito"=>true, "mensaje"=>"Inicio de sesion exitoso"];
    }

    private function verificarExistenciaDeUsuario($username, $mail) {
        $sql = "SELECT 1
                FROM usuario
                WHERE mail = '" . $mail . "'
                OR nombreUsuario = '" . $username . "'";

        $usuario = $this->database->query($sql);

        return sizeof($usuario) > 0;
    }

    public function validarUsuario($mail, $password) {
        $sql = "SELECT password 
            FROM usuario 
            WHERE mail = '" . $mail . "'";

        $usuario = $this->database->query($sql);

        if (sizeof($usuario) == 1) {
            $hashedPassword = $usuario[0]['password'];
            return password_verify($password, $hashedPassword);
        } else {
            return false;
        }
    }

    public function validarUsuarioPorCorreo($mail) {
        $sql = "SELECT 1 
            FROM usuario 
            WHERE mail = '" . $mail . "'";

        $usuario = $this->database->query($sql);

        return sizeof($usuario) == 1;
    }
    public function mostrarDatosUsuario($mail) {
        $sql = "SELECT *
        FROM usuario
        where mail = '" . $mail . "'";
        $usuario = $this->database->query($sql);

       

        return $usuario;
    }

    public function actualizarDatosPerfil($nombreUsuario, $mail, $nombreCompleto, $fechaNacimiento, $sexo, $foto, $password, $mailActual) {

        $sqlImagenActual = "SELECT foto FROM USUARIO WHERE mail = '" . $mailActual . "'";
        $imagenActual = $this->database->query($sqlImagenActual);


        $nombreImagenActual = !empty($imagenActual) ? $imagenActual[0]['foto'] : null;

        $sql = "UPDATE usuario 
        SET nombreUsuario = '" . $nombreUsuario . "',
        mail = '" . $mail . "', 
        nombreCompleto = '" . $nombreCompleto . "',
        fechaNacimiento = '" . $fechaNacimiento . "', 
        idSexo = (SELECT id FROM sexo WHERE nombre LIKE '%" . $sexo . "%')";


        $nuevoNombreImagen = $this->manejarImagen($foto, $nombreImagenActual);
        if ($nuevoNombreImagen) {
            $sql .= ", foto = '" . $nuevoNombreImagen . "'";
        }

        if (!empty($password)) {
            $password = password_hash($password, PASSWORD_BCRYPT);
            $sql .= ", password = '" . $password . "'";
        }

        $sql .= " WHERE mail = '" . $mailActual . "'";

        $cambio = $this->database->add($sql);

        return [
            "exito" => $cambio == 1,
            "mensaje" => $cambio == 1 ? "Cambios realizados correctamente" : "Los cambios no se pudieron realizar"
        ];
    }

    private function manejarImagen($foto, $nombreImagenActual) {
        if (!empty($foto['name'])) {
            $targetDir = "public/images/";
            $targetFile = basename($foto["name"]);


            if (move_uploaded_file($foto["tmp_name"], $targetDir . $targetFile)) {

                if (!empty($nombreImagenActual) && file_exists($targetDir . $nombreImagenActual)) {
                    $this->deleteImagen($nombreImagenActual);
                }
                return $targetFile;
            }
        }
        return null;
    }

    private function deleteImagen($nombreImagen) {

        $rutaImagenCompleta = $_SERVER['DOCUMENT_ROOT'] . '/public/images/' . $nombreImagen;

        if (file_exists($rutaImagenCompleta)) {
            unlink($rutaImagenCompleta);
        }
    }

    public function crearToken(){
        $token = substr(bin2hex(random_bytes(5)), 0, 10);
        return $token;
    }
    public function guardarTokenDeVerificacion($mail, $token) {
        $sql = "UPDATE usuario SET token_verificacion = '" . $token . "' WHERE mail = '" . $mail . "'";
        $this->database->add($sql);
    }
    public function crearEnlaceValidacion($idUsuario, $token){
        $enlaceValidacion = "http://localhost/usuario/validarCuenta?id=$idUsuario&token=$token";
        return $enlaceValidacion;
    }
    public function crearMensajeEmail($enlaceValidacion){
        return "Hola! Hacé click en el siguiente enlace para validar tu cuenta: <a href='$enlaceValidacion'>Click aquí</a>";
    }

    public function obtenerIdUsuarioConEmail($mail) {
        $sql = "SELECT id FROM usuario WHERE mail = '$mail' LIMIT 1";
        $resultado = $this->database->query($sql);
        return $resultado[0]['id'];
    }

    public function verificarToken($id, $token) {
        $sql = "SELECT token_verificacion FROM usuario WHERE id = $id AND cuenta_verificada = 'I'";
        $resultado = $this->database->query($sql);

        if (!empty($resultado) && $resultado[0]['token_verificacion'] === $token) {
            return ["exito" => true];
        } else {
            return ["exito" => false, "mensaje" => "Token inválido o cuenta ya verificada."];
        }
    }

    public function activarCuenta($id) {
        $sql = "UPDATE usuario SET cuenta_verificada = 'A', token_verificacion = NULL WHERE id = $id";
        $this->database->add($sql);
        return ['exito' => true];
    }

    public function estadoDeCuenta($id){
        $sql = "SELECT cuenta_verificada FROM usuario WHERE id = '$id'";
        $resultado = $this->database->query($sql);

        if (!empty($resultado) && isset($resultado[0]['cuenta_verificada'])) {
            return $resultado[0]['cuenta_verificada'];
        } else {
            return null;
        }
    }
    public function puntajeMaximoDeUsuario($idUsuario){
        $sql = "SELECT MAX(puntaje) as puntajeMaximo FROM partida WHERE idUsuario = '$idUsuario'";
        $resultado = $this->database->query($sql);
        return $resultado[0]['puntajeMaximo'] ?? 0;
    }

    public function validarEditarPerfil($username, $mail, $password, $password2, $name, $date, $sex, $foto, $usernameActual, $mailActual)
    {
        $errores = [];

        if (empty($mail) || !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $errores['mailInvalido'] = "Debes ingresar un mail válido";
        }

        if($this->verificarExistenciaDeUsuarioPorMail($mail, $mailActual)){
            $errores['mailExistente'] = "El mail ya existe";
        }

        if (empty($username) || strlen($username) < 4) {
            $errores['usernameInvalido'] = "Debes ingresar un nombre de usuario mayor a 4 caracteres";
        }

        if ($this->verificarExistenciaDeUsuarioPorUsuario($username, $usernameActual)) {
            $errores['usernameExistente'] = "El nombre de usuario ya existe";
        }

        if (!empty($password) && strlen($password) < 6) {
            $errores['passwordInvalido'] = "La contraseña debe tener al menos 6 caracteres";
        }

        if ($password !== $password2) {
            $errores['password2Invalido'] = "Las contraseñas no coinciden";
        }

        if (empty($name)) {
            $errores['nameInvalido'] = "Debes ingresar tu nombre";
        }

        if (empty($date) || (new DateTime())->diff(new DateTime($date))->y < 12) {
            $errores['dateInvalido'] = "Debes tener al menos 12 años para registrarte";
        }

        if (empty($sex)) {
            $errores['sexInvalido'] = "Debes seleccionar tu sexo";
        }

        if (isset($foto) && $foto['error'] !== UPLOAD_ERR_OK && $foto['size'] > 0) {
            $errores['fimagenInvalida'] = "Error al subir la imagen";
        }

        return $errores;
    }


    public function verificarExistenciaDeUsuarioPorMail($mail, $mailActual)
    {
        $sql = "SELECT 1 FROM usuario WHERE mail = '$mail'";
        $resultado = $this->database->query($sql);

        if(sizeof($resultado) > 0 && $mailActual != $mail){
            $resultado = true;
        } else {
            $resultado = false;
        }

        return $resultado;
    }

    public function verificarExistenciaDeUsuarioPorUsuario($username, $usernameActual)
    {
        $sql = "SELECT 1 FROM usuario WHERE nombreUsuario = '$username'";
        $resultado = $this->database->query($sql);

        if(sizeof($resultado) > 0 && $usernameActual != $username){
            $resultado = true;
        } else {
            $resultado = false;
        }

        return $resultado;
    }
    public function obtenerNombreUsuario($mail) {
        $sql = "SELECT nombreUsuario FROM usuario WHERE mail = '$mail' LIMIT 1";
        $resultado = $this->database->query($sql);
        return $resultado[0]['nombreUsuario'];
    }

    public function obtenerRankingUsuarios() {
        $sql = "SELECT nombreUsuario, MAX(puntaje) as puntajeMaximo 
        FROM usuario 
        JOIN partida ON usuario.id = partida.idUsuario 
        GROUP BY usuario.id
        ORDER BY puntajeMaximo DESC, usuario.id ASC
        LIMIT 50";

        $ranking = $this->database->query($sql);

        $posicion = 1;

        $rankingConPosiciones = [];
        $puntaje = null;

        foreach ($ranking as $usuario) {

            if($puntaje == null){
                $puntaje = $usuario['puntajeMaximo'];
            }

            if($puntaje != $usuario['puntajeMaximo']){
                $usuario['posicion'] = "#" . ++$posicion;
                $puntaje = $usuario['puntajeMaximo'];
            }else{
                $usuario['posicion'] = "#" . $posicion;
            }

           $rankingConPosiciones[] = $usuario;
        }

        return $rankingConPosiciones;
    }


    public function verPerfilUsuario($nombreUsuario) {
        $sql = "SELECT nombreUsuario, mail, foto, nombreCompleto, fechaNacimiento, MAX(puntaje) as puntajeMaximo
            FROM usuario 
            JOIN partida ON usuario.id = partida.idUsuario 
            WHERE nombreUsuario = '$nombreUsuario'";

        $perfil = $this->database->query($sql);
        return $perfil;


    }

    public function verPartidasPorUsuario($nombreUsuario) {
        $sql = "SELECT partida.idPartida, partida.puntaje
            FROM usuario
            JOIN partida ON usuario.id = partida.idUsuario 
            WHERE usuario.nombreUsuario = '$nombreUsuario'";

        $partidas = $this->database->query($sql);

        //Agrego un indice a cada partida
        $index = 1;
        foreach ($partidas as &$partida) {
            $partida['indice'] = $index++;
        }

        return $partidas;


    }

}