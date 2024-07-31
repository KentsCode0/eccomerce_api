<?php
namespace Src\Controllers;

use Src\Services\CartService;

class CartController
{
    private $cartService;

    function __construct()
    {
        $this->cartService = new CartService();
    }

    function createCartItem()
    {
        $postData = json_decode(file_get_contents("php://input"), true);
        $payload = $this->cartService->create($postData);

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }

    function getCartItem($request)
    {
        $userId = $request["userId"];
        $productId = $request["product_id"];
        $sizeId = $request["size_id"];
        $payload = $this->cartService->get($userId, $productId, $sizeId);

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }

    function getAllCartItems()
    {
        $payload = $this->cartService->getAll();

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }

    function updateCartItem($request)
    {
        $userId = $request["user_id"];
        $productId = $request["product_id"];
        $sizeId = $request["size_id"];
        $postData = json_decode(file_get_contents("php://input"), true);
        $payload = $this->cartService->update($postData, $userId, $productId, $sizeId);

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }

    function deleteCartItem($request)
    {
        $userId = $request["user_id"];
        $productId = $request["product_id"];
        $sizeId = $request["size_id"];
        $payload = $this->cartService->delete($userId, $productId, $sizeId);

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }

    function getCartItemsForUser($request)
    {
        $userId = $request["userId"];
        $payload = $this->cartService->getCartItemsForUser($userId);

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }
}
