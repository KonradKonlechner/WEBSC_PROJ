<?php

namespace product;

use db;

require_once '../../config/dbaccess.php';
require_once __DIR__ . '/../model/Product.php';


class ProductRepository
{

    public function getAllProducts()
    {
        $connection = db\DBConnection::getConnection();
        $sqlSelect = "SELECT * FROM products";

        $statement = $connection->prepare($sqlSelect);

        $allProducts = $this->fetchAllAndBindResult($statement);
        $statement->close();
        $connection->close();
        return $allProducts;
    }

    public function getProductById($productId)
    {
        $connection = db\DBConnection::getConnection();
        $sqlSelect = "SELECT * FROM products WHERE id = ?";

        $statement = $connection->prepare($sqlSelect);
        $statement->bind_param("s", $productId); # character "s" is used due to placeholders of type String
        $products = $this->fetchAllAndBindResult($statement);
        $statement->close();
        $connection->close();
        return $products[0];
    }

    public function getAllProductsOfCategory($productCategory)
    {
        $connection = db\DBConnection::getConnection();
        $sqlSelect = "SELECT * FROM products WHERE category = ?";

        $statement = $connection->prepare($sqlSelect);
        $statement->bind_param("s", $productCategory); # character "s" is used due to placeholders of type String
        $allProductsOfCategory = $this->fetchAllAndBindResult($statement);
        $statement->close();
        $connection->close();
        return $allProductsOfCategory;
    }

    public function getAllProductsFilteredBySearchTerm($searchTerm)
    {
        $connection = db\DBConnection::getConnection();
        $sqlSelect = "SELECT * FROM products WHERE name LIKE CONCAT('%',?,'%')";

        $statement = $connection->prepare($sqlSelect);
        $statement->bind_param("s", $searchTerm); # character "s" is used due to placeholders of type String
        $allProductsFilteredBySearchTerm = $this->fetchAllAndBindResult($statement);
        $statement->close();
        $connection->close();
        return $allProductsFilteredBySearchTerm;
    }

    public function getAllProductsFilteredBySearchTermAndCategory($searchTerm, $productCategory)
    {
        $connection = db\DBConnection::getConnection();
        $sqlSelect = "SELECT * FROM products WHERE name LIKE CONCAT('%',?,'%') AND category = ?";

        $statement = $connection->prepare($sqlSelect);
        $statement->bind_param("ss", $searchTerm, $productCategory); # character "s" is used due to placeholders of type String
        $allProductsFilteredBySearchTerm = $this->fetchAllAndBindResult($statement);
        $statement->close();
        $connection->close();
        return $allProductsFilteredBySearchTerm;
    }

    private function fetchAllAndBindResult(mixed $statement)
    {
        $statement->execute();

        $statement->bind_result($id, $name, $description, $category, $price, $imagePath, $thumbnailPath, $createdAt);

        $allProducts = [];
        while($statement->fetch()) {
            $fetched =
                new Product( # use constructor to create new instance of product
                $id,
                $name,
                $description,
                $category,
                $price,
                $imagePath,
                $thumbnailPath
            );
            $allProducts[] = $fetched;
        };

        return $allProducts;
    }

    public function updateProduct(Product $productToUpdate)
    {
        $connection = db\DBConnection::getConnection();

        $name = $productToUpdate->getName();
        $description = $productToUpdate->getDescription();
        $category = $productToUpdate->getCategory();
        $price = $productToUpdate->getPrice();
        $imagePath = $productToUpdate->getImagePath();
        $thumbnailPath = $productToUpdate->getThumbnailPath();
        $id = $productToUpdate->getId();

        $sqlInsert = "UPDATE products SET 
                 name = ?, description = ?, category = ?, price_per_unit_eur = ?, image_path = ?, thumbnail_path = ?
                 WHERE id = ?";

        $statement = $connection->prepare($sqlInsert);

        $statement->bind_param(
            "sssdssi",
            $name,
            $description,
            $category,
            $price,
            $imagePath,
            $thumbnailPath,
            $id
        );

        $statement->execute();
        $statement->close();
        $connection->close();
    }

}