<?php
class MysqlObjectDatabase
{
    private $conn;

    public function __construct($host, $port, $user, $password, $database)
    {
        $this->conn = new mysqli($host, $user, $password, $database, $port);

        // Verificar si la conexión fue exitosa
        if ($this->conn->connect_error) {
            die("Error en la conexión: " . $this->conn->connect_error);
        }
    }

    // Método para consultas SELECT simples
    public function query($sql)
    {
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Método para consultas como INSERT, UPDATE, DELETE sin parámetros
    public function execute($sql)
    {
        $this->conn->query($sql);
        return $this->conn->affected_rows;
    }

    // Nuevo método para consultas preparadas con parámetros
    public function prepareAndExecute($sql, $types, $params)
    {
        // Preparar la consulta
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die("Error en la preparación: " . $this->conn->error);
        }

        // Vincular los parámetros con bind_param()
        $stmt->bind_param($types, ...$params);

        // Ejecutar la consulta
        $stmt->execute();

        // Verificar errores de ejecución
        if ($stmt->error) {
            die("Error en la ejecución: " . $stmt->error);
        }

        // Obtener filas afectadas
        return $stmt->affected_rows;
    }

    // Cerrar la conexión al destruir la instancia
    public function __destruct()
    {
        $this->conn->close();
    }
}
?>
