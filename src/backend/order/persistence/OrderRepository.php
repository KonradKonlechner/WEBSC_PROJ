<?php

namespace order;

use db;

include ('../../config/dbaccess.php');
require_once "../model/Order.php";

class OrderRepository
{

    private function fetchAllAndBindResult(mixed $statement)
    {
        $statement->execute();
        $statement->bind_result($id, $userid, $createdAt);

        $allOrders = [];
        while($statement->fetch()) {
            $fetched =
                new Order( # use constructor to create new instance of product
                    $id,
                    $userid,
                    null,
                    $createdAt
                );
            $allOrders[] = $fetched;
        };

        return $allOrders;
    }
}