<?php

date_default_timezone_set('Asia/Kolkata');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

include('database/connection.php');

$query = "SELECT 
    ps.id,
    ps.quantity_order,
    ps.quantity_received,
    ps.quantity_remaining,
    ps.stats,
    ps.created_at,

    p.product_name,
    s.supplier_name,
    u.first_name,
    u.last_name

FROM productsupplier ps

LEFT JOIN products p
    ON ps.product = p.id

LEFT JOIN supplier s
    ON ps.supplier = s.id

LEFT JOIN users u
    ON ps.created_by = u.id

ORDER BY ps.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->execute();

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <title>View Orders</title>

    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/order.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

    <div id="DashboardMainContainer">

        <!-- SIDEBAR -->
        <?php include('partials/app-sidebar.php'); ?>

        <div class="DashboardContent_container">

            <?php include('partials/app-topNav.php'); ?>

            <div class="dashboardContent">

                <div class="dashboard_content_main">

                    <h1 class="section_header">
                        <i class="fa fa-list"></i> List of Purchase Orders
                    </h1>

                    <div class="users">

                        <p class="userCount"><?= count($orders) ?> Orders</p>

                        <table>

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Supplier</th>
                                    <th>Qty Ordered</th>
                                    <th>Qty Received</th>
                                    <th>Qty Remaining</th>
                                    <th>Status</th>
                                    <th>Ordered By</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php if (!empty($orders)) { ?>

                                    <?php foreach ($orders as $index => $order) { ?>

                                        <tr id="orderRow<?= $order['id'] ?>">

                                            <td><?= $index + 1 ?></td>
                                            <td><?= $order['product_name'] ?></td>
                                            <td><?= $order['supplier_name'] ?></td>
                                            <td><?= $order['quantity_order'] ?></td>
                                            <td><?= $order['quantity_received'] ?></td>
                                            <td><?= $order['quantity_remaining'] ?></td>

                                            <td>
                                                <span class="status status-<?= $order['stats'] ?>">
                                                    <?= $order['stats'] ?>
                                                </span>
                                            </td>

                                            <td><?= $order['first_name'] . ' ' . $order['last_name'] ?></td>
                                            <td><?= date('M d,Y  @h:i:s A', strtotime($order['created_at'])) ?></td>
                                            <td class="actionCell">

                                                <button class="updateOrderBtn action-btn" data-id="<?= $order['id'] ?>"
                                                    data-product="<?= $order['product_name'] ?>"
                                                    data-ordered="<?= $order['quantity_order'] ?>"
                                                    data-received="<?= $order['quantity_received'] ?>"
                                                    data-supplier="<?= $order['supplier_name'] ?>"
                                                    data-status="<?= $order['stats'] ?>">

                                                    <i class="fa fa-edit"></i> Update

                                                </button>


                                                <button class="viewDeliveryBtn action-btn" data-id="<?= $order['id'] ?>">

                                                    <i class="fa fa-truck"></i> Deliveries

                                                </button>

                                                <button class="deleteOrderBtn action-btn deleteBtn"
                                                    data-id="<?= $order['id'] ?>"
                                                    data-name="<?= $order['product_name'] ?>">

                                                    <i class="fa fa-trash"></i> Delete

                                                </button>

                                            </td>

                                        </tr>

                                    <?php } ?>

                                <?php } else { ?>

                                    <tr>
                                        <td colspan="10" style="text-align:center">No Orders Found</td>
                                    </tr>


                                <?php } ?>

                            </tbody>
                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/order.js"></script>
    <script src="js/dashboard.js"></script>
    <script src="js/script.js"></script>

</body>

</html>