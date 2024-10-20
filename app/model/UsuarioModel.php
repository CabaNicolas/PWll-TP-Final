<?php

class UsuarioModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function validarDatosRegistro($username, $mail, $password, $name, $date, $sex, $foto)
    {
        $errores = [];

        if (empty($mail) || empty($username) || empty($password) || empty($name) || empty($date) || empty($sex)) {
            $errores[] = "Completa todos los campos";
            return $errores;
        }

        if(empty($mail) || !filter_var($mail, FILTER_VALIDATE_EMAIL)){
            $errores[] = "Debes ingresar un mail valido";
        }
        if(empty($username) || strlen($username) < 4){
            $errores[] = "Debes ingresar un nombre de usuario mayor a 4 caracteres";
        }

        if(empty($password) || strlen($password) < 6){
            $errores[] = "La contraseña debe tener al menos 6 caracteres";
        }

        if(empty($name)){
            $errores[] = "Debes ingresar tu nombre";
        }

        if (empty($date)) {
            $errores[] = "La fecha de nacimiento es obligatoria.";
        } else {
            $fechaNacimiento = new DateTime($date);
            $hoy = new DateTime();
            $edad = $hoy->diff($fechaNacimiento)->y;

            if ($edad < 12) {
                $errores[] = "Debes tener al menos 12 años para registrarte";
            }
        }

        if(empty($sex)){
            $errores[] = "Debes seleccionar tu sexo";
        }

        if(isset($foto) && $foto['error'] !== UPLOAD_ERR_OK){
            $errores[] = "Error al subir la imagen";
        }

        return $errores;

    }

    public function guardarUsuario($username, $mail, $password, $name, $date, $sex, $foto)
    {
        $nombreFoto = "";
        if(isset($foto)){
            //TODO: Agregar mas validaciones de imagenes.
            $nombreFoto = $foto['name'];
            $archivoTemporal = $foto['tmp_name'];

            $dirDestino = $_SERVER['DOCUMENT_ROOT'] . '/public/Imagenes/';
            $dirFoto = $dirDestino . $nombreFoto;
            move_uploaded_file($archivoTemporal, $dirFoto);
        }

        //TODO:Agregar validaciones de campos.

        $existe = $this->verificarExistenciaDeUsuario($username, $mail);

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
            $mail;
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
        $sql = "SELECT 1 
                FROM usuario 
                WHERE mail = '" . $mail. "' 
                AND password = '" . $password . "'";

        $usuario = $this->database->query($sql);

        return sizeof($usuario) == 1;
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

    public function actualizarDatosPerfil($mail, $nombre, $fechaNacimiento, $sexo, $foto, $password) {


        $sql = "UPDATE usuario 
                SET nombreUsuario = '" . $nombre . "', 
                mail = '" . $mail . "',
                fechaNacimiento = '" . $fechaNacimiento . "', 
                idSexo = (SELECT id FROM sexo WHERE nombre LIKE '%" . $sexo . "%')";



        if (!empty($password)) {
            $sql .= ", password = '" . $password . "'";
        }

        $sql .= " WHERE mail = '" . $mail . "'";

        $cambio=$this->database->add($sql);

        if ($cambio == 1) {
            $cambio =[
                "exito" => true,
                "mensaje" => "Cambios realizados correctamente"
            ];
        } else{
            $cambio = [
                "exito" => false,
                "mensaje" => "Los cambios no se pudieron realizar"
            ];
        }
        return $cambio;
    }

}