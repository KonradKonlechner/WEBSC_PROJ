$(document).ready(function () {
    
    $("#btn_login").click(function (e) {
        console.log('Input username: ' + $("#username").val());   
        var username = $("#username").val();
        var password = $("#password").val();
        console.log('trying to log in user: ' + username);
        login(username, password);
    });
});

function login(username, password) {    
    $.ajax({
        type: "POST",
        url: "../backend/login_check.php",
        cache: false,
        data: {username: username, password: password},
        dataType: "text" // better use json!
    }).done(function(response) {
        console.log("Request succeeded! Response: " + response);   
        displayLogInInfo(response); 
    }).fail(function() {
        console.log("Request failed!");         
    });
} 

function displayLogInInfo(response) {
    $("#login_info").remove();
    
    $("<p/>", {
		id: "login_info",
		html: response
	}).appendTo( "#login_field" );
}