<?php

namespace order;

use order\OrderPosition;

use JsonSerializable;

class Order implements JsonSerializable
{
    private $orderId;

    private $userId;

    private array $positions = array();

    private $totalPrice;

    private $state;

    private $createdAt;

    public function __construct($orderId, $userId, $positions, $state, $createdAt)
    {
        $this->orderId = $orderId;
        $this->userId = $userId;
        $this->positions = $positions;
        $this->totalPrice = $this->calculateTotalPrice();
        $this->state = $state;
        $this->createdAt = $createdAt;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getState()
    {
        return $this->state;
    }

    public function getPositions()
    {
        return $this->positions;
    }

    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    public function calculateTotalPrice(): float
    {
        $totalPrice = 0;

        foreach ($this->positions as $position) {
            $totalPrice = $totalPrice + $position->getProduct()->getPrice() * $position->getQuantity();
        }

        $totalPrice = round($totalPrice, 2);

        $this->totalPrice = $totalPrice;

        return $totalPrice;
    }

    public function addPosition($product)
    {
        if(count($this->positions) === 0) {
            $positionId = 1;
        } else {
            $positionId = $this->positions[count($this->positions)-1]->getPositionId()+1;
        }

        $newOrderPosition = new OrderPosition($positionId, $product, 1);

        array_push($this->positions, $newOrderPosition);

        $this->calculateTotalPrice();
    }

    public function removePosition($idx)
    {
        array_splice($this->positions, $idx, 1);

        $this->calculateTotalPrice();
    }

    public function jsonSerialize() {
        return [
            'orderId' => $this->orderId,
            'userId' => $this->userId,
            'positions' => $this->positions,
            'totalPrice' => $this->totalPrice,
            'state' => $this->state,
            'createdAt' => $this->createdAt
        ];
    }
}