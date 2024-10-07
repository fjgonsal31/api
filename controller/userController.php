<?php

require_once '../config/user.php';

// header('Content-Type: application/json');

$user = new User();

$method = $_SERVER['REQUEST_METHOD'];

$id = isset($_SERVER['REQUEST_URI']) ? isset(explode('=', $_SERVER['REQUEST_URI'])[1]) : null;

switch ($method) {
    case 'GET':
        if ($id) {
            $result = getUserId($user, $id);
        } else {
            $result = getAllUsers($user);
        }
        echo json_encode($result);
        break;

    default:
        http_response_code(405);
        echo json_encode(['Error' => 'Método no permitido!']);
        break;
}

function getUserId($user, $id)
{
    return $user->getId($id);
}

function getAllUsers($user)
{
    return $user->getData();
}
