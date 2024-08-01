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
    $queryStr = "
        SELECT 
            orders.*, 
            users.username, 
            users.address,
            OrderItems.order_item_id,
            OrderItems.product_id,
            OrderItems.size_id,
            OrderItems.quantity,
            OrderItems.price,
            Product.product_name,
            Product.product_description,
            Product.product_image,
            ProductSize.size_label
        FROM orders
        JOIN users ON orders.user_id = users.user_id
        LEFT JOIN OrderItems ON orders.order_id = OrderItems.order_id
        LEFT JOIN Product ON OrderItems.product_id = Product.product_id
        LEFT JOIN ProductSize ON OrderItems.size_id = ProductSize.size_id
        WHERE orders.order_id = :id
    ";

    $stmt = $this->pdo->prepare($queryStr);

    try {
        $stmt->execute(array("id" => $id));
        $report = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $order = null;
        $orderItems = [];

        if ($report) {
            foreach ($report as $row) {
                if (!$order) {
                    $order = [
                        'order_id' => $row['order_id'],
                        'user_id' => $row['user_id'],
                        'total_amount' => $row['total_amount'],
                        'order_date' => $row['order_date'],
                        'status' => $row['status'], 
                        'username' => $row['username'],
                        'address' => $row['address'],
                        'items' => []
                    ];
                }

                if ($row['order_item_id']) {
                    $orderItems[] = [
                        'order_item_id' => $row['order_item_id'],
                        'product_id' => $row['product_id'],
                        'size_id' => $row['size_id'],
                        'quantity' => $row['quantity'],
                        'price' => $row['price'],
                        'product_name' => $row['product_name'],
                        'product_description' => $row['product_description'],
                        'product_image' => $row['product_image'],
                        'size_label' => $row['size_label']
                    ];
                }
            }
            $order['items'] = $orderItems;
        }

        return $order;
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return null;
    }
}


    function getAll($filter = "")
    {
        $queryStr = "
            SELECT 
                orders.*, 
                users.username, 
                users.address,
                OrderItems.order_item_id,
                OrderItems.product_id,
                OrderItems.size_id,
                OrderItems.quantity,
                OrderItems.price,
                Product.product_name,
                Product.product_description,
                Product.product_image,
                ProductSize.size_label
            FROM orders
            JOIN users ON orders.user_id = users.user_id
            LEFT JOIN OrderItems ON orders.order_id = OrderItems.order_id
            LEFT JOIN Product ON OrderItems.product_id = Product.product_id
            LEFT JOIN ProductSize ON OrderItems.size_id = ProductSize.size_id
        ";

        if ($filter) {
            $queryStr .= " WHERE $filter";
        }

        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute();
            $report = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $orders = [];

            if ($report) {
                foreach ($report as $row) {
                    $order_id = $row['order_id'];
                    
                    if (!isset($orders[$order_id])) {
                        $orders[$order_id] = [
                            'order_id' => $row['order_id'],
                            'user_id' => $row['user_id'],
                            'total_amount' => $row['total_amount'],
                            'status' => $row['status'], 
                            'order_date' => $row['order_date'],
                            'username' => $row['username'],
                            'address' => $row['address'],
                            'items' => []
                        ];
                    }

                    if ($row['order_item_id']) {
                        $orders[$order_id]['items'][] = [
                            'order_item_id' => $row['order_item_id'],
                            'product_id' => $row['product_id'],
                            'size_id' => $row['size_id'],
                            'quantity' => $row['quantity'],
                            'price' => $row['price'],
                            'product_name' => $row['product_name'],
                            'product_description' => $row['product_description'],
                            'product_image' => $row['product_image'],
                            'size_label' => $row['size_label']
                        ];
                    }
                }
            }

            return array_values($orders); // Return as a list of orders
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

        try {
            $stmt->execute(array(
                "user_id" => $user_id,
                "total_amount" => $total_amount
            ));
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
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

    function completeOrder($id)
    {
        $queryStr = "
            UPDATE orders 
            SET status = 'completed'
            WHERE order_id = :id
        ";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute(array(
                "id" => $id,
            ));
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
