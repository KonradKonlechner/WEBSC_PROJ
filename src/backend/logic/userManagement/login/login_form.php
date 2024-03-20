<?php
    require_once('../User.php');
    session_start(); 
    
    include './login_logic.php';
?>

<!DOCTYPE html>
<html lang="DE">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <title>Login</title>

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
        <?php 
            if (!isset($_SESSION["currentUser"])) {                
                include './login_form_no_active_user.php';
            } else {                               
                include './login_form_logged_in.php';
            }            
        ?>
    </div>
</main>

<footer>
    <?php
        include '../../navigation/footer/footerNav.php';
    ?>
</footer>
</body>
</html>