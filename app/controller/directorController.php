<?php

require_once '../../data/directorDB.php';
require_once '../../data/getParamsURI.php';

$directorDB = new directorDB();

$method = $_SERVER['REQUEST_METHOD']; // GET, POST, PUT, DELETE
$params = getParamsURI($_SERVER['REQUEST_URI']);
$id = getParamValue($params, "id");

switch ($method) {
    case 'GET':
        if ($id) {
            $result = getDirectorId($directorDB, $id);
        } else {
            $result = getAllDirector($directorDB);
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        break;

    case 'POST':
        setDirector($directorDB);
        break;

    case 'PUT': //UPDATE
        if ($id) {
            updateDirector($directorDB, $id);
        } else {
            http_response_code(400);
            echo json_encode(['Error' => 'ID no especificado.'], JSON_UNESCAPED_UNICODE);
        }
        break;

    case 'DELETE':
        if ($id) {
            deleteDirector($directorDB, $id);
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

function getDirectorId($directorDB, $id)
{
    return $directorDB->getId($id);
}

function getAllDirector($directorDB)
{
    return $directorDB->getData();
}

function setDirector($directorDB)
{
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['nombre'])) {
        if (isset($data['apellido'])) {
            if (isset($data['f_nacimiento'])) {
                if (isset($data['biografia'])) {
                    $inserted = $directorDB->create($data['nombre'], $data['apellido'], $data['f_nacimiento'], $data['biografia']);
                    if ($inserted == 0) {
                        $result = json_encode(['error' => 'Datos ya existentes!']);
                    } else {
                        $result = json_encode(['insert' => $inserted]);
                    }
                } else {
                    $result = json_encode(['error' => 'Biografía no enviada']);
                }
            } else {
                $result = json_encode(['error' => 'Fecha de nacimiento no enviada']);
            }
        } else {
            $result = json_encode(['error' => 'Apellido no enviado']);
        }
    } else {
        $result = json_encode(['error' => 'Nombre no enviado']);
    }

    echo $result;
}


function updateDirector($directorDB, $id)
{
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['nombre'])) {
        if (isset($data['apellido'])) {
            if (isset($data['f_nacimiento'])) {
                if (isset($data['biografia'])) {
                    $updated = $directorDB->update($id[0], $data['nombre'], $data['apellido'], $data['f_nacimiento'], $data['biografia']);

                    if ($updated == 0) {
                        $result = json_encode(['error' => 'Director ya existente!']);
                    } else {
                        $result = json_encode(['update' => $updated]);
                    }
                } else {
                    $result = json_encode(['error' => 'Biografía no enviada']);
                }
            } else {
                $result = json_encode(['error' => 'Fecha de nacimiento no enviada']);
            }
        } else {
            $result = json_encode(['error' => 'Apellido no enviado']);
        }
    } else {
        $result = json_encode(['error' => 'Nombre no enviado']);
    }

    echo $result;
}


function deleteDirector($directorDB, $id)
{
    $deleted = $directorDB->delete($id);
    $result = json_encode(['delete' => $deleted]);

    echo $result;
}
