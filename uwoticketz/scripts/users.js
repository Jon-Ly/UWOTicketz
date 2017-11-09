$("document").ready(function () {

});

$("#submitUserButton").click(function(e) {

    var validForm = true;

    var inputs = $("#addUserForm input");

    for (var i = 0; i < inputs.length; i++){
        if (inputs[0].value === "") {
            validForm = false;
            break;
        }
    }

    if (validForm) {
        e.preventDefault();
        $('#addUserModal').modal('toggle');
    }
});