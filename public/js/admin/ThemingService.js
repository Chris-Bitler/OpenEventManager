class ThemingService {

    constructor() {
        this.setupEvents();
    }

    setupEvents() {
        let parent = this;
        $("#color-picker-button").click(function() {
            parent.attemptUpdateThemeColor();
        });

        $("#name-button").click(function() {
            parent.attemptUpdateSiteName();
        });

        $("#theme-color").colorpicker();
        $("#theme-color").on('change', function() {
            parent.previewThemeColorBackground();
        });

        $("#color-reset-button").click(function() {
            parent.resetColor();
        });

        $('#color-slider-txt').slider({ id: "color-slider", min: 0, max: 255, value: originalTextColor })
            .on('change', this.previewThemeColorText);

        $("#site-name").on('input', function() {
            parent.previewNameChange();
        })

        $("#name-reset-button").click(function() {
            parent.resetName();
        })
    }

    previewThemeColorBackground() {
        let themeElements = $(".bg-dark");
        themeElements.each(function() {
           this.style.background = $("#theme-color").val();
        });
    }

    previewThemeColorText(event) {
        let val = event.value.newValue;
        let textElements = $(".nav-item > .nav-link, footer > .container > p, .navbar-brand");
        textElements.each(function() {
            this.style.setProperty("color", "rgb(" + val + "," + val + "," + val + ")", "important");
        });
    }

    previewNameChange() {
        let name = $("#site-name").val();
        $("#navbar-brand").text(name);
    }

    resetName() {
        $("#navbar-brand").text(originalSiteName);
        $("#site-name").val(originalSiteName);
    }
    resetColor() {
        let colorpicker = $("#theme-color");
        colorpicker.val(originalColor);
        colorpicker.colorpicker('setValue', originalColor);
        let themeElements = $(".bg-dark");
        themeElements.each(function() {
            this.style.background = originalColor;
        });
        let val = originalTextColor;
        let textElements = $(".nav-item > .nav-link, footer > .container > p, .navbar-brand");
        textElements.each(function() {
            this.style.color = "rgb(" +  + "," + val + "," + val + ") !important";
        });
        $("#color-slider-txt").slider("setValue", val);
    }

    attemptUpdateThemeColor() {
        let parent = this;
        let color = $("#theme-color").val();
        let textColor = $("#color-slider-txt").val();

        let request = {
            "color": color,
            "textColor": textColor
        };

        let client = new Client(window.location.protocol + "//" + window.location.host + "/api");
        client.request("admin/theme/updateColor", request, function( result ) {
            parent.updateNotifyWithResponse(result,
                "An unknown error has occurred during updating the theme.");
        });
    }

    attemptUpdateSiteName() {
        let parent = this;
        let siteName = $("#site-name").val();

        let request = {
            "name": siteName
        };

        let client = new Client(window.location.protocol + "//" + window.location.host + "/api");
        client.request("admin/theme/updateName", request, function( result ) {
            parent.updateNotifyWithResponse(result,
                "An unknown error has occurred during updating the name.");
        });
    }

    updateNotifyWithResponse(result, unknownError) {
        let notify = $("#update-notify");
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
            notify.text(unknownError);
        }
    }
}

$(document).ready(function() {
    let themingService = new ThemingService();
});
