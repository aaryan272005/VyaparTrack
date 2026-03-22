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

// ✅ ADMIN CHECK
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

$_SESSION['table'] = 'users';

/* FETCH USERS */
$users = include('database/show.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>View Users ~VyaparTrack</title>

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
                        <i class="fa fa-list"></i> List of Users
                    </h1>

                    <!-- ⚠ WARNING -->
                    <?php if (!$isAdmin): ?>
                        <div style="background:#ffe0e0;color:#b30000;padding:10px;border-radius:5px;margin-bottom:15px;">
                            ⚠ You have view-only access. Only admins can edit or delete users.
                        </div>
                    <?php endif; ?>

                    <div class="users">

                        <p class="userCount"><?= count($users) ?> Users</p>

                        <table class="users">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($users as $index => $user) { ?>

                                    <tr>

                                        <td><?= $index + 1 ?></td>

                                        <td class="fname"><?= $user['first_name'] ?></td>

                                        <td class="lname"><?= $user['last_name'] ?></td>

                                        <td class="email"><?= $user['email'] ?></td>

                                        <td><?= date('M d,Y  @h:i:s A', strtotime($user['created_at'])) ?></td>

                                        <td><?= date('M d,Y  @h:i:s A', strtotime($user['updated_at'])) ?></td>

                                        <!-- ACTION -->
                                        <td class="actionCell">

                                            <?php if ($isAdmin): ?>

                                                <a href="#" class="action-btn editUser editBtn"
                                                    data-userid="<?= $user['id'] ?>"
                                                    data-fname="<?= $user['first_name'] ?>"
                                                    data-lname="<?= $user['last_name'] ?>"
                                                    data-email="<?= $user['email'] ?>">

                                                    <i class="fa fa-pencil"></i> Edit
                                                </a>

                                                <a href="#" class="action-btn deleteUser deleteBtn"
                                                    data-userid="<?= $user['id'] ?>"
                                                    data-fname="<?= $user['first_name'] ?>"
                                                    data-lname="<?= $user['last_name'] ?>">

                                                    <i class="fa fa-trash"></i> Delete
                                                </a>

                                            <?php else: ?>

                                                <span style="color:#999;">🔒 Admin Only</span>

                                            <?php endif; ?>

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