<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
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
</head>
<body>
    <?php include __DIR__ . '/../includes/layout/navbar.php'; ?>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php $currentPage = 'admin_requests'; include __DIR__ . '/../includes/layout/sidebar.php'; ?>

        <main class="dashboard-main">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1.5rem;">
                <div>
                    <h1 class="page-title" style="margin:0; font-size: 1.875rem; color: #0f172a;">Requests Manager</h1>
                    <p class="page-subtitle" style="margin: 0.5rem 0 0; color: #64748b;">Manage consumer registrations and monitor your block requests.</p>
                </div>
            </div>

            <article class="sa-box" style="margin-bottom: 2rem; background: var(--bg-card); border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm);">
                <header class="sa-box-header" style="margin-bottom: 1.5rem;">
                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; width: 100%;">
                        <input type="text" id="searchInput" class="input-field" placeholder="Search targets by name, username, or email..." style="flex: 1; min-width: 200px;" onkeyup="handleSearch(event)">
                        
                        <select id="filterStatus" class="input-field" style="width: auto;" onchange="loadRequests(1)">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>

                        <select id="filterType" class="input-field" style="width: auto;" onchange="loadRequests(1)">
                            <option value="">All Types</option>
                            <option value="registration">Registration</option>
                            <option value="block">Block</option>
                            <option value="unblock">Unblock</option>
                        </select>
                        
                        <div style="display: flex; align-items: center; gap: 0.5rem; background: #f8fafc; padding: 0.25rem 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <label for="startDate" style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin: 0;">FROM:</label>
                            <input type="date" id="startDate" class="input-field" style="border: none; background: transparent; padding: 0.25rem; font-size: 0.85rem;" onchange="loadRequests(1)">
                            <label for="endDate" style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin: 0; margin-left: 0.5rem;">TO:</label>
                            <input type="date" id="endDate" class="input-field" style="border: none; background: transparent; padding: 0.25rem; font-size: 0.85rem;" onchange="loadRequests(1)">
                        </div>

                        <button class="btn-secondary" onclick="resetFilters()">Reset</button>
                    </div>
                </header>

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
        let currentSearch = '';
        let currentStatus = '';
        let currentType = '';
        let currentStartDate = '';
        let currentEndDate = '';

        function loadRequests(page = 1) {
            currentPage = page;
            currentStatus = document.getElementById('filterStatus').value;
            currentType = document.getElementById('filterType').value;
            currentStartDate = document.getElementById('startDate').value;
            currentEndDate = document.getElementById('endDate').value;

            const params = new URLSearchParams({ 
                page, 
                limit, 
                search: currentSearch, 
                status: currentStatus,
                type: currentType,
                start_date: currentStartDate,
                end_date: currentEndDate
            });
            fetch(api + '/admin_requests_list.php?' + params.toString())
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;

                    if (data.requests.length === 0) {
                        document.getElementById('requestsTableContainer').innerHTML = '<div style="padding: 3rem; text-align: center; color: #94a3b8;"><p>No requests found matching your criteria.</p></div>';
                    } else {
                        let html = '<table class="data-table"><thead><tr><th>Target User</th><th>Type of Request</th><th>Reason</th><th>Date</th><th>Status</th><th>Notes</th><th>Process</th></tr></thead><tbody>';
                        data.requests.forEach(r => {
                            const typeLower = (r.request_type || 'registration').toLowerCase();
                            const typeStyle = typeLower === 'registration' ? 'background: #dbeafe; color: #1e40af;' :
                                              typeLower === 'unblock' ? 'background: #dcfce7; color: #166534;' :
                                              'background: #fee2e2; color: #991b1b;';
                            
                            let statusClass = 'status-pending';
                            if (r.status === 'approved') statusClass = 'status-ok';
                            if (r.status === 'rejected') statusClass = 'status-trash';

                            html += `<tr>
                                <td>
                                    <div style="font-weight: 600; color: #1e293b;">${escapeHtml(r.firstName + ' ' + r.lastName)}</div>
                                    <div class="text-muted" style="font-size: 0.8rem;">@${escapeHtml(r.username)}</div>
                                </td>
                                <td>
                                    <span class="status-badge no-dot" style="font-size: 0.7rem; font-weight: 700; ${typeStyle}">
                                        ${typeLower.toUpperCase()}
                                    </span>
                                </td>
                                <td style="max-width: 250px; font-size: 0.85rem; color: #475569;">${escapeHtml(r.reason)}</td>
                                <td class="text-muted" style="font-size: 0.85rem;">${new Date(r.created_at).toLocaleDateString()}</td>
                                <td><span class="status-badge no-dot ${statusClass}">${r.status.toUpperCase()}</span></td>
                                <td class="text-muted small" style="max-width: 150px;">${escapeHtml(r.review_notes || '-')}</td>
                                <td>`;

                            if (typeLower === 'registration' && r.status === 'pending') {
                                html += `<div style="display:flex; gap:0.5rem;">
                                    <button class="btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.75rem; background: #10b981; border-color: #10b981;" onclick="handleRequest(${r.id}, 'approve')">Approve</button>
                                    <button class="btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.75rem; color: #ef4444; border-color: #ef4444;" onclick="handleRequest(${r.id}, 'reject')">Reject</button>
                                </div>`;
                            } else {
                                html += `<span class="text-muted" style="font-size: 0.8rem; font-style: italic;">${r.status === 'pending' ? 'Pending SPADM' : 'Processed'}</span>`;
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

        let searchTimeout;
        function handleSearch(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentSearch = e.target.value;
                loadRequests(1);
            }, 300);
        }

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('filterStatus').value = '';
            document.getElementById('filterType').value = '';
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            currentSearch = '';
            currentStatus = '';
            currentType = '';
            currentStartDate = '';
            currentEndDate = '';
            loadRequests(1);
        }

        function handleRequest(id, action) {
            if (!confirm(`Are you sure you want to ${action} this registration?`)) return;
            const fd = new FormData();
            fd.append('request_id', id);
            fd.append('action', action);
            fetch(api + '/admin_request_action.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => {
                    if (d.success) loadRequests(currentPage);
                    else alert(d.error);
                });
        }

        function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }

        loadRequests();
    </script>
</body>
</html>
