<?php
/**
 * Navbar: fixed at top, full width. Expects $basePath, $baseUrl (or getBaseUrl()).
 * Shows cart icon (with badge) for consumers when $userRole === 'consumer'.
 */
if (!isset($basePath)) {
    $basePath = isset($base) ? rtrim($base, '/') . '/' : '../../';
}
if (!function_exists('getBaseUrl') && !isset($baseUrl)) {
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/DAMALERIO';
}
if (!isset($baseUrl)) {
    $baseUrl = getBaseUrl();
}
$navUserRole = $userRole ?? ($_SESSION['user']['role'] ?? '');
$showCart = ($navUserRole === 'consumer');
?>
<nav class="navbar" role="navigation" aria-label="Main">
    <div class="navbar-inner">
        <div class="navbar-left">
            <a href="<?php echo htmlspecialchars($baseUrl); ?>/php/auth/dashboard.php" class="navbar-brand" style="display:flex;align-items:center;gap:0.75rem;text-decoration:none;color:inherit;">
                <img src="<?php echo htmlspecialchars($basePath); ?>images/logo4.png" alt="FoodGrab" class="logo">
                <span class="navbar-text">FoodGrab <span class="navbar-subtext">Online Food Delivery</span></span>
            </a>
        </div>
        <div class="navbar-right">
            <?php if ($showCart): ?>
            <a href="<?php echo htmlspecialchars($baseUrl); ?>/php/forms/cart.php" class="nav-cart-link" id="navCartLink" title="Cart">
                <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>
                <span class="nav-cart-count" id="navbarCartCount">0</span>
            </a>
            <?php
endif; ?>
            <?php if (!empty($showSidebarToggle)): ?>
            <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Open menu" aria-expanded="false">
                <i class="fa-solid fa-bars" aria-hidden="true"></i>
            </button>
            <?php
endif; ?>
            <form action="<?php echo htmlspecialchars($baseUrl); ?>/php/auth/dashboard.php" method="POST" style="display:inline;">
                <input type="hidden" name="logout_action" value="1">
                <button type="submit" class="nav-link"><i class="fa-solid fa-right-from-bracket"></i> Log Out</button>
            </form>
        </div>
    </div>
</nav>
<?php if ($showCart): ?>
<script>
(function() {
    function updateNavCartBadge() {
        var el = document.getElementById('navbarCartCount');
        if (!el) return;
        fetch('<?php echo htmlspecialchars($baseUrl); ?>/php/database/cart_get.php')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var count = 0;
                if (data.success && data.items) {
                    data.items.forEach(function(i) { count += (i.quantity || 0); });
                }
                el.textContent = count;
                el.classList.toggle('nav-cart-count--zero', count === 0);
            })
            .catch(function() { el.textContent = '0'; el.classList.add('nav-cart-count--zero'); });
    }
    updateNavCartBadge();
    window.updateNavCartBadge = updateNavCartBadge;
})();
</script>
<?php
endif; ?>
