<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
requireRole('superadmin');
$pageTitle = 'Login Logs';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - FoodGrab</title>
    <link rel="stylesheet" href="../../css/design-system.css">
    <link rel="stylesheet" href="../../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .filters { display: flex; gap: 1rem; margin-bottom: 1rem; align-items: center; }
        .logs-table { width: 100%; border-collapse: collapse; background: var(--bg-card); border-radius: 8px; overflow: hidden; }
        .logs-table th, .logs-table td { padding: 12px; text-align: left; border-bottom: 1px solid var(--border-color); }
        .logs-table th { background: var(--bg-body); font-weight: 600; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/layout/navbar.php'; ?>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php $currentPage = 'superadmin_logs';
include __DIR__ . '/../includes/layout/sidebar.php'; ?>

        <main class="dashboard-main">
            <h1 class="page-title">Login Logs</h1>

            <div class="filters" style="flex-wrap: wrap;">
                <input type="text" id="filterSearch" class="input-field" placeholder="Search by name or username" style="flex: 1; min-width: 200px;" onkeyup="debounceLoadLogs()">

                <div class="select-wrapper" style="width: 150px;">
                    <select id="filterRole" class="input-field" onchange="loadLogs(1)">
                        <option value="">All Roles</option>
                        <option value="superadmin">Superadmin</option>
                        <option value="admin">Admin</option>
                        <option value="consumer">Consumer</option>
                    </select>
                </div>

                <input type="date" id="filterDate" class="input-field" style="width: auto;" onchange="loadLogs(1)">

                <button class="btn-secondary" onclick="resetFilters()" style="padding: 0.5rem 1rem;">Reset</button>
            </div>

            <div id="logsTableContainer">Loading...</div>

            <div class="pagination-controls">
                <span id="pageInfo" class="pagination-info">Page 1 of 1</span>
                <div style="display: flex; gap: 0.5rem;">
                    <button id="prevBtn" onclick="changePage(-1)" class="btn-secondary" disabled>Previous</button>
                    <button id="nextBtn" onclick="changePage(1)" class="btn-secondary" disabled>Next</button>
                </div>
            </div>
        </main>
        <?php include __DIR__ . '/../includes/layout/footer.php'; ?>
    </div>

    <script>
        const api = '../../php/database';
        let currentPage = 1;
        let totalPages = 1;
        let debounceTimer;

        function debounceLoadLogs() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => loadLogs(1), 300);
        }

        function loadLogs(page = 1) {
            currentPage = page;
            const date = document.getElementById('filterDate').value;
            const search = document.getElementById('filterSearch').value;
            const role = document.getElementById('filterRole').value;

            const params = new URLSearchParams({ date, search, role, page });

            document.getElementById('logsTableContainer').innerHTML = '<p class="muted">Loading...</p>';

            fetch(api + '/superadmin_logs_list.php?' + params)
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;

                    totalPages = data.pagination.total_pages;
                    updatePaginationUI();

                    if (data.logs.length === 0) {
                        document.getElementById('logsTableContainer').innerHTML = '<div class="empty-state"><p>No logs found.</p></div>';
                        return;
                    }

                    let html = '<table class="data-table"><thead><tr><th>User</th><th>Role</th><th>Login Time</th><th>Logout Time</th><th>Duration</th></tr></thead><tbody>';
                    data.logs.forEach(l => {
                        const loginDate = new Date(l.login_time);
                        const logoutDate = l.logout_time ? new Date(l.logout_time) : null;

                        const fmtLogin = loginDate.toLocaleString();
                        const fmtLogout = logoutDate ? logoutDate.toLocaleString() : '<span class="muted">--</span>';

                        let duration = '-';
                        if (logoutDate) {
                            const diffMs = logoutDate - loginDate;
                            const diffMins = Math.floor(diffMs / 60000);
                            const hrs = Math.floor(diffMins / 60);
                            const mins = diffMins % 60;
                            duration = `${hrs}h ${mins}m`;
                        }

                        let roleClass = 'role-consumer';
                        if (l.role === 'admin') roleClass = 'role-admin';
                        if (l.role === 'superadmin') roleClass = 'role-superadmin';

                        html += `<tr>
                            <td>
                                <span class="table-primary-text">${escapeHtml(l.firstName + ' ' + l.lastName)}</span>
                                <span class="table-secondary-text">@${escapeHtml(l.username)}</span>
                            </td>
                            <td><span class="status-badge no-dot ${roleClass}">${l.role}</span></td>
                            <td>${fmtLogin}</td>
                            <td>${fmtLogout}</td>
                            <td>${duration}</td>
                        </tr>`;
                    });
                    html += '</tbody></table>';
                    document.getElementById('logsTableContainer').innerHTML = html;
                });
        }

        function resetFilters() {
            document.getElementById('filterSearch').value = '';
            document.getElementById('filterRole').value = '';
            document.getElementById('filterDate').value = '';
            loadLogs(1);
        }

        function changePage(delta) {
            const newPage = currentPage + delta;
            if (newPage >= 1 && newPage <= totalPages) {
                loadLogs(newPage);
            }
        }

        function updatePaginationUI() {
            document.getElementById('pageInfo').textContent = `Page ${currentPage} of ${totalPages || 1}`;
            document.getElementById('prevBtn').disabled = currentPage <= 1;
            document.getElementById('nextBtn').disabled = currentPage >= totalPages;
        }

        function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }

        loadLogs();
    </script>
</body>
</html>
