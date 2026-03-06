<?php

/* SAFE SESSION START */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* LOGIN CHECK */
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user = $_SESSION['user'];

/* DETECT CURRENT PAGE FOR ACTIVE MENU */
$current_page = basename($_SERVER['PHP_SELF']);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - VyaparTrack</title>

    <!-- MAIN DASHBOARD CSS -->
    <link rel="stylesheet" href="css/dashboard.css">

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>


<body>

    <div id="DashboardMainContainer">

        <!-- SIDEBAR -->
        <?php include('partials/app-sidebar.php'); ?>


        <!-- MAIN CONTENT -->
        <div class="DashboardContent_container" id="DashboardContent_container">


            <!-- TOP NAV -->
            <?php include('partials/app-topNav.php'); ?>


            <!-- PAGE CONTENT -->
            <div class="dashboardContent">

                <div class="dashboard_content_main">

                    <!-- EMPTY DASHBOARD -->

                    <h2>Welcome, <?= $user['first_name']; ?> 👋</h2>

                    <p>
                        This is your dashboard.
                        Use the sidebar to manage products, suppliers, orders and users.
                    </p>

                </div>

            </div>

        </div>

    </div>


    <!-- DASHBOARD JS -->
    <script src="js/dashboard.js"></script>

</body>

</html>