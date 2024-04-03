<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include './includes/include.php'; ?>
    <title>Register Here - gizzmo | Erode Sengunthar Engineering College</title>
</head>

<body>

    <?php
    include './includes/loader.html';
    include './includes/header.php'; ?>

    <div class="form-section">
        <section class="form-container">

            <!-- Registration Form -->
            <section class="form">
                <div class="illustration">
                    <img src="./assets/img/register.svg" alt="OTP Illutration">
                </div>
                <form method="post" id="register">
                    <h2>REGISTER</h2>
                    <span class="error"></span>
                    <div class="input-group">
                        <input type="text" name="name" id="name" value="" />
                        <label for="name">Name</label>
                    </div>
                    <div class="input-group">
                        <input type="tel" name="phone" id="phone" value="" />
                        <label for="phone">Contact No</label>
                    </div>
                    <div class="input-group">
                        <input type="email" name="email" id="email" value="" />
                        <label for="email">Email</label>
                        <span class="error alreadyExist" onclick="showVerifyOTP()"></span>
                    </div>
                    <div class="input-group">
                        <input type="text" name="college" id="college" value="" />
                        <label for="college">College</label>
                    </div>
                    <div class="input-group">
                        <select name="year" id="year">
                            <option value="">Select Year</option>
                            <option value="1">I</option>
                            <option value="2">II</option>
                            <option value="3">III</option>
                            <option value="4">IV</option>
                            <option value="5">V</option>
                        </select>
                        <label for="year">Year</label>
                    </div>
                    <div class="input-group">
                        <input type="text" name="dep" id="dep" value="" />
                        <label for="dep">Department</label>
                    </div>
                    <div class="day-1 checkbox-group">
                        <h3>Day 1</h3>
                        <ul></ul>
                    </div>
                    <div class="day-2 checkbox-group">
                        <h3>Day 2</h3>
                        <ul></ul>
                    </div>
                    <div class="input-group">
                        <input type="checkbox" name="verify" id="verify" required>
                        <label for="verify">I've verified that all the details I entered are correct.</label>
                    </div>
                    <div class="btn-group">
                        <button type="submit" name="register" class="btn-success">Register</button>
                    </div>
                </form>
            </section>

            <!-- Otp Validation -->
            <section class="form" style="align-items: center; height: 100vh;">
                <div class="illustration">
                    <img src="./assets/img/otp.svg" alt="OTP Illutration">
                </div>
                <form method="POST" id="otp-verify">
                    <h2>OTP Verification</h2>
                    <span class="error"></span>
                    <div class="input-group">
                        <input type="text" name="otp" id="otp" value="">
                        <label for="otp">OTP</label>
                    </div>
                    <a onclick="resendOTP()">Resend OTP</a>
                    <div class="btn-group">
                        <button type="submit" name="otp-submit" class="btn-success">Submit</button>
                    </div>
                </form>
            </section>

            <!-- Payment screenshot -->
            <section class="form" style="align-items: center; height: 100vh;">
                <div class="illustration" id="screenshot">
                    <img src="./assets/img/payment.svg" alt="Transaction Illutration">
                </div>
                <form method="POST" enctype="multipart/form-data" id="paymentProof">
                    <h2>Payment Verification</h2>
                    <span class="error"></span>
                    <div class="input-group">
                        <input type="text" name="uid" id="uid" value="">
                        <label for="uid">GIZZMO ID</label>
                        <span class="error"> Note: gizzmo ID will be sent to your Email with Successfull Registration Email</span>
                    </div>
                    <div class="input-group">
                        <input type="text" name="amount" id="amount" value="" readonly>
                        <label for="amount">Amount to be paid</label>
                    </div>
                    <div class="input-group">
                        <input type="date" name="payment_date" id="payment-date" value="">
                        <label for="payment-date">Payment Date<label>
                    </div>
                    <div class="input-group">
                        <input type="text" name="transaction_id" id="transaction-id" value="">
                        <label for="transaction-id">Transaction ID</label>
                    </div>
                    <div class="input-group">
                        <input type="file" name="payment_screenshot" id="payment-screenshot" value="">
                        <label for="payment-screenshot">Payment Screenshot</label>
                    </div>

                    <div class="btn-group">
                        <button type="submit" name="payment" class="btn-success">Submit</button>
                    </div>
                </form>
            </section>

        </section>
    </div>

    <?php include './includes/footer.php'; ?>

    <script>
        // Get All Events
        $.ajax({
            url: "./backend/eventOperation.php",
            type: "GET",
            data: {
                getAllEvents: 1
            },
            success: function(data) {
                var response = JSON.parse(data);

                if (response.status != "success") {
                    message(response.message, "error");
                    return;
                }

                response = response.events;
                if (response.length > 0) {
                    response.forEach(event => {
                        var checkbox = `
                            <li>
                                <input type="checkbox" name="events[]" id="event-${event.EID}" value="${event.EID}" data-date="${event.EVENT_DATE}" data-start="${event.START}" data-end="${event.END}">
                                <label for="event-${event.EID}">${event.EVENT_NAME} ${(event.EID).includes("W") ? "( Workshop )" : ""}</label>
                            </li>
                        `;
                        if ((event.EVENT_DATE).includes("2024-04-22")) {
                            $(".day-1 ul").append(checkbox);
                        } else {
                            $(".day-2 ul").append(checkbox);
                        }
                    });

                    $("input[name='events[]']").each(function() {
                        $(this).on("change", function() {

                            // Disable all other checkboxes if workshop is selected
                            if ($(this).val().includes("W")) {
                                if ($(this).is(":checked")) {
                                    $("input[name='events[]']").not(this).prop("disabled", true);
                                } else {
                                    $("input[name='events[]']").not(this).prop("disabled", false);
                                }
                                checkDisabled();
                            }   

                            // Disable all other events if Hasty Mastro is selected
                            if ($(this).val() == "GIZ008EN") {
                                if ($(this).is(":checked")) {
                                    $("input[name='events[]']").not(this).prop("disabled", true);
                                } else {
                                    $("input[name='events[]']").not(this).prop("disabled", false);
                                }
                                checkDisabled();
                            }

                            // Disable Cyber Titan event if Pitch Your AI is selected and vice versa
                            if ($(this).val() == "GIZ003ET") {
                                if ($(this).is(":checked")) {
                                    $("input[name='events[]'][value='GIZ005ET']").prop("disabled", true);
                                } else {
                                    $("input[name='events[]'][value='GIZ005ET']").prop("disabled", false);
                                }
                                checkDisabled();
                            }

                            if ($(this).val() == "GIZ005ET") {
                                if ($(this).is(":checked")) {
                                    $("input[name='events[]'][value='GIZ003ET']").prop("disabled", true);
                                } else {
                                    $("input[name='events[]'][value='GIZ003ET']").prop("disabled", false);
                                }
                                checkDisabled();
                            }

                            // Disable 
                        });
                    });



                } else {
                    message("No Events Found", "error");
                }
            }
        })

        function checkDisabled() {
            $("input[name='events[]']").each(function() {
                if ($(this).is(":disabled")) {
                    $(this).prop("checked", false);
                    $(this).next().css("text-decoration", "line-through");
                } else {
                    $(this).next().css("text-decoration", "none");
                }
            });
        }

        $(document).ready(function() {

            // Hide Loader
            $("#loader-container").fadeOut(1000);


            // Set the value of input fields
            $("input").each(function() {
                $(this).on("blur change keydown", function() {
                    setValue(this);
                });
            });

            // Register Form Validation and Submission
            $("#register").submit(function(e) {
                e.preventDefault();

                var form = $(this);

                // Validate Fields
                var name = $("#name").val();
                var phone = $("#phone").val();
                var email = $("#email").val();
                var college = $("#college").val();
                var year = $("#year").val();
                var dep = $("#dep").val();
                var events = $("input[name='events[]']:checked").length;

                if (name == "") {
                    message("Name is required", "error");
                    $("#name").focus();
                    $(".error").text("Name is required");
                    return false;
                }

                if (phone == "") {
                    message("Contact No is required", "error");
                    $("#phone").focus();
                    $(".error").text("Contact No is required");
                    return false;
                }

                if (!validatePhone(phone)) {
                    message("Invalid Contact No", "error");
                    $("#phone").focus();
                    $(".error").text("Invalid Contact No");
                    return false;
                }

                if (email == "") {
                    message("Email is required", "error");
                    $("#email").focus();
                    $(".error").text("Email is required");
                    return false;
                }

                if (!validateEmail(email)) {
                    message("Invalid Email", "error");
                    $("#email").focus();
                    $(".error").text("Invalid Email");
                    return false;
                }

                if (college == "") {
                    message("College is required", "error");
                    $("#college").focus();
                    $(".error").text("College is required");
                    return false;
                }

                if (year == "") {
                    message("Year is required", "error");
                    $("#year").focus();
                    $(".error").text("Year is required");
                    return false;
                }

                if (dep == "") {
                    message("Department is required", "error");
                    $("#dep").focus();
                    $(".error").text("Department is required");
                    return false;
                }

                if (events == 0) {
                    message("Select atleast one event", "error");
                    $(".error").text("Select atleast one event");
                    return false;
                }

                $(".error").text("");

                var formData = new FormData(form[0]);
                formData.append("register", 1);

                // Show Loader
                $("#loader-container").fadeIn(1000);

                $.ajax({
                    url: "./backend/register.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(data) {

                        var response = JSON.parse(data);

                        // Hide Loader
                        $("#loader-container").fadeOut(1000);

                        if (response.status == "success") {

                            localStorage.setItem("name", name);
                            localStorage.setItem("uid", response.uid);
                            localStorage.setItem("email", email);

                            // Get All Events
                            let events = formData.getAll("events[]");
                            localStorage.setItem("events", JSON.stringify(events));

                            message(response.message, "success");
                            $(".form-container").css("transform", "translateX(-100%)");
                            $(document).scrollTop(0);

                        } else {
                            message(response.message, "error");
                        }
                    }
                });
            });

            // Check Email Exists
            $("#email").on("blur keyup keydown change", function() {
                var email = $(this).val();
                if (email != "") {
                    $.ajax({
                        url: "./backend/register.php",
                        type: "POST",
                        data: {
                            checkMail: true,
                            email: email
                        },
                        success: function(data) {
                            var response = JSON.parse(data);
                            if (response.status == "error") {
                                $('.alreadyExist').html(`
                                    Your Email is already registered.
                                    <button type='button' onclick="showVerifyOTP()">Click Here</button> to verify your Email.
                                `);
                                localStorage.setItem("email", email);
                            }
                        }
                    });
                }
            });

            // OTP Verification
            $("#otp-verify").submit(function(e) {
                e.preventDefault();
                var form = $(this);
                var otp = $("#otp").val();
                if (otp == "") {
                    message("OTP is required", "error");
                    $("#otp").focus();
                    $(".error").text("OTP is required");
                    return false;
                }

                var email = localStorage.getItem("email");

                var formData = form.serialize();
                formData += "&otp-verify=1";
                formData += "&email=" + email;

                // Show Loader
                $("#loader-container").fadeIn(1000);

                $.ajax({
                    url: "./backend/register.php",
                    type: "POST",
                    data: formData,
                    success: function(data) {
                        var response = JSON.parse(data);

                        // Hide Loader
                        $("#loader-container").fadeOut(1000);

                        if (response.status == "success") {
                            message(response.message, "success");

                            // Set the UID in the Payment Screenshot form
                            $("#uid").val(localStorage.getItem("uid"));
                            $("#uid").attr("value", localStorage.getItem("uid"));

                            $("#amount").val(response.amount);
                            $("#amount").attr("value", response.amount);

                            let events = JSON.parse(localStorage.getItem("events"));

                            //  If user only selects GIZ001ET redirect to paper upload page
                            if (events.length == 1) {
                                if (events[0] == "GIZ001ET") {
                                    window.location.href = "./uploadPaper.php";
                                }
                            }

                            $(".form-container").css("transform", "translateX(-200%)");
                            $(document).scrollTop(0);

                        } else {
                            message(response.message, "error");
                        }
                    }
                });
            });

            // Payment Screenshot upload
            $("#paymentProof").submit(function(e) {
                e.preventDefault();
                var form = $(this);
                var paymentDate = $("#payment-date").val();
                var transactionID = $("#transaction-id").val();
                var paymentScreenshot = $("#payment-screenshot").val();

                if (paymentDate == "") {
                    message("Payment Date is required", "error");
                    $("#payment-date").focus();
                    $(".error").text("Payment Date is required");
                    return false;
                }

                if (transactionID == "") {
                    message("Transaction ID is required", "error");
                    $("#transaction-id").focus();
                    $(".error").text("Transaction ID is required");
                    return false;
                }

                if (paymentScreenshot == "") {
                    message("Payment Screenshot is required", "error");
                    $("#payment-screenshot").focus();
                    $(".error").text("Payment Screenshot is required");
                    return false;
                }

                // Validate File
                var file = $("#payment-screenshot")[0].files[0];
                var fileType = file["type"];
                var validImageTypes = ["image/gif", "image/jpeg", "image/png"];
                if ($.inArray(fileType, validImageTypes) < 0) {
                    message("Invalid File Type. Only JPG, PNG and GIF are allowed", "error");
                    $("#payment-screenshot").focus();
                    $(".error").text("Invalid File Type. Only JPG, PNG and GIF are allowed");
                    return false;
                }

                var formData = new FormData(form[0]);
                formData.append("payment_upload", 1);

                // Show Loader
                $("#loader-container").fadeIn(1000);

                $.ajax({
                    url: "./backend/register.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        var response = JSON.parse(data);

                        // Hide Loader
                        $("#loader-container").fadeOut(1000);

                        if (response.status == "success") {
                            message(response.message, "success");
                            message("Your Registration is Successfull. Your gizzmo ID is " + localStorage.getItem("uid"), "success", 5000);

                            $(".form-container").css("transform", "translateX(0%)");
                            $(document).scrollTop(0);

                            localStorage.clear();

                            $("#register")[0].reset();
                            $("#otp-verify")[0].reset();
                            $("#paymentProof")[0].reset();

                            // Redirect to home page
                            setTimeout(function() {
                                window.location.href = "./index.php?registration=success";
                            }, 3000);

                        } else {
                            message(response.message, "error");
                        }
                    }
                });
            });

        });

        // Phone Number Validation
        function validatePhone(phone) {
            var regex = /^[6-9]\d{9}$/;
            return regex.test(phone);
        }

        // Email Validation
        function validateEmail(email) {
            var regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
            return regex.test(email);
        }

        function setValue(el) {
            $(el).attr("value", $(el).val())
        }

        // Resend OTP
        function resendOTP() {
            var email = $("#email").val();
            if (email == "") {
                email = localStorage.getItem("email");
            }

            // Show Loader
            $("#loader-container").fadeIn(500);

            $.ajax({
                url: "./backend/register.php",
                type: "POST",
                data: {
                    resendOTP: true,
                    email: email
                },
                success: function(data) {
                    // Hide Loader
                    $("#loader-container").fadeOut(1000);

                    var response = JSON.parse(data);
                    if (response.status == "success") {
                        message(response.message, "success");
                    } else {
                        message(response.message, "error");
                    }
                }
            });
        }

        // Show Verify OTP
        function showVerifyOTP() {
            localStorage.setItem("email", $("#email").val());

            $('.form-container').css("transform", "translateX(-100%)");
            resendOTP();
        }
    </script>
</body>

</html>