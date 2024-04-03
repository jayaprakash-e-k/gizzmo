<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Abstract Paper - GIZZMO 2K24 | Erode Sengunthar Engineering College</title>
    <?php include "./includes/include.php" ?>
</head>

<body>
    <?php
    include "./includes/loader.html";
    include "./includes/header.php";
     ?>

    <div class="form-container">

        <!-- Paper upload -->
        <section class="form" style="align-items: start; height: calc(100vh - 100px);">
            <div class="illustration">
                <img src="./assets/img/Upload.png" alt="Upload Illutration">
            </div>
            <form method="POST" enctype="multipart/form-data" id="paper-upload">
                <h2>Paper Upload</h2>
                <span class="error"></span>
                <div class="input-group">
                    <input type="text" name="tl_id" id="tl_id" value="">
                    <label for="tl_id">Team Leader ID</label>
                    <span class="error tl_id"></span>
                    <span class="error"> Note: gizzmo ID will be sent to your Email with Successfull Registration Email</span>

                </div>
                <div class="input-group">
                    <input type="text" name="member[]" id="member-1" value="">
                    <label for="member-1">Member 1 ID (Optional)</label>
                    <span class="error member-1"></span>
                </div>
                <div class="input-group">
                    <input type="text" name="member[]" id="member-2" value="">
                    <label for="member-2">Member 2 ID (Optional)</label>
                    <span class="error member-2"></span>
                </div>
                <div class="input-group">
                    <input type="text" name="paper_title" id="paper-title" value="">
                    <label for="paper-title">Paper Title</label>
                </div>
                <div class="input-group">
                    <input type="file" name="paper_file" id="paper-file" placeholder="Upload Paper File" value="">
                    <label for="paper-file">Paper File</label>
                    <span class="note">Note: File Formats Supported: pdf, ppt, pptx, doc, docx</span>
                </div>
                <div class="btn-group">
                    <button type="submit" name="paper_upload" class="btn-success">Submit</button>
                </div>
            </form>
        </section>
        
    </div>

    <?php include "./includes/footer.php"; ?>

    <script>
        // Show Loader
        $("#loader-container").fadeIn(500);

        $(document).ready(function() {

            // Set the value of input fields
            $("input").each(function() {
                $(this).on("blur change keydown", function() {
                    setValue(this);
                });
            });

            // Hide Loader
            $("#loader-container").fadeOut(500);

            // Paper Upload
            $("#paper-upload").submit(function(e) {
                e.preventDefault();
                var form = $(this);
                var paperTitle = $("#paper-title").val();
                var paperFile = $("#paper-file").val();
                var tl_id = $("#tl_id").val();

                if (paperTitle == "") {
                    message("Paper Title is required", "error");
                    $("#paper-title").focus();
                    $(".error").text("Paper Title is required");
                    return false;
                }

                if (paperFile == "") {
                    message("Paper File is required", "error");
                    $("#paper-file").focus();
                    $(".error").text("Paper File is required");
                    return false;
                }

                // Validate file
                var file = $("#paper-file")[0].files[0];
                var fileType = file["type"];
                var validExtensions = ["application/pdf", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/msword", "application/vnd.ms-powerpoint", "application/vnd.openxmlformats-officedocument.presentationml.presentation"];
                if ($.inArray(fileType, validExtensions) < 0) {
                    message("Invalid File Format", "error");
                    $("#paper-file").focus();
                    $(".error").text("Invalid File Format");
                    return false;
                }

                var formData = new FormData(form[0]);
                formData.append("paper_upload", 1);

                // show Loader
                $("#loader-container").fadeIn(500);

                $.ajax({
                    url: "./backend/register.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        var response = JSON.parse(data);

                        // Hide Loader
                        $("#loader-container").fadeOut(500);

                        if (response.status == "success") {
                            message(response.message, "success");

                            // Count Member Fields
                            var count = $("#paper-upload").find("input[type='member[]']").length;

                            $("#tl_id_p").val(localStorage.getItem("tl_id"));
                            $("#tl_id_p").attr("value", localStorage.getItem("tl_id"));

                            $("#amount").val(response.amount);

                            setInterval(() => {
                                window.location.href = "./index.php";
                            }, 5000);
                        } else {
                            message(response.message, "error");
                        }
                    }
                });
            });

            // Team Leader UID and  Member UID Validation with AJAX
            $("#tl_id").on("blur change keydown keyup", function() {
                validateUID("tl_id");
            });

            $(document).on("blur change keydown keyup", "input[name='member[]']", function() {
                var id = $(this).attr("id");
                validateUID(id);
            });
        });

        // Validate UID
        function validateUID(id) {
            var uid = $(`#${id}`).val();
            if (uid != "") {
                $.ajax({
                    url: "./backend/register.php",
                    type: "POST",
                    data: {
                        "validate_uid": 1,
                        "uid": uid
                    },
                    success: function(data) {
                        var response = JSON.parse(data);
                        if (response.status == "error") {
                            $(`.error.${id}`).text(response.message);
                        } else {
                            $(`.error.${id}`).text("");
                        }
                    }
                });
            }
        }

        function setValue(el) {
            $(el).attr("value", $(el).val())
        }
    </script>
</body>

</html>