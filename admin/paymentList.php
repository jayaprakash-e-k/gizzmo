<?php include "./backend/authenticate.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments List - GIZZMO | Erode Sengunthar Engineering College</title>
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
                <i class="fa fa-file-invoice" aria-hidden="true"></i>
                <h2>Payments List</h2>
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
                    <option value="-1">All</option>
                    <option value="VERIFIED">Verified</option>
                    <option value="PENDING">Pending</option>
                </select>
                <label for="filter">Filter</label>
            </div>
        </form>

        <div class="content">

            <div class="table">
                <table>
                    <tr>
                        <th>S.NO</th>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Payment Date</th>
                        <th>Transaction ID</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Download Form -->
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

    <div id="screenshot-container">
        <div class="screenshot">
            <div class="header">
                <h2>Payment Screenshot</h2>
                <button onclick="closeScreenshot()" class="btn-danger">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="content">
                <img src="" alt="Payment Screenshot">
            </div>
        </div>
    </div>

    <script>
        // Show Loader
        $("#loader-container").fadeIn(500);

        $(document).ready(function() {

            // Get all payments
            $.ajax({
                url: './backend/paymentOperation.php?getAllPayments=true',
                type: 'GET',
                success: function(response) {
                    var data = JSON.parse(response);

                    // Hide Loader
                    $("#loader-container").fadeOut(500);

                    var table = $('table')
                    var i = 1;

                    if (data.length == 0) {
                        var tr = `<tr><td colspan='8'>No Payments Found</td></tr>`;
                        table.append(tr);
                    }

                    data.forEach(function(payment) {
                        var tr = `
                            <tr id='${payment.UID}'>
                                <td>${i}</td>
                                <td>${payment.UID}</td>
                                <td>${payment.NAME}</td>
                                <td>${payment.PAID_DATE}</td>
                                <td>${payment.TRANSACTION_ID}</td>
                                <td  style="${payment.STATUS == 'VERIFIED' ? "color: var(--success);": "color: var(--danger); "} font-weight: 600;">${payment.STATUS}</td>
                                <td>
                                    ${ payment.STATUS == 'PENDING' ? `
                                        <button class="btn-success" title='Verify Payment' onclick="paymentStatus('${payment.UID}')">
                                            <i class="fa fa-check" aria-hidden="true"></i>
                                        </button>
                                    ` : `
                                        <button class="btn-danger" title='Mark as Pending' onclick="paymentStatus('${payment.UID}')">
                                            <i class="fa fa-times" aria-hidden="true"></i>
                                        </button>
                                    `}
                                    <button class="btn-info" title='View Screenshot' onclick="viewScreenshot('${payment.UID}')">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </button>
                                    <button class="btn-warning" title='Download Payment' onclick="downloadScreenshot('${payment.UID}')">
                                        <i class="fa fa-download" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                        table.append(tr);
                        i++;
                    });
                }
            });
        });

        // Update payment status
        function paymentStatus(uid) {
            var status = document.getElementById(uid).querySelector('td:nth-child(6)').innerText;
            var newStatus = status == 'PENDING' ? 'VERIFIED' : 'PENDING';

            // Show Loader
            $("#loader-container").fadeIn(500);

            $.ajax({
                url: './backend/paymentOperation.php',
                type: 'POST',
                data: {
                    updatePaymentStatus: true,
                    uid: uid,
                    status: newStatus
                },
                success: function(response) {
                    // Hide Loader
                    $("#loader-container").fadeOut(500);

                    if (response == 'success') {
                        $(`#${uid} td:nth-child(6)`).text(newStatus);
                        $(`#${uid} td:nth-child(6)`).css("color", newStatus == 'VERIFIED' ? "var(--success)" : "var(--danger)");
                        $(`#${uid} td:nth-child(7) button:nth-child(1)`).html(newStatus == 'PENDING' ? `
                            <i class="fa fa-check" aria-hidden="true"></i>
                        ` : `
                            <i class="fa fa-times" aria-hidden="true"></i>
                        `);
                    } else {
                        message(response, 'error');
                    }
                }
            });
        }

        // View payment screenshot
        function viewScreenshot(uid) {
            var screenshotContainer = document.getElementById('screenshot-container');
            screenshotContainer.style.display = 'flex';

            var screenshot = document.querySelector('.screenshot .content img');

            // Get Current Page Path
            var path = window.location.pathname;
            var path = path.split('/admin/')[0];

            screenshot.src = `${path}/uploads/payments/${uid}.png`;
        }

        // Download payment screenshot
        function downloadScreenshot(uid) {

            // Show Loader
            $("#loader-container").fadeIn(500);

            // Get Current Page Path
            var path = window.location.pathname;
            var path = path.split('/admin/')[0];

            var a = document.createElement('a');
            a.href = `${path}/uploads/payments/${uid}.png`;

            a.download = `Payment_Screenshot_${uid}.png`;
            a.click();

            // Hide Loader
            $("#loader-container").fadeOut(500);

            a.remove();
        }

        // Close screenshot
        function closeScreenshot() {
            var screenshotContainer = document.getElementById('screenshot-container');
            screenshotContainer.style.display = 'none';
        }

        // Search and Filter
        $('#search').on('input', function() {
            var value = $(this).val().toLowerCase();
            $('table tr:not(:first-child)').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        $('#filter').on('change', function() {
            let filter = $(this).val();
            $(".table table tr:not(:first-child)").each(function() {
                if (filter == -1) {
                    $(this).css("display", "table-row");
                } else if ($(this).find("td:nth-child(6)").text().toLowerCase().includes(filter.toLowerCase())) {
                    $(this).css("display", "table-row");
                } else {
                    $(this).css("display", "none");
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
            var format = $('#format').val();
            var table = $('table');

            // Show Loader
            $("#loader-container").fadeIn(500);
          
            if (format == 'csv'){
                var csv = [];
                var rows = table.find('tr');
                for (var i = 0; i < rows.length; i++) {
                    var row = [],
                        cols = $(rows[i]).find('td');
                    for (var j = 0; j < cols.length; j++) {
                        row.push(cols[j].innerText);
                    }
                    csv.push(row.join(","));
                }

                var csvFile = new Blob([csv.join("\n")], {type: "text/csv"});

                var  downloadLink = document.createElement("a");
                downloadLink.download = 'Payments.csv';
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