<?php
require "../vendor/autoload.php";

use Dotenv\Dotenv;
use Src\Config\DatabaseConnector;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

try {
    $pdo = (new DatabaseConnector)->getConnection();
    $sql = file_get_contents(__DIR__ . "\migrations\commerce_v1.sql");
    $statements = explode(';', $sql);
    foreach ($statements as $statement) {
        if (!empty(trim($statement))) {
            $pdo->exec($statement);
        }
    }
} catch (PDOException $e) {
    echo $e->getMessage();
    error_log($e->getMessage());
}
/* 
DB_HOST=srv1158.hstgr.io
DB_PORT=3306
DB_NAME=u669059076_anuwrap
DB_DRIVER=mysql
DB_USERNAME=u669059076_dataflix	
DB_PASSWORD=Dataflix_75
SECRET_API_KEY = EUNILLELOVEKENT */