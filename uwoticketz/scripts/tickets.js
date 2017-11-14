$("document").ready(function () {

});

$(".statusSelect").change(function (e) {

    var statusId = $(e.currentTarget)[0].value;
    var optionLength = $(e.currentTarget)[0].length;
    var name = "";

    //GET THE NAME OF THE OPTION, THIS WAS ANNOYING.
    for (var i = 0; i < optionLength; i++) {
        if ($(e.currentTarget[i])[0].value === statusId) {
            name = $(e.currentTarget[i])[0].text;
        }
    }

    //retrieve the ticket number
    var ticketNumber = ($(this).closest("tr")[0].cells[0].textContent);

    var isCompleted = name.toLowerCase() === "completed";
    var isIgnored = name.toLowerCase() === "ignored";

    var row = $(this);

    var returnedData = {};

    if (computerId != "" && location != "") {
        $.ajax({
            type: "POST", //request type
            url: "functions.php", //the page containing php script
            dataType: 'json',
            data: { statusId: statusId, ticketNumber: ticketNumber, statusName: name },
            success: function (data) {
                returnedData = data;
            },
            error: function (jqXHR, errorStatus, errorText) {

            },
            complete: function () {
                if (isCompleted) {
                    //"real-time" update of Completed time on the table.
                    row.closest("tr")[0].cells[3].textContent = returnedData.dateCompleted;
                }
                if (isCompleted || isIgnored) {
                    $(e.currentTarget).prop('disabled', true);
                }
            }
        });
    } else {
        return false;
    }

    return false;
});