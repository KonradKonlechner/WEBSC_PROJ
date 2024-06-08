<?php

namespace product;

require_once "../service/ProductService.php";

class ProductControllerRequestHandler
{
    private $service;

    function __construct()
    {
        $this->service = new ProductService();
    }

    function handleRequest($method, $param)
    {
        switch ($method) {
            case "getAllProductsOfCategory":
                $res = $this->service->getAllProductsOfCategory($param);
                break;
            default:
                $res = null;
                break;
        }
        return $res;
    }
}