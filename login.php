<?php
// Start the session.
session_start();
if(isset($_SESSION['user'])) header('Location: dashboard.php');

$error_message = '';

if($_POST){
    include('database/connection.php');

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = 'SELECT * FROM users WHERE users.email="' . $username . '" AND users.password="' . $password . '"';
    $stmt = $conn->prepare($query);
    $stmt->execute();

    if($stmt->rowCount() > 0){
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $user = $stmt->fetchAll()[0];

        // Captures data of currently login users.
        
        $_SESSION['user'] = $user;
        $_SESSION['logged_in'] = true;
        header('Location: dashboard.php');
    } else {
        $error_message = 'Please make sure that username and password are correct.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VyaparTrack Login - Inventory Management System</title>
    <link rel="stylesheet" href="css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Noto+Sans+Devanagari:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
function togglePassword() {
  let password = document.getElementById("password");
  let toggleIcon = document.querySelector(".toggle");

  if (password.type === "password") {
    password.type = "text";
    toggleIcon.classList.remove("fa-eye");
    toggleIcon.classList.add("fa-eye-slash");
  } else {
    password.type = "password";
    toggleIcon.classList.remove("fa-eye-slash");
    toggleIcon.classList.add("fa-eye");
  }
}
setTimeout(() => {
    let error = document.getElementById("error-message");
    if(error){
        error.classList.add("fade-out");
        setTimeout(() => {
            error.remove();
        }, 600);
    }
}, 3000);
</script>
</head>
<body id="login_body">
    <?php if(!empty($error_message)) { ?>
    <div id="error-message">
        <i class="fas fa-exclamation-circle"></i> <?= $error_message ?>
    </div>
    <?php } ?>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="logo-wrapper">
                    <span class="hindi">व्यापार</span><span class="english">Track</span>
                </div>
                <p class="login-subtitle">Inventory Management System</p>
            </div>
            <form action="login.php" method="POST" class="login-form">
                <div class="input-group">
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <input type="text" id="username" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <input type="password" id="password" placeholder="Password" name="password" required>
                    <span class="toggle-password" onclick="togglePassword()">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <button type="submit" class="login-btn">
                    <span>Login</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
            <div class="login-footer">
                <p>&copy; 2024 VyaparTrack. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
