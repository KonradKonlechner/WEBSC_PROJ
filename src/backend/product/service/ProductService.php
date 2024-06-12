<?php

namespace product;

require_once "../model/Product.php";
require_once "../persistence/ProductManagementSystem.php";
require_once "../../user/model/User.php";

class ProductService
{
    private ProductManagementSystem $pms;

    public function __construct()
    {
        $this->pms = new ProductManagementSystem();
    }

    public function getAllProducts()
    {
        $allProducts = $this->pms->getAllProducts();

        return  $allProducts;
    }

    public function getAllProductsOfCategory($productCategory): array
    {

        $productCategory = htmlspecialchars($productCategory);

        $productsOfCategory = $this->pms->getAllProductsOfCategory($productCategory);

        return  $productsOfCategory;
    }

    public function getAllProductsFilteredBySearchTermAndCategory($searchParameter)
    {
        $searchTerm = htmlspecialchars($searchParameter["searchTerm"]);
        $productCategory = htmlspecialchars($searchParameter["productCategory"]);

        if($productCategory === "all") {
            $filteredProducts = $this->pms->getAllProductsFilteredBySearchTerm($searchTerm);
        } else {
            $filteredProducts = $this->pms->getAllProductsFilteredBySearchTermAndCategory($searchTerm, $productCategory);
        }

        return  $filteredProducts;
    }

    public function updateProduct($param)
    {
        if (isset($_SESSION["currentUser"]) && $_SESSION["currentUser"]->isAdmin() == 1) {
            $enteredId = $this->prepareInput($param["id"]);
            $enteredName = $this->prepareInput($param["name"]);
            $enteredDescription = $this->prepareInput($param["description"]);
            $enteredCategory = $this->prepareInput($param["category"]);
            $enteredPrice = $this->prepareInput($param["price"]);
            $enteredImagePath = $this->prepareInput($param["imagePath"]);
            $enteredThumbnailPath = $this->prepareInput($param["thumbnailPath"]);

            $productToUpdate = new Product(
                $enteredId,
                $enteredName,
                $enteredDescription,
                $enteredCategory,
                $enteredPrice,
                $enteredImagePath,
                $enteredThumbnailPath
            );

            $updatedProduct = $this->pms->updateProduct($productToUpdate);
            return $updatedProduct;
        } else {
            throw new \Exception('You are not an admin and therefore have no rights to use this API! Be gone!!!');
        }
    }

    private function prepareInput($data)
    {
        // Hier findet die Aufbereitung des User-inputs für sämtliche Eingabemasken statt
        $sanitizedInput = htmlspecialchars($data);
        $sanitizedInput = trim($sanitizedInput);
        return stripslashes($sanitizedInput);
    }

    public function createProduct($param)
    {
        if (isset($_SESSION["currentUser"]) && $_SESSION["currentUser"]->isAdmin() == 1) {
            $enteredName = $this->prepareInput($param["name"]);
            $enteredDescription = $this->prepareInput($param["description"]);
            $enteredCategory = $this->prepareInput($param["category"]);
            $enteredPrice = $this->prepareInput($param["price"]);
            $enteredImagePath = $this->prepareInput($param["imagePath"]);
            $enteredThumbnailPath = $this->prepareInput($param["thumbnailPath"]);

            $productToCreate = new Product(
                0,
                $enteredName,
                $enteredDescription,
                $enteredCategory,
                $enteredPrice,
                $enteredImagePath,
                $enteredThumbnailPath
            );

            return $this->pms->createProduct($productToCreate);
        }
        throw new \Exception('You are not an admin and therefore have no rights to use this API! Be gone!!!');
    }

}