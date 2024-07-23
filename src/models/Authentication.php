<?php

namespace Src\Models;

class Authentication
{
    private $pdo;
    function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    function get($key, $val)
    {
        $queryStr = "SELECT * FROM Users WHERE $key=:$key";
        $stmt = $this->pdo->prepare($queryStr);

        $stmt->execute(array(
            $key => $val
        ));

        $user = $stmt->fetch();
        return $user;
    }
}
