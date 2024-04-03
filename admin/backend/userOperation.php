<?php
include "../../config.php";

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

$error = array();

// Get all registered users
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['getUsers'])) {
    $sql = "SELECT * FROM users";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $users = array();
        $i = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $user = array();
            $user['UID'] = $row['UID'];
            $user['SNO'] = $i++;
            $user['NAME'] = $row['NAME'];
            $user['PHONE'] = $row['PHONE'];
            $user['EMAIL'] = $row['EMAIL'];
            $user['EVENTS'] = getRegisteredEvents($conn, $row['UID']);
            array_push($users, $user);
        }
        $error['status'] = "success";
        $error['users'] = $users;
    } else {
        $error['status'] = "error";
        $error['message'] = "No Users Found";
    }
    echo json_encode($error);
}

// Get User Details
else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['getUserDetails'])) {
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

// Download Data
else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['download'])) {
    $team = $_POST['team'];
    $event = $_POST['event'];

    if ($team == "IN") {

        if ($event == "ALL") {
            $sql = "SELECT users.UID, NAME, PHONE, EMAIL, GROUP_CONCAT(events.EVENT_NAME) AS EVENTS FROM users JOIN registered_event ON users.UID=registered_event.UID JOIN events ON events.EID=registered_event.EID GROUP BY users.UID;";
            $fields = array('UID', 'NAME', 'PHONE', 'EMAIL', 'EVENTS');
        } else {
            $sql = "SELECT users.UID, NAME, PHONE, EMAIL, events.EVENT_NAME AS EVENT FROM users JOIN registered_event ON users.UID=registered_event.UID JOIN events ON events.EID=registered_event.EID WHERE registered_event.EID='$event'";
            $fields = array('UID', 'NAME', 'PHONE', 'EMAIL', 'EVENTS');
        }
    } else {

        if ($event == "ALL") {
            $sql = "SELECT TEAM_ID, (SELECT CONCAT(EVENT_NAME, ' ( ', EID, ')') FROM events WHERE events.EID=registered_event.EID) AS EVENT, GROUP_CONCAT(CONCAT(users.NAME, ' ( ', users.UID, ' )') SEPARATOR ' | ' ) AS MEMBERS, (SELECT PHONE FROM users WHERE users.UID=TEAM_ID) AS CONTACT, (SELECT EMAIL FROM users WHERE users.UID=TEAM_ID) AS EMAIL FROM registered_event JOIN users ON users.UID = registered_event.UID GROUP BY TEAM_ID, EID;";
            $fields = array('TEAM_ID', 'EVENT', 'TEAM_MEMBERS', 'LEADER_CONTACT', 'LEADER_EMAIL');
        } else {
            $sql = "SELECT TEAM_ID, (SELECT CONCAT(EVENT_NAME, ' ( ', EID, ')') FROM events WHERE events.EID=registered_event.EID) AS EVENT, GROUP_CONCAT(CONCAT(users.NAME, ' ( ', users.UID, ' )') SEPARATOR ' | ' ) AS MEMBERS, (SELECT PHONE FROM users WHERE users.UID=TEAM_ID) AS CONTACT, (SELECT EMAIL FROM users WHERE users.UID=TEAM_ID) AS EMAIL FROM registered_event JOIN users ON users.UID = registered_event.UID WHERE EID = '$event' GROUP BY TEAM_ID, EID;";
            $fields = array('TEAM_ID', 'EVENT', 'TEAM_MEMBERS', 'LEADER_CONTACT', 'LEADER_EMAIL');
        }
    }

    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {

        // Create a json data and echo it
        $data = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        echo json_encode(array('status' => 'success', 'data' => $data, 'fields' => $fields));
    } else {
        $error['status'] = "error";
        $error['message'] = "No Data Found";
    }
}

// Delete User
else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteUser'])) {
    $uid = $_POST['uid'];

    // Check user exist
    $sql = "SELECT * FROM users WHERE UID='$uid'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 0) {
        $error['status'] = "error";
        $error['message'] = "User Not Found";
        echo json_encode($error);
        exit();
    }

    // Check in papers table
    $sql = "SELECT * FROM papers WHERE UID='$uid' OR MEMBER_1='$uid' OR MEMBER_2='$uid'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $error['status'] = "error";
        $error['message'] = "User is a part of a paper. Delete the paper first.";
    } else {

        // Check in registered_event table
        $sql = "SELECT * FROM registered_event WHERE UID='$uid'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            $sql = "DELETE FROM registered_event WHERE UID='$uid'";
            if (!mysqli_query($conn, $sql)) {
                $error['status'] = "error";
                $error['message'] = "Something went wrong. Please try again later.";
                echo json_encode($error);
                exit();
            }
        }

        // Check in payments table
        $sql = "SELECT * FROM payments WHERE UID='$uid'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            $sql = "DELETE FROM payments WHERE UID='$uid'";
            if (!mysqli_query($conn, $sql)) {
                $error['status'] = "error";
                $error['message'] = "Something went wrong. Please try again later.";
                echo json_encode($error);
                exit();
            }
        }

        // Delete User
        $sql = "DELETE FROM users WHERE UID='$uid'";
        if (mysqli_query($conn, $sql)) {
            $error['status'] = "success";
            $error['message'] = "User Deleted Successfully";
        } else {
            $error['status'] = "error";
            $error['message'] = "Something went wrong. Please try again later.";
        }
    }

    echo json_encode($error);
}
