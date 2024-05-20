<?php


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "<script>console.log(' Logging out... ' );</script>";
    session_unset();
    setcookie('LOGON_USER', '', -1, '/');
    echo "<script>console.log(' Session has been closed ' );</script>";
}

