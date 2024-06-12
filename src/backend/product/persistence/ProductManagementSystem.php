<?php

namespace product;

require_once __DIR__ . '/../model/Product.php';
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

    public function getProductById($productId)
    {
        return$this->repository->getProductById($productId);
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

    public function updateProduct(Product $productToUpdate): ?Product
    {
        $this->repository->updateProduct($productToUpdate);
        return $productToUpdate;
    }

    public function createProduct(product $product): Product
    {
        return$this->repository->createProduct($product);
    }


}