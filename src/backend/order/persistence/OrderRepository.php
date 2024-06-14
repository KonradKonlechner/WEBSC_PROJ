<?php

namespace order;

use db;

include ('../../config/dbaccess.php');
require_once "../model/Order.php";

class OrderRepository
{
    public function getUsersMaxOrderId($userId)
    {
        $connection = db\DBConnection::getConnection();
        $sqlSelect = "SELECT MAX(id) FROM orders WHERE user_id = ?";

        $statement = $connection->prepare($sqlSelect);
        $statement->bind_param("i", $userId);
        $statement->execute();
        $statement->bind_result($maxId);
        $statement->fetch();
        $statement->close();
        $connection->close();
        return $maxId;
    }

    public function addOrderToDatabase(Order $order)
    {
        $connection = db\DBConnection::getConnection();

        // save order to orders table

        $userId = $order->getUserId();
        $totalPrice = $order->getTotalPrice();

        $sqlInsert = "INSERT INTO orders (user_id, total_price_eur) VALUES (?,?)";

        $statement = $connection->prepare($sqlInsert);
        $statement->bind_param(
            "id",
            $userId,
            $totalPrice
        );

        $statement->execute();
        $statement->close();

        // save positions to orderpositions table

        $orderId = $this->getUsersMaxOrderId($userId);
        $positions = $order->getPositions();

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

            $statement->execute();
            $statement->close();
        }

        $connection->close();

        return $orderId;
    }

    public function findAllByUserId($userId): array
    {
        $connection = db\DBConnection::getConnection();
        $sqlSelect = "SELECT * FROM orders WHERE user_id = ?";

        $statement = $connection->prepare($sqlSelect);
        $statement->bind_param("s", $userId); # character "s" is used due to placeholders of type String
        $allProductsOfCategory = $this->fetchAllAndBindResult($statement);
        $statement->close();
        $connection->close();
        return $allProductsOfCategory;
    }

    private function fetchAllAndBindResult(mixed $statement): array
    {
        $statement->execute();
        $statement->bind_result($orderId, $userid, $totalPrice, $state, $createdAt);

        $allOrders = [];
        while($statement->fetch()) {
            $fetched =
                new Order( # use constructor to create new instance of order
                    $orderId,
                    $userid,
                    [],
                    $state,
                    $createdAt
                );
            $allOrders[] = $fetched;
        };

        return $allOrders;
    }
}