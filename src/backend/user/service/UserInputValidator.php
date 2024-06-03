<?php

namespace user;
require_once '../model/User.php';
require_once('../persistence/UserManagementSystem.php');

class UserInputValidator
{
    private UserManagementSystem $ums;

    /**
     * @param $ums
     */
    public function __construct($ums)
    {
        $this->ums = $ums;
    }

    public function isValidEmail($email): string
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Ungültiges E-Mail-Format!";
        }
        if ($this->ums->isRegisteredEmail($email)) {
            return "Die E-Mail-Adresse wurde bereits registriert!";
        }
        return "";
    }

    private function isValidUsername($username): string
    {
        if (strlen($username) < 5) {
            return "Der Username muss mindestens 5 Zeichen lang sein!";
        } else if ($this->ums->isRegisteredUser($username)) {
            return "Der Username existiert bereits!";
        }
        return "";
    }

    public function isValidPassword($password1, $password2): string
    {
        if (strlen($password1) < 8) {
            return "Das Passwort muss mindestens 8 Zeichen lang sein!";
        } else if ($password1 !== $password2) {
            return "Die Passwörter stimmen nicht überein!";
        }
        return "";
    }

    public function isValidRegistration($email, $username, $password1, $password2): array
    {
        $emailError = $this->isValidEmail($email);
        $usernameError = $this->isValidUsername($username);
        $passwordError = $this->isValidPassword($password1, $password2);

        return [
            $emailError,
            $usernameError,
            $passwordError,
        ];
    }

    public function arrayContainsErrors(array $errors): bool
    {
        $errors = array_filter($errors, function ($error) {
            return $error !== "";
        });
        return count($errors) > 0;
    }

    public function prepareInput($data)
    {
        // Hier findet die Aufbereitung des User-inputs für sämtliche Eingabemasken statt
        $sanitizedInput = htmlspecialchars($data);
        $sanitizedInput = trim($sanitizedInput);
        return stripslashes($sanitizedInput);
    }

}