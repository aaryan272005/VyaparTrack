<?php

date_default_timezone_set('Asia/Kolkata');
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

                        <table class="products">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Product Name</th>
                                    <th>Description</th>
                                    <th>Supplier</th>
                                    <th>Created By</th>
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
                                            <img src="uploads/products/<?= $product['img'] ?>" width="60">
                                        </td>

                                        <td class="lname"><?= $product['product_name'] ?></td>

                                        <td class="email"><?= $product['description'] ?></td>

                                        <td>
                                            <?php

                                            $query = "SELECT DISTINCT supplier.supplier_name FROM productsupplier JOIN supplier ON supplier.id = productsupplier.supplier WHERE productsupplier.product = :product_id";

                                            $stmt = $conn->prepare($query);
                                            $stmt->execute(['product_id' => $product['id']]);

                                            $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                            if (!empty($suppliers)) {

                                                $names = array_column($suppliers, 'supplier_name');
                                                $uniqueSuppliers = array_unique($names);

                                                foreach ($uniqueSuppliers as $supplier) {
                                                    echo $supplier . "<br>";
                                                }
                                            } else {

                                                echo "No Supplier";
                                            }

                                            ?>
                                        </td>


                                        <!-- CREATED BY -->
                                        <td>
                                            <?php

                                            $query = "SELECT first_name
                                                        FROM users
                                                        WHERE id = :user_id";

                                            $stmt = $conn->prepare($query);
                                            $stmt->execute(['user_id' => $product['created_by']]);

                                            $user = $stmt->fetch(PDO::FETCH_ASSOC);

                                            echo $user['first_name'];

                                            ?>
                                        </td>


                                        <td><?= date('M d,Y  @h:i:s A', strtotime($product['created_at'])) ?></td>

                                        <td><?= date('M d,Y  @h:i:s A', strtotime($product['updated_at'])) ?></td>


                                        <td class="actionCell">

                                            <a href="#" class="action-btn editProduct editBtn"
                                                data-pid="<?= $product['id'] ?>" data-name="<?= $product['product_name'] ?>"
                                                data-description="<?= $product['description'] ?>">

                                                <i class="fa fa-pencil"></i> Edit

                                            </a>


                                            <a href="#" class="action-btn deleteProduct deleteBtn"
                                                data-id="<?= $product['id'] ?>" data-table="products"
                                                data-name="<?= $product['product_name'] ?>">

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
    <script src="js/dashboard.js"></script>
    <script src="js/script.js"></script>

</body>

</html>