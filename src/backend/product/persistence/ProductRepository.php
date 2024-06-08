<?php

namespace product;

use db;

include ('../../config/dbaccess.php');
require_once "../model/Product.php";


class ProductRepository
{
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

}