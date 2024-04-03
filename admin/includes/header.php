<header>
    <input type="checkbox" id="toggleInput">
    <label for="toggleInput">
        <span></span>
        <span></span>
        <span></span>
    </label>
    <div class="header">
        <img src="../assets/img/logo.png" alt="Erode Sengunthar">
        <h2>
            GIZZMO
        </h2>
    </div>
    <nav>
        <ul>
            <li class="dashboard">
                <a href="./dashboard.php">
                    <i class="fa fa-home" aria-hidden="true" title="Dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="eventList">
                <a href="./eventList.php">
                    <i class="fa fa-calendar" aria-hidden="true" title="Events"></i>
                    <span>Events</span>
                </a>
            </li>
            <li class="userList">
                <a href="./userList.php">
                    <i class="fa fa-users" aria-hidden="true" title="Participants"></i>
                    <span>Participants</span>
                </a>
            </li>
            <li class="paperList">
                <a href="./paperList.php">
                    <i class="fa fa-file-powerpoint" aria-hidden="true" title="Uploaded Papers"></i>
                    <span>Uploaded Papers</span>
                </a>
            </li>
            <li class="paymentList">
                <a href="./paymentList.php">
                    <i class="fa fa-file-invoice" aria-hidden="true" title="Payments"></i>
                    <span>Payments</span>
                </a>
            </li>
            <li class="generateURL">
                <a href="./generateURL.php">
                    <i class="fa fa-link" aria-hidden="true" title="URL List"></i>
                    <span>URL List</span>
                </a>
            </li>
            <li class="logout">
                <a href="./logout.php">
                    <i class="fa fa-arrow-right-from-bracket" aria-hidden="true" title="Logout"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>
</header>

<script>
    // Toggle Sidebar
    $("#toggleInput").click(function() {
        $("header").toggleClass("active");
    });

    $(document).ready(function() {
        // Active Link
        var path = window.location.pathname.split("/").pop();
        var link = path.split(".")[0];
        $("li." + link).addClass("active");

        // Checkbox true if window width is less than 768px
        if ($(window).width() > 768) {
            $("#toggleInput").prop("checked", true);
        } else {
            $("#toggleInput").prop("checked", false);
        }

        // Resize Header
        resizeHeader();
    });

    $(window).resize(resizeHeader());

    function resizeHeader() {
        if ($(window).width() < 768) {
            $("header").removeClass("active");
        } else {
            $("header").addClass("active");
        }
    }
</script>