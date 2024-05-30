setTopNavBarLinksIfUserIsLoggedIn();

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