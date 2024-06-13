<?php

namespace order;

use db;

include ('../../config/dbaccess.php');
require_once "../model/Order.php";

class OrderRepository
{

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