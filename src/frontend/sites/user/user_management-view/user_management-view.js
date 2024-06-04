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
        response.forEach(function(user) {
           appendUser(user)
        })
    }).fail(function () {
        console.log("Request failed!");
        alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
            "Bitte probieren Sie es später erneut.");
    });
}

function userClicked(user) {
    console.log(user.username + "has been clicked.")
}

function appendUser(user) {
    $("#users")
        .append(
            $("<div/>", {
                id: user.username,
                class: "userClickBox",
                type: "user"
            })
                .click(function(e) {userClicked(user)})
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
                )
        )
}