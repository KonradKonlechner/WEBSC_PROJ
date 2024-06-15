<?php


namespace order;

use db;

require_once "../mapper/OrderPositionMapper.php";

class PositionRepository
{

    public function findAllOrdersJoinedProductsByOrderId($orderId): array
    {
        $connection = db\DBConnection::getConnection();
        $sqlSelect = "SELECT "
            // order data
            . " o.order_id, o.position_id, o.product_id, o.quantity, "
            // product data
            . " p.name, p.description, p.category ,p.price_per_unit_eur, p.image_path, p.thumbnail_path "
            . " FROM orderpositions as o "
            // join clause
            . " JOIN products as p ON p.id = o.product_id "
            // where clause
            . " WHERE order_id = ?";

        $statement = $connection->prepare($sqlSelect);
        $statement->bind_param("i", $orderId); # character "s" is used due to placeholders of type String
        $orderPositions = $this->fetchAllAndBindResult($statement);
        $statement->close();
        $connection->close();
        return $orderPositions;
    }

    private function fetchAllAndBindResult(mixed $statement): array
    {
        $statement->execute();
        $statement->bind_result(
            $orderId,
            $positionId,
            $productId,
            $quantity,
            $productName,
            $productDescription,
            $productCategory,
            $productPrice,
            $productImagePath,
            $productThumbnailPath
        );

        $allOrderPositions = [];
        while ($statement->fetch()) {
            $fetched =
                OrderPositionMapper::mapRawData(
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
                );
            $allOrderPositions[] = $fetched;
        };

        return $allOrderPositions;
    }
}