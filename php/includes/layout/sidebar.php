<?php
/**
 * Role-based sidebar (Standardized).
 * Expects $basePath, $currentPage, $user / $userRole.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($user)) {
    $user = $_SESSION['user'] ?? null;
}
$userRole = $user['role'] ?? 'consumer';

// Ensure we have a base URL
if (!function_exists('getBaseUrl')) {
    function getBaseUrl()
    {
        return 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/DAMALERIO';
    }
}
$baseUrl = getBaseUrl();

// Helper to check active state
function isActive($page, $current)
{
    return $current === $page ? 'active' : '';
}
// Default current page if not set
$currentPage = $currentPage ?? '';
?>
<aside class="dashboard-sidebar">
    <div class="profile-section">
        <p><strong><?php echo htmlspecialchars($user['firstName'] ?? 'Guest'); ?></strong></p>
        <p class="muted small"><?php echo ucfirst($userRole); ?></p>
    </div>
    <nav class="sidebar-menu">
        <?php if ($userRole === 'superadmin'): ?>
            <a href="<?php echo $baseUrl; ?>/php/superadmin/index.php" class="<?php echo isActive('superadmin_dashboard', $currentPage); ?>"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="<?php echo $baseUrl; ?>/php/superadmin/users.php" class="<?php echo isActive('superadmin_users', $currentPage); ?>"><i class="fa-solid fa-users"></i> Users & Roles</a>
            <a href="<?php echo $baseUrl; ?>/php/superadmin/requests.php" class="<?php echo isActive('superadmin_requests', $currentPage); ?>"><i class="fa-solid fa-user-shield"></i> Block Requests</a>
            <a href="<?php echo $baseUrl; ?>/php/superadmin/logs.php" class="<?php echo isActive('superadmin_logs', $currentPage); ?>"><i class="fa-solid fa-list"></i> Login Logs</a>
            <a href="<?php echo $baseUrl; ?>/php/superadmin/logs.php" class="<?php echo isActive('superadmin_logs', $currentPage); ?>"><i class="fa-solid fa-list"></i> Login Logs</a>

            <div style="border-top: 1px solid rgba(0,0,0,0.05); margin: 0.5rem 0; padding-top: 0.5rem;">
                <p class="muted small" style="padding: 0 1rem; margin-bottom: 0.5rem;">Store Management</p>
                <a href="<?php echo $baseUrl; ?>/php/admin/restaurants.php" class="<?php echo isActive('admin_restaurants', $currentPage); ?>"><i class="fa-solid fa-store"></i> Stores</a>
                <a href="<?php echo $baseUrl; ?>/php/admin/menu.php" class="<?php echo isActive('admin_menu', $currentPage); ?>"><i class="fa-solid fa-utensils"></i> Menu Items</a>
                <a href="<?php echo $baseUrl; ?>/php/admin/orders.php" class="<?php echo isActive('admin_orders', $currentPage); ?>"><i class="fa-solid fa-receipt"></i> Order Management</a>
            </div>

        <?php
elseif ($userRole === 'admin'): ?>
            <a href="<?php echo $baseUrl; ?>/php/admin/index.php" class="<?php echo isActive('admin_dashboard', $currentPage); ?>"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="<?php echo $baseUrl; ?>/php/admin/consumers.php" class="<?php echo isActive('admin_consumers', $currentPage); ?>"><i class="fa-solid fa-users"></i> Consumers</a>
            <a href="<?php echo $baseUrl; ?>/php/admin/admin_requests.php" class="<?php echo isActive('admin_requests', $currentPage); ?>"><i class="fa-solid fa-file-contract"></i> My Requests</a>
            <a href="<?php echo $baseUrl; ?>/php/admin/orders.php" class="<?php echo isActive('admin_orders', $currentPage); ?>"><i class="fa-solid fa-receipt"></i> Orders</a>
            <a href="<?php echo $baseUrl; ?>/php/admin/restaurants.php" class="<?php echo isActive('admin_restaurants', $currentPage); ?>"><i class="fa-solid fa-store"></i> Restaurants</a>
            <a href="<?php echo $baseUrl; ?>/php/admin/menu.php" class="<?php echo isActive('admin_menu', $currentPage); ?>"><i class="fa-solid fa-utensils"></i> Menu Items</a>

        <?php
else: ?>
            <!-- Consumer -->
            <a href="<?php echo $baseUrl; ?>/php/auth/dashboard.php" class="<?php echo isActive('consumer_dashboard', $currentPage); ?>"><i class="fa-solid fa-utensils"></i> Browse Menu</a>
            <a href="<?php echo $baseUrl; ?>/php/forms/cart.php" class="<?php echo isActive('cart', $currentPage); ?>"><i class="fa-solid fa-cart-shopping"></i> My Cart</a>
            <!-- <a href="<?php echo $baseUrl; ?>/php/forms/orders.php"><i class="fa-solid fa-receipt"></i> My Orders</a> -->
            <a href="<?php echo $baseUrl; ?>/php/forms/profile.php" class="<?php echo isActive('profile', $currentPage); ?>"><i class="fa-solid fa-user"></i> My Profile</a>
        <?php
endif; ?>

        <a href="<?php echo $baseUrl; ?>/php/auth/logout.php" class="danger-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>
</aside>
