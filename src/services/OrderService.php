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
    private $tokenService;
    private $order;
    private $filter;

    function __construct()
    {
        $this->pdo = (new DatabaseConnector())->getConnection();
        $this->order = new Orders($this->pdo);
        $this->tokenService = new TokenService();
        $this->filter = new Filter("order_id", "user_id");
    }

    public function create($orderRequest)
    {
        $token = $this->tokenService->readEncodedToken();
    
        if (!$token) {
            error_log("Unauthorized access: no token");
            return Response::payload(404, false, "unauthorized access");
        }
    
        // Log the incoming request
        error_log("Order creation request: " . print_r($orderRequest, true));
    
        if (!Checker::isFieldExist($orderRequest, ["user_id", "items"])) {
            error_log("Missing fields in request: " . print_r($orderRequest, true));
            return Response::payload(400, false, "user_id and items are required");
        }
    
        // Calculate total amount from items
        $totalAmount = 0;
        foreach ($orderRequest['items'] as $item) {
            if (!isset($item['price']) || !isset($item['quantity']) || !isset($item['product_id'])) {
                error_log("Missing fields in item: " . print_r($item, true));
                return Response::payload(400, false, "Each item must have product_id, quantity, and price");
            }
            $totalAmount += $item['price'] * $item['quantity'];
        }
    
        // Log calculated total amount
        error_log("Calculated total amount: $totalAmount");
    
        // Create order
        $orders = new Orders($this->pdo);
        $orderId = $orders->create([
            'user_id' => $orderRequest['user_id'],
            'total_amount' => $totalAmount
        ]);
    
        if ($orderId === false) {
            error_log("Order creation failed with user_id: " . $orderRequest['user_id']);
            return Response::payload(500, false, "Order creation failed");
        }
    
        // Create order items
        $orderItemsModel = new OrderItems($this->pdo);
        foreach ($orderRequest['items'] as $item) {
            $item['order_id'] = $orderId;
            $orderItemId = $orderItemsModel->create($item);
            if ($orderItemId === false) {
                // Rollback order if any item creation fails
                $orders->delete($orderId);
                error_log("Order item creation failed for item: " . print_r($item, true) . ", order rolled back");
                return Response::payload(500, false, "Order item creation failed, order rolled back");
            }
        }
    
        return Response::payload(201, true, "Order created successfully", ["order_id" => $orderId]);
    }
    

    function get($orderId)
    {
        $token = $this->tokenService->readEncodedToken();

        if (!$token) {
            return Response::payload(404, false, "unauthorized access");
        }

        $order = $this->order->get($orderId);

        if (!$order) {
            return Response::payload(404, false, "Order not found");
        }

        return $order ? Response::payload(
            200,
            true,
            "Order found",
            array("order" => $order)
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

        $orders = $this->order->getAll($filterStr);

        if (!$orders) {
            return Response::payload(404, false, "Orders not found");
        }

        return $orders ? Response::payload(
            200,
            true,
            "Orders found",
            array("orders" => $orders)
        ) : Response::payload(400, false, "Contact administrator (your-email@example.com)");
    }

    function update($order, $orderId)
    {
        $token = $this->tokenService->readEncodedToken();

        if (!$token) {
            return Response::payload(404, false, "unauthorized access");
        }

        $orderUpdated = $this->order->update($order, $orderId);

        if (!$orderUpdated) {
            return Response::payload(404, false, "Update unsuccessful");
        }

        return $orderUpdated ? Response::payload(
            200,
            true,
            "Order updated successfully",
            array("order" => $this->order->get($orderId))
        ) : Response::payload(400, false, "Contact administrator (your-email@example.com)");
    }

    function delete($orderId)
    {
        $token = $this->tokenService->readEncodedToken();

        if (!$token) {
            return Response::payload(404, false, "unauthorized access");
        }

        $orderDeleted = $this->order->delete($orderId);

        if (!$orderDeleted) {
            return Response::payload(404, false, "Deletion unsuccessful");
        }

        return $orderDeleted ? Response::payload(
            200,
            true,
            "Order deleted successfully"
        ) : Response::payload(400, false, "Contact administrator (your-email@example.com)");
    }
}
