<?php

namespace product;

require_once '../model/Product.php';
require_once 'ProductRepository.php';

class ProductManagementSystem
{
    private ProductRepository $repository;

    public function __construct()
    {
        $this->repository = new ProductRepository();
    }

    public function getAllProductsOfCategory($productCategory)
    {
        return$this->repository->getAllProductsOfCategory($productCategory);
    }

}