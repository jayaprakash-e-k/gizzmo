<?php
include '../config.php';
include './mail.php';

// TODO: Change it accordingly
$registrationLink = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http") . "://" . $_SERVER['SERVER_NAME'] . "/gizzmo/registerForm.php";

// TODO: Symposium Registration Amount
$amount = 250;

// TODO: Contact Email
$contactMail = "gizzmo@esec.ac.in";

// TODO: Paper Upload Link
$paperUploadLink = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http") . "://" . $_SERVER['SERVER_NAME'] . "/gizzmo/uploadPaper.php";


// TODO: Paper Submission Deadline
$paperSubmissionDeadline = "<b style='color: red;'>April 15, 2024</b>";

// TODO: Payment Proof Upload Link
$paymentProofLink = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http") . "://" . $_SERVER['SERVER_NAME'] . "/gizzmo/registerForm.php?";

// Logo URL
$gizzmoLogoURL = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http") . "://" . $_SERVER['SERVER_NAME'] . "/gizzmo/asset/img/symp_logo.png";
$paymentQRURL = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http") . "://" . $_SERVER['SERVER_NAME'] . "/gizzmo/asset/img/payment-qr.png";

function validate($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function checkEmail($email)
{
    global $conn;
    $sql = "SELECT * FROM users WHERE EMAIL = '$email'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        return true;
    } else {
        return false;
    }
}

function generateId()
{
    global $conn;
    $sql = "SELECT UID FROM users ORDER BY UID DESC LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $olduid = mysqli_fetch_assoc($result)['UID'];

        $id = substr($olduid, 3);
        $id = (int)$id + 1;

        $newuid = "GIZ" . str_pad($id, 3, '0', STR_PAD_LEFT);
    } else {
        $newuid = "GIZ001";
    }
    return $newuid;
}

function sendOTP($name, $email)
{
    global $mail;
    global $response;
    global $conn;

    $otp = rand(100000, 999999);

    $sql = "UPDATE users SET OTP = '$otp' WHERE EMAIL = '$email'";
    mysqli_query($conn, $sql);

    // Send the OTP to the user
    $mailvars = array(
        'name' => $name,
        'otp' => $otp
    );

    $body = file_get_contents("../assets/mail/otp.html");
    foreach ($mailvars as $key => $value) {
        $body = str_replace('{{' . $key . '}}', $value, $body);
    }

    $subject = "Confirmation OTP - GIZZMO 2K24";

    $mail->addAddress($email);
    $mail->Subject = $subject;
    $mail->isHTML(true);
    $mail->msgHTML($body);

    if ($mail->send()) {
        $mail->ClearAllRecipients();
        return true;
    } else {
        $mail->ClearAllRecipients();
        return false;
    }
}

$response = array(); // JSON response

// Register the user
if (isset($_POST['register'])) {

    // Get the data from the form
    $name = validate($_POST['name']);
    $email = validate($_POST['email']);
    $phone = validate($_POST['phone']);
    $college = validate($_POST['college']);
    $department = validate($_POST['dep']);
    $year = validate($_POST['year']);

    // Get the events from the form
    $events = array();
    if (isset($_POST['events'])) {
        $events = $_POST['events'];
    }

    // Check if the email is already registered
    if (checkEmail($email)) {
        $response['status'] = 'error';
        $response['message'] = 'Email already registered';
        echo json_encode($response);
        exit();
    }

    // Generate UID
    $uid = generateId();

    // Insert the data into the database
    $sql = "INSERT INTO users (UID, NAME, EMAIL, PHONE, COLLEGE, DEPARTMENT, YEAR) VALUES ('$uid', '$name', '$email', '$phone', '$college', '$department', '$year')";

    if (mysqli_query($conn, $sql)) {


        // Insert the events into the database
        for ($i = 0; $i < count($events); $i++) {
            $eid = $events[$i];
            $sql = "INSERT INTO registered_event (UID, EID, TEAM_ID) VALUES ('$uid', '$eid', '$uid')";
            mysqli_query($conn, $sql);
        }


        // Send the OTP to the user
        if (sendOTP($name, $email)) {

            $response['uid'] = $uid;
            $response['status'] = 'success';
            $response['message'] = 'OTP sent to your email';

            echo json_encode($response);
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Something went wrong. Please try again later';
            echo json_encode($response);
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Something went wrong. Please try again later';
        echo json_encode($response);
    }
}

// Check if the email is already registered
if (isset($_POST['checkMail'])) {
    $email = validate($_POST['email']);
    if (checkEmail($email)) {
        $response['status'] = 'error';
        $response['message'] = 'Email already registered';
        echo json_encode($response);
    } else {
        $response['status'] = 'success';
        $response['message'] = 'Email not registered';
        echo json_encode($response);
    }
}

// Resend OTP
if (isset($_POST['resendOTP'])) {
    $email = validate($_POST['email']);

    $sql = "SELECT NAME FROM users WHERE EMAIL = '$email'";
    $result = mysqli_query($conn, $sql);
    $name = mysqli_fetch_assoc($result)['NAME'];

    if (sendOTP($name, $email)) {
        $response['status'] = 'success';
        $response['message'] = 'OTP sent to your email';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Something went wrong. Please try again later';
    }
    echo json_encode($response);
}

// OTP Verification
if (isset($_POST['otp-verify'])) {
    $email = validate($_POST['email']);
    $otp = validate($_POST['otp']);

    $sql = "SELECT * FROM users WHERE EMAIL = '$email' AND OTP = '$otp'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {

        // User Details
        $user = mysqli_fetch_assoc($result);
        $name = $user['NAME'];
        $uid = $user['UID'];
        $phone = $user['PHONE'];


        if ($user['STATUS'] == 'PENDING') {

            // Send Registration successful Mail to the user
            $mailvars = array(
                'participant_name' => $name,
                'user_id' => $uid,
                'symposium_title' => 'GIZZMO 2K24',
                'date' => "April 22, 2024 and April 23, 2024",
                'venue' => "CSE Block ( Main Block )",
                'email_add' => $email,
                'contact_no' => $phone,
                'contact_email' => $contactMail,
                "gizzmo_logo" => $gizzmoLogoURL,
                "payment_img" => $paymentQRURL,
                "payment_proof_link" => $paymentProofLink . "uid=" . $uid . "&email=" . $email . "&operation=payment_proof",
                "paper_upload_link" => $paperUploadLink,
                "paper_submission_deadline" => $paperSubmissionDeadline,
            );

            $body = file_get_contents("../assets/mail/registration.html");
            foreach ($mailvars as $key => $value) {
                $body = str_replace('{{' . $key . '}}', $value, $body);
            }

            $subject = "Registration Successful - GIZZMO 2K24";

            $mail->addAddress($email);
            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->msgHTML($body);

            if ($mail->send()) {
                $mail->ClearAllRecipients();
            }
        }

        $sql = "UPDATE users SET OTP = NULL, STATUS='VERIFIED' WHERE EMAIL = '$email'";
        mysqli_query($conn, $sql);

        $response['uid'] = $user['UID'];
        $response['amount'] = $amount;
        $response['status'] = 'success';
        $response['message'] = 'OTP verified successfully';

        echo json_encode($response);
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Invalid OTP';

        echo json_encode($response);
    }
}

// Payment Screenshot Upload with Database Entry
if (isset($_POST['payment_upload'])) {

    $uid = validate($_POST['uid']);
    $paidAmount = validate($_POST['amount']);
    $payment_date = validate($_POST['payment_date']);
    $transactionId = validate($_POST['transaction_id']);
    $payment = $_FILES['payment_screenshot'];

    // Check if the team leader is already registered
    $sql = "SELECT * FROM users WHERE UID = '$uid'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 0) {
        $response['status'] = 'error';
        $response['message'] = 'Team Leader not registered';
        echo json_encode($response);
        exit();
    }

    $user = mysqli_fetch_assoc($result);
    $name = $user['NAME'];
    $email = $user['EMAIL'];


    // Check if amount greater than 0
    if ($paidAmount <= 0) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid amount';
        echo json_encode($response);
        exit();
    }

    // Check if the payment is already done
    $sql = "SELECT * FROM payments WHERE UID = '$uid'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $response['status'] = 'error';
        $response['message'] = 'Payment screenshot already uploaded';
        echo json_encode($response);
        exit();
    }


    // Upload the payment screenshot
    $ext = pathinfo($payment['name'], PATHINFO_EXTENSION);

    $target_dir = "../uploads/payments";
    $target_file = $target_dir . "/" . $uid . "." . $ext;

    // Check if the file already exists, if yes then delete it
    if (file_exists($target_file)) {
        unlink($target_file);
    }

    // Upload the file
    if (move_uploaded_file($payment['tmp_name'], $target_file)) {
        $sql = "INSERT INTO payments (UID, TRANSACTION_ID, PAID_DATE, FILE_PATH) VALUES ('$uid', '$transactionId', '$payment_date', '$uid.$ext')";
        mysqli_query($conn, $sql);

        // Send Mail to the user
        $email_vars = array(
            "gizzmo_logo" => $gizzmoLogoURL,
            "participant_name" => $name,
            "amount" => $paidAmount,
            "user_id" => $uid,
            "transaction_id" => $transactionId,
            "date" => $payment_date
        );

        $body = file_get_contents("../assets/mail/payment.html");

        foreach ($email_vars as $key => $value) {
            $body = str_replace('{{' . $key . '}}', $value, $body);
        }

        $subject = "Payment Proof Uploaded - GIZZMO 2K24";

        $mail->addAddress($email);
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->msgHTML($body);

        if ($mail->send()) {
            $mail->ClearAllRecipients();
        }

        $response['status'] = 'success';
        $response['message'] = 'Payment screenshot uploaded successfully';
        echo json_encode($response);
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Something went wrong. Please try again later';
        echo json_encode($response);
    }
}

// Paper Upload
if (isset($_POST['paper_upload'])) {

    $tl_id = validate($_POST['tl_id']);
    $paper_title = validate($_POST['paper_title']);

    $totalAmount = $amount;

    $paper = $_FILES['paper_file'];

    $members_id = array();
    if (isset($_POST['member']))
        $members_id = $_POST['member'];


    // Check if the team leader is already registered
    $sql = "SELECT * FROM users WHERE UID = '$tl_id'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);
    $name = $user['NAME'] . " ( " . $user['EMAIL'] . " ) ";

    if (mysqli_num_rows($result) == 0) {
        $response['status'] = 'error';
        $response['message'] = 'Team Leader not registered';
        echo json_encode($response);
        exit();
    }


    // Check Members already registered
    $emails = array();
    $members = array();
    foreach ($members_id as $member) {
        $sql = "SELECT * FROM users WHERE UID = '$member'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) == 0) {
            $emails[] = null;
            $members[] = null;
        } else {
            $members[] = mysqli_fetch_assoc($result)['UID'];
        }
        $totalAmount += $amount;
    }


    // Check Team Leader or Member Already Registered for the event
    $sql = "SELECT * FROM papers WHERE UID = '$tl_id'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $response['status'] = 'error';
        $response['message'] = 'Team Leader already uploaded';
        echo json_encode($response);
        exit();
    }

    // Check if the team leader is already registered for the event as member
    $sql = "SELECT * FROM papers WHERE MEMBER_1 = '$tl_id' OR MEMBER_2 = '$tl_id'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $response['status'] = 'error';
        $response['message'] = 'Team Leader already registered for the event';
        echo json_encode($response);
        exit();
    }



    // Check Member 1 or Member 2 Already Registered for the event
    foreach ($members as $member) {
        if ($member == "") {
            continue;
        }

        $sql = "SELECT * FROM papers WHERE UID = '$member'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $response['status'] = 'error';
            $response['message'] = 'Paper already uploaded';
            echo json_encode($response);
            exit();
        }

        // Check if the member is already registered for the event as member
        $sql = "SELECT * FROM papers WHERE MEMBER_1 = '$member' OR MEMBER_2 = '$member'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $response['status'] = 'error';
            $response['message'] = 'Member already registered for the event';
            echo json_encode($response);
            exit();
        }
    }


    // Generate ID for paper   :Format = GIZ001P
    $sql = "SELECT PID FROM papers ORDER BY PID DESC LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $oldpid = mysqli_fetch_assoc($result)['PID'];

        $id = substr($oldpid, 3);
        $id = (int)$id + 1;

        $pid = "GIZ" . str_pad($id, 3, '0', STR_PAD_LEFT) . "P";
    } else {
        $pid = "GIZ001P";
    }

    // Upload the paper
    $ext = pathinfo($paper['name'], PATHINFO_EXTENSION);

    $target_dir = "../uploads/papers";
    $target_file = $target_dir . "/" . $pid . "." . $ext;

    // Check if the file already exists, if yes then delete it
    if (file_exists($target_file)) {
        unlink($target_file);
    }

    // Upload the file
    if (move_uploaded_file($paper['tmp_name'], $target_file)) {

        $sql = "INSERT INTO papers (PID, UID, P_TITLE, MEMBER_1, MEMBER_2, FILE_PATH) VALUES ( '$pid', '$tl_id', '$paper_title', '$members[0]', '$members[1]', '$target_file')";
        mysqli_query($conn, $sql);

        // Update TEAM_ID as TLID in registered_event WHERE EID is GIZ001ET for members
        if ($members[0] != null && $members[0] != "") {
            $sql = "UPDATE registered_event SET TEAM_ID = '$tl_id' WHERE UID = '$members[0]' AND EID = 'GIZ001ET'";
            mysqli_query($conn, $sql);
        }

        if ($members[1] != null && $members[1] != "") {
            $sql = "UPDATE registered_event SET TEAM_ID = '$tl_id' WHERE UID = '$members[1]' AND EID = 'GIZ001ET'";
            mysqli_query($conn, $sql);
        }


        // Send Mail to the user
        $email_vars = array(
            "gizzmo_logo" => $gizzmoLogoURL,
            "participant_name" => $name,
            "pid" => $pid,
            "paper_title" => $paper_title,
            "team_leader_id" => $tl_id,
            "member_1" => $members[0],
            "member_2" => $members[1]
        );

        $body = file_get_contents("../assets/mail/paper.html");

        foreach ($email_vars as $key => $value) {
            $body = str_replace('{{' . $key . '}}', $value, $body);
        }

        $subject = "Paper Uploaded - GIZZMO 2K24";

        $mail->addAddress($user['EMAIL']);
        foreach ($emails as $email) {

            if ($email == "") {
                continue;
            }

            $mail->addAddress($email);
        }

        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->msgHTML($body);

        if ($mail->send()) {
            $mail->ClearAllRecipients();
        }

        $response['status'] = 'success';
        $response['message'] = 'Paper uploaded successfully';
        $response['amount'] = $totalAmount;
        echo json_encode($response);
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Something went wrong. Please try again later';
        echo json_encode($response);
    }
}

// Validate User ID
if (isset($_POST['validate_uid'])) {
    $uid = validate($_POST['uid']);
    $sql = "SELECT * FROM users WHERE UID = '$uid'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $response['status'] = 'success';
        $response['message'] = 'User ID is valid';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'User ID is invalid';
    }
    echo json_encode($response);
}
