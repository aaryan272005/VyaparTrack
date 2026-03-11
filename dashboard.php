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

/* DATABASE CONNECTION */
require_once('database/connection.php');

/* DETECT CURRENT PAGE */
$current_page = basename($_SERVER['PHP_SELF']);


/* ================= DASHBOARD COUNTS ================= */

// Total Products
$stmt = $conn->query("SELECT COUNT(*) as total FROM products");
$products = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total Suppliers
$stmt = $conn->query("SELECT COUNT(*) as total FROM supplier");
$supplier = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total Users
$stmt = $conn->query("SELECT COUNT(*) as total FROM users");
$total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total Orders
$stmt = $conn->query("SELECT COUNT(*) as total FROM productsupplier");
$total_orders = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - VyaparTrack</title>

    <!-- MAIN CSS -->
    <link rel="stylesheet" href="css/dashboard.css">

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- HIGHCHARTS -->
    <script src="https://code.highcharts.com/highcharts.js"></script>

    <style>
        .dashboardCards {
            display: flex;
            gap: 20px;
            margin-top: 25px;
            flex-wrap: wrap;
        }

        .dashboardCard {
            flex: 1;
            min-width: 220px;
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .dashboardCard i {
            font-size: 30px;
            color: #3498db;
        }

        .dashboardCard h3 {
            margin: 0;
            font-size: 26px;
        }

        .dashboardCard p {
            margin: 0;
            color: #777;
        }


        /* CHART SECTION */

        .dashboardCharts {
            display: flex;
            gap: 20px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .chartBox {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            flex: 1;
            min-width: 450px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        #orderStatusChart,
        #supplierProductChart,
        #deliveryHistoryChart {
            height: 350px;
        }
    </style>

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

                    <h2>Welcome, <?= $user['first_name']; ?> 👋</h2>

                    <p>
                        This is your dashboard. Use the sidebar to manage products, suppliers, orders and users.
                    </p>


                    <!-- DASHBOARD CARDS -->

                    <div class="dashboardCards">

                        <div class="dashboardCard">
                            <i class="fa fa-box"></i>
                            <div>
                                <h3><?= $products ?></h3>
                                <p>Total Products</p>
                            </div>
                        </div>

                        <div class="dashboardCard">
                            <i class="fa fa-truck"></i>
                            <div>
                                <h3><?= $supplier ?></h3>
                                <p>Total Suppliers</p>
                            </div>
                        </div>

                        <div class="dashboardCard">
                            <i class="fa fa-users"></i>
                            <div>
                                <h3><?= $total_users ?></h3>
                                <p>Total Users</p>
                            </div>
                        </div>

                        <div class="dashboardCard">
                            <i class="fa fa-shopping-cart"></i>
                            <div>
                                <h3><?= $total_orders ?></h3>
                                <p>Total Orders</p>
                            </div>
                        </div>
                    </div>



                    <!-- DASHBOARD CHARTS -->

                    <div class="dashboardCharts">

                        <div class="chartBox">
                            <h3>Purchase Orders By Status</h3>
                            <div id="orderStatusChart"></div>
                        </div>

                        <div class="chartBox">
                            <h3>Product Count Assigned To Supplier</h3>
                            <div id="supplierProductChart"></div>
                        </div>

                    </div>


                    <div class="chartBox">
                        <h3>Delivery History Per Day</h3>
                        <div id="deliveryHistoryChart"></div>
                    </div>



                </div>
            </div>

        </div>

    </div>


    <!-- DASHBOARD CHART SCRIPT -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/10.3.3/highcharts.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/dashboard.js"></script>
    <script src="js/dashboard-charts.js"></script>
    <script src="js/script.js"></script>
</body>

</html>