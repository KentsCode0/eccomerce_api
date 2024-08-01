<?php

namespace Src\Controllers;

use Src\Services\OrderItemService;

class OrderItemsController
{
    private $orderItemsService;
    
    function __construct()
    {
        $this->orderItemsService = new OrderItemService();
    }

    function createOrderItem()
    {
        $postData = json_decode(file_get_contents("php://input"), true);
        $payload = $this->orderItemsService->createOrderItem($postData);

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }

    function getOrderItem($request)
    {
        $order_item_id = $request["order_item_id"];
        $payload = $this->orderItemsService->get($order_item_id);

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }

    function getAllOrderItems($request)
    {
        $order_id = $request["order_id"];
        $payload = $this->orderItemsService->getAll($order_id);

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }

    function deleteOrderItem($request)
    {
        $order_item_id = $request["order_item_id"];
        $payload = $this->orderItemsService->delete($order_item_id);

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }

    function updateOrderItem($request)
    {
        $order_item_id = $request["order_item_id"];
        $postData = json_decode(file_get_contents("php://input"), true);
        $payload = $this->orderItemsService->update($postData, $order_item_id);

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }
}
