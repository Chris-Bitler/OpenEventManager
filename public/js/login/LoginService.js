class LoginService {

    constructor() {
        this.setupEvents();
    }

    setupEvents() {
        let parent = this;
        $("#username-login, #password-login").on('input', function ( e ) {
            if(e.keyCode === 13) { //Enter
                parent.attemptLogin();
            }
        });
        $("#submit-login").click(function() {
            parent.attemptLogin();
        });
    }

    attemptLogin() {
        let username = $("#username-login").val();
        let password = $("#password-login").val();

        var request = {
            "username": username,
            "password": password
        }

        let client = new Client(window.location.protocol + "//" + window.location.host + "/api");
        client.request("user/login", request, function( result ) {
            if(result.result.error === false) {
                $("#login-notify").css("color", "green");
                $("#login-notify").text(result.result.message);
            } else {
                $("#login-notify").css("color", "red");
                $("#login-notify").text(result.result.message);
            }
        })
    }
}

$(document).ready(function() {
   let loginService = new LoginService();
});
