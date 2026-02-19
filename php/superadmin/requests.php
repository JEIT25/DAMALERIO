<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
requireRole('superadmin');
$pageTitle = 'Block Requests';
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
        .requests-table { width: 100%; border-collapse: collapse; background: var(--bg-card); border-radius: 8px; overflow: hidden; }
        .requests-table th, .requests-table td { padding: 12px; text-align: left; border-bottom: 1px solid var(--border-color); }
        .requests-table th { background: var(--bg-body); font-weight: 600; }
        .action-container { display: flex; gap: 8px; }
        .btn-approve { background: #ef4444; color: white; padding: 6px 12px; border-radius: 4px; border: none; cursor: pointer; }
        .btn-reject { background: #6b7280; color: white; padding: 6px 12px; border-radius: 4px; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/layout/navbar.php'; ?>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php $currentPage = 'superadmin_requests';
include __DIR__ . '/../includes/layout/sidebar.php'; ?>

        <main class="dashboard-main">
            <h1 class="page-title">Block Requests</h1>
            <p>Review requests from Admins to block Consuemrs.</p>

            <div id="requestsTableContainer">Loading...</div>
        </main>
        <?php include __DIR__ . '/../includes/layout/footer.php'; ?>
    </div>

    <script>
        const api = '../../php/database';

        function loadRequests() {
            fetch(api + '/superadmin_requests_list.php')
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;
                    if (data.requests.length === 0) {
                        document.getElementById('requestsTableContainer').innerHTML = '<div class="empty-state"><p>No pending requests.</p></div>';
                        return;
                    }
                    let html = '<table class="data-table"><thead><tr><th>Requester (Admin)</th><th>Target (Consumer)</th><th>Reason</th><th>Status</th><th>Date</th><th>Action</th></tr></thead><tbody>';
                    data.requests.forEach(r => {
                        html += `<tr>
                            <td>${escapeHtml(r.r_first + ' ' + r.r_last)}</td>
                            <td>${escapeHtml(r.t_first + ' ' + r.t_last)} <span class="muted">(${r.t_username})</span></td>
                            <td>${escapeHtml(r.reason)}</td>
                            <td><span class="status-badge status-${r.status}">${r.status}</span></td>
                            <td>${new Date(r.created_at).toLocaleDateString()}</td>
                            <td>`;

                        if (r.status === 'pending') {
                            html += `<div class="action-container" style="display:flex; gap:0.5rem;">
                                <button class="btn-primary" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; background-color: var(--error-color);" onclick="handleRequest(${r.id}, 'approve')">Block</button>
                                <button class="btn-secondary" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;" onclick="handleRequest(${r.id}, 'reject')">Reject</button>
                            </div>`;
                        } else {
                            const label = r.status === 'approved' ? 'Approved' : 'Rejected';
                            const color = r.status === 'approved' ? '#dc2626' : '#6b7280';
                            html += `<span style="font-weight:600; color:${color};">${label}</span>`;
                        }

                        html += `</td></tr>`;
                    });
                    html += '</tbody></table>';
                    document.getElementById('requestsTableContainer').innerHTML = html;
                });
        }

        function handleRequest(id, action) {
            if (!confirm(`Are you sure you want to ${action} this request?`)) return;
            const fd = new FormData();
            fd.append('request_id', id);
            fd.append('action', action);
            fetch(api + '/superadmin_request_action.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => {
                    if (d.success) loadRequests();
                    else alert(d.error);
                });
        }

        function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }

        loadRequests();
    </script>
</body>
</html>
