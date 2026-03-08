<?php

include('connection.php');

$supplier_id = $_POST['supplier_id'];
$name = $_POST['supplier_name'];
$location = $_POST['supplier_location'];
$email = $_POST['email'];

try{

$query = "UPDATE supplier
          SET supplier_name = :name,
              supplier_location = :location,
              email = :email,
              updated_at = NOW()
          WHERE id = :id";

$stmt = $conn->prepare($query);

$stmt->execute([
    ':name'=>$name,
    ':location'=>$location,
    ':email'=>$email,
    ':id'=>$supplier_id
]);

echo json_encode(['success'=>true]);

}catch(PDOException $e){

echo json_encode([
    'success'=>false,
    'message'=>$e->getMessage()
]);

}