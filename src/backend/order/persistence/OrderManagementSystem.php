<?php

namespace order;

require_once '../model/Order.php';
require_once 'OrderRepository.php';
require_once 'PositionRepository.php';

class OrderManagementSystem
{
    private OrderRepository $orderRepo;
    private PositionRepository $orderPositionRepo;

    public function __construct()
    {
        $this->orderRepo = new OrderRepository();
        $this->orderPositionRepo = new PositionRepository();
    }

    public function addProductToShoppingCart($productId)
    {
        return $this->orderRepo->addProductToShoppingCart($productId);
    }

    public function saveOrder(Order $order)
    {
        $previousOrderId = $this->orderRepo->getUsersMaxOrderId($order->getUserId());
        $newOrderId = $this->orderRepo->addOrderToDatabase($order);

        if($previousOrderId != $newOrderId) {
            $order->setOrderId($newOrderId);
            if($this->orderPositionRepo->addOrderPositionsToDatabase($order) === true)
            {
                return $newOrderId;
            }
        }
        return null;
    }

    public function getOrdersForUser($userId)
    {
        $orders = $this->orderRepo->findAllByUserId($userId);

        foreach ($orders as $order) {
            $positions = $this->orderPositionRepo->findAllOrdersJoinedProductsByOrderId($order->getOrderId());
            foreach ($positions as $position) {
                $order->addPositionValue($position);
            }
        }
        return $orders;
    }

    public function getOrderById(string $oderId): Order
    {
        $order = $this->orderRepo->findByOrderId($oderId);
        $positions = $this->orderPositionRepo->findAllOrdersJoinedProductsByOrderId($oderId);

        foreach ($positions as $position) {
            $order->addPositionValue($position);
        }

        return $order;
    }

    public function updateOrderPositionQty(string $orderId, string $positionId, string $qty)
    {
        $orderPos = $this->orderPositionRepo->findPositionByIds($orderId, $positionId);
        if ($orderPos == null) {
            throw new \Exception("Keine Bestellungsposition mit den gesuchten Kriterien vorhanden.");
        }
        $this->orderPositionRepo->updateOrderPositionQty($orderId, $positionId, $qty);
    }

    public function deleteOrderPosition(string $orderId, string $positionId)
    {
        $orderPos = $this->orderPositionRepo->findPositionByIds($orderId, $positionId);
        if ($orderPos == null) {
            throw new \Exception("Keine Bestellungsposition mit den gesuchten Kriterien vorhanden.");
        }
        $this->orderPositionRepo->deleteOrderPosition($orderId, $positionId);
    }

    public function updateOrderState(string $orderId, string $state)
    {
        $orderPos = $this->orderRepo->findByOrderId($orderId);
        if ($orderPos == null) {
            throw new \Exception("Keine Bestellung mit den gesuchten Kriterien vorhanden.");
        }
        $this->orderRepo->updateOrderState($orderId, $state);
    }

    public function getInvoiceIdByOrderId($orderId)
    {
        return $this->orderRepo->getInvoiceIdByOrderId($orderId);
    }

}