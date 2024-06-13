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

    /**
     * @throws \Exception if userId is not current user
     */
    public function getOrdersForUser($userId)
    {
        if (isset($_SESSION["currentUser"])
            && $_SESSION["currentUser"]->getUserId() === $userId) {
            return $this->repository->findAllByUserId($userId);
        }
        throw new \Exception('You have no permission to excess this order.');
    }
}