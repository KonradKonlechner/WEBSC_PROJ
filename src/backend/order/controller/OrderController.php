<?php

namespace order;

require_once "OrderControllerRequestHandler.php";

session_start();

$httpRequestMethod = $_SERVER["REQUEST_METHOD"];

$requestHandler = new OrderControllerRequestHandler();

$param = "";
$method = "";
$httpStatus = 200;

header('Content-Type: application/json');

switch ($httpRequestMethod) {
    case "POST":
        isset($_POST["method"]) ? $method = $_POST["method"] : false;
        isset($_POST["param"]) ? $param = $_POST["param"] : false;

        $data = $requestHandler->handleRequest($method, $param);

        if ($data == null) {
            $httpStatus = 400;
        }
        http_response_code($httpStatus);
        echo(json_encode($data));
        break;
    case "GET":
        isset($_GET["method"]) ? $method = $_GET["method"] : false;
        isset($_GET["param"]) ? $param = $_GET["param"] : false;

        $data = $requestHandler->handleRequest($method, $param);

        if ($data == null && !empty($errors)) {
            $httpStatus = 400;
        }
        http_response_code($httpStatus);
        echo(json_encode($data));
        break;
    case "PUT":
        parse_str(file_get_contents("php://input"), $_PUT);

        $method = $_PUT["method"];
        $param = $_PUT["param"];

        $data = $requestHandler->handleRequest($method, $param);

        if ($data == null) {
            $httpStatus = 400;
        }
        http_response_code($httpStatus);
        echo(json_encode($data));
        break;
    case "DELETE":
        parse_str(file_get_contents("php://input"), $_DELETE);

        $method = $_DELETE["method"];
        $param = $_DELETE["param"];

        $data = $requestHandler->handleRequest($method, $param);

        if ($data == null) {
            $httpStatus = 400;
        }
        http_response_code($httpStatus);
        echo(json_encode($data));
        break;
    default:
        http_response_code(405);
        echo("Method not supported yet!");
}
