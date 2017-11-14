$("document").ready(function () {

});

$("#submitComputerButton").click(function (e) {

    var computerNumber = $("#computerNumber")[0].value;
    var location = parseInt($("#location option:selected")[0].value);

    if (computerId != "" && location != "") {
        $.ajax({
            type: "POST", //request type
            url: "functions.php", //the page containing php script
            dataType: 'json',
            data: { computerNumber: computerNumber, location: location },
            success: function (data) {
                $("#computerId")[0].value = "";
                $("#location")[0].value = "";
                $("#location option:selected").prop("selected", false);
            },
            error: function (jqXHR, errorStatus, errorText) {
                
            },
            complete: function () {
                // close the modal
                e.preventDefault();
                $('#addCompModal').modal('toggle');
            }
        });
    } else {
        return false;
    }

    return false;
});