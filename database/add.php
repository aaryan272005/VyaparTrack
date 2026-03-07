<?php

// Start the session
session_start();

// Capture table mappings
include('table_columns.php');

// Capture table name
$table_name = $_SESSION['table'];
$columns = $table_columns_mapping[$table_name];

// Loop through the columns
$db_arr = [];
$user = $_SESSION['user'];

foreach ($columns as $column) {

    if (in_array($column, ['created_at', 'updated_at'])) {
        $value = date('Y-m-d H:i:s');
    } else if ($column == 'created_by') {
        $value = $user['id'];
    } else if ($column == 'password') {
        $value = password_hash($_POST[$column], PASSWORD_DEFAULT);
    }

    // IMAGE UPLOAD
    else if ($column == 'img') {

        $image_name = '';

        if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {

            $target_dir = "../uploads/";

            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }

            $image_name = time() . '_' . $_FILES['img']['name'];

            $target_file = $target_dir . $image_name;

            move_uploaded_file($_FILES['img']['tmp_name'], $target_file);
        }

        $value = $image_name;
    } else {
        $value = isset($_POST[$column]) ? $_POST[$column] : '';
    }

    $db_arr[$column] = $value;
}

// Build SQL query
$table_properties = implode(", ", array_keys($db_arr));
$table_placeholders = ':' . implode(", :", array_keys($db_arr));

// Adding the record
try {

    $sql = "INSERT INTO 
            $table_name ($table_properties)
            VALUES
            ($table_placeholders)";

    include('connection.php');

    $stmt = $conn->prepare($sql);
    $stmt->execute($db_arr);

    $response = [
        'success' => true,
        'message' => 'Successfully Added to the System.'
    ];

} catch (PDOException $e) {

    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

$_SESSION['response'] = $response;
header('Location: ../' . $_SESSION['redirect_to']);
exit;
?> 