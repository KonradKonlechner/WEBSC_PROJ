<?php

namespace order;

use product\ProductManagementSystem;
use product\ProductRepository;

require_once '../model/Order.php';
require_once '../../product/model/Product.php';
require_once 'OrderRepository.php';
require_once 'PositionRepository.php';
require_once '../../product/persistence/ProductRepository.php';

class OrderManagementSystem
{
    private OrderRepository $orderRepo;
    private ProductRepository $productRepo;
    private PositionRepository $orderPositionRepo;

    public function __construct()
    {
        $this->orderRepo = new OrderRepository();
        $this->orderPositionRepo = new PositionRepository();
        $this->productRepo = new ProductRepository();
    }

    public function addProductToShoppingCart($productId)
    {
        return $this->orderRepo->addProductToShoppingCart($productId);
    }

    public function saveOrder(Order $order)
    {
        return $this->orderRepo->addOrderToDatabase($order);
    }

    public function getOrdersForUser($userId)
    {
        $orders = $this->orderRepo->findAllByUserId($userId);

        foreach ($orders as $order) {
            $positions = $this->orderPositionRepo->findAllOrdersJoinedProductsByOrderId($order->getOrderId());
            foreach ($positions as $position) {
                $order->addPositionWithQuantity($position->getProduct(), $position->getQuantity());
            }
        }
        return $orders;
    }
}