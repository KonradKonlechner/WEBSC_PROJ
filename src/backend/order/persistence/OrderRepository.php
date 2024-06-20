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
        $connection->close();

        $orderId = $this->getUsersMaxOrderId($userId);

        return $orderId;
    }

    public function findAllByUserId($userId): array
    {
        $connection = db\DBConnection::getConnection();
        $sqlSelect = "SELECT * FROM orders WHERE user_id = ?";

        $statement = $connection->prepare($sqlSelect);
        $statement->bind_param("i", $userId); # character "i" is used due to placeholders of type Integer
        $orders = $this->fetchAllAndBindResult($statement);
        $statement->close();
        $connection->close();
        return $orders;
    }

    public function addNewInvoiceForOrderId($orderId): bool
    {
        $connection = db\DBConnection::getConnection();
        $sqlInsert = "INSERT INTO invoices (order_id) VALUES (?)";

        $statement = $connection->prepare($sqlInsert);
        $statement->bind_param(
            "i",
            $orderId
        );

        $statementExecutionSuccess = $statement->execute();
        $statement->close();
        $connection->close();
        return $statementExecutionSuccess;
    }

    public function getOrdersMaxInvoiceId($orderId)
    {
        $connection = db\DBConnection::getConnection();
        $sqlSelect = "SELECT MAX(id) FROM invoices WHERE order_id = ?";

        $statement = $connection->prepare($sqlSelect);
        $statement->bind_param("i", $orderId);
        $statement->execute();
        $statement->bind_result($invoiceId);
        $statement->fetch();
        $statement->close();
        $connection->close();
        return $invoiceId;
    }

    public function getInvoiceIdByOrderId($orderId)
    {
        $invoiceId = $this->getOrdersMaxInvoiceId($orderId);

        if($invoiceId === null) {
            if($this->addNewInvoiceForOrderId($orderId) === true) {
                $invoiceId = $this->getOrdersMaxInvoiceId($orderId);
            }
        }

        return $invoiceId;
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

    public function findByOrderId($orderId): ?Order
    {
        $connection = db\DBConnection::getConnection();
        $sqlSelect = "SELECT * FROM orders WHERE id = ?";

        $statement = $connection->prepare($sqlSelect);
        $statement->bind_param("i", $orderId);

        $statement->execute();
        $statement->bind_result($foundOrderId, $userid, $totalPrice, $state, $createdAt);
        $statement->fetch();

        if($foundOrderId == null) {
            return null;
        }

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

    public function updateOrderState(string $orderId, string $state)
    {
        $connection = db\DBConnection::getConnection();
        $sqlSelect = "UPDATE orders SET state = ? WHERE id = ? ";

        $statement = $connection->prepare($sqlSelect);
        $statement->bind_param("ss", $state, $orderId); # character "s" is used due to placeholders of type String

        $statement->execute();
        $statement->close();
        $connection->close();
    }
}