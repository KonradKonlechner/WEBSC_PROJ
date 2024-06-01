<?php
namespace user;
require_once "../service/LoginService.php";

class UserControllerRequestHandler
{
    private $service;

    function __construct()
    {
        $this->service = new LoginService();
    }

    function handleRequest($method, $param)
    {
        switch ($method) {
            case "login":
                $res = $this->service->loginWithParameters($param);
                break;
            case "logout":
                $res = $this->service->logout();
                break;
            case "checkUserSession":
                $res = $this->service->checkUserSession();
                break;
            case "checkUserIsAdmin":
                $res = $this->service->checkUserIsAdmin();
                break;
            case "getCurrentUser":
                $res = $this->service->getCurrentUser();
                break;
            case "registerUser":
                $res = $this->service->registerUser($param);
                break;
            case "updateUserProfile":
                $res = $this->service->updateUserProfile($param);
                break;
            default:
                $res = null;
                break;
        }
        return $res;
    }
}