<?php

namespace order;

class Position
{
    private $positionId;
    private $orderId;
    private $productId;
    private $quantity;

    /**
     * @param $positionId
     * @param $orderId
     * @param $productId
     * @param $quantity
     */
    public function __construct($positionId, $orderId, $productId, $quantity)
    {
        $this->positionId = $positionId;
        $this->orderId = $orderId;
        $this->productId = $productId;
        $this->quantity = $quantity;
    }
}