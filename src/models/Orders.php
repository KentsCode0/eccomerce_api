<?php

namespace Src\Models;

use PDOException;

class Orders
{
    private $pdo;
    function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    function get($id)
    {
        $queryStr = "SELECT * FROM orders WHERE order_id = :id";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute(array("id" => $id));
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
            $queryStr = "SELECT * FROM orders";
        } else {
            $queryStr = "SELECT * FROM orders WHERE $filter";
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
    $user_id = $request["user_id"];
    $total_amount = $request["total_amount"];

    $queryStr = "INSERT INTO orders(user_id, total_amount) VALUES (:user_id, :total_amount)";
    $stmt = $this->pdo->prepare($queryStr);

    // Log the query and parameters
    error_log("Executing SQL: $queryStr");
    error_log("Parameters: " . print_r(array(
        "user_id" => $user_id,
        "total_amount" => $total_amount
    ), true));

    try {
        $stmt->execute(array(
            "user_id" => $user_id,
            "total_amount" => $total_amount
        ));
        return $this->pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("SQL Error: " . $e->getMessage());
        return false;
    }
}


    function delete($id)
    {
        $queryStr = "DELETE FROM orders WHERE order_id = :id";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute(array("id" => $id));
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    function update($request, $id)
    {
        $user_id = $request["user_id"];
        $total_amount = $request["total_amount"];

        $queryStr = "UPDATE orders SET user_id=:user_id, total_amount=:total_amount WHERE order_id = :id";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute(array(
                "user_id" => $user_id,
                "total_amount" => $total_amount,
                "id" => $id
            ));
            return $id;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
