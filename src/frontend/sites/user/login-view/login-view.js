$(document).ready(function () {

    setLoginFormIfUserIsLoggedIn()

    // event handler for clicking on login button
    $("#submit").click(function () {
        var keepLogin = $("#keepLogin").prop("checked");
        console.log("Login merken: " + keepLogin);
        login($("#username").val(), $("#password").val(), keepLogin);
    });
    // event handler for clicking on logout button
    $("#logout").click(function () {
        logout();
    });

    // event handler for clicking on update user profile button
    $("#updateUserProfile").click(function () {

        console.log("new password: test"); // + $("#passwordNew").val());
        /*
        updateUserProfile(
            $("#username").val(),
            $("#sex").val(),
            $("#name").val(),
            $("#lastname").val(),
            $("#address").val(),
            $("#postal_code").val(),
            $("#city").val(),
            $("#email").val(),
            $("#passwordNew").val(),
            $("#passwordNew2").val(),
            $("#password").html()
            );
         */
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
        if(response === '1') {
            //console.log("a user is already logged in!");
            setLoginForm(true);
        } else {
            setLoginForm(false);
        }
    }).fail(function() {
        console.log("Request failed!");
    });
}

function login(username, password, keepLogin) {

    const loginParameter = {
        username: username,
        password: password,
        keepLogin: keepLogin
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

function updateUserProfile(username, sex, name, lastname, address, postal_code, city, email, passwordNew, passwordNew2, password) {

    console.log("updating user profile...");

    if(passwordNew != passwordNew2) {
        console.log("new passwords do not match!");
        $("#passwordNew").addClass("is-invalid");
        $("#passwordNew2").addClass("is-invalid");
        return;
    }

    const updateUserProfileParameter = {
        username: username,
        sex: sex,
        name: name,
        lastname: lastname,
        address: address,
        postal_code: postal_code,
        city: city,
        email: email,
        passwordNew: passwordNew,
        password: password
    }

    console.log("Attempting to login");
    $.ajax({
        type: "POST",
        url: "../../../../backend/user/controller/UserController.php",
        cache: false,
        data: {method: "updateUserProfile", param: updateUserProfileParameter},
        dataType: "json"
    }).done(function(response) {
        console.log("Request succeeded! Response: " + response);
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
        $("#profileContainer").hide();
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

    var sex = currentUser["sex"];
    var firstname = currentUser["name"];
    var lastname = currentUser["lastname"];
    var address = currentUser["address"];
    var postal_code = currentUser["postal_code"];
    var city = currentUser["city"];
    var email = currentUser["email"];
    var username = currentUser["username"];

    $("#profileContainer").show();

    $("#userProfileGreeting").remove();

    $("<div/>", {
        id: "userProfileGreeting",
        class: "mb-3",
        text: "Wilkommen " + sex + " " + firstname + " " + lastname + "!"
    }).append($("<p/>", {
        text: "Hier können Sie Ihr Profil einsehen und ändern."
    })).appendTo("#profileContainer");

    $("#userSex").remove();

    $("<div/>", {
        id: "userSex",
        class: "mb-3"
    }).append(
        $("<label/>", {
            htmlFor: "sex",
            class: "form-label",
            text: "Anrede"
        }),
        $("<select/>", {
            name: "sex",
            id: "sex",
            class: "form-select",
        }).append(
            $("<option/>", {
                text: "Keine",
                selected: (sex == "Keine")
            }),
            $("<option/>", {
                text: "Frau",
                selected: (sex == "Frau")
            }),
            $("<option/>", {
                text: "Herr",
                selected: (sex == "Herr")
            })
        )
    ).appendTo("#profileContainer");

    $("#userFirstName").remove();

    $("<div/>", {
        id: "userFirstName",
        class: "mb-3"
    }).append(
        $("<label/>", {
            htmlFor: "name",
            class: "form-label",
            text: "Vorname"
        }),
        $("<input/>", {
            type: "text",
            id: "name",
            class: "form-control",
            name: "name",
            value: firstname,
            required: true
        })
    ).appendTo("#profileContainer");

    $("#userLastName").remove();

    $("<div/>", {
        id: "userLastName",
        class: "mb-3"
    }).append(
        $("<label/>", {
            htmlFor: "lastname",
            class: "form-label",
            text: "Nachname"
        }),
        $("<input/>", {
            type: "text",
            id: "lastname",
            class: "form-control",
            name: "lastname",
            value: lastname,
            required: true
        })
    ).appendTo("#profileContainer");

    $("#userAddress").remove();

    $("<div/>", {
        id: "userAddress",
        class: "mb-3"
    }).append(
        $("<label/>", {
            htmlFor: "address",
            class: "form-label",
            text: "Adresse"
        }),
        $("<input/>", {
            type: "text",
            id: "address",
            class: "form-control",
            name: "address",
            value: address,
            required: true
        })
    ).appendTo("#profileContainer");

    $("#userPostalCode").remove();

    $("<div/>", {
        id: "userPostalCode",
        class: "mb-3"
    }).append(
        $("<label/>", {
            htmlFor: "postal_code",
            class: "form-label",
            text: "Postleitzahl"
        }),
        $("<input/>", {
            type: "text",
            id: "postal_code",
            class: "form-control",
            name: "postalCode",
            value: postal_code,
            required: true
        })
    ).appendTo("#profileContainer");

    $("#userCity").remove();

    $("<div/>", {
        id: "userCity",
        class: "mb-3"
    }).append(
        $("<label/>", {
            htmlFor: "city",
            class: "form-label",
            text: "Ort"
        }),
        $("<input/>", {
            type: "text",
            id: "city",
            class: "form-control",
            name: "city",
            value: city,
            required: true
        })
    ).appendTo("#profileContainer");

    $("#userName").remove();

    $("<div/>", {
        id: "userName",
        class: "mb-3"
    }).append(
        $("<label/>", {
            htmlFor: "username",
            class: "form-label",
            text: "Benutzername"
        }),
        $("<input/>", {
            type: "text",
            id: "username",
            class: "form-control",
            name: "username",
            "aria-describedby": "usernameHelp",
            value: username,
            readonly: true
        }),
        $("<div/>", {
            id: "usernameHelp",
            class: "form-text"
        })
    ).appendTo("#profileContainer");

    $("#userEmail").remove();

    $("<div/>", {
        id: "userEmail",
        class: "mb-3"
    }).append(
        $("<label/>", {
            htmlFor: "email",
            class: "form-label",
            text: "Email"
        }),
        $("<input/>", {
            type: "email",
            id: "email",
            class: "form-control",
            name: "email",
            "aria-describedby": "emailHelp",
            value: email,
            required: true
        }),
        $("<div/>", {
            id: "emailHelp",
            class: "form-text"
        })
    ).appendTo("#profileContainer");

    $("#userPasswordNew").remove();

    $("<div/>", {
        id: "userPasswordNew",
        class: "mb-3"
    }).append(
        $("<label/>", {
            htmlFor: "passwordNew",
            class: "form-label",
            text: "Neues Passwort"
        }),
        $("<input/>", {
            type: "password",
            id: "passwordNew",
            class: "form-control",
            name: "passwordNew",
            "aria-describedby": "passwordNewHelp",
            value: null,
            required: false
        }),
        $("<div/>", {
            id: "passwordNewHelp",
            class: "form-text"
        })
    ).appendTo("#profileContainer");

    $("#userPasswordNew2").remove();

    $("<div/>", {
        id: "userPasswordNew2",
        class: "mb-3"
    }).append(
        $("<label/>", {
            htmlFor: "passwordNew2",
            class: "form-label",
            text: "Neues Passwort wiederholen"
        }),
        $("<input/>", {
            type: "password",
            id: "passwordNew2",
            class: "form-control",
            name: "passwordNew2",
            "aria-describedby": "passwordNew2Help",
            value: null,
            required: false
        }),
        $("<div/>", {
            id: "passwordNew2Help",
            class: "form-text"
        })
    ).appendTo("#profileContainer");

    $("#userPassword").remove();

    $("<div/>", {
        id: "userPassword",
        class: "mb-3"
    }).append(
        $("<label/>", {
            htmlFor: "password",
            class: "form-label",
            text: "Aktuelles Passwort"
        }),
        $("<input/>", {
            type: "password",
            id: "password",
            class: "form-control",
            name: "password",
            "aria-describedby": "passwordHelp",
            value: null,
            required: true
        }),
        $("<div/>", {
            id: "passwordHelp",
            class: "form-text"
        })
    ).appendTo("#profileContainer");

    $("#updateUserProfile").remove();

    $("<button/>", {
        id: "updateUserProfile",
        class: "btn btn-success",
        type: "submit",
        name: "updateUserProfile",
        text: "Änderungen übernehmen"
    }).appendTo("#profileContainer");

}