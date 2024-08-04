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
    $stock = isset($request["stock"]) ? $request["stock"] : 0;

    $queryStr = "INSERT INTO product (product_name, product_description, product_image, product_price, stock) VALUES
        (:product_name, :product_description, :product_image, :product_price, :stock)";

    $stmt = $this->pdo->prepare($queryStr);

    try {
        $stmt->execute([
            "product_name" => $product_name,
            "product_description" => $product_description,
            "product_image" => $product_image,
            "product_price" => $product_price,
            "stock" => $stock
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
    $stock = isset($request["stock"]) ? $request["stock"] : 0;

    $queryStr = "UPDATE product 
        SET product_name = :product_name, product_description = :product_description, 
            product_image = :product_image, product_price = :product_price, stock = :stock
        WHERE product_id = :id";

    $stmt = $this->pdo->prepare($queryStr);

    try {
        $stmt->execute([
            "product_name" => $product_name,
            "product_description" => $product_description,
            "product_image" => $product_image,
            "product_price" => $product_price,
            "stock" => $stock,
            "id" => $id
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

    public function getAllCategories()
    {
        $queryStr = "SELECT * FROM ProductCategory";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function addCategoryToProduct($productId, $categoryId)
    {
        $queryStr = "INSERT INTO ProductCategoryMapping (product_id, category_id) VALUES (:product_id, :category_id)";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute([
                "product_id" => $productId,
                "category_id" => $categoryId
            ]);
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function removeCategoryFromProduct($productId, $categoryId)
    {
        $queryStr = "DELETE FROM ProductCategoryMapping WHERE product_id = :product_id AND category_id = :category_id";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute([
                "product_id" => $productId,
                "category_id" => $categoryId
            ]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function getCategoriesForProduct($productId)
    {
        $queryStr = "SELECT c.category_id, c.category_name 
                    FROM ProductCategoryMapping pcm 
                    JOIN ProductCategory c ON pcm.category_id = c.category_id 
                    WHERE pcm.product_id = :product_id";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute(["product_id" => $productId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function getAllSizes()
    {
        $queryStr = "SELECT * FROM ProductSize";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }    

    public function addSizeToProduct($productId, $sizeId)
    {
        $queryStr = "INSERT INTO ProductSizeMapping (product_id, size_id) VALUES (:product_id, :size_id)";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute([
                "product_id" => $productId,
                "size_id" => $sizeId
            ]);
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function removeSizeFromProduct($productId, $sizeId)
    {
        $queryStr = "DELETE FROM ProductSizeMapping WHERE product_id = :product_id AND size_id = :size_id";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute([
                "product_id" => $productId,
                "size_id" => $sizeId
            ]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function getSizesForProduct($productId)
    {
        $queryStr = "SELECT s.size_id, s.size_label 
                    FROM ProductSizeMapping psm 
                    JOIN ProductSize s ON psm.size_id = s.size_id 
                    WHERE psm.product_id = :product_id";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute(["product_id" => $productId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function createCategory($category_name)
    {
        $queryStr = "INSERT INTO ProductCategory (category_name) VALUES (:category_name)";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute(["category_name" => $category_name]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function createSize($size_label)
    {
        $queryStr = "INSERT INTO ProductSize (size_label) VALUES (:size_label)";
        $stmt = $this->pdo->prepare($queryStr);

        try {
            $stmt->execute(["size_label" => $size_label]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }


}
