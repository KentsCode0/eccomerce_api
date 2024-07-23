<?php

namespace Src\Routes;

use FastRoute;
use Src\Controllers\ProductController;
use Src\Controllers\UsersController;
use Src\Controllers\OrderController;
use Src\Controllers\OrderItemsController;
use Src\Controllers\TokenController;

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

                if (count($vars) == 0) {
                    $controller->$method();
                } else {
                    $controller->$method($vars);
                }
                break;
        }
    }
}
?>
