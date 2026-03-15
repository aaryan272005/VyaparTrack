<?php

session_start();
include('connection.php');

if (isset($_POST['id'])) {

    $query = "DELETE FROM productsupplier WHERE id = :id";

    $stmt = $conn->prepare($query);

    $stmt->execute([
        'id' => $_POST['id']
    ]);

    echo "success";
}
