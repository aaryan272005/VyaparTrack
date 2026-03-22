<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// ✅ ADMIN CHECK
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

$_SESSION['table'] = 'supplier';
$_SESSION['redirect_to'] = 'supplier-add.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Supplier ~VyaparTrack</title>

    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

    <div id="DashboardMainContainer">

        <!-- Sidebar -->
        <?php include('partials/app-sidebar.php'); ?>

        <div class="DashboardContent_container">

            <!-- Top Navigation -->
            <?php include('partials/app-topNav.php'); ?>


            <div class="dashboardContent">

                <div class="dashboard_content_main">

                    <h1 class="section_header">
                        <i class="fa fa-plus"></i> Create Supplier
                    </h1>

                    <!-- ⚠ WARNING FOR NON-ADMIN -->
                    <?php if (!$isAdmin): ?>
                        <div style="background:#ffe0e0;color:#b30000;padding:10px;border-radius:5px;margin-bottom:15px;">
                            ⚠ You do not have permission to perform this action. Only admins can create suppliers.
                        </div>
                    <?php endif; ?>


                    <div id="userAddFormContainer">

                        <form action="database/add.php" method="POST" class="userForm">

                            <label>Supplier Name:</label>
                            <input type="text" placeholder="Enter supplier name..." name="supplier_name"
                                <?= !$isAdmin ? 'disabled' : '' ?> required>

                            <label>Location:</label>
                            <input type="text" placeholder="Enter product supplier location..." name="supplier_location"
                                <?= !$isAdmin ? 'disabled' : '' ?> required>

                            <label>Email:</label>
                            <input type="email" placeholder="Enter supplier email..." name="email"
                                <?= !$isAdmin ? 'disabled' : '' ?> required>


                            <!-- ✅ BUTTON CONTROL -->
                            <?php if ($isAdmin): ?>
                                <button type="submit" class="userFormBtn">
                                    <i class="fa fa-plus"></i> Create Supplier
                                </button>
                            <?php else: ?>
                                <button type="button" class="userFormBtn" style="background:#ccc;cursor:not-allowed;">
                                    🔒 Admin Only
                                </button>
                            <?php endif; ?>

                        </form>

                    </div>


                    <!-- Response Message -->

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

    </div>

    <script src="js/dashboard.js"></script>

</body>

</html>