<?php

class UsuarioModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
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