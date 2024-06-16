<?php

namespace order;

require_once "../service/OrderService.php";

class OrderControllerRequestHandler
{
    private $service;

    function __construct()
    {
        $this->service = new OrderService();
    }

    function handleRequest($method, $param)
    {
        switch ($method) {
            case "addProductToShoppingCart":
                $res = $this->service->addProductToShoppingCart($param);
                break;
            case "getSessionShoppingCart":
                $res = $this->service->getSessionShoppingCart();
                break;
            case "updateShoppingCart":
                $res = $this->service->updateShoppingCart($param);
                break;
            case "removePositionFromShoppingCart":
                $res = $this->service->removePositionFromShoppingCart($param);
                break;
            case "getOrdersForUser":
                $res = $this->service->getOrdersForUser();
                break;
            case "orderFromShoppingCart":
                $res = $this->service->orderFromShoppingCart();
                break;
            case "getOrderByOrderId":
                $res = $this->service->getOrderByOrderId($param);
                break;
            case "getOrdersForUserId":
                $res = $this->service->getOrderByUserId($param);
                break;
            case "updateOrderQty":
                $res = $this->service->updateOrderQty($param);
                break;
            case "deleteOrderPosition":
                $res = $this->service->deleteOrderPos($param);
                break;
            case "updateOrderState":
                $res = $this->service->updateOrderState($param);
                break;
            case "getInvoiceIdByOrderId":
                $res = $this->service->getInvoiceIdByOrderId($param);
                break;
            default:
                $res = null;
                break;
        }
        return $res;
    }
}