class RegisterService {

    constructor() {
        this.setupEvents();
    }

    setupEvents() {
        let parent = this;
        $("#username-register").on('input', function ( e ) {
            parent.checkAvailability();
        });

        $("#username-register, #password-register, #first, #last, #email").on(
            'input',
            function(e) {
                if(e.keyCode === 13) { ///Enter
                    parent.attemptRegister();
                }
            }
        );

        $("#submit-register").click(function() {
            parent.attemptRegister();
        });
    }
    /**
     * Attempt to register a user using form values from the page
     * The IP is set to a placeholder as it is replaced later
     */
    attemptRegister() {
        let username = $("#username-register").val();
        let password = $("#password-register").val();
        let first = $("#first").val();
        let last = $("#last").val();
        let email = $("#email").val();

        let request = {
            "username": username,
            "password": password,
            "first": first,
            "last": last,
            "email": email,
            "ip": "placeholder"
        };

        let client = new Client(window.location.protocol + "//" + window.location.host + "/api");
        client.request("user/register", request, function( result ) {
           if(result.result.error === false) {
               $("#register-notify").css("color", "green");
               $("#register-notify").text(result.result.message);
           } else {
               $("#register-notify").css("color", "red");
               $("#register-notify").text(result.result.message);
           }
        });
    }

    checkAvailability() {
        let username = $("#username-register").val();

        let request = {
            "username": username
        };

        let client = new Client(window.location.protocol + "//" + window.location.host + "/api");

        client.request("user/checkUsernameAvailability", request, function ( result ) {
            if(result.result === false) {
                $("#register-notify").css("color", "red");
                $("#register-notify").text("Username taken");
            } else {
                $("#register-notify").text("");
            }
        })
    }
}

$(document).ready(function() {
    let registerService = new RegisterService();
});
