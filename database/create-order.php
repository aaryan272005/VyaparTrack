<?php

session_start();
include('connection.php');

if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit();
}

$user = $_SESSION['user'];
$created_by = $user['id'];

$product_ids = $_POST['product_id'];
$supplier_ids = $_POST['supplier_id'];
$quantities = $_POST['quantity'];

try {

    $conn->beginTransaction();

    for ($i = 0; $i < count($supplier_ids); $i++) {

        $supplier = $supplier_ids[$i];
        $product = $product_ids[$i];
        $quantity = $quantities[$i];

        if ($quantity <= 0) {
            continue;
        }

        $stmt = $conn->prepare("
            INSERT INTO productsupplier
            (supplier, product, quantity_order, quantity_received, quantity_remaining, stats, created_by, created_at, updated_at)
            VALUES (?, ?, ?, 0, ?, 'pending', ?, NOW(), NOW())
        ");

        $stmt->execute([
            $supplier,
            $product,
            $quantity,
            $quantity,
            $created_by
        ]);
    }

    $conn->commit();

    $_SESSION['response'] = [
        'success' => true,
        'message' => 'Order created successfully'
    ];

} catch (PDOException $e) {

    $conn->rollBack();

    $_SESSION['response'] = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

header('Location: ../order-create.php');
exit();