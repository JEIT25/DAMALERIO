<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
requireRole('superadmin');
$pageTitle = 'Requests';
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
        .search-filters {
            display: flex;
            gap: 1.25rem;
            flex-wrap: wrap;
            align-items: flex-end;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: var(--bg-card);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
        }
        .search-filters .form-group { flex: 1; min-width: 150px; margin-bottom: 0; }
        .search-filters label { display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; }
        
        @media (max-width: 768px) {
            .search-filters { flex-direction: column; align-items: stretch; }
            .search-filters .form-group { min-width: 100%; }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/layout/navbar.php'; ?>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php $currentPage = 'superadmin_requests'; include __DIR__ . '/../includes/layout/sidebar.php'; ?>

        <main class="dashboard-main">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1.5rem;">
                <div>
                    <h1 class="page-title" style="margin:0; font-size: 1.875rem; color: #0f172a;">Requests</h1>
                    <p class="page-subtitle" style="margin: 0.5rem 0 0; color: #64748b;">Review registration and block/unblock requests.</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="search-filters">
                <div class="form-group">
                    <label>Type</label>
                    <select id="filterType" class="input-field" onchange="loadRequests(1)">
                        <option value="">All Types</option>
                        <option value="registration">Registration</option>
                        <option value="block">Block</option>
                        <option value="unblock">Unblock</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select id="filterStatus" class="input-field" onchange="loadRequests(1)">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" id="startDate" class="input-field" onchange="loadRequests(1)">
                </div>
                <div class="form-group">
                    <label>End Date</label>
                    <input type="date" id="endDate" class="input-field" onchange="loadRequests(1)">
                </div>
                <button class="btn-secondary" style="padding: 0.75rem 1.5rem; height: 50px;" onclick="resetFilters()">Reset</button>
            </div>

            <article class="sa-box" style="margin-bottom: 2rem; background: var(--bg-card); border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm);">
                <div class="sa-box-content no-pad">
                    <div id="requestsTableContainer">
                        <div style="padding: 2rem; text-align: center; color: var(--text-muted);">Loading...</div>
                    </div>
                </div>
            </article>

            <div class="pagination-container" style="margin-top: 1.5rem; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 1rem 1.5rem; border-radius: 12px; border: 1px solid #e2e8f0;">
                <span id="requestCountBadge" style="background:#dbeafe; color:#1e40af; padding:0.35rem 0.8rem; border-radius:20px; font-weight:700; font-size:0.85rem;">0 Requests</span>
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

        function loadRequests(page = 1) {
            currentPage = page;
            const params = new URLSearchParams({
                page,
                limit,
                type: document.getElementById('filterType').value,
                status: document.getElementById('filterStatus').value,
                start_date: document.getElementById('startDate').value,
                end_date: document.getElementById('endDate').value
            });
            fetch(api + '/superadmin_requests_list.php?' + params.toString())
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;
                    if (data.requests.length === 0) {
                        document.getElementById('requestsTableContainer').innerHTML = '<div style="padding: 3rem; text-align: center; color: #94a3b8;"><p>No requests found matching your filters.</p></div>';
                    } else {
                        let html = '<table class="data-table"><thead><tr><th>Requester</th><th>Target User</th><th>Type of Request</th><th>Reason</th><th>Status</th><th>Date</th><th>Action</th></tr></thead><tbody>';
                        data.requests.forEach(r => {
                            const typeLower = r.request_type.toLowerCase();
                            const typeClass = typeLower === 'registration' ? 'status-pending' : (typeLower === 'unblock' ? 'status-ok' : 'status-trash');
                            
                            html += `<tr>
                                <td>
                                    <div style="font-weight: 500;">${escapeHtml(r.r_first + ' ' + r.r_last)}</div>
                                    <div class="text-muted" style="font-size: 0.8rem;">${escapeHtml(r.r_role)}</div>
                                </td>
                                <td>
                                    <div style="font-weight: 600;">${escapeHtml(r.t_first + ' ' + r.t_last)}</div>
                                    <div class="text-muted" style="font-size: 0.8rem; display: flex; align-items: center; gap: 4px;">
                                        <span class="status-badge no-dot role-${(r.t_role || 'consumer').toLowerCase()}" style="padding: 2px 6px; font-size: 0.65rem;">${(r.t_role || 'consumer').toUpperCase()}</span>
                                        @${escapeHtml(r.t_username)}
                                    </div>
                                </td>
                                <td><span class="status-badge no-dot ${typeClass}">${r.request_type.toUpperCase()}</span></td>
                                <td style="max-width: 200px; font-size: 0.85rem; color: #475569;">${escapeHtml(r.reason)}</td>
                                <td><span class="status-badge no-dot ${r.status === 'pending' ? 'status-pending' : r.status === 'approved' ? 'status-ok' : 'status-trash'}">${r.status.toUpperCase()}</span></td>
                                <td class="text-muted">${new Date(r.created_at).toLocaleDateString()}</td>
                                <td>`;

                            if (r.status === 'pending') {
                                html += `<div style="display:flex; gap:0.5rem;">
                                    <button class="btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.75rem; background-color: var(--primary-color); border-color: var(--primary-color);" onclick="handleRequest(${r.id}, 'approve')">Approve</button>
                                    <button class="btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;" onclick="handleRequest(${r.id}, 'reject')">Reject</button>
                                </div>`;
                            } else {
                                html += `<span class="text-muted" style="font-size: 0.8rem; font-style: italic;">Processed</span>`;
                            }

                            html += `</td></tr>`;
                        });
                        html += '</tbody></table>';
                        document.getElementById('requestsTableContainer').innerHTML = html;
                    }

                    const badge = document.getElementById('requestCountBadge');
                    const total = data.pagination.total_requests || 0;
                    badge.textContent = `${total} Request${total !== 1 ? 's' : ''}`;
                    if (total > 0) { badge.style.background = '#dbeafe'; badge.style.color = '#1e40af'; }
                    else { badge.style.background = '#fee2e2'; badge.style.color = '#dc2626'; }

                    window.renderPagination(
                        document.getElementById('paginationControls'),
                        currentPage,
                        data.pagination.total_pages || 1,
                        limit,
                        n => loadRequests(n),
                        l => { limit = l; loadRequests(1); }
                    );
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
                    if (d.success) loadRequests(currentPage);
                    else alert(d.error);
                });
        }

        function resetFilters() {
            document.getElementById('filterType').value = '';
            document.getElementById('filterType').value = '';
            document.getElementById('filterStatus').value = '';
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            loadRequests(1);
        }

        function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }

        loadRequests();
    </script>
</body>
</html>
