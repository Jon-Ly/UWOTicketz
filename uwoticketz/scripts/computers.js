$("document").ready(function () {

});

//need the previous comp id for updating.
var old_comp_id;
var row;

$("tbody").on("click", "button.edit_computer_button", function (e) {

    var computer_id = $(this).closest("tr")[0].cells[0].textContent;
    var location = $(this).closest("tr")[0].cells[1].textContent;
    var locations = $("#locationsEdit")[0];

    for (var x = 0; x < locations.length; x++) {
        if (locations[x].label === location) {
            $("#locationsEdit").val(locations[x].value);
        }
    }

    old_comp_id = parseInt(computer_id);
    row = Object.assign({}, e);

    $("#computerNumberEdit")[0].value = computer_id;

    $("#edit_comp_modal").modal("show");

    return false;
});

$("#edit_computer_button").click(function (e) {

    var computer_id = parseInt($("#computerNumberEdit")[0].value);
    var location = parseInt($("#locationsEdit option:selected")[0].value);

    if (computer_id !== "" && location !== "" && old_comp_id !== "") {
        $.ajax({
            type: "POST", //request type
            url: "functions.php", //the page containing php script
            dataType: 'json',
            data: { computer_id: computer_id, location: location, previous_computer_id: old_comp_id},
            success: function (data) {
                $("#computers_table > tbody")[0].innerHTML = data[0];
            },
            error: function (jqXHR, errorStatus, errorText) {},
            complete: function () {
                // close the modal
                e.preventDefault();
                $('#edit_comp_modal').modal('toggle');
            }
        });
    } else {
        return false;
    }

    return false;
});

$("button.remove_computer_button").click(function (e) {

    var computer_id = $(this).closest("tr")[0].cells[0].textContent;

    if (computer_id !== "") {
        $.ajax({
            type: "POST", //request type
            url: "functions.php", //the page containing php script
            dataType: 'json',
            data: { computer_id: computer_id },
            success: function (data) {
                $("#computers_table > tbody")[0].innerHTML = data[0];
            },
            error: function (jqXHR, errorStatus, errorText) {
                alert("Unable to delete that computer due to a ticket still open for that computer.");
            },
            complete: function () {

            }
        });
    } else {
        return false;
    }

    return false;
});

$("#submitComputerButton").click(function (e) {

    var computerId = $("#computerNumberAdd")[0].value;
    var location = parseInt($("#locationsAdd option:selected")[0].value);
    var locationLabel = $("#locationsAdd option:selected")[0].label;

    if (computerId !== "" && location !== "") {
        $.ajax({
            type: "POST", //request type
            url: "functions.php", //the page containing php script
            dataType: 'json',
            data: { computerNumber: computerId, location: location },
            success: function (data) {
                var newRow = 
                    "<tr>" +
                        "<td>" + computerId + "</td>" +
                        "<td>" + locationLabel + "</td>" +
                        "<td>" +
                            "<form id='edit_computer_form_"+computerId+"' method='POST'>" +
                                "<input type='text' name='computer_id' value='"+computerId+"' hidden aria-hidden='true'/>" +
                                "<button class='btn btn-info edit_computer_button' type='submit' id='edit_computer_button_"+computerId+"'>Edit</button>" +
                            "</form >" +
			            "</td>" +
                        "<td>" +
                            "<form id='delete_computer_form_"+computerId+"' method='POST'>" +
                                "<input type='text' name='computer_id' value='$id' hidden aria-hidden='true'/>" +
                                "<button class='btn btn-info remove_computer_button' type='submit' id='delete_computer_button_"+computerId+"'>Delete</button>" +
                            "</form>" +
			            "</td>"+
                    "</tr>";

                $("#computers_table > tbody")[0].innerHTML += newRow;
            },
            error: function (jqXHR, errorStatus, errorText) {
                console.log(jqXHR);
            },
            complete: function () {
                $("#computerNumberAdd")[0].value = "";
                $("#locationsAdd")[0].value = "";
                $("#locationsAdd option:selected").prop("selected", false);
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