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

function appendUser(user) {
    $("#users")
        .append(
            $("<div/>", {
                id: user.username,
                type: "user",
                name: user.username,
                text: user.username
            })
        )
}