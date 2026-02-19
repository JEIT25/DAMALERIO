<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
if (!hasRole('consumer')) {
    header('Location: ' . getDashboardRedirect());
    exit;
}
$pageTitle = 'My Cart';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart - FoodGrab</title>
    <link rel="stylesheet" href="../../css/design-system.css">
    <link rel="stylesheet" href="../../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .cart-container { max-width: 800px; margin: 2rem auto; }
        .cart-item { display: flex; align-items: center; background: var(--bg-card); padding: 1rem; margin-bottom: 1rem; border-radius: 8px; box-shadow: var(--shadow-sm); }
        .cart-img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-right: 1rem; background: #eee; }
        .cart-details { flex: 1; }
        .cart-actions { display: flex; align-items: center; gap: 0.5rem; }
        .qty-input { width: 50px; text-align: center; padding: 0.25rem; }
        .cart-total { font-size: 1.25rem; font-weight: bold; text-align: right; margin-top: 1rem; }
        .checkout-btn { display: block; width: 100%; text-align: center; margin-top: 1rem; }
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
            <div class="cart-container">
                <h1 class="page-title">Shopping Cart</h1>
                <div id="cartItems">Loading...</div>
                <div id="cartSummary" style="display:none;">
                    <div class="cart-total">Total: ₱<span id="totalPrice">0.00</span></div>
                    <button onclick="checkout()" class="btn-primary checkout-btn">Proceed to Checkout</button>
                </div>
            </div>
        </main>
    </div>

    <script>
        const api = '../../php/database';

        function loadCart() {
            fetch(api + '/cart_get.php')
                .then(r => r.json())
                .then(data => {
                    if (!data.success || data.items.length === 0) {
                        document.getElementById('cartItems').innerHTML = '<p class="muted">Your cart is empty.</p>';
                        document.getElementById('cartSummary').style.display = 'none';
                        return;
                    }
                    let html = '';
                    data.items.forEach(item => {
                        html += `<div class="cart-item">
                            <img src="${item.image_path || '../../images/placeholder_food.png'}" class="cart-img" alt="Food">
                            <div class="cart-details">
                                <h3>${escapeHtml(item.name)}</h3>
                                <p class="muted">${escapeHtml(item.restaurant_name)}</p>
                                <p>₱${parseFloat(item.price).toFixed(2)}</p>
                            </div>
                            <div class="cart-actions">
                                <button onclick="updateQty(${item.id}, ${item.quantity - 1})" class="btn-secondary btn-sm">-</button>
                                <span class="qty-display">${item.quantity}</span>
                                <button onclick="updateQty(${item.id}, ${item.quantity + 1})" class="btn-secondary btn-sm">+</button>
                                <button onclick="updateQty(${item.id}, 0)" class="btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div>`;
                    });
                    document.getElementById('cartItems').innerHTML = html;
                    document.getElementById('totalPrice').textContent = data.total.toFixed(2);
                    document.getElementById('cartSummary').style.display = 'block';
                });
        }

        function updateQty(id, qty) {
            const fd = new FormData();
            fd.append('item_id', id);
            fd.append('quantity', qty);
            fetch(api + '/cart_update.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => {
                    if (d.success) loadCart();
                    else alert(d.error);
                });
        }

        function checkout() {
            window.location.href = 'checkout.php';
        }

        function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }

        loadCart();
    </script>
</body>
</html>
