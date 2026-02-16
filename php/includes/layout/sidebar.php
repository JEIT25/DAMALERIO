<?php
/**
 * Role-based sidebar (AMORA-style). Expects $basePath, $currentPage, $user / $userRole.
 */
if (!isset($user)) {
    $user = $_SESSION['user'] ?? null;
}
$userRole = $user['role'] ?? 'consumer';
if (!isset($basePath)) {
    $basePath = isset($base) ? rtrim($base, '/') . '/' : '../../';
}
$baseUrl = getBaseUrl();
$currentPage = $currentPage ?? 'dashboard';

function isActive($page) {
    global $currentPage;
    return $currentPage === $page ? 'active' : '';
}
?>
<aside class="dashboard-sidebar">
    <div class="profile-section">
        <img src="<?php echo $basePath; ?>images/profile.png" alt="Profile" class="profile">
        <?php if ($user): ?>
            <h2><?php echo htmlspecialchars($user['firstName'] . ' ' . $user['lastName']); ?></h2>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
            <span class="user-role-badge"><?php
                echo htmlspecialchars($userRole === 'consumer' ? 'Consumer' : ($userRole === 'admin' ? 'Admin' : 'Super Admin'));
            ?></span>
        <?php endif; ?>
    </div>
    <nav class="sidebar-menu">
        <?php if ($userRole === 'consumer'): ?>
            <a href="<?php echo $baseUrl; ?>/php/auth/dashboard.php" class="<?php echo isActive('dashboard'); ?>"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
            <a href="<?php echo $baseUrl; ?>/php/forms/order_food.php" class="<?php echo isActive('order_food'); ?>"><i class="fa-solid fa-utensils"></i> Order Food</a>
            <a href="<?php echo $baseUrl; ?>/php/forms/cart.php" class="<?php echo isActive('cart'); ?>"><i class="fa-solid fa-cart-shopping"></i> Cart</a>
            <a href="<?php echo $baseUrl; ?>/php/forms/order_history.php" class="<?php echo isActive('order_history'); ?>"><i class="fa-solid fa-receipt"></i> Order History</a>
            <a href="<?php echo $baseUrl; ?>/php/forms/track_order.php" class="<?php echo isActive('track_order'); ?>"><i class="fa-solid fa-truck-fast"></i> Track Order</a>
            <a href="<?php echo $baseUrl; ?>/php/forms/favorites.php" class="<?php echo isActive('favorites'); ?>"><i class="fa-solid fa-heart"></i> Favorites</a>
            <a href="<?php echo $baseUrl; ?>/php/forms/payment_methods.php" class="<?php echo isActive('payment_methods'); ?>"><i class="fa-solid fa-credit-card"></i> Payment Methods</a>
            <a href="<?php echo $baseUrl; ?>/php/forms/change_password.php" class="<?php echo isActive('change_password'); ?>"><i class="fa-solid fa-key"></i> Change Password</a>
        <?php elseif ($userRole === 'admin'): ?>
            <a href="<?php echo $baseUrl; ?>/php/auth/dashboard.php" class="<?php echo isActive('dashboard'); ?>">Dashboard</a>
            <a href="<?php echo $baseUrl; ?>/php/admin/orders.php" class="<?php echo isActive('orders'); ?>">Manage Orders</a>
            <a href="<?php echo $baseUrl; ?>/php/admin/restaurants.php" class="<?php echo isActive('restaurants'); ?>">Restaurants</a>
            <a href="<?php echo $baseUrl; ?>/php/admin/menu.php" class="<?php echo isActive('menu'); ?>">Menu Items</a>
            <a href="<?php echo $baseUrl; ?>/php/pages/users/admin/users/index.php" class="<?php echo isActive('users'); ?>"><i class="fa-solid fa-users"></i> Manage Users</a>
            <a href="<?php echo $baseUrl; ?>/php/pages/users/admin/approvals.php" class="<?php echo isActive('approvals'); ?>"><i class="fa-solid fa-clipboard-list"></i> My Requests</a>
            <a href="<?php echo $baseUrl; ?>/php/forms/change_password.php">Change Password</a>
        <?php else: ?>
            <a href="<?php echo $baseUrl; ?>/php/auth/dashboard.php" class="<?php echo isActive('dashboard'); ?>">Dashboard</a>
            <a href="<?php echo $baseUrl; ?>/php/superadmin/index.php" class="<?php echo isActive('users'); ?>">Users & Roles</a>
            <a href="<?php echo $baseUrl; ?>/php/pages/users/superadmin/approvals/index.php" class="<?php echo isActive('approvals'); ?>"><i class="fa-solid fa-clipboard-check"></i> Approval Requests</a>
            <a href="<?php echo $baseUrl; ?>/php/admin/index.php" class="<?php echo isActive('admin'); ?>">Admin Panel</a>
            <a href="<?php echo $baseUrl; ?>/php/forms/change_password.php">Change Password</a>
        <?php endif; ?>
    </nav>
</aside>
