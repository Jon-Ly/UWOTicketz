$(document).ready(function () {

    /**
    * This was my first ajax call so, some notes!
    * In the PHP, if you "echo json_encode(array(...))"
    * you will always get SUCCESS! This is because it expects
    * a json result so, if you do return json, it thinks everything
    * is okay - even if you throw an exception in php.
    * In the case you want error, just echo nothing.
    * As of now, unsure if you can do double ajax calls for php.
    */
    $("#submitTicketButton").click(function (e) {

        var computerId = $("#computerId")[0].value;
        var description = $("#description")[0].value;

        if (computerId != "" && description != "") {
            $.ajax({
                type: "POST", //request type
                url: "functions.php", //the page containing php script
                dataType: 'json',
                data: { computerId: computerId, description: description },
                success: function (data) {

                },
                error: function (jqXHR, errorStatus, errorText) {

                },
                complete: function () {
                    $("#computerId")[0].value = "";
                    $("#description")[0].value = "";
                    // close the modal
                    e.preventDefault();
                    $('#submitTicketModal').modal('toggle');
                }
            });
        } else {
            return false;
        }

        return false;
    });

});