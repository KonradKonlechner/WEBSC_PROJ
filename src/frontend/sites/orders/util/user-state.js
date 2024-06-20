export function userIsAdmin() {
    let isAdmin = false;
    $.ajax({
        type: "GET",
        url: "../../../../backend/user/controller/UserController.php",
        cache: false,
        async: false,
        data: {method: "checkUserIsAdmin", param: null},
        dataType: "json"
    }).done(function (response) {
        console.log("Request succeeded! IsAdmin - Response: " + response);
        isAdmin = (response === '1');
    }).fail(function () {
        return isAdmin;
    });
    return isAdmin;
}