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
    <title>Menu Items - Admin</title>
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
                <a href="restaurants.php">Restaurants</a>
                <a href="menu.php" class="active">Menu Items</a>
            </nav>
        </aside>
        <main class="dashboard-main">
            <h1>Menu Items</h1>
            <div class="form-group">
                <label>Filter by restaurant</label>
                <select id="restFilter">
                    <option value="">All restaurants</option>
                </select>
            </div>
            <button type="button" id="addMenuBtn" class="submitBtn" style="margin:1rem 0;">Add Menu Item</button>
            <div id="menuList"></div>
            <div id="menuModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:200; align-items:center; justify-content:center;">
                <div style="background:white; padding:2rem; border-radius:1rem; max-width:400px; width:90%;">
                    <h2 id="menuModalTitle">Add Menu Item</h2>
                    <form id="menuForm">
                        <input type="hidden" name="id" id="menu_id" value="">
                        <div class="form-group">
                            <label>Restaurant</label>
                            <select name="restaurant_id" id="menu_restaurant_id" required></select>
                        </div>
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" id="menu_name" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" id="menu_desc" rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Price</label>
                            <input type="number" step="0.01" min="0" name="price" id="menu_price" required>
                        </div>
                        <div class="form-group">
                            <label><input type="checkbox" name="is_available" id="menu_available" value="1" checked> Available</label>
                        </div>
                        <button type="submit" class="submitBtn">Save</button>
                        <button type="button" id="closeMenuModal" class="btn-remove" style="margin-left:0.5rem;">Cancel</button>
                    </form>
                </div>
            </div>
        </main>
        <footer class="dashboard-footer"><div class="footer-bottom"><p>&copy; 2025</p></div></footer>
    </div>
    <script>
        const api = '<?php echo $base; ?>' + '/php/database';
        let restaurants = [];
        fetch(api + '/admin_restaurants.php', { credentials: 'same-origin' })
            .then(r => r.json())
            .then(data => {
                if (data.success && data.restaurants) {
                    restaurants = data.restaurants;
                    const sel = document.getElementById('restFilter');
                    restaurants.forEach(r => { sel.innerHTML += `<option value="${r.id}">${escapeHtml(r.name)}</option>`; });
                    const sel2 = document.getElementById('menu_restaurant_id');
                    restaurants.forEach(r => { sel2.innerHTML += `<option value="${r.id}">${escapeHtml(r.name)}</option>`; });
                    document.getElementById('restFilter').onchange = loadMenu;
                    loadMenu();
                }
            });
        function loadMenu() {
            const rid = document.getElementById('restFilter').value;
            const url = rid ? api + '/admin_menu.php?restaurant_id=' + rid : api + '/admin_menu.php';
            fetch(url, { credentials: 'same-origin' })
                .then(r => r.json())
                .then(data => {
                    if (!data.success || !data.menu.length) {
                        document.getElementById('menuList').innerHTML = '<p class="muted">No menu items.</p>';
                        return;
                    }
                    const getRestName = (id) => (restaurants.find(r => r.id == id) || {}).name || '';
                    let html = '<table class="orders-table"><thead><tr><th>Name</th><th>Restaurant</th><th>Price</th><th>Available</th><th></th></tr></thead><tbody>';
                    data.menu.forEach(m => {
                        html += `<tr><td>${escapeHtml(m.name)}</td><td>${escapeHtml(getRestName(m.restaurant_id))}</td><td>₱${parseFloat(m.price).toFixed(2)}</td><td>${m.is_available ? 'Yes' : 'No'}</td><td><button type="button" class="edit-menu" data-id="${m.id}" data-rid="${m.restaurant_id}" data-name="${escapeAttr(m.name)}" data-desc="${escapeAttr(m.description)}" data-price="${m.price}" data-avail="${m.is_available}">Edit</button> <button type="button" class="delete-menu" data-id="${m.id}">Delete</button></td></tr>`;
                    });
                    html += '</tbody></table>';
                    document.getElementById('menuList').innerHTML = html;
                    document.querySelectorAll('.edit-menu').forEach(btn => {
                        btn.addEventListener('click', () => {
                            document.getElementById('menuModalTitle').textContent = 'Edit Menu Item';
                            document.getElementById('menu_id').value = btn.dataset.id;
                            document.getElementById('menu_restaurant_id').value = btn.dataset.rid;
                            document.getElementById('menu_name').value = btn.dataset.name || '';
                            document.getElementById('menu_desc').value = btn.dataset.desc || '';
                            document.getElementById('menu_price').value = btn.dataset.price || '';
                            document.getElementById('menu_available').checked = btn.dataset.avail == 1;
                            document.getElementById('menuModal').style.display = 'flex';
                        });
                    });
                    document.querySelectorAll('.delete-menu').forEach(btn => {
                        btn.addEventListener('click', () => {
                            if (!confirm('Delete this item?')) return;
                            const fd = new FormData(); fd.append('action', 'delete'); fd.append('id', btn.dataset.id);
                            fetch(api + '/admin_menu.php', { method: 'POST', body: fd, credentials: 'same-origin' })
                                .then(r => r.json())
                                .then(d => { if (d.success) loadMenu(); });
                        });
                    });
                });
        }
        document.getElementById('addMenuBtn').onclick = () => {
            document.getElementById('menuModalTitle').textContent = 'Add Menu Item';
            document.getElementById('menuForm').reset();
            document.getElementById('menu_id').value = '';
            document.getElementById('menu_restaurant_id').innerHTML = restaurants.map(r => `<option value="${r.id}">${escapeHtml(r.name)}</option>`).join('');
            document.getElementById('menuModal').style.display = 'flex';
        };
        document.getElementById('closeMenuModal').onclick = () => document.getElementById('menuModal').style.display = 'none';
        document.getElementById('menuForm').onsubmit = (e) => {
            e.preventDefault();
            const fd = new FormData();
            fd.append('action', 'save');
            fd.append('id', document.getElementById('menu_id').value);
            fd.append('restaurant_id', document.getElementById('menu_restaurant_id').value);
            fd.append('name', document.getElementById('menu_name').value);
            fd.append('description', document.getElementById('menu_desc').value);
            fd.append('price', document.getElementById('menu_price').value);
            fd.append('is_available', document.getElementById('menu_available').checked ? 1 : 0);
            fetch(api + '/admin_menu.php', { method: 'POST', body: fd, credentials: 'same-origin' })
                .then(r => r.json())
                .then(d => { if (d.success) { document.getElementById('menuModal').style.display = 'none'; loadMenu(); } else alert(d.error || 'Error'); });
        };
        function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
        function escapeAttr(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML.replace(/"/g, '&quot;'); }
    </script>
</body>
</html>
