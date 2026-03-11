<?php

include('connection.php');

$order_id = $_GET['order_id'];

$stmt = $conn->prepare("
SELECT * FROM delivery_history
WHERE order_id = ?
");

$stmt->execute([$order_id]);

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);