<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
$pageTitle = 'Consumer Management';
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
        .users-table { width: 100%; border-collapse: collapse; background: var(--bg-card); border-radius: 8px; overflow: hidden; }
        .users-table th, .users-table td { padding: 12px; text-align: left; border-bottom: 1px solid var(--border-color); }
        .users-table th { background: var(--bg-body); font-weight: 600; }
        .action-btn { padding: 6px 12px; border-radius: 4px; border: none; cursor: pointer; font-size: 0.85rem; margin-right: 4px; }
        .btn-edit { background: #3b82f6; color: white; }
        .btn-block { background: #ef4444; color: white; }
        .blocked-row { opacity: 0.6; background: #fee2e2; }

        /* Modal Form Styles */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .section-heading { grid-column: 1 / -1; color: var(--primary-color); border-bottom: 2px solid #eee; padding-bottom: 0.5rem; margin-top: 1rem; margin-bottom: 0.5rem; }
        .required { color: red; margin-left: 2px; }
        .hint { font-size: 0.8rem; color: #666; font-weight: normal; }

        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/layout/navbar.php'; ?>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php $currentPage = 'admin_consumers';
include __DIR__ . '/../includes/layout/sidebar.php'; ?>

        <main class="dashboard-main">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h1 class="page-title">Consumer Management</h1>
                <button class="btn-primary" onclick="openUserModal()">Add Consumer</button>
            </div>

            <div id="usersTableContainer">Loading...</div>
        </main>
        <?php include __DIR__ . '/../includes/layout/footer.php'; ?>
    </div>

    <!-- Consumer Modal (Add/Edit) -->
    <div id="consumerModal" class="modal2">
        <div class="modal2-content" style="max-width: 800px; width: 95%;">
             <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                <h2 id="modalTitle">Add Consumer</h2>
                <button onclick="closeConsumerModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
            </div>

            <form id="consumerForm" style="width: 100%; text-align: left; max-height: 80vh; overflow-y: auto; padding-right: 10px;">
                <input type="hidden" name="id" id="consumerId">
                <input type="hidden" name="custom_id" id="customId">

                <h3 class="section-heading">Personal Information</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>First Name<span class="required">*</span></label>
                        <input type="text" name="firstName" id="firstName" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name<span class="required">*</span></label>
                        <input type="text" name="lastName" id="lastName" required>
                    </div>
                    <div class="form-group">
                        <label>Middle Initial</label>
                        <input type="text" name="middleInitial" id="middleInitial">
                    </div>
                    <div class="form-group">
                        <label>Extension</label>
                        <input type="text" name="extension" id="extension">
                    </div>
                    <div class="form-group">
                        <label>Sex<span class="required">*</span></label>
                        <select name="sex" id="sex" required>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Birthdate<span class="required">*</span></label>
                        <input type="date" name="birthdate" id="birthdate" required onchange="calculateAge()">
                    </div>
                    <div class="form-group">
                        <label>Age</label>
                        <input type="number" name="age" id="age" readonly>
                    </div>
                </div>

                <h3 class="section-heading">Address</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Purok</label>
                        <input type="text" name="purok" id="purok">
                    </div>
                    <div class="form-group">
                        <label>Barangay</label>
                        <input type="text" name="barangay" id="barangay">
                    </div>
                    <div class="form-group">
                        <label>City/Municipality</label>
                        <input type="text" name="city" id="city">
                    </div>
                    <div class="form-group">
                        <label>Province</label>
                        <input type="text" name="province" id="province">
                    </div>
                    <div class="form-group">
                        <label>Zip Code</label>
                        <input type="text" name="zipCode" id="zipCode">
                    </div>
                    <div class="form-group">
                        <label>Country</label>
                        <input type="text" name="country" id="country" value="Philippines">
                    </div>
                </div>

                <h3 class="section-heading">Account Details</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Username<span class="required">*</span></label>
                        <input type="text" name="username" id="username" required>
                    </div>
                    <div class="form-group">
                        <label>Email<span class="required">*</span></label>
                        <input type="email" name="email" id="email" required>
                    </div>
                    <div class="form-group">
                        <label>Password <span id="pwHint" class="hint">(Default)</span></label>
                        <input type="password" name="password" id="password" autocomplete="new-password">
                    </div>
                </div>

                <h3 class="section-heading">Security Questions</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Question 1<span class="required">*</span></label>
                        <select name="secure_question" id="sq1" required>
                            <option value="">-- Select --</option>
                            <option value="Who is your bestfriend in elementary?">Who is your bestfriend in elementary?</option>
                            <option value="What is the name of your pet?">What is the name of your pet?</option>
                            <option value="Who is your favorite teacher in highschool?">Who is your favorite teacher in highschool?</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Answer 1<span class="required">*</span></label>
                        <input type="text" name="secure_answer" id="sa1" required>
                    </div>
                    <div class="form-group">
                        <label>Question 2<span class="required">*</span></label>
                        <select name="secure_question2" id="sq2" required>
                            <option value="">-- Select --</option>
                            <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
                            <option value="What elementary school did you attend?">What elementary school did you attend?</option>
                            <option value="What is your favorite food?">What is your favorite food?</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Answer 2<span class="required">*</span></label>
                        <input type="text" name="secure_answer2" id="sa2" required>
                    </div>
                    <div class="form-group">
                        <label>Question 3<span class="required">*</span></label>
                        <select name="secure_question3" id="sq3" required>
                            <option value="">-- Select --</option>
                            <option value="What is your father's middle name?">What is your father's middle name?</option>
                            <option value="What street did you grow up on?">What street did you grow up on?</option>
                            <option value="What is your favorite movie?">What is your favorite movie?</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Answer 3<span class="required">*</span></label>
                        <input type="text" name="secure_answer3" id="sa3" required>
                    </div>
                </div>

                <div style="text-align: right; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #eee;">
                    <button type="button" onclick="closeConsumerModal()" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Save Consumer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Block Request Modal -->
    <div id="blockModal" class="modal2">
        <div class="modal2-content">
            <h2>Request Block</h2>
            <p>Admin approval required to block consumers.</p>
            <form id="blockForm" style="width: 100%; text-align: left;">
                <input type="hidden" name="target_id" id="targetId">
                <div class="form-group">
                    <label>Reason for blocking</label>
                    <textarea name="reason" id="blockReason" rows="3" required></textarea>
                </div>
                <div style="text-align: right; margin-top: 1rem;">
                    <button type="button" onclick="document.getElementById('blockModal').style.display='none'" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const api = '../../php/database';

        function loadUsers() {
            fetch(api + '/admin_consumers_list.php')
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;
                    let html = '<table class="users-table"><thead><tr><th>Name</th><th>Username</th><th>Email</th><th>Status</th><th>Actions</th></tr></thead><tbody>';
                    data.consumers.forEach(u => {
                        const isBlocked = u.is_blocked == 1;
                        html += `<tr class="${isBlocked ? 'blocked-row' : ''}">
                            <td>${escapeHtml(u.firstName + ' ' + u.lastName)}</td>
                            <td>${escapeHtml(u.username)}</td>
                            <td>${escapeHtml(u.email)}</td>
                            <td>${isBlocked ? '<span style="color:red">Blocked</span>' : '<span style="color:green">Active</span>'}</td>
                            <td>
                                <button class="action-btn btn-edit" onclick='editUser(${JSON.stringify(u)})'>Edit</button>
                                ${!isBlocked ? `<button class="action-btn btn-block" onclick="openBlockModal('${u.id}')">Request Block</button>` : ''}
                            </td>
                        </tr>`;
                    });
                    html += '</tbody></table>';
                    document.getElementById('usersTableContainer').innerHTML = html;
                });
        }

        function calculateAge() {
            const birthDate = new Date(document.getElementById('birthdate').value);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            document.getElementById('age').value = age;
        }

        function openUserModal() {
            document.getElementById('modalTitle').textContent = 'Add Consumer';
            document.getElementById('consumerForm').reset();
            document.getElementById('consumerId').value = '';
            document.getElementById('pwHint').textContent = '(Required)';
            document.getElementById('consumerModal').style.display = 'flex';
        }

        function closeConsumerModal() {
            document.getElementById('consumerModal').style.display = 'none';
        }

        function editUser(u) {
            document.getElementById('modalTitle').textContent = 'Edit Consumer';
            document.getElementById('consumerId').value = u.id;
            document.getElementById('customId').value = u.id;

            // Populate fields
            const fields = ['firstName', 'lastName', 'middleInitial', 'extension', 'sex', 'birthdate', 'purok', 'barangay', 'city', 'province', 'zipCode', 'country', 'username', 'email'];
            fields.forEach(f => {
                if (document.getElementById(f)) document.getElementById(f).value = u[f] || '';
            });

            if (u.birthdate) calculateAge();

            document.getElementById('password').value = '';
            document.getElementById('pwHint').textContent = '(Leave blank to keep current)';
            document.getElementById('consumerModal').style.display = 'flex';
        }

        document.getElementById('consumerForm').onsubmit = function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            fetch(api + '/admin_consumer_save.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        closeConsumerModal();
                        loadUsers();
                    } else {
                        alert(d.error);
                    }
                });
        };

        document.getElementById('blockForm').onsubmit = function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            fetch(api + '/admin_request_block.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        document.getElementById('blockModal').style.display = 'none';
                        alert('Request submitted.');
                    } else {
                        alert(d.error);
                    }
                });
        };

        function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }

        loadUsers();
    </script>
</body>
</html>
