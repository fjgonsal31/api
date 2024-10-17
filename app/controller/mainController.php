<?php

require_once '../../data/mainDB.php';

$mainDB = new mainDB();

$method = $_SERVER['REQUEST_METHOD']; // GET, POST, PUT, DELETE

if ($method == 'GET') {
    $result = getAllPeliculas($mainDB);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}

function getAllPeliculas($mainDB)
{
    return $mainDB->getData();
}
