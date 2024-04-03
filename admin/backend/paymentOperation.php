<?php
include "../../config.php";
include "./authenticate.php";

if (isset($_GET['getAllPayments'])) {
    $sql = "SELECT * FROM users JOIN payments ON users.UID=payments.UID";
    $result = $conn->query($sql);
    $payments = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $payments[] = $row;
        }
    }
    
    echo json_encode($payments);
}

if (isset($_POST['updatePaymentStatus'])) {
    $id = $_POST['uid'];
    $status = $_POST['status'];

    $sql = "UPDATE payments SET STATUS='$status'  WHERE UID ='$id'";
    if ($conn->query($sql) === TRUE) {
        echo "success";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>