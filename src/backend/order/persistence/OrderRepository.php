<?php

namespace order;

use db;

include ('../../config/dbaccess.php');
require_once "../model/Order.php";
require_once "../model/OrderPosition.php";

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
        // ToDo: please not here... saving order positions can be done in a separate repository
        // Using our current format, these query methods are long and ugly enough, event without doing multiple actions
        // For exactly this reason (keeping our repositories somewhat neat and clean) we use the management systems
        // In a real world application which uses actually modern technology this would be done by JDBC or something similar
        // As a rule of thumb I suggest: if you can reasonably put in in one query, then its ok (e.g. simple joins that reduce overhead
        // time as we do not have to open and close connections x times) and event then, we try to do as much logic outside of the repos
        // (e.g. using mappers, etc.)

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
        $statement->bind_param("i", $userId); # character "s" is used due to placeholders of type String
        $orders = $this->fetchAllAndBindResult($statement);
        $statement->close();
        $connection->close();
        return $orders;
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

    public function findByOrderId($orderId): Order
    {
        $connection = db\DBConnection::getConnection();
        $sqlSelect = "SELECT * FROM orders WHERE id = ?";

        $statement = $connection->prepare($sqlSelect);
        $statement->bind_param("i", $orderId);

        $statement->execute();
        $statement->bind_result($foundOrderId, $userid, $totalPrice, $state, $createdAt);
        $statement->fetch();
        $order = new Order( # use constructor to create new instance of order
            $foundOrderId,
            $userid,
            [],
            $state,
            $createdAt
        );

        $statement->close();
        $connection->close();
        return $order;
    }
}