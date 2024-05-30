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
            #$username, $password, $sex, $name, $lastname, $address, $postal_code, $city, $email
            $user->setAllValues(
                $enteredUsername,
                $enteredPassword,
                $enteredSex,
                $enteredName,
                $enteredLastName,
                "",
                "",
                "",
                $enteredEmail
            );

            $this->ums->saveUserAsRegistered($user);

            $this->loginWithParameters($param);
            return "Registrierung erfolgreich!";
        }
        return "User already logged in. Can't perform registration.";
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

}