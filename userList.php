<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include './includes/include.php'; ?>
    <title>User List - Gizzmo | Erode Sengunthar Engineering College</title>
    <style>
        .container {
            width: calc(100% - 40px);
            margin: 20px auto;
            padding: 20px;
            border-radius: 20px;
            background-color: var(--white);
            overflow: hidden;
            box-shadow: rgba(50, 50, 93, 0.25) 0px 30px 60px -12px, rgba(0, 0, 0, 0.3) 0px 18px 36px -18px;
        }

        .container .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            position: relative;
        }

        .container .header .title {
            display: flex;
        }

        .container .header .title i {
            font-size: 1.5rem;
            color: var(--primary);
            margin-right: 15px;
        }

        .container .header .title h2 {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .container .header .line {
            width: 100%;
            height: 2px;
            background-color: var(--primary-50);
            position: absolute;
            bottom: 0;
        }

        .container .header button {
            padding: 10px 15px;
            border: none;
            outline: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .3s ease-in-out;
        }

        .container .content {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .container .content .table {
            width: 100%;
            padding: 10px;
            overflow-x: auto;
        }

        .container .table table {
            width: 100%;
            overflow-x: auto;
            border-collapse: collapse;
        }

        .container .table table tr {
            border-bottom: 1px solid #ccc;
        }

        .container .table table th,
        .container .table table td {
            padding: 10px 15px;
            text-align: left;
        }

        .container .table table th {
            background-color: var(--secondary);
            color: var(--light);
        }

        .container .table table tr:nth-child(even) {
            background-color: #f4f4f4;
        }

        .container .table table tr td button {
            padding: 10px;
            border: none;
            outline: none;
            border-radius: 5px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .3s ease-in-out;
        }

        .user-details,
        .event-details-popup {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: rgba(0, 0, 0, 0.4);
        }

        .user-details .user-info,
        .event-details-popup .event-details {
            width: min(90%, 500px);
            background: var(--white);
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: start;
            flex-direction: column;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }

        .user-details .user-info .user-info-title,
        .event-details-popup .event-details .event-details-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            width: 100%;
        }

        .user-details .user-info .user-info-title h2,
        .event-details-popup .event-details .event-details-title h2 {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .user-details .user-info .user-info-title button,
        .event-details-popup .event-details .event-details-title button {
            padding: 10px 15px;
            border: none;
            outline: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .3s ease-in-out;
        }

        .user-details .user-info .user-info-content {
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 100%;
        }

        .user-details .user-info .user-info-content .user-info-item {
            display: flex;
            justify-content: space-between;
            width: 100%;
            padding: 10px 0;
            border-bottom: 1px solid var(--primary-50);
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <h2>GIZZMO - User Attendance</h2>
            <div class="line"></div>
        </div>
        <div class="content">

            <div class="table">
                <table>
                    <tr>
                        <th>S.NO</th>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Action</th>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            var url = new URL(window.location.href);
            var eid = url.searchParams.get("eid");

            $.ajax({
                url: './backend/eventOperation.php',
                type: 'GET',
                data: {
                    eid: eid,
                    getUsers: 1
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    var table = $('table');
                    var i = 0;
                    data.forEach(user => {
                        i++;
                        var tr = `
                                <tr>
                                    <td>${i}</td>
                                    <td>${user.UID}</td>
                                    <td>${user.NAME}</td>
                                    <td>${user.PHONE}</td>
                                    <td>
                                        <button class="btn-info" onclick="getUserDetails('${user.UID}')">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                        `;
                        table.append(tr);
                    });
                }
            })
        });

        // Get User Details
        function getUserDetails(uid) {
            $.ajax({
                url: "./backend/eventOperation.php",
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
    </script>
</body>

</html>