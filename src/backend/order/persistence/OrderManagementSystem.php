<?php

namespace order;

require_once '../model/Order.php';
require_once 'OrderRepository.php';

class OrderManagementSystem
{
    private OrderRepository $repository;

    public function __construct()
    {
        $this->repository = new OrderRepository();
    }

    public function addProductToShoppingCart($productId)
    {
        return$this->repository->addProductToShoppingCart($productId);
    }
}