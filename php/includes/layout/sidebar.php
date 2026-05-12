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
<?php
$fullName = trim(
    htmlspecialchars($user['firstName'] ?? 'Guest') . ' ' .
    htmlspecialchars($user['middleInitial'] ?? '') . ' ' .
    htmlspecialchars($user['lastName'] ?? '')
);
$initials = '';
if (!empty($user['firstName']))
    $initials .= strtoupper($user['firstName'][0]);
if (!empty($user['lastName']))
    $initials .= strtoupper($user['lastName'][0]);
if (!$initials)
    $initials = 'G';

$roleBadgeColors = [
    'superadmin' => 'background:#ede9fe; color:#7c3aed;',
    'admin' => 'background:#fef3c7; color:#d97706;',
    'consumer' => 'background:#dbeafe; color:#2563eb;',
];
$roleIcon = [
    'superadmin' => 'fa-crown',
    'admin' => 'fa-shield-halved',
    'consumer' => 'fa-user',
];
$badgeStyle = $roleBadgeColors[$userRole] ?? 'background:#f3f4f6; color:#6b7280;';
$icon = $roleIcon[$userRole] ?? 'fa-user';
?>
<aside class="dashboard-sidebar">
    <div class="profile-section" style="display:flex; flex-direction:column; align-items:center; gap:0.5rem; padding:1.25rem 1rem;">
        <div style="width:52px; height:52px; border-radius:50%; background:linear-gradient(135deg, var(--primary-color), #6366f1); display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.15rem; font-weight:700; box-shadow:0 2px 8px rgba(99,102,241,0.25);">
            <?php echo $initials; ?>
        </div>
        <p style="margin:0; font-weight:700; font-size:0.95rem; color:var(--text-heading); text-align:center; line-height:1.3;"><?php echo $fullName; ?></p>
        <span style="display:inline-flex; align-items:center; gap:0.3rem; padding:0.2rem 0.65rem; border-radius:20px; font-size:0.75rem; font-weight:600; <?php echo $badgeStyle; ?>">
            <i class="fa-solid <?php echo $icon; ?>" style="font-size:0.7rem;"></i>
            <?php echo ucfirst($userRole); ?>
        </span>
    </div>
    <nav class="sidebar-menu">
        <?php if ($userRole === 'superadmin'): ?>
            <a href="<?php echo $baseUrl; ?>/php/superadmin/index.php" class="<?php echo isActive('superadmin_dashboard', $currentPage); ?>"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="<?php echo $baseUrl; ?>/php/superadmin/users.php" class="<?php echo isActive('superadmin_users', $currentPage); ?>"><i class="fa-solid fa-users"></i> Users & Roles</a>
            <a href="<?php echo $baseUrl; ?>/php/superadmin/requests.php" class="<?php echo isActive('superadmin_requests', $currentPage); ?>"><i class="fa-solid fa-user-shield"></i> Requests</a>
            <a href="<?php echo $baseUrl; ?>/php/superadmin/logs.php" class="<?php echo isActive('superadmin_logs', $currentPage); ?>"><i class="fa-solid fa-list"></i>Logs</a>


            <div style="border-top: 1px solid rgba(0,0,0,0.05); margin: 0.5rem 0; padding-top: 0.5rem;">
                <p class="muted small" style="padding: 0 1rem; margin-bottom: 0.5rem;">Store Management</p>
                <a href="<?php echo $baseUrl; ?>/php/admin/restaurants.php" class="<?php echo isActive('admin_restaurants', $currentPage); ?>"><i class="fa-solid fa-store"></i> Stores</a>
                <a href="<?php echo $baseUrl; ?>/php/admin/menu.php" class="<?php echo isActive('admin_menu', $currentPage); ?>"><i class="fa-solid fa-utensils"></i> Menu Items</a>
                <a href="<?php echo $baseUrl; ?>/php/admin/orders.php" class="<?php echo isActive('admin_orders', $currentPage); ?>"><i class="fa-solid fa-receipt"></i> Order Management</a>
            </div>
            <a href="<?php echo $baseUrl; ?>/php/forms/profile.php" class="<?php echo isActive('profile', $currentPage); ?>"><i class="fa-solid fa-user"></i> My Profile</a>

        <?php
elseif ($userRole === 'admin'): ?>
            <a href="<?php echo $baseUrl; ?>/php/admin/index.php" class="<?php echo isActive('admin_dashboard', $currentPage); ?>"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="<?php echo $baseUrl; ?>/php/admin/consumers.php" class="<?php echo isActive('admin_consumers', $currentPage); ?>"><i class="fa-solid fa-users"></i> Manage Consumers</a>
            <a href="<?php echo $baseUrl; ?>/php/admin/admin_requests.php" class="<?php echo isActive('admin_requests', $currentPage); ?>"><i class="fa-solid fa-file-contract"></i> Requests</a>
            <a href="<?php echo $baseUrl; ?>/php/admin/orders.php" class="<?php echo isActive('admin_orders', $currentPage); ?>"><i class="fa-solid fa-receipt"></i> Manage Orders</a>
            <a href="<?php echo $baseUrl; ?>/php/admin/restaurants.php" class="<?php echo isActive('admin_restaurants', $currentPage); ?>"><i class="fa-solid fa-store"></i> Manage Stores</a>
            <a href="<?php echo $baseUrl; ?>/php/admin/menu.php" class="<?php echo isActive('admin_menu', $currentPage); ?>"><i class="fa-solid fa-utensils"></i> Manage Menu Items</a>
            <a href="<?php echo $baseUrl; ?>/php/forms/profile.php" class="<?php echo isActive('profile', $currentPage); ?>"><i class="fa-solid fa-user"></i> My Profile</a>

        <?php
else: ?>
            <!-- Consumer -->
            <a href="<?php echo $baseUrl; ?>/php/auth/dashboard.php" class="<?php echo isActive('consumer_dashboard', $currentPage);
    echo isActive('dashboard', $currentPage); ?>"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="<?php echo $baseUrl; ?>/php/forms/order_food.php" class="<?php echo isActive('order_food', $currentPage); ?>"><i class="fa-solid fa-utensils"></i> Order Food</a>
            <a href="<?php echo $baseUrl; ?>/php/forms/cart.php" class="<?php echo isActive('cart', $currentPage); ?>"><i class="fa-solid fa-cart-shopping"></i> My Cart</a>
            <a href="<?php echo $baseUrl; ?>/php/forms/order_history.php" class="<?php echo isActive('order_history', $currentPage); ?>"><i class="fa-solid fa-receipt"></i> Order History</a>
            <a href="<?php echo $baseUrl; ?>/php/forms/favorites.php" class="<?php echo isActive('favorites', $currentPage); ?>"><i class="fa-solid fa-heart"></i> Favorites</a>
            <a href="<?php echo $baseUrl; ?>/php/forms/payment_methods.php" class="<?php echo isActive('payment_methods', $currentPage); ?>"><i class="fa-solid fa-credit-card"></i> Payment Methods</a>
            <a href="<?php echo $baseUrl; ?>/php/forms/profile.php" class="<?php echo isActive('profile', $currentPage); ?>"><i class="fa-solid fa-user"></i> My Profile</a>
        <?php
endif; ?>

        <a href="<?php echo $baseUrl; ?>/php/auth/logout.php" class="danger-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>
</aside>
