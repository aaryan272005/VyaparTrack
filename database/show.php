<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('connection.php');


$table_name = $_SESSION['table'];




$stmt = $conn->prepare("SELECT * FROM $table_name ORDER BY created_at DESC, id DESC");
$stmt->execute();

$stmt->setFetchMode(PDO::FETCH_ASSOC);

return $stmt->fetchAll();

?>