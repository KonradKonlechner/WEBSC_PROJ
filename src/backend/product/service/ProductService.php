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

    public function getAllProductsOfCategory($productCategory)
    {
        $productsOfCategory = $this->pms->getAllProductsOfCategory($productCategory);

        return  $productsOfCategory;
    }

}