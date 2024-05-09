<?php

require_once('./logic/userManagement/UserManagementSystem.php');

$ums = new UserManagementSystem();

$username = "";
$password = "";

isset($_POST["username"]) ? $username = $_POST["username"] : false;
isset($_POST["password"]) ? $password = $_POST["password"] : false;

//echo $ums->isRegisteredUserWithCorrectPassword('maxim', 'holimoli');

$pwCheckResult = $ums->isRegisteredUserWithCorrectPassword($username,  $password);

//http_response_code(200);

if($pwCheckResult == TRUE){
    echo 'user and pw correct!';
} else {
    echo 'user or pw wrong!';
}