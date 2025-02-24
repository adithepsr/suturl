<?php
session_start();

// Include the database connection
include 'db_connection.php';

// Initialize error message
$error_message = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user input
    $usernameInput = trim($_POST['username']);
    $passwordInput = trim($_POST['password']);

    // Check if username or password is empty
    if (empty($usernameInput) || empty($passwordInput)) {
        $error_message = 'Please enter your username and password';
    } else {
        try {
            // Query to check user credentials
            $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = :username LIMIT 1");
            $stmt->bindParam(':username', $usernameInput);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify password and check if user exists
            if ($user && password_verify($passwordInput, $user['password'])) {
                // Set session variables
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role']; // Save role in session

                // Save login IP
                $user_ip = $_SERVER['REMOTE_ADDR'];
                if ($user_ip == '::1') {
                    $user_ip = '127.0.0.1'; // Convert IPv6 localhost to IPv4
                }
                $update_stmt = $pdo->prepare("UPDATE users SET last_login_ip = :last_login_ip WHERE id = :id");
                $update_stmt->bindParam(':last_login_ip', $user_ip);
                $update_stmt->bindParam(':id', $user['id']);
                $update_stmt->execute();

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header('Location: index.php'); // Redirect to admin.php for admin users
                } else {
                    header('Location: index.php'); // Redirect to index.php for regular users
                }
                exit();
            } else {
                $error_message = "Invalid username or password!";
            }
        } catch (Exception $e) {
            $error_message = "An error occurred: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Shortener System | Suranaree University of Technology</title>
    <link rel="icon" href="assets/dist/img/SUT_LOGO.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="css/login.css">
    <style>
        body {
            font-family: 'Noto Sans', sans-serif;                                  
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: url('assets/dist/img/sut.jpg'); /* Background image */
            background-size: 1920px 1080px;
            background-position: center;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="login-container">
            <div class="logo">
                <img src="assets/dist/img/SUT_LOGO.png" alt="Logo"> 
            </div>
            <h1>URL Shortener System | SUT</h1> 
            <p class="subtitle">Sign in to use the system</p>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Sign In</button>
            </form>
        </div>
    </div>
</body>
</html>