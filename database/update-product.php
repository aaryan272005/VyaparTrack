<?php

include('connection.php');

$id = $_POST['id'];
$product_name = $_POST['product_name'];
$description = $_POST['description'];

$image_name = null;

if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){

    $image_name = time().'_'.$_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/'.$image_name);

    $query = "UPDATE products 
              SET product_name=:product_name,
                  description=:description,
                  img=:img,
                  updated_at=NOW()
              WHERE id=:id";

    $stmt = $conn->prepare($query);

    $stmt->execute([
        ':product_name'=>$product_name,
        ':description'=>$description,
        ':img'=>$image_name,
        ':id'=>$id
    ]);

}else{

    $query = "UPDATE products 
              SET product_name=:product_name,
                  description=:description,
                  updated_at=NOW()
              WHERE id=:id";

    $stmt = $conn->prepare($query);

    $stmt->execute([
        ':product_name'=>$product_name,
        ':description'=>$description,
        ':id'=>$id
    ]);

}

echo json_encode([
    'success'=>true,
    'product_name'=>$product_name,
    'description'=>$description,
    'img'=>$image_name
]);