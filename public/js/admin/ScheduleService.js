class ScheduleService {

    constructor() {
        this.setupEvents();
    }

    setupEvents() {
        let parent = this;
        $("#add-item-link").click(function() {
            $("#add-item-modal").modal("show");
        });
        $("#add-item-modal-confirm").click(function() {
            parent.addEventToSystem();
        });
        $("[id^=edit]").click(function(event) {
            let id = event.target.data("id");
            let description = $("#description-" + id).innerHTML;
            let dateString = $("#dateString-" + id).innerHTML;
            parent.showEditModal(id, description, dateString);
        });
        $("#edit-item-modal-confirm").click(function() {
            parent.editItem();
        });
        $("[id^=delete]").click(function(event) {
            let id = event.target.data("id");
            parent.showDeleteModal(id);
        });
        $("#delete-item-modal-confirm").click(function() {
            parent.deleteItem();
        })
    }

    //TODO: dateTimes are wrong and need to be switched to epoch

    addEventToSystem() {
        let parent = this;
        let description = $("#description").val();
        let dateTime = $("#date-time").val();

        let request = {
            "description": description,
            "dateTime": dateTime
        };

        let client = new Client(window.location.protocol + "//" + window.location.host + "/api");
        client.request("admin/schedule/addItem", request, function( result ) {
            let notify = $("#update-notify");
            notify.removeClass('hidden');
            try {
                if (result.result.error === false) {
                    notify.removeClass('alert-danger');
                    notify.addClass('alert-success');
                    notify.text(result.result.message);

                    var params = result.result.params;

                    // There needs to be a cleaner way to do this.
                    $("#schedule-body").append("<tr>" +
                        "<td>" + params.description + "</td>" +
                        "<td>" + params.dateTimeString + "</td>" +
                        "<td>" +
                        "<button id='edit-" + params.id + "' data-id='" + params.id + "' type='button' class='btn btn-default'>" +
                        "<span class='glyphicon glyphicon-cog'></span>" +
                        "</button>" +
                        "<button id='delete-" + params.id + "' data-id='" + params.id + "' type='button' class='btn btn-danger'>" +
                        "<span class='glyphicon glyphicon-trash'></span>" +
                        "</button>" +
                        + "</td></tr>");

                } else {
                    notify.removeClass('alert-success');
                    notify.addClass('alert-danger');
                    notify.text(result.result.message);
                }
            } catch (err) {
                notify.removeClass('alert-success');
                notify.addClass('alert-danger');
                notify.text("An unknown error has occurred while adding an item.");
            }
            $("#add-item-modal").modal("hide");
        });
    }

    editItem() {
        let description = $("#description-edit").val();
        let dateTime = $("#date-time-edit").val();
        let id = $("#edit-id").val();

        let request = {
            "description": description,
            "eventDateTime": dateTime,
            "id": id
        };

        let client = new Client(window.location.protocol + "//" + window.location.host + "/api");
        client.request("admin/schedule/updateItem", request, function( result ) {
            let notify = $("#update-notify");
            notify.removeClass('hidden');
            try {
                if (result.result.error === false) {
                    notify.removeClass('alert-danger');
                    notify.addClass('alert-success');
                    notify.text(result.result.message);

                    $("#description-" + id).innerHTML = description;
                    $("#dateString-" + id).innerHTML = dateTime;

                } else {
                    notify.removeClass('alert-success');
                    notify.addClass('alert-danger');
                    notify.text(result.result.message);
                }
            } catch (err) {
                notify.removeClass('alert-success');
                notify.addClass('alert-danger');
                notify.text("An unknown error has occurred while adding an item.");
            }
            $("#add-item-modal").modal("hide");
        });
    }

    showEditModal(id, description, dateString) {
        $("#description-edit").val(description);
        $("#date-time-edit").val(dateString);
        $("#edit-id").val(id);

        $("#edit-modal").modal("show");
    }

    showDeleteModal(id) {
        $("#delete-id").val(id);
        $("#delete-modal").modal("show");
    }
}

$(document).ready(function() {
    let scheduleService = new ScheduleService();
});
