// Assign Value To Input
$(document).ready(function () {
    $("input").each(function () {
        $(this).on("keydown", function () {
            $(this).attr("value", $(this).val())
        })
        $(this).on("change", function () {
            $(this).attr("value", $(this).val())
        })
    });
});