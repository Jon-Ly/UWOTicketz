var ajaxURL = "../uwoticketz/";

$(document).ready(function () {
});

$("#submitTicketButton").click(function () {

    var computerId = $("#computerNumber")[0].value;
    var description = $("#description")[0].value;
    
    $.ajax({
        url: ajaxURL+"functions.php", //the page containing php script
        type: "POST", //request type,
        dataType: 'json',
        data: { computerId: computerId, description: description },
        success: function (result) {
            console.log(result);
        },
        error: function (error) {
            console.log(error);
        }
    });
});