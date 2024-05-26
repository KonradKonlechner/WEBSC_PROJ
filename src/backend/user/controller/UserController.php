<?php
namespace user;
session_start();
require_once "UserControllerRequestHandler.php";
require_once "../model/User.php";

$param = "";
$method = "";
$httpRequestMethod = "POST";

isset($_POST["method"]) ? $method = $_POST["method"] : false;
isset($_POST["param"]) ? $param = $_POST["param"] : false;

if($method === "") {
    $httpRequestMethod = "GET";
    isset($_GET["method"]) ? $method = $_GET["method"] : false;
    isset($_GET["param"]) ? $param = $_GET["param"] : false;
}

$requestHandler = new UserControllerRequestHandler();
$result = $requestHandler->handleRequest($method, $param);
if ($result == null) {
    response($httpRequestMethod, 400, null);
} else {
    response($httpRequestMethod, 200, $result);
}

function response($httpRequestMethod, $httpStatus, $data)
{
    header('Content-Type: application/json');
    switch ($httpRequestMethod) {
        case "POST":
            http_response_code($httpStatus);
            echo (json_encode($data));
            break;
        case "GET":
            http_response_code($httpStatus);
            echo (json_encode($data));
            break;
        default:
            http_response_code(405);
            echo ("Method not supported yet!");
    }
}