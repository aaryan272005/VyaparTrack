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

$_SESSION['table'] = 'products';

/* FETCH USERS */
$products = include('database/show.php');


?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>View Products</title>

    <link rel="stylesheet" href="css/dashboard.css">

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


            <div class="dashboardContent">

                <div class="dashboard_content_main">

                    <h1 class="section_header">
                        <i class="fa fa-list"></i> List of Products
                    </h1>


                    <div class="users">

                        <p class="userCount"><?= count($products) ?> Products</p>

                        <table>

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Product Name</th>
                                    <th>Description</th>
                                    <th>Created by</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($products as $index => $product) { ?>

                                    <tr>

                                        <td><?= $index + 1 ?></td>

                                        <td class="fname">
                                            <img class="productImages"src="uploads\<?= $product['img'] ?>" alt="">
                                        </td>

                                        <td class="lname"><?= $product['product_name'] ?></td>

                                        <td class="email"><?= $product['description'] ?></td>

                                        <td><?= $product['created_by'] ?></td>

                                        <td><?= date('M d,Y  @h:i:s A', strtotime($product['created_at'])) ?></td>

                                        <td><?= date('M d,Y  @h:i:s A', strtotime($product['updated_at'])) ?></td>

                                        <td class="actionCell">

                                            <a href="#" class="action-btn editUser" data-userid="<?= $product['id'] ?>">

                                                <i class="fa fa-pencil"></i> Edit

                                            </a>

                                            <a href="#" class="action-btn deleteUser" data-userid="<?= $product['id'] ?>">

                                                <i class="fa fa-trash"></i> Delete

                                            </a>

                                        </td>

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

    <script src="js/script.js"></script>

</body>

</html>