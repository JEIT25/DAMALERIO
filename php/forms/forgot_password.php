<?php
/**
 * Forgot Password - Step 1: ID Verification
 * Shows user information and allows OTP sending
 */
session_start();
require_once '../database/db_connect.php';

$error = '';
$success = '';
$user_data = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_id'])) {
    $user_id = trim($_POST['user_id']);

    if (empty($user_id)) {
        $error = 'Please enter your ID number';
    }
    else {
        // Verify user exists and get details, checking if account is approved
        $stmt = $conn->prepare("SELECT id, firstName, lastName, email, status FROM users WHERE id = ?");
        $stmt->bind_param('s', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user_data = $result->fetch_assoc();
            
            // --- NEW: Block forgot password for pending/rejected registrations ---
            if ($user_data['status'] === 'pending') {
                $error = 'Your account is still pending approval. You cannot reset your password yet.';
                $user_data = null;
            } elseif ($user_data['status'] === 'rejected') {
                $error = 'Your registration request was rejected. Please contact support.';
                $user_data = null;
            } else {
                $_SESSION['reset_user_id'] = $user_id;
            }
        }
        else {
            $error = 'ID not found in our system';
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/serve_asset.php?file=design-system.css">
    <link rel="stylesheet" href="../../css/serve_asset.php?file=login.css">
    <title>Forgot Password - FoodGrab</title>
</head>

<body>
    <nav class="navbar">
        <div class="navbar-left" id="navbarLeft">
            <img src="../../images/logo4.png" alt="FoodGrab logo" class="logo">
            <span class="navbar-text">
                FoodGrab
                <span class="navbar-subtext">Online Food Delivery</span>
            </span>
        </div>
        <div class="navbar-right">
            <a href="./login.php" class="nav-link">Back to Login</a>
        </div>
    </nav>

    <main>
        <div class="form-container">
            <h2>Forgot Password</h2>

            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php
endif; ?>

            <?php if (!$user_data): ?>
                <!-- Step 1: ID Verification -->
                <form method="POST" class="forgot-form">
                    <p>Enter your ID number to verify your identity</p>

                    <div class="form-group">
                        <label for="user_id">ID Number:</label>
                        <input type="text" id="user_id" name="user_id" required
                               placeholder="Enter your ID number (e.g., 2021-0909)">
                    </div>

                    <button type="submit" name="verify_id" class="btn-primary">Verify Identity</button>
                </form>
            <?php
else: ?>
                <!-- Step 2: User Information Display -->
                <div class="user-info">
                    <h3>Confirm Identity</h3>
                    <div class="info-card">
                        <div class="info-row">
                            <span class="label">ID Number:</span>
                            <span class="value"><?php echo htmlspecialchars($user_data['id']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="label">Full Name:</span>
                            <span class="value"><?php echo htmlspecialchars($user_data['firstName'] . ' ' . $user_data['lastName']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="label">Email:</span>
                            <span class="value"><?php echo htmlspecialchars($user_data['email']); ?></span>
                        </div>
                    </div>

                    <p class="info-text">Please confirm that you want to send the verification code to this email address.</p>

                    <form method="POST" action="forgot_password_send_otp.php" class="forgot-form" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').innerText='Sending...';">
                        <button type="submit" class="btn-primary">Confirm & Send OTP</button>
                        <a href="forgot_password.php" class="btn-secondary" style="display: block; text-align: center; margin-top: 10px; text-decoration: none;">Cancel</a>
                    </form>
                </div>
            <?php
endif; ?>
        </div>
    </main>

    <footer>
        <div class="footer-bottom">
            <p>All rights reserved &copy; 2026</p>
        </div>
    </footer>
</body>
</html>
