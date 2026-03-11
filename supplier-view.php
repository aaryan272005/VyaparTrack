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
    s.id,
    s.supplier_name,
    s.supplier_location,
    s.email,
    s.created_by,
    s.created_at,
    s.updated_at,
    u.first_name,
    u.last_name,
    GROUP_CONCAT(p.product_name SEPARATOR ', ') AS products
FROM supplier s
LEFT JOIN users u 
    ON s.created_by = u.id
LEFT JOIN productsupplier ps 
    ON s.id = ps.supplier
LEFT JOIN products p 
    ON ps.product = p.id
GROUP BY 
    s.id,
    s.supplier_name,
    s.supplier_location,
    s.email,
    s.created_by,
    s.created_at,
    s.updated_at,
    u.first_name,
    u.last_name
ORDER BY s.created_at DESC;
";

$stmt = $conn->prepare($query);
$stmt->execute();

$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <title>View Suppliers</title>

    <link rel="stylesheet" href="css/dashboard.css">
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
                        <i class="fa fa-list"></i> List of Suppliers
                    </h1>

                    <div class="users">

                        <p class="userCount"><?= count($suppliers) ?> Suppliers</p>

                        <table class="suppliers">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Supplier Name</th>
                                    <th>Supplier Location</th>
                                    <th>Contact Details</th>
                                    <th>Products</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($suppliers as $index => $supplier) { ?>

                                    <tr>

                                        <td><?= $index + 1 ?></td>

                                        <td class="supplierName"><?= $supplier['supplier_name'] ?></td>

                                        <td class="supplierLocation"><?= $supplier['supplier_location'] ?></td>

                                        <td class="supplierEmail"><?= $supplier['email'] ?></td>

                                        <td>

                                            <?php

                                            $productQuery = "SELECT p.product_name FROM productsupplier ps JOIN products p  ON ps.product = p.id WHERE ps.supplier = :supplier_id ";

                                            $stmt = $conn->prepare($productQuery);

                                            $stmt->execute([
                                                'supplier_id' => $supplier['id']
                                            ]);

                                            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                            if (!empty($products)) {

                                                echo "<ul>";

                                                foreach ($products as $product) {
                                                    echo "<li>" . $product['product_name'] . "</li>";
                                                }

                                                echo "</ul>";

                                            } else {

                                                echo "-";

                                            }

                                            ?>

                                        </td>

                                        <td><?= $supplier['first_name'] . ' ' . $supplier['last_name'] ?></td>

                                        <td><?= date('M d,Y  @h:i:s A', strtotime($supplier['created_at'])) ?></td>

                                        <td><?= date('M d,Y  @h:i:s A', strtotime($supplier['updated_at'])) ?></td>

                                        <td class="actionCell">

                                            <a href="#" class="action-btn editSupplier editBtn"
                                                data-id="<?= $supplier['id'] ?>"
                                                data-name="<?= $supplier['supplier_name'] ?>"
                                                data-location="<?= $supplier['supplier_location'] ?>"
                                                data-email="<?= $supplier['email'] ?>">

                                                <i class="fa fa-pencil"></i> Edit

                                            </a>


                                            <a href="#" class="action-btn deleteSupplier deleteBtn"
                                                data-id="<?= $supplier['id'] ?>"
                                                data-name="<?= $supplier['supplier_name'] ?>">

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