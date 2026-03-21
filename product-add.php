<?php

require_once('partials/auth.php');

// ✅ SAFE ADMIN CHECK (no warning now)
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// SESSION SETTINGS
$_SESSION['table'] = 'products';
$_SESSION['redirect_to'] = 'product-add.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Products ~ VyaparTrack</title>

    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

    <div id="DashboardMainContainer">

        <!-- SIDEBAR -->
        <?php include('partials/app-sidebar.php'); ?>

        <!-- MAIN CONTENT -->
        <div class="DashboardContent_container">

            <!-- TOP NAV -->
            <?php include('partials/app-topNav.php'); ?>

            <div class="dashboardContent">

                <div class="dashboard_content_main">

                    <h1 class="section_header">
                        <i class="fa fa-plus"></i> Create Product
                    </h1>

                    <!-- ⚠️ WARNING -->
                    <?php if (!$isAdmin): ?>
                        <div style="background:#ffe0e0;color:#b30000;padding:10px;border-radius:5px;margin-bottom:15px;">
                            ⚠ You do not have permission to perform this action. Only admins can create products.
                        </div>
                    <?php endif; ?>

                    <div id="userAddFormContainer">

                        <form action="database/add.php" method="POST" class="userForm" enctype="multipart/form-data">

                            <!-- Product Name -->
                            <label>Product Name:</label>
                            <input type="text" 
                                   placeholder="Enter Product Name..." 
                                   name="product_name" 
                                   <?= !$isAdmin ? 'disabled' : '' ?> 
                                   required>

                            <!-- Product Description -->
                            <label>Description:</label>
                            <textarea class="productTextArea" 
                                      placeholder="Enter Product Description..." 
                                      name="description"
                                      <?= !$isAdmin ? 'disabled' : '' ?> 
                                      required></textarea>

                            <!-- Suppliers -->
                            <label>Suppliers:</label>
                            <select name="suppliers[]" 
                                    id="supplierInput" 
                                    multiple 
                                    <?= !$isAdmin ? 'disabled' : '' ?> 
                                    required>
                                <option value="">Select Supplier</option>

                                <?php
                                $temp = $_SESSION['table'];

                                $_SESSION['table'] = 'supplier';
                                $suppliers = include('database/show.php');

                                $_SESSION['table'] = $temp;

                                foreach ($suppliers as $supplier) {
                                    echo "<option value='" . $supplier['id'] . "'>" . $supplier['supplier_name'] . "</option>";
                                }
                                ?>
                            </select>

                            <!-- Upload Image -->
                            <div class="imageUploadWrapper">
                                <label for="img" class="uploadBtn">
                                    <i class="fa fa-upload"></i> Upload Product Image
                                </label>
                                <input type="file" 
                                       name="img" 
                                       id="img" 
                                       hidden 
                                       <?= !$isAdmin ? 'disabled' : '' ?> 
                                       required>
                                <span id="fileName">No file selected</span>
                            </div>

                            <!-- ✅ BUTTON FIX -->
                            <?php if ($isAdmin): ?>
                                <button type="submit" class="userFormBtn">
                                    <i class="fa fa-plus"></i> Create Product
                                </button>
                            <?php else: ?>
                                <button type="button" 
                                        class="userFormBtn" 
                                        disabled 
                                        style="opacity:0.6; cursor:not-allowed;">
                                    <i class="fa fa-lock"></i> Admin Only
                                </button>
                            <?php endif; ?>

                        </form>

                    </div>

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

    <script src="js/dashboard.js"></script>

    <script>
        // Show file name after upload
        const fileInput = document.getElementById("img");
        if(fileInput){
            fileInput.addEventListener("change", function() {
                const fileName = this.files[0]?.name || "No file selected";
                document.getElementById("fileName").innerText = fileName;
            });
        }
    </script>

</body>

</html>