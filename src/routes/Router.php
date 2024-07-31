<?php

namespace Src\Routes;

use FastRoute;
use Src\Controllers\ProductController;
use Src\Controllers\UsersController;
use Src\Controllers\OrderController;
use Src\Controllers\OrderItemsController;
use Src\Controllers\TokenController;
use Src\Controllers\CartController;

class Router
{
    private $dispatcher;

    public function __construct()
    {
        $this->dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {

            /* users */
            $r->addRoute('POST', '/users', [UsersController::class, 'postUser']);
            $r->addRoute('GET', '/users', [UsersController::class, 'getAllUser']);
            $r->addRoute('GET', '/users/{userId:\d+}', [UsersController::class, 'getUser']);
            $r->addRoute('PATCH', '/users/{userId:\d+}', [UsersController::class, 'updateUser']);
            $r->addRoute('DELETE', '/users/{userId:\d+}', [UsersController::class, 'deleteUser']);

            /* tokens */
            $r->addRoute('POST', '/token', [TokenController::class, 'postToken']);

            /* products */
            $r->addRoute('POST', '/products', [ProductController::class, 'createProduct']);
            $r->addRoute('GET', '/products', [ProductController::class, 'getAllProduct']);
            $r->addRoute('GET', '/products/{productId:\d+}', [ProductController::class, 'getProduct']);
            $r->addRoute('PUT', '/products/{productId:\d+}', [ProductController::class, 'updateProduct']);
            $r->addRoute('DELETE', '/products/{productId:\d+}', [ProductController::class, 'deleteProduct']);
            $r->addRoute('POST', '/products/{productId:\d+}/productimage', [ProductController::class, 'uploadImage']);

            // Sizes
            $r->addRoute('POST', '/sizes', [ProductController::class, 'createSize']);
            $r->addRoute('POST', '/products/{productId:\d+}/sizes/{sizeId:\d+}', [ProductController::class, 'addSizeToProduct']);
            $r->addRoute('DELETE', '/products/{productId:\d+}/sizes/{sizeId:\d+}', [ProductController::class, 'removeSizeFromProduct']);
            $r->addRoute('GET', '/products/{productId:\d+}/sizes', [ProductController::class, 'getSizesForProduct']);
            $r->addRoute('GET', '/sizes', [ProductController::class, 'getAllSizes']);

            // Categories
            $r->addRoute('POST', '/categories', [ProductController::class, 'createCategory']);
            $r->addRoute('POST', '/products/{productId:\d+}/categories/{categoryId:\d+}', [ProductController::class, 'addCategoryToProduct']);
            $r->addRoute('DELETE', '/products/{productId:\d+}/categories/{categoryId:\d+}', [ProductController::class, 'removeCategoryFromProduct']);
            $r->addRoute('GET', '/products/{productId:\d+}/categories', [ProductController::class, 'getCategoriesForProduct']);
            $r->addRoute('GET', '/categories', [ProductController::class, 'getAllCategories']);

            /* orders */
            $r->addRoute('POST', '/orders', [OrderController::class, 'createOrder']);
            $r->addRoute('GET', '/orders', [OrderController::class, 'getAllOrders']);
            $r->addRoute('GET', '/orders/{orderId:\d+}', [OrderController::class, 'getOrder']);
            $r->addRoute('PUT', '/orders/{orderId:\d+}', [OrderController::class, 'updateOrder']);
            $r->addRoute('DELETE', '/orders/{orderId:\d+}', [OrderController::class, 'deleteOrder']);

            /* order items */
            $r->addRoute('POST', '/orderitems', [OrderItemsController::class, 'createOrderItem']);
            $r->addRoute('GET', '/orderitems', [OrderItemsController::class, 'getAllOrderItems']);
            $r->addRoute('GET', '/orderitems/{orderItemId:\d+}', [OrderItemsController::class, 'getOrderItem']);
            $r->addRoute('PUT', '/orderitems/{orderItemId:\d+}', [OrderItemsController::class, 'updateOrderItem']);
            $r->addRoute('DELETE', '/orderitems/{orderItemId:\d+}', [OrderItemsController::class, 'deleteOrderItem']);

          /* cart */
            $r->addRoute('POST', '/cart', [CartController::class, 'createCartItem']); // Add item to the cart
            $r->addRoute('GET', '/cart/{user_id:\d+}/{product_id:\d+}/{size_id:\d+}', [CartController::class, 'getCartItem']); // Get specific cart item
            $r->addRoute('GET', '/cart', [CartController::class, 'getAllCartItems']); // Get all cart items
            $r->addRoute('PUT', '/cart/{user_id:\d+}/{product_id:\d+}/{size_id:\d+}', [CartController::class, 'updateCartItem']); // Update specific item in the cart
            $r->addRoute('DELETE', '/cart/{user_id:\d+}/{product_id:\d+}/{size_id:\d+}', [CartController::class, 'deleteCartItem']); // Remove specific item from the cart
            $r->addRoute('GET', '/cart/{userId:\d+}', [CartController::class, 'getCartItemsForUser']); // Get all items for a specific user

        });
    }

    public function handle($method, $uri)
    {
        $routeInfo = $this->dispatcher->dispatch($method, $uri);
        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                http_response_code(404);
                echo json_encode(array("error" => "Not found"));
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                http_response_code(405);
                echo json_encode(array("error" => "Method not allowed"));
                break;
            case FastRoute\Dispatcher::FOUND:
                $controllerName = $routeInfo[1][0];
                $method = $routeInfo[1][1];
                $vars = $routeInfo[2];

                $controller = new $controllerName();

                if ($method === 'createCategory' || $method === 'createSize' || $method === 'addItem' || $method === 'updateItem') {
                    // For POST requests that need data from the request body
                    $data = json_decode(file_get_contents('php://input'), true);
                    $controller->$method($data);
                } else {
                    // For routes with URL parameters
                    if (count($vars) == 0) {
                        $controller->$method();
                    } else {
                        $controller->$method($vars);
                    }
                }
                break;
        }
    }
}
