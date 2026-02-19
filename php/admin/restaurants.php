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
    <link rel="stylesheet" href="../../css/design-system.css">
    <link rel="stylesheet" href="../../css/dashboard.css">
    <link rel="stylesheet" href="../../css/order_food.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/layout/navbar.php'; ?>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php $currentPage = 'admin_restaurants';
include __DIR__ . '/../includes/layout/sidebar.php'; ?>
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
        <?php include __DIR__ . '/../includes/layout/footer.php'; ?>
    </div>
    <script>
        const api = '<?php echo $base; ?>' + '/php/database';
        let currentPage = 1;
        let totalPages = 1;

        function load(page = 1) {
            currentPage = page;
            fetch(api + '/admin_restaurants.php?page=' + page, { credentials: 'same-origin' })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;

                    totalPages = data.pagination?.total_pages || 1;
                    updatePaginationUI();

                    if (!data.restaurants.length) {
                        document.getElementById('restaurantsList').innerHTML = '<div class="empty-state"><p>No restaurants found.</p></div>';
                        return;
                    }

                    let html = '<table class="orders-table"><thead><tr><th>Name</th><th>Description</th><th>Address</th><th>Status</th><th>Actions</th></tr></thead><tbody>';
                    data.restaurants.forEach(r => {
                        const activeBtn = r.is_active == 1
                            ? `<button class="btn-secondary" style="color:var(--success-color); border-color:var(--success-color);" onclick="toggleRest(${r.id}, 0)">Active</button>`
                            : `<button class="btn-secondary" style="opacity:0.6;" onclick="toggleRest(${r.id}, 1)">Inactive</button>`;

                        html += `<tr>
                            <td style="font-weight:600">${escapeHtml(r.name)}</td>
                            <td><div class="muted small">${escapeHtml(r.description || '-')}</div></td>
                            <td>${escapeHtml(r.address || '-')}</td>
                            <td>${activeBtn}</td>
                            <td>
                                <button type="button" class="btn-primary" style="padding:0.25rem 0.75rem; font-size:0.85rem;"
                                    onclick='openEditModal(${JSON.stringify(r)})'>
                                    Edit
                                </button>
                            </td>
                        </tr>`;
                    });
                    html += '</tbody></table>';
                    html += `<div class="pagination-controls" style="margin-top:1rem; display:flex; justify-content:flex-end; gap:0.5rem;">
                        <button class="btn-secondary" onclick="load(currentPage-1)" ${currentPage<=1?'disabled':''}>Previous</button>
                        <span style="align-self:center;">Page ${currentPage} of ${totalPages}</span>
                        <button class="btn-secondary" onclick="load(currentPage+1)" ${currentPage>=totalPages?'disabled':''}>Next</button>
                    </div>`;

                    document.getElementById('restaurantsList').innerHTML = html;
                });
        }

        function updatePaginationUI() {
            // Handled inside HTML generation for simplicity
        }

        function toggleRest(id, status) {
            const fd = new FormData();
            fd.append('action', 'toggle_active');
            fd.append('id', id);
            fd.append('status', status);
            fetch(api + '/admin_restaurants.php', { method: 'POST', body: fd, credentials: 'same-origin' })
                .then(r => r.json())
                .then(d => { if (d.success) load(currentPage); });
        }

        function openEditModal(r) {
            document.getElementById('modalTitle').textContent = 'Edit Restaurant';
            document.getElementById('rest_id').value = r.id;
            document.getElementById('rest_name').value = r.name || '';
            document.getElementById('rest_desc').value = r.description || '';
            document.getElementById('rest_address').value = r.address || '';
            document.getElementById('rest_active').checked = r.is_active == 1;
            document.getElementById('restaurantModal').style.display = 'flex';
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
                .then(d => { if (d.success) { document.getElementById('restaurantModal').style.display = 'none'; load(currentPage); } else alert(d.error || 'Error'); });
        };
        function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }

        load();
    </script>
</body>
</html>
