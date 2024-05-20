<?php
namespace user;
require_once "../service/LoginService.php";

class UserControllerRequestHandler
{
    private $service;

    function __construct()
    {
        $this->service = new \user\LoginService();
    }

    function handleRequest($method, $param)
    {
        switch ($method) {
            case "login":
                $res = $this->service->loginWithParameters($param);
                break;
            default:
                $res = null;
                break;
        }
        return $res;
    }
}