$(document).ready(function () {
});

/**
* This was my first ajax call so, some notes!
* In the PHP, if you "echo json_encode(array(...))"
* you will always get SUCCESS! This is because it expects
* a json result so, if you do return json, it thinks everything
* is okay - even if you throw an exception in php.
* In the case you want error, just echo nothing.
* As of now, unsure if you can do double ajax calls for php.
*/
$("#submitTicketButton").click(function () {

    var computerId = $("#computerId")[0].value;
    var description = $("#description")[0].value;

    var isValidComputerId = /^\d+$/.test(computerId);
    var computerExists = false;

    if (computerId != "" && description != "" && isValidComputerId) {
        $.ajax({
            type: "POST", //request type
            url: "functions.php", //the page containing php script
            dataType: 'json',
            data: { computerId: computerId, description: description },
            success: function (data) {
                $("#computerId")[0].value = "";
                $("#description")[0].value = "";
                $("#successMessage").removeClass("noDisplay");
                $("#errorMessage").addClass("noDisplay");
            },
            error: function (jqXHR, errorStatus, errorText) {
                $("#errorMessage").removeClass("noDisplay");
                $("#successMessage").addClass("noDisplay");
                return false;
            }
        });
    } else {
        return false;
    }

    return false;
});