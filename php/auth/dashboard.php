<?php
/**
 * Single dashboard for all roles (AMORA-style).
 * Uses layout: navbar, role-based sidebar, footer.
 * Consumer: aggregated stats (total orders, pending, latest order, favorites).
 */
require_once __DIR__ . '/../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout_action'])) {
    session_unset();
    session_destroy();
    header('Location: ' . getBaseUrl() . '/php/auth/login.php');
    exit;
}

$basePath = getBasePath(__FILE__);
$baseUrl = getBaseUrl();
$currentPage = 'dashboard';
$pageTitle = 'Dashboard';

// Consumer dashboard stats (aggregation)
$consumerStats = null;
if ($userRole === 'consumer' && isset($user['id'])) {
    try {
        require_once __DIR__ . '/../database/db_connect.php';
        $uid = $user['id'];
        $consumerStats = [
            'total_orders' => 0,
            'pending_orders' => 0,
            'total_spent' => 0,
            'favorites_count' => 0,
            'latest_order' => null,
        ];
        $stmt = $conn->prepare("SELECT COUNT(*) AS n FROM orders WHERE user_id = ?");
    $stmt->bind_param('s', $uid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $consumerStats['total_orders'] = (int) ($row['n'] ?? 0);
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) AS n FROM orders WHERE user_id = ? AND status NOT IN ('delivered','cancelled')");
    $stmt->bind_param('s', $uid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $consumerStats['pending_orders'] = (int) ($row['n'] ?? 0);
    $stmt->close();

    $stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) AS total FROM orders WHERE user_id = ? AND status = 'delivered'");
    $stmt->bind_param('s', $uid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $consumerStats['total_spent'] = (float) ($row['total'] ?? 0);
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) AS n FROM user_favorites WHERE user_id = ?");
    $stmt->bind_param('s', $uid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $consumerStats['favorites_count'] = (int) ($row['n'] ?? 0);
    $stmt->close();

    $stmt = $conn->prepare("
        SELECT o.id, o.status, o.total_amount, o.created_at, r.name AS restaurant_name
        FROM orders o
        JOIN restaurants r ON r.id = o.restaurant_id
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC
        LIMIT 1
    ");
    $stmt->bind_param('s', $uid);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $consumerStats['latest_order'] = $res->fetch_assoc();
        $consumerStats['latest_order']['total_amount'] = (float) ($consumerStats['latest_order']['total_amount'] ?? 0);
    }
    $stmt->close();
    $conn->close();
    } catch (Exception $e) {
        $consumerStats = null;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - FoodGrab</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>css/serve_asset.php?file=design-system.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>css/serve_asset.php?file=dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php
    $showSidebarToggle = true;
    include __DIR__ . '/../includes/layout/navbar.php';
    ?>
    <div class="dashboard-container">
        <div class="sidebar-overlay" id="sidebarOverlay" aria-hidden="true"></div>
        <?php include __DIR__ . '/../includes/layout/sidebar.php'; ?>
        <main class="dashboard-main">
            <h1 class="page-title">Welcome, <?php echo htmlspecialchars($user['firstName']); ?>!</h1>
            <p class="page-subtitle"><?php
                if ($userRole === 'consumer') echo 'Order food, track orders, and manage your account.';
                elseif ($userRole === 'admin') echo 'Manage orders, restaurants, and menu items.';
                else echo 'Manage users, roles, and system settings.';
            ?></p>

            <?php if ($userRole === 'consumer'): ?>
                <?php if ($consumerStats !== null): ?>
                <section class="dashboard-stats" aria-label="Order and account summary">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon" aria-hidden="true"><i class="fa-solid fa-receipt"></i></div>
                            <div class="stat-content">
                                <span class="stat-value"><?php echo (int) $consumerStats['total_orders']; ?></span>
                                <span class="stat-label">Total orders</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon stat-icon-pending" aria-hidden="true"><i class="fa-solid fa-clock"></i></div>
                            <div class="stat-content">
                                <span class="stat-value"><?php echo (int) $consumerStats['pending_orders']; ?></span>
                                <span class="stat-label">Active orders</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon stat-icon-spent" aria-hidden="true"><i class="fa-solid fa-peso-sign"></i></div>
                            <div class="stat-content">
                                <span class="stat-value"><?php echo number_format($consumerStats['total_spent'], 0); ?></span>
                                <span class="stat-label">Total spent (delivered)</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon stat-icon-fav" aria-hidden="true"><i class="fa-solid fa-heart"></i></div>
                            <div class="stat-content">
                                <span class="stat-value"><?php echo (int) $consumerStats['favorites_count']; ?></span>
                                <span class="stat-label">Favorites</span>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($consumerStats['latest_order'])): $lo = $consumerStats['latest_order']; ?>
                    <div class="latest-order-section">
                        <h2 class="section-title">Latest order</h2>
                        <a href="<?php echo $baseUrl; ?>/php/forms/track_order.php?order_id=<?php echo (int) $lo['id']; ?>" class="latest-order-card card-link">
                            <div class="latest-order-main">
                                <span class="latest-order-restaurant"><?php echo htmlspecialchars($lo['restaurant_name'] ?? 'Restaurant'); ?></span>
                                <span class="latest-order-date"><?php echo date('M j, Y g:i A', strtotime($lo['created_at'])); ?></span>
                            </div>
                            <div class="latest-order-meta">
                                <span class="order-status-badge order-status-<?php echo htmlspecialchars($lo['status']); ?>"><?php echo htmlspecialchars($lo['status']); ?></span>
                                <span class="latest-order-amount">₱<?php echo number_format($lo['total_amount'], 2); ?></span>
                            </div>
                            <span class="latest-order-link-text">Track order <i class="fa-solid fa-arrow-right"></i></span>
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="latest-order-section">
                        <h2 class="section-title">Latest order</h2>
                        <div class="empty-state">
                            <i class="fa-solid fa-receipt"></i>
                            <p>No orders yet.</p>
                            <a href="<?php echo $baseUrl; ?>/php/forms/order_food.php" class="btn-primary">Order food</a>
                        </div>
                    </div>
                    <?php endif; ?>
                </section>
                <?php endif; ?>
                <h2 class="section-title quick-links-title">Quick links</h2>
                <div class="card-container">
                    <a href="<?php echo $baseUrl; ?>/php/forms/order_food.php" class="card card-link">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" aria-hidden="true"><path d="M575.8 255.5c0 18-15 32.1-32 32.1h-32l.7 160.2c0 2.7-.2 5.4-.5 8.1V472c0 22.1-17.9 40-40 40H456c-1.1 0-2.2 0-3.3-.1c-1.4 .1-2.8 .1-4.2 .1H416 392c-22.1 0-40-17.9-40-40V448 384c0-17.7-14.3-32-32-32H256c-17.7 0-32 14.3-32 32v64 24c0 22.1-17.9 40-40 40H160 128.1c-1.5 0-3-.1-4.5-.2c-1.2 .1-2.4 .2-3.6 .2H104c-22.1 0-40-17.9-40-40V360c0-.9 0-1.9 .1-2.8V287.6H32c-18 0-32-14-32-32.1c0-9 3-17 10-24L26.6 53.3c7-15 21-25.3 39.4-25.3H510c18.4 0 32.4 10.3 39.4 25.3l16.4 178.3c7 7 10 15 10 24zM176 256c-17.7 0-32 14.3-32 32s14.3 32 32 32H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H176zM32 480h32c17.7 0 32-14.3 32-32s-14.3-32-32-32H32c-17.7 0-32 14.3-32 32s14.3 32 32 32z"/></svg>
                        <h3>Order Food</h3>
                        <p>Browse menus and place orders.</p>
                    </a>
                    <a href="<?php echo $baseUrl; ?>/php/forms/order_history.php" class="card card-link">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" aria-hidden="true"><path d="M64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64H64zM96 64H288c17.7 0 32 14.3 32 32v32c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V96c0-17.7 14.3-32 32-32z"/></svg>
                        <h3>Order History</h3>
                        <p>View and track your orders.</p>
                    </a>
                    <a href="<?php echo $baseUrl; ?>/php/forms/favorites.php" class="card card-link">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" aria-hidden="true"><path d="M47.6 300.4L228.3 469.1c7.5 7 17.4 10.9 27.7 10.9s20.2-3.9 27.7-10.9L464.4 300.4c30.4-28.3 47.6-68 47.6-109.5v-5.8c0-69.9-50.5-129.5-119.4-141C347 36.5 300.6 51.4 268 84L256 96 244 84c-32.6-32.6-79-47.5-124.6-39.9C50.5 55.6 0 115.2 0 185.1v5.8c0 41.5 17.2 81.2 47.6 109.5z"/></svg>
                        <h3>Favorites</h3>
                        <p>Your saved items.</p>
                    </a>
                    <a href="<?php echo $baseUrl; ?>/php/forms/payment_methods.php" class="card card-link">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" aria-hidden="true"><path d="M64 32C28.7 32 0 60.7 0 96v320c0 35.3 28.7 64 64 64h448c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64z"/></svg>
                        <h3>Payment Methods</h3>
                        <p>Manage payment options.</p>
                    </a>
                </div>
            <?php elseif ($userRole === 'admin'): ?>
                <div class="card-container">
                    <a href="<?php echo $baseUrl; ?>/php/admin/orders.php" class="card card-link">
                        <h3>Manage Orders</h3>
                        <p>View and update order status.</p>
                    </a>
                    <a href="<?php echo $baseUrl; ?>/php/admin/restaurants.php" class="card card-link">
                        <h3>Restaurants</h3>
                        <p>Add or edit restaurants.</p>
                    </a>
                    <a href="<?php echo $baseUrl; ?>/php/admin/menu.php" class="card card-link">
                        <h3>Menu Items</h3>
                        <p>Manage menu items per restaurant.</p>
                    </a>
                    <a href="<?php echo $baseUrl; ?>/php/pages/users/admin/users/index.php" class="card card-link">
                        <h3>Manage Users</h3>
                        <p>View users and submit deletion requests.</p>
                    </a>
                    <a href="<?php echo $baseUrl; ?>/php/pages/users/admin/approvals.php" class="card card-link">
                        <h3>My Requests</h3>
                        <p>Track your approval requests.</p>
                    </a>
                </div>
            <?php else: ?>
                <div class="card-container">
                    <a href="<?php echo $baseUrl; ?>/php/superadmin/index.php" class="card card-link">
                        <h3>Users & Roles</h3>
                        <p>Manage all users and assign roles.</p>
                    </a>
                    <a href="<?php echo $baseUrl; ?>/php/pages/users/superadmin/approvals/index.php" class="card card-link">
                        <h3>Approval Requests</h3>
                        <p>Review and approve or reject deletion requests.</p>
                    </a>
                    <a href="<?php echo $baseUrl; ?>/php/admin/index.php" class="card card-link">
                        <h3>Admin Panel</h3>
                        <p>Access admin features.</p>
                    </a>
                </div>
            <?php endif; ?>
        </main>
        <?php include __DIR__ . '/../includes/layout/footer.php'; ?>
    </div>
    <script>
(function() {
    var toggle = document.getElementById('sidebarToggle');
    var overlay = document.getElementById('sidebarOverlay');
    if (toggle && overlay) {
        toggle.addEventListener('click', function() {
            document.body.classList.toggle('sidebar-open');
            overlay.classList.toggle('is-open', document.body.classList.contains('sidebar-open'));
            overlay.setAttribute('aria-hidden', document.body.classList.contains('sidebar-open') ? 'false' : 'true');
        });
        overlay.addEventListener('click', function() {
            document.body.classList.remove('sidebar-open');
            overlay.classList.remove('is-open');
            overlay.setAttribute('aria-hidden', 'true');
        });
    }
})();
    </script>
</body>
</html>
