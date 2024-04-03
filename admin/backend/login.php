<?php
include "../../config.php";

function validate($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["uname"]) && isset($_POST["pass"]) && isset($_POST["login"]) && $_POST["login"] == "true"){
 
    $uname = validate($_POST["uname"]);
    $pass = validate($_POST["pass"]);

    if ($uname == "") {
        echo "Email is required";
        return;
    }

    if ($pass == "") {
        echo "Password is required";
        return;
    }

    $sql = "SELECT * FROM admin WHERE EMAIL = '$uname'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {

        $row = $result->fetch_assoc();
        if (password_verify($pass, $row["PASS"])) {
            
            session_start();
            
            $_SESSION["USER"] = $row["AID"];
            $_SESSION["NAME"] = $row["NAME"];
            $_SESSION["EMAIL"] = $row["EMAIL"];
            $_SESSION['LOGGEDIN'] = true;

            echo "success";
        } else {
            echo "Invalid Email or Password.";
        }
        
    } else {
        echo "Invalid Email or Password.";
    }
} else {
    echo "Invalid Request";
}