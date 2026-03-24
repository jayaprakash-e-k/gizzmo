<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


$mail = new PHPMailer(true);

$mail->isSMTP();
$mail->Host = "smtp.gmail.com";
$mail->SMTPAuth = true;
$mail->Username = "sample@example.com";
$mail->Password = "sample-app-password";
$mail->SMTPSecure = "ssl";
$mail->Port = 465;

$mail->setFrom("sample@example.com", "GIZZMO - 2K24 | Erode Sengunthar Engineering College");