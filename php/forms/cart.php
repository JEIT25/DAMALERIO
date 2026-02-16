<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireRole('consumer');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout_action'])) {
    session_unset();
    session_destroy();
    header('Location: ' . getBaseUrl() . '/php/auth/login.php');
    exit;
}

$basePath = getBasePath(__FILE__);
$baseUrl = getBaseUrl();
$currentPage = 'cart';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - FoodGrab</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>css/serve_asset.php?file=design-system.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>css/serve_asset.php?file=dashboard.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>css/serve_asset.php?file=order_food.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>css/serve_asset.php?file=cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php $showSidebarToggle = true; include __DIR__ . '/../includes/layout/navbar.php'; ?>
    <div class="dashboard-container">
        <div class="sidebar-overlay" id="sidebarOverlay" aria-hidden="true"></div>
        <?php include __DIR__ . '/../includes/layout/sidebar.php'; ?>
        <main class="dashboard-main">
            <h1 class="page-title">Your Cart</h1>
            <p class="page-subtitle">Review your items below, then proceed to checkout.</p>
            <div id="cartEmpty" class="cart-empty" style="display:none;">
                <p>Your cart is empty.</p>
                <a href="<?php echo $baseUrl; ?>/php/forms/order_food.php" class="submitBtn btn-primary">Order Food</a>
            </div>
            <div id="cartContent" class="cart-content">
                <div id="cartByStore"></div>
                <div class="cart-actions">
                    <a href="<?php echo $baseUrl; ?>/php/forms/order_food.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Continue shopping</a>
                    <a href="<?php echo $baseUrl; ?>/php/forms/checkout.php" id="checkoutBtn" class="submitBtn">Proceed to Checkout</a>
                </div>
            </div>
        </main>
        <?php include __DIR__ . '/../includes/layout/footer.php'; ?>
    </div>
    <script>
        (function(){ var o=document.getElementById('sidebarOverlay'),t=document.getElementById('sidebarToggle'); if(t&&o){ t.addEventListener('click',function(){ document.body.classList.toggle('sidebar-open'); o.classList.toggle('is-open',document.body.classList.contains('sidebar-open')); }); o.addEventListener('click',function(){ document.body.classList.remove('sidebar-open'); o.classList.remove('is-open'); }); } })();
    </script>
    <script>
        window.BASE_URL = '<?php echo $baseUrl; ?>';
    </script>
    <script src="<?php echo $basePath; ?>js/serve_asset.php?file=cart.js"></script>
</body>
</html>
