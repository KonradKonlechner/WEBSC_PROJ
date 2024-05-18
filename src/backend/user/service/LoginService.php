<?php
namespace user;
require_once "../persistence/UserManagementSystem.php";
require_once "UserInputValidator.php";

class LoginService
{
    private UserManagementSystem $ums;
    private UserInputValidator $inputValidator;

    public function __construct()
    {
        $this->ums = new UserManagementSystem();
        $this->inputValidator = new UserInputValidator($this->ums);
    }

    public function loginWithParameters($param)
    {
        $enteredUsername = $this->inputValidator->prepareInput($param["username"]);
        $enteredPassword = $this->inputValidator->prepareInput($param["password"]);

        if (!isset($_SESSION["currentUser"])
            && $this->ums->isRegisteredUserWithCorrectPassword($enteredUsername, $enteredPassword)) {

            $loggedInUser = $this->ums->getUserByUsername($enteredUsername);
            $_SESSION["currentUser"] = $loggedInUser;
            $_SESSION["currentUserIsAdminUser"] = $loggedInUser->isAdmin();

            //setcookie("LOGON_USER", $_SESSION["currentUser"]->getUsername(), time()+31557500, '/');

            //echo "<script>console.log(' Logged in as $enteredUsername' );</script>";
            return "Login Successful";
        } else {
            //echo "<script>console.log(' Please check if you are already logged in or your input!' );</script>";
            return "Login was not successful";
        }
    }
}