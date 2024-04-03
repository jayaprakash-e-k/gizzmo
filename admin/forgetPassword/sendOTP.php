<?php 
include "../../config.php";
include "../../backend/mail.php";

if (isset($_POST['sendOTP'])) {
    $email = $_POST['email'];
    $otp = rand(100000, 999999);
    
    $res = mysqli_query($conn, "SELECT * FROM admin WHERE EMAIL='$email'");
    if (mysqli_num_rows($res) > 0) {
        mysqli_query($conn, "UPDATE admin SET OTP='$otp' WHERE EMAIL='$email'");
    } else {
        header("Location: ./index.php?error=invalidEmail");
    }

    $mailvars = [
        "otp" => $otp
    ];

    $body = file_get_contents("../assets/mail/otp.html");
    foreach ($mailvars as $key => $value) {
        $body = str_replace('{{' . $key . '}}', $value, $body);
    }

    $mail->addAddress($email);
    $mail->Subject = "OTP for Password Reset";
    $mail->isHTML(true);
    $mail->msgHTML($body);

    if ($mail->send()) {
        $mail->ClearAllRecipients();
        header("Location: ./verifyOTP.php?email=$email");
    } else {
        header("Location: ./index.php?error=mailError");
    }
}

else {
    header("Location: ./index.php");
}