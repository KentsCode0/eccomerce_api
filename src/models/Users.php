<?php

namespace Src\Models;

use PDOException;
use Exception;
class Users
{
    private $pdo;
    private static $base_directory = "../uploads/users/";
    private static $path = "http://localhost/ecommerce-api/public/";
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

    function update($id, $request)
{
    $fields = [];
    $params = [];

    // Build dynamic query and parameters
    foreach ($request as $key => $value) {
        if ($key !== 'user_id' && !empty($value)) {
            $fields[] = "$key = :$key";
            $params[$key] = $value;
        }
    }

    if (empty($fields)) {
        return false; // No fields to update
    }

    $fieldsList = implode(", ", $fields);
    $queryStr = "UPDATE users SET $fieldsList WHERE user_id = :id";
    $params['id'] = $id;

    $stmt = $this->pdo->prepare($queryStr);

    try {
        $stmt->execute($params);
        return $id;
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return false;
    }
}


    function uploadUserAvatar($id, $files)
    {
        $target_file = self::$base_directory . basename($id . "_" . $files['image']['name']);

        try {
            if (!move_uploaded_file($files['image']['tmp_name'], $target_file)) {
                throw new Exception('Failed to move uploaded file.');
            }
        } catch (Exception $e) {
            error_log('Upload Error: ' . $e->getMessage());
            return false;
        }

        $queryStr = "UPDATE users SET avatar=:avatar WHERE user_id = :id";

        try {
            $stmt = $this->pdo->prepare($queryStr);
            $stmt->execute([
                "avatar" => self::$path . $target_file,
                "id" => $id
            ]);
            return $id;
        } catch (PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            return false;
        }
    }

}
