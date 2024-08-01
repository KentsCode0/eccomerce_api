<?php
namespace Src\Services;

use Src\Models\Orders;
use Src\Models\OrderItems;
use Src\Config\DatabaseConnector;
use Src\Utils\Checker;
use Src\Utils\Response;
use Src\Utils\Filter;

class OrderService
{
    private $pdo;
    private $order;
    private $orderItems;
    private $tokenService;
    private $filter;

    function __construct()
    {
        $this->pdo = (new DatabaseConnector())->getConnection();
        $this->order = new Orders($this->pdo);
        $this->orderItems = new OrderItems($this->pdo);
        $this->tokenService = new TokenService();
        $this->filter = new Filter("order_id", "user_id");
    }

    function createOrder($order)
    {
        if (!Checker::isFieldExist($order, ["user_id", "total_amount"])) {
            return Response::payload(400, false, "user_id and total_amount are required");
        }

        $orderId = $this->order->create($order);

        if (!$orderId) {
            return Response::payload(500, false, "Contact administrator (belenkentharold@gmail.com)");
        }

        if (isset($order["items"])) {
            foreach ($order["items"] as $item) {
                $item["order_id"] = $orderId;
                $this->orderItems->create($item);
            }
        }

        return Response::payload(
            201,
            true,
            "order created successfully",
            array("order_id" => $orderId)
        );
    }

    function get($orderId)
    {
       

        $order = $this->order->get($orderId);

        if (!$order) {
            return Response::payload(404, false, "Order not found");
        }

        return Response::payload(
            200,
            true,
            "Order found",
            array("order" => $order)
        );
    }

    function getAll()
    {
       

        $filterStr = $this->filter->getFilterStr();

        if (str_contains($filterStr, "unavailable") || str_contains($filterStr, "empty")) {
            return Response::payload(400, false, $filterStr);
        }

        $orders = $this->order->getAll($filterStr);

        if (!$orders) {
            return Response::payload(404, false, "Orders not found");
        }

        return Response::payload(
            200,
            true,
            "Orders found",
            array("orders" => $orders)
        );
    }

    function update($order, $orderId)
    {
       

        $orderUpdated = $this->order->update($order, $orderId);

        if (!$orderUpdated) {
            return Response::payload(404, false, "Update unsuccessful");
        }

        return Response::payload(
            200,
            true,
            "Order updated successfully",
            array("order" => $this->order->get($orderId))
        );
    }

    function delete($orderId)
    {
       

        $orderDeleted = $this->order->delete($orderId);

        if (!$orderDeleted) {
            return Response::payload(404, false, "Deletion unsuccessful");
        }

        return Response::payload(
            200,
            true,
            "Order deleted successfully"
        );
    }

    function completeOrder($orderId)
    {
        $orderCompleted = $this->order->completeOrder($orderId);

        if (!$orderCompleted) {
            return Response::payload(500, false, "Failed to mark order as completed");
        }

        return Response::payload(
            200,
            true,
            "Order marked as completed",
            array("order_id" => $orderId)
        );
    }
}
