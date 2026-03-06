<?php
session_start();
include('connection.php');

header('Content-Type: application/json');

if(!isset($_POST['user_id'])){
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$id = intval($_POST['user_id']);

try {

    $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute([':id' => $id]);

    echo json_encode(['success' => true]);

} catch(PDOException $e){
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}