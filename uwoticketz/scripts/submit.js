var ajaxURL = "../uwoticketz/";

$(document).ready(function () {
});

$("#submitTicketButton").click(function () {

    var computerId = $("#computerId")[0].value;
    var description = $("#description")[0].value;

    var isValidComputerId = /^\d+$/.test(computerId);
    var computerExists = false;

    if (computerId != "" && description != "" && isValidComputerId) {
        $.ajax({
            type: "POST", //request type
            url: "functions.php", //the page containing php script
            dataType: JSON,
            data: { computerId: computerId, description: description },
            success: function (data) {
                console.log(data);
                $("#computerId")[0].value = "";
                $("#description")[0].value = "";
                $("#successMessage").removeClass("noDisplay");
                $("#errorMessage").addClass("noDisplay");
            },
            error: function (jqXHR, errorStatus, errorText) {
                $("#errorMessage").removeClass("noDisplay");
                $("#successMessage").addClass("noDisplay");
            }
        });
    } else {
        return false;
    }

    return false;
});