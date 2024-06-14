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

            $newOrderPosition = new OrderPosition(1, $product, 1);

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

            $_SESSION["shoppingCart"]->getPositions()[$positionId]->setQuantity($newQuantity);

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
            return $this->oms->getOrdersForUser($userId);
        }
        throw new \Exception('You have no permission to excess this order.');

    }

    public function orderFromShoppingCart()
    {
        if (isset($_SESSION["currentUser"])) {
            $userId = $_SESSION["currentUser"]->getUserId();
            $_SESSION["shoppingCart"]->setUserId($userId);

            return "OrderId: " . $this->oms->saveOrder($_SESSION["shoppingCart"]);
        }
        return "no user session set";
    }


    private function prepareInput($data)
    {
        // Hier findet die Aufbereitung des User-inputs für sämtliche Eingabemasken statt
        $sanitizedInput = htmlspecialchars($data);
        $sanitizedInput = trim($sanitizedInput);
        return stripslashes($sanitizedInput);
    }


}