<?php

require_once '../../data/userDB.php';
require_once '../../data/getParamsURI.php';

$userDB = new UserDB();

$method = $_SERVER['REQUEST_METHOD']; // GET, POST, PUT, DELETE
$params = getParamsURI($_SERVER['REQUEST_URI']);
$id = getParamValue($params, "id");

switch ($method) {
    case 'GET':
        if ($id) {
            $result = getUserId($userDB, $id);
        } else {
            $result = getAllUsers($userDB);
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        break;

    case 'POST':
        setUser($userDB);
        break;

    case 'PUT': //UPDATE
        if ($id) {
            updateUser($userDB, $id);
        } else {
            http_response_code(400);
            echo json_encode(['Error' => 'ID no especificado.'], JSON_UNESCAPED_UNICODE);
        }
        break;

    case 'DELETE':
        if ($id) {
            deleteUser($userDB, $id);
        } else {
            http_response_code(400);
            echo json_encode(['Error' => 'ID no especificado.'], JSON_UNESCAPED_UNICODE);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['Error' => 'Método no permitido!']);
        break;
}

function getUserId($userDB, $id)
{
    return $userDB->getId($id);
}

function getAllUsers($userDB)
{
    return $userDB->getData();
}

function setUser($userDB)
{
    $data = json_decode(file_get_contents('php://input'), true); // obtener datos introducidos en Postman

    if (isset($data['nombre'])) {
        if (isset($data['email'])) {
            $inserted = $userDB->create($data['nombre'], $data['email']);
            if ($inserted == 0) {
                $result = 'Datos ya existentes!';
                $result = json_encode(['insert' => $result]);
            } else {
                $result = json_encode(['insert' => $inserted]);
            }
        } else {
            $result = 'Email no enviado';
        }
    } else {
        $result = 'Nombre no enviado';
    }

    echo $result;
}

function updateUser($userDB, $id)
{
    $idValid = Validator::cleanData([$id]);
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($idValid[0])) {
        if (isset($data['nombre'])) {
            if (isset($data['email'])) {
                $updated = $userDB->update($idValid[0], $data['nombre'], $data['email']);
                if ($updated == 0) {
                    $result = 'Email ya existente!';
                    $result = json_encode(['update' => $result]);
                } else {
                    $result = json_encode(['update' => $updated]);
                }
            } else {
                $result = 'Email no enviado';
            }
        } else {
            $result = 'Nombre no enviado';
        }
    } else {
        $result = 'ID no válido';
    }

    echo $result;
}

function deleteUser($userDB, $id)
{
    $idValid = Validator::cleanData([$id]);
    $deleted = $userDB->delete($idValid[0]);
    $result = json_encode(['delete' => $deleted]);

    echo $result;
}
