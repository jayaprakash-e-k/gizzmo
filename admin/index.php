<?php

session_start();
if(isset($_SESSION['admin'])){
    header("location: ./dashboard.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - GIZZMO | Erode Sengunthar Engineering College</title>
    <?php include "./includes/includes.php"; ?>
</head>
<body class="form">

    <?php include "./includes/loader.html"; ?>

    <form method="post">
        <h2>Login</h2>
        <div class="input-group">
            <input type="text" id="uname" name="uname" value="">
            <label for="uname">Email</label>
            <span class="error uname"></span>
        </div>
        <div class="input-group">
            <input type="password" id="pass" name="pass" value="">
            <label for="pass">Password</label>
            <span class="error pass"></span>
            <span class="icon" id="show-pass" title="Show Password">
                <i class="fa-solid fa-eye"></i>
            </span>
        </div>
        <a href="./forgetPassword/">Forget Password?</a>
        <div class="btn-group">
            <button type="submit" class="btn-success">Login</button>
        </div>
    </form>

    <script>
        $("#show-pass").click(function(){
            if($("#pass").attr("type") == "password"){
                $("#pass").attr("type", "text");
                $("#show-pass i").removeClass("fa-eye").addClass("fa-eye-slash");
            }else{
                $("#pass").attr("type", "password");
                $("#show-pass i").removeClass("fa-eye-slash").addClass("fa-eye");
            }
        });

        $("form").submit(function(e){
            e.preventDefault();
            var uname = $("#uname").val();
            var pass = $("#pass").val();

            // Validation
            if(uname == ""){
                $(".uname").html("Email is required");
                return;
            } else {
                $(".uname").html("");
            }

            if(pass == ""){
                $(".pass").html("Password is required");
                return;
            } else {
                $(".pass").html("");
            }

            // Show Loader
            $("#loader-container").fadeIn(500);

            $.ajax({
                url: "./backend/login.php",
                type: "post",
                data: {
                    uname: uname,
                    pass: pass,
                    login: true
                },
                success: function(data){
                    // Hide Loader
                    $("#loader-container").fadeOut(500);
                    
                    if(data == "success"){
                        window.location.href = "./dashboard.php";
                    } else {
                        message( data, "error");
                    }
                }
            });
        });
    </script>
    
</body>
</html>