<?php

require_once('vendor/autoload.php');

if (file_exists(__DIR__ . "/.env")) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

session_status() === PHP_SESSION_ACTIVE || session_start();

//error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
error_reporting(0);

define("PHP_ENVIRONMENT", $_ENV['PHP_ENVIRONMENT']);

define("MYSQL_SERVER", $_ENV['MYSQL_SERVER']);
define("MYSQL_USERNAME", $_ENV['MYSQL_USERNAME']);
define("MYSQL_PASSWORD", $_ENV['MYSQL_PASSWORD']);
define("MYSQL_DATABASE", $_ENV['MYSQL_DATABASE']);
define("MYSQL_PORT", $_ENV['MYSQL_PORT']);

define("MAIN_URL", $_ENV['MAIN_URL']);
define("SALT", $_ENV['SALT']);
