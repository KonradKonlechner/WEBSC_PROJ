<?php
    // Start or continue a session
    session_start();
    include './registration_logic.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>

    <title>Registrierung</title>
    <link rel="stylesheet" href="../../styles.css"/>
</head>
<body class="bgColor bg-gradient">
<header>
    <?php
        include '../../navigation/navbar/topNavBar.php';
    ?>
</header>

<main>
    <div class="container justify-content-left">
        <h1>Benutzer-Registrierung</h1>

        <?php if($isValidRegistration === false) : ?>
            <form class="col-3 mb-3" method="POST">
                <div class="mb-3">
                    <label for="sex" class="form-label">Anrede</label>
                    <select name="sex" id="sex" class="form-select">
                        <?php if(!isset($enteredSex)): ?>
                            <option selected value="Keine">Keine</option>                                
                            <option value="Frau">Frau</option>
                            <option value="Herr">Herr</option>
                        <?php else: ?>
                            <option <?php if($enteredSex==="Keine") {echo "selected";} ?> value="Keine">Keine</option>                                
                            <option <?php if($enteredSex==="Frau") {echo "selected";} ?> value="Frau">Frau</option>
                            <option <?php if($enteredSex==="Herr") {echo "selected";} ?> value="Herr">Herr</option>           
                        <?php endif;?>     
                    </select>
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">Vorname</label>
                    <input type="text" class="form-control" name="name" id="name" <?php if(isset($enteredName)) { echo "value=" . $enteredName;} ?> required>
                </div>
                <div class="mb-3">
                    <label for="lastname" class="form-label">Nachname</label>
                    <input type="text" class="form-control" name="lastname" id="lastname" <?php if(isset($enteredLastname)) { echo "value=" . $enteredLastname;} ?> required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control <?php if(isset($emailErrMessage)) {echo "is-invalid";}?>" name="email" id="email" aria-describedby="emailHelp" required <?php if(isset($enteredEmail)) { echo "value=" . $enteredEmail;} ?>>
                    <div id="emailHelp" class="form-text <?php if(isset($emailErrMessage)) {echo "text-danger";}?>"><?php if(isset($emailErrMessage)) { echo $emailErrMessage; } else { echo "Wir werden Ihre E-Mailadresse niemals an Dritte weitergeben!";} ?></div>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Benutzername</label>
                    <input type="text" class="form-control <?php if(isset($userNameErrMessage)) {echo "is-invalid";}?>" name="username" id="username" aria-describedby="usernameHelp" required <?php if(isset($enteredUsername)) { echo "value=" . $enteredUsername;} ?>>
                    <div id="usernameHelp" class="form-text <?php if(isset($userNameErrMessage)) {echo "text-danger";}?>"><?php if(isset($userNameErrMessage)) { echo $userNameErrMessage; } ?></div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Passwort</label>
                    <input type="password" class="form-control <?php if(isset($pwErrMessage)) {echo "is-invalid";}?>" name="password" id="password" aria-describedby="passwordHelp" required>
                    <div id="passwordHelp" class="form-text <?php if(isset($pwErrMessage)) {echo "text-danger";}?>"><?php if(isset($pwErrMessage)) { echo $pwErrMessage;} ?></div>
                </div>
                <div class="mb-3">
                    <label for="password2" class="form-label">Passwort bestätigen</label>
                    <input type="password" class="form-control <?php if(isset($pwErrMessage)) {echo "is-invalid";}?>" name="password2" id="password2" aria-describedby="password2Help" required>
                    <div id="password2Help" class="form-text <?php if(isset($pwErrMessage)) {echo "text-danger";}?>"><?php if(isset($pwErrMessage)) { echo $pwErrMessage;} else {echo "Bitte hier das Passwort wiederholen!";} ?></div>
                </div>
                <button class="btn btn-success" type="submit" name="submit" id="submit">Registrieren</button>
            </form>
        <?php else: ?>
            <h2>Vielen Dank! Die Registrierung war erfolgreich! Sie erhalten in Kürze eine E-Mail zur Bestätigung.</h2>                        
        <?php endif; ?>
    </div>
</main>

<footer>
    <?php
        include '../../navigation/footer/footerNav.php';
    ?>
</footer>
</body>
</html>