<?php
session_start();

if (!isset($_SESSION['LOGGEDIN'])) {
    header('Location: ./logout.php');
    exit();
}
?>