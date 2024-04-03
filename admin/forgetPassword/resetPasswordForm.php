<?php
include "../../config.php";

if (!isset($_GET['email'])) {
    header("Location: ./index.php");
} else {
    $email = $_GET['email'];
    $res = mysqli_query($conn, "SELECT * FROM admin WHERE EMAIL='$email' AND OTP=0");
    if (mysqli_num_rows($res) > 0) {
        header("Location: ../index.php?error=invalid-request");
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - GIZZMO | Erode Sengunathar Engineering College</title>
    <?php include "../includes/includes.php"; ?>
</head>

<body class="form">
    <form method="post" id="form" onsubmit="validateForm(event)">
        <h2>Reset Password</h2>
        <div class="input-group">
            <input type="password" name="password" id="password" value="">
            <label for="password">New Password</label>
            <span class="error password"></span>
            <span class="icon" id="show-pass">
                <i class="fa-solid fa-eye"></i>
            </span>
        </div>
        <div class="input-group">
            <input type="password" name="cpassword" id="cpassword" value="">
            <label for="cpassword">Confirm Password</label>
            <span class="error cpassword"></span>
        </div>
        <div class="btn-group">
            <button type="submit" name="resetPassword" class="btn-success">Reset Password</button>
        </div>
    </form>
    <script>
        $("#show-pass").click(function() {
            var pass = $("#password");
            if (pass.attr("type") == "password") {
                pass.attr("type", "text");
                $("#show-pass i").removeClass("fa-eye").addClass("fa-eye-slash");
            } else {
                pass.attr("type", "password");
                $("#show-pass i").removeClass("fa-eye-slash").addClass("fa-eye");
            }
        });

        function validateForm(ev) {
            ev.preventDefault();
            var email = "<?php echo $email; ?>";
            var password = $("#password").val();
            var cpassword = $("#cpassword").val();
            var passRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

            if (password == "") {
                $(".error.password").text("Password is required");
                return;
            } else {
                $(".error.password").text("");
            }

            if (cpassword == "") {
                $(".error.cpassword").text("Confirm Password is required");
                return;
            } else {
                $(".error.cpassword").text("");
            }

            if (!passRegex.test(password)) {
                $(".error.password").text("Password must contain at least 8 characters, including UPPER/lowercase, numbers and special characters");
                return;
            } else {
                $(".error.password").text("");
            }

            if (password != cpassword) {
                $(".error.cpassword").text("Passwords do not match");
                return;
            } else {
                $(".error.cpassword").text("");
            }

            $.ajax({
                url: "./resetPassword.php",
                type: "POST",
                data: {
                    resetPassword: 1,
                    email: email,
                    password: password,
                    cpassword: cpassword
                },
                success: function(data) {
                    if (data == "success") {
                        window.location.href = "../index.php?success=pwd-reset";
                    } else {
                        $("body").prepend(data);
                    }
                }
            });
        }
    </script>
</body>

</html>