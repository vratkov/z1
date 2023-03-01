<?php

require_once('classes/user_service_class.php');
require_once('classes/connection_class.php');
require_once('config.php');

function executeQuery($sql_query) {
    $mysqli = Connection::getConnection();
    $result = $mysqli->query($sql_query);

    if (!$result) {
        echo "{$mysqli->error} SQL: {$sql_query}" . PHP_EOL;
        return FALSE;
    }

    return $result;
}

function selectQuery($sql_query) {
    $result = executeQuery($sql_query);
    return $result->fetch_all(MYSQLI_ASSOC);
}
