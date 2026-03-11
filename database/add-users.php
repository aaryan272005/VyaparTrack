<?php

session_start();

include('connection.php');

$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];
$password = $_POST['password'];

// encrypt password
$password = password_hash($password, PASSWORD_DEFAULT);

try{

$stmt = $conn->prepare("
INSERT INTO users
(first_name, last_name, email, password, created_at, updated_at)
VALUES (?, ?, ?, ?, NOW(), NOW())
");

$stmt->execute([
$first_name,
$last_name,
$email,
$password
]);

$_SESSION['response'] = [
'success' => true,
'message' => 'User successfully added.'
];

}catch(PDOException $e){

$_SESSION['response'] = [
'success' => false,
'message' => $e->getMessage()
];

}

header('location: ../users-add.php');
exit();

?>