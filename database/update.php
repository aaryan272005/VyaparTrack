<?php

date_default_timezone_set('Asia/Kolkata');

session_start();
include('connection.php');

header('Content-Type: application/json');

// ✅ AUTH CHECK (FIXED)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// ✅ ADMIN ONLY (SAFE)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Access Denied']);
    exit();
}

// ✅ TABLE PARAM (IMPORTANT)
$table = $_POST['table'] ?? '';

try {

    /* ================= PRODUCT UPDATE ================= */
    if ($table === 'products') {

        $id = intval($_POST['id'] ?? 0);
        $product_name = trim($_POST['product_name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (!$id || empty($product_name)) {
            throw new Exception("Invalid product data");
        }

        $image_name = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

            $img = $_FILES['image'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];

            if (!in_array($img['type'], $allowed_types)) {
                throw new Exception("Invalid image type");
            }

            $image_name = time() . '_' . basename($img['name']);
            $upload_dir = "../uploads/products/";

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            move_uploaded_file($img['tmp_name'], $upload_dir . $image_name);

            $query = "UPDATE products 
                      SET product_name=:name,
                          description=:desc,
                          img=:img,
                          updated_at=NOW()
                      WHERE id=:id";

            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':name' => $product_name,
                ':desc' => $description,
                ':img' => $image_name,
                ':id' => $id
            ]);

        } else {

            $query = "UPDATE products 
                      SET product_name=:name,
                          description=:desc,
                          updated_at=NOW()
                      WHERE id=:id";

            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':name' => $product_name,
                ':desc' => $description,
                ':id' => $id
            ]);
        }

        echo json_encode([
            'success' => true,
            'product_name' => $product_name,
            'description' => $description,
            'img' => $image_name
        ]);
    }


    /* ================= SUPPLIER UPDATE ================= */
    if ($table === 'supplier') {

        $id = intval($_POST['supplier_id'] ?? 0);
        $name = trim($_POST['supplier_name'] ?? '');
        $location = trim($_POST['supplier_location'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (!$id || empty($name)) {
            throw new Exception("Invalid supplier data");
        }

        $stmt = $conn->prepare("
            UPDATE supplier
            SET supplier_name = :name,
                supplier_location = :location,
                email = :email,
                updated_at = NOW()
            WHERE id = :id
        ");

        $stmt->execute([
            ':name' => $name,
            ':location' => $location,
            ':email' => $email,
            ':id' => $id
        ]);

        echo json_encode([
            'success' => true,
            'supplier_name' => $name,
            'supplier_location' => $location,
            'email' => $email
        ]);
    }


    /* ================= USER UPDATE ================= */
    if ($table === 'users') {

        $id = intval($_POST['user_id'] ?? 0);
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (!$id || empty($first_name) || empty($email)) {
            throw new Exception("Invalid user data");
        }

        $stmt = $conn->prepare("
            UPDATE users 
            SET first_name = ?, last_name = ?, email = ?, updated_at = NOW()
            WHERE id = ?
        ");

        $stmt->execute([$first_name, $last_name, $email, $id]);

        echo json_encode([
            'success' => true,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email
        ]);
    }


    /* ================= ORDER UPDATE ================= */
    if ($table === 'productsupplier') {

        $order_id = intval($_POST['order_id'] ?? 0);
        $quantity_delivered = intval($_POST['quantity_delivered'] ?? 0);
        $status = $_POST['status'] ?? '';

        if (!$order_id) {
            throw new Exception("Invalid order");
        }

        $conn->beginTransaction();

        // Get current order
        $stmt = $conn->prepare("SELECT quantity_order, quantity_received FROM productsupplier WHERE id = ?");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) throw new Exception("Order not found");

        $new_received = $order['quantity_received'] + $quantity_delivered;
        $remaining = max(0, $order['quantity_order'] - $new_received);

        // Update order
        $stmt = $conn->prepare("
            UPDATE productsupplier 
            SET quantity_received=?, quantity_remaining=?, stats=? 
            WHERE id=?
        ");

        $stmt->execute([$new_received, $remaining, $status, $order_id]);

        // Insert history + update stock
        if ($quantity_delivered > 0) {

            $stmt = $conn->prepare("
                INSERT INTO delivery_history (order_id, quantity_received, date_received) 
                VALUES (?, ?, NOW())
            ");
            $stmt->execute([$order_id, $quantity_delivered]);

            $stmt = $conn->prepare("SELECT product FROM productsupplier WHERE id=?");
            $stmt->execute([$order_id]);
            $product_id = $stmt->fetchColumn();

            $stmt = $conn->prepare("
                INSERT INTO stock (product_id, quantity)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
            ");
            $stmt->execute([$product_id, $quantity_delivered]);
        }

        $conn->commit();

        echo json_encode(['success' => true]);
    }

} catch (Exception $e) {

    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}