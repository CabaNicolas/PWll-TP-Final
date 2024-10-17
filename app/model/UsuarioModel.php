<?php

class UsuarioModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function guardarUsuario($username, $password)
    {
        // Verificar si el usuario ya existe
        $sql = "SELECT * FROM usuario WHERE username = ?";
        $result = $this->database->prepareAndExecute($sql, 's', [$username]);

        if ($result && $result->num_rows > 0) {
            return "error"; // Usuario ya registrado
        } else {
            // Si no se encontró el usuario, proceder a registrarlo

            $sql = "INSERT INTO usuario (username, password) VALUES (?, ?)";
            $insertResult = $this->database->prepareAndExecute($sql, 'ss', [$username, $password]);

            if ($insertResult) {
                return true;
            } else {
                return "error";
            }
        }
        }
    public function validarUsuario($username, $password) {
        $sql = "SELECT 1 
                FROM usuario 
                WHERE username = '" . $username. "' 
                AND password = '" . $password . "'";

        $usuario = $this->database->query($sql);

        return sizeof($usuario) == 1;
    }


}