<?php
include "../../config.php";

if (isset($_POST['resetPassword'])) {
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    $email = $_POST['email'];
    if ($password == $cpassword) {
        $password = password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));
        $sql = "UPDATE admin SET PASS = '$password', OTP=0 WHERE EMAIL = '$email'";
        if ($conn->query($sql) === TRUE) {
            echo "success";
        } else {
            echo "<span class='message error'>Error: " . $sql . "<br>" . $conn->error . "</span>";
        }
    } else {
        echo "<span class='message error'>Password and Confirm Password doesn't match</span>";
    }
}

else {
    header("Location: ./index.php");
}