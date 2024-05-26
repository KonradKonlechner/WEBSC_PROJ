<?php
include ('../../../../backend/user/model/User.php');
session_start();
/*if (isset($_SESSION["currentUser"]))
{
    $currentUser = $_SESSION["currentUser"]->getUsername();
    echo "Aktuell eingeloggter User: $currentUser";
}*/
?>
<nav class="navbar fixed-top navbar-expand-lg navBarColor bg-gradient">
    <div class="container justify-content-left" id="nav-mother-container">
        <div class="navbar d-flex flex-column navbar-brand">
            <a class="p-1" id="homeLink" href="../../index.html">
                <div>
                    <img src="../../../res/images/paw_print.png" alt="paw print">
                </div>
                <div>
                    Pawsomemart
                </div>
            </a>
        </div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-3 nav-main-menu-item-list">
                <?php
                    if (!isset($_SESSION["currentUser"])) {
                        echo "
                            <li class='nav-item px-2'>
                                <a href='../../userManagement/registration/registration_form.php'>Benutzer-Registrierung</a>
                            </li>
                            ";
                    }
                ?>
                <li class="nav-item px-2">
                    <a href="../../content/news/news_form.php">Neuigkeiten</a>
                </li>
                <li class="nav-item px-2">
                    <a href="../../content/information/rooms.php">Zimmer</a>
                </li>
                <li class="nav-item px-2">
                    <a href="../../content/reservation/roomReservation.php">Reservierung</a>
                </li>
                <!--<li class="nav-item px-2">
                    <a href="../../content/information/coupons.php">Gutscheine</a>
                </li>-->
                <?php
                    if (isset($_SESSION["currentUser"])
                        && isset($_SESSION["currentUserIsAdminUser"])
                        && $_SESSION["currentUserIsAdminUser"]):
                ?>
                        <li class="nav-item px-2">
                            <a href="../../userManagement/admin_usermanagement/admin_usermanagement.php">Benutzer verwalten</a>
                        </li>
                        <li class="nav-item px-2">
                            <a href="../../reservationManagement/admin_reservationManagement/admin_reservation_management.php">Reservierungen verwalten</a>
                        </li>
                <?php endif; ?>
            </ul>
            <div class="nav-item d-flex justify-content-end bg-info-subtle bg-opacity-25 rounded">
                <a class="btn btn-outline-info mb-2" id="loginLink" href="../../user/login-view/login-view.html">Login</a>
            </div>
        </div>
    </div>
</nav>