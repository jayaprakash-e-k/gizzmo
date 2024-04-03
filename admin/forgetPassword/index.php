<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password - GIZZMO | Erode Sengunthar Engineering College</title>
    <?php include "../includes/includes.php"; ?>
</head>

<body class="form">

    <form action="sendOTP.php" method="post" onsubmit="validateForm(ev)">
        <h2>Forget Password</h2>
        <div class="input-group">
            <input type="email" name="email" id="email" value="">
            <label for="email">Email</label>
            <span class="error email"></span>
        </div>
        <a href="../index.php">Remember Password?</a>
        <div class="btn-group">
            <button type="submit" name="sendOTP" class="btn-success">Send OTP</button>
        </div>
    </form>

    <script>
        function validateForm(ev) {
            ev.preventDefault();
            const email = $("#email").val();
            if (email === "") {
                $(".error.email").text("Email is required");
            } else {
                ev.target.submit();
            }
        }
    </script>
</body>

</html>