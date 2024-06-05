$(document).ready(function () {
    getAllUsers();
});

function getAllUsers() {
    console.log("show profile of current user");
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
    const isAlreadyOpen = $("#userInfo"+user.username).hasClass("closed");
    closeAllUserInfos();
    if (isAlreadyOpen) {
        $("#userInfo" + user.username)
            .removeClass("closed")
            .css("display", "block");
    }
}

function closeAllUserInfos() {
    $(".userInfo")
        .addClass("closed")
        .css("display", "none");
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
                        text: user.username
                    }),
                    $("<div/>", {
                        name: user.name,
                        text: user.name
                    }),
                    $("<div/>", {
                        name: user.lastname,
                        text: user.lastname
                    }),
                    $("<div/>", {
                        class: "userInfo",
                        id: "userInfo" + user.username,
                        text: "Hier sollten die Userdaten stehen"
                    })
                )
        )
}