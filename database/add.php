<?php

session_start();
include('connection.php');

// ✅ AUTH CHECK (FIXED)
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}

// ✅ ADMIN ONLY (SAFE CHECK)
if (($_SESSION['role'] ?? '') !== 'admin') {
    $_SESSION['response'] = [
        'success' => false,
        'message' => 'Access Denied - Admin Only'
    ];

    $redirect = $_SESSION['redirect_to'] ?? 'dashboard.php';
    header("location: ../$redirect");
    exit();
}

$table_name = $_SESSION['table'] ?? '';
$user_id = $_SESSION['user_id'];

// ✅ ALLOWED TABLES (SECURITY)
$allowed_tables = ['products', 'supplier', 'users'];

if (!in_array($table_name, $allowed_tables)) {
    die("Invalid table");
}

// Default redirect
$redirect = $_SESSION['redirect_to'] ?? 'product-add.php';

/* ================= PRODUCT ADD ================= */

if ($table_name == 'products') {

    $product_name = trim($_POST['product_name'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $suppliers    = $_POST['suppliers'] ?? [];

    if (empty($product_name) || empty($description)) {
        $_SESSION['response'] = [
            'success' => false,
            'message' => 'All fields are required'
        ];
        header("location: ../product-add.php");
        exit();
    }

    $image_name = '';

    if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {

        $img = $_FILES['img'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($img['type'], $allowed_types)) {
            $_SESSION['response'] = [
                'success' => false,
                'message' => 'Invalid image type'
            ];
            header("location: ../product-add.php");
            exit();
        }

        $image_name = time() . '_' . basename($img['name']);
        $upload_dir = "../uploads/products/";

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        move_uploaded_file($img['tmp_name'], $upload_dir . $image_name);
    }

    try {

        $conn->beginTransaction();

        $stmt = $conn->prepare("
            INSERT INTO products 
            (product_name, description, img, created_by, created_at, updated_at) 
            VALUES (?, ?, ?, ?, NOW(), NOW())
        ");

        $stmt->execute([
            $product_name,
            $description,
            $image_name,
            $user_id
        ]);

        $product_id = $conn->lastInsertId();

        foreach ($suppliers as $supplier) {

            $stmt = $conn->prepare("
                INSERT INTO productsupplier (supplier, product) 
                VALUES (?, ?)
            ");

            $stmt->execute([$supplier, $product_id]);
        }

        $conn->commit();

        $_SESSION['response'] = [
            'success' => true,
            'message' => 'Product created successfully'
        ];

    } catch (PDOException $e) {

        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        $_SESSION['response'] = [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}


/* ================= SUPPLIER ADD ================= */

if ($table_name == 'supplier') {

    $supplier_name     = trim($_POST['supplier_name'] ?? '');
    $supplier_location = trim($_POST['supplier_location'] ?? '');
    $email             = trim($_POST['email'] ?? '');

    if (empty($supplier_name) || empty($supplier_location) || empty($email)) {
        $_SESSION['response'] = [
            'success' => false,
            'message' => 'All fields are required'
        ];
        header("location: ../supplier-add.php");
        exit();
    }

    try {

        $stmt = $conn->prepare("
            INSERT INTO supplier 
            (supplier_name, supplier_location, email, created_by, created_at, updated_at) 
            VALUES (?, ?, ?, ?, NOW(), NOW())
        ");

        $stmt->execute([
            $supplier_name,
            $supplier_location,
            $email,
            $user_id
        ]);

        $_SESSION['response'] = [
            'success' => true,
            'message' => 'Supplier created successfully'
        ];

    } catch (PDOException $e) {

        $_SESSION['response'] = [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}


/* ================= USER ADD ================= */

if ($table_name == 'users') {

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';

    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        $_SESSION['response'] = [
            'success' => false,
            'message' => 'All fields are required'
        ];
        header("location: ../users-add.php");
        exit();
    }

    $password = password_hash($password, PASSWORD_DEFAULT);

    try {

        $stmt = $conn->prepare("
            INSERT INTO users
            (first_name, last_name, email, password, role, created_at, updated_at)
            VALUES (?, ?, ?, ?, 'user', NOW(), NOW())
        ");

        $stmt->execute([
            $first_name,
            $last_name,
            $email,
            $password
        ]);

        $_SESSION['response'] = [
            'success' => true,
            'message' => 'User successfully added.'
        ];

    } catch (PDOException $e) {

        $_SESSION['response'] = [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }

    header("location: ../users-add.php");
    exit();
}


/* ================= FINAL REDIRECT ================= */

header("location: ../$redirect");
exit();