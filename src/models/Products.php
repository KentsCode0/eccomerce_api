<?php

namespace Src\Models;

use PDOException;
use Exception;

class Products
{
    private $pdo;
    private static $hostname = "http://localhost";
    private static $base_directory = "../uploads/products/";
    private static $path = "http://localhost/ecommerce-api/public/";


    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function get($id)
    {
        $queryStr = "SELECT * FROM product WHERE product_id = :id";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute(["id" => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function getAll($filter = "")
    {
        $queryStr = $filter ? "SELECT * FROM product WHERE $filter" : "SELECT * FROM product";
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
        $product_name = $request["product_name"];
        $product_description = $request["product_description"];
        $product_image = ""; // Default empty image
        $product_price = $request["product_price"];

        $queryStr = "INSERT INTO product(product_name, product_description, product_image, product_price) VALUES
            (:product_name, :product_description, :product_image, :product_price)";

        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute([
                "product_name" => $product_name,
                "product_description" => $product_description,
                "product_image" => $product_image,
                "product_price" => $product_price,
            ]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function update($request, $id)
    {
        $product_name = $request["product_name"];
        $product_description = $request["product_description"];
        $product_price = $request["product_price"];
        $product_image = isset($request["product_image"]) ? $request["product_image"] : '';

        $queryStr = "UPDATE product 
            SET product_name = :product_name, product_description = :product_description, 
                product_image = :product_image, product_price = :product_price 
            WHERE product_id = :id";

        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute([
                "product_name" => $product_name,
                "product_description" => $product_description,
                "product_image" => $product_image,
                "product_price" => $product_price,
                "id" => $id,
            ]);
            return $id;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function delete($id)
    {
        $queryStr = "DELETE FROM product WHERE product_id = :id";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute(["id" => $id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    function uploadImage($id, $files)
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

    $queryStr = "UPDATE product SET product_image=:product_image WHERE product_id = :id";

    try {
        $stmt = $this->pdo->prepare($queryStr);
        $stmt->execute([
            "product_image" => self::$path . $target_file,
            "id" => $id
        ]);
        return $id;
    } catch (PDOException $e) {
        error_log('Database Error: ' . $e->getMessage());
        return false;
    }
}

}