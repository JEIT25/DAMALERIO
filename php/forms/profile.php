<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$user = $_SESSION['user'];
$pageTitle = 'My Profile';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - FoodGrab</title>
    <link rel="stylesheet" href="../../css/design-system.css">
    <link rel="stylesheet" href="../../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-container { max-width: 900px; margin: 2rem auto; display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; }
        .profile-card { background: var(--bg-card); padding: 2rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); }
        .form-section { margin-bottom: 2rem; }
        .form-section h3 { border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1rem; color: var(--primary-color); }
        .info-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; }
        label { font-weight: 600; display: block; margin-bottom: 0.25rem; }
        input { width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/layout/navbar.php'; ?>
    <div class="dashboard-container" style="padding-top: 80px;">
        <!-- Dynamic Sidebar -->
        <!-- Dynamic Sidebar -->
        <?php $currentPage = 'profile';
include __DIR__ . '/../includes/layout/sidebar.php'; ?>

        <main class="dashboard-main">
            <div class="profile-container">
            <!-- Sidebar / Identity -->
            <div class="profile-card" style="text-align: center;">
                <div style="width: 100px; height: 100px; background: #eee; border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: #aaa;">
                    <i class="fa-solid fa-user"></i>
                </div>
                <h2><?php echo htmlspecialchars($user['firstName'] . ' ' . $user['lastName']); ?></h2>
                <p class="muted">@<?php echo htmlspecialchars($user['username']); ?></p>
                <p class="status-badge"><?php echo ucfirst($user['role']); ?></p>
            </div>

            <!-- Details Form -->
            <div class="profile-card">
                <form id="profileForm">
                    <div class="form-section">
                        <h3>Personal Information</h3>
                        <div class="info-row">
                            <div><label>First Name</label><input type="text" value="<?php echo htmlspecialchars($user['firstName']); ?>" readonly></div>
                            <div><label>Last Name</label><input type="text" value="<?php echo htmlspecialchars($user['lastName']); ?>" readonly></div>
                        </div>
                        <div class="info-row">
                            <div><label>Email</label><input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly></div>
                            <div><label>Phone/Mobile</label><input type="text" placeholder="N/A"></div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Address</h3>
                        <div class="info-row">
                            <div><label>Purok</label><input type="text" value="<?php echo htmlspecialchars($user['purok']); ?>" readonly></div>
                            <div><label>Barangay</label><input type="text" value="<?php echo htmlspecialchars($user['barangay']); ?>" readonly></div>
                        </div>
                        <div class="info-row">
                            <div><label>City</label><input type="text" value="<?php echo htmlspecialchars($user['city']); ?>" readonly></div>
                            <div><label>Province</label><input type="text" value="<?php echo htmlspecialchars($user['province']); ?>" readonly></div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Security Credentials</h3>
                        <div class="info-group" style="margin-bottom: 1rem;">
                            <label>Current Password</label>
                            <input type="password" name="current_password" required placeholder="To set new password">
                        </div>
                        <div class="info-group" style="margin-bottom: 1rem;">
                            <label>New Password</label>
                            <input type="password" name="new_password" id="new_password" placeholder="Min 8 characters">
                        </div>
                        <div class="info-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password">
                        </div>
                        <p id="pwMsg" style="color: red; font-size: 0.9em; margin-top: 0.5rem;"></p>
                    </div>

                    <div style="text-align: right;">
                        <button type="submit" class="btn-primary">Update Password</button>
                    </div>
                </form>
            </div>
            </div>
        </main>
    </div>

    <script>
        const api = '../../php/database';

        document.getElementById('profileForm').onsubmit = function(e) {
            e.preventDefault();
            const np = document.getElementById('new_password').value;
            const cp = document.getElementById('confirm_password').value;
            const msg = document.getElementById('pwMsg');

            if (!np) {
                msg.textContent = "Please enter a new password.";
                return;
            }
            if (np !== cp) {
                msg.textContent = "New passwords do not match.";
                return;
            }

            const fd = new FormData(this);
            fetch(api + '/update_password.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        alert('Password updated successfully.');
                        this.reset();
                        msg.textContent = "";
                    } else {
                        msg.textContent = d.error || 'Update failed.';
                    }
                });
        };
    </script>
</body>
</html>
