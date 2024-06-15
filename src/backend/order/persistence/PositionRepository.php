<?php


namespace order;

use db;

require_once "../mapper/OrderPositionMapper.php";
require_once "../model/Position.php";

class PositionRepository
{
    public function addOrderPositionsToDatabase(Order $order)
    {
        $connection = db\DBConnection::getConnection();

        $orderId = $order->getOrderId();
        $positions = $order->getPositions();
        $statementSuccess = false;

        foreach ($positions as $p) {
            $positionId = $p->getPositionId();
            $positionProductId = $p->getProduct()->getId();
            $positionQuantity = $p->getQuantity();

            $sqlInsert = "INSERT INTO orderpositions (order_id, position_id, product_id, quantity) VALUES (?,?,?,?)";

            $statement = $connection->prepare($sqlInsert);
            $statement->bind_param(
                "iiii",
                $orderId,
                $positionId,
                $positionProductId,
                $positionQuantity
            );

            $statementSuccess = $statement->execute();
            $statement->close();
        }

        $connection->close();

        return $statementSuccess;
    }

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

    public function findOrderByIds(string $orderId, string $positionId): ?Position
    {
        $connection = db\DBConnection::getConnection();
        $sqlSelect = "SELECT * FROM orderpositions WHERE order_id = ? AND position_id= ?";

        $statement = $connection->prepare($sqlSelect);
        $statement->bind_param("ss", $orderId, $positionId); # character "s" is used due to placeholders of type String


        $statement->execute();
        $statement->bind_result($foundOrderId, $foundPositionId, $productId, $qty);
        $statement->fetch();
        $statement->close();
        $connection->close();

        if ($foundPositionId == null) {
            return null;
        }

        return new Position(
            $foundOrderId,
            $foundPositionId,
            $productId,
            $qty
        );
    }

    public function updateOrderPositionQty(string $orderId, string $positionId, string $qty)
    {
        $connection = db\DBConnection::getConnection();
        $sqlSelect = "UPDATE orderpositions SET quantity = ? WHERE order_id = ? AND position_id= ?";

        $statement = $connection->prepare($sqlSelect);
        $statement->bind_param("sss", $qty, $orderId, $positionId); # character "s" is used due to placeholders of type String

        $statement->execute();
        $statement->close();
        $connection->close();
    }

    public function deleteOrderPosition(string $orderId, string $positionId)
    {
        $connection = db\DBConnection::getConnection();
        $sqlSelect = "DELETE FROM orderpositions WHERE order_id = ? AND position_id= ?";

        $statement = $connection->prepare($sqlSelect);
        $statement->bind_param("ss", $orderId, $positionId); # character "s" is used due to placeholders of type String

        $statement->execute();
        $statement->close();
        $connection->close();
    }
}