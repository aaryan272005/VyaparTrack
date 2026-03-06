<?php
include('connection.php');

if(isset($_POST['user_id'])){

    $id = $_POST['user_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    try{

        $stmt = $conn->prepare("
            UPDATE users 
            SET first_name = ?, last_name = ?, email = ?, updated_at = NOW()
            WHERE id = ?
        ");

        $stmt->execute([$first_name, $last_name, $email, $id]);

        echo json_encode([
            'success' => true,
            'message' => 'User updated successfully.'
        ]);

    } catch(PDOException $e){

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request.'
    ]);
}
?>