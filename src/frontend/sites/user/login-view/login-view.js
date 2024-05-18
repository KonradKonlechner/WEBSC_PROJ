$(document).ready(function () {
    $(function () {
        // load navbar
        $("#navbar")
            .load("../../navigation/navbar/topNavBar.php");
        // load footer
        $("#footer")
            .load("../../navigation/footer/footerNav.php");

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
    });;
}