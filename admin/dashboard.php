<?php include "./backend/authenticate.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GIZZMO 2K24 | Erode Sengunthar Engineering College</title>
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
                <i class="fa fa-home" aria-hidden="true"></i>
                <h2>Dashboard</h2>
            </div>
            <div class="line"></div>
        </div>

        <div class="content">
            <div class="table">
                <table>
                    <tr>
                        <th>Field</th>
                        <th>Count</th>
                    </tr>
                    <tr class="users">
                        <td>Total Paticipants</td>
                        <td>0</td>
                    </tr>
                    <tr class="paid">
                        <td>Total Payments Completed</td>
                        <td>0</td>
                    </tr>
                    <tr class="verifiedPayments">
                        <td>Total Verified Payments</td>
                        <td>0</td>
                    </tr>
                    <tr class="events">
                        <td>Events</td>
                        <td>0</td>
                    </tr>
                    <?php
                        include "../config.php";
                        $events = mysqli_query($conn, "SELECT * FROM events");
                        while ($event = mysqli_fetch_assoc($events)) {
                            echo "<tr class='".$event['EID']."'><td>".$event['EVENT_NAME']."</td><td></td></tr>";
                        }
                    ?>
                </table>
            </div>
        </div>
    </div>


    <script>
        // Show Loader
        $("#loader-container").fadeIn(100);

        $(document).ready(function() {
            $.ajax({
                url: 'backend/data.php',
                type: 'POST',
                data: {
                    data: true
                },
                success: function(response) {
                    var data = JSON.parse(response);

                    // Update Table
                    for (var key in data) {
                        var value = data[key] || 0;
                        $(`tr.${key} td:nth-child(2)`).text(value);
                    }
                    // Counter Animation
                    $("table tr td:nth-child(2)").each(function() {
                        var $this = $(this);
                        var countTo = $this.text();
                        $this.text("0");
                        $({ countNum: $this.text() }).animate({ countNum: countTo }, {
                            duration: 1000,
                            easing: "linear",
                            step: function() {
                                $this.text(Math.floor(this.countNum));
                            },
                            complete: function() {
                                $this.text(this.countNum);
                            }
                        });
                    });

                    // Hide Loader
                    $("#loader-container").fadeOut(500);
                }
            });

        });
    </script>
</body>
</html>