<?php

include('connection.php');

$productId = $_POST['product_id'];

$query = "
SELECT s.id, s.supplier_name
FROM productsupplier ps
JOIN supplier s
ON ps.supplier = s.id
WHERE ps.product = :product_id
";

$stmt = $conn->prepare($query);
$stmt->execute(['product_id' => $productId]);

$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($suppliers as $supplier) {

    echo "

<div class='supplierRow'>

<label>" . $supplier['supplier_name'] . "</label>

<input type='number'
name='quantity[" . $supplier['id'] . "]'
placeholder='Enter quantity...'>

</div>

";

}

?>