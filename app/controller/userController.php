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
            echo json_encode(['error' => 'ID no especificado.'], JSON_UNESCAPED_UNICODE);
        }
        break;

    case 'DELETE':
        if ($id) {
            deleteUser($userDB, $id);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'ID no especificado.'], JSON_UNESCAPED_UNICODE);
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
    $result = 'Error en body Postman';

    if (isset($data['nombre']) && isset($data['email'])) {
        $inserted = $userDB->create($data['nombre'], $data['email']);
        $result = json_encode(['inserted' => $inserted]);
    }

    echo $result;
}

function updateUser($userDB, $id)
{
    $idValid = Validator::cleanData([$id]);
    $data = json_decode(file_get_contents('php://input'), true);
    $result = 'Error en body Postman';

    if (isset($data['nombre']) && isset($data['email'])) {
        $updated = $userDB->update($idValid[0], $data['nombre'], $data['email']);
        $result =  json_encode(['updated' => $updated]); //true o false (1 o 0)
    }

    echo $result;
}

function deleteUser($userDB, $id)
{
    $idValid = Validator::cleanData([$id]);
    $deleted = $userDB->delete($idValid[0]);
    $result = json_encode(['deleted' => $deleted]);

    echo $result;
}
