<?php

include('connection.php');

/* PURCHASE ORDER STATUS */

$statusQuery = "SELECT stats, COUNT(*) as total FROM productsupplier GROUP BY stats ";

$stmt = $conn->prepare($statusQuery);
$stmt->execute();

$statusData = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* PRODUCTS PER SUPPLIER */

$supplierQuery = "SELECT s.supplier_name, COUNT(ps.product) as total FROM productsupplier ps JOIN supplier s ON ps.supplier = s.id GROUP BY ps.supplier ";

$stmt = $conn->prepare($supplierQuery);
$stmt->execute();

$supplierData = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* DELIVERY HISTORY PER DAY */

$deliveryQuery = "SELECT DATE(date_received) as day, SUM(quantity_received) as total FROM delivery_history GROUP BY DATE(date_received) ORDER BY day ASC ";

$stmt = $conn->prepare($deliveryQuery);
$stmt->execute();

$deliveryData = $stmt->fetchAll(PDO::FETCH_ASSOC);


echo json_encode([
    "status" => $statusData,
    "supplier" => $supplierData,
    "delivery" => $deliveryData
]);