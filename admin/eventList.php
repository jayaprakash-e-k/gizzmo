<?php include "./backend/authenticate.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events List - GIZZMO | Erode Sengunthar Engineering College</title>
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
                <h2>Events List</h2>
            </div>
            <button onclick="openForm()" class="btn-info">Add Event</button>
            <div class="line"></div>
        </div>

        <form class="search-form">
            <div class="input-group">
                <input type="text" id="search" name="search" value="">
                <label for="search">Search<label>
            </div>
            <div class="input-group">
                <select name="filter" id="filter">
                    <option value="-1">All</option>
                    <option value="OPEN">Registration Opened</option>
                    <option value="CLOSE">Registration Closed</option>
                </select>
                <label for="filter">Filter</label>
            </div>
        </form>

        <div class="content">
            <div class="table">
                <table>
                    <tr>
                        <th>S.NO</th>
                        <th>Event ID</th>
                        <th>Event Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Event details -->
        <div class="event-details-popup">
            <div class="event-details">
                <div class="event-details-title">
                    <h2>Event Details</h2>
                    <button class="btn-danger" onclick="closeEventDetails()">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="event-details-content">
                    <table>
                        <tr>
                            <td>Event Name</td>
                            <td id="eventNameTD"></td>
                        </tr>
                        <tr>
                            <td>Event Date</td>
                            <td id="eventDateTD"></td>
                        </tr>
                        <tr>
                            <td>Event Description</td>
                            <td id="eventDescriptionTD"></td>
                        </tr>
                        <tr>
                            <td>Event Rules</td>
                            <td id="eventRulesTD"></td>
                        </tr>
                        <tr>
                            <td>Team Size</td>
                            <td id="teamSizeTD"></td>
                        </tr>
                        <tr>
                            <td>Coordinator Name</td>
                            <td id="coNameTD"></td>
                        </tr>
                        <tr>
                            <td>Coordinator Number</td>
                            <td id="coNoTD"></td>
                        </tr>
                        <tr>
                            <td>Event Image</td>
                            <td id="eventImageTD">
                                <img src="" alt="Event Related Image">
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Form -->
        <div class="form-popup">
            <form method="post">
                <h2>Add Event</h2>
                <div class="input-group">
                    <select name="eventType" id="eventType">
                        <option value="T">Technical</option>
                        <option value="W">Workshop</option>
                        <option value="N">Non-Technical</option>
                    </select>
                    <label for="eventType">Event Type</label>
                    <span class="error eventType"></span>
                </div>
                <div class="input-group">
                    <input type="text" id="eventName" name="eventName" value="">
                    <label for="eventName">Event Name</label>
                    <span class="error eventName"></span>
                </div>
                <div class="input-group">
                    <textarea name="eventDescription" id="eventDescription"></textarea>
                    <label for="eventDescription">Event Description</label>
                    <span class="error eventDescription"></span>
                </div>
                <div class="input-group">
                    <textarea name="eventRules" id="eventRules"></textarea>
                    <label for="eventRules">Event Rules</label>
                    <span class="error eventRules"></span>
                </div>
                <div class="input-group">
                    <input type="date" name="eventDate" id="eventDate" value="">
                    <label for="eventDate">Event Date</label>
                    <span class="error eventDate"></span>
                </div>
                <div class="input-group">
                    <input type="time" name="eventStartTime" id="eventStartTime" value="">
                    <label for="eventStartTime">Event Start Time</label>
                    <span class="error eventStartTime"></span>
                </div>
                <div class="input-group">
                    <input type="time" name="eventEndTime" id="eventEndTime" value="">
                    <label for="eventEndTime">Event End Time</label>
                    <span class="error eventEndTime"></span>
                </div>
                <div class="input-group">
                    <input type="text" name="eventVenue" id="eventVenue" value="">
                    <label for="eventVenue">Event Venue</label>
                    <span class="error eventVenue"></span>
                </div>
                <div class="input-group">
                    <input type="number" id="teamSize" name="teamSize" value="">
                    <label for="teamSize">Team Size</label>
                    <span class="error team"></span>
                </div>
                <div class="input-group">
                    <input type="text" id="coName" name="coName" value="">
                    <label for="coName">Co-ordinator Name</label>
                    <span class="error coName"></span>
                </div>
                <div class="input-group">
                    <input type="tel" id="coNo" name="coNo" value="">
                    <label for="coNo">Co-ordinator Contact No</label>
                    <span class="error coNo"></span>
                </div>
                <div class="input-group">
                    <input type="file" name="eventImage" id="eventImage" value="">
                    <label for="eventImage">Event Image</label>
                    <span class="note">Note: Only jpg, jpeg, png, gif formats supported.</span>
                    <span class="error eventImage"></span>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn-danger" onclick="closeForm()">Close</button>
                    <button type="button" class="btn-success" id="submitBtn" onclick="addEvent()">Add Event</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Show Loader
        $("#loader-container").fadeIn(100);

        // Get All Events
        $.ajax({
            url: './backend/eventOperation.php',
            type: 'GET',
            data: {
                getAllEvents: 1
            },
            success: function(data) {
                data = JSON.parse(data);
                
                // Hide Loader
                $("#loader-container").fadeOut(500);

                if (data.length == 0) {
                    $(".table table").append(`
                        <tr>
                            <td colspan="5">No Events Found</td>
                        </tr>
                    `);
                    return;
                }

                // Append Data to Table
                data.forEach((event, index) => {
                    $(".table table").append(tableRowStructure(index + 1, event.EID, event.EVENT_NAME, event.STATUS));
                });

            }
        });

        // Open Form
        function openForm() {
            $(".form-popup").css("display", "flex");
        }

        // Close Form
        function closeForm() {
            $(".form-popup").css("display", "none");
        }

        // Table Row Structure
        function tableRowStructure(sno, eid, eventName, status) {
            return `
                <tr id="${eid}">
                    <td>${sno}</td>
                    <td>${eid}</td>
                    <td>${eventName}</td>
                    <td>${status}</td>
                    <td>
                        <button class="btn-info" onclick="viewEventDetails('${eid}')">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </button>
                        <button class="btn-warning status" onclick="lockRegistration('${eid}')">
                            ${status == 'CLOSE' ? '<i class="fa fa-unlock" aria-hidden="true"></i>' : '<i class="fa fa-lock" aria-hidden="true"></i>'}
                        </button>
                        <button class="btn-info" onclick="openEditForm('${eid}')">
                            <i class="fa fa-pencil" aria-hidden="true"></i>
                        </button>
                        <button class="btn-danger" onclick="deleteEvent('${eid}')">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                    </td>
                </tr>
            `;
        }

        // Add Event
        function addEvent() {
            let eventType = $("#eventType").val();
            let eventName = $("#eventName").val();
            let eventDescription = $("#eventDescription").val();
            let eventRules = $("#eventRules").val();
            let teamSize = $("#teamSize").val();
            let eventDate = $("#eventDate").val();
            let eventStartTime = $("#eventStartTime").val();
            let eventEndTime = $("#eventEndTime").val();
            let eventVenue = $("#eventVenue").val();
            let coName = $("#coName").val();
            let coNo = $("#coNo").val();
            let eventImage = $("#eventImage").val();

            // Validation
            if (eventType == "") {
                $(".eventType").text("Event Type is Required");
                return;
            } else {
                $(".eventType").text("");
            }

            if (eventName == "") {
                $(".eventName").text("Event Name is Required");
                return;
            } else {
                $(".eventName").text("");
            }

            if (eventDescription == "") {
                $(".eventDescription").text("Event Description is Required");
                return;
            } else {
                $(".eventDescription").text("");
            }

            if (teamSize == "") {
                $(".team").text("Team Size is Required");
                return;
            } else {
                $(".team").text("");
            }

            if (eventDate == "") {
                $(".eventDate").text("Event Date is Required");
                return;
            } else {
                $(".eventDate").text("");
            }

            if (eventStartTime == "") {
                $(".eventStartTime").text("Event Start Time is Required");
                return;
            } else {
                $(".eventStartTime").text("");
            }

            if (eventEndTime == "") {
                $(".eventEndTime").text("Event End Time is Required");
                return;
            } else {
                $(".eventEndTime").text("");
            }

            if (eventVenue == "") {
                $(".eventVenue").text("Event Venue is Required");
                return;
            } else {
                $(".eventVenue").text("");
            }

            if (coName == "") {
                $(".coName").text("Coordinator Name is Required");
                return;
            } else {
                $(".coName").text("");
            }

            if (coNo == "") {
                $(".coNo").text("Coordinator Contact No is Required");
                return;
            } else {
                $(".coNo").text("");
            }

            if (eventImage == "") {
                $(".eventImage").text("Event Image is Required");
                return;
            } else {
                $(".eventImage").text("");
            }

            var formData = new FormData($(".form-popup form")[0]);
            formData.append("addEvent", true);

            // Show Loader
            $("#loader-container").fadeIn(500);

            // Add Event
            $.ajax({
                url: './backend/eventOperation.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(data) {

                    // Hide Loader
                    $("#loader-container").fadeOut(500);

                    data = JSON.parse(data);
                    if (data.status == 'success') {
                        message("Event Added Successfully", "success");
                        closeForm();

                        // Append Data to Table
                        $(".table table").append(tableRowStructure($(".table table tr").length, data.EID, eventName, "OPEN"));

                        $(".form-popup form")[0].reset();
                        setTimeout(window.location.reload(), 1000);

                    } else {
                        message(data.message, "error");
                        console.log(data.error);
                    }

                }
            });

        }

        // Edit Event
        function openEditForm(EID) {
            if (!confirm("Are you sure want to edit?")) return;

            openForm();
            $(".form-popup form h2").text("Edit Event");
            $(".form-popup form .submit").text("Update Event");
            $(".form-popup form .submit").attr("onclick", `updateEvent('${EID}')`);

            // Show Loader
            $("#loader-container").fadeIn(500);

            // Get Event Details for Edit
            $.ajax({
                url: './backend/eventOperation.php',
                type: 'GET',
                data: {
                    getEventDetails: 1,
                    EID: EID
                },
                success: function(data) {
                    data = JSON.parse(data);

                    // Hide Loader
                    $("#loader-container").fadeOut(500);

                    if (data.status == "success") {
                        setEventDetails(data.event.EID, data.event.EVENT_NAME, data.event.DESCRIPTION, data.event.RULES, data.event.TEAM_SIZE, data.event.EVENT_DATE, data.event.START, data.event.END, data.event.VENUE, data.event.CO_NAME, data.event.CO_NO);
                        $("#submitBtn").text("Update Event");
                        $("#submitBtn").attr("onclick", `updateEvent('${EID}')`);
                    } else {
                        message(data.message, "error");
                        console.log(data.error);
                        $("#submitBtn").text("Add Event");
                        $("#submitBtn").attr("onclick", `addEvent('${EID}')`);
                    }
                }
            });
        }

        // Set Event Details
        function setEventDetails(eid, eventName, eventDescription, eventRules, teamSize, eventDate, eventStartTime, eventEndTime, eventVenue, coName, coNo) {
            $("#eventType").val(eid.charAt(eid.length - 1).toUpperCase());
            $("#eventType").attr("value", eid.charAt(eid.length - 1).toUpperCase());

            $("#eventName").val(eventName);
            $("#eventName").attr("value", eventName);

            $("#eventDescription").html(eventDescription);
            $("#eventDescription").attr("value", $("#eventDescription").html());

            $("#eventRules").html(eventRules);
            $("#eventRules").attr("value", $("#eventRules").html());

            $("#teamSize").val(teamSize);
            $("#teamSize").attr("value", teamSize);

            $("#eventDate").val(eventDate);
            $("#eventDate").attr("value", eventDate);

            $("#eventStartTime").val(eventStartTime);
            $("#eventStartTime").attr("value", eventStartTime);

            $("#eventEndTime").val(eventEndTime);
            $("#eventEndTime").attr("value", eventEndTime);

            $("#eventVenue").val(eventVenue);
            $("#eventVenue").attr("value", eventVenue);

            $("#coName").val(coName);
            $("#coName").attr("value", coName);

            $("#coNo").val(coNo);
            $("#coNo").attr("value", coNo);
        }

        // Update Event
        function updateEvent(EID) {

            let eventType = $("#eventType").val();
            let eventName = $("#eventName").val();
            let eventDescription = $("#eventDescription").val();
            let eventRules = $("#eventRules").val();
            let teamSize = $("#teamSize").val();
            let eventDate = $("#eventDate").val();
            let eventStartTime = $("#eventStartTime").val();
            let eventEndTime = $("#eventEndTime").val();
            let eventVenue = $("#eventVenue").val();
            let coName = $("#coName").val();
            let coNo = $("#coNo").val();

            // Validation
            if (eventType == "") {
                $(".eventType").text("Event Type is Required");
                return;
            } else {
                $(".eventType").text("");
            }

            if (eventName == "") {
                $(".eventName").text("Event Name is Required");
                return;
            } else {
                $(".eventName").text("");
            }

            if (eventDescription == "") {
                $(".eventDescription").text("Event Description is Required");
                return;
            } else {
                $(".eventDescription").text("");
            }

            if (teamSize == "") {
                $(".team").text("Team Size is Required");
                return;
            } else {
                $(".team").text("");
            }

            if (eventDate == "") {
                $(".eventDate").text("Event Date is Required");
                return;
            } else {
                $(".eventDate").text("");
            }

            if (eventStartTime == "") {
                $(".eventStartTime").text("Event Start Time is Required");
                return;
            } else {
                $(".eventStartTime").text("");
            }

            if (eventEndTime == "") {
                $(".eventEndTime").text("Event End Time is Required");
                return;
            } else {
                $(".eventEndTime").text("");
            }

            if (eventVenue == "") {
                $(".eventVenue").text("Event Venue is Required");
                return;
            } else {
                $(".eventVenue").text("");
            }

            if (coName == "") {
                $(".coName").text("Coordinator Name is Required");
                return;
            } else {
                $(".coName").text("");
            }

            if (coNo == "") {
                $(".coNo").text("Coordinator Contact No is Required");
                return;
            } else {
                $(".coNo").text("");
            }

            var formData = new FormData($(".form-popup form")[0]);
            formData.append("updateEvent", true);
            formData.append("EID", EID);

            if ($("#eventImage").val() == "") {
                formData.delete("eventImage");
            }

            // Show Loader
            $("loader-container").fadeIn(500);

            // Update Event
            $.ajax({
                url: './backend/eventOperation.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(data) {
                    data = JSON.parse(data);

                    // Hide Loader
                    $("#loader-container").fadeOut(500);

                    if (data.status == 'success') {
                        message("Event Updated Successfully", "success");
                        closeForm();

                        // Update Table
                        $(`#${EID} td:nth-child(3)`).text(eventName);

                        $(".form-popup form")[0].reset();
                        setTimeout(window.location.reload(), 1000);

                    } else {
                        message(data.message, "error");
                        console.log(data.error);
                    }

                    $("#submitBtn").text("Add Event");
                    $("#submitBtn").attr("onclick", `addEvent('${EID}')`);
                }
            });
        }

        // Delete Event
        function deleteEvent(EID) {
            if (confirm("Are you sure want to delete?")) {

                // Show Loader
                $("#loader-container").fadeIn(500);

                $.ajax({
                    url: './backend/eventOperation.php',
                    type: 'POST',
                    data: {
                        deleteEvent: true,
                        EID: EID
                    },
                    success: function(data) {
                        data = JSON.parse(data);

                        // Hide Loader
                        $("#loader-container").fadeOut(500);

                        if (data.status == 'success') {
                            message("Event Deleted Successfully", "success");
                            $(`#${EID}`).remove();
                        } else {
                            message(data.message, "error");
                            console.log(data.error);
                        }
                    }
                });
            }
        }

        // Lock Event
        function lockRegistration(EID) {
            if (confirm("Are you sure want to lock registration?")) {

                // Show Loader
                $("#loader-container").fadeIn(500);

                $.ajax({
                    url: './backend/eventOperation.php',
                    type: 'POST',
                    data: {
                        lockEvent: true,
                        EID: EID
                    },
                    success: function(data) {
                        data = JSON.parse(data);

                        // Hide Loader
                        $("#loader-container").fadeOut(500);

                        if (data.status == 'success') {
                            message("Event Locked Successfully", "success");
                            $(`#${EID} td:nth-child(4)`).text($(`#${EID} td:nth-child(4)`).text() == 'OPEN' ? 'CLOSE' : 'OPEN');
                            $(`#${EID} td:nth-child(5) button.status`).html($(`#${EID} td:nth-child(4)`).text() == 'CLOSE' ? '<i class="fa fa-unlock" aria-hidden="true"></i>' : '<i class="fa fa-lock" aria-hidden="true"></i>');
                        } else {
                            message(data.message, "error");
                            console.log(data.error);
                        }
                    }
                });
            }
        }

        // View Event Details
        function viewEventDetails(EID) {
            // Show Loader
            $("#loader-container").fadeIn(500);

            $.ajax({
                url: './backend/eventOperation.php',
                type: 'GET',
                data: {
                    getEventDetails: 1,
                    EID: EID
                },
                success: function(data) {
                    data = JSON.parse(data);

                    // HIde Loader
                    $("#loader-container").fadeOut(500);

                    if (data.status == "success") {

                        $(".event-details-popup").css("display", "flex");
                        $("#eventNameTD").text(data.event.EVENT_NAME ? data.event.EVENT_NAME : "No Name");
                        $("#eventDescriptionTD").html(data.event.DESCRIPTION ? data.event.DESCRIPTION : "No Description");
                        $("#eventRulesTD").html(data.event.RULES ? data.event.RULES : "No Rules");
                        $("#teamSizeTD").text(data.event.TEAM_SIZE ? data.event.TEAM_SIZE : "No Team Size");
                        $("#eventDateTD").text(data.event.EVENT_DATE ? (data.event.EVENT_DATE + " - " + data.event.START + " to " + data.event.END): "No Event Date Available");
                        $("#coNameTD").text(data.event.CO_NAME ? data.event.CO_NAME : "No Coordinator Added");
                        $("#coNoTD").text(data.event.CO_NO ? data.event.CO_NO : "No Coordinator Added");
                        $("#eventImageTD img").attr("src", data.event.FILE ? `../uploads/events/${data.event.FILE}` : "./assets/img/no-image.png");

                    } else {
                        message(data.message, "error");
                        console.log(data.error);

                    }
                }
            });
        }

        // Close Event Details
        function closeEventDetails() {
            $(".event-details-popup").css("display", "none");
        }

        // Search Function
        $("#search").on("keyup", function(e) {
            if (e.key == "Enter") {
                e.preventDefault();
            }

            let search = $(this).val().toLowerCase();
            $(".table table tr:not(:first-child)").each(function() {
                if ($(this).text().toLowerCase().includes(search)) {
                    $(this).css("display", "table-row");
                } else {
                    $(this).css("display", "none");
                }
            });
        });

        // Filter Function
        $("#filter").on("change", function() {
            let filter = $(this).val();
            $(".table table tr:not(:first-child)").each(function() {
                if (filter == -1) {
                    $(this).css("display", "table-row");
                } else if ($(this).find("td:nth-child(4)").text().toLowerCase().includes(filter.toLowerCase())) {
                    $(this).css("display", "table-row");
                } else {
                    $(this).css("display", "none");
                }
            });
        });
    </script>
</body>

</html>