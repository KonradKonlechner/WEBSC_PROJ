$(document).ready(function () {
    validateAdminAuthorization()
    getAllUsers();
});

function validateAdminAuthorization() {
    $.ajax({
        type: "GET",
        url: "../../../../backend/user/controller/UserController.php",
        cache: false,
        data: {method: "checkUserIsAdmin", param: null},
        dataType: "json"
    }).done(function (response) {
        console.log("Request succeeded! IsAdmin - Response: " + response);
        if (response !== '1') {
            // redirect to welcome page
            window.location.href = "../../welcome/welcome-view/welcome-view.html"
        }
    }).fail(function () {
        console.log("Request failed!");
        alert("Adminauthentifizierung konnte nicht durchgeführt werden.\nBitte versuchen Sie es später noch einmal")
        // redirect to welcome page
        window.location.href = "../../welcome/welcome-view/welcome-view.html"
    });
}

function getAllUsers() {
    $.ajax({
        type: "GET",
        url: "../../../../backend/user/controller/UserController.php",
        cache: false,
        data: {method: "getAllUsers", param: null},
        dataType: "json"
    }).done(function (response) {
        response.forEach(function (user) {
            appendUser(user)
        })
        closeAllUserInfos()
    }).fail(function () {
        console.log("Request failed!");
        alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
            "Bitte probieren Sie es später erneut.");
    });
}

function openUserInfo(user) {
    const isAlreadyOpen = $("#userInfo" + user.username).hasClass("closed");
    closeAllUserInfos();
    if (isAlreadyOpen) {
        $("#userInfo" + user.username)
            .removeClass("closed")
            .css("display", "block");
        $("#"+user.username)
            .addClass("active");
    }
}

function closeAllUserInfos() {
    $(".userInfo")
        .addClass("closed")
        .css("display", "none");
    $(".userClickBox")
        .removeClass("active");
}

function appendUser(user) {
    $("#users")
        .append(
            $("<button/>", {
                id: user.username,
                class: "userClickBox",
                type: "user"
            })
                .click(function (e) {
                    openUserInfo(user)
                })
                .append(
                    $("<div/>", {
                        name: user.username,
                        text: user.username,
                        class: "userNameHeader"
                    }),
                    $("<div/>", {
                        id: "nameHeader" + user.username,
                        name: user.name,
                        text: user.name + " " + user.lastname,
                        class: "nameHeader"
                    }),
                    $("<div/>", {
                        id: "userActiveState" + user.username,
                        name: "userActiveState",
                        text: user.isInactive === 0 ? "Aktiv" : "Inaktiv",
                        class: user.isInactive === 0 ? "activeState" : "inactiveState"
                    }).addClass("userState")
                ),
            $("<div/>", {
                class: "userInfo",
                id: "userInfo" + user.username,
            }).append(getAppendableObjectsFor(user))
        );
    setUpdateEventListener(user)
}

function setUpdateEventListener(user) {
    console.log("Attempting to update user profile");
    $("#submit" + user.username).click(function () {
        updateUser(
            user.username,
            $("#sex" + user.username).val(),
            $("#name" + user.username).val(),
            $("#lastname" + user.username).val(),
            $("#address" + user.username).val(),
            $("#postalCode" + user.username).val(),
            $("#city" + user.username).val(),
            $("#email" + user.username).val(),
            $("#isActive" + user.username).is(":checked"),
        );
    });
}

function updateUser(
    username,
    sex,
    name,
    lastname,
    address,
    postalCode,
    city,
    email,
    isActive
) {
    console.log(isActive)
    const userShouldBeUpdated = confirm(
        "Sie sind dabei die Daten des Users "
        + username
        + " zu ändern.\nMöchten Sie fortfahren?"
    );

    if (!userShouldBeUpdated) {
        return;
    }

    console.log("Attempting to update user profile of User " + username);

    const updateUserProfileParameter = {
        username: username,
        sex: sex,
        name: name,
        lastname: lastname,
        address: address,
        postal_code: postalCode,
        city: city,
        email: email,
        isActive: isActive
    }

    $.ajax({
        type: "POST",
        url: "../../../../backend/user/controller/UserController.php",
        cache: false,
        data: {method: "updateUserProfileAsAdmin", param: updateUserProfileParameter},
        dataType: "json"
    }).done(function (response) {
        alert("Der User " + response.username + " wurde erfolgreich geändert!");
        // technically not required
        setFieldsToUpdatedValues(response)
    }).fail(function () {
        console.log("Request failed!");
        alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
            "Bitte probieren Sie es später erneut.");
    });
}

function setFieldsToUpdatedValues(user) {
    $("#userActiveState" + user.username)
        .removeClass("activeState")
        .removeClass("inactiveState")
        .addClass(user.isInactive === 0 ? "activeState" : "inactiveState")
        .text(user.isInactive === 0 ? "Aktiv" : "Inaktiv");
    $("#nameHeader" + user.username).text(user.name + " " + user.lastname);
}

function getAppendableObjectsFor(user) {
    // Note: yes, this is ugly but its easy to write and gets the job done.
    return "<div class=\"mb-3\">\n" +
        "  <label for=\"sex\" class=\"form-label\">Anrede</label>\n" +
        "  <select name=\"sex\" id=\"sex" + user.username + "\" class=\"form-select\">\n" +
        "    <option " + (user.sex === "Keine" ? "selected" : "") + " value=\"Keine\">Keine</option>\n" +
        "    <option " + (user.sex === "Frau" ? "selected" : "") + " value=\"Frau\">Frau</option>\n" +
        "    <option " + (user.sex === "Herr" ? "selected" : "") + " value=\"Herr\">Herr</option>\n" +
        "  </select>\n" +
        "</div>" +
        "<div class=\"mb-3\">\n" +
        "  <label for=\"name\" class=\"form-label\">Vorname</label>\n" +
        "  <input type=\"text\" class=\"form-control\" name=\"name\" id=\"name" + user.username + "\" value=\"" + user.name + "\" required >\n" +
        "</div>\n" +
        "<div class=\"mb-3\">\n" +
        "  <label for=\"lastname\" class=\"form-label\">Nachname</label>\n" +
        "  <input type=\"text\" class=\"form-control\" name=\"lastname\" id=\"lastname" + user.username + "\" value=\"" + user.lastname + "\" required>\n" +
        "</div>\n" +
        "<div class=\"mb-3\">\n" +
        "  <label for=\"address\" class=\"form-label\">Adresse</label>\n" +
        "  <input type=\"text\" class=\"form-control\" name=\"address\" id=\"address" + user.username + "\" value=\"" + user.address + "\" required>\n" +
        "</div>\n" +
        "<div class=\"mb-3\">\n" +
        "  <label for=\"postalCode\" class=\"form-label\">Postleitzahl</label>\n" +
        "  <input type=\"text\" class=\"form-control\" name=\"postalCode\" id=\"postalCode" + user.username + "\" value=\"" + user.postal_code + "\" required>\n" +
        "</div>\n" +
        "<div class=\"mb-3\">\n" +
        "  <label for=\"city\" class=\"form-label\">Ort</label>\n" +
        "  <input type=\"text\" class=\"form-control\" name=\"city\" id=\"city" + user.username + "\" value=\"" + user.city + "\" required>\n" +
        "</div>\n" +
        "<div class=\"mb-3\">\n" +
        "  <label for=\"email\" class=\"form-label\">Email</label>\n" +
        "  <input class=\"form-control\" name=\"email\" id=\"email" + user.username + "\" aria-describedby=\"emailHelp\" value=\"" + user.email + "\" required>\n" +
        "  <div id=\"emailHelp\" class=\"form-text\"></div>\n" +
        "</div>\n" +
        "<div class=\"mb-3\">\n" +
        "  <label for=\"isActive\" class=\"form-label\">Ist Aktiv?</label>\n" +
        "  <input type=\"checkbox\"  name=\"isActive\" id=\"isActive" + user.username + "\" value=\"isActive\" "+(user.isInactive === 0 ? "checked" : "")+">\n" +
        "</div>\n" +
        "<button class=\"btn btn-success\" type=\"submit\" name=\"submit\" id=\"submit" + user.username + "\">Ändern</button>\n"
}