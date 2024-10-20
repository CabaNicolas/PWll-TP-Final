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
        $errores = [
            'username' => '',
            'mail' => '',
            'password' => '',
            'name' => '',
            'date' => '',
            'sex' => '',
            'foto' => ''
        ];

        if (empty($mail)) {
            $errores['mail'] = "Debes ingresar un mail.";
        } elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $errores['mail'] = "Debes ingresar un mail válido.";
        }

        if (empty($username)) {
            $errores['username'] = "Debes ingresar un nombre de usuario.";
        } elseif (strlen($username) < 4) {
            $errores['username'] = "El nombre de usuario debe tener al menos 4 caracteres.";
        }

        if (empty($password)) {
            $errores['password'] = "Debes ingresar una contraseña.";
        } elseif (strlen($password) < 6) {
            $errores['password'] = "La contraseña debe tener al menos 6 caracteres.";
        }

        if (empty($name)) {
            $errores['name'] = "Debes ingresar tu nombre completo.";
        }

        if (empty($date)) {
            $errores['date'] = "Debes ingresar tu fecha de nacimiento.";
        } else {
            $fechaNacimiento = new DateTime($date);
            $hoy = new DateTime();
            $edad = $hoy->diff($fechaNacimiento)->y;
            if ($edad < 12) {
                $errores['date'] = "Debes tener al menos 12 años para registrarte.";
            }
        }

        if (empty($sex)) {

            $errores['sex'] = "Debes seleccionar tu sexo.";
        }

        if (isset($foto) && $foto['error'] !== UPLOAD_ERR_OK) {
            $errores['foto'] = "Error al subir la imagen.";
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

        $errores = [
            'mail' => '',
            'password' => '',
            'mensaje' => ''
        ];


        if (empty($mail)) {
            $errores['mail'] = "Por favor, ingresa tu correo.";
        }

        if (empty($password)) {
            $errores['password'] = "Por favor, ingresa tu contraseña.";
        }

        if(!empty($mail) && !$this->validarUsuarioPorCorreo($mail)){
            $errores['mail'] = "Correo incorrecto";
        }

        if(!empty($mail) && !empty($password) && !$this->validarUsuario($mail, $password)){
            $errores['password'] = "Contraseña incorrecta";
        }

        if (empty($errores['mail']) && empty($errores['password']) && empty($errores['mensaje'])) {
            return ["exito" => true, "mensaje" => "Inicio de sesión exitoso"];
        }

        return ["exito" => false, "errores" => $errores];
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

}