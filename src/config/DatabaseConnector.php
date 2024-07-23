<?php

namespace Src\Config;

use PDO;

date_default_timezone_set("Asia/Manila");

class DatabaseConnector
{
    private $pdo = null;
    private $user;
    private $password;
    private $connectionString;

    private static $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    function __construct()
    {
        $this->user = $_ENV['DB_USERNAME'];
        $this->password = $_ENV['DB_PASSWORD'];
        $this->connectionString = "{$_ENV['DB_DRIVER']}:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_NAME']};charset=utf8mb4";
    }
    function getConnection()
    {
        try {
            $this->pdo = new PDO($this->connectionString, $this->user, $this->password, DatabaseConnector::$options);
            return $this->pdo;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
}
