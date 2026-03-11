<?php

date_default_timezone_set('Asia/Kolkata');

session_start();

if(!isset($_SESSION['user'])){
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in'
    ]);
    exit();
}

include('connection.php');

try{

    $order_id = $_POST['order_id'];
    $quantity_delivered = $_POST['quantity_delivered'];
    $status = $_POST['status'];

    /* GET CURRENT ORDER DATA */

    $stmt = $conn->prepare("SELECT quantity_order, quantity_received FROM productsupplier WHERE id = ?
    ");

    $stmt->execute([$order_id]);

    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$order){
        echo json_encode([
            'success'=>false,
            'message'=>'Order not found'
        ]);
        exit();
    }

    $current_received = $order['quantity_received'];
    $quantity_order = $order['quantity_order'];

    /* CALCULATE NEW VALUES */

    $new_received = $current_received + $quantity_delivered;
    $remaining = $quantity_order - $new_received;

    if($remaining < 0){
        $remaining = 0;
    }

    /* UPDATE ORDER */

    $updateQuery = "UPDATE productsupplier SET  quantity_received = ?, quantity_remaining = ?, stats = ? WHERE id = ? ";

    $stmt = $conn->prepare($updateQuery);

    $stmt->execute([
        $new_received,
        $remaining,
        $status,
        $order_id
    ]);

    /* INSERT DELIVERY HISTORY */

    if($quantity_delivered > 0){

        $historyQuery = "INSERT INTO delivery_history (order_id, quantity_received, date_received) VALUES (?, ?, NOW()) ";

        $stmt = $conn->prepare($historyQuery);

        $stmt->execute([
            $order_id,
            $quantity_delivered
        ]);
    }

    echo json_encode([
        'success'=>true,
        'message'=>'Order updated successfully'
    ]);

}catch(PDOException $e){

    echo json_encode([
        'success'=>false,
        'message'=>$e->getMessage()
    ]);

}