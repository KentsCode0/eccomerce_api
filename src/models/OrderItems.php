<?php

namespace Src\Models;

use PDOException;

class OrderItems
{
    private $pdo;
    function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    function get($id)
    {
        $queryStr = "SELECT * FROM orderitems WHERE order_item_id = :id";
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
            $queryStr = "SELECT * FROM orderitems";
        } else {
            $queryStr = "SELECT * FROM orderitems WHERE $filter";
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

    public function create($request)
    {
        $queryStr = "INSERT INTO orderitems (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute([
                "order_id" => $request["order_id"],
                "product_id" => $request["product_id"],
                "quantity" => $request["quantity"],
                "price" => $request["price"]
            ]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    function delete($id)
    {
        $queryStr = "DELETE FROM orderitems WHERE order_item_id = :id";
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
        $order_id = $request["order_id"];
        $product_id = $request["product_id"];
        $quantity = $request["quantity"];
        $price = $request["price"];

        $queryStr = "UPDATE orderitems SET order_id=:order_id, product_id=:product_id, quantity=:quantity, price=:price WHERE order_item_id = :id";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute(array(
                "order_id" => $order_id,
                "product_id" => $product_id,
                "quantity" => $quantity,
                "price" => $price,
                "id" => $id
            ));
            return $id;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
