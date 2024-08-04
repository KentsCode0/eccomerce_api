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
    if (!Checker::isFieldExist($product, ["product_name", "product_description", "product_image", "product_price", "stock"])) {
        return Response::payload(
            400,
            false,
            "product_name, product_description, product_image, product_price, and stock are required"
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
    ) : Response::payload(400, false, "Contact administrator (belenkentharold@gmail.com)");
}

    
    function get($productId)
    {

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
    $product = $this->Product->update($product, $productId);

    if (!$product) {
        return Response::payload(404, false, "update unsuccessful");
    }

    return $product ? Response::payload(
        200,
        true,
        "product updated successfully",
        array("product" => $this->Product->get($productId))
    ) : Response::payload(400, false, "Contact administrator (belenkentharold@gmail.com)");
}

    function delete($productId)
    {

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

        $this->Product->uploadImage($productId, $files);
        return Response::payload(200, true, "Image uploaded successfully", array("user" => $this->Product->get($productId)));
    }

    function getAllSizes()
    {
        $sizes = $this->Product->getAllSizes();

        if (!$sizes) {
            return Response::payload(404, false, "Sizes not found");
        }

        return Response::payload(
            200,
            true,
            "Sizes retrieved successfully",
            array("sizes" => $sizes)
        );
    }

    function addSizeToProduct($productId, $sizeId)
    {

        $result = $this->Product->addSizeToProduct($productId, $sizeId);

        return $result ? Response::payload(
            200,
            true,
            "Size added to product successfully"
        ) : Response::payload(500, false, "Failed to add size to product");
    }

    function removeSizeFromProduct($productId, $sizeId)
    {

        $result = $this->Product->removeSizeFromProduct($productId, $sizeId);

        return $result ? Response::payload(
            200,
            true,
            "Size removed from product successfully"
        ) : Response::payload(500, false, "Failed to remove size from product");
    }

    function getSizesForProduct($productId)
    {

        $sizes = $this->Product->getSizesForProduct($productId);

        if (!$sizes) {
            return Response::payload(404, false, "Sizes not found");
        }

        return Response::payload(
            200,
            true,
            "Sizes retrieved successfully",
            array("sizes" => $sizes)
        );
    }

    function getAllCategories()
    {
        $categories = $this->Product->getAllCategories();

        if (!$categories) {
            return Response::payload(404, false, "Categories not found");
        }

        return Response::payload(
            200,
            true,
            "Categories retrieved successfully",
            array("categories" => $categories)
        );
    }

    function addCategoryToProduct($productId, $categoryId)
    {

        $result = $this->Product->addCategoryToProduct($productId, $categoryId);

        return $result ? Response::payload(
            200,
            true,
            "Category added to product successfully"
        ) : Response::payload(500, false, "Failed to add category to product");
    }

    function removeCategoryFromProduct($productId, $categoryId)
    {

        $result = $this->Product->removeCategoryFromProduct($productId, $categoryId);

        return $result ? Response::payload(
            200,
            true,
            "Category removed from product successfully"
        ) : Response::payload(500, false, "Failed to remove category from product");
    }

    function getCategoriesForProduct($productId)
    {

        $categories = $this->Product->getCategoriesForProduct($productId);

        if (!$categories) {
            return Response::payload(404, false, "Categories not found");
        }

        return Response::payload(
            200,
            true,
            "Categories retrieved successfully",
            array("categories" => $categories)
        );
    }

    public function createCategory($category_name)
    {
        return $this->Product->createCategory($category_name);
    }

    public function createSize($size_label)
    {
        return $this->Product->createSize($size_label);
    }

}
