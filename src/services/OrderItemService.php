<?php
namespace Src\Services;

use Src\Models\OrderItems;
use Src\Config\DatabaseConnector;
use Src\Utils\Checker;
use Src\Utils\Response;

class OrderItemService
{
    private $pdo;
    private $orderItems;

    function __construct()
    {
        $this->pdo = (new DatabaseConnector())->getConnection();
        $this->orderItems = new OrderItems($this->pdo);
    }

    function createOrderItem($orderItem)
    {
        if (!Checker::isFieldExist($orderItem, ["order_id", "product_id", "size_id", "quantity", "price"])) {
            return Response::payload(400, false, "order_id, product_id, size_id, quantity, and price are required");
        }

        $orderItemId = $this->orderItems->create($orderItem);

        if (!$orderItemId) {
            return Response::payload(500, false, "Contact administrator (belenkentharold@gmail.com)");
        }

        return Response::payload(
            201,
            true,
            "Order item created successfully",
            ["order_item_id" => $orderItemId]
        );
    }

    function get($orderItemId)
    {
        $orderItem = $this->orderItems->get($orderItemId);

        if (!$orderItem) {
            return Response::payload(404, false, "Order item not found");
        }

        return Response::payload(
            200,
            true,
            "Order item found",
            ["order_item" => $orderItem]
        );
    }

    function getAll($orderId)
    {
        $orderItems = $this->orderItems->getAll($orderId);

        if (!$orderItems) {
            return Response::payload(404, false, "Order items not found");
        }

        return Response::payload(
            200,
            true,
            "Order items found",
            ["order_items" => $orderItems]
        );
    }

    function update($orderItem, $orderItemId)
    {
        $orderItemUpdated = $this->orderItems->update($orderItem, $orderItemId);

        if (!$orderItemUpdated) {
            return Response::payload(404, false, "Update unsuccessful");
        }

        return Response::payload(
            200,
            true,
            "Order item updated successfully",
            ["order_item" => $this->orderItems->get($orderItemId)]
        );
    }

    function delete($orderItemId)
    {
        $orderItemDeleted = $this->orderItems->delete($orderItemId);

        if (!$orderItemDeleted) {
            return Response::payload(404, false, "Deletion unsuccessful");
        }

        return Response::payload(
            200,
            true,
            "Order item deleted successfully"
        );
    }
}
