$(document).ready(function () {
    // reroute to login page if user is already logged in
    rerouteToLoginFormIfUserIsLoggedIn();

    // set clickEvent for registration button
    $("#submit").click(function () {
        registerUser(
            $("#sex").val(),
            $("#name").val(),
            $("#lastname").val(),
            $("#email").val(),
            $("#username").val(),
            $("#password").val(),
            $("#password2").val()
        );
    });
});

function rerouteToLoginFormIfUserIsLoggedIn() {
    $.ajax({
        type: "GET",
        url: "../../../../backend/user/controller/UserController.php",
        cache: false,
        data: {method: "checkUserSession", param: null},
        dataType: "json"
    }).done(function (response) {
        if (response === '1') {
            window.location.replace("../../user/login-view/login-view.html")
        }
    }).fail(function () {
        console.log("Request failed!");
    });
}

function registerUser(
    sex,
    name,
    lastname,
    email,
    username,
    password,
    password2
) {
    const parameter = {
        sex: sex,
        name: name,
        lastname: lastname,
        email: email,
        username: username,
        password: password,
        password2: password2,
    }
    console.log("registering user...");
    $.ajax({
        type: "POST",
        url: "../../../../backend/user/controller/UserController.php",
        cache: false,
        data: {method: "registerUser", param: parameter},
        dataType: "json"
    }).done(function (response) {
        console.log("Request succeeded! Response: " + response);
        alert(response);
    }).fail(function () {
        console.log("Request failed!");
        alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
            "Bitte probieren Sie es später erneut.");
    });
}