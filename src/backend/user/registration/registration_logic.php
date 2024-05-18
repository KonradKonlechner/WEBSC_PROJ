<?php

use userManagement\User as User;
use userManagement\UserInputValidator;
use userManagement\UserManagementSystem;

require_once('../UserManagementSystem.php');
require_once('../UserInputValidator.php');

$ums = new UserManagementSystem();
$inputValidator = new UserInputValidator($ums);

if (!isset($_SESSION)) {
    session_start();
}

$isValidRegistration = false;

if (isset($_SESSION["currentUser"])) {
    header("Location: /userManagement/login/login_form.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["submit"])) {
        $enteredSex =  $inputValidator->prepareInput($_POST["sex"]);
        $enteredName = $inputValidator->prepareInput($_POST["name"]);
        $enteredLastname = $inputValidator->prepareInput($_POST["lastname"]);
        $enteredEmail = $inputValidator->prepareInput($_POST["email"]);
        $enteredUsername = $inputValidator->prepareInput($_POST["username"]);
        $enteredPassword1 = $inputValidator->prepareInput($_POST["password"]);
        $enteredPassword2 = $inputValidator->prepareInput($_POST["password2"]);

        $isValidRegistration = $inputValidator->isValidRegistration($enteredEmail, $enteredUsername, $enteredPassword1, $enteredPassword2);
        $emailErrMessage = $inputValidator->getEmailErrMessage();
        $userNameErrMessage = $inputValidator->getUserNameErrMessage();
        $pwErrMessage = $inputValidator->getPwErrMessage();

        if ($isValidRegistration) {
            $newUser = new User();
            $newUser->setAllValues(
                $enteredUsername,
                $enteredPassword1,
                $enteredSex,
                $enteredName,
                $enteredLastname,
                $enteredEmail
            );

            $ums->saveUserAsRegistered($newUser);
        }
        // ToDo: else { fail }
    }
}
