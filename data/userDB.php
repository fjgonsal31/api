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

        $result = $this->db->query("SELECT id, email FROM usuario WHERE email = ?;", [$dataRevisados['email']]);

        if ($result->num_rows > 0) {
            return 'Email ya existente!';
        } else {
            $this->db->query("INSERT INTO usuario (nombre, email) VALUES (?, ?);", [$dataRevisados['nombre'], $dataRevisados['email']]);

            return $this->db->query("SELECT LAST_INSERT_ID() AS newId;")->fetch_assoc()['newId'];
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

        $result = $this->db->query("SELECT SUM(CASE WHEN id = ? THEN 1 ELSE 0 END) AS count_id, SUM(CASE WHEN email = ? THEN 1 ELSE 0 END) AS count_email FROM usuario WHERE id = ? OR email = ?;", [$id, $dataRevisados['email'], $id, $dataRevisados['email']])->fetch_assoc();

        if ($result['count_id'] == 1) {
            if ($result['count_email'] == 0) {
                $this->db->query("UPDATE usuario SET nombre = ?, email = ? WHERE id = ?;", [$dataRevisados['nombre'], $dataRevisados['email'], $id]);

                return $this->db->query("SELECT ROW_COUNT() AS affected;")->fetch_assoc()['affected'];
            } else {
                return 'Email ya existente!';
            }
        } else {
            return 'ID no existente!';
        }
    }

    public function delete($id)
    {
        $this->db->query("DELETE FROM usuario WHERE id = ?;", [$id]);

        return $this->db->query("SELECT ROW_COUNT() AS affected;")->fetch_assoc()['affected'];
    }
}
