<?php

namespace order;

require_once "../persistence/OrderManagementSystem.php";

require_once "../../product/persistence/ProductManagementSystem.php";

require_once "../model/Order.php";

require_once "../model/OrderPosition.php";

use product\ProductManagementSystem;

/*
use order\Order;
use order\OrderManagementSystem;
use order\OrderPosition;
*/

//use product\ProductManagementSystem;

/*
require_once "../model/Order.php";
require_once "../persistence/OrderManagementSystem.php";
require_once "../../product/model/Product.php";
require_once "../../product/persistence/ProductManagementSystem.php";
*/
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

        if (!isset($_SESSION["shoppingCart"])){

            $newOrderPosition = new OrderPosition(1, $product, 1);

            $positions = [$newOrderPosition];
            $newOrder = new Order(null, null, $positions);

            $_SESSION["shoppingCart"] = $newOrder;

            return $_SESSION["shoppingCart"];
        } else {

            $_SESSION["shoppingCart"]->addPosition($product);

            return $_SESSION["shoppingCart"];
        }
    }

    public function getSessionShoppingCart()
    {
        if (!isset($_SESSION["shoppingCart"])){
            return "no shoppingCart is set";
        } else {
            return $_SESSION["shoppingCart"];
        }
    }

    public function updateShoppingCart($updateParameter)
    {
        if (!isset($_SESSION["shoppingCart"])){
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
        if (!isset($_SESSION["shoppingCart"])){
            return "no shoppingCart is set";
        } else {

            $_SESSION["shoppingCart"]->removePosition($positionId);

            $_SESSION["shoppingCart"]->calculateTotalPrice();

            return $_SESSION["shoppingCart"];
        }
    }



}