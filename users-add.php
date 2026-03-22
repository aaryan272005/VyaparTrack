<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// ✅ ADMIN CHECK
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

$_SESSION['table'] = 'users';
$_SESSION['redirect_to'] = 'users-add.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Users ~VyaparTrack</title>

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
                        <i class="fa fa-plus"></i> Create User
                    </h1>

                    <!-- ⚠ WARNING -->
                    <?php if (!$isAdmin): ?>
                        <div style="background:#ffe0e0;color:#b30000;padding:10px;border-radius:5px;margin-bottom:15px;">
                            ⚠ You do not have permission to perform this action. Only admins can create users.
                        </div>
                    <?php endif; ?>


                    <div id="userAddFormContainer">

                        <form action="database/add.php" method="POST" class="userForm">

                            <label>First Name:</label>
                            <input type="text" name="first_name" <?= !$isAdmin ? 'disabled' : '' ?>>

                            <label>Last Name:</label>
                            <input type="text" name="last_name" <?= !$isAdmin ? 'disabled' : '' ?>>

                            <label>Email:</label>
                            <input type="email" name="email" <?= !$isAdmin ? 'disabled' : '' ?>>

                            <label>Password:</label>
                            <input type="password" name="password" <?= !$isAdmin ? 'disabled' : '' ?>>

                            <!-- ✅ BUTTON CONTROL -->
                            <?php if ($isAdmin): ?>
                                <button type="submit" class="userFormBtn">
                                    <i class="fa fa-plus"></i> Add User
                                </button>
                            <?php else: ?>
                                <button type="button" class="userFormBtn" style="background:#ccc;cursor:not-allowed;">
                                    🔒 Admin Only
                                </button>
                            <?php endif; ?>

                        </form>

                        <!-- RESPONSE MESSAGE -->
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

    </div>

    <script src="js/dashboard.js"></script>

</body>

</html>