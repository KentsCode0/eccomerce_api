<?php

namespace Src\Models;

use PDOException;

class Users
{
    private $pdo;
    function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    function get($id)
    {
        $queryStr = "SELECT * FROM users WHERE user_id = :id";

        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute(array(
                "id" => $id
            ));

            $report = $stmt->fetch();
            return $report;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    function getAll($filter = "")
    {
        if ($filter == "") {
            $queryStr = "SELECT * FROM users";
        } else {
            $queryStr = "SELECT * FROM users WHERE $filter";
        }

        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute();

            $report = $stmt->fetchAll();
            return $report;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }
    function create($request)
    {
        $username = $request["username"];
        $email = $request["email"];
        $password = $request["password"];

        $queryStr = "INSERT INTO users(username, email, password) VALUES
        (:username, :email, :password)";

        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute(array(
                "username" => $username,
                "email" => $email,
                "password" => password_hash($password, PASSWORD_DEFAULT),
            ));
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }


    function delete($id)
    {
        $queryStr = "DELETE FROM users WHERE user_id = :id";

        $stmt = $this->pdo->prepare($queryStr);
        try {
            $stmt->execute(
                array(
                    "id" => $id,
                )
            );
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    function update($request, $id)
    {
        $username = $request["username"];
        $password = $request["password"];

        $queryStr = "UPDATE Report 
            SET username=:username, password=:password WHERE user_id = :id";

        $stmt = $this->pdo->prepare($queryStr);
        try {
            $stmt->execute(
                array(
                    "username" => $username,
                    "password" => $password,
                    "id" => $id
                )
            );
            return $id;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

}
