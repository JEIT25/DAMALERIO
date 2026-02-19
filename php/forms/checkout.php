<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
if (!hasRole('consumer')) {
    header('Location: ' . getDashboardRedirect());
    exit;
}
$pageTitle = 'Checkout';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - FoodGrab</title>
    <link rel="stylesheet" href="../../css/design-system.css">
    <link rel="stylesheet" href="../../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .checkout-container { max-width: 600px; margin: 2rem auto; background: var(--bg-card); padding: 2rem; border-radius: var(--radius-lg); }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
        select, textarea, input { width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/layout/navbar.php'; ?>
    <div class="dashboard-container" style="padding-top: 80px;">
        <!-- Sidebar -->
    <div class="dashboard-container" style="padding-top: 80px;">
        <!-- Sidebar -->
        <?php $currentPage = 'cart';
include __DIR__ . '/../includes/layout/sidebar.php'; ?>

        <main class="dashboard-main">
            <div class="checkout-container">
                <h1 class="page-title">Checkout</h1>
                <form id="checkoutForm">
                    <div class="form-group">
                        <label>Delivery Address</label>
                        <textarea name="address" required placeholder="Enter complete address..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select name="payment_method">
                            <option value="cod">Cash on Delivery</option>
                            <!-- Future: Load saved methods -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" placeholder="Special instructions..."></textarea>
                    </div>
                    <button type="submit" class="btn-primary" style="width: 100%;">Place Order</button>
                </form>
            </div>
        </main>
    </div>

    <script>
        const api = '../../php/database';
        document.getElementById('checkoutForm').onsubmit = function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            fd.append('from_cart', '1'); // Flag to use cart items

            fetch(api + '/place_order.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        alert('Order placed successfully!');
                        window.location.href = '../auth/dashboard.php';
                    } else {
                        alert(d.error || 'Failed to place order.');
                    }
                });
        };
    </script>
</body>
</html>
