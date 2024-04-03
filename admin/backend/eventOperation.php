<?php
include "../../config.php";

$error = array();

function validate($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Add new event
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addEvent'])) {
    $eventName      = validate($_POST['eventName']);
    $description    = validate($_POST['eventDescription']);
    $rules          = validate($_POST['eventRules']);
    $teamSize       = validate($_POST['teamSize']);
    $eventDate      = validate($_POST['eventDate']);
    $eventStartTime = validate($_POST['eventStartTime']);
    $eventEndTime   = validate($_POST['eventEndTime']);
    $eventVenue     = validate($_POST['eventVenue']);
    $coName         = validate($_POST['coName']);
    $coNo           = validate($_POST['coNo']);
    $eventType      = validate($_POST['eventType']);

    if ($eventName == "" || $description == "" || $teamSize == "" || $eventDate == "" || $coName == "" || $coNo == "" || $eventType == "" || $eventStartTime == "" || $eventEndTime == "" || $eventVenue == "") {
        $error['status'] = "error";
        $error['message'] = "All Fields are Required";
        echo json_encode($error);
        exit();
    }

    // Check File is uploaded
    if (!isset($_FILES['eventImage'])) {
        $error['status'] = "error";
        $error['message'] = "Event Image is Required";
        echo json_encode($error);
        exit();
    }

    
    // Generate Event ID
    $sql = "SELECT * FROM events";
    $result = mysqli_query($conn, $sql);

    $eid = "GIZ" . str_pad(mysqli_num_rows($result) + 1, 3, "0", STR_PAD_LEFT) . "E" . $eventType;
    
    // Upload Image
    $target_dir = "../../uploads/events/";
    $ext = pathinfo($_FILES['eventImage']['name'], PATHINFO_EXTENSION);
    $target_file = $target_dir . $eid . ".". $ext;

    $sql = "INSERT INTO events (EID, EVENT_NAME, DESCRIPTION, RULES, TEAM_SIZE, EVENT_DATE, START, END, VENUE, CO_NAME, CO_NO, FILE) VALUES ('$eid', '$eventName', '$description', '$rules', '$teamSize', '$eventDate', '$eventStartTime' , '$eventEndTime', '$eventVenue', '$coName', '$coNo', '$eid.$ext')";
    if (mysqli_query($conn, $sql)) {



        if (!in_array(strtolower($ext), array('jpg', 'jpeg', 'png', 'gif'))) {
            $error['status'] = "error";
            $error['message'] = "Invalid Image Format. Image not Uploaded. Only JPG, JPEG, PNG, GIF are allowed";
            echo json_encode($error);
            exit();
        }

        if (!move_uploaded_file($_FILES['eventImage']['tmp_name'], $target_file)) {
            $error['status'] = "error";
            $error['message'] = "Something Went Wrong";
            $error['error'] = "Error Uploading Image";
            echo json_encode($error);
            exit();
        }

        $error['EID'] = $eid; // [IMP] Send EID to the client
        $error['status'] = "success";
        $error['message'] = "Event Added Successfully";
    } else {
        $error['status'] = "error";
        $error['message'] = "Something Went Wrong";
        $error['error'] = mysqli_error($conn);
    }

    echo json_encode($error);
}

// Update event
else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateEvent'])) {
    $eid         = validate($_POST['EID']);
    $eventName   = validate($_POST['eventName']);
    $description = validate($_POST['eventDescription']);
    $rules       = validate($_POST['eventRules']);
    $teamSize    = validate($_POST['teamSize']);
    $eventDate   = validate($_POST['eventDate']);
    $eventStartTime = validate($_POST['eventStartTime']);
    $eventEndTime   = validate($_POST['eventEndTime']);
    $eventVenue     = validate($_POST['eventVenue']);
    $coName      = validate($_POST['coName']);
    $coNo        = validate($_POST['coNo']);
    $eventType   = validate($_POST['eventType']);

    // Check if event is available
    $sql = "SELECT * FROM events WHERE EID='$eid'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 0) {
        $error['status'] = "error";
        $error['message'] = "Event Not Found";
        echo json_encode($error);
        exit();
    }

    // Check file is uploaded
    if (isset($_FILES['eventImage'])) {
        $target_dir = "../../uploads/events/";
        $ext = pathinfo($_FILES['eventImage']['name'], PATHINFO_EXTENSION);

        if (!in_array(strtolower($ext), array('jpg', 'jpeg', 'png', 'gif'))) {
            $error['status'] = "error";
            $error['message'] = "Invalid Image Format. Image not Uploaded. Only JPG, JPEG, PNG, GIF are allowed";
            echo json_encode($error);
            exit();
        }

        $target_file = $target_dir . $eid . "." . $ext;

        if (!move_uploaded_file($_FILES['eventImage']['tmp_name'], $target_file)) {
            $error['status'] = "error";
            $error['message'] = "Something Went Wrong";
            $error['error'] = "Error Uploading Image";
            echo json_encode($error);
            exit();
        }
    }

    $fileQuery = isset($target_file) ? ", FILE='$eid.$ext'" : "";
    
    $oldEventType = substr($eid, 7, 1);
    $newEID = str_replace($oldEventType, $eventType, $eid);

    $sql = "UPDATE events SET EID='$newEID', EVENT_NAME='$eventName', DESCRIPTION='$description', RULES='$rules', TEAM_SIZE='$teamSize', EVENT_DATE='$eventDate', START='$eventStartTime', END='$eventEndTime', VENUE='$eventVenue', CO_NAME='$coName', CO_NO='$coNo'  $fileQuery  WHERE EID='$eid'";
    if (mysqli_query($conn, $sql)) {
        $error['status'] = "success";
        $error['message'] = "Event Updated Successfully";
    } else {
        $error['status'] = "error";
        $error['message'] = "Something Went Wrong";
        $error['error'] = mysqli_error($conn);
    }

    echo json_encode($error);
}

// Delete Event
else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteEvent'])) {
    $eid = $_POST['EID'];

    // Check if any user is registered for the event
    $sql = "SELECT * FROM registered_event WHERE EID='$eid'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $error['status'] = "error";
        $error['message'] = "Cannot Delete Event. Some Users are registered for this event";
        echo json_encode($error);
        exit();
    }

    // Check if event is available
    $sql = "SELECT * FROM events WHERE EID='$eid'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 0) {
        $error['status'] = "error";
        $error['message'] = "Event Not Found";
        echo json_encode($error);
        exit();
    }

    $sql = "DELETE FROM events WHERE EID='$eid'";
    if (mysqli_query($conn, $sql)) {
        $error['status'] = "success";
        $error['message'] = "Event Deleted Successfully";
    } else {
        $error['status'] = "error";
        $error['message'] = "Something Went Wrong";
        $error['error'] = mysqli_error($conn);
    }

    echo json_encode($error);
}

// Lock Event
else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['lockEvent'])) {
    $eid = $_POST['EID'];

    // Get Previous Status
    $sql = "SELECT STATUS FROM events WHERE EID='$eid'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $status = $row['STATUS'] == "OPEN" ? "CLOSE" : "OPEN";

    $sql = "UPDATE events SET STATUS='$status' WHERE EID='$eid'";
    if (mysqli_query($conn, $sql)) {
        $error['status'] = "success";
        $error['message'] = "Event Locked Successfully";
    } else {
        $error['status'] = "error";
        $error['message'] = "Something Went Wrong";
        $error['error'] = mysqli_error($conn);
    }

    echo json_encode($error);
}

// Get all events
else if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['getAllEvents']) && $_GET['getAllEvents'] == 1) {
    $sql = "SELECT * FROM events ORDER BY EID";
    $result = mysqli_query($conn, $sql);

    $events = [];
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($events, $row);
        }
    }

    echo json_encode($events);
}

// Get Event Details
else if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['getEventDetails'])) {
    $eid = $_GET['EID'];
    $sql = "SELECT * FROM events WHERE EID='$eid'";
    $result = mysqli_query($conn, $sql);

    $event = [];
    if (mysqli_num_rows($result) > 0) {
        $event = mysqli_fetch_assoc($result);
        $respose = array(
            "status" => "success",
            "event" => $event
        );
    } else {
        $respose = array(
            "status" => "error",
            "message" => "Event Not Found"
        );
    }

    echo json_encode($respose);
}
