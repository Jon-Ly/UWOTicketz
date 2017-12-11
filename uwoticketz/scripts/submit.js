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
    $("#submit_ticket_button").click(function (e) {

        var computer_id = $("#computer_id")[0].value;
        var description = $("#description")[0].value;
        var successful = false;

        if (computer_id != "" && description != "") {
            $.ajax({
                type: "POST", //request type
                url: "functions.php", //the page containing php script
                dataType: 'json',
                data: { computer_id: computer_id, description: description },
                success: function (data) {
                    $("#tickets_table > tbody")[0].innerHTML = data[0];
                    successful = true;
                },
                error: function (jqXHR, errorStatus, errorText) {console.log(jqXHR)},
                complete: function () {
                    $("#computer_id")[0].value = "";
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