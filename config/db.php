<?php

require_once 'conexionDB.php';

class Database
{

    private $conn;

    public function __construct()
    {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($this->conn->connect_error) {
            die("Error de conexión: " . $this->conn->connect_error);
        }
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Error en la query: " . $this->conn->error);
        }

        if (!empty($params)) {
            $types = str_repeat("s", count($params));
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();

        $result = $stmt->get_result();

        return $result;
    }

    public function close()
    {
        $this->conn->close();
    }
}
