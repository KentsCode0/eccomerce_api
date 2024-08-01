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
        $queryStr = "SELECT OrderItems.*, Product.product_name AS product_name, Product.product_description AS product_description, Product.product_image AS product_image, ProductSize.size_label AS size_name 
                     FROM OrderItems 
                     JOIN Product ON OrderItems.product_id = Product.product_id
                     JOIN ProductSize ON OrderItems.size_id = ProductSize.size_id
                     WHERE OrderItems.order_item_id = :id";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute(["id" => $id]);
            $report = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $report;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    function getAll($orderId)
    {
        $queryStr = "SELECT OrderItems.*, Product.product_name AS product_name, Product.product_description AS product_description, Product.product_image AS product_image, ProductSize.size_label AS size_name 
                     FROM OrderItems 
                     JOIN Product ON OrderItems.product_id = Product.product_id
                     JOIN ProductSize ON OrderItems.size_id = ProductSize.size_id
                     WHERE OrderItems.order_id = :orderId";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute(["orderId" => $orderId]);
            $report = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $report;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    function create($request)
    {
        $queryStr = "INSERT INTO OrderItems(order_id, product_id, size_id, quantity, price) 
                     VALUES (:order_id, :product_id, :size_id, :quantity, :price)";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute([
                "order_id" => $request["order_id"],
                "product_id" => $request["product_id"],
                "size_id" => $request["size_id"],
                "quantity" => $request["quantity"],
                "price" => $request["price"]
            ]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return false;
        }
    }

    function delete($id)
    {
        $queryStr = "DELETE FROM OrderItems WHERE order_item_id = :id";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute(["id" => $id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    function update($request, $id)
    {
        $queryStr = "UPDATE OrderItems 
                     SET order_id = :order_id, product_id = :product_id, size_id = :size_id, quantity = :quantity, price = :price 
                     WHERE order_item_id = :id";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute([
                "order_id" => $request["order_id"],
                "product_id" => $request["product_id"],
                "size_id" => $request["size_id"],
                "quantity" => $request["quantity"],
                "price" => $request["price"],
                "id" => $id
            ]);
            return $id;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
