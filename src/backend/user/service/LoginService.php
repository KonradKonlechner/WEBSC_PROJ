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

    public function registerUser($param)
    {
        $enteredSex = $this->inputValidator->prepareInput($param["sex"]);
        $enteredName = $this->inputValidator->prepareInput($param["name"]);
        $enteredLastName = $this->inputValidator->prepareInput($param["lastname"]);
        $enteredAddress = $this->inputValidator->prepareInput($param["address"]);
        $enteredPostalCode = $this->inputValidator->prepareInput($param["postalCode"]);
        $enteredCity = $this->inputValidator->prepareInput($param["city"]);
        $enteredEmail = $this->inputValidator->prepareInput($param["email"]);
        $enteredUsername = $this->inputValidator->prepareInput($param["username"]);
        $enteredPassword = $this->inputValidator->prepareInput($param["password"]);
        $enteredPassword2 = $this->inputValidator->prepareInput($param["password2"]);

        if (!isset($_SESSION["currentUser"])) {

            $validationErrors = $this->inputValidator->isValidRegistration(
                $enteredEmail,
                $enteredUsername,
                $enteredPassword,
                $enteredPassword2
            );

            if ($this->inputValidator->arrayContainsErrors($validationErrors)) {
                $validationErrors = array_filter($validationErrors, function ($error) {return $error !== "";});
                return "Die angegebenen Daten beinhalten "
                    . count($validationErrors)
                    . " Fehler:\n"
                    . implode(PHP_EOL, $validationErrors);
            }
            $user = new User();
            $user->setAllValues(
                $enteredUsername,
                $enteredPassword,
                $enteredSex,
                $enteredName,
                $enteredLastName,
                $enteredAddress,
                $enteredPostalCode,
                $enteredCity,
                $enteredEmail
            );

            $this->ums->saveUserAsRegistered($user);

            $param["keepLogin"] = "false";
            $this->loginWithParameters($param);
            return "Registrierung erfolgreich!";
        }
        return "User already logged in. Can't perform registration.";
    }

    public function updateUserProfile($param)
    {
        $enteredUsername = $this->inputValidator->prepareInput($param["username"]);
        $enteredSex = $this->inputValidator->prepareInput($param["sex"]);
        $enteredName = $this->inputValidator->prepareInput($param["name"]);
        $enteredLastName = $this->inputValidator->prepareInput($param["lastname"]);
        $enteredAddress = $this->inputValidator->prepareInput($param["address"]);
        $enteredPostalCode = $this->inputValidator->prepareInput($param["postal_code"]);
        $enteredCity = $this->inputValidator->prepareInput($param["city"]);
        $enteredEmail = $this->inputValidator->prepareInput($param["email"]);
        $enteredNewPassword = $this->inputValidator->prepareInput($param["passwordNew"]);
        $enteredPassword = $this->inputValidator->prepareInput($param["password"]);

        if($this->ums->isRegisteredUserWithCorrectPassword($enteredUsername, $enteredPassword)) {

            $updatedUser = new User();;

            $updatedUser->setAllValues(
                $enteredUsername,
                null,
                $enteredSex,
                $enteredName,
                $enteredLastName,
                $enteredAddress,
                $enteredPostalCode,
                $enteredCity,
                $enteredEmail
            );

            $this->ums->updateUser($updatedUser);

            if($enteredNewPassword != "") {
                $this->ums->updateUserPassword($enteredUsername, $enteredNewPassword);
            }

            $_SESSION["currentUser"] = $this->ums->getUserByUsername($enteredUsername);

            return "user profile has been updated";
        } else {
            return "failed to update user profile";
        }
    }

    public function loginWithParameters($param)
    {
        $enteredUsername = $this->inputValidator->prepareInput($param["username"]);
        $enteredPassword = $this->inputValidator->prepareInput($param["password"]);
        $keepLogin = $this->inputValidator->prepareInput($param["keepLogin"]);

        if (!isset($_SESSION["currentUser"])
            && $this->ums->isRegisteredUserWithCorrectPassword($enteredUsername, $enteredPassword)) {

            $loggedInUser = $this->ums->getUserByUsername($enteredUsername);
            if($keepLogin === "true") {
                setcookie("keepLoggedIn", $enteredUsername, time() + 3600);
            }
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
            setcookie("keepLoggedIn", "", time() - 3600);
            return "User logged out.";
        } else {
            return "No user has been logged in!";
        }
    }

    public function checkUserSession()
    {
        if (isset($_COOKIE["keepLoggedIn"])) {
            $keepLoggedInUsername = htmlspecialchars($_COOKIE["keepLoggedIn"]);
            $loggedInUser = $this->ums->getUserByUsername($keepLoggedInUsername);
            $_SESSION["currentUser"] = $loggedInUser;
            $_SESSION["currentUserIsAdminUser"] = $loggedInUser->isAdmin();
        }

        if (isset($_SESSION["currentUser"])) {
            //echo "user session is set!";
            return "1";
        } else {
            //echo "user session is not set!";
            return "0";
        }
    }

    public function checkUserIsAdmin()
    {
        if (isset($_SESSION["currentUser"])) {
            if ($_SESSION["currentUser"]->isAdmin() == 1) {
                return "1";
            } else {
                return "0";
            }
        } else {
            return "no user logged in";
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

    public function getAllUsers()
    {
        // check if user is admin, else return no user information
        if ($_SESSION["currentUser"]->isAdmin() == 1) {
          return $this->ums->getAllUsers();
        }
        return [];
    }

}