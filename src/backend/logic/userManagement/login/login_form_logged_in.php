<?php

use userManagement\User as User;
use userManagement\UserInputValidator;
use userManagement\UserManagementSystem;

require_once('../UserManagementSystem.php');
require_once('../UserInputValidator.php');

$ums = new UserManagementSystem();
$inputValidator = new UserInputValidator($ums);

$currentUser = $_SESSION["currentUser"];

$isUpdateInputValid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["change"])) {

        $enteredSex = $inputValidator->prepareInput($_POST["sex"]);
        $enteredName = $inputValidator->prepareInput($_POST["name"]);
        $enteredLastname = $inputValidator->prepareInput($_POST["lastname"]);
        $enteredEmail = $inputValidator->prepareInput($_POST["email"]);

        if ($enteredEmail != $currentUser->getEmail()) {
            $isValidEmail = $inputValidator->isValidEmail($enteredEmail);
            $emailErrMessage = $inputValidator->getEmailErrMessage();
        } else {
            $isValidEmail = true;
        }

        if ($isValidEmail) {
            $updatedUser = new User();
            $updatedUser->setAllValues(
                $currentUser->getUsername(),
                $currentUser->getPassword(),
                $enteredSex,
                $enteredName,
                $enteredLastname,
                $enteredEmail
            );

            $ums->updateUser($updatedUser);
            $_SESSION['currentUser'] = $updatedUser;
            $currentUser = $updatedUser;
        }
        // ToDo: else { fail }
    }
    if (isset($_POST["changePassword"])) {
        $enteredOldPassword = $inputValidator->prepareInput($_POST["oldPassword"]);

        $enteredPassword1 = $inputValidator->prepareInput($_POST["password"]);
        $enteredPassword2 = $inputValidator->prepareInput($_POST["password2"]);

        $enteredOldPasswordIsValid = $ums->isRegisteredUserWithCorrectPassword($currentUser->getUsername(), $enteredOldPassword);
        $isValidPassword = $inputValidator->isValidPassword($enteredPassword1, $enteredPassword2);
        $pwErrMessage = $inputValidator->getPwErrMessage();

        if ($isValidPassword && $enteredOldPasswordIsValid) {
            // update
            $ums->updateUserPassword($currentUser->getUsername(), $enteredPassword2);
            // show successful modal
            echo "<script type='text/javascript'>
                    $(document).ready(function(){
                    $('#changePasswordSuccessfulModal').modal('show');
                    });
                    </script>";
        } else {
            // keep showing modal
            echo "<script type='text/javascript'>
                    $(document).ready(function(){
                    $('#changePasswordModal').modal('show');
                    });
                    </script>";
        }
    }
}
?>

<div>
    <h1>
        Willkommen
        <?php
        if ($currentUser->getSex() !== "Keine") {
            echo $currentUser->getSex()
                . " "
                . $currentUser->getName()
                . " "
                . $currentUser->getLastname()
                . "!";
        } else {
            echo $currentUser->getName()
                . " "
                . $currentUser->getLastname()
                . "!";
        }

        if ($isUpdateInputValid) {
            echo "<p class=" . "text-danger" . ">Die Änderung wurde übernommen!</p>";
        }
        ?>
    </h1>

    <form method="POST" class="mt-2 mb-3">
        <div class="mt-1">
            <button class="btn btn-danger" type="submit" name="submit" id="submit" value="logout">
                Logout
            </button>
        </div>
    </form>
  
    <div class="mt-1 mb-3">
        <button class="btn btn-primary" onclick="document.location='../../content/reservation/myReservations.php'">Meine Reservierungen</button> 
    </div>
  
    <div class="mb-3">
        <label for="username" class="form-label">Benutzername</label>
        <input type="text" class="form-control" name="username" id="username" disabled value=<?php echo $currentUser->getUsername(); ?>>
        <div id="username" class="form-text">Ihr Benutzername kann nicht geändert werden!</div>
    </div>
  
    <!-- change password -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
        Passwort ändern
    </button>

    <!-- change password modal -->
    <div class="modal fade" id="changePasswordModal" data-bs-keyboard="false"
         tabindex="-1"
         aria-labelledby="passwordChangeLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <form method="POST">

                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="passwordChangeLabel">Passwort ändern</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">


                        <div class="mb-3">

                            <label for="password" class="form-label">Neues Passwort</label>

                            <input type="password" class="form-control <?php if (isset($pwErrMessage)) {
                                echo "is-invalid";
                            } ?>"
                                   name="password" id="password" aria-describedby="passwordHelp" required>

                            <div id="passwordHelp" class="form-text <?php if (isset($pwErrMessage)) {
                                echo "text-danger";
                            } ?>"><?php if (isset($pwErrMessage)) {
                                    echo $pwErrMessage;
                                } ?></div>

                        </div>

                        <div class="mb-3">

                            <label for="password2" class="form-label">Neues Passwort bestätigen</label>

                            <input type="password" class="form-control <?php if (isset($pwErrMessage)) {
                                echo "is-invalid";
                            } ?>"
                                   name="password2" id="password2" aria-describedby="password2Help" required>

                            <div id="password2Help" class="form-text <?php if (isset($pwErrMessage)) {
                                echo "text-danger";
                            } ?>"><?php if (isset($pwErrMessage)) {
                                    echo $pwErrMessage;
                                } else {
                                    echo "Bitte hier das Passwort wiederholen!";
                                } ?></div>

                        </div>

                        <div class="mb-3">

                            <label for="oldPassword" class="form-label">Altes Passwort</label>

                            <input type="password" class="form-control <?php if (isset($enteredOldPassword)) {
                                if (isset($enteredOldPasswordIsValid) && !$enteredOldPasswordIsValid) {
                                    echo "is-invalid";
                                }
                            } ?>" name="oldPassword" id="oldPassword" aria-describedby="oldPasswordHelp" required>
                            <div id="oldPasswordHelp" class="form-text <?php if (isset($enteredOldPassword)) {
                                if (isset($enteredOldPasswordIsValid) && !$enteredOldPasswordIsValid) {
                                    echo "text-danger";
                                }
                            } ?>"><?php if (isset($enteredOldPassword)) {
                                    if (isset($enteredOldPasswordIsValid) && !$enteredOldPasswordIsValid) {
                                        echo "Falsches Passwort!";
                                    } else {
                                        echo "Bitte das aktuelle Passwort zur Bestätigung eingeben!";
                                    }
                                } ?></div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
                        <button type="submit" class="btn btn-primary" name="changePassword" id="changePassword">
                            Passwort ändern
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- change password successful modal -->
    <div class="modal fade" id="changePasswordSuccessfulModal" data-bs-keyboard="false"
         tabindex="-1"
         aria-labelledby="passwordChangeSuccessful" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="passwordChangeSuccessful">Passwortänderung erfolgreich!</h1>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
                </div>
            </div>
        </div>
    </div>

    <form class="col-3" method="POST">

        <div class="mb-3">
            <label for="sex" class="form-label">Anrede</label>
            <select name="sex" id="sex" class="form-select">
                <option <?php if ($currentUser->getSex() === "Keine") {
                    echo "selected";
                } ?> value="Keine">Keine
                </option>
                <option <?php if ($currentUser->getSex() === "Frau") {
                    echo "selected";
                } ?> value="Frau">Frau
                </option>
                <option <?php if ($currentUser->getSex() === "Herr") {
                    echo "selected";
                } ?> value="Herr">Herr
                </option>
            </select>
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">Vorname</label>
            <input type="text" class="form-control" name="name" id="name" required
                   value=<?php if (isset($enteredName)) {
                echo $enteredName;
            } else {
                echo $currentUser->getName();
            } ?>>
        </div>
        <div class="mb-3">
            <label for="lastname" class="form-label">Nachname</label>
            <input type="text" class="form-control" name="lastname" id="lastname" required
                   value=<?php if (isset($enteredLastname)) {
                echo $enteredLastname;
            } else {
                echo $currentUser->getLastname();
            } ?>>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control <?php if (isset($emailErrMessage)) {
                echo "is-invalid";
            } ?>" name="email" id="email" aria-describedby="emailHelp" required
                   value=<?php if (isset($enteredEmail)) {
                echo $enteredEmail;
            } else {
                echo $currentUser->getEmail();
            } ?>>
            <div id="emailHelp" class="form-text <?php if (isset($emailErrMessage)) {
                echo "text-danger";
            } ?>"><?php if (isset($emailErrMessage)) {
                    echo $emailErrMessage;
                } else {
                    echo "Wir werden Ihre E-Mailadresse niemals an Dritte weitergeben!";
                } ?></div>
        </div>

        <button class="btn btn-success mb-3" type="submit" name="change" id="change">Ändern</button>
    </form>

</div>


