<?php

$hostURL = (((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http")) . "://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$adminPos = strpos($hostURL, "admin/");
$hostURL = substr($hostURL, 0, $adminPos + 5);
?>

<!-- Style Sheet -->
<link rel="stylesheet" type="text/css" href="<?php echo $hostURL; ?>/assets/css/style.css?<?php echo time(); ?>" />

<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/ef6be5e094.js" crossorigin="anonymous"></script>

<!-- Icon -->
<link rel="icon" href="<?php echo $hostURL; ?>/assets/img/logo.png" type="image/png" />

<!-- JS Files -->
<script src="<?php echo $hostURL; ?>/assets/js/message.js?<?php echo time(); ?>" type="text/javascript"></script>
<script src="<?php echo $hostURL; ?>/assets/js/inputValue.js" type="text/javascript"></script>