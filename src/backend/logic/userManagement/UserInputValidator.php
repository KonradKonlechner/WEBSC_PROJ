<?php

namespace userManagement;
require_once 'User.php';
require_once('UserManagementSystem.php');

class UserInputValidator
{
    private $emailErrMessage;
    private $userNameErrMessage;
    private $pwErrMessage;
    private UserManagementSystem $ums;

    /**
     * @param $ums
     */
    public function __construct($ums)
    {
        $this->ums = $ums;
    }

    /**
     * @return string
     */
    public function getEmailErrMessage()
    {
        return $this->emailErrMessage;
    }

    /**
     * @return string
     */
    public function getUserNameErrMessage()
    {
        return $this->userNameErrMessage;
    }

    /**
     * @return string
     */
    public function getPwErrMessage()
    {
        return $this->pwErrMessage;
    }


    public function isValidEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->emailErrMessage = "Ungültiges E-Mail-Format!";
            return false;
        } else if ($this->ums->isRegisteredEmail($email)) {
            $this->emailErrMessage = "Die E-Mail-Adresse wurde bereits registriert!";
            return false;
        } else {
            return true;
        }
    }

    private function isValidUsername($username)
    {
        if (strlen($username) < 5) {
            $this->userNameErrMessage = "Der Username muss mindestens 5 Zeichen lang sein!";
            return false;
        } else if ($this->ums->isRegisteredUser($username)) {
            $this->userNameErrMessage = "Der Username existiert bereits!";
            return false;
        } else {
            return true;
        }
    }

    public function isValidPassword($password1, $password2)
    {
        if (strlen($password1) < 8) {
            $this->pwErrMessage = "Das Passwort muss mindestens 8 Zeichen lang sein!";
            return false;
        } else if ($password1 !== $password2) {
            $this->pwErrMessage = "Die Passwörter stimmen nicht überein!";
            return false;
        } else {
            return true;
        }
    }

    public function isValidRegistration($email, $username, $password1, $password2)
    {
        return $this->isValidEmail($email) && $this->isValidUsername($username) && $this->isValidPassword($password1, $password2);
    }

    public function prepareInput($data)
    {
        // Hier findet die Aufbereitung des User-inputs für sämtliche Eingabemasken statt
        $sanitizedInput = htmlspecialchars($data);
        $sanitizedInput = trim($sanitizedInput);
        return stripslashes($sanitizedInput);
    }

}