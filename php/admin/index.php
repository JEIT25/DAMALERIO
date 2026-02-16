<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin', 'superadmin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout_action'])) {
    session_unset();
    session_destroy();
    header('Location: ' . getBaseUrl() . '/php/forms/login.php');
    exit;
}

$base = getBaseUrl();
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - FoodGrab</title>
    <link rel="stylesheet" href="../../css/serve_asset.php?file=design-system.css">
    <link rel="stylesheet" href="../../css/serve_asset.php?file=dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <div class="navbar-left">
                <img src="../../images/logo4.png" alt="Logo" class="logo">
                <span class="navbar-text">FoodGrab <span class="navbar-subtext">Admin</span></span>
            </div>
            <div class="navbar-right">
                <form action="" method="POST">
                    <input type="hidden" name="logout_action" value="1">
                    <button type="submit" class="nav-link">Log Out</button>
                </form>
            </div>
        </header>
        <aside class="dashboard-sidebar">
            <div class="profile-section">
                <p><strong><?php echo htmlspecialchars($user['firstName'] . ' ' . $user['lastName']); ?></strong></p>
                <p class="muted small"><?php echo htmlspecialchars($user['role']); ?></p>
            </div>
            <nav class="sidebar-menu">
                <a href="index.php" class="active">Dashboard</a>
                <a href="orders.php">Manage Orders</a>
                <a href="restaurants.php">Restaurants</a>
                <a href="menu.php">Menu Items</a>
                <?php if (($user['role'] ?? '') === 'superadmin') { ?>
                <a href="../superadmin/index.php">Superadmin Panel</a>
                <?php } ?>
            </nav>
        </aside>
        <main class="dashboard-main">
            <h1>Admin Dashboard</h1>
            <div class="card-container">
                <a href="orders.php" class="card card-link">
                    <h3>Manage Orders</h3>
                    <p>View and update order status (pending, preparing, delivered).</p>
                </a>
                <a href="restaurants.php" class="card card-link">
                    <h3>Restaurants</h3>
                    <p>Add or edit restaurants.</p>
                </a>
                <a href="menu.php" class="card card-link">
                    <h3>Menu Items</h3>
                    <p>Manage menu items per restaurant.</p>
                </a>
            </div>
        </main>
        <footer class="dashboard-footer"><div class="footer-bottom"><p>All rights reserved &copy; 2025</p></div></footer>
    </div>
</body>
</html>
