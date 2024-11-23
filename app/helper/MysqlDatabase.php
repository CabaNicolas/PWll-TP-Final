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

    public function query($sql, $params = [])
    {
        $stmt = mysqli_prepare($this->conn, $sql);

        if ($stmt === false) {
            die('MySQL prepare failed: ' . mysqli_error($this->conn));
        }

        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_free_result($result);
        mysqli_stmt_close($stmt);

        return $data;
    }

    public function add($sql, $params = [])
    {
        $stmt = mysqli_prepare($this->conn, $sql);

        if ($stmt === false) {
            die('MySQL prepare failed: ' . mysqli_error($this->conn));
        }

        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }

        mysqli_stmt_execute($stmt);

        $affectedRows = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);

        return $affectedRows;
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
