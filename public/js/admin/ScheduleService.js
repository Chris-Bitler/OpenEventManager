class ScheduleService {

    constructor() {
        this.setupEvents();
    }

    setupEvents() {
        let parent = this;
        $("#add-item-btn").click(function() {
            $("#add-item-modal").modal("show");
        });
        $("#add-item-modal-confirm").click(function() {
            parent.addEventToSystem();
        });
        $("button[id^=edit]").click(function(event) {
            let id = $(this).attr('data-id');
            let description = $("#description-" + id).html();
            let dateString = $("#dateString-" + id).html();
            let actualDateString = parent.structureDateTimeForLocal(dateString.substring(0,16)); //Remove anything after date
            parent.showEditModal(id, description, actualDateString);
        });
        $("#item-edit-modal-confirm").click(function() {
            parent.editItem();
        });
        $("button[id^=delete]").click(function(event) {
            let id = $(this).attr('data-id');
            parent.showDeleteModal(id);
        });
        $("#item-delete-modal-confirm").click(function() {
            parent.deleteItem();
        });

        $("#update-timezone-btn").click(function() {
            parent.updateTimezone();
        });

        $("#schedule-table").DataTable( {
            "order": [[ 1, 'asc' ]],
            "dom": "t"
        });
    }

    structureDateTimeForLocal(dateTimeString) {
        let month = dateTimeString.substring(0,2);
        let day = dateTimeString.substring(3,5);
        let year = dateTimeString.substring(6,10);
        let hour = dateTimeString.substring(11,13);
        let minute = dateTimeString.substring(14,17);

        return year + "-" + month + "-" + day + "T" + hour + ":" + minute;
    }

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

                    let params = result.result.params;

                    let optionButtons = "<button id='edit-" + params.id + "' data-id='" + params.id + "' type='button' class='btn btn-default'>" +
                        "<i class='fas fa-edit'></i>" +
                        "</button>" +
                        "<button id='delete-" + params.id + "' data-id='" + params.id + "' type='button' class='btn btn-danger'>" +
                        "<i class='fas fa-trash'></i>" +
                        "</button>";

                    let table = $("#schedule-table").DataTable();

                    let node = table
                        .row.add([params.description, params.dateTimeString, optionButtons])
                        .draw(false)
                        .node();

                    $(node).attr('id', 'row-' + params.id);
                    $(node).find("td:eq(0)").attr('id', 'description-' + params.id);
                    $(node).find("td:eq(1)").attr('id', 'dateString-' + params.id);
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

                    let params = result.result.params;

                    let optionButtons = "<button id='edit-" + params.id + "' data-id='" + params.id + "' type='button' class='btn btn-default'>" +
                        "<i class='fas fa-edit'></i>" +
                        "</button>" +
                        "<button id='delete-" + params.id + "' data-id='" + params.id + "' type='button' class='btn btn-danger'>" +
                        "<i class='fas fa-trash'></i>" +
                        "</button>";

                    let table = $("#schedule-table").DataTable();

                    table.rows("#row-" + id).remove().draw();

                    let node = table
                        .row.add([params.description, params.dateTimeString, optionButtons])
                        .draw(false)
                        .node();

                    $(node).attr('id', 'row-' + params.id);
                    $(node).find("td:eq(0)").attr('id', 'description-' + params.id);
                    $(node).find("td:eq(1)").attr('id', 'dateString-' + params.id);

                } else {
                    notify.removeClass('alert-success');
                    notify.addClass('alert-danger');
                    notify.text(result.result.message);
                }
            } catch (err) {
                notify.removeClass('alert-success');
                notify.addClass('alert-danger');
                notify.text("An unknown error has occurred while editing an item.");
            }
            $("#edit-modal").modal("hide");
        });
    }

    deleteItem() {
        let id = $("#delete-id").val();

        let request = {
            "id": id
        };

        let client = new Client(window.location.protocol + "//" + window.location.host + "/api");
        client.request("admin/schedule/removeItem", request, function( result ) {
            let notify = $("#update-notify");
            notify.removeClass('hidden');
            try {
                if (result.result.error === false) {
                    notify.removeClass('alert-danger');
                    notify.addClass('alert-success');
                    notify.text(result.result.message);

                    let table = $("#schedule-table").DataTable();

                    table.rows("#row-" + id).remove().draw();
                } else {
                    notify.removeClass('alert-success');
                    notify.addClass('alert-danger');
                    notify.text(result.result.message);
                }
            } catch (err) {
                notify.removeClass('alert-success');
                notify.addClass('alert-danger');
                notify.text("An unknown error has occurred while deleting an item.");
            }
            $("#delete-modal").modal("hide");
        });
    }

    updateTimezone() {
        let parent = this;
        let timezone = $("#timezone").find(":selected").text();

        let request = {
            "timezone": timezone
        };

        let client = new Client(window.location.protocol + "//" + window.location.host + "/api");
        client.request("admin/schedule/updateTimezone", request, function( result ) {
            let notify = $("#update-notify");
            notify.removeClass('hidden');
            try {
                if (result.result.error === false) {
                    notify.removeClass('alert-danger');
                    notify.addClass('alert-success');
                    notify.text(result.result.message + " Refreshing in 5 seconds..");

                    setTimeout(function() {
                        location.reload();
                    }, 5000);
                } else {
                    notify.removeClass('alert-success');
                    notify.addClass('alert-danger');
                    notify.text(result.result.message);
                }
            } catch (err) {
                notify.removeClass('alert-success');
                notify.addClass('alert-danger');
                notify.text("An unknown error has occurred while updating the site timezone.");
            }
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

    if(typeof(curTimezone) !== 'undefined') {
        document.getElementById('timezone').value = curTimezone.replace("&amp;", "&");
    }
});
