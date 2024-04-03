<?php
include "../config.php";

$response = array();

function checkRegistered($conn, $uid, $eid)
{
    $sql = "SELECT * FROM registered_event WHERE UID='$uid' AND EID='$eid'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}

function getRegisteredEvents($conn, $uid)
{
    $sql = "SELECT * FROM registered_event JOIN events ON events.EID=registered_event.EID WHERE UID='$uid'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $events = array();
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($events, $row['EVENT_NAME']);
        }
        return implode(", ", $events);
    } else {
        return "No Events";
    }
}

function checkPaymentVerified($conn, $uid)
{
    $sql = "SELECT * FROM payments WHERE UID='$uid'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['STATUS'] == "VERIFIED") {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }

}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['getEventDetails']) ) {
    $eid = $_GET['eid'];

    $sql = "SELECT * FROM events WHERE eid = '$eid'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        $response['status'] = "success";
        $response['event'] = $row;

    } else {
        $response['status'] = "error";
        $response['message'] = "Event not found";
    }
    echo json_encode($response);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitAttendance']) ) {
    $eid = $_POST['eid'];
    $uid = $_POST['tl_uid'];

    if (checkRegistered($conn, $uid, $eid)) {
        $response['status'] = "error";
        $response['message'] = "You have already submitted attendance for this event";
        echo json_encode($response);
        exit();
    }

    if (!checkPaymentVerified($conn, $uid)) {
        $response['status'] = "error";
        $response['message'] = "Please verify your payment first";
        echo json_encode($response);
        exit();
    }
    
    
    $result = $conn->query($sql);
    
    if (isset($_POST['member'])) {

        $member = $_POST['member'];
        for ($i = 0; $i < count($member); $i++) {

            if (checkRegistered($conn, $member[$i], $eid)) {
                $response['status'] = "error";
                $response['message'] = "You have already submitted attendance for this event";
                echo json_encode($response);
                exit();
            }

            if (!checkPaymentVerified($conn, $member[$i])) {
                $response['status'] = "error";
                $response['message'] = "Please verify your payment first";
                echo json_encode($response);
                exit();
            }

            $sql = "UPDATE registered_event SET TEAM_ID='$uid' WHERE UID='$member[$i]' AND EID='$eid'";            
            $result = $conn->query($sql);
        }
    }

    if ($result) {
        $response['status'] = "success";
        $response['message'] = "Attendance submitted successfully";
    } else {
        $response['status'] = "error";
        $response['message'] = "Error submitting attendance";
    }

    echo json_encode($response);
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['getUsers']) ) {
    $eid = $_GET['eid'];
    $sql = "SELECT * FROM registered_event WHERE EID='$eid'";
    $result = $conn->query($sql);
    $data = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $uid = $row['UID'];
            $sql = "SELECT * FROM users WHERE UID='$uid'";
            $result2 = $conn->query($sql);
            if ($result2->num_rows > 0) {
                $row2 = $result2->fetch_assoc();
                array_push($data, $row2);
            }
        }
        echo json_encode($data);
    } else {
        echo json_encode($data);
    }
}

// Get User Details
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['getUserDetails'])) {
    $uid = $_POST['uid'];
    $sql = "SELECT * FROM users WHERE UID='$uid'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $user['EVENTS'] = getRegisteredEvents($conn, $user['UID']);
        $error['status'] = "success";
        $error['user'] = $user;
    } else {
        $error['status'] = "error";
        $error['message'] = "User Not Found";
    }
    echo json_encode($error);
}

// Get All Events
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['getAllEvents'])) {
    $sql = "SELECT * FROM events ORDER BY EVENT_DATE";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $events = array();
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($events, $row);
        }
        $error['status'] = "success";
        $error['events'] = $events;
    } else {
        $error['status'] = "error";
        $error['message'] = "No Events Found";
    }
    echo json_encode($error);
}

// Get Event Details
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['getEvent'])) {
    $eid = $_GET['EID'];
    $sql = "SELECT * FROM events WHERE EID='$eid'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $event = mysqli_fetch_assoc($result);
        $error['status'] = "success";
        $error['event'] = $event;
    } else {
        $error['status'] = "error";
        $error['message'] = "Event Not Found";
    }
    echo json_encode($error);
}