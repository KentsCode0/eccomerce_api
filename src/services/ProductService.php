<?php

namespace Src\Services;

use Src\Models\Products;
use Src\Config\DatabaseConnector;
use Src\Utils\Checker;
use Src\Utils\Response;
use Src\Utils\Filter;

class ProductService
{
    private $pdo;
    private $tokenService;
    private $Product;
    private $filter;
    function __construct()
    {
        $this->pdo = (new DatabaseConnector())->getConnection();
        $this->Product = new Products($this->pdo);
        $this->tokenService = new TokenService();
        $this->filter = new Filter("title", "workspace_id");
    }

    function create($product)
    {
        $token = $this->tokenService->readEncodedToken();

        if (!$token) {
            return Response::payload(404, false, "unauthorized access");
        }

        if (!Checker::isFieldExist($product, ["product_name", "product_description", "product_image", "product_price" ])) {
            return Response::payload(
                400,
                false,
                "product_name, and product_description , product_image, product_price is required"
            );
        }

        $productId = $this->Product->create($product);

        if ($productId === false) {
            return Response::payload(500, false, array("message" => "Contact administrator (belenkentharold@gmail.com)"));
        }

        return $productId ? Response::payload(
            201,
            true,
            "product created successfully",
            array("product" => $this->Product->get($productId))
        ) : Response::payload(400, False, message: "Contact administrator (belenkentharold@gmail.com)",);
    }
    
    function get($productId)
    {
        $token = $this->tokenService->readEncodedToken();

        if (!$token) {
            return Response::payload(404, false, "unauthorized access");
        }

        $product = $this->Product->get($productId);

        if (!$product) {
            return Response::payload(404, false, "product not found");
        }
        return $product ? Response::payload(
            200,
            true,
            "product found",
            array("product" => $product)
        ) : Response::payload(400, False, message: "Contact administrator (belenkentharold@gmail.com)",);
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

        $products = $this->Product->getAll($filterStr);

        if (!$products) {
            return Response::payload(404, false, "products not found");
        }
        return $products ? Response::payload(
            200,
            true,
            "products found",
            array("product" => $products)
        ) : Response::payload(400, False, message: "Contact administrator (belenkentharold@gmail.com)",);
    }

    function update($product, $productId)
    {
        $token = $this->tokenService->readEncodedToken();

        if (!$token) {
            return Response::payload(404, false, "unauthorized access");
        }

        $product = $this->Product->update($product, $productId);

        if (!$product) {
            return Response::payload(404, false, "update unsuccessful");
        }

        return $product ? Response::payload(
            200,
            true,
            "product updated successfully",
            array("product" => $this->Product->get($productId))
        ) : Response::payload(400, False, message: "Contact administrator (belenkentharold@gmail.com)",);
    }
    function delete($productId)
    {
        $token = $this->tokenService->readEncodedToken();

        if (!$token) {
            return Response::payload(404, false, "unauthorized access");
        }

        $product = $this->Product->delete($productId);

        if (!$product) {
            return Response::payload(404, false, "deletion unsuccessful");
        }

        return $product ? Response::payload(
            200,
            true,
            "product deleted successfully",
        ) : Response::payload(400, False, message: "Contact administrator (belenkentharold@gmail.com)",);
    }

    function uploadImage($productId, $files)
    {
        $token = $this->tokenService->readEncodedToken();

        if (!$token) {
            return Response::payload(404, false, "unauthorized access");
        }

        $this->Product->uploadImage($productId, $files);
        return Response::payload(200, true, "Image uploaded successfully", array("user" => $this->Product->get($productId)));
    }

}
