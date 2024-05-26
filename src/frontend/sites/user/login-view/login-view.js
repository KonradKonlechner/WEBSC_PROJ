$(document).ready(function () {

    checkIfUserIsLoggedIn()

    $("#logout").hide();
    $("#userProfile").hide();

    // event handler for clicking on login button
    $("#submit").click(function (e) {
        login($("#username").val(), $("#password").val());
    });
    $("#logout").click(function (e) {
        logout();
    });

});

function checkIfUserIsLoggedIn() {
    console.log("chek if user is logged in");
    $.ajax({
        type: "GET",
        url: "../../../../backend/user/controller/UserController.php",
        cache: false,
        data: {method: "checkUserSession", param: null},
        dataType: "json"
    }).done(function(response) {
        console.log("Request succeeded! Response: " + response);
        if(response == '1') {
            $("#loginLink").html('Profil');
            $("#loginForm").hide();
            $("#logout").show();
            showUserProfile()
        }
    }).fail(function() {
        console.log("Request failed!");
    });
}

function login(username, password) {

    const loginParameter = {
        username: username,
        password: password,
    }

    console.log("Attempting to login");
    $.ajax({
        type: "POST",
        url: "../../../../backend/user/controller/UserController.php",
        cache: false,        
        data: {method: "login", param: loginParameter},
        dataType: "json"      
    }).done(function(response) {
        console.log("Request succeeded! Response: " + JSON.stringify(response));
        if(response != "Login was not successful!") {
            $("#loginLink").html('Profil');
            $("#loginForm").hide();
            $("#logout").show();
            showUserProfile();
        }
    }).fail(function() {
        console.log("Request failed!");         
    });
}

function logout() {

    console.log("logging user out");
    $.ajax({
        type: "POST",
        url: "../../../../backend/user/controller/UserController.php",
        cache: false,
        data: {method: "logout", param: null},
        dataType: "json"
    }).done(function(response) {
        console.log("Request succeeded! Response: " + response);
        $("#loginLink").html('Login');
        $("#loginForm").show();
        $("#logout").hide();
        $("#userProfile").remove();
    }).fail(function() {
        console.log("Request failed!");
    });
}

function showUserProfile() {
    $( "<div/>", {
        id: "userProfile",
        text: "Wilkommen!"
    }).appendTo( "#loginContainer" );
}