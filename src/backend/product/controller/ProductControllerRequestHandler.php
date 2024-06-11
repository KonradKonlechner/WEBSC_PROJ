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
            case "getAllProducts":
                $res = $this->service->getAllProducts();
                break;
            case "getAllProductsOfCategory":
                $res = $this->service->getAllProductsOfCategory($param);
                break;
            case "getAllProductsFilteredBySearchTermAndCategory":
                $res = $this->service->getAllProductsFilteredBySearchTermAndCategory($param);
                break;
            case "updateProduct":
                $res = $this->service->updateProduct($param);
                break;
            default:
                $res = null;
                break;
        }
        return $res;
    }
}