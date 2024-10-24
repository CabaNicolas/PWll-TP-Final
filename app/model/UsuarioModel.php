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

        if(empty($mail) || !filter_var($mail, FILTER_VALIDATE_EMAIL)){
            $errores['mailInvalido'] = "Debes ingresar un mail valido";
        }
        if(empty($username) || strlen($username) < 4){
            $errores['usernameInvalido'] = "Debes ingresar un nombre de usuario mayor a 4 caracteres";
        }

        if(empty($password) || strlen($password) < 6){
            $errores['passwordInvalido'] = "La contraseña debe tener al menos 6 caracteres";
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
            //TODO: Agregar mas validaciones de imagenes.
            $nombreFoto = $foto['name'];
            $archivoTemporal = $foto['tmp_name'];

            $dirDestino = $_SERVER['DOCUMENT_ROOT'] . '/public/images/';
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

    public function actualizarDatosPerfil($nombreUsuario, $mail, $nombreCompleto, $fechaNacimiento, $sexo, $foto, $password) {

        $mailActual = $_SESSION['mail'];
        $sqlImagenActual = "SELECT foto FROM USUARIO WHERE mail = '" . $mailActual . "'";
        $imagenActual = $this->database->query($sqlImagenActual);


        $nombreImagenActual = !empty($imagenActual) ? $imagenActual[0]['foto'] : null;

        $sql = "UPDATE usuario 
        SET nombreUsuario = '" . $nombreUsuario . "',
        mail = '" . $mail . "', 
        nombreCompleto = '" . $nombreCompleto . "',
        fechaNacimiento = '" . $fechaNacimiento . "', 
        idSexo = (SELECT id FROM sexo WHERE nombre LIKE '%" . $sexo . "%')";


        $nuevoNombreImagen = $this->manejarFoto($foto, $nombreImagenActual);
        if ($nuevoNombreImagen) {
            $sql .= ", foto = '" . $nuevoNombreImagen . "'";
        }

        if (!empty($password)) {
            $sql .= ", password = '" . $password . "'";
        }

        $sql .= " WHERE mail = '" . $mailActual . "'";

        $cambio = $this->database->add($sql);

        return [
            "exito" => $cambio == 1,
            "mensaje" => $cambio == 1 ? "Cambios realizados correctamente" : "Los cambios no se pudieron realizar"
        ];
    }

    private function manejarFoto($foto, $nombreImagenActual) {
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



}