<?php include "./backend/authenticate.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papers List - GIZZMO | Erode Sengunthar Engineering College</title>
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
                <i class="fa fa-file-powerpoint" aria-hidden="true"></i>
                <h2>Papers List</h2>
            </div>
            <button class="btn-info" id="downloadData" onclick="showDownloadForm()">
                <i class="fa fa-download" aria-hidden="true"></i>
                &nbsp;
                DOWNLOAD
            </button>
            <div class="line"></div>
        </div>

        <div class="content">

            <form class="search-form">
                <div class="input-group">
                    <input type="text" id="search" name="search" value="">
                    <label for="search">Search<label>
                </div>
                <div class="input-group">
                    <select id="status">
                        <option value="all">All</option>
                        <option value="UPLOADED">UPLOADED</option>
                        <option value="UNDER REVIEW">UNDER REVIEW</option>
                        <option value="ACCEPTED">ACCEPTED</option>
                        <option value="REJECTED">REJECTED</option>
                    </select>
                    <label for="filter">Filter</label>
                </div>
            </form>

            <div class="table">
                <table>
                    <tr>
                        <th>S.NO</th>
                        <th>Paper ID</th>
                        <th>Team Leader</th>
                        <th>Member 1</th>
                        <th>Member 2</th>
                        <th>Paper Topic</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </table>
            </div>
        </div>

        <div id="downloadForm">
            <form>
                <h2>Download Data</h2>
                <div class="input-group">
                    <select id="format">
                        <option value="csv">Table Data</option>
                    </select>
                    <label for="format">Select Data</label>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn-danger" onclick="closeDownloadForm()">Cancel</button>
                    <button type="button" class="btn-success" onclick="download()">Download</button>
                </div>
            </form>
        </div>
    </div>

    <div class="fileViewerContainer">
        <iframe src="" id="fileViewer">
        </iframe>
    </div>

    <script>
        // Show Loader
        $("#loader-container").fadeIn(500);

        $(document).ready(function() {
            // Get All Papers
            $.ajax({
                url: './backend/paperOperation.php',
                type: 'GET',
                data: {
                    getAllPapers: true
                },
                success: function(data) {
                    data = JSON.parse(data);

                    // Hide Loader
                    $("#loader-container").fadeOut(500);

                    var table = $('table');

                    if (data.papers == null) {
                        table.append('<tr><td colspan="7">No Papers Found</td></tr>');
                    } else {

                        if (data.status == 'success') {
                            var papers = data.papers;

                            papers.forEach(function(paper, index) {
                                table.append(`
                                    <tr id="${paper.PID}">
                                        <td>${index + 1}</td>
                                        <td>${paper.PID}</td>
                                        <td>
                                            <span class='text'>
                                                ${paper.TL_NAME}
                                            </span>
                                            <button class="btn-info" onclick="getUserDetails('${paper.TL_ID}')">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                        <td>
                                        ${paper.MEMBER_1 == null ? '<span class="text">-</span>' : 
                                            `<span class="text">
                                                ${paper.MEMBER_1}
                                            </span>
                                            <button class="btn-info" onclick="getUserDetails('${paper.M1_ID}')">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </button>`}
                                        </td>
                                        <td>
                                            ${paper.MEMBER_2 == null ? '<span class="text">-</span>' : 
                                            `<span class="text">
                                                ${paper.MEMBER_2}
                                            </span>
                                            <button class="btn-info" onclick="getUserDetails('${paper.M2_ID}')">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </button>`}
                                        </td>
                                        <td>${paper.P_TITLE}</td>
                                        <td><b>${paper.STATUS}</b></td>
                                        <td>
                                            <button class="btn-info" onclick="viewPaper('${paper.PID}')">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </button>
                                            <button class="btn-success" onclick="updateStatus('${paper.PID}', 'ACCEPTED')">
                                            <i class="fa-solid fa-check" aria-hidden="true"></i>
                                            </button>
                                            <button class="btn-danger" onclick="updateStatus('${paper.PID}', 'REJECTED')">
                                            <i class="fa fa-ban" aria-hidden="true"></i>
                                            </button>
                                            <button class="btn-warning" onclick="sendMail('${paper.PID}')">
                                            <i class="fa fa-envelope" aria-hidden="true"></i>
                                            </button>
                                            <button class="btn-info" onclick="downloadPaper('${paper.PID}')">
                                            <i class="fa fa-download" aria-hidden="true"></i>
                                            </button>
                                            <button class="btn-danger" onclick="deletePaper('${paper.PID}')">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `);
                            });
                        } else {
                            message(data.message, 'error');
                        }

                    }
                }
            });

            var colors = {
                "UPLOADED": "var(--secondary)",
                "UNDER REVIEW": "var(--warning)",
                "ACCEPTED": "var(--success)",
                "REJECTED": "var(--danger)"
            }

            $("td:contains('UPLOADED')").css('color', colors['UPLOADED']);
            $("td:contains('UNDER REVIEW')").css('color', colors['UNDER REVIEW']);
            $("td:contains('ACCEPTED')").css('color', colors['ACCEPTED']);
            $("td:contains('REJECTED')").css('color', colors['REJECTED']);
        });

        // Get User Details
        function getUserDetails(uid) {
            $.ajax({
                url: "backend/userOperation.php",
                type: "POST",
                data: {
                    getUserDetails: true,
                    uid: uid
                },
                success: function(response) {
                    var data = JSON.parse(response);
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
                                    <button class="btn-danger" onclick="closeUserDetails()">
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
                                        <h3>Events</h3></h3>
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

        // View Paper
        function viewPaper(pid) {
            $.ajax({
                url: "./backend/paperOperation.php",
                type: "GET",
                data: {
                    viewPaper: true,
                    pid: pid
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.status == "success") {
                        var paper = data.paper;
                        console.log(paper);
                        openFile(paper.FILE_PATH);
                    } else {
                        message(data.message, data.status);
                    }
                }
            });
        }

        // Update Paper Status
        function updateStatus(pid, status) {
            if (confirm(`Are you sure you want to update the status of Paper ID: ${pid} to ${status}?`) == false) {
                return;
            }
            $.ajax({
                url: "./backend/paperOperation.php",
                type: "POST",
                data: {
                    updateStatus: true,
                    pid: pid,
                    status: status
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    message(data.message, data.status);
                    if (data.status == "success") {
                        $(`#${pid} td:nth-child(7)`).html(`<b>${status}</b>`);
                    }
                }
            });
        }


        // Open File Viewer
        function openFile(filePath) {
            var fileType = filePath.split('.').pop().toLowerCase();

            if (fileType == 'pdf') {
                // filePath = filePath.replace('./', '../');
                filePath = filePath.replace('../', window.location.origin + '/gizzmo/');
            } else if (fileType == 'doc' || fileType == 'docx') {
                filePath = filePath.replace('./', window.location.origin + '/gizzmo/');

                filePath = 'https://docs.google.com/gview?url=' + filePath + '&embedded=true';
            }

            $(".fileViewerContainer").css('display', 'flex');
            $("#fileViewer").attr('src', filePath);

        }

        // Close File Viewer
        $(".fileViewerContainer").click(function(event) {
            if (event.target == this) {
                $(".fileViewerContainer").css('display', 'none');
                $("#fileViewer").attr('src', '');
            }
        });

        // Download Paper
        function downloadPaper(pid) {
            $.ajax({
                url: "./backend/paperOperation.php",
                type: "GET",
                data: {
                    viewPaper: true,
                    pid: pid
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.status == "success") {
                        var filePath = data.paper.FILE_PATH;
                        var a = document.createElement('a');
                        a.href = filePath;
                        a.download = filePath.substr(filePath.lastIndexOf('/') + 1);
                        a.click();
                    } else {
                        message(data.message, data.status);
                    }
                }
            });
        }

        // Delete Paper
        function deletePaper(pid) {
            if (confirm(`Are you sure you want to delete Paper ID: ${pid}?`) == false) {
                return;
            }
            $.ajax({
                url: "./backend/paperOperation.php",
                type: "POST",
                data: {
                    deletePaper: true,
                    pid: pid
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    message(data.message, data.status);
                    if (data.status == "success") {
                        $(`#${pid}`).remove();
                    }
                }
            });
        }

        // Send Mail
        function sendMail(pid) {
            if (confirm(`Are you sure you want to send mail to Paper ID: ${pid}?`) == false) {
                return;
            }
            $.ajax({
                url: "./backend/paperOperation.php",
                type: "POST",
                data: {
                    sendMail: true,
                    pid: pid
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    message(data.message, data.status);
                }
            });
        }


        // Search Functionality
        $("#search").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("table tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        $("#status").on('change', function() {
            var value = $(this).val().toLowerCase();
            $("table tr:not(:first-child)").filter(function() {
                if (value == 'all') {
                    $(this).toggle(true);
                } else {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                }
            });
        });

        // HTML Table Export as Excel or CSV File
        function showDownloadForm() {
            var downloadForm = document.getElementById('downloadForm');
            downloadForm.style.display = 'flex';
        }

        function closeDownloadForm() {
            var downloadForm = document.getElementById('downloadForm');
            downloadForm.style.display = 'none';
        }

        function download() {

            var format = document.getElementById('format').value;
            var table = document.querySelector('table');
            var html = table.outerHTML;

            // Show Loader
            $("#loader-container").fadeIn(500);

            if (format == 'csv') {
                var csv = [];
                var rows = document.querySelectorAll("table tr");
                for (var i = 0; i < rows.length; i++) {
                    var row = [],
                        cols = rows[i].querySelectorAll("td, th");
                    for (var j = 0; j < cols.length; j++)
                        row.push(cols[j].innerText);
                    csv.push(row.join(","));
                }

                var csvFile = new Blob([csv.join("\n")], {
                    type: "text/csv"
                });

                var downloadLink = document.createElement("a");
                downloadLink.download = "papers.csv";
                downloadLink.href = window.URL.createObjectURL(csvFile);
                downloadLink.style.display = "none";

                document.body.appendChild(downloadLink);
                downloadLink.click();

                // Hide Loader
                $("#loader-container").fadeOut(500);
            }
        }
    </script>
</body>

</html>