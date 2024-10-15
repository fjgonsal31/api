<?php

require_once 'db.php';
// require_once 'validator.php';
// require_once 'validatorException.php';

class DirectorDB
{

    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getData()
    {
        $result = $this->db->query("SELECT id, nombre, apellido, f_nacimiento, biografia FROM director;");

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getId($id)
    {
        $result = $this->db->query("SELECT id, nombre, apellido, f_nacimiento, biografia FROM director WHERE id = ?;", [$id]);

        return $result->fetch_assoc();
    }

    public function create($nombre, $apellido, $f_nacimiento, $biografia)
    {
        $data = ['nombre' => $nombre, 'apellido' => $apellido, 'f_nacimiento' => $f_nacimiento, 'biografia' => $biografia];
        // $dataRevisados = Validator::cleanData($data);
        // $errors = Validator::validateNameEmail($dataRevisados);

        // if (!empty($errors)) {
        //     $errors =  new ValidatorException($errors);

        //     return $errors->__get('errors');
        // }

        $result = $this->db->query("SELECT id, nombre, apellido, f_nacimiento, biografia FROM director WHERE nombre = ? AND apellido = ?;", [$data['nombre'], $data['apellido']]);

        if ($result->num_rows > 0) {
            return 'Director ya existente!';
        } else {
            $this->db->query("INSERT INTO director (nombre, apellido, f_nacimiento, biografia) VALUES (?, ?, ?, ?);", [$data['nombre'], $data['apellido'], $data['f_nacimiento'], $data['biografia']]);

            return $this->db->query("SELECT LAST_INSERT_ID() AS newId;")->fetch_assoc()['newId'];
        }
    }

    public function update($id, $nombre, $apellido, $f_nacimiento, $biografia)
    {
        $data = ['nombre' => $nombre, 'apellido' => $apellido, 'f_nacimiento' => $f_nacimiento, 'biografia' => $biografia];

        // $dataRevisados = Validator::cleanData($data);
        // $errors = Validator::validateNameEmail($dataRevisados);

        // if (!empty($errors)) {
        //     $errors =  new ValidatorException($errors);

        //     return $errors->__get('errors');
        // }

        $result = $this->db->query("SELECT nombre, apellido FROM director WHERE nombre = ? AND apellido = ?;", [$data['nombre'], $data['apellido']])->fetch_assoc();

        if (!$result) {

            $this->db->query("UPDATE director SET nombre = ?, apellido = ?, f_nacimiento = ?, biografia = ? WHERE id = ?;", [$data['nombre'], $data['apellido'], $data['f_nacimiento'], $data['biografia'], $id]);

            return $this->db->query("SELECT ROW_COUNT() AS affected;")->fetch_assoc()['affected'];
        } else {
            return 'Director ya existente!';
        }
    }

    public function delete($id)
    {
        $this->db->query("DELETE FROM director WHERE id = ?;", [$id]);

        return $this->db->query("SELECT ROW_COUNT() AS affected;")->fetch_assoc()['affected'];
    }
}
