$("document").ready(function () {

});

$("#submitUserButton").click(function(e) {

    var validForm = true;

    var inputs = $("#addUserForm input");

    var first_name = $("#first_name")[0].value;
    var last_name = $("#last_name")[0].value;
    var username = $("#username")[0].value;
    var access_level = parseInt($("#access_level option:selected")[0].value);

    var validNames = (/^[a-zA-Z]+$/.test(first_name) && /^[a-zA-Z]+$/.test(last_name));

    if (first_name !== "" && last_name !== "" && username !== "" && access_level >= 0 && validNames) {
        $.ajax({
            type: "POST", //request type
            url: "functions.php", //the page containing php script
            dataType: 'json',
            data: {
                first_name,
                last_name,
                username,
                access_level
            },
            success: function (data) {
                $("#first_name")[0].value = "";
                $("#last_name")[0].value = "";
                $("#username")[0].value = "";
                $("#access_level option:selected").prop("selected", false);
                successful = true;

                $("#users_table > tbody")[0].innerHTML = data[0];
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