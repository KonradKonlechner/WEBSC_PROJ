<?php

namespace order;

use product\product;

use JsonSerializable;

class OrderPosition implements JsonSerializable
{
    private $positionId;

    private $orderId;

    private Product $product;

    private $quantity;

    public function __construct($positionId, $product, $quantity)
    {
        $this->positionId = $positionId;
        $this->product = $product;
        $this->quantity = $quantity;
    }

    /**
     * @return int
     */
    public function getPositionId()
    {
        return $this->positionId;
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return string
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    public function setQuantity($newQuantity)
    {
        $this->quantity = $newQuantity;
    }

    public function jsonSerialize() {
        return [
            'positionId' => $this->positionId,
            'product' => $this->product,
            'quantity' => $this->quantity
        ];
    }

}