<div class="DashboardSidebar" id="DashboardSidebar">

    <div class="dashboard_logo">
        <span class="hindi">व्यापार</span>
        <span class="english">Track</span>
    </div>

    <div class="dashboardSidebar_User">

        <img src="images/user/user2.png">

        <span>
            <?= $user['first_name'] . " " . $user['last_name'] ?>
        </span>

    </div>


    <ul class="dashboard_menu_list">


        <li class="liMenu <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">

            <a href="dashboard.php">

                <i class="fa fa-dashboard"></i>
                <span class="menuText">Dashboard</span>

            </a>

        </li>


        <li class="liMenu <?= ($current_page == 'reports.php') ? 'active' : '' ?>">

            <a href="reports.php">

                <i class="fa fa-chart-line"></i>
                <span class="menuText">Reports</span>

            </a>

        </li>


        <!-- PRODUCT -->

        <li class="liMenu has-submenu 
            <?= ($current_page == 'products-view.php' || $current_page == 'products-add.php') ? 'open active' : '' ?>">

            <a href="#">

                <i class="fa fa-tag"></i>
                <span class="menuText">Product</span>
                <i class="fa fa-angle-down arrow"></i>

            </a>

            <ul class="sub-menu">

                <li>
                    <a href="products-view.php" class="<?= ($current_page == 'products-view.php') ? 'active' : '' ?>">
                        <i class="fa fa-circle"></i>
                        <span class="menuText">View Products</span>
                    </a>
                </li>

                <li>
                    <a href="products-add.php" class="<?= ($current_page == 'products-add.php') ? 'active' : '' ?>">
                        <i class="fa fa-circle"></i>
                        <span class="menuText">Add Products</span>
                    </a>
                </li>

            </ul>

        </li>


        <!-- SUPPLIER -->

        <li class="liMenu has-submenu">

            <a href="#">

                <i class="fa fa-truck"></i>
                <span class="menuText">Supplier</span>

                <i class="fa fa-angle-down arrow"></i>

            </a>

            <ul class="sub-menu">

                <li>
                    <a href="supplier-view.php">
                        <i class="fa fa-circle"></i>
                        <span class="menuText">View Supplier</span>
                    </a>
                </li>

                <li>
                    <a href="supplier-add.php">
                        <i class="fa fa-circle"></i>
                        <span class="menuText">Add Supplier</span>
                    </a>
                </li>

            </ul>

        </li>


        <!-- PURCHASE ORDER -->

        <li class="liMenu has-submenu">

            <a href="#">

                <i class="fa fa-cart-plus"></i>
                <span class="menuText">Purchase Order</span>

                <i class="fa fa-angle-down arrow"></i>

            </a>

            <ul class="sub-menu">

                <li>
                    <a href="order-create.php">
                        <i class="fa fa-circle"></i>
                        <span class="menuText">Create Order</span>
                    </a>
                </li>

                <li>
                    <a href="order-view.php">
                        <i class="fa fa-circle"></i>
                        <span class="menuText">View Orders</span>
                    </a>
                </li>

            </ul>

        </li>


        <!-- USERS -->

        <li class="liMenu has-submenu">

            <a href="#">

                <i class="fa fa-user"></i>
                <span class="menuText">User</span>

                <i class="fa fa-angle-down arrow"></i>

            </a>

            <ul class="sub-menu">

                <li>
                    <a href="users-view.php">
                        <i class="fa fa-circle"></i>
                        <span class="menuText">View Users</span>
                    </a>
                </li>

                <li>
                    <a href="users-add.php">
                        <i class="fa fa-circle"></i>
                        <span class="menuText">Add Users</span>
                    </a>
                </li>

            </ul>

        </li>


    </ul>

</div>