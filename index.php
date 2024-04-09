<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GIZZMO 2K24 - An National Level Technical Symposium organised by Dept. of CSE | ESEC</title>
    <script src="https://unpkg.com/scrollreveal"></script>
    <?php include "./includes/include.php"; ?>

    <style>
        body {
            background: rgb(47, 0, 131);
            background: linear-gradient(132deg, #140F16 0%, #140F45 100%);
            max-width: 100%;
            overflow-x: hidden;
        }
    </style>
</head>

<body>

    <?php

    include "./includes/loader.html";
    include "./includes/header.php";

    ?>

    <button id="scrollToTop">
        <i class="fa-solid fa-chevron-up"></i>
        <p class="text">Back to Top</p>
    </button>


    <div class="home" id="home">

        <img src="./assets/img/symp_logo.png" alt="GIZZMO Logo">

        <div class="content">

            <div class="timer" id="timer">
                <h2>Events starts in</h2>
                <div class="launchtime">
                    <div class="cd">
                        <p id="days"></p>
                        <span>Days</span>
                    </div>
                    <div class="cd">
                        <p id="hours"></p>
                        <span>Hours</span>
                    </div>
                    <div class="cd">
                        <p id="minutes"></p>
                        <span>Minutes</span>
                    </div>
                    <div class="cd">
                        <p id="seconds"></p>
                        <span>Seconds</span>
                    </div>
                </div>
            </div>

            <!-- Registration CTA -->
            <div class="cta">

                <a href="./registerForm.php" class="cta-btn">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    Register Now
                </a>

                <!-- Events CTA -->
                <a href="#events" class="cta-btn">
                    Explore Events
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="about" id="about-us">
        <h1><span>J</span>oin with us</h1>
        <p>The Department of Computer Science and Engineering has been successfully functioning since 1998. It offers B.E. (Computer Science and Engineering) and M.E. (Computer Science and Engineering). The MoU signed by the Department are CISCO, Infosys (Campus Connect), MICROSOFT (Campus Agreement) and ORACLE. It organizes Symposia, Exhibitions, Conferences, Seminars and Workshops for both students and Faculty belonging to various Technical Educational Institutions, Research Scholars of Research Institutes and Industries all over India. The Department is Accredited by NBA, AICTE since 2009. The department comprises of 7 centralized air conditioned Computer Centers with 300 systems, state of the art computing facilities with sufficient power supply backup. Our students are placed in various top MNCs like CTS, TCS, Wipro, Infosys, IBM, Tech Mahendra, IBM, EDS, SAP Labs, Accenture, Mind Tree, Hexaware, etc.</p>
    </div>

    <div class="events-no">
        <div class="event slide-left">
            <h1 id="technical">7</h1>
            <p>Technical events</p>
        </div>
        <div class="event slide-down">
            <h1 id="workshop">1</h1>
            <p>workshop</p>
        </div>
        <div class="event slide-right   ">
            <h1 id="non-technical">2</h1>
            <p>Non-Technical events</p>
        </div>
    </div>

    <div class="events" id="events">
        <h1>Events</h1>

        <h3>Technical</h3>
        <div class="tech">
        </div>

        <h3>Non-Technical</h3>
        <div class="ntech">
        </div>
    </div>

    <!-- whatsapp button -->
    <div class="wa-container">
        <span> <i class="fa-solid fa-arrow-left"></i>Join Us To Know More</span>
        <a id="wa-btn" href="https://chat.whatsapp.com/DU85XsXdObI5TwMuhldzRu" role="button" target="_blank">
            <i class="fa-brands fa-whatsapp"></i>
        </a>
    </div>

    <?php include "./includes/footer.php"; ?>
</body>
<script>
    // Whatsapp Button
    setInterval(function(){
        $('.wa-container span').toggleClass('active');
    }, 4000);

    // Show Loader
    $("#loader-container").fadeIn(1000);

    // Get all events
    $.ajax({
        url: "./backend/eventOperation.php",
        method: "GET",
        data: {
            getAllEvents: 1
        },
        success: function(data) {
            data = JSON.parse(data);
            if (data.status == "error" || data.events.length == 0) {
                return;
            }
            data = data.events;

            var techEventsCount = data.filter(function(event) {
                return (event.EID).includes("T");
            }).length;
            var workshopCount = data.filter(function(event) {
                return (event.EID).includes("W");
            }).length;
            var nTechEventsCount = data.filter(function(event) {
                return (event.EID).includes("N");
            }).length;

            $("#technical").text(techEventsCount);
            $("#workshop").text(workshopCount);
            $("#non-technical").text(nTechEventsCount);

            var sliderClass = [
                "slide-up",
                "slide-right",
                "slide-left",
                "slide-down"
            ];

            var i = 0;
            data.forEach(function(event) {
                var eventCard = `<div class="event-card ${sliderClass[i%4]}" onclick="openPopup('${event.EID}')">
                                    <div class='event-card-bg'></div>
                                    <img src="./uploads/events/${event.FILE}" alt="${event.EVENT_NAME}">
                                    <h4>${event.EVENT_NAME} ${event.EID.includes("W") ? "(Workshop)" : ""}</h4>
                                    <span>Click Here to More info</span>
                                </div>`;

                if ((event.EID).includes("N")) {
                    $(".events .ntech").append(eventCard);
                } else {
                    $(".events .tech").append(eventCard);
                }
                i++;
            });

            scrollReveal()
        }
    });

    $(document).ready(function() {

        // Hide Laoder
        $("#loader-container").fadeOut(1000);
        scrollReveal();

        // Scroll to top
        $("#scrollToTop").on("click", function() {
            $("html, body").animate({
                scrollTop: 0
            }, 1000);
        });

        // Show Registration Success message
        const urlParams = new URLSearchParams(window.location.search);
        const registration = urlParams.get('registration');
        if (registration == "success") {
            message("Registration Successfull", "success");
        }
    });

    function openPopup(eid) {

        // Show Loader
        $("#loader-container").fadeIn(1000);

        $.ajax({
            url: "./backend/eventOperation.php",
            method: "GET",
            data: {
                getEvent: 1,
                EID: eid
            },
            success: function(data) {
                data = JSON.parse(data);

                // Hide Laoder
                $("#loader-container").fadeOut(1000);

                if (data.status == "error") {
                    return;
                }

                data = data.event;

                if (data.RULES == null) {
                    data.RULES = "";
                }
                var rule = data.RULES.split(".");
                var rules = "";
                rule.forEach(function(r) {
                    if (r != "") {
                        rules += `<li>${r}</li>`;
                    }
                });

                var startTime = new Date(data.EVENT_DATE + " " + data.START).toLocaleString(
                    'en-US', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true,
                        timeZone: 'IST'
                    });

                var endTime = new Date(data.EVENT_DATE + " " + data.END).toLocaleString(
                    'en-US', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true,
                        timeZone: 'IST'
                    });



                var popup = `<div class="popup-container">
                                <div class="popup-glow">
                                    <div class="popup">
                                        <h2 class="popup-header">${data.EVENT_NAME}</h2>
                                        <div class="popup-content">
                                            <h3>Description</h3>
                                            <p>
                                                ${data.DESCRIPTION}
                                            </p>
                                        </div>
                                        ${
                                            rules == "" ? "" :
                                            `<div class="popup-content">
                                                <h3>Rules</h3>
                                                <ul>
                                                    ${rules}
                                                </ul>
                                            </div>`
                                        }
                                        <div class="popup-content">
                                            <p><b>Venue:</b>  ${data.VENUE}</p><br>
                                            <p><b>Time: &nbsp;</b> ${data.EVENT_DATE} - ${startTime} to ${endTime} </p>
                                        </div>
                                        <div class="popup-content">
                                            <h3>Coordinator</h3>
                                            <p>${data.CO_NAME} &nbsp; - &nbsp; +91-${data.CO_NO}</p>
                                        </div>
                                        <button onclick="closePopup()">
                                            Understand
                                        </button>
                                    </div>
                                </div>
                            </div>`;

                $("body").append(popup);
            }
        });
    }

    function scrollReveal() {
        // Scroll Reveal
        ScrollReveal().reveal('.slide-up', {
            distance: '100px',
            origin: 'bottom',
            duration: 1000,
            delay: 50,
            easing: 'ease',
            reset: true,
            opacity: 0
        });

        ScrollReveal().reveal('.slide-right', {
            distance: '100px',
            origin: 'right',
            duration: 1000,
            delay: 100,
            easing: 'ease',
            reset: true,
            opacity: 0
        });

        ScrollReveal().reveal('.slide-left', {
            distance: '100px',
            origin: 'left',
            duration: 1000,
            delay: 100,
            easing: 'ease',
            reset: true,
            opacity: 0
        });

        ScrollReveal().reveal('.slide-down', {
            distance: '100px',
            origin: 'top',
            duration: 1000,
            delay: 100,
            easing: 'ease',
            reset: true,
            opacity: 0
        });
    }

    // TODO: Set Symposium Date Time
    var count = new Date("april 22, 2024 09:30:00").getTime();
    setInterval(function() {
        var now = new Date().getTime();
        var distance = count - now;

        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("days").innerHTML = days;
        document.getElementById("hours").innerHTML = hours;
        document.getElementById("minutes").innerHTML = minutes;
        document.getElementById("seconds").innerHTML = seconds;
    }, 1000);

    function closePopup() {
        $(".popup-container").remove();
    }

    $(".popup-container").on("click", function(e) {
        if (e.target == this) {
            closePopup();
        }
    })
</script>

</html>