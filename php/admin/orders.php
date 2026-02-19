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
    <link rel="stylesheet" href="../../css/design-system.css">
    <link rel="stylesheet" href="../../css/dashboard.css">
    <link rel="stylesheet" href="../../css/order_history.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/layout/navbar.php'; ?>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php $currentPage = 'admin_orders';
include __DIR__ . '/../includes/layout/sidebar.php'; ?>
        <main class="dashboard-main">
            <h1>Manage Orders</h1>
            <div id="ordersTable"></div>
        </main>
        <?php include __DIR__ . '/../includes/layout/footer.php'; ?>
    </div>
    <script>
        window.BASE_URL = '<?php echo $base; ?>';
        const api = window.BASE_URL + '/php/database';
        const statuses = ['pending','confirmed','preparing','out_for_delivery','delivered','cancelled'];
        let currentPage = 1;

        function loadOrders(page = 1) {
            currentPage = page;
            fetch(api + '/admin_orders_list.php?page=' + page, { credentials: 'same-origin' })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;

                    const totalPages = data.pagination?.total_pages || 1;

                    if (!data.orders.length) {
                        document.getElementById('ordersTable').innerHTML = '<div class="empty-state"><p>No orders found.</p></div>';
                        return;
                    }

                    let html = '<table class="orders-table"><thead><tr><th>ID</th><th>Restaurant</th><th>User ID</th><th>Total</th><th>Status</th><th>Date</th><th>Update</th></tr></thead><tbody>';
                    data.orders.forEach(o => {
                        const sc = o.status === 'delivered' ? 'status-ok' : o.status === 'cancelled' ? 'status-cancel' : 'status-pending';

                        html += `<tr>
                            <td>#${o.id}</td>
                            <td>${escapeHtml(o.restaurant_name)}</td>
                            <td>${escapeHtml(o.user_id)}</td>
                            <td style="font-weight:600">₱${parseFloat(o.total_amount).toFixed(2)}</td>
                            <td><span class="status-badge ${sc}">${o.status}</span></td>
                            <td>${new Date(o.created_at).toLocaleString()}</td>
                            <td><select class="status-select input-field" style="padding:0.25rem; font-size:0.85rem;" data-order-id="${o.id}">${statuses.map(s => '<option value="'+s+'"'+(s===o.status?' selected':'')+'>'+s+'</option>').join('')}</select></td>
                        </tr>`;
                    });
                    html += '</tbody></table>';

                     html += `<div class="pagination-controls" style="margin-top:1rem; display:flex; justify-content:flex-end; gap:0.5rem;">
                        <button class="btn-secondary" onclick="loadOrders(currentPage-1)" ${currentPage<=1?'disabled':''}>Previous</button>
                        <span style="align-self:center;">Page ${currentPage} of ${totalPages}</span>
                        <button class="btn-secondary" onclick="loadOrders(currentPage+1)" ${currentPage>=totalPages?'disabled':''}>Next</button>
                    </div>`;

                    document.getElementById('ordersTable').innerHTML = html;

                    document.querySelectorAll('.status-select').forEach(sel => {
                        sel.addEventListener('change', () => {
                            const fd = new FormData();
                            fd.append('order_id', sel.dataset.orderId);
                            fd.append('status', sel.value);
                            fetch(api + '/admin_order_status.php', { method: 'POST', body: fd, credentials: 'same-origin' })
                                .then(r => r.json())
                                .then(d => {
                                    if (d.success) {
                                        const row = sel.closest('tr');
                                        const badge = row.querySelector('.status-badge');
                                        badge.textContent = sel.value;
                                        badge.className = 'status-badge ' + (sel.value === 'delivered' ? 'status-ok' : sel.value === 'cancelled' ? 'status-cancel' : 'status-pending');
                                    }
                                });
                        });
                    });
                })
                .catch(() => document.getElementById('ordersTable').innerHTML = '<p class="muted">Error loading orders.</p>');
        }

        loadOrders();
        function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    </script>
</body>
</html>
