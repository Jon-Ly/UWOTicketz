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

        var computerId = $("#computerId")[0].value;
        var description = $("#description")[0].value;

        if (computerId != "" && description != "") {
            $.ajax({
                type: "POST", //request type
                url: "functions.php", //the page containing php script
                dataType: 'json',
                data: { computerId: computerId, description: description },
                success: function (data) {
                    //grab the last one - the inserted one.
                    data = data[data.length-1];
                    console.log(data);
                    var newRow = "";

                    newRow =
                        "<tr>" +
                        "<td>"+data["TicketId"]+"</td>" +
                            "<td>"+data["ComputerId"]+"</td>" +
                            "<td>"+data["DateSubmitted"]+"</td>" +
                            "<td></td>" +
                        "<td><select class='form-control statusSelect'>" +
                        "<option value='1' selected>Open</option>" +
                        "<option value='2'>In Progress</option>" +
                        "<option value='3'>Completed</option>" +
                        "<option value='4'>Ignored</option>" +
                        "</select></td>" +
                        "<td></td>" +
                        "<td>"+
                            "<form id='view_ticket_form_"+data["TicketId"]+"' method='GET'>" +
                                "<input type='text' name='ticket_id' value='"+data["TicketId"]+"' hidden aria-hidden='true'>" +
					            "<button class='btn btn-info view_ticket_button' type='submit' id='view_ticket_button"+data["TicketId"]+"'>View</button>" +
				            "</form>" +
			            "</td>" +
                        "</tr>";

                        $("#tickets_table > tbody")[0].innerHTML = newRow + $("#tickets_table > tbody")[0].innerHTML;
                },
                error: function (jqXHR, errorStatus, errorText) {
                    console.log(jqXHR)
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