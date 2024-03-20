<?php
// Start or continue a session
require_once('../User.php');
session_start();

use userManagement\User;
use userManagement\UserInputValidator;
use userManagement\UserManagementSystem;

require_once('../UserManagementSystem.php');
require_once('../UserInputValidator.php');

$ums = new UserManagementSystem();
$inputValidator = new UserInputValidator($ums);

User:
$currentUser = $_SESSION["currentUser"];

if (!$currentUser->isAdmin()) {
    header("Location: ../../content/welcomepage/welcome.php");
    exit();
}

$allUsers = $ums->getAllUsers();
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
              integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
              crossorigin="anonymous">
        <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
                crossorigin="anonymous"></script>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <title>Benutzer verwalten</title>
        <link rel="stylesheet" href="../../styles.css"/>
        <link rel="stylesheet" href="../../styles/user-management.css"/>
    </head>
    <body class="bgColor bg-gradient">
    <header>
        <?php
        include '../../navigation/navbar/topNavBar.php';
        ?>
    </header>

    <main>
        <div class="container justify-content-left">
            <h1>User Management</h1>
            <h2>Hier können User-Daten erfasst und aktualisiert werden.</h2>

            <ul class="userDataList">
                <?php
                foreach ($allUsers as $user) {
                    ?>

                    <li class="userListItem">
                        <div class="userDataContainer">

                            <h5>
                                <?php echo $user->getUsername() ?>
                            </h5>

                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target=<?php echo "#changeUserData" . $user->getUsername() ?>>
                                Benutzerdaten ändern
                            </button>

                            <div class="modal fade"
                                 id=<?php echo "changeUserData" . $user->getUsername() ?> data-bs-keyboard="false"
                                 tabindex="-1"
                                 aria-labelledby="userChangeLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">

                                        <form method="POST">

                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5"> Benutzerdaten ändern </h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                <div class="mb-3">
                                                    <label for="sex" class="form-label">Anrede</label>
                                                    <select name="sex" id="sex" class="form-select">
                                                        <option <?php if ($user->getSex() === "Keine") {
                                                            echo "selected";
                                                        } ?> value="Keine">Keine
                                                        </option>
                                                        <option <?php if ($user->getSex() === "Frau") {
                                                            echo "selected";
                                                        } ?> value="Frau">Frau
                                                        </option>
                                                        <option <?php if ($user->getSex() === "Herr") {
                                                            echo "selected";
                                                        } ?> value="Herr">Herr
                                                        </option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Vorname</label>
                                                    <input type="text" class="form-control" name="name" id="name"
                                                           required
                                                           value=<?php if (isset($enteredName)) {
                                                        echo $enteredName;
                                                    } else {
                                                        echo $user->getName();
                                                    } ?>>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="lastname" class="form-label">Nachname</label>
                                                    <input type="text" class="form-control" name="lastname"
                                                           id="lastname"
                                                           required
                                                           value=<?php if (isset($enteredLastname)) {
                                                        echo $enteredLastname;
                                                    } else {
                                                        echo $user->getLastname();
                                                    } ?>>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email"
                                                           class="form-control <?php if (isset($emailErrMessage)) {
                                                               echo "is-invalid";
                                                           } ?>" name="email" id="email" aria-describedby="emailHelp"
                                                           required
                                                           value=<?php if (isset($enteredEmail)) {
                                                        echo $enteredEmail;
                                                    } else {
                                                        echo $user->getEmail();
                                                    } ?>>
                                                    <div id="emailHelp"
                                                         class="form-text <?php if (isset($emailErrMessage)) {
                                                             echo "text-danger";
                                                         } ?>"><?php if (isset($emailErrMessage)) {
                                                            echo $emailErrMessage;
                                                        } ?></div>
                                                </div>

                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="isActive"
                                                        <?php if (!$user->isInactive()) echo "checked" ?>
                                                           name="activeUser" id="activeUser">
                                                    <label class="form-check-label" for="flexCheckChecked">
                                                        Aktiver Nutzer
                                                    </label>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Schließen
                                                </button>
                                                <button type="submit" class="btn btn-primary" name="changeUser"
                                                        id="changeUser" value="<?php echo $user->getUsername() ?>">
                                                    Benutzerdaten ändern
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>


                            <!-- change password -->
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="<?php echo "#changePasswordModal" . $user->getUsername() ?>">
                                Passwort ändern
                            </button>

                            <!-- change password modal -->
                            <div class="modal fade" id="<?php echo "changePasswordModal" . $user->getUsername() ?>"
                                 data-bs-keyboard="false"
                                 tabindex="-1"
                                 aria-labelledby="passwordChangeLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">

                                        <form method="POST">

                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="passwordChangeLabel">Passwort
                                                    ändern</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">


                                                <div class="mb-3">

                                                    <label for="password" class="form-label">Neues Passwort</label>

                                                    <input type="password"
                                                           class="form-control <?php if (isset($pwErrMessage)) {
                                                               echo "is-invalid";
                                                           } ?>"
                                                           name="password" id="password" aria-describedby="passwordHelp"
                                                           required>

                                                    <div id="passwordHelp"
                                                         class="form-text <?php if (isset($pwErrMessage)) {
                                                             echo "text-danger";
                                                         } ?>"><?php if (isset($pwErrMessage)) {
                                                            echo $pwErrMessage;
                                                        } ?></div>

                                                </div>

                                                <div class="mb-3">

                                                    <label for="password2" class="form-label">Neues Passwort
                                                        bestätigen</label>

                                                    <input type="password"
                                                           class="form-control <?php if (isset($pwErrMessage)) {
                                                               echo "is-invalid";
                                                           } ?>"
                                                           name="password2" id="password2"
                                                           aria-describedby="password2Help"
                                                           required>

                                                    <div id="password2Help"
                                                         class="form-text <?php if (isset($pwErrMessage)) {
                                                             echo "text-danger";
                                                         } ?>"><?php if (isset($pwErrMessage)) {
                                                            echo $pwErrMessage;
                                                        } else {
                                                            echo "Bitte hier das Passwort wiederholen!";
                                                        } ?></div>

                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Schließen
                                                </button>
                                                <button type="submit" class="btn btn-primary" name="changePassword"
                                                        id="changePassword" value="<?php echo $user->getUsername() ?>">
                                                    Passwort ändern
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="changeSuccessfulModal" data-bs-keyboard="false"
                                 tabindex="-1"
                                 aria-labelledby="changeSuccessful" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="changeSuccessful">Änderungen
                                                erfolgreich!</h1>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                Schließen
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
    </main>

    <footer>
        <?php
        include '../../navigation/footer/footerNav.php';
        ?>
    </footer>
    </body>
    </html>


<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["changeUser"])) {

        $userToUpdate = $ums->getUserByUsername($_POST["changeUser"]);

        $enteredSex = $inputValidator->prepareInput($_POST["sex"]);
        $enteredName = $inputValidator->prepareInput($_POST["name"]);
        $enteredLastname = $inputValidator->prepareInput($_POST["lastname"]);
        $enteredEmail = $inputValidator->prepareInput($_POST["email"]);
        $userActiveCheckBox = isset($_POST["activeUser"]);

        if ($enteredEmail != $userToUpdate->getEmail()) {
            $isValidEmail = $inputValidator->isValidEmail($enteredEmail);
            $emailErrMessage = $inputValidator->getEmailErrMessage();
        } else {
            $isValidEmail = true;
        }

        if ($isValidEmail) {
            $updatedUser = User::of(
                $userToUpdate->getUserId(),
                $userToUpdate->getUsername(),
                $userToUpdate->getPassword(),
                $enteredSex,
                $enteredName,
                $enteredLastname,
                $enteredEmail,
                $userToUpdate->isAdmin(),
                !$userActiveCheckBox
            );

            $ums->updateUser($updatedUser);
            $allUsers = $ums->getAllUsers();
            // show successful modal
            echo "<script type='text/javascript'>
                    $(document).ready(function(){
                    $('#changeSuccessfulModal').modal('show');
                    });
                    </script>";

        } else {
            // keep showing modal
            $modalId = "changeUserData" . $_POST["changeUser"];
            echo "<script type='text/javascript'>
                    $(document).ready(function(){
                    $(" . $modalId . ").modal('show');
                    });
                    </script>";
        }
    }
    if (isset($_POST["changePassword"])) {

        $enteredPassword1 = $inputValidator->prepareInput($_POST["password"]);
        $enteredPassword2 = $inputValidator->prepareInput($_POST["password2"]);

        $isValidPassword = $inputValidator->isValidPassword($enteredPassword1, $enteredPassword2);
        $pwErrMessage = $inputValidator->getPwErrMessage();

        if ($isValidPassword) {
            // update
            $ums->updateUserPassword($_POST["changePassword"], $enteredPassword2);
            // show successful modal
            echo "<script type='text/javascript'>
                    $(document).ready(function(){
                    $('#changeSuccessfulModal').modal('show');
                    });
                    </script>";
        } else {
            // keep showing modal
            $modalId = "changePasswordModal" . $_POST["changePassword"];
            echo "<script type='text/javascript'>
                    $(document).ready(function(){
                    $(" . $modalId . ").modal('show');
                    });
                    </script>";
        }
    }
}