<?php

include('connection.php');

$supplier_id = $_POST['supplier_id'];

try{

/* Delete supplier relations first */

$stmt = $conn->prepare("DELETE FROM productsupplier WHERE supplier = :id");
$stmt->execute([':id'=>$supplier_id]);

/* Delete supplier */

$stmt = $conn->prepare("DELETE FROM supplier WHERE id = :id");
$stmt->execute([':id'=>$supplier_id]);

echo json_encode(['success'=>true]);

}catch(PDOException $e){

echo json_encode([
'success'=>false,
'message'=>$e->getMessage()
]);

}