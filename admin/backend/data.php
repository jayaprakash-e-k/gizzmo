<?php
include "../../config.php";
include "./authenticate.php";

if (isset($_POST['data'])) {
    // Data JSON
    $data = array();

    // Users Count
    $result = mysqli_query($conn, "SELECT * FROM users");
    $data['users'] = mysqli_num_rows($result);

    // Total Paid Users
    $result = mysqli_query($conn, "SELECT * FROM payments");
    $data['paid'] = mysqli_num_rows($result);

    // Total Verified Payments
    $result = mysqli_query($conn, "SELECT * FROM payments WHERE STATUS = 'VERIFIED'");
    $data['verifiedPayments'] = mysqli_num_rows($result);

    // Events Count
    $result = mysqli_query($conn, "SELECT * FROM events");
    $data['events'] = mysqli_num_rows($result);

    // Registered Events Count
    while ($row = mysqli_fetch_assoc($result)) {
        $eid = $row['EID'];
        $eventCount = mysqli_query($conn, "SELECT * FROM registered_event WHERE EID='$eid'");
        $data[$eid] = mysqli_num_rows($eventCount);
    }

    // Echo Data
    echo json_encode($data);
}
