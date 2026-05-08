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
</head>
<body>
    <?php include __DIR__ . '/../includes/layout/navbar.php'; ?>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php $currentPage = 'superadmin_logs'; include __DIR__ . '/../includes/layout/sidebar.php'; ?>

        <main class="dashboard-main">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1.5rem;">
                <div>
                    <h1 class="page-title" style="margin:0; font-size: 1.875rem; color: #0f172a;">Login Logs</h1>
                    <p class="page-subtitle" style="margin: 0.5rem 0 0; color: #64748b;">Monitor system access and user sessions.</p>
                </div>
            </div>

            <article class="sa-box" style="margin-bottom: 2rem; background: var(--bg-card); border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm);">
                <header class="sa-box-header" style="margin-bottom: 1.5rem;">
                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; width: 100%;">
                        <input type="text" id="filterSearch" class="input-field" placeholder="Search by name or username" style="flex: 1; min-width: 200px;" onkeyup="debounceLoadLogs()">
                        
                        <select id="filterRole" class="input-field" style="width: auto;" onchange="loadLogs(1)">
                            <option value="">All Roles</option>
                            <option value="superadmin">Superadmin</option>
                            <option value="admin">Admin</option>
                            <option value="consumer">Consumer</option>
                        </select>

                        <input type="date" id="filterDate" class="input-field" style="width: auto;" onchange="loadLogs(1)">

                        <button class="btn-secondary" onclick="resetFilters()">Reset</button>
                    </div>
                </header>

                <div class="sa-box-content no-pad">
                    <div id="logsTableContainer">
                        <div style="padding: 2rem; text-align: center; color: var(--text-muted);">Loading...</div>
                    </div>
                </div>
            </article>

            <div class="pagination-container" style="margin-top: 1.5rem; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 1rem 1.5rem; border-radius: 12px; border: 1px solid #e2e8f0;">
                <span id="logCountBadge" style="background:#dbeafe; color:#1e40af; padding:0.35rem 0.8rem; border-radius:20px; font-weight:700; font-size:0.85rem;">0 Logs</span>
                <div id="paginationControls" style="display: flex; align-items: center; justify-content: flex-end;"></div>
            </div>
        </main>
        <?php include __DIR__ . '/../includes/layout/footer.php'; ?>
    </div>

    <script src="../../js/pagination_util.js"></script>
    <script>
        const api = '../../php/database';
        let currentPage = 1;
        let limit = 10;
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

            const params = new URLSearchParams({ date, search, role, page, limit });

            fetch(api + '/superadmin_logs_list.php?' + params)
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;

                    if (data.logs.length === 0) {
                        document.getElementById('logsTableContainer').innerHTML = '<div style="padding: 3rem; text-align: center; color: #94a3b8;"><p>No login logs found.</p></div>';
                    } else {
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

                            const roleLower = (l.role || 'consumer').toLowerCase();
                            let roleClass = `role-${roleLower}`;

                            html += `<tr>
                                <td>
                                    <div style="font-weight: 500;">${escapeHtml(l.firstName + ' ' + l.lastName)}</div>
                                    <div class="text-muted" style="font-size: 0.8rem;">@${escapeHtml(l.username)}</div>
                                </td>
                                <td><span class="status-badge no-dot ${roleClass}">${(l.role || 'consumer').toUpperCase()}</span></td>
                                <td>${fmtLogin}</td>
                                <td>${fmtLogout}</td>
                                <td style="font-family: monospace; font-weight: 600;">${duration}</td>
                            </tr>`;
                        });
                        html += '</tbody></table>';
                        document.getElementById('logsTableContainer').innerHTML = html;
                    }

                    const badge = document.getElementById('logCountBadge');
                    const total = data.pagination.total_records || 0;
                    badge.textContent = `${total} Log${total !== 1 ? 's' : ''}`;
                    if (total > 0) { badge.style.background = '#dbeafe'; badge.style.color = '#1e40af'; }
                    else { badge.style.background = '#fee2e2'; badge.style.color = '#dc2626'; }

                    window.renderPagination(
                        document.getElementById('paginationControls'),
                        currentPage,
                        data.pagination.total_pages || 1,
                        limit,
                        n => loadLogs(n),
                        l => { limit = l; loadLogs(1); }
                    );
                });
        }

        function resetFilters() {
            document.getElementById('filterSearch').value = '';
            document.getElementById('filterRole').value = '';
            document.getElementById('filterDate').value = '';
            loadLogs(1);
        }

        function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }

        loadLogs();
    </script>
</body>
</html>
