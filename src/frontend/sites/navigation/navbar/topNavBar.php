<?php
    include ('../../../../backend/user/model/User.php');
    session_start();
?>
<nav class="navbar fixed-top navbar-expand-lg navBarColor bg-gradient">
    <div class="container justify-content-left" id="nav-mother-container">
        <div class="navbar d-flex flex-column navbar-brand">
            <a class="p-1" id="homeLink" href="../../index.html">
                <div>
                    <img src="../../../res/images/paw_print.png" alt="paw print">
                </div>
                <div>
                    PawsomeMart
                </div>
            </a>
        </div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-3 nav-main-menu-item-list">
                <li id="registrationLinkListEntry" class='nav-item px-2'>
                </li>
                <li class="nav-item px-2">
                    <a href="../../products/product-view/product-view.html">Produkte</a>
                </li>
                <li class="nav-item px-2">
                    <a id="navbarShoppingCartLink" href="../../products/shopping_cart-view/shopping_cart-view.html">
                    </a>
                </li>
                <li id="manageProductsLinkListEntry" class="nav-item px-2">
                </li>
                <li id="manageCustomersLinkListEntry" class="nav-item px-2">
                </li>
                <!--<li class="nav-item px-2">
                    <a href="../../">Gutscheine verwalten</a>
                </li>-->
            </ul>
            <div id="loginLinkArea" class="nav-item d-flex justify-content-end bg-info-subtle bg-opacity-25 rounded">
               <!-- <a class="btn btn-outline-info mb-2" id="loginLink" href="../../user/login-view/login-view.html">Login</a> -->
            </div>
        </div>
    </div>
</nav>

<script src="../../navigation/navbar/topNavBar_logic.js"></script>