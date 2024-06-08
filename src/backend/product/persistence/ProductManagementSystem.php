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

    public function getAllProducts()
    {
        return$this->repository->getAllProducts();
    }

    public function getAllProductsOfCategory($productCategory)
    {
        return$this->repository->getAllProductsOfCategory($productCategory);
    }

    public function getAllProductsFilteredBySearchTerm($searchTerm)
    {
        return$this->repository->getAllProductsFilteredBySearchTerm($searchTerm);
    }

    public function getAllProductsFilteredBySearchTermAndCategory($searchTerm, $productCategory)
    {
        return$this->repository->getAllProductsFilteredBySearchTermAndCategory($searchTerm, $productCategory);
    }



}