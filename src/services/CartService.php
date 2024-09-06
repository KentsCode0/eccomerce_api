<?php
namespace Src\Services;

use Src\Models\Cart;
use Src\Config\DatabaseConnector;
use Src\Utils\Checker;
use Src\Utils\Response;

class CartService
{
    private $pdo;
    private $Cart;

    function __construct()
    {
        $this->pdo = (new DatabaseConnector())->getConnection();
        $this->Cart = new Cart($this->pdo);
    }

    function create($cart)
    {
        if (!Checker::isFieldExist($cart, ["user_id", "product_id", "size_id", "quantity"])) {
            return Response::payload(
                400,
                false,
                "user_id, product_id, size_id, and quantity are required"
            );
        }
    
        // Check if the cart item already exists
        $existingCartItem = $this->Cart->get($cart["user_id"], $cart["product_id"], $cart["size_id"]);
    
        if ($existingCartItem) {
            // If the item exists, update its quantity
            $newQuantity = $existingCartItem["quantity"] + $cart["quantity"];
            $updateData = [
                "quantity" => $newQuantity
            ];
    
            $result = $this->Cart->update($updateData, $cart["user_id"], $cart["product_id"], $cart["size_id"]);
    
            if (!$result) {
                return Response::payload(500, false, "Contact administrator (belenkentharold@gmail.com)");
            }
    
            return Response::payload(
                200,
                true,
                "cart item updated successfully",
                array("cart" => $this->Cart->get($cart["user_id"], $cart["product_id"], $cart["size_id"]))
            );
        } else {
            // If the item does not exist, create a new entry
            $result = $this->Cart->create($cart);
    
            if (!$result) {
                return Response::payload(500, false, "Contact administrator (belenkentharold@gmail.com)");
            }
    
            return Response::payload(
                201,
                true,
                "cart item created successfully",
                array("cart" => $this->Cart->get($cart["user_id"], $cart["product_id"], $cart["size_id"]))
            );
        }
    }
    

    function get($user_id, $product_id, $size_id)
    {
        $cart = $this->Cart->get($user_id, $product_id, $size_id);

        if (!$cart) {
            return Response::payload(404, false, "cart item not found");
        }
        return Response::payload(
            200,
            true,
            "cart item found",
            array("cart" => $cart)
        );
    }

    function getAll()
    {
        $carts = $this->Cart->getAll();

        if (!$carts) {
            return Response::payload(404, false, "cart items not found");
        }
        return Response::payload(
            200,
            true,
            "cart items found",
            array("carts" => $carts)
        );
    }

    function update($cart, $user_id, $product_id, $size_id)
    {
        $result = $this->Cart->update($cart, $user_id, $product_id, $size_id);

        if (!$result) {
            return Response::payload(404, false, "update unsuccessful");
        }

        return Response::payload(
            200,
            true,
            "cart item updated successfully",
            array("cart" => $this->Cart->get($user_id, $product_id, $size_id))
        );
    }

    function delete($user_id, $product_id, $size_id)
    {
        $result = $this->Cart->delete($user_id, $product_id, $size_id);

        if (!$result) {
            return Response::payload(404, false, "deletion unsuccessful");
        }

        return Response::payload(
            200,
            true,
            "cart item deleted successfully"
        );
    }

    function getCartItemsForUser($userId)
    {
        $carts = $this->Cart->getCartItemsForUser($userId);

        if (!$carts) {
            return Response::payload(404, false, "cart items not found");
        }

        return Response::payload(
            200,
            true,
            "cart items retrieved successfully",
            array("carts" => $carts)
        );
    }
}
