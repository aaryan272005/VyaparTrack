<?php

session_start();
include('connection.php');

header('Content-Type: application/json');

// ✅ AUTH CHECK
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// ✅ ADMIN ONLY
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Access Denied']);
    exit();
}

// INPUTS
$id = intval($_POST['id'] ?? 0);
$table = $_POST['table'] ?? '';

if (!$id || !$table) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

// ✅ ALLOWED TABLES (SECURITY)
$allowed_tables = ['products', 'supplier', 'productsupplier', 'users'];

if (!in_array($table, $allowed_tables)) {
    echo json_encode(['success' => false, 'message' => 'Invalid table']);
    exit();
}

try {

    $conn->beginTransaction();

    /* ================= SPECIAL CASES ================= */

    if ($table === 'supplier') {

        // Delete supplier relations first
        $stmt = $conn->prepare("DELETE FROM productsupplier WHERE supplier = :id");
        $stmt->execute([':id' => $id]);

    }

    if ($table === 'users') {

        // ✅ Prevent self delete (FIXED)
        if ($id == $_SESSION['user_id']) {
            echo json_encode([
                'success' => false,
                'message' => 'You cannot delete yourself'
            ]);
            exit();
        }

    }

    /* ================= MAIN DELETE ================= */

    $stmt = $conn->prepare("DELETE FROM $table WHERE id = :id");
    $stmt->execute([':id' => $id]);

    $conn->commit();

    echo json_encode(['success' => true]);

} catch (PDOException $e) {

    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}