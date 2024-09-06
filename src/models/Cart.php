<?php
namespace Src\Models;

use PDOException;

class Cart
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function get($user_id, $product_id, $size_id)
{
    error_log("get() called with user_id: $user_id, product_id: $product_id, size_id: $size_id");

    $queryStr = "SELECT cart.*, product.product_name, product.product_description, product.product_price, product.product_image, productsize.size_label
                 FROM cart 
                 JOIN product ON cart.product_id = product.product_id
                 JOIN productsize ON cart.size_id = productsize.size_id
                 WHERE cart.user_id = :user_id AND cart.product_id = :product_id AND cart.size_id = :size_id";
    $stmt = $this->pdo->prepare($queryStr);

    try {
        $stmt->execute([
            "user_id" => $user_id,
            "product_id" => $product_id,
            "size_id" => $size_id
        ]);

        $result = $stmt->fetch();
        
        if (!$result) {
            error_log("No results found for user_id: $user_id, product_id: $product_id, size_id: $size_id");
        } else {
            error_log("Result found: " . print_r($result, true));
        }

        return $result;
    } catch (PDOException $e) {
        error_log("SQL Error: " . $e->getMessage());
        return null;
    }
}

    
    public function getAll()
    {
        $queryStr = "SELECT cart.*, product.product_name, product.product_description, product.product_price, product.product_image, productsize.size_label
                     FROM cart 
                     JOIN product ON cart.product_id = product.product_id
                     JOIN productsize ON cart.size_id = productsize.size_id";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function create($request)
    {
        $queryStr = "INSERT INTO cart(user_id, product_id, size_id, quantity) VALUES (:user_id, :product_id, :size_id, :quantity)";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute([
                "user_id" => $request["user_id"],
                "product_id" => $request["product_id"],
                "size_id" => $request["size_id"],
                "quantity" => $request["quantity"],
            ]);
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function update($request, $user_id, $product_id, $size_id)
    {
        $queryStr = "UPDATE cart 
                     SET quantity = :quantity
                     WHERE user_id = :user_id AND product_id = :product_id AND size_id = :size_id";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute([
                "quantity" => $request["quantity"],
                "user_id" => $user_id,
                "product_id" => $product_id,
                "size_id" => $size_id,
            ]);
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function delete($user_id, $product_id, $size_id)
    {
        $queryStr = "DELETE FROM cart WHERE user_id = :user_id AND product_id = :product_id AND size_id = :size_id";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute([
                "user_id" => $user_id,
                "product_id" => $product_id,
                "size_id" => $size_id,
            ]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function getCartItemsForUser($userId)
    {
        $queryStr = "SELECT cart.*, product.product_name, product.product_description, product.product_price, product.product_image 
                     FROM cart 
                     JOIN product ON cart.product_id = product.product_id
                     WHERE cart.user_id = :user_id";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute(["user_id" => $userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }
}
