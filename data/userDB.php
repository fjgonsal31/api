<?php

require_once 'db.php';
require_once 'validator.php';
require_once 'validatorException.php';

class UserDB
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

    public function create($nombre, $email)
    {
        $data = ['nombre' => $nombre, 'email' => $email];
        $dataRevisados = Validator::cleanData($data);
        $errors = Validator::validateNameEmail($dataRevisados);

        if (!empty($errors)) {
            $errors =  new ValidatorException($errors);

            return $errors->__get('errors');
        }
        try {
            $this->db->query("INSERT INTO usuario (nombre, email) VALUES (?, ?);", [$dataRevisados['nombre'], $dataRevisados['email']]);

            return $this->db->query("SELECT count(LAST_INSERT_ID()) AS inserted;")->fetch_assoc()['inserted'];
        } catch (\Throwable $th) {
            return '0';
        }
    }

    public function update($id, $nombre, $email)
    {
        $data = ['nombre' => $nombre, 'email' => $email];
        $dataRevisados = Validator::cleanData($data);
        $errors = Validator::validateNameEmail($dataRevisados);

        if (!empty($errors)) {
            $errors =  new ValidatorException($errors);

            return $errors->__get('errors');
        }

        $this->db->query("UPDATE usuario SET nombre = ?, email = ? WHERE id = ?;", [$nombre, $email, $id]);

        return $this->db->query("SELECT ROW_COUNT() AS affected;")->fetch_assoc()['affected'];
    }

    public function delete($id)
    {
        $this->db->query("DELETE FROM usuario WHERE id = ?;", [$id]);

        return $this->db->query("SELECT ROW_COUNT() AS affected;")->fetch_assoc()['affected'];
    }
}
