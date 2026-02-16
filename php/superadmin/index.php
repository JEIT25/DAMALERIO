<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
requireRole('superadmin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout_action'])) {
    session_unset();
    session_destroy();
    header('Location: ' . getBaseUrl() . '/php/forms/login.php');
    exit;
}

$base = getBaseUrl();
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin - FoodGrab</title>
    <link rel="stylesheet" href="../../css/serve_asset.php?file=design-system.css">
    <link rel="stylesheet" href="../../css/serve_asset.php?file=dashboard.css">
    <link rel="stylesheet" href="../../css/serve_asset.php?file=order_history.css">
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <div class="navbar-left">
                <img src="../../images/logo4.png" alt="Logo" class="logo">
                <span class="navbar-text">FoodGrab <span class="navbar-subtext">Superadmin</span></span>
            </div>
            <div class="navbar-right">
                <a href="../admin/index.php" class="nav-link">Admin Panel</a>
                <form action="" method="POST" style="display:inline;">
                    <input type="hidden" name="logout_action" value="1">
                    <button type="submit" class="nav-link">Log Out</button>
                </form>
            </div>
        </header>
        <aside class="dashboard-sidebar">
            <div class="profile-section">
                <p><strong><?php echo htmlspecialchars($user['firstName'] . ' ' . $user['lastName']); ?></strong></p>
                <p class="muted small">Superadmin</p>
            </div>
            <nav class="sidebar-menu">
                <a href="index.php" class="active">Users & Roles</a>
                <a href="../admin/index.php">Admin Panel</a>
            </nav>
        </aside>
        <main class="dashboard-main">
            <h1>Users & Roles</h1>
            <p class="muted">Change user roles: consumer, admin, or superadmin.</p>
            <div id="usersTable"></div>
        </main>
        <footer class="dashboard-footer"><div class="footer-bottom"><p>&copy; 2025</p></div></footer>
    </div>
    <script>
        const api = '<?php echo $base; ?>' + '/php/database';
        fetch(api + '/superadmin_users_list.php', { credentials: 'same-origin' })
            .then(r => r.json())
            .then(data => {
                if (!data.success || !data.users.length) {
                    document.getElementById('usersTable').innerHTML = '<p class="muted">No users.</p>';
                    return;
                }
                let html = '<table class="orders-table"><thead><tr><th>ID</th><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Change role</th></tr></thead><tbody>';
                data.users.forEach(u => {
                    html += `<tr>
                        <td>${escapeHtml(u.id)}</td>
                        <td>${escapeHtml(u.firstName + ' ' + u.lastName)}</td>
                        <td>${escapeHtml(u.username)}</td>
                        <td>${escapeHtml(u.email)}</td>
                        <td><span class="status-badge">${u.role}</span></td>
                        <td><select class="role-select" data-user-id="${escapeAttr(u.id)}">${['consumer','admin','superadmin'].map(r => '<option value="'+r+'"'+(r===u.role?' selected':'')+'>'+r+'</option>').join('')}</select></td>
                    </tr>`;
                });
                html += '</tbody></table>';
                document.getElementById('usersTable').innerHTML = html;
                document.querySelectorAll('.role-select').forEach(sel => {
                    sel.addEventListener('change', () => {
                        const fd = new FormData();
                        fd.append('user_id', sel.dataset.userId);
                        fd.append('role', sel.value);
                        fetch(api + '/superadmin_user_role.php', { method: 'POST', body: fd, credentials: 'same-origin' })
                            .then(r => r.json())
                            .then(d => { if (d.success) { const row = sel.closest('tr'); row.querySelector('.status-badge').textContent = sel.value; } else alert(d.error || 'Error'); });
                    });
                });
            })
            .catch(() => document.getElementById('usersTable').innerHTML = '<p class="muted">Error loading users.</p>');
        function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
        function escapeAttr(s) { return escapeHtml(s).replace(/"/g, '&quot;'); }
    </script>
</body>
</html>
