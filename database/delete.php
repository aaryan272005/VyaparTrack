<?php

include('connection.php');

$id = $_POST['id'];
$table = $_POST['table'];

$query = "DELETE FROM $table WHERE id = :id";

$stmt = $conn->prepare($query);

if($stmt->execute(['id'=>$id])){
    echo json_encode(['success'=>true]);
}else{
    echo json_encode(['success'=>false]);
}