<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL List - GIZZMO | Erode Sengunthar Engineering College</title>
    <script src="./assets/js/keyEncryption.js"></script>
    <?php include './includes/includes.php'; ?>
</head>

<body>
    <?php 
    include './includes/header.php';
    include './includes/loader.html';
    ?>

    <div class="container">

        <div class="header">
            <div class="title">
                <i class="fa fa-calendar" aria-hidden="true"></i>
                <h2>URL List</h2>
            </div>
            <div class="line"></div>
        </div>

        <div class="content">
            <h3>Attendance Links</h3>
            <div class="table attendanceLink">
                <table>
                    <tr>
                        <th>S.NO</th>
                        <th>Event</th>
                        <th>Action</th>
                    </tr>
                </table>
            </div>

            <h3>User List Link</h3>
            <div class="table userListLink">
                <table>
                    <tr>
                        <th>S.NO</th>
                        <th>EVENT</th>
                        <th>ACTION</th>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Show Loader
        $("#loader-container").fadeIn(500);

        $(document).ready(function() {
            // Get All Events
            $.ajax({
                url: './backend/eventOperation.php',
                type: 'GET',
                data: {
                    getAllEvents: 1
                },
                success: function(response) {
                    var data = JSON.parse(response);

                    // Hide Loader
                    $("#loader-container").fadeOut(500);

                    if (data.length == 0) {
                        $('.attendanceLink table').append('<tr><td colspan="4">No Events Found</td></tr>');
                    } else {
                        for (var i = 0; i < data.length; i++) {
                            $('.attendanceLink table').append(attendanceLinkRow(i, data[i].EID, data[i].EVENT_NAME));
                            $('.userListLink table').append(userListLinkRow(i, data[i].EID, data[i].EVENT_NAME));
                        }
                    }
                }
            });
        });

        function attendanceLinkRow(i, eid, eventName) {
            var link = window.location.href;
            link = link.replace('admin/generateURL.php', 'attendance.php?eid=' + eid);

            return `<tr>
                        <td>${i+1}</td>
                        <td>
                            <a href="${link}" target="_blank">${eventName}</a>
                        </td>
                        <td>
                            <button class="btn-success" onclick="copyLink(this)">
                                <i class="fa fa-copy" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>`;
        }

        function userListLinkRow(i, eid, eventName) {
            var link = window.location.href;
            var key = encryptPassword( "1234", eid+eventName);
            link = link.replace('admin/generateURL.php', `userList.php?eid=${eid}&key=${key}`);

            return `<tr>
                        <td>${i+1}</td>
                        <td>
                            <a href="${link}" target="_blank">${eventName}</a>
                        </td>
                        <td>
                            <button class="btn-success" onclick="copyLink(this)">
                                <i class="fa fa-copy" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>`;
        }

        function copyLink(e) {
            var link = $(e).parent().prev().children().attr('href');
            var temp = $("<input>");
            $("body").append(temp);
            temp.val(link).select();
            document.execCommand("copy");
            temp.remove();
            message('Link Copied', 'success')
        }
    </script>

</body>

</html>