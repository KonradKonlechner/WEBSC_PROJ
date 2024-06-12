$(document).ready(function () {
    setTopNavBarLinksIfUserIsLoggedIn();
    setTopNavBarShoppingCartCount();
});


/*
    *
    * shopping cart
    *
 */
export function setTopNavBarShoppingCartCount() {
    $.ajax({
        type: "GET",
        url: "../../../../backend/order/controller/OrderController.php",
        cache: false,
        data: {method: "getSessionShoppingCart", param: null},
        dataType: "json"
    }).done(function (response) {
        console.log("Request succeeded! ShoppingCart - Response: " + response);
        if (response !== "no shoppingCart is set" && response["positions"].length > 0) {
            let count = 0
            const positions = response["positions"];
            positions.forEach(function (position) {
                count += Number(position.quantity);
            });
            const linkText = "(" + count + ") Warenkorb";
            setTopNavBarShoppingCartLink(linkText);
            setShoppingCartHoverEffect(positions);
        } else {
            setTopNavBarShoppingCartLink(" Warenkorb");
            removeAnyHoverEffects()
        }
    }).fail(function () {
        console.log("Request failed!");
        alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
            "Bitte probieren Sie es später erneut.");
    });
}

function setShoppingCartHoverEffect(positions) {
    $("#navbarShoppingCartLink")
        .addClass("popup")
        .append(
            "<div class=\"popuptext\" id=\"myPopup\"> " +
            positions.map(position =>
                "<p>" + (position["product"].name) + ": " + (position.quantity) + "</p>"
            ).join("") +
            "</div>"
        )
        .hover(function () {
            const popup = document.getElementById("myPopup");
            popup.classList.toggle("show");
        });
}

function removeAnyHoverEffects() {
    $("#navbarShoppingCartLink")
        .off("mouseenter")
        .off("mouseleave")
        .children().remove();
}

function setTopNavBarShoppingCartLink(linkText) {
    $("#navbarShoppingCartLink").html("");
    $("#navbarShoppingCartLink").append(
        $("<img/>", {
            id: "shoppingCartIcon",
            src: "../../../res/images/shopping_cart.png",
            alt: "shopping cart"
        }),
        linkText
    );
}


/*
    *
    * actual navbar
    *
 */
function setTopNavBarLinksIfUserIsLoggedIn() {
    console.log("check if user is logged in");

    $.ajax({
        type: "GET",
        url: "../../../../backend/user/controller/UserController.php",
        cache: false,
        data: {method: "checkUserSession", param: null},
        dataType: "json"
    }).done(function (response) {
        console.log("Request succeeded! UserSessionIsSet - Response: " + response);
        if (response === '1') {
            console.log("a user is already logged in!");
            $("#registrationLink").remove();
            setTopNavBarLinksIfUserIsAdmin();
            setLoginLink("Mein Konto");
        } else {
            setRegistrationLink()
            setTopNavBarLinksIfUserIsAdmin();
            setLoginLink("Login");
        }
    }).fail(function () {
        console.log("Request failed!");
    });
}

function setRegistrationLink() {
    $("#registrationLink").remove();

    $("<a/>", {
        id: "registrationLink",
        "href": "../../user/registration-view/registration-view.html",
        text: "Benutzer-Registrierung"
    }).appendTo("#registrationLinkListEntry");
}

function setLoginLink(loginLinkText) {
    $("#loginLink").remove();

    $("<a/>", {
        id: "loginLink",
        class: "btn btn-outline-info mb-2",
        "href": "../../user/login-view/login-view.html",
        text: loginLinkText
    }).appendTo("#loginLinkArea");
}

function setTopNavBarLinksIfUserIsAdmin() {
    console.log("check if user is admin");

    $.ajax({
        type: "GET",
        url: "../../../../backend/user/controller/UserController.php",
        cache: false,
        data: {method: "checkUserIsAdmin", param: null},
        dataType: "json"
    }).done(function (response) {
        console.log("Request succeeded! IsAdmin - Response: " + response);
        if (response === '1') {
            console.log("the user logged in is admin!");
            setAdminLinks()
        } else {
            $("#manageProductsLink").remove();
            $("#manageCustomersLink").remove();
        }
    }).fail(function () {
        console.log("Request failed!");
    });
}

function setAdminLinks() {
    $("#manageProductsLink").remove();

    $("<a/>", {
        id: "manageProductsLink",
        "href": "../../products/product_management-view/product_management-view.html",
        text: "Produkte bearbeiten"
    }).appendTo("#manageProductsLinkListEntry");

    $("#manageCustomersLink").remove();

    $("<a/>", {
        id: "manageCustomersLink",
        "href": "../../user/user_management-view/user_management-view.html",
        text: "Kunden bearbeiten"
    }).appendTo("#manageCustomersLinkListEntry");
}

