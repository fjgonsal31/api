<?php

require_once 'db.php';

class User
{

    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getData()
    {
        $result = $this->db->query("SELECT id, nombre, email FROM usuario;");

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getId($id)
    {
        $result = $this->db->query("SELECT id, nombre, email FROM usuario WHERE id = ?;", [$id]);

        return $result->fetch_assoc();
    }
}
