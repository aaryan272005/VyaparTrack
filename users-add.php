<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$_SESSION['table'] = 'users';
$user = $_SESSION['user'];
$_SESSION['redirect_to'] = 'users-add.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Users</title>

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



                    <div id="userAddFormContainer">

                        <form action="database/add.php" method="POST" class="userForm">

                            <label>First Name:</label>
                            <input type="text" name="first_name">

                            <label>Last Name:</label>
                            <input type="text" name="last_name">

                            <label>Email:</label>
                            <input type="email" name="email">

                            <label>Password:</label>
                            <input type="password" name="password">

                            <button class="userFormBtn">
                                <i class="fa fa-plus"></i> Add User
                            </button>

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