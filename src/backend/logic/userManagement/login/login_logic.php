<?php

use userManagement\UserManagementSystem;

require_once('../UserManagementSystem.php');
require_once('../UserInputValidator.php');

$ums = new UserManagementSystem();
$inputValidator = new \userManagement\UserInputValidator($ums);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    echo "<script>console.log(' Logging in... ' );</script>";

    $enteredUsername = $inputValidator->prepareInput($_POST["username"]);
    $enteredPassword = $inputValidator->prepareInput($_POST["password"]);

    if (!isset($_SESSION["currentUser"])
        && $ums->isRegisteredUserWithCorrectPassword($enteredUsername, $enteredPassword)) {

        $loggedInUser = $ums->getUserByUsername($enteredUsername);
        $_SESSION["currentUser"] = $loggedInUser;
        $_SESSION["currentUserIsAdminUser"] = $loggedInUser->isAdmin();

        setcookie("LOGON_USER", $_SESSION["currentUser"]->getUsername(), time() + 31557500, '/');

        echo "<script>console.log(' Logged in as $enteredUsername' );</script>";
    } else {
        echo "<script>console.log(' Please check if you are already logged in or your input!' );</script>";
    }
}

if (isset($_POST["submit"]) && $_POST["submit"] == 'logout') {
    echo "<script>console.log(' Logging out... ' );</script>";
    session_unset();
    setcookie('LOGON_USER', '', -1, '/');
    echo "<script>console.log(' Session has been closed ' );</script>";
}

?>