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
            let notify = $("#login-notify");
            notify.removeClass('hidden');
            try {
                if (result.result.error === false) {
                    notify.removeClass('alert-danger');
                    notify.addClass('alert-success');
                    notify.text(result.result.message);
                } else {
                    notify.removeClass('alert-success');
                    notify.addClass('alert-danger');
                    notify.text(result.result.message);
                }
            } catch (err) {
                notify.removeClass('alert-success');
                notify.addClass('alert-danger');
                notify.text("An unknown error has occurred during login.");
            }
        });
    }
}

$(document).ready(function() {
   let loginService = new LoginService();
});
