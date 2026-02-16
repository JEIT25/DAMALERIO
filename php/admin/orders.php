<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin', 'superadmin']);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout_action'])) {
    session_unset();
    session_destroy();
    header('Location: ' . getBaseUrl() . '/php/forms/login.php');
    exit;
}
$base = getBaseUrl();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin</title>
    <link rel="stylesheet" href="../../css/serve_asset.php?file=design-system.css">
    <link rel="stylesheet" href="../../css/serve_asset.php?file=dashboard.css">
    <link rel="stylesheet" href="../../css/serve_asset.php?file=order_history.css">
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <div class="navbar-left">
                <a href="index.php" style="display:flex;align-items:center;gap:0.75rem;text-decoration:none;color:inherit;">
                    <img src="../../images/logo4.png" alt="Logo" class="logo">
                    <span class="navbar-text">FoodGrab <span class="navbar-subtext">Admin</span></span>
                </a>
            </div>
            <div class="navbar-right">
                <form action="" method="POST">
                    <input type="hidden" name="logout_action" value="1">
                    <button type="submit" class="nav-link">Log Out</button>
                </form>
            </div>
        </header>
        <aside class="dashboard-sidebar">
            <nav class="sidebar-menu">
                <a href="index.php">Dashboard</a>
                <a href="orders.php" class="active">Manage Orders</a>
                <a href="restaurants.php">Restaurants</a>
                <a href="menu.php">Menu Items</a>
            </nav>
        </aside>
        <main class="dashboard-main">
            <h1>Manage Orders</h1>
            <div id="ordersTable"></div>
        </main>
        <footer class="dashboard-footer"><div class="footer-bottom"><p>&copy; 2025</p></div></footer>
    </div>
    <script>
        window.BASE_URL = '<?php echo $base; ?>';
        const api = window.BASE_URL + '/php/database';
        const statuses = ['pending','confirmed','preparing','out_for_delivery','delivered','cancelled'];
        fetch(api + '/admin_orders_list.php', { credentials: 'same-origin' })
            .then(r => r.json())
            .then(data => {
                if (!data.success || !data.orders.length) {
                    document.getElementById('ordersTable').innerHTML = '<p class="muted">No orders.</p>';
                    return;
                }
                let html = '<table class="orders-table"><thead><tr><th>ID</th><th>Restaurant</th><th>User ID</th><th>Total</th><th>Status</th><th>Date</th><th>Update</th></tr></thead><tbody>';
                data.orders.forEach(o => {
                    const sc = o.status === 'delivered' ? 'status-ok' : o.status === 'cancelled' ? 'status-cancel' : 'status-pending';
                    html += `<tr>
                        <td>#${o.id}</td>
                        <td>${escapeHtml(o.restaurant_name)}</td>
                        <td>${escapeHtml(o.user_id)}</td>
                        <td>₱${parseFloat(o.total_amount).toFixed(2)}</td>
                        <td><span class="status-badge ${sc}">${o.status}</span></td>
                        <td>${o.created_at}</td>
                        <td><select class="status-select" data-order-id="${o.id}">${statuses.map(s => '<option value="'+s+'"'+(s===o.status?' selected':'')+'>'+s+'</option>').join('')}</select></td>
                    </tr>`;
                });
                html += '</tbody></table>';
                document.getElementById('ordersTable').innerHTML = html;
                document.querySelectorAll('.status-select').forEach(sel => {
                    sel.addEventListener('change', () => {
                        const fd = new FormData();
                        fd.append('order_id', sel.dataset.orderId);
                        fd.append('status', sel.value);
                        fetch(api + '/admin_order_status.php', { method: 'POST', body: fd, credentials: 'same-origin' })
                            .then(r => r.json())
                            .then(d => { if (d.success) { const row = sel.closest('tr'); const badge = row.querySelector('.status-badge'); badge.textContent = sel.value; badge.className = 'status-badge ' + (sel.value === 'delivered' ? 'status-ok' : sel.value === 'cancelled' ? 'status-cancel' : 'status-pending'); } });
                    });
                });
            })
            .catch(() => document.getElementById('ordersTable').innerHTML = '<p class="muted">Error loading orders.</p>');
        function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    </script>
</body>
</html>
