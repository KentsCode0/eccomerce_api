<?php
namespace Src\Services;

use Src\Models\OrderItems;
use Src\Config\DatabaseConnector;
use Src\Utils\Checker;
use Src\Utils\Response;
use Src\Utils\Filter;

class OrderItemService
{
    private $pdo;
    private $tokenService;
    private $orderItem;
    private $filter;

    function __construct()
    {
        $this->pdo = (new DatabaseConnector())->getConnection();
        $this->orderItem = new OrderItems($this->pdo);
        $this->tokenService = new TokenService();
        $this->filter = new Filter("order_item_id", "order_id", "product_id");
    }

    function create($orderItem)
    {
        $token = $this->tokenService->readEncodedToken();

        if (!$token) {
            return Response::payload(404, false, "unauthorized access");
        }

        if (!Checker::isFieldExist($orderItem, ["order_id", "product_id", "quantity", "price"])) {
            return Response::payload(400, false, "order_id, product_id, quantity, and price are required");
        }

        $orderItemId = $this->orderItem->create($orderItem);

        if ($orderItemId === false) {
            return Response::payload(500, false, array("message" => "Contact administrator (your-email@example.com)"));
        }

        return $orderItemId ? Response::payload(
            201,
            true,
            "Order item created successfully",
            array("order_item" => $this->orderItem->get($orderItemId))
        ) : Response::payload(400, false, "Contact administrator (your-email@example.com)");
    }

    function get($orderItemId)
    {
        $token = $this->tokenService->readEncodedToken();

        if (!$token) {
            return Response::payload(404, false, "unauthorized access");
        }

        $orderItem = $this->orderItem->get($orderItemId);

        if (!$orderItem) {
            return Response::payload(404, false, "Order item not found");
        }

        return $orderItem ? Response::payload(
            200,
            true,
            "Order item found",
            array("order_item" => $orderItem)
        ) : Response::payload(400, false, "Contact administrator (your-email@example.com)");
    }

    function getAll()
    {
        $token = $this->tokenService->readEncodedToken();

        if (!$token) {
            return Response::payload(404, false, "unauthorized access");
        }

        $filterStr = $this->filter->getFilterStr();

        if (str_contains($filterStr, "unavailable") || str_contains($filterStr, "empty")) {
            return Response::payload(400, false, $filterStr);
        }

        $orderItems = $this->orderItem->getAll($filterStr);

        if (!$orderItems) {
            return Response::payload(404, false, "Order items not found");
        }

        return $orderItems ? Response::payload(
            200,
            true,
            "Order items found",
            array("order_items" => $orderItems)
        ) : Response::payload(400, false, "Contact administrator (your-email@example.com)");
    }

    function update($orderItem, $orderItemId)
    {
        $token = $this->tokenService->readEncodedToken();

        if (!$token) {
            return Response::payload(404, false, "unauthorized access");
        }

        $orderItemUpdated = $this->orderItem->update($orderItem, $orderItemId);

        if (!$orderItemUpdated) {
            return Response::payload(404, false, "Update unsuccessful");
        }

        return $orderItemUpdated ? Response::payload(
            200,
            true,
            "Order item updated successfully",
            array("order_item" => $this->orderItem->get($orderItemId))
        ) : Response::payload(400, false, "Contact administrator (your-email@example.com)");
    }

    function delete($orderItemId)
    {
        $token = $this->tokenService->readEncodedToken();

        if (!$token) {
            return Response::payload(404, false, "unauthorized access");
        }

        $orderItemDeleted = $this->orderItem->delete($orderItemId);

        if (!$orderItemDeleted) {
            return Response::payload(404, false, "Deletion unsuccessful");
        }

        return $orderItemDeleted ? Response::payload(
            200,
            true,
            "Order item deleted successfully"
        ) : Response::payload(400, false, "Contact administrator (your-email@example.com)");
    }
}
