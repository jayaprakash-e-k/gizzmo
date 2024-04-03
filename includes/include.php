<?php
    $hostURL = (isset($_SERVER['HTTPS'])) ? "https" : "http" . "://" . $_SERVER['SERVER_NAME'];
?>

<!-- Style Sheet -->
<link rel="stylesheet" type="text/css" href="<?php echo $hostURL; ?>/gizzmo/assets/css/style.css?<?php echo time(); ?>" />

<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/ef6be5e094.js" crossorigin="anonymous"></script>

<!-- Icon -->
<link rel="icon" href="<?php echo $hostURL; ?>/gizzmo/assets/img/logo.png" type="image/png" />

<!-- JS Files -->
<script src="<?php echo $hostURL; ?>/gizzmo/assets/js/message.js?<?php echo time(); ?>" type="text/javascript"></script>