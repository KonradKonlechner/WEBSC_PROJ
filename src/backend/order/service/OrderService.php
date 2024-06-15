<?php

namespace order;

require_once "../persistence/OrderManagementSystem.php";

require_once "../../product/persistence/ProductManagementSystem.php";

require_once "../model/Order.php";

require_once "../model/OrderPosition.php";

require_once "../../user/model/User.php";

use product\ProductManagementSystem;

class OrderService
{
    private OrderManagementSystem $oms;

    private ProductManagementSystem $pms;

    public function __construct()
    {
        $this->oms = new OrderManagementSystem();
        $this->pms = new ProductManagementSystem();
    }

    public function addProductToShoppingCart($productId)
    {
        $product = $this->pms->getProductById($productId);

        if (!isset($_SESSION["shoppingCart"])) {

            $newOrderPosition = new OrderPosition(null, 1, $product, 1);

            $positions = [$newOrderPosition];

            $newOrder = new Order(null, null, $positions, "new", date("Y-m-d H:i:s", time()));

            $_SESSION["shoppingCart"] = $newOrder;

            return $_SESSION["shoppingCart"];
        } else {

            $_SESSION["shoppingCart"]->addPosition($product);

            return $_SESSION["shoppingCart"];
        }
    }

    public function getSessionShoppingCart()
    {
        if (!isset($_SESSION["shoppingCart"])) {
            return "no shoppingCart is set";
        } else {
            return $_SESSION["shoppingCart"];
        }
    }

    public function updateShoppingCart($updateParameter)
    {
        if (!isset($_SESSION["shoppingCart"])) {
            return "no shoppingCart is set";
        } else {
            $positionId = $updateParameter["positionId"];
            $newQuantity = $updateParameter["newQuantity"];
            // update quantity of a position in shopping cart
            $_SESSION["shoppingCart"]->getPositions()[$positionId]->setQuantity($newQuantity);
            // update total price of all positions in shopping cart
            $_SESSION["shoppingCart"]->calculateTotalPrice();

            return $_SESSION["shoppingCart"];
        }
    }

    public function removePositionFromShoppingCart($positionId)
    {
        if (!isset($_SESSION["shoppingCart"])) {
            return "no shoppingCart is set";
        } else {

            $_SESSION["shoppingCart"]->removePosition($positionId);

            $_SESSION["shoppingCart"]->calculateTotalPrice();

            return $_SESSION["shoppingCart"];
        }
    }

    public function getOrdersForUser()
    {
        if (isset($_SESSION["currentUser"])) {
            $userId = $_SESSION["currentUser"]->getUserId();
            $usersOrders = $this->oms->getOrdersForUser($userId);
            if(count($usersOrders) === 0) {
                return "no orders available";
            }
            return $usersOrders;
        }
        throw new \Exception('You have no permission to access this order.');
    }

    public function orderFromShoppingCart()
    {
        if (isset($_SESSION["currentUser"])) {
            $userId = $_SESSION["currentUser"]->getUserId();
            $_SESSION["shoppingCart"]->setUserId($userId);
            $orderId = $this->oms->saveOrder($_SESSION["shoppingCart"]);
            if ($orderId != null) {
                // after successful saving of new order to database the shopping cart should be reset to empty
                $_SESSION["shoppingCart"] = null;
            }
            return $orderId;
        }
        return "no user session set";
    }

    public function getOrderByOrderId($param)
    {
        $userId = $_SESSION["currentUser"]->getUserId();
        $isAdmin = $_SESSION["currentUser"]->isAdmin();
        $oderId = $this->prepareInput($param["orderId"]);

        $order = $this->oms->getOrderById($oderId);

        if ($order->getUserId() === $userId || $isAdmin === 1) {
            return $order;
        }
        throw new \Exception('You have no permission to excess this order.');
    }


    private function prepareInput($data)
    {
        // Hier findet die Aufbereitung des User-inputs für sämtliche Eingabemasken statt
        $sanitizedInput = htmlspecialchars($data);
        $sanitizedInput = trim($sanitizedInput);
        return stripslashes($sanitizedInput);
    }

    public function getOrderByUserId($param): array
    {
        if ($_SESSION["currentUser"]->isAdmin()) {
            $userId = $this->prepareInput($param["userId"]);
            $ordersForUser = $this->oms->getOrdersForUser($userId);

            if(count($ordersForUser) === 0) {
                return "no orders available";
            }
            return $ordersForUser;
        }
        throw new \Exception('You have no permission to excess this order.');
    }

    public function updateOrderQty($param)
    {
        if ($_SESSION["currentUser"]->isAdmin()) {
            $qty = $this->prepareInput($param["positionQty"]);
            $orderId = $this->prepareInput($param["orderId"]);
            $positionId = $this->prepareInput($param["positionId"]);
            $this->oms->updateOrderPositionQty($orderId, $positionId, $qty);
            return true;
        }
        throw new \Exception('You have no permission to excess this order.');
    }

    public function deleteOrderPos($param)
    {
        if ($_SESSION["currentUser"]->isAdmin()) {
            $orderId = $this->prepareInput($param["orderId"]);
            $positionId = $this->prepareInput($param["positionId"]);
            $this->oms->deleteOrderPosition($orderId, $positionId);
            return true;
        }
        throw new \Exception('You have no permission to excess this order.');
    }


}