<?php

namespace product;

require_once "../model/Product.php";
require_once "../persistence/ProductManagementSystem.php";

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

    public function getAllProductsOfCategory($productCategory)
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

}