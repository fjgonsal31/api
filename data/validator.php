<?php

class Validator
{

    public static function cleanData($data)
    {
        $datosRevisados = [];

        foreach ($data as $key => $value) {
            $datosRevisados[$key] = htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
        }

        return $datosRevisados;
    }

    public static function validateNameEmail($data)
    {
        $errors = [];

        if (!isset($data['nombre']) || empty(trim($data['nombre']))) {
            $errors['nombre'] = 'El nombre es necesario.';
        } elseif (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]*$/", $data['nombre'])) {
            $errors['nombre'] = 'El nombre debe contener letras y espacios únicamente.';
        } elseif (strlen($data['nombre'] > 2 && strlen($data['nombre'] < 50))) {
            $errors['nombre'] = 'El nombre debe tener más de 1 y menos de 50 caracteres.';
        }

        if (!isset($data['email']) || empty(trim($data['email']))) {
            $errors['email'] = 'El email es necesario.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El email debe tener un formato válido.';
        } elseif (strlen($data['email'] > 6 && strlen($data['email'] < 50))) {
            $errors['email'] = 'El email debe tener más de 6 y menos de 50 caracteres.';
        }

        return $errors;
    }
}
