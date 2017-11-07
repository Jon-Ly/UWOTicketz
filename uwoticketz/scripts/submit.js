var ajaxURL = "../uwoticketz/";

$(document).ready(function () {
});

$("#submitTicketButton").click(function () {

    var computerId = $("#computerId")[0].value;
    var description = $("#description")[0].value;

    var isValidComputerId = /^\d+$/.test(computerId);
    var computerExists = false;

    if (isValidComputerId) {
        $.ajax({
            type: "GET", //request type
            url: "functions.php", //the page containing php script
            dataType: 'text',
            data: { computerId: computerId },
            success: function (data) {
                console.log(data);
            },
            error: function (data) {
                console.log(data);
            }
        });
    }

    //if (computerId != "" && description != "" && isValidComputerId) {
    //    $.ajax({
    //        type: "POST", //request type
    //        url: "functions.php", //the page containing php script
    //        dataType: 'text',
    //        data: { computerId: computerId, description: description },
    //        success: function (data) {
    //            console.log(data);
    //        },
    //        error: function (data) {
    //            console.log(data);
    //        }
    //    });
    //}

    if (!isValidComputerId) {
        alert("INVALID COMPUTER ID");
        return false;
    }
});