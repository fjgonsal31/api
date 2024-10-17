<?php

require_once 'db.php';

class MainDB
{

    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getData()
    {
        $result = $this->db->query("SELECT p.titulo, p.precio, d.nombre, d.apellido, p.imagen FROM pelicula p LEFT JOIN director d ON p.id_director = d.id;");

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
