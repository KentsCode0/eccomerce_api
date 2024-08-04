<?php


namespace Src\Controllers;

use Src\Services\ProductService;

class ProductController
{
    private $productService;
    function __construct()
    {
        $this->productService = new ProductService();
    }

    function createProduct()
{
    $postData = json_decode(file_get_contents("php://input"));
    $postData = json_decode(json_encode($postData), true);
    $payload = $this->productService->create($postData);

    if (array_key_exists("code", $payload)) {
        http_response_code($payload["code"]);
        unset($payload["code"]);
    }
    echo json_encode($payload);
}


    function getProduct($request)
    {
        $productId = $request["productId"];
        $payload = $this->productService->get($productId);

        if(array_key_exists("code", $payload))
        {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }

    function getAllProduct()
    {
        $payload = $this->productService->getAll();

        if(array_key_exists("code", $payload))
        {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }

    function deleteProduct($request)
    {
        $productId = $request["productId"];
        $payload = $this->productService->delete($productId);

        if(array_key_exists("code", $payload))
        {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }

    function updateProduct($request)
{
    $productId = $request["productId"];
    $postData = json_decode(file_get_contents("php://input"));
    $postData = json_decode(json_encode($postData), true);
    $payload = $this->productService->update($postData, $productId);

    if (array_key_exists("code", $payload)) {
        http_response_code($payload["code"]);
        unset($payload["code"]);
    }
    echo json_encode($payload);
}


    function uploadImage($request){
        $payload = $this->productService->uploadImage($request["productId"], $_FILES);


        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }

        echo json_encode($payload);
    }

    function getAllSizes()
    {
        $payload = $this->productService->getAllSizes();

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }

        echo json_encode($payload);
    }

    function addSizeToProduct($request)
    {
        $productId = $request["productId"];
        $sizeId = $request["sizeId"];
        $payload = $this->productService->addSizeToProduct($productId, $sizeId);

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }

        echo json_encode($payload);
    }

    function removeSizeFromProduct($request)
    {
        $productId = $request["productId"];
        $sizeId = $request["sizeId"];
        $payload = $this->productService->removeSizeFromProduct($productId, $sizeId);

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }

        echo json_encode($payload);
    }

    function getSizesForProduct($request)
    {
        $productId = $request["productId"];
        $payload = $this->productService->getSizesForProduct($productId);

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }

        echo json_encode($payload);
    }

    function getAllCategories()
    {
        $payload = $this->productService->getAllCategories();

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }

        echo json_encode($payload);
    }

    function addCategoryToProduct($request)
    {
        $productId = $request["productId"];
        $categoryId = $request["categoryId"];
        $payload = $this->productService->addCategoryToProduct($productId, $categoryId);

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }

        echo json_encode($payload);
    }

    function removeCategoryFromProduct($request)
    {
        $productId = $request["productId"];
        $categoryId = $request["categoryId"];
        $payload = $this->productService->removeCategoryFromProduct($productId, $categoryId);

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }

        echo json_encode($payload);
    }

    function getCategoriesForProduct($request)
    {
        $productId = $request["productId"];
        $payload = $this->productService->getCategoriesForProduct($productId);

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }

        echo json_encode($payload);
    }
    
    public function createCategory($request)
    {
        $category_name = $request['category_name'];
        $result = $this->productService->createCategory($category_name);

        if ($result) {
            echo json_encode(['status' => 'success', 'data' => ['category_id' => $result]]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create category']);
        }
    }

    public function createSize($request)
    {
        $size_label = $request['size_label'];
        $result = $this->productService->createSize($size_label);

        if ($result) {
            echo json_encode(['status' => 'success', 'data' => ['size_id' => $result]]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create size']);
        }
    }

}
