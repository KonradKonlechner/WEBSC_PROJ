<?php

namespace order;


use product\product;

require_once "../model/OrderPosition.php";
require_once "../../product/model/Product.php";

class OrderPositionMapper
{
    public static function mapRawData(
        $orderId,
        $positionId,
        $quantity,
        $productId,
        $productName,
        $productDescription,
        $productCategory,
        $productPrice,
        $productImagePath,
        $productThumbnailPath
    )
    {
        $product = new Product(
            $productId,
            $productName,
            $productDescription,
            $productCategory,
            $productPrice,
            $productImagePath,
            $productThumbnailPath
        );

        return new OrderPosition(
            $orderId,
            $positionId,
            $product,
            $quantity
        );
    }
}