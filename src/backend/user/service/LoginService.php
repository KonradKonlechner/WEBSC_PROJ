<?php
namespace user;
require_once "../model/User.php";
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

            return $loggedInUser;
        } else {
            return "Login was not successful!";
        }
    }

    public function logout()
    {
        if (isset($_SESSION["currentUser"])) {
            $_SESSION["currentUser"] = null;
            $_SESSION["currentUserIsAdminUser"] = null;
            return "User logged out.";
        } else {
            return "No user has been logged in!";
        }
    }

    public function checkUserSession()
    {
        if (isset($_SESSION["currentUser"])) {
            //echo "user session is set!";
            return "1";
        } else {
            //echo "user session is not set!";
            return "0";
        }
    }

    public function getCurrentUser()
    {
        if (isset($_SESSION["currentUser"])) {
            return $_SESSION["currentUser"];
        } else {
            return "No user has been logged in!";
        }
    }

}