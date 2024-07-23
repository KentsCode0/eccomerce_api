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

        if(array_key_exists("code", $payload))
        {
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

        if(array_key_exists("code", $payload))
        {
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

}
