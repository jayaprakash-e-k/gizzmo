<header>
    <a href="./" class="logo">
        <img src="./assets/img/logo.png" class="logo" alt="ESEC">
        <img src="./assets/img/symp_logo.png" class="logo" alt="ESEC">
    </a>
    <input type="checkbox" id="toggle-btn">
    <label for="toggle-btn" id="toggle">
        <span style="--i:2"></span>
        <span style="--i:1"></span>
        <span style="--i:2"></span>
    </label>
    <ul>
        <li>
            <a href="./#home">HOME</a>
        </li>
        <li>
            <a href="./#about-us">ABOUT US</a>
        </li>
        <li>
            <a href="./#events">EVENTS</a>
        </li>
        <li>
            <a href="./registerForm.php">REGISTER</a>
        </li>
        <li>
            <a href="./uploadPaper.php">UPLOAD ABSTRACT</a>
        </li>
        <li>
            <a href="./assets/GIZZMO-BROCHURE.pdf">GET BROCHURE</a>
        </li>
    </ul>
</header>

<script>
    // Active Header if scroll is not at top
    $(document).ready(function() {

        // Close Header if anchor is clicked
        $('header ul li a').click(function() {
            $("#toggle-btn").prop("checked", false);
        });

        $("#toggle-btn").change(headerOverflow);
        headerOverflow();

        if (window.location.pathname != "/gizzmo/") {
            $('header').addClass('active');
            return;
        }

        $(window).scroll(headerScroll);
        headerScroll();
    });

    function headerScroll() {
        if ($(window).scrollTop() > 0) {
            $('header').addClass('active');
        } else {
            $('header').removeClass('active');
        }
    }

    function headerOverflow() {
        if ($("#toggle-btn").is(":checked")) {
            $('header').addClass('overflow');
        } else {
            $('header').removeClass('overflow');
        }
    }

    // Scroll Page if in same page
    $(document).ready(function() {
        $('a[href^="#"]').on('click', function(event) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                event.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top
                }, 1000);
            }
        });
    });

</script>