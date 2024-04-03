<?php
include "../../config.php";

if (!isset($_GET['email'])) {
    header("Location: ./index.php");
}

if (isset($_POST['verifyOTP'])) {
    $email = $_GET['email'];
    $otp = $_POST['otp'];

    $res = mysqli_query($conn, "SELECT * FROM admin WHERE EMAIL='$email' AND OTP='$otp'");
    if (mysqli_num_rows($res) > 0) {
        header("Location: ./resetPasswordForm.php?email=$email");
    } else {
        header("Location: ./verifyOTP.php?email=$email&error=invalidOTP");
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICIT - 24 - Verify OTP</title>
    <?php include "../includes/includes.php"; ?>
</head>

<body class="form">
    <form action="./verifyOTP.php?email=<?php echo $_GET['email']; ?>" method="post" onsubmit="validateForm(ev)">
        <h2>Validate OTP</h2>
        <p>An 6 Digit one time password has been sent to your mail</p><br>
        <div class="input-group">
            <input type="number" id="otp" name="otp" value="">
            <label for="otp">OTP</label>
        </div>
        <a onclick="sendOTP()">
            Haven't received OTP? &nbsp;
            <span class="./sendOTP.php">Resend OTP</span>
        </a>
        <div class="btn-group">
            <button type="submit" name="verifyOTP" class="btn-success">Verify OTP</button>
        </div>
    </form>

    <script>
        function validateForm(ev) {
            ev.preventDefault();
            let otp = document.getElementById('otp').value;
            if (otp.length != 6) {
                var message = ` <span class='message error'>OTP must be 6 digits long</span>`;
                $('body').append(message);
            }
            ev.submit();
        }

        function sendOTP() {
            let email = "<?php echo $_GET['email']; ?>";
            $.ajax({
                url: "./sendOTP.php",
                method: "POST",
                data: {
                    sendOTP: true,
                    email: email
                },
                success: function(data) {
                    if (data == "success") {
                        var message = `<span class="message success">OTP has been sent to your mail</span>`;
                        $("body").append(message);
                    } else {
                        var message = `<span class="message error">Failed to send OTP. Please try again later</span>`;
                        $("body").append(message);
                    }
                }
            });
        }
    </script>
</body>

</html>