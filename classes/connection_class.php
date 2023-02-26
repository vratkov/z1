<?php

chdir(dirname(__FILE__));
require_once('../config.php');

class Connection {

    protected static $connection;

    private function __construct() {
        try {

            self::$connection = new mysqli(
                'db_data',
                MYSQL_USERNAME,
                MYSQL_PASSWORD,
                MYSQL_DATABASE,
                   MYSQL_PORT
                   );

        } catch (mysqli $e) {
            echo "MySql Connection Error: " . $e->getMessage();
        }
    }

    public static function getConnection() {
        if (!self::$connection) {
            new Connection();
        }

        return self::$connection;
    }

    public static function closeConnection() {
        self::$connection?->close();

        return True;
    }

}
