$("document").ready(function () {
});

/**
   * Targets the row clicked and gets the ticket number.
   */
$("tbody").on("click", "button.view_ticket_button", function (e) {
    var ticket_id = ($(this).closest("tr")[0].cells[0].textContent);

    var successful = false;

    //e.preventDefault();
    $("#ticketDataModal").modal("show");

    if (ticket_id !== "") {
        $.ajax({
            type: "GET", //request type
            url: "functions.php", //the page containing php script
            dataType: 'json',
            data: { ticket_id: ticket_id },
            success: function (data) {
                var ticket_html =
                    "<div><p class='bold'>Problem Description:</p>" +
                    "<p>" + data[1]["Description"] + "</p>" +
                    "</div> <hr> <p class='bold'>Comment Thread:</p> <div id='comment_thread'>";
                //The data[0] holds the username for whomever submitted the ticket. The actual comments start at data[1].
                $("#ticket_id")[0].innerHTML = ticket_id + " - " + data[0]["Username"];
                if (typeof data[1]["Comment"] !== 'undefined') {
                    for (var x = 1; x < data.length; x++) {
                        ticket_html +=
                            "<blockquote class='blockquote marginLeft10px fontSize14px'>" + data[x]["Comment"] +
                            "<footer class='blockquote-footer'><span class='marginRight10px' >" + data[x]["Username"] + "</span>" +
                            data[x]["DateSubmitted"] + "</footer></blockquote>";
                    }
                } else {
                    ticket_html += "<p class='marginLeft10px'>There are currently no comments</p>"
                }

                ticket_html += "</div>";

                $(".ticket_information")[0].innerHTML = ticket_html;

                //disable the comments if the ticket is completed, for Users
                if (data[1]["Status"] > 3) {
                    $("#user_comment").attr("disabled", "disabled");
                } else {
                    $("#user_comment").removeAttr("disabled");
                }

                successful = true;
            },
            error: function (jqXHR, errorStatus, errorText) {

            },
            complete: function () {
                e.preventDefault();
                $('#ticketDataModal').modal('toggle');
            }
        });
    } else {
        return false;
    }

    return false;
});

/**
* Update the database with the brand new status.
*/
$("tbody").on("change", ".statusSelect", function (e) {

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
    var ticket_number = $(this).closest("tr")[0].cells[0].textContent;

    var isCompleted = name.toLowerCase() === "completed";
    var isIgnored = name.toLowerCase() === "ignored";

    var row = $(this);

    var returnedData = {};

    if (computer_id !== "" && location !== "") {
        $.ajax({
            type: "POST", //request type
            url: "functions.php", //the page containing php script
            dataType: 'json',
            data: { statusId: statusId, ticket_number: ticket_number, statusName: name },
            success: function (data) {
                returnedData = data;
            },
            error: function (jqXHR, errorStatus, errorText) {},
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

$("#submit_comment_button").click(function (e) {

    var comment = $("#user_comment")[0].value;
    var ticket_id = parseInt($("#ticket_id")[0].innerHTML);

    if (comment !== "") {
        $.ajax({
            type: "POST", //request type
            url: "functions.php", //the page containing php script
            dataType: 'json',
            data: { comment: comment, ticket_id: ticket_id },
            success: function (data) {
                var ticket_html = $("#comment_thread")[0].innerHTML;

                if ($("#comment_thread > p").length == 0) {
                    ticket_html +=
                        "<blockquote class='blockquote marginLeft10px fontSize14px'>" + comment +
                        "<footer class='blockquote-footer'><span class='marginRight10px'>" + data["Username"] + "</span>" +
                        data[0] + "</footer></blockquote>";
                } else {
                    ticket_html =
                        "<blockquote class='blockquote marginLeft10px fontSize14px'>" + comment +
                        "<footer class='blockquote-footer'><span class='marginRight10px'>" + data["Username"] + "</span>" +
                        data[0] + "</footer></blockquote>";
                }

                $("#comment_thread")[0].innerHTML = ticket_html;
            },
            error: function (jqXHR, errorStatus, errorText) {
                
            },
            complete: function () {
                $("#user_comment")[0].value = "";
            }
        });
    } else {
        return false;
    }

    return false;
});

$("button.good_rate_button").click(function (e) {

    var ticket_id = parseInt($(this).closest("tr")[0].cells[0].textContent);

    if (ticket_id !== "") {
        $.ajax({
            type: "POST", //request type
            url: "functions.php", //the page containing php script
            dataType: 'json',
            data: { ticket_id: ticket_id, rating: 1 },
            success: function (data) {
                $("#tickets_table > tbody")[0].innerHTML = data[0];
            },
            error: function (jqXHR, errorStatus, errorText) { console.log(jqXHR); },
            complete: function () {}
        });
    } else {
        return false;
    }

    return false;
});

$("button.bad_rate_button").click(function (e) {
    var ticket_id = parseInt($(this).closest("tr")[0].cells[0].textContent);

    if (ticket_id !== "") {
        $.ajax({
            type: "POST", //request type
            url: "functions.php", //the page containing php script
            dataType: 'json',
            data: { ticket_id: ticket_id, rating: 2 },
            success: function (data) {
                $("#tickets_table > tbody")[0].innerHTML = data[0];
            },
            error: function (jqXHR, errorStatus, errorText) { },
            complete: function () { }
        });
    } else {
        return false;
    }

    return false;
});