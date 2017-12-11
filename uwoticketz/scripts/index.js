$(function () {

    /**
    * These are masks, the prevent users from entering
    * unwanted text.
    *
    * e = event.
    */
    $(".letters").on("input", function (e) {
        var field = e.currentTarget;

        var characters = field.value.split("");
        var validString = "";

        var hasApostrophe = false;

        for (var x = 0; x < characters.length; x++) {

            var char = characters[x];

            validCharacter = /^[a-zA-Z ']+$/.test(char);
            if (validCharacter) {
                if (char === "'" && x === 0)
                    continue;
                if (hasApostrophe && char === "'")
                    continue;
                if (char === "'")
                    hasApostrophe = true;
                validString += char;
            }
        }

        field.value = validString;
    });

    $(".numeric").on("input", function (e) {
        var field = e.currentTarget;

        var characters = field.value.split("");
        var validString = "";

        for (var x = 0; x < characters.length; x++) {
            var char = characters[x];

            validCharacter = /^\d+$/.test(char);
            if (validCharacter) {
                if (char === "0" && x === 0)
                    continue;
                validString += char;
            }
        }

        field.value = validString;
    });

    $(".letterNumeric").on("input", function (e) {
        var field = e.currentTarget;

        var characters = field.value.split("");
        var validString = "";

        for (var x = 0; x < characters.length; x++) {

            var char = characters[x];

            validCharacter = /^[a-zA-Z0-9]+$/.test(char);
            if (validCharacter) {
                if (char === "'")
                    hasApostrophe = true;
                validString += char;
            }
        }

        field.value = validString;
    });
});