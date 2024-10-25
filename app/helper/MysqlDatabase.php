<?php
class MysqlDatabase
{
    private $conn;

    public function __construct($host, $port, $username, $password, $database)
    {
        $this->conn = mysqli_connect($host, $username, $password, $database, $port);

        if (!$this->conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }

    public function query($sql){
        $result = mysqli_query($this->conn, $sql);
        return  mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function add($sql){
        $result = mysqli_query($this->conn, $sql);
        return mysqli_affected_rows($this->conn);
    }
    public function lastInsertId()
    {
        return mysqli_insert_id($this->conn);
    }

    public function __destruct()
    {
        mysqli_close($this->conn);
    }
}
