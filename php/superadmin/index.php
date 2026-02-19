<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
requireRole('superadmin');

// Fetch Stats
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

// Fetch Latest Pending Requests (Limit 5)
$requests = $conn->query("SELECT acr.*, u.firstName as requester_name
                          FROM admin_creation_requests acr
                          LEFT JOIN users u ON acr.requested_by = u.id
                          WHERE acr.status = 'pending'
                          ORDER BY acr.created_at DESC
                          LIMIT 5");

// Fetch Latest Logs (Limit 5)
$logs = $conn->query("SELECT l.*, u.firstName, u.lastName, u.username, u.role
                      FROM login_logs l
                      JOIN users u ON l.user_id = u.id
                      ORDER BY l.log_time DESC
                      LIMIT 5");

$pageTitle = 'Superadmin Dashboard';
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
        <?php $currentPage = 'superadmin_dashboard';
include __DIR__ . '/../includes/layout/sidebar.php'; ?>

        <main class="dashboard-main">
            <h1 class="page-title">Dashboard Overview</h1>

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

            <!-- Dashboard Widgets -->
            <div class="dashboard-widgets" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem;">

                <!-- Recent Requests -->
                <div class="widget-card" style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h2 class="section-title" style="margin:0; font-size: 1.1rem;">Pending Admin Requests</h2>
                        <a href="requests.php" class="small-link" style="font-size: 0.85rem; color: var(--primary-color);">View All</a>
                    </div>
                    <?php if ($requests->num_rows > 0): ?>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                            <thead>
                                <tr style="border-bottom: 2px solid var(--bg-body); text-align: left;">
                                    <th style="padding: 0.5rem;">User</th>
                                    <th style="padding: 0.5rem;">Role</th>
                                    <th style="padding: 0.5rem;">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($r = $requests->fetch_assoc()): ?>
                                    <tr style="border-bottom: 1px solid var(--border-color);">
                                        <td style="padding: 0.5rem;">
                                            <strong><?php echo htmlspecialchars($r['target_username']); ?></strong><br>
                                            <small class="muted"><?php echo htmlspecialchars($r['target_email']); ?></small>
                                        </td>
                                        <td style="padding: 0.5rem;">
                                            <span style="background: #f59e0b; color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem;"><?php echo ucfirst($r['target_role']); ?></span>
                                        </td>
                                        <td style="padding: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">
                                            <?php echo date('M j', strtotime($r['created_at'])); ?>
                                        </td>
                                    </tr>
                                <?php
    endwhile; ?>
                            </tbody>
                        </table>
                    <?php
else: ?>
                        <p class="muted" style="font-size: 0.9rem; font-style: italic;">No pending requests.</p>
                    <?php
endif; ?>
                </div>

                <!-- Latest Logs -->
                <div class="widget-card" style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h2 class="section-title" style="margin:0; font-size: 1.1rem;">Recent System Logs</h2>
                        <a href="logs.php" class="small-link" style="font-size: 0.85rem; color: var(--primary-color);">View All</a>
                    </div>
                    <?php if ($logs->num_rows > 0): ?>
                        <table class="data-table" style="font-size: 0.9rem;">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>User</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($l = $logs->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <span style="color: <?php echo $l['action'] === 'login' ? '#059669' : '#dc2626'; ?>; font-weight: 500;">
                                                <?php echo ucfirst($l['action']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($l['username']); ?>
                                        </td>
                                        <td style="color: var(--text-muted);">
                                            <?php echo date('M j H:i', strtotime($l['log_time'])); ?>
                                        </td>
                                    </tr>
                                <?php
    endwhile; ?>
                            </tbody>
                        </table>
                    <?php
else: ?>
                        <div class="empty-state" style="padding: 1rem;"><p class="muted" style="margin:0;">No logs found.</p></div>
                    <?php
endif; ?>
                </div>

            </div>
        </main>
        <?php include __DIR__ . '/../includes/layout/footer.php'; ?>
    </div>
</body>
</html>
