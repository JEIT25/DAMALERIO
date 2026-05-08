<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';

if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
    $role = $_SESSION['user']['role'] ?? 'consumer';
    $redirect = getBaseUrl() . '/php/auth/dashboard.php';
    if ($role === 'admin') {
        $redirect = getBaseUrl() . '/php/admin/index.php';
    }
    elseif ($role === 'superadmin') {
        $redirect = getBaseUrl() . '/php/superadmin/index.php';
    }
    header('Location: ' . $redirect);
    exit;
}

$login_error = null;
if (isset($_SESSION['login_error'])) {
    $login_error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'blocked_consumer') {
        $login_error = "Your account has been blocked. Please contact admin or superadmin.";
    }
    elseif ($_GET['error'] === 'blocked_admin' || $_GET['error'] === 'blocked') {
        $login_error = "Your account has been blocked. Please contact superadmin.";
    }
}

$lockoutActive = false;
$lockoutTime = 0;
$failedAttempts = 0;
if (isset($_SESSION['lockout_time']) && $_SESSION['lockout_time'] > time()) {
    $lockoutActive = true;
    $lockoutTime = $_SESSION['lockout_time'];
    $failedAttempts = $_SESSION['failed_attempts'] ?? 0;
}
$baseUrl = getBaseUrl();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FoodGrab</title>
    <link rel="stylesheet" href="../../css/serve_asset.php?file=design-system.css">
    <link rel="stylesheet" href="../../css/serve_asset.php?file=login.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <img src="../../images/logo4.png" alt="FoodGrab" class="logo">
            <span class="navbar-text">FoodGrab <span class="navbar-subtext">Online Food Delivery</span></span>
        </div>
        <div class="navbar-right">
            <a href="../forms/homepage.php" class="nav-link" id="home">Home</a>
            <a href="../forms/signup.php" class="nav-link" id="register">Register</a>
        </div>
    </nav>

    <main>
        <div id="validationModal" <?php if ($lockoutActive): ?>data-lockout-active="true" data-lockout-time="<?php echo $lockoutTime; ?>" data-failed-attempts="<?php echo $failedAttempts; ?>"<?php
endif; ?>>
            <div class="modal-content">
                <svg class="error-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#e66868" width="2em" height="2em">
                    <path d="M256 512C397.4 512 512 397.4 512 256C512 114.6 397.4 0 256 0C114.6 0 0 114.6 0 256C0 397.4 114.6 512 256 512zM232 128C232 119.2 239.2 112 248 112H264C272.8 112 280 119.2 280 128V288C280 296.8 272.8 304 264 304H248C239.2 304 232 296.8 232 288V128zM256 384C238.3 384 224 369.7 224 352C224 334.3 238.3 320 256 320C273.7 320 288 334.3 288 352C288 369.7 273.7 384 256 384z"/>
                </svg>
                <div class="text">
                    <span>Too Many Failed Attempts</span>
                    <div id="timer">Try Again in <span id="countdown">0</span> seconds</div>
                </div>
            </div>
        </div>

        <div id="userNotFoundModal" class="modal-simple-alert">
            <div class="modal-simple-alert-content">
                <svg class="error-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#e66868" width="2em" height="2em">
                    <path d="M256 512C397.4 512 512 397.4 512 256C512 114.6 397.4 0 256 0C114.6 0 0 114.6 0 256C0 397.4 114.6 512 256 512zM232 128C232 119.2 239.2 112 248 112H264C272.8 112 280 119.2 280 128V288C280 296.8 272.8 304 264 304H248C239.2 304 232 296.8 232 288V128zM256 384C238.3 384 224 369.7 224 352C224 334.3 238.3 320 256 320C273.7 320 288 334.3 288 352C288 369.7 273.7 384 256 384z"/>
                </svg>
                <span class="modal-simple-alert-text">User ID not found!</span>
                <button id="userNotFoundOkBtn" class="submitBtn">Okay</button>
            </div>
        </div>

        <form class="login-form" id="loginForm" method="POST" action="<?php echo $baseUrl; ?>/php/database/login.php">
            <div class="left-side">
                <img class="form-img" src="../../images/background2.png" alt="Food Delivery">
            </div>
            <div class="right-side">
                <h2>Login</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username">
                        <span id="validationMess" class="userValidationMess"></span>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <div class="password-container">
                            <input type="password" id="password" name="password" placeholder="" autocomplete>
                            <svg id="togglePassword" class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" onclick="togglePassword()" viewBox="0 0 24 24">
                                <path d="M1 12s4.5-8 11-8 11 8 11 8-4.5 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </div>
                        <span id="validationMessPw" class="userValidationMess" style="<?php echo $login_error ? 'display: block;' : ''; ?>"><?php if ($login_error)
    echo htmlspecialchars($login_error); ?></span>
                    </div>
                </div>
                <button type="submit" class="submitBtn">Login</button>
                <a href="#" id="forgotPasswordLink" class="forgot-pw">Forgot Password? Reset Here</a>
            </div>
        </form>
    </main>

    <!-- Forgot Password Modal (OTP flow: Verify ID → Send/Verify OTP → Security Question → New Password) -->
    <div id="forgotPasswordModal" class="modal2" style="display: none">
        <div class="modal2-content">
            <span class="close" id="forgotModalClose">&times;</span>
            <h2>Forgot Password</h2>
            <p id="forgotStepTitle" class="forgot-step-title">Step 1: Verify your User ID</p>
            <div id="forgotStep1" class="forgot-step">
                <label for="reset_id">Enter your User ID:</label>
                <input type="text" id="reset_id" name="reset_id" placeholder="e.g. 12345" autocomplete="username">
                <span id="forgotStep1Msg" class="forgot-msg"></span>
                <button type="button" id="forgotStep1Btn" class="submitBtn">Verify & Continue</button>
            </div>
            <div id="forgotStep2" class="forgot-step" style="display: none;">
                <div class="user-details-box" style="background: rgba(255, 200, 87, 0.15); padding: 12px; border-radius: 8px; margin-bottom: 12px; font-size: 0.9em; text-align: left;">
                    <p style="margin: 4px 0;"><strong>ID:</strong> <span id="step2_display_id"></span></p>
                    <p style="margin: 4px 0;"><strong>Name:</strong> <span id="step2_display_name"></span></p>
                    <p style="margin: 4px 0;"><strong>Email:</strong> <span id="step2_display_email"></span></p>
                </div>
                <p class="forgot-email-hint">We will send a 6-digit code to the email above.</p>
                <button type="button" id="forgotSendOtpBtn" class="submitBtn">Send OTP</button>
                <span id="forgotOtpSentTo" class="forgot-email-sent" style="display: none;"></span>
                <div id="forgotOtpInputWrap" style="display: none;">
                    <label for="forgot_otp">Enter 6-digit code:</label>
                    <input type="password" id="forgot_otp" maxlength="6" pattern="[0-9]*" inputmode="numeric" placeholder="000000">
                    <span id="forgotResendHint" class="forgot-resend-hint"></span>
                    <button type="button" id="forgotResendOtpBtn" class="btn-secondary" disabled>Resend OTP (60s)</button>
                    <button type="button" id="forgotVerifyOtpBtn" class="submitBtn">Verify OTP</button>
                </div>
                <span id="forgotStep2Msg" class="forgot-msg"></span>
            </div>
            <div id="forgotStep3" class="forgot-step" style="display: none;">
                <div class="user-details-box" style="background: rgba(255, 200, 87, 0.15); padding: 12px; border-radius: 8px; margin-bottom: 12px;">
                    <p style="margin: 0;"><strong>User ID:</strong> <span id="display_id"></span> &nbsp; <strong>Username:</strong> <span id="display_username"></span></p>
                </div>
                <div class="form-group" style="text-align: left;">
                    <label id="secure_question_label1" for="secure_answer1" style="display:block; margin-bottom:4px; font-weight:600; font-size: 0.9em;"></label>
                    <div class="password-container">
                        <input type="password" id="secure_answer1" name="secure_answer1" placeholder="Your Answer" style="margin-bottom: 0;">
                        <svg id="toggleSecureAnswer1" class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M1 12s4.5-8 11-8 11 8 11 8-4.5 8-11 8S1 12 1 12z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </div>
                </div>

                <div class="form-group" style="text-align: left;">
                    <label id="secure_question_label2" for="secure_answer2" style="display:block; margin-bottom:4px; font-weight:600; font-size: 0.9em;"></label>
                    <div class="password-container">
                        <input type="password" id="secure_answer2" name="secure_answer2" placeholder="Your Answer" style="margin-bottom: 0;">
                        <svg id="toggleSecureAnswer2" class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M1 12s4.5-8 11-8 11 8 11 8-4.5 8-11 8S1 12 1 12z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </div>
                </div>

                <div class="form-group" style="text-align: left;">
                    <label id="secure_question_label3" for="secure_answer3" style="display:block; margin-bottom:4px; font-weight:600; font-size: 0.9em;"></label>
                    <div class="password-container">
                        <input type="password" id="secure_answer3" name="secure_answer3" placeholder="Your Answer" style="margin-bottom: 0;">
                        <svg id="toggleSecureAnswer3" class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M1 12s4.5-8 11-8 11 8 11 8-4.5 8-11 8S1 12 1 12z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </div>
                </div>

                <span id="forgotStep3Msg" class="forgot-msg"></span>
                <button type="button" id="forgotStep3Btn" class="submitBtn">Verify & Set New Password</button>
            </div>
            <div id="forgotStep4" class="forgot-step" style="display: none;">
                <label for="forgot_new_password">New password (8–25 characters):</label>
                <div class="password-container">
                    <input type="password" id="forgot_new_password" minlength="8" maxlength="25" placeholder="New password" autocomplete="new-password">
                    <svg id="toggleForgotNewPassword" class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4.5-8 11-8 11 8 11 8-4.5 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                </div>
                <span id="forgotPwStrength" class="validation-message" style="display:block; margin-bottom:10px; font-size:0.85em;"></span>

                <label for="forgot_confirm_password">Confirm password:</label>
                <div class="password-container">
                    <input type="password" id="forgot_confirm_password" minlength="8" maxlength="25" placeholder="Confirm" autocomplete="new-password">
                    <svg id="toggleForgotConfirmPassword" class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4.5-8 11-8 11 8 11 8-4.5 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                </div>
                <span id="forgotPwMatch" class="validation-message" style="display:block; margin-bottom:10px; font-size:0.85em;"></span>

                <span id="forgotStep4Msg" class="forgot-msg"></span>
                <button type="button" id="forgotStep4Btn" class="submitBtn">Change Password</button>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-upper">
            <div class="footer-left">
                <div class="logo-area">
                    <img src="../../images/logo4.png" alt="FoodGrab" class="logo">
                    <div class="text">
                        <h3>FoodGrab</h3>
                        <p class="sub-text">Online Food Delivery</p>
                    </div>
                </div>
                <p class="description">We bring the best local flavors right to your door.</p>
            </div>
        </div>
        <div class="footer-bottom"><p>All rights reserved &copy; 2026</p></div>
    </footer>

    <script>
        window.BASE_URL = '<?php echo $baseUrl; ?>';
        window.LOGIN_API = '<?php echo $baseUrl; ?>/php/database/login.php';
        window.CHECK_ID_API = '<?php echo $baseUrl; ?>/php/database/check_id.php';
        window.FORGOT_PASSWORD_API = '<?php echo $baseUrl; ?>/php/database/forgot_password.php';
    </script>
    <script src="../../js/serve_asset.php?file=login.js&v=<?php echo time(); ?>"></script>
</body>
</html>
