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
    <title>Restaurants - Admin</title>
    <link rel="stylesheet" href="../../css/serve_asset.php?file=design-system.css">
    <link rel="stylesheet" href="../../css/serve_asset.php?file=dashboard.css">
    <link rel="stylesheet" href="../../css/serve_asset.php?file=order_food.css">
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
                <a href="orders.php">Manage Orders</a>
                <a href="restaurants.php" class="active">Restaurants</a>
                <a href="menu.php">Menu Items</a>
            </nav>
        </aside>
        <main class="dashboard-main">
            <h1>Restaurants</h1>
            <button type="button" id="addRestaurantBtn" class="submitBtn" style="margin-bottom:1rem;">Add Restaurant</button>
            <div id="restaurantsList"></div>
            <div id="restaurantModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:200; align-items:center; justify-content:center;">
                <div style="background:white; padding:2rem; border-radius:1rem; max-width:400px; width:90%;">
                    <h2 id="modalTitle">Add Restaurant</h2>
                    <form id="restaurantForm">
                        <input type="hidden" name="id" id="rest_id" value="">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" id="rest_name" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" id="rest_desc" rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="address" id="rest_address">
                        </div>
                        <div class="form-group">
                            <label><input type="checkbox" name="is_active" id="rest_active" value="1" checked> Active</label>
                        </div>
                        <button type="submit" class="submitBtn">Save</button>
                        <button type="button" id="closeModal" class="btn-remove" style="margin-left:0.5rem;">Cancel</button>
                    </form>
                </div>
            </div>
        </main>
        <footer class="dashboard-footer"><div class="footer-bottom"><p>&copy; 2025</p></div></footer>
    </div>
    <script>
        const api = '<?php echo $base; ?>' + '/php/database';
        function load() {
            fetch(api + '/admin_restaurants.php', { credentials: 'same-origin' })
                .then(r => r.json())
                .then(data => {
                    if (!data.success || !data.restaurants.length) {
                        document.getElementById('restaurantsList').innerHTML = '<p class="muted">No restaurants.</p>';
                        return;
                    }
                    let html = '<table class="orders-table"><thead><tr><th>Name</th><th>Address</th><th>Active</th><th></th></tr></thead><tbody>';
                    data.restaurants.forEach(r => {
                        html += `<tr><td>${escapeHtml(r.name)}</td><td>${escapeHtml(r.address)}</td><td>${r.is_active ? 'Yes' : 'No'}</td><td><button type="button" class="edit-rest" data-id="${r.id}" data-name="${escapeAttr(r.name)}" data-desc="${escapeAttr(r.description)}" data-addr="${escapeAttr(r.address)}" data-active="${r.is_active}">Edit</button> <button type="button" class="delete-rest" data-id="${r.id}">Delete</button></td></tr>`;
                    });
                    html += '</tbody></table>';
                    document.getElementById('restaurantsList').innerHTML = html;
                    document.querySelectorAll('.edit-rest').forEach(btn => {
                        btn.addEventListener('click', () => {
                            document.getElementById('modalTitle').textContent = 'Edit Restaurant';
                            document.getElementById('rest_id').value = btn.dataset.id;
                            document.getElementById('rest_name').value = btn.dataset.name || '';
                            document.getElementById('rest_desc').value = btn.dataset.desc || '';
                            document.getElementById('rest_address').value = btn.dataset.addr || '';
                            document.getElementById('rest_active').checked = btn.dataset.active == 1;
                            document.getElementById('restaurantModal').style.display = 'flex';
                        });
                    });
                    document.querySelectorAll('.delete-rest').forEach(btn => {
                        btn.addEventListener('click', () => {
                            if (!confirm('Delete this restaurant and its menu items?')) return;
                            const fd = new FormData(); fd.append('action', 'delete'); fd.append('id', btn.dataset.id);
                            fetch(api + '/admin_restaurants.php', { method: 'POST', body: fd, credentials: 'same-origin' })
                                .then(r => r.json())
                                .then(d => { if (d.success) load(); });
                        });
                    });
                });
        }
        document.getElementById('addRestaurantBtn').onclick = () => {
            document.getElementById('modalTitle').textContent = 'Add Restaurant';
            document.getElementById('restaurantForm').reset();
            document.getElementById('rest_id').value = '';
            document.getElementById('restaurantModal').style.display = 'flex';
        };
        document.getElementById('closeModal').onclick = () => document.getElementById('restaurantModal').style.display = 'none';
        document.getElementById('restaurantForm').onsubmit = (e) => {
            e.preventDefault();
            const fd = new FormData();
            fd.append('action', 'save');
            fd.append('id', document.getElementById('rest_id').value);
            fd.append('name', document.getElementById('rest_name').value);
            fd.append('description', document.getElementById('rest_desc').value);
            fd.append('address', document.getElementById('rest_address').value);
            fd.append('is_active', document.getElementById('rest_active').checked ? 1 : 0);
            fetch(api + '/admin_restaurants.php', { method: 'POST', body: fd, credentials: 'same-origin' })
                .then(r => r.json())
                .then(d => { if (d.success) { document.getElementById('restaurantModal').style.display = 'none'; load(); } else alert(d.error || 'Error'); });
        };
        function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
        function escapeAttr(s) { return escapeHtml(s).replace(/"/g, '&quot;'); }
        load();
    </script>
</body>
</html>
