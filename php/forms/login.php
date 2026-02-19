<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: ' . (function_exists('getBaseUrl') ? getBaseUrl() : 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/DAMALERIO') . '/php/auth/dashboard.php');
    exit;
}
require_once __DIR__ . '/../includes/auth.php';
header('Location: ' . getBaseUrl() . '/php/auth/login.php');
exit;
?>
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
        <div class="navbar-left" id="navbarLeft">
            <img src="../../images/logo4.png" alt="FoodGrab logo" class="logo">
            <span class="navbar-text">
                FoodGrab
                <span class="navbar-subtext">Online Food Delivery</span>
            </span>
        </div>
        <div class="navbar-right">
            <a href="./homepage.php" class="nav-link" id="home">Home</a>
            <a href="signup.php" class="nav-link" id="register">Registered</a>
        </div>
    </nav>

    <main>
        <div id="validationModal">
            <div class="modal-content">
                <svg class="error-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#e66868"
                    width="2em" height="2em">
                    <path
                        d="M256 512C397.4 512 512 397.4 512 256C512 114.6 397.4 0 256 0C114.6 0 0 114.6 0 256C0 397.4 114.6 512 256 512zM232 128C232 119.2 239.2 112 248 112H264C272.8 112 280 119.2 280 128V288C280 296.8 272.8 304 264 304H248C239.2 304 232 296.8 232 288V128zM256 384C238.3 384 224 369.7 224 352C224 334.3 238.3 320 256 320C273.7 320 288 334.3 288 352C288 369.7 273.7 384 256 384z" />
                </svg>
                <div class="text">
                    <span>Too Many Failed Attempts</span>
                    <div id="timer">Try Again in <span id="countdown"></span> seconds</div>
                </div>
            </div>
        </div>

        <div id="userNotFoundModal" class="modal-simple-alert">
            <div class="modal-simple-alert-content">
                <svg class="error-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#e66868" width="2em" height="2em">
                    <path d="M256 512C397.4 512 512 397.4 512 256C512 114.6 397.4 0 256 0C114.6 0 0 114.6 0 256C0 397.4 114.6 512 256 512zM232 128C232 119.2 239.2 112 248 112H264C272.8 112 280 119.2 280 128V288C280 296.8 272.8 304 264 304H248C239.2 304 232 296.8 232 288V128zM256 384C238.3 384 224 369.7 224 352C224 334.3 238.3 320 256 320C273.7 320 288 334.3 288 352C288 369.7 273.7 384 256 384z" />
                </svg>
                <span class="modal-simple-alert-text">User ID not found!</span>
                <button id="userNotFoundOkBtn" class="submitBtn">Okay</button>
            </div>
        </div>
        <form class="login-form" id="loginForm" method="POST" action="http://localhost/DAMALERIO/php/forms/login.php">
            <div class="left-side">
                <img class="form-img" src="../../images/background2.png" alt="Food Delivery">
            </div>

            <div class="right-side">
                <h2>Login</h2>
                <div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" >
                            <span id="validationMess" class="userValidationMess" ></span>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <div class="password-container">
                                <input type="password" id="password" name="password" placeholder="" autocomplete>
                                <svg id="togglePassword" class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" onclick="togglePassword()"
                                    viewBox="0 0 24 24">
                                    <path d="M1 12s4.5-8 11-8 11 8 11 8-4.5 8-11 8S1 12 1 12z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </div>
                            <span id="validationMessPw" class="userValidationMess" style="<?php echo $login_error ? 'display: block;' : ''; ?>">
    <?php if ($login_error) echo htmlspecialchars($login_error); ?>
</span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="submitBtn">Login</button>

                <a href="#" id="forgotPasswordLink" class="forgot-pw">Forgot Password? Reset
                    Here</a>
            </div>
        </form>
    </main>

    <!-- UPDATED FORGOT PASSWORD MODAL -->
    <div id="forgotPasswordModal" class="modal2" style="display: none">
        <div class="modal2-content">
            <span class="close">&times;</span>
            <h2>Forgot Password</h2>

            <!-- Step 1: Enter ID -->
            <div id="usernameStep">
                <form id="usernameForm" method="POST" action="">
                    <label for="reset_id">Enter your User ID:</label>
                    <input type="text" id="reset_id" name="reset_id" required>
                    <button type="submit" class="submitBtn">Next</button>
                </form>
            </div>

            <!-- Step 2: Confirm & Answer -->
            <div id="securityQuestionStep" style="display: none;">
                <form id="securityQuestionForm" method="POST" action="handle_forgot_password.php">

                    <!-- New: Display ID and Username (Styled for Happy Paws) -->
                    <div class="user-details-box" style="background: rgba(255, 200, 87, 0.15); padding: 12px; border-radius: 8px; margin-bottom: 16px; border: 1px solid var(--color-muted);">
                        <p style="margin-bottom: 4px; color: var(--color-heading);"><strong>User ID:</strong> <span id="display_id" style="color: var(--color-accent); font-weight: bold;"></span></p>
                        <p style="margin-bottom: 0; color: var(--color-heading);"><strong>Username:</strong> <span id="display_username" style="color: var(--color-accent); font-weight: bold;"></span></p>
                    </div>

                    <label id="secure_question_label" style="display:block; margin-bottom:8px; font-weight:600;"></label>
                    <input type="password" id="secure_answer" name="secure_answer" required placeholder="Enter your answer">

                    <!-- Hidden field to pass the ID to the final handler -->
                    <input type="hidden" id="hidden_user_id" name="user_id">

                    <button type="submit" class="submitBtn">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-upper">
            <div class="footer-left">
                <div class="logo-area">
                    <img src="../../images/logo4.png" alt="FoodGrab Icon" class="logo">
                    <div class="text">
                        <h3>FoodGrab</h3>
                        <p class="sub-text">Online Food Delivery</p>
                    </div>
                </div>
                <p class="description">We bring the best local flavors right to your door with fast, reliable delivery and a smile.</p>
            </div>

            <!-- <div class="footer-right">
                <div class="wrapper">
                    <h4>Partners</h4>
                    <p>Partner with us</p>
                    <p>Ride with us</p>
                </div>
            </div> -->
        </div>

        <div class="footer-bottom">
            <p>All rights reserved &copy; 2026</p>
        </div>
    </footer>

    <script src="../../js/serve_asset.php?file=login.js"></script>
</body>

</html>