<?php
// Check if session has started
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start session if not started
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

include 'db_connection.php'; // Include database connection

// Fetch user data from database
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username, fullname FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@100..900&display=swap" rel="stylesheet">
</head>
<body>
    <aside class="main-sidebar sidebar-light-navy elevation-4">
        <!-- Brand Logo -->
        <a href="index.php" class="brand-link bg-navy">
            <img src="assets/dist/img/SUT_logo_orange.png" alt="SUT Logo" class="brand-image img-square elevation-3">
            <span class="brand-text font-weight-light">URL Shortener SUT</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="assets/dist/img/SUT_LOGO.png" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <?php if ($user): ?>
                        <a href="#" class="d-block"><?= htmlspecialchars($user['username']) ?></a>
                        <small class="d-block"><?= htmlspecialchars($user['fullname']) ?></small>
                    <?php else: ?>
                        <a href="#" class="d-block">Guest User</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar nav-child-indent flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-header">Menu</li>
                    <li class="nav-item">
                        <a href="index.php" class="nav-link <?= ($menu == "index") ? "active" : "" ?>">
                            <i class="nav-icon fas fa-link"></i>
                            <p>Short-URL</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="manage_urls.php" class="nav-link <?= ($menu == "manage_urls") ? "active" : "" ?>">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>Manage URLs</p>
                        </a>
                    </li>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a href="admin.php" class="nav-link <?= ($menu == "admin") ? "active" : "" ?>">
                            <i class="nav-icon fas fa-key"></i>
                            <p>Admin</p>
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a href="logout.php" class="nav-link text-danger">
                            <i class="nav-icon fas fa-power-off"></i>
                            <p>Logout</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>
</body>
</html>