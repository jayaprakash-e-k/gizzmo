<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include './includes/include.php'; ?>
    <title>Attendance Form - Gizzmo | Erode Sengunthar Engineering College</title>
</head>

<body>

    <section class="form">
        <form method="POST" id="attendance">
            <h2>Attendance</h2>
            <span class="error"></span>
            <div class="input-group">
                <input type="text" name="tl_uid" id="tl_uid" value="">
                <label for="tl_uid">Team Leader User ID</label>
            </div>
            <div class="btn-group">
                <button type="submit" class="btn-success" name="submit" id="submit">Submit</button>
            </div>
        </form>
    </section>

    <script>
        $(document).ready(function() {


            // Get event id from URL
            var url = new URL(window.location.href);
            var eid = url.searchParams.get("eid");

            // Get Event Details
            $.ajax({
                type: "GET",
                url: "backend/eventOperation.php",
                data: {
                    getEventDetails: true,
                    eid: eid
                },
                success: function(response) {
                    var data = JSON.parse(response);

                    if (data.status == "error") {
                        message(data.message, "error");
                    } else {

                        // Set event details
                        $("#attendance h2").text(data.event.EVENT_NAME + " - Attendance");

                        if (data.event.TEAM_SIZE > 1) {
                            for (var i = 1; i < data.event.TEAM_SIZE; i++) {

                                var input = `<div class="input-group">
                                                <input type="text" name="member[]" id="uid${i}" value="">
                                                <label for="uid${i}">Member User ID ${i}</label>
                                            </div>`;

                                $("#attendance .btn-group").before(input);
                            }
                        }

                        // Set the value of input fields
                        $("input").each(function() {
                            $(this).on("blur change keydown", function() {
                                setValue(this);
                            });
                        });
                    }
                }
            });
        });

        function setValue(el) {
            $(el).attr("value", $(el).val())
        }

        $("#attendance").on("submit", function(e) {
            e.preventDefault();

            var url = new URL(window.location.href);

            var formData = $(this).serialize();
            formData += "&submitAttendance=true";
            formData += "&eid=" + url.searchParams.get("eid");

            $.ajax({
                type: "POST",
                url: "./backend/eventOperation.php",
                data: formData,
                success: function(response) {
                    var data = JSON.parse(response);

                    if (data.status == "error") {
                        message(data.message, "error");
                    } else {
                        message(data.message, "success");
                        $("#attendance").trigger("reset");
                    }
                }
            });
        });
    </script>

</body>

</html>