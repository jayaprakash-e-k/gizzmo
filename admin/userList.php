<?php include "./backend/authenticate.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participants List - GIZZMO | Erode Sengunthar Engineering College</title>
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
                <i class="fa fa-users" aria-hidden="true"></i>
                <h2>Participants List</h2>
            </div>
            <button class="btn-info" id="downloadData" onclick="showDownloadForm()">
                <i class="fa fa-download" aria-hidden="true"></i>
                &nbsp;
                DOWNLOAD
            </button>
            <div class="line"></div>
        </div>

        <form class="search-form">
            <div class="input-group">
                <input type="text" id="search" name="search" value="">
                <label for="search">Search<label>
            </div>
            <div class="input-group">
                <select name="filter" id="filter">
                    <option value="1">All</option>
                    <option value="No Events">No Events</option>
                </select>
            </div>
        </form>

        <div class="content">
            <div class="table">
                <table>
                    <tr>
                        <th>S.NO</th>
                        <th>NAME</th>
                        <th>PHONE</th>
                        <th>EMAIL</th>
                        <th>EVENTS</th>
                        <th>ACTION</th>
                    </tr>
                </table>
            </div>
        </div>

        <div id="downloadForm">
            <form>
                <h2>Download Data</h2>
                <div class="input-group">
                    <select name="team" id="teamFilter">
                        <option value="-1">Select Type</option>
                        <option value="IN">Individual</option>
                        <option value="TEAM">Team</option>
                    </select>
                </div>
                <div class="input-group">
                    <select name="event" id="eventFilter">
                        <option value="-1">Select Event</option>
                        <option value="ALL">All</option>
                    </select>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn-danger" onclick="closeDownloadForm()">Cancel</button>
                    <button type="button" class="btn-success" onclick="download()">Download</button>
                </div>
            </form>
        </div>
    </div>



    <script>
        // Show Loader
        $("#loader-container").fadeIn(500);

        $(document).ready(function() {
            // Get all registered users
            $.ajax({
                url: "backend/userOperation.php",
                type: "POST",
                data: {
                    getUsers: true
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    var table = $("table");

                    // Hide Loader
                    $("#loader-container").fadeOut(500);

                    if (data.users == null) {
                        table.append(`
                    <tr>
                        <td colspan="6">No Participants Found</td>
                    </tr>
                    `);
                        return;
                    }

                    if (data.status == "success") {
                        var users = data.users;
                        for (var i = 0; i < users.length; i++) {
                            table.append(getTableRowTemplate(users[i]));
                        }
                    } else {
                        message(data.message, data.status)
                    }
                }
            });

            // Get all events and append in filter option
            $.ajax({
                url: "./backend/eventOperation.php",
                type: "GET",
                data: {
                    getAllEvents: 1
                },
                success: function(response) {
                    var events = JSON.parse(response);
                    if (events.length == 0) {
                        return;
                    }

                    for (var i = 0; i < events.length; i++) {
                        $("#filter").append(`<option value="${events[i].EVENT_NAME}">${events[i].EVENT_NAME}</option>`);
                        $("#eventFilter").append(`<option value="${events[i].EID}">${events[i].EVENT_NAME}</option>`);
                    }
                }
            });
        });

        // Table Row Template
        function getTableRowTemplate(data) {
            return `
            <tr id='${data.UID}'>
                <td>${data.SNO}</td>
                <td>${data.NAME}</td>
                <td>${data.PHONE}</td>
                <td>${data.EMAIL}</td>
                <td>${data.EVENTS}</td>
                <td>
                    <button onclick="getUserDetails('${data.UID}')" class="btn-info">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                    </button>
                    <button onclick="deleteUser('${data.UID}')" class="btn-danger">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>
            `;
        }

        // Delete User
        function deleteUser(uid) {
            var confirmDelete = confirm("Are you sure you want to delete this user?");
            if (confirmDelete) {
                // Show Loader
                $("#loader-container").fadeIn(500);

                $.ajax({
                    url: "backend/userOperation.php",
                    type: "POST",
                    data: {
                        deleteUser: true,
                        uid: uid
                    },
                    success: function(response) {
                        var data = JSON.parse(response);

                        // Hide Loader
                        $("#loader-container").fadeOut(500);

                        if (data.status == "success") {
                            message(data.message, data.status);
                            $("#" + uid).remove();
                        } else {
                            message(data.message, data.status);
                        }
                    }
                });
            }
        }

        // Get User Details
        function getUserDetails(uid) {
            // Show Loader
            $("#loader-container").fadeIn(500);

            $.ajax({
                url: "backend/userOperation.php",
                type: "POST",
                data: {
                    getUserDetails: true,
                    uid: uid
                },
                success: function(response) {
                    var data = JSON.parse(response);

                    // Hide Loader
                    $("#loader-container").fadeOut(500);

                    if (data.status == "success") {
                        var user = data.user;
                        var events = user.EVENTS.split(",");
                        var eventList = "";
                        for (var i = 0; i < events.length; i++) {
                            eventList += `<li>${events[i]}</li>`;
                        }
                        var userDetails = `
                        <div class="user-details">
                            <div class="user-info">
                                <div class="user-info-title">
                                    <h2>User Details</h2>
                                    <button onclick="closeUserDetails()" class="btn-danger">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </button>
                                </div>
                                <div class="user-info-content">
                                    <div class="user-info-item">
                                        <h3>Name</h3>
                                        <p>${user.NAME}</p>
                                    </div>
                                    <div class="user-info-item">
                                        <h3>Phone</h3>
                                        <p>${user.PHONE}</p>
                                    </div>
                                    <div class="user-info-item">
                                        <h3>Email</h3>
                                        <p>${user.EMAIL}</p>
                                    </div>
                                    <div class="user-info-item">
                                        <h3>College</h3>
                                        <p>${user.COLLEGE}</p>
                                    </div>
                                    <div class="user-info-item">
                                        <h3>Department</h3>
                                        <p>${user.DEPARTMENT}</p>
                                    </div>
                                    <div class="user-info-item">
                                        <h3>Events</h3>
                                        <ul>${eventList}</ul>

                                    </div>
                                </div>
                            </div>
                        </div>
                        `;
                        $("body").append(userDetails);
                    } else {
                        message(data.message, data.status);
                    }
                }
            });
        }

        // Close User Details
        function closeUserDetails() {
            $(".user-details").remove();
        }

        // Search
        $("#search").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("table tr:not(:first-child)").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Filter
        $("#filter").on("change", function() {
            let filter = $(this).val();
            $(".table table tr:not(:first-child)").each(function() {
                if (filter == 1) {
                    $(this).css("display", "table-row");
                } else if ($(this).find("td:nth-child(5)").text().toLowerCase().includes(filter.toLowerCase())) {
                    $(this).css("display", "table-row");
                } else {
                    $(this).css("display", "none");
                }
            });
        });

        function showDownloadForm() {
            var downloadForm = document.getElementById('downloadForm');
            downloadForm.style.display = 'flex';
        }

        function closeDownloadForm() {
            var downloadForm = document.getElementById('downloadForm');
            downloadForm.style.display = 'none';
        }

        function download() {
            var event = $("#eventFilter").val();
            var team = $("#teamFilter").val();

            if (event == -1 || team == -1) {
                message("Select Event and Team", "error");
                return;
            }

            // Show Loader
            $("#loader-container").fadeIn(500);

            $.ajax({
                url: "./backend/userOperation.php",
                type: "POST",
                data: {
                    download: "1",
                    event: event,
                    team: team
                },
                success: function(response) {

                    var data = JSON.parse(response);

                    // Hide Loader
                    $("#loader-container").fadeOut(500);

                    if (data.status == "error") {
                        message(data.message, data.status);

                    } else {
                        
                        var csvData = data.data;
                        var fields = data.fields;

                        var csv = '';
                        csv += fields.join(',') + '\n';
                        csvData.forEach(function(row) {
                            // Json to Array
                            row = Object.values(row);
                            csv += row.join(',') + "\n";
                        });

                        var hiddenElement = document.createElement('a');
                        hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(csv);
                        hiddenElement.target = '_blank';
                        hiddenElement.download = 'Participants.csv';
                        hiddenElement.click();
                        
                    }
                } 
            });

            // Hide Loader
            $("#loader-container").fadeOut(500);
        }
    </script>

</body>

</html>