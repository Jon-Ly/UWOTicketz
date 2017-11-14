$("document").ready(function () {

});

$("#submitUserButton").click(function(e) {

    var validForm = true;

    var inputs = $("#addUserForm input");

    var firstName = $("#firstName")[0].value;
    var lastName = $("#lastName")[0].value;
    var username = $("#username")[0].value;
    var accessLevel = parseInt($("#accessLevel option:selected")[0].value);

    var validNames = (/^[a-zA-Z]+$/.test(firstName) && /^[a-zA-Z]+$/.test(lastName));

    var successful = false;

    if (firstName != "" && lastName != "" && username != "" && accessLevel >= 0 && validNames) {
        $.ajax({
            type: "POST", //request type
            url: "functions.php", //the page containing php script
            dataType: 'json',
            data: {
                firstName,
                lastName,
                username,
                accessLevel
            },
            success: function (data) {
                $("#firstName")[0].value = "";
                $("#lastName")[0].value = "";
                $("#username")[0].value = "";
                $("#accessLevel option:selected").prop("selected", false);
                successful = true;
            },
            error: function (jqXHR, errorStatus, errorText) {
                
            },
            complete: function () {
                // close the modal
                e.preventDefault();
                $('#addUserModal').modal('toggle');
            }
        });
    } else {
        return false;
    }

    return false;
});