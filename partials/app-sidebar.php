<?php
$user = $_SESSION['user'];
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="DashboardSidebar" id="DashboardSidebar">

    <div class="dashboard_logo">
        <span class="hindi">व्यापार</span>
        <span class="english">Track</span>
    </div>

    <div class="dashboardSidebar_User">

        <img src="images/user/user1.png">

        <span>
            <?= $user['first_name'] . " " . $user['last_name'] ?>
        </span>

    </div>


    <ul class="dashboard_menu_list">


        <!-- DASHBOARD -->

        <li class="liMenu <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">

            <a href="dashboard.php">

                <i class="fa-solid fa-dashboard"></i>
                <span class="menuText">Dashboard</span>

            </a>

        </li>


        <!-- REPORTS -->

        <li class="liMenu <?= ($current_page == 'reports.php') ? 'active' : '' ?>">

            <a href="reports.php">

                <i class="fa-solid fa-chart-line"></i>
                <span class="menuText">Reports</span>

            </a>

        </li>


        <!-- PRODUCT -->

        <li class="liMenu has-submenu 
        <?= ($current_page == 'product-view.php' || $current_page == 'product-add.php') ? 'open active' : '' ?>">

            <a href="javascript:void(0)">

                <i class="fa-solid fa-tag"></i>
                <span class="menuText">Product</span>
                <i class="fa fa-angle-down arrow"></i>

            </a>

            <ul class="sub-menu">

                <li>
                    <a href="product-view.php"
                        class="<?= ($current_page == 'product-view.php') ? 'active' : '' ?>">

                        <i class="fa-solid fa-circle"></i>
                        <span class="menuText">View Products</span>

                    </a>
                </li>

                <li>
                    <a href="product-add.php"
                        class="<?= ($current_page == 'product-add.php') ? 'active' : '' ?>">

                        <i class="fa-solid fa-circle"></i>
                        <span class="menuText">Add Products</span>

                    </a>
                </li>

            </ul>

        </li>


        <!-- SUPPLIER -->

        <li class="liMenu has-submenu
        <?= ($current_page == 'supplier-view.php' || $current_page == 'supplier-add.php') ? 'open active' : '' ?>">

            <a href="javascript:void(0)">

                <i class="fa-solid fa-truck"></i>
                <span class="menuText">Supplier</span>

                <i class="fa fa-angle-down arrow"></i>

            </a>

            <ul class="sub-menu">

                <li>
                    <a href="supplier-view.php"
                        class="<?= ($current_page == 'supplier-view.php') ? 'active' : '' ?>">

                        <i class="fa-solid fa-circle"></i>
                        <span class="menuText">View Supplier</span>

                    </a>
                </li>

                <li>
                    <a href="supplier-add.php"
                        class="<?= ($current_page == 'supplier-add.php') ? 'active' : '' ?>">

                        <i class="fa-solid fa-circle"></i>
                        <span class="menuText">Add Supplier</span>

                    </a>
                </li>

            </ul>

        </li>


        <!-- PURCHASE ORDER -->

        <li class="liMenu has-submenu
        <?= ($current_page == 'order-view.php' || $current_page == 'order-create.php') ? 'open active' : '' ?>">

            <a href="javascript:void(0)">

                <i class="fa-solid fa-cart-plus"></i>
                <span class="menuText">Purchase Order</span>

                <i class="fa fa-angle-down arrow"></i>

            </a>

            <ul class="sub-menu">

                <li>
                    <a href="order-view.php"
                        class="<?= ($current_page == 'order-view.php') ? 'active' : '' ?>">

                        <i class="fa-solid fa-circle"></i>
                        <span class="menuText">View Orders</span>

                    </a>
                </li>

                <li>
                    <a href="order-create.php"
                        class="<?= ($current_page == 'order-create.php') ? 'active' : '' ?>">

                        <i class="fa-solid fa-circle"></i>
                        <span class="menuText">Create Order</span>

                    </a>
                </li>

            </ul>

        </li>

        <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
            <li class="<?= ($current_page == 'pos.php') ? 'active' : '' ?>">
                <a href="pos.php">
                    <i class="fa-solid fa-store"></i> POS
                </a>
            </li>
        <?php endif; ?>


        <!-- USERS -->

        <li class="liMenu has-submenu
        <?= ($current_page == 'users-view.php' || $current_page == 'users-add.php') ? 'open active' : '' ?>">

            <a href="javascript:void(0)">

                <i class="fa-solid fa-user"></i>
                <span class="menuText">User</span>

                <i class="fa fa-angle-down arrow"></i>

            </a>

            <ul class="sub-menu">

                <li>
                    <a href="users-view.php"
                        class="<?= ($current_page == 'users-view.php') ? 'active' : '' ?>">

                        <i class="fa-solid fa-circle"></i>
                        <span class="menuText">View Users</span>

                    </a>
                </li>

                <li>
                    <a href="users-add.php"
                        class="<?= ($current_page == 'users-add.php') ? 'active' : '' ?>">

                        <i class="fa-solid fa-circle"></i>
                        <span class="menuText">Add Users</span>

                    </a>
                </li>

            </ul>

        </li>


    </ul>

</div>