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
$currentPage = 'checkout';
$user = $_SESSION['user'];
$delivery_address = trim(
    ($user['purok'] ?? '') . ', ' .
    ($user['barangay'] ?? '') . ', ' .
    ($user['city'] ?? '') . ', ' .
    ($user['province'] ?? '') . ' ' .
    ($user['zipCode'] ?? '') . ', ' .
    ($user['country'] ?? '')
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - FoodGrab</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>css/serve_asset.php?file=design-system.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>css/serve_asset.php?file=dashboard.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>css/serve_asset.php?file=order_food.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>css/serve_asset.php?file=cart.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>css/serve_asset.php?file=checkout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php $showSidebarToggle = true; include __DIR__ . '/../includes/layout/navbar.php'; ?>
    <div class="dashboard-container">
        <div class="sidebar-overlay" id="sidebarOverlay" aria-hidden="true"></div>
        <?php include __DIR__ . '/../includes/layout/sidebar.php'; ?>
        <main class="dashboard-main checkout-page">
            <h1 class="page-title">Checkout</h1>
            <p class="page-subtitle">Review your order and confirm delivery details.</p>
            <div id="checkoutEmpty" class="cart-empty" style="display:none;">
                <p>Your cart is empty.</p>
                <a href="<?php echo $baseUrl; ?>/php/forms/cart.php" class="submitBtn btn-primary">View Cart</a>
            </div>
            <div id="checkoutContent" class="checkout-content" style="display:none;">
                <form id="checkoutForm">
                    <div class="checkout-layout">
                        <div class="checkout-main">
                            <div class="checkout-card">
                                <div class="checkout-card-header">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <span>Delivery address</span>
                                </div>
                                <div class="checkout-card-body checkout-address">
                                    <textarea id="delivery_address" name="delivery_address" rows="3" required placeholder="Enter full delivery address"><?php echo htmlspecialchars($delivery_address); ?></textarea>
                                </div>
                            </div>
                            <div class="checkout-card">
                                <div class="checkout-card-header">
                                    <i class="fa-solid fa-note-sticky"></i>
                                    <span>Order notes</span>
                                </div>
                                <div class="checkout-card-body checkout-notes">
                                    <input type="text" id="notes" name="notes" placeholder="e.g. No onions, leave at gate">
                                </div>
                            </div>
                            <div class="checkout-card">
                                <div class="checkout-card-header">
                                    <i class="fa-solid fa-credit-card"></i>
                                    <span>Payment method</span>
                                </div>
                                <div class="checkout-card-body">
                                    <div id="paymentMethodsList" class="checkout-payment-options"></div>
                                    <div id="checkoutPaymentDetails" class="checkout-payment-details" aria-live="polite"></div>
                                </div>
                            </div>
                        </div>
                        <div class="checkout-sidebar">
                            <div class="checkout-summary-card">
                                <div class="checkout-card-header">
                                    <i class="fa-solid fa-bag-shopping"></i>
                                    <span>Order summary</span>
                                </div>
                                <div id="checkoutOrderSummary" class="checkout-summary-items"></div>
                                <div class="checkout-summary-total">
                                    <div class="checkout-summary-row grand">
                                        <span>Total</span>
                                        <span id="grandTotalDisplay">₱0.00</span>
                                    </div>
                                </div>
                                <div class="checkout-card-body" style="padding-top: 0;">
                                    <div class="checkout-actions-bar">
                                        <a href="<?php echo $baseUrl; ?>/php/forms/cart.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to cart</a>
                                        <button type="submit" class="btn-place-order" id="placeOrderBtn" form="checkoutForm">Place Order</button>
                                    </div>
                                </div>
                            </div>
                            <div class="checkout-mobile-bar">
                                <div>
                                    <span class="grand-label">Total </span>
                                    <span class="grand-value" id="mobileGrandTotal">₱0.00</span>
                                </div>
                                <button type="submit" class="btn-place-order" form="checkoutForm" id="placeOrderBtnMobile">Place Order</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- GCash payment confirmation modal (dummy flow) -->
            <div id="gcashPaymentModal" class="checkout-modal gcash-modal" role="dialog" aria-modal="true" aria-labelledby="gcashModalTitle" style="display: none;">
                <div class="checkout-modal-overlay"></div>
                <div class="checkout-modal-box gcash-modal-box">
                    <div id="gcashModalStepConfirm">
                        <div class="gcash-modal-icon"><i class="fa-solid fa-mobile-screen-button" aria-hidden="true"></i></div>
                        <h2 class="checkout-modal-title" id="gcashModalTitle">Pay with GCash</h2>
                        <p class="gcash-modal-amount">Amount: <strong id="gcashModalAmount">₱0.00</strong></p>
                        <p class="gcash-modal-hint">Click below to confirm payment. Your GCash will be charged when you confirm.</p>
                        <button type="button" class="checkout-modal-btn gcash-pay-btn" id="gcashConfirmBtn">Confirm payment</button>
                    </div>
                    <div id="gcashModalStepSuccess" style="display: none;">
                        <div class="checkout-modal-icon success"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
                        <h2 class="checkout-modal-title">Payment successful</h2>
                        <p class="checkout-modal-message">GCash has been charged. Your order is being placed.</p>
                        <button type="button" class="checkout-modal-btn success" id="gcashSuccessBtn">Continue</button>
                    </div>
                </div>
            </div>
            <!-- Checkout result modal (success / error) -->
            <div id="checkoutResultModal" class="checkout-modal" role="dialog" aria-modal="true" aria-labelledby="checkoutModalTitle" style="display: none;">
                <div class="checkout-modal-overlay"></div>
                <div class="checkout-modal-box">
                    <div class="checkout-modal-icon" id="checkoutModalIcon"></div>
                    <h2 class="checkout-modal-title" id="checkoutModalTitle">Result</h2>
                    <p class="checkout-modal-message" id="checkoutModalMessage"></p>
                    <button type="button" class="checkout-modal-btn" id="checkoutModalOk">OK</button>
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
        window.DELIVERY_ADDRESS = <?php echo json_encode($delivery_address); ?>;
    </script>
    <script src="<?php echo $basePath; ?>js/serve_asset.php?file=checkout.js"></script>
</body>
</html>
