<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');

// Fetch Stats (Global stats as requested)
require_once __DIR__ . '/../database/db_connect.php';

$stats = [
    'restaurants' => 0,
    'menu_items' => 0,
    'orders_total' => 0,
    'orders_pending' => 0,
    'items_sold' => 0
];

// Restaurants
$res = $conn->query("SELECT COUNT(*) as n FROM restaurants");
if ($row = $res->fetch_assoc())
    $stats['restaurants'] = $row['n'];

// Menu Items
$res = $conn->query("SELECT COUNT(*) as n FROM menu_items");
if ($row = $res->fetch_assoc())
    $stats['menu_items'] = $row['n'];

// Orders
$res = $conn->query("SELECT COUNT(*) as n FROM orders");
if ($row = $res->fetch_assoc())
    $stats['orders_total'] = $row['n'];

// Pending Orders
$res = $conn->query("SELECT COUNT(*) as n FROM orders WHERE status = 'pending'");
if ($row = $res->fetch_assoc())
    $stats['orders_pending'] = $row['n'];

// Items Sold
$res = $conn->query("SELECT SUM(quantity) as n FROM order_items");
if ($row = $res->fetch_assoc())
    $stats['items_sold'] = $row['n'] ?? 0;

$pageTitle = 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - FoodGrab</title>
    <link rel="stylesheet" href="../../css/design-system.css">
    <link rel="stylesheet" href="../../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 1rem; }
        .stat-icon { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: white; }
        .bg-blue { background: #3b82f6; }
        .bg-green { background: #10b981; }
        .bg-orange { background: #f59e0b; }
        .bg-purple { background: #8b5cf6; }
        .stat-info h3 { margin: 0; font-size: 1.5rem; font-weight: 700; color: var(--text-heading); }
        .stat-info p { margin: 0; color: var(--text-muted); font-size: 0.9rem; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/layout/navbar.php'; ?>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php $currentPage = 'admin_dashboard';
include __DIR__ . '/../includes/layout/sidebar.php'; ?>

        <main class="dashboard-main">
            <h1 class="page-title">Admin Dashboard</h1>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon bg-blue"><i class="fa-solid fa-store"></i></div>
                    <div class="stat-info">
                        <h3><?php echo $stats['restaurants']; ?></h3>
                        <p>Restaurants</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-green"><i class="fa-solid fa-utensils"></i></div>
                    <div class="stat-info">
                        <h3><?php echo $stats['menu_items']; ?></h3>
                        <p>Menu Items</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-orange"><i class="fa-solid fa-receipt"></i></div>
                    <div class="stat-info">
                        <h3><?php echo $stats['orders_total']; ?></h3>
                        <p>Total Orders</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-purple"><i class="fa-solid fa-box-open"></i></div>
                    <div class="stat-info">
                        <h3><?php echo $stats['items_sold']; ?></h3>
                        <p>Items Sold</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <h2 class="section-title">Quick Actions</h2>
            <div class="card-container">
                <a href="orders.php" class="card card-link">
                    <h3>Manage Orders</h3>
                    <p>View and update order status.</p>
                </a>
                <a href="consumers.php" class="card card-link">
                    <h3>Manage Consumers</h3>
                    <p>Add/Edit consumers and request blocks.</p>
                </a>
                <a href="menu.php" class="card card-link">
                    <h3>Menu Management</h3>
                    <p>Update menu items and prices.</p>
                </a>
                <a href="requests.php" class="card card-link">
                    <h3>Approval Requests</h3>
                    <p>Track your submitted block requests.</p>
                </a>
            </div>
        </main>
        <?php include __DIR__ . '/../includes/layout/footer.php'; ?>
    </div>
</body>
</html>
