<?php
include "../../config.php";
include "../../backend/mail.php";

// TODO: Payment Proof Upload Link
$paymentProofLink = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http") . "://" . $_SERVER['SERVER_NAME'] . "/gizzmo/registerForm.php?";

// Logo URL
$gizzmoLogoURL = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http") . "://" . $_SERVER['SERVER_NAME'] . "/gizzmo/assets/img/symp_logo.png";
$paymentQRURL = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http") . "://" . $_SERVER['SERVER_NAME'] . "/gizzmo/assets/img/payment-qr.png";

if (isset($_GET['getAllPapers'])) {
    $sql = "SELECT * FROM papers";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $papers = array();
        while ($row = $result->fetch_assoc()) {
            $sql = "SELECT * FROM users WHERE UID = '" . $row['UID'] . "'";
            $tl = $conn->query($sql)->fetch_assoc();

            $sql = "SELECT * FROM users WHERE UID = '" . $row['MEMBER_1'] . "'";
            $sql = $conn->query($sql);
            $m1 = $sql->num_rows > 0 ? $sql->fetch_assoc() : null;

            $sql = "SELECT * FROM users WHERE UID = '" . $row['MEMBER_2'] . "'";
            $sql = $conn->query($sql);
            $m2 = $sql->num_rows > 0 ? $sql->fetch_assoc() : null;

            $papers[] = array(
                "PID" => $row['PID'],
                "TL_ID" => $tl['UID'],
                "TL_NAME" => $tl['NAME'],
                "MEMBER_1" => $m1 == null ? null : $m1['NAME'],
                "M1_ID" => $m1 == null ? null : $m1['UID'],
                "MEMBER_2" => $m2 == null ? null : $m2['NAME'],
                "M2_ID" => $m2 == null ? null : $m2['UID'],
                "P_TITLE" => $row['P_TITLE'],
                "STATUS" => $row['STATUS'],
            );
        }

        echo json_encode(array("status" => "success", "papers" => $papers));
    } else {
        echo json_encode(array("status" => "error", "message" => "No Papers Found"));
    }
}

// View Paper 
if (isset($_GET['viewPaper']) && isset($_GET['pid'])) {
    $sql = "SELECT * FROM papers WHERE PID = '" . $_GET['pid'] . "'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $paper = $result->fetch_assoc();
        echo json_encode(array("status" => "success", "paper" => $paper));
    } else {
        echo json_encode(array("status" => "error", "message" => "Paper Not Found"));
    }
}

// Update Paper Status
if (isset($_POST['updateStatus'])) {
    $status = $_POST['status'];
    $pid = $_POST['pid'];

    $sql = "UPDATE papers SET STATUS = '$status' WHERE PID = '$pid'";
    if ($conn->query($sql)) {
        echo json_encode(array("status" => "success", "message" => "Paper Status Updated Successfully"));
    } else {
        echo json_encode(array("status" => "error", "message" => "Failed to Update Paper Status"));
    }
}

// Delete Paper
if (isset($_POST['deletePaper']) && isset($_POST['pid'])) {
    
    // Delete file
    $sql = "SELECT * FROM papers WHERE PID = '" . $_POST['pid'] . "'";
    $result = $conn->query($sql);
    $paper = $result->fetch_assoc();
    unlink("../" . $paper['FILE_PATH']);
    
    $sql = "DELETE FROM papers WHERE PID = '" . $_POST['pid'] . "'";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(array("status" => "success", "message" => "Paper Deleted Successfully"));
    } else {
        echo json_encode(array("status" => "error", "message" => "Failed to Delete Paper"));
    }
}

// send mail
if (isset($_POST['sendMail'])) {
    
    $pid = $_POST['pid'];
    $sql = "SELECT * FROM papers WHERE PID = '$pid'";
    $result = $conn->query($sql);
    $paper = $result->fetch_assoc();

    if ($paper['STATUS'] != "ACCEPTED") {
        echo json_encode(array("status" => "error", "message" => "Paper Not Accepted"));
        return;
    }

    $sql = "SELECT * FROM users WHERE UID = '" . $paper['UID'] . "'";
    $tl = $conn->query($sql)->fetch_assoc();

    $sql = "SELECT * FROM users WHERE UID = '" . $paper['MEMBER_1'] . "'";
    $sql = $conn->query($sql);
    $m1 = $sql->num_rows > 0 ? $sql->fetch_assoc() : null;

    $sql = "SELECT * FROM users WHERE UID = '" . $paper['MEMBER_2'] . "'";
    $sql = $conn->query($sql);
    $m2 = $sql->num_rows > 0 ? $sql->fetch_assoc() : null;

    $email_vars = array(
        "participant_name" => $tl['NAME'],
        "paper_title" => $paper['P_TITLE'],
        "symposium_title" => "GIZZMO 2k24",
        "date" => "April 22, 2024",
        "venue" => "CSE Department, ESEC, Erode",
        "user_id" => $tl['NAME'] . " - " . $tl['UID'],
        "mem1_id" => $m1 == null ? null : $m1['NAME'] . " - " . $m1['UID'],
        "mem2_id" => $m2 == null ? null : $m2['NAME'] . " - " .  $m2['UID'],
        "payment_img" => $paymentQRURL,
        "payment_proof_link" => $paymentProofLink,
        "gizzmo_logo" => $gizzmoLogoURL
    );

    $body = file_get_contents("../assets/mail/acceptanceMail.html");

    foreach ($email_vars as $key => $value) {
        $body = str_replace("{{" . $key . "}}", $value, $body);
    }

    $mail->addAddress($tl['EMAIL'], $tl['NAME']);
    if ($m1 != null) {
        $mail->addAddress($m1['EMAIL'], $m1['NAME']);
    }
    if ($m2 != null) {
        $mail->addAddress($m2['EMAIL'], $m2['NAME']);
    }

    $mail->Subject = "Paper Acceptance - GIZZMO 2k24";
    $mail->isHTML(true);
    $mail->Body = $body;

    if ($mail->send()) {
        echo json_encode(array("status" => "success", "message" => "Mail Sent Successfully"));
    } else {
        echo json_encode(array("status" => "error", "message" => "Failed to Send Mail"));
    }
}