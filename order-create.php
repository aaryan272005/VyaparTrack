<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$_SESSION['table'] = 'products';
$user = $_SESSION['user'];
$_SESSION['redirect_to'] = 'order-create.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Orders ~VyaparTrack</title>

    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

    <div id="DashboardMainContainer">

        <?php include('partials/app-sidebar.php'); ?>

        <div class="DashboardContent_container">

            <?php include('partials/app-topNav.php'); ?>

            <div class="dashboardContent">

                <div class="dashboard_content_main">

                    <h1 class="section_header">
                        <i class="fa fa-plus"></i> Create Order
                    </h1>

                    <form action="database/create-order.php" method="POST">

                        <div>

                            <div class="align-right">
                                <button type="button" class="orderProductBtn">Add Another Product</button>
                            </div>

                            <div id="orderProductList">

                                <div class="orderProductRow" id="productRowTemplate">

                                    <div class="align-right">
                                        <button type="button" class="removeProductRowBtn">
                                            <i class="fa fa-trash"></i> Remove
                                        </button>
                                    </div>

                                    <div>
                                        <label>PRODUCT NAME</label>

                                        <select name="product_id[]" class="product_name">

                                            <option value="">Select Product</option>

                                            <?php
                                            $temp = $_SESSION['table'];

                                            $_SESSION['table'] = 'products';
                                            $products = include('database/show.php');

                                            $_SESSION['table'] = $temp;

                                            foreach ($products as $product) {
                                                echo "<option value='" . $product['id'] . "'>" . $product['product_name'] . "</option>";
                                            }
                                            ?>

                                        </select>

                                    </div>

                                    <div class="supplierRows">
                                        <!-- Suppliers will load dynamically -->
                                    </div>

                                </div>

                            </div>

                            <div class="align-right">
                                <button type="submit" class="orderProductSubmitBtn">Submit</button>
                            </div>

                        </div>

                    </form>

                </div>


                <?php
                if (isset($_SESSION['response'])) {
                    $response = $_SESSION['response'];
                    ?>

                    <div class="responseMessage">
                        <p class="<?= $response['success'] ? 'successMessage' : 'errorMessage' ?>">
                            <?= $response['message'] ?>
                        </p>
                    </div>

                    <?php
                    unset($_SESSION['response']);
                }
                ?>

            </div>

        </div>

    </div>

    <script src="js/dashboard.js"></script>

</body>

</html>