$(document).ready(function () {

    setLoginFormIfUserIsLoggedIn()

    // event handler for clicking on login button
    $("#submit").click(function (e) {
        login($("#username").val(), $("#password").val());
    });
    // event handler for clicking on logout button
    $("#logout").click(function (e) {
        logout();
    });
});

function setLoginFormIfUserIsLoggedIn() {
    //console.log("check if user is logged in");
    $.ajax({
        type: "GET",
        url: "../../../../backend/user/controller/UserController.php",
        cache: false,
        data: {method: "checkUserSession", param: null},
        dataType: "json"
    }).done(function(response) {
        //console.log("Request succeeded! Response: " + response);
        if(response == '1') {
            //console.log("a user is already logged in!");
            setLoginForm(true);
        } else {
            setLoginForm(false);
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
        setLoginFormIfUserIsLoggedIn()
        setTopNavBarLinksIfUserIsLoggedIn();
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
        setLoginFormIfUserIsLoggedIn()
        setTopNavBarLinksIfUserIsLoggedIn();
    }).fail(function() {
        console.log("Request failed!");
    });
}

function setLoginForm(userIsLoggedIn) {

    if(userIsLoggedIn) {
        $("#loginForm").hide();
        $("#noProfile").hide();
        $("#logout").show();
        showProfileOfCurrentUser()
    } else {
        $("#loginForm").show();
        $("#noProfile").show();
        $("#logout").hide();
        $("#userProfile").remove();
    }
}

function showProfileOfCurrentUser() {
    console.log("show profile of current user");
    $.ajax({
        type: "GET",
        url: "../../../../backend/user/controller/UserController.php",
        cache: false,
        data: {method: "getCurrentUser", param: null},
        dataType: "json"
    }).done(function(response) {
        //console.log("Request succeeded! Response: " + JSON.stringify(response));
        showUserProfile(response)
    }).fail(function() {
        console.log("Request failed!");
    });
}

function showUserProfile(currentUser) {
    $("#userProfile").remove();

    $( "<div/>", {
        id: "userProfile",
        text: "Wilkommen " + currentUser["sex"] + " " + currentUser["name"] + " " + currentUser["lastname"] + "!"
    }).appendTo( "#loginContainer" );
}