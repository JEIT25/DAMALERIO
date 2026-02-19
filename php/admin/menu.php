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
    <link rel="stylesheet" href="../../css/design-system.css">
    <link rel="stylesheet" href="../../css/dashboard.css">
    <link rel="stylesheet" href="../../css/order_food.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/layout/navbar.php'; ?>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php $currentPage = 'admin_menu';
include __DIR__ . '/../includes/layout/sidebar.php'; ?>
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
        <?php include __DIR__ . '/../includes/layout/footer.php'; ?>
    </div>
    <script>
        const api = '<?php echo $base; ?>' + '/php/database';
        const api = '<?php echo $base; ?>' + '/php/database';
        let restaurants = [];
        let currentPage = 1;

        // Load restaurants for dropdowns
        fetch(api + '/admin_restaurants.php', { credentials: 'same-origin' })
            .then(r => r.json())
            .then(data => {
                if (data.success && data.restaurants) {
                    restaurants = data.restaurants;
                    const sel = document.getElementById('restFilter');
                    restaurants.forEach(r => { sel.innerHTML += `<option value="${r.id}">${escapeHtml(r.name)}</option>`; });
                    const sel2 = document.getElementById('menu_restaurant_id');
                    restaurants.forEach(r => { sel2.innerHTML += `<option value="${r.id}">${escapeHtml(r.name)}</option>`; });

                    document.getElementById('restFilter').onchange = () => loadMenu(1);
                    loadMenu();
                }
            });

        function loadMenu(page = 1) {
            currentPage = page;
            const rid = document.getElementById('restFilter').value;
            let url = api + '/admin_menu.php?page=' + page;
            if (rid) url += '&restaurant_id=' + rid;

            fetch(url, { credentials: 'same-origin' })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;

                    const totalPages = data.pagination?.total_pages || 1;

                    if (!data.menu.length) {
                        document.getElementById('menuList').innerHTML = '<div class="empty-state"><p>No menu items found.</p></div>';
                        return;
                    }

                    const getRestName = (id) => (restaurants.find(r => r.id == id) || {}).name || 'Unknown';

                    let html = '<table class="orders-table"><thead><tr><th>Name</th><th>Restaurant</th><th>Price</th><th>Available</th><th>Actions</th></tr></thead><tbody>';
                    data.menu.forEach(m => {
                        const availBtn = m.is_available == 1
                            ? `<button class="btn-secondary" style="color:var(--success-color); border-color:var(--success-color);" onclick="toggleAvail(${m.id}, 0)">Yes</button>`
                            : `<button class="btn-secondary" style="opacity:0.6;" onclick="toggleAvail(${m.id}, 1)">No</button>`;

                        html += `<tr>
                            <td style="font-weight:600">${escapeHtml(m.name)}</td>
                            <td>${escapeHtml(getRestName(m.restaurant_id))}</td>
                            <td>₱${parseFloat(m.price).toFixed(2)}</td>
                            <td>${availBtn}</td>
                            <td>
                                <button type="button" class="btn-primary" style="padding:0.25rem 0.75rem; font-size:0.85rem;"
                                    onclick='openEditModal(${JSON.stringify(m)})'>
                                    Edit
                                </button>
                            </td>
                        </tr>`;
                    });
                    html += '</tbody></table>';

                     html += `<div class="pagination-controls" style="margin-top:1rem; display:flex; justify-content:flex-end; gap:0.5rem;">
                        <button class="btn-secondary" onclick="loadMenu(currentPage-1)" ${currentPage<=1?'disabled':''}>Previous</button>
                        <span style="align-self:center;">Page ${currentPage} of ${totalPages}</span>
                        <button class="btn-secondary" onclick="loadMenu(currentPage+1)" ${currentPage>=totalPages?'disabled':''}>Next</button>
                    </div>`;

                    document.getElementById('menuList').innerHTML = html;
                });
        }

        function toggleAvail(id, status) {
            const fd = new FormData();
            fd.append('action', 'toggle_available');
            fd.append('id', id);
            fd.append('status', status);
            fetch(api + '/admin_menu.php', { method: 'POST', body: fd, credentials: 'same-origin' })
                .then(r => r.json())
                .then(d => { if (d.success) loadMenu(currentPage); });
        }

        function openEditModal(m) {
            document.getElementById('menuModalTitle').textContent = 'Edit Menu Item';
            document.getElementById('menu_id').value = m.id;
            document.getElementById('menu_restaurant_id').value = m.restaurant_id;
            document.getElementById('menu_name').value = m.name || '';
            document.getElementById('menu_desc').value = m.description || '';
            document.getElementById('menu_price').value = m.price || '';
            document.getElementById('menu_available').checked = m.is_available == 1;
            document.getElementById('menuModal').style.display = 'flex';
        }

        document.getElementById('addMenuBtn').onclick = () => {
            document.getElementById('menuModalTitle').textContent = 'Add Menu Item';
            document.getElementById('menuForm').reset();
            document.getElementById('menu_id').value = '';
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
                .then(d => { if (d.success) { document.getElementById('menuModal').style.display = 'none'; loadMenu(currentPage); } else alert(d.error || 'Error'); });
        };

        function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    </script>
</body>
</html>
