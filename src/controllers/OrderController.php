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
        $postData = json_decode(file_get_contents("php://input"));
        $postData = json_decode(json_encode($postData), true);
        $payload = $this->orderService->create($postData);

        if(array_key_exists("code", $payload))
        {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }

    function getOrder($request)
    {
        $orderId = $request["orderId"];
        $payload = $this->orderService->get($orderId);

        if(array_key_exists("code", $payload))
        {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }

    function getAllOrders()
    {
        $payload = $this->orderService->getAll();

        if(array_key_exists("code", $payload))
        {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }

    function deleteOrder($request)
    {
        $orderId = $request["orderId"];
        $payload = $this->orderService->delete($orderId);

        if(array_key_exists("code", $payload))
        {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }

    function updateOrder($request)
    {
        $orderId = $request["orderId"];
        $postData = json_decode(file_get_contents("php://input"));
        $postData = json_decode(json_encode($postData), true);
        $payload = $this->orderService->update($postData, $orderId);

        if(array_key_exists("code", $payload))
        {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }
}
