<?php

namespace Src\Controllers;

use Src\Services\OrderItemService;

class OrderItemsController
{
    private $orderItemService;
    
    function __construct()
    {
        $this->orderItemService = new OrderItemService();
    }

    function createOrderItem()
    {
        $postData = json_decode(file_get_contents("php://input"));
        $postData = json_decode(json_encode($postData), true);
        $payload = $this->orderItemService->create($postData);

        if(array_key_exists("code", $payload))
        {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }

    function getOrderItem($request)
    {
        $orderItemId = $request["orderItemId"];
        $payload = $this->orderItemService->get($orderItemId);

        if(array_key_exists("code", $payload))
        {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }

    function getAllOrderItems()
    {
        $payload = $this->orderItemService->getAll();

        if(array_key_exists("code", $payload))
        {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }

    function deleteOrderItem($request)
    {
        $orderItemId = $request["orderItemId"];
        $payload = $this->orderItemService->delete($orderItemId);

        if(array_key_exists("code", $payload))
        {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }

    function updateOrderItem($request)
    {
        $orderItemId = $request["orderItemId"];
        $postData = json_decode(file_get_contents("php://input"));
        $postData = json_decode(json_encode($postData), true);
        $payload = $this->orderItemService->update($postData, $orderItemId);

        if(array_key_exists("code", $payload))
        {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }
}
