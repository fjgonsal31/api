<?php

function getParamsURI($uri)
{
    $array = explode('?', $uri);
    $params = [];


    if (count($array) > 1) {
        $items = explode('&', $array[1]);

        foreach ($items as $key => $value) {
            $item = explode('=', $value);

            if (count($item) == 2) {
                $params[$item[0]] = $item[1];
            }
        }
    }

    return $params;
}

function getParamValue($params, $paramKey)
{
    return isset($params[$paramKey]) ? $params[$paramKey] : null;
}
