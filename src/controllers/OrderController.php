<?php
namespace Src\Controllers;

use Src\Services\OrderService;

class OrderController
{
    private $orderService;
    
    function __construct()
    {
        $this->orderService = new OrderService();
    }

    function createOrder()
    {
        $postData = json_decode(file_get_contents("php://input"), true);
        $payload = $this->orderService->createOrder($postData);

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }

    function getOrder($request)
    {
        $order_id = $request["order_id"];
        $payload = $this->orderService->get($order_id);

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }

    function getAllOrders()
    {
        $payload = $this->orderService->getAll();

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }

    function deleteOrder($request)
    {
        $order_id = $request["order_id"];
        $payload = $this->orderService->delete($order_id);

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }

    function updateOrder($request)
{
    $order_id = $request["order_id"];
    $postData = json_decode(file_get_contents("php://input"), true);

    // Ensure required keys are present
    if (!isset($postData["receiver_name"])) {
        $postData["receiver_name"] = null; // Or some default value if necessary
    }
    if (!isset($postData["receiver_address"])) {
        $postData["receiver_address"] = null; // Or some default value if necessary
    }

    $payload = $this->orderService->update($postData, $order_id);

    if (array_key_exists("code", $payload)) {
        http_response_code($payload["code"]);
        unset($payload["code"]);
    }
    echo json_encode($payload);
}


    function completeOrder($request)
    {
        $order_id = $request["order_id"];
        $payload = $this->orderService->completeOrder($order_id);

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }

    
}
