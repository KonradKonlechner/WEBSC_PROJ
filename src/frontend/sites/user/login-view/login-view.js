import {setTopNavBarLinksIfUserIsLoggedIn} from "../../navigation/navbar/topNavBar_logic.js";

$(document).ready(function () {

    setLoginFormIfUserIsLoggedIn()
});

function setLoginFormIfUserIsLoggedIn() {
    setTopNavBarLinksIfUserIsLoggedIn();
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
        if(response === "Login was not successful!") { // specific error case, login Data incorrect

            $("#username").addClass("is-invalid");
            $("#usernameHelp").addClass("text-danger");
            $("#usernameHelp").html("Username oder Passwort falsch!");
            $("#password").addClass("is-invalid");
            $("#passwordHelp").addClass("text-danger");
            $("#passwordHelp").html("Username oder Passwort falsch!");

        } else if (typeof response === 'string') { // any other failure case
            alert(response)
        } else { // success case
            setLoginFormIfUserIsLoggedIn();
        }
    }).fail(function() {
        console.log("Request failed!");
        alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
            "Bitte probieren Sie es später erneut.");
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
    }).fail(function() {
        console.log("Request failed!");
        alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
            "Bitte probieren Sie es später erneut.");
    });
}

function setLoginForm(userIsLoggedIn) {

    if(userIsLoggedIn) {
        $("#loginForm").remove();
        $("#noProfile").hide();
        showLogoutButton();
        showProfileOfCurrentUser();
    } else {
        $("#logoutBtn").remove();
        $("#updateProfileForm").remove();
        showLoginForm()
        $("#noProfile").show();
    }
}

function showLoginForm() {

    $("#loginForm").remove();

    $("<div/>", {
        id: "loginForm",
        class: "mb-3",
        method: null
    }).appendTo("#loginContainer");

    $("#userAccountName").remove();

    $("<div/>", {
        id: "userAccountName",
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
            value: null,
            required: true
        }),
        $("<div/>", {
            id: "usernameHelp",
            class: "form-text"
        })
    ).appendTo("#loginForm");

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
    ).appendTo("#loginForm");

    $("#userKeepLogin").remove();

    $("<div/>", {
        id: "userKeepLogin",
        class: "form-check mb-3"
    }).append(
        $("<label/>", {
            htmlFor: "keepLogin",
            class: "form-check-label",
            text: "Login merken"
        }),
        $("<input/>", {
            type: "checkbox",
            id: "keepLogin",
            class: "form-check-input",
            name: "keepLogin",
            value: "keep login data"
        })
    ).appendTo("#loginForm");

    $("#loginAndResetBtn").remove();

    $("<div/>", {
        id: "loginAndResetBtn",
        class: "mb-3"
    }).append(
        $("<button/>", {
            id: "reset",
            class: "btn btn-danger",
            type: "reset",
            name: "reset",
            text: "Reset"
        }),
        $("<button/>", {
            id: "login",
            class: "btn btn-success ms-2",
            type: "submit",
            name: "login",
            text: "Login"
        }),
    ).appendTo("#loginForm");

    // event handler for clicking on login button
    $("#login").click(function () {
        var keepLogin = $("#keepLogin").prop("checked");
        console.log("Login merken: " + keepLogin);
        login($("#username").val(), $("#password").val(), keepLogin);
    });
}

function showLogoutButton() {
    $("#logoutBtn").remove();

    $("<div/>", {
        id: "logoutBtn",
        class: "mb-3"
    }).append(
        $("<button/>", {
            id: "logout",
            class: "btn btn-danger",
            type: "submit",
            name: "logout",
            text: "Logout"
        })
    ).appendTo("#loginContainer");

    // event handler for clicking on logout button
    $("#logout").click(function () {
        logout();
    });
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
        alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
            "Bitte probieren Sie es später erneut.");
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

    $("#updateProfileForm").remove();

    $("<div/>", {
        id: "updateProfileForm",
        class: "mb-3"
    }).appendTo("#loginContainer");

    $("#userProfileGreeting").remove();

    $("<div/>", {
        id: "userProfileGreeting",
        class: "mb-3",
        text: "Willkommen " + sex + " " + firstname + " " + lastname + "!"
    }).append($("<p/>", {
        text: "Hier können Sie Ihr Profil einsehen und ändern."
    })).appendTo("#updateProfileForm");

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
                selected: (sex === "Keine")
            }),
            $("<option/>", {
                text: "Frau",
                selected: (sex === "Frau")
            }),
            $("<option/>", {
                text: "Herr",
                selected: (sex === "Herr")
            })
        )
    ).appendTo("#updateProfileForm");

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
    ).appendTo("#updateProfileForm");

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
    ).appendTo("#updateProfileForm");

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
    ).appendTo("#updateProfileForm");

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
    ).appendTo("#updateProfileForm");

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
    ).appendTo("#updateProfileForm");

    $("#userAccountName").remove();

    $("<div/>", {
        id: "userAccountName",
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
            class: "form-text",
            text: "Der Benutzername kann nicht geändert werden!"
        })
    ).appendTo("#updateProfileForm");

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
    ).appendTo("#updateProfileForm");

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
            required: false,
            placeholder: "Optional"
        }),
        $("<div/>", {
            id: "passwordNewHelp",
            class: "form-text",
            text: "Das Passwort muss mindestens 8 Zeichen lang sein!"
        })
    ).appendTo("#updateProfileForm");

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
            required: false,
            placeholder: "Optional"
        }),
        $("<div/>", {
            id: "passwordNew2Help",
            class: "form-text"
        })
    ).appendTo("#updateProfileForm");

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
    ).appendTo("#updateProfileForm");

    $("#updateUserProfile").remove();

    $("<button/>", {
        id: "updateUserProfile",
        class: "btn btn-success",
        type: "submit",
        name: "updateUserProfile",
        text: "Änderungen übernehmen"
    }).appendTo("#updateProfileForm");

    // event handler for clicking on update user profile button
    $("#updateUserProfile").click(function () {

        updateUserProfile(
            username,
            $("#sex").val(),
            $("#name").val(),
            $("#lastname").val(),
            $("#address").val(),
            $("#postal_code").val(),
            $("#city").val(),
            $("#email").val(),
            $("#passwordNew").val(),
            $("#passwordNew2").val(),
            $("#password").val()
        );
    });
}

function updateUserProfile(username, sex, name, lastname, address, postal_code, city, email, passwordNew, passwordNew2, password) {

    if (checkNewPasswordInput(passwordNew, passwordNew2)) {

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

        console.log("Attempting to update user profile");

        $.ajax({
            type: "PUT",
            url: "../../../../backend/user/controller/UserController.php",
            cache: false,
            data: {method: "updateUserProfile", param: updateUserProfileParameter},
            dataType: "json"
        }).done(function (response) {
            console.log("Request succeeded! Response: " + response);
            if(response === "user profile has been updated") {
                showProfileOfCurrentUser()
            } else {
                $("#password").addClass("is-invalid");
                $("#passwordHelp").addClass("text-danger");
                $("#passwordHelp").html("Das Passwort ist falsch!");
            }
        }).fail(function () {
            console.log("Request failed!");
            alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
                "Bitte probieren Sie es später erneut.");
        });

    }
}

function checkNewPasswordInput(passwordNew, passwordNew2) {
    if (passwordNew == "" && passwordNew2 == "") {
        // no new password has been set
        return true;
    }else {

        if (passwordNew != passwordNew2) {
            console.log("new passwords do not match!");
            $("#passwordNew").addClass("is-invalid");
            $("#passwordNewHelp").addClass("text-danger");
            $("#passwordNewHelp").html("Passwörter stimmen nicht überein!");
            $("#passwordNew2").addClass("is-invalid");
            $("#passwordNew2Help").addClass("text-danger");
            $("#passwordNew2Help").html("Passwörter stimmen nicht überein!");
            return false;
        } else {
            console.log("new passwords match!");
            $("#passwordNew").removeClass("is-invalid");
            $("#passwordNewHelp").removeClass("text-danger");
            $("#passwordNew2").removeClass("is-invalid");
            $("#passwordNew2Help").removeClass("text-danger");
            $("#passwordNewHelp").html("");
            $("#passwordNew2Help").html("");

            if (passwordNew.length < 8) {
                $("#passwordNew").addClass("is-invalid");
                $("#passwordNewHelp").addClass("text-danger");
                $("#passwordNewHelp").html("Das Passwort muss mindestens 8 Zeichen lang sein!");

                return false;
            }

            return true;
        }
    }
}

