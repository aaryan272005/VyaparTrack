<?php


session_start();

include('connection.php');


$table_name = $_SESSION['table'];

if($table_name == 'products'){

$product_name = $_POST['product_name'];
$description = $_POST['description'];
$suppliers = $_POST['suppliers'];
$user = $_SESSION['user'];
$created_by = $user['id'];

$image_name = '';

if(isset($_FILES['img']) && $_FILES['img']['error'] == 0){

$img = $_FILES['img'];
$image_name = time().'_'.$img['name'];

$upload_dir = "../uploads/products/";

if(!is_dir($upload_dir)){
mkdir($upload_dir,0777,true);
}

move_uploaded_file($img['tmp_name'],$upload_dir.$image_name);

}

try{

$conn->beginTransaction();

$stmt = $conn->prepare("
INSERT INTO products
(product_name,description,img,created_by,created_at,updated_at)
VALUES (?,?,?,?,NOW(),NOW())
");

$stmt->execute([
$product_name,
$description,
$image_name,
$created_by
]);

$product_id = $conn->lastInsertId();

foreach($suppliers as $supplier){

$stmt = $conn->prepare("
INSERT INTO productsupplier
(supplier,product)
VALUES (?,?)
");

$stmt->execute([
$supplier,
$product_id
]);

}

$conn->commit();

$_SESSION['response']=[
'success'=>true,
'message'=>'Product created successfully'
];

}catch(PDOException $e){

$conn->rollBack();

$_SESSION['response']=[
'success'=>false,
'message'=>$e->getMessage()
];

}

}

header('location: ../product-add.php');
exit();