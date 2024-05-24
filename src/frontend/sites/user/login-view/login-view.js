$(document).ready(function () {
    $(function () {
        // event handler for clicking on login button
        $("#submit").click(function (e) {
            login($("#username").val(), $("#password").val());
        });
    });
});

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
        console.log("Request succeeded! Response: " + response);           
    }).fail(function() {
        console.log("Request failed!");         
    });
}