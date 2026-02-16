(function() {
    window.BASE_URL = window.BASE_URL || 'http://localhost/DAMALERIO';
    window.LOGIN_API = window.LOGIN_API || window.BASE_URL + '/php/database/login.php';
    window.CHECK_ID_API = window.CHECK_ID_API || window.BASE_URL + '/php/database/check_id.php';
})();
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('loginForm');
    const modal = document.getElementById('validationModal');
    const validationMessage = document.getElementById('validationMess');
    const countdownElement = document.getElementById('countdown');
    const registerLink = document.getElementById('register');
    const homeLink = document.getElementById('home');
    const toggleIcon = document.getElementById('togglePassword');
    const forgotPwLink = document.querySelector('.forgot-pw');

    // *** NEW MODAL ELEMENTS ***
    const userNotFoundModal = document.getElementById('userNotFoundModal');
    const userNotFoundOkBtn = document.getElementById('userNotFoundOkBtn');

    // Access control check (Lockout logic)
    async function updateRegisterAccess(isRestricted) {
        try {
            const formData = new FormData();
            formData.append('isForm', false);
            formData.append('isRegisterRestrict', isRestricted);

            const response = await fetch(window.LOGIN_API, {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                console.error('Failed to update access');
            }
        } catch (error) {
            console.error('Error updating access:', error);
        }
    }

    function showModal(failedAttempts, lockoutTime) {
        localStorage.setItem('failedAttempts', failedAttempts);
        toggleForgotPwLink(failedAttempts);

        if (lockoutTime > Date.now() / 1000) {
            const remainingTime = lockoutTime - Math.floor(Date.now() / 1000);
            countdownElement.textContent = remainingTime;
            modal.style.display = 'flex';
            disableFormAndRegister();
            window.addEventListener('pointermove', disableBackButton);
            localStorage.setItem('lockoutTime', lockoutTime);

            const interval = setInterval(() => {
                const newTime = lockoutTime - Math.floor(Date.now() / 1000);
                countdownElement.textContent = newTime;
                if (newTime <= 0) {
                    clearInterval(interval);
                    modal.style.display = 'none';
                    enableFormAndRegister();
                    localStorage.removeItem('lockoutTime');
                    window.removeEventListener('pointermove', disableBackButton);
                }
            }, 1000);
        }
    }

    function toggleForgotPwLink(failedAttempts) {
        if (failedAttempts >= 2) {
            forgotPwLink.style.display = 'block';
        } else {
            forgotPwLink.style.display = 'none';
        }
    }

    function enableFormAndRegister() {
        updateRegisterAccess(false);
        form.querySelectorAll('input, button').forEach((element) => {
            element.disabled = false;
            if (element.type === 'submit') {
                element.classList.remove('submitBtnDisabled');
                element.classList.add('submitBtn');
            }
        });
        registerLink.style.pointerEvents = 'auto';
        registerLink.setAttribute('href', '../forms/signup.php');
        registerLink.classList.remove('disabled-link');
        registerLink.classList.add('nav-link');
        homeLink.style.pointerEvents = 'auto';
        homeLink.setAttribute('href', '../forms/homepage.php');
        homeLink.classList.remove('disabled-link');
        homeLink.classList.add('nav-link');
        forgotPwLink.setAttribute('href', '#');
        forgotPwLink.style.pointerEvents = 'auto';
        forgotPwLink.classList.remove('disabled-link');
        forgotPwLink.classList.add('forgot-pw');
    }

    const disableBackButton = () => {
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };
    };

    function disableFormAndRegister() {
        updateRegisterAccess(true);
        form.querySelectorAll('input, button').forEach((element) => {
            element.disabled = true;
            if (element.type === 'submit') {
                element.classList.remove('submitBtn');
                element.classList.add('submitBtnDisabled');
            }
        });
        registerLink.style.pointerEvents = 'none';
        registerLink.classList.remove('nav-link');
        registerLink.classList.add('disabled-link');
        registerLink.removeAttribute('href');
        homeLink.style.pointerEvents = 'none';
        homeLink.classList.remove('nav-link');
        homeLink.classList.add('disabled-link');
        homeLink.removeAttribute('href');
        forgotPwLink.removeAttribute('href');
        forgotPwLink.style.pointerEvents = 'none';
        forgotPwLink.classList.remove('forgot-pw');
        forgotPwLink.classList.add('disabled-link');
    }

    window.togglePassword = function () {
        const passwordInput = document.getElementById('password');
        const isPasswordVisible = passwordInput.type === 'text';
        passwordInput.type = isPasswordVisible ? 'password' : 'text';
        toggleIcon.style.fill = isPasswordVisible ? 'none' : 'currentColor';
    }

    // Login Form Submit
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const usernameValidationMessage = document.getElementById('validationMess');
        const passwordValidationMessage = document.getElementById('validationMessPw');

        usernameValidationMessage.innerText = '';
        passwordValidationMessage.innerText = '';

        const formData = new FormData(form);
        formData.append('isForm', true);
        formData.append('isRegisterRestrict', false);

        const response = await fetch(window.LOGIN_API, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        });

        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Login response not JSON:', text);
            passwordValidationMessage.style.display = 'block';
            passwordValidationMessage.innerText = 'Server error. Please try again.';
            return;
        }

        if (data.requireUsername) {
            usernameValidationMessage.style.display = 'block';
            usernameValidationMessage.innerText = data.requireUsername;
            showModal(data.failed_attempts, data.lockout_time);
        }
        if (data.requirePw) {
            passwordValidationMessage.style.display = 'block';
            passwordValidationMessage.innerText = data.requirePw;
            showModal(data.failed_attempts, data.lockout_time);
        }
        if (data.error) {
            passwordValidationMessage.style.display = 'block';
            passwordValidationMessage.innerText = data.error;
            showModal(data.failed_attempts, data.lockout_time);
        }
        if (data.redirect) {
            localStorage.clear();
            window.location.href = data.redirect;
        }
    });

    // Page Load Init
    const storedLockoutTime = localStorage.getItem('lockoutTime');
    const storedFailedAttempts = localStorage.getItem('failedAttempts');
    if (storedLockoutTime) {
        showModal(parseInt(storedFailedAttempts || 0, 10), parseInt(storedLockoutTime, 10));
    }
    toggleForgotPwLink(parseInt(storedFailedAttempts || 0, 10));

    // --- FORGOT PASSWORD LOGIC (OTP flow when FORGOT_PASSWORD_API is set) ---
    var modal2 = document.getElementById("forgotPasswordModal");
    var forgotPasswordLink = document.getElementById("forgotPasswordLink");
    var resetIdInput = document.getElementById("reset_id");
    var displayIdSpan = document.getElementById("display_id");
    var displayUsernameSpan = document.getElementById("display_username");
    var secureQuestionLabel = document.getElementById("secure_question_label");

    if (userNotFoundOkBtn) {
        userNotFoundOkBtn.onclick = function () {
            userNotFoundModal.style.display = 'none';
        };
    }

    var closeModalEl = document.getElementById("forgotModalClose") || document.querySelector("#forgotPasswordModal .close");
    if (closeModalEl) closeModalEl.onclick = function () { modal2.style.display = "none"; };
    window.onclick = function (event) {
        if (event.target === modal2) modal2.style.display = "none";
    };

    if (window.FORGOT_PASSWORD_API && document.getElementById("forgotStep1")) {
        var step1 = document.getElementById("forgotStep1");
        var step2 = document.getElementById("forgotStep2");
        var step3 = document.getElementById("forgotStep3");
        var step4 = document.getElementById("forgotStep4");
        var stepTitle = document.getElementById("forgotStepTitle");
        var step1Msg = document.getElementById("forgotStep1Msg");
        var step2Msg = document.getElementById("forgotStep2Msg");
        var step3Msg = document.getElementById("forgotStep3Msg");
        var step4Msg = document.getElementById("forgotStep4Msg");
        var sendOtpBtn = document.getElementById("forgotSendOtpBtn");
        var otpSentTo = document.getElementById("forgotOtpSentTo");
        var otpInputWrap = document.getElementById("forgotOtpInputWrap");
        var forgotOtpInput = document.getElementById("forgot_otp");
        var resendHint = document.getElementById("forgotResendHint");
        var resendOtpBtn = document.getElementById("forgotResendOtpBtn");
        var verifyOtpBtn = document.getElementById("forgotVerifyOtpBtn");
        var step3Btn = document.getElementById("forgotStep3Btn");
        var step4Btn = document.getElementById("forgotStep4Btn");
        var newPwInput = document.getElementById("forgot_new_password");
        var confirmPwInput = document.getElementById("forgot_confirm_password");
        var resendCountdown = null;

        function showStep(n) {
            [step1, step2, step3, step4].forEach(function (s) { if (s) s.style.display = "none"; });
            step1Msg.textContent = ""; step2Msg.textContent = ""; step3Msg.textContent = ""; step4Msg.textContent = "";
            var titles = ["Step 1: Verify your User ID", "Step 2: Get and enter OTP", "Step 3: Security question", "Step 4: Set new password"];
            if (stepTitle) stepTitle.textContent = titles[n - 1] || "";
            var el = [step1, step2, step3, step4][n - 1];
            if (el) el.style.display = "block";
        }

        function clearResendTimer() {
            if (resendCountdown) { clearInterval(resendCountdown); resendCountdown = null; }
            if (resendOtpBtn) { resendOtpBtn.disabled = false; resendOtpBtn.textContent = "Resend OTP"; }
            if (resendHint) resendHint.textContent = "";
        }

        forgotPasswordLink.onclick = function () {
            modal2.style.display = "flex";
            resetIdInput.value = "";
            if (forgotOtpInput) forgotOtpInput.value = "";
            if (secureQuestionLabel) secureQuestionLabel.textContent = "";
            var sa = document.getElementById("secure_answer"); if (sa) sa.value = "";
            if (newPwInput) newPwInput.value = ""; if (confirmPwInput) confirmPwInput.value = "";
            clearResendTimer();
            if (otpSentTo) otpSentTo.style.display = "none";
            if (otpInputWrap) otpInputWrap.style.display = "none";
            showStep(1);
            setTimeout(function () { resetIdInput.focus(); }, 100);
        };

        document.getElementById("forgotStep1Btn").onclick = function () {
            var id = (resetIdInput.value || "").trim();
            step1Msg.textContent = ""; if (!id) { step1Msg.textContent = "Enter your User ID."; step1Msg.style.color = "#c00"; return; }
            var fd = new FormData(); fd.append("action", "verify_user_id"); fd.append("user_id", id);
            fetch(window.FORGOT_PASSWORD_API, { method: "POST", body: fd })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.status === "success") {
                        if (displayIdSpan) displayIdSpan.textContent = data.user_id;
                        if (displayUsernameSpan) displayUsernameSpan.textContent = data.username || "";
                        showStep(2);
                    } else {
                        step1Msg.textContent = data.message || "User ID not found.";
                        step1Msg.style.color = "#c00";
                    }
                })
                .catch(function () { step1Msg.textContent = "Network error."; step1Msg.style.color = "#c00"; });
        };

        sendOtpBtn.onclick = function () {
            step2Msg.textContent = "";
            var fd = new FormData(); fd.append("action", "send_otp");
            fetch(window.FORGOT_PASSWORD_API, { method: "POST", body: fd })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.status === "success" || data.status === "existing_otp") {
                        if (otpSentTo) { otpSentTo.style.display = "block"; otpSentTo.textContent = "Code sent to " + (data.email || "your email") + "."; }
                        if (otpInputWrap) otpInputWrap.style.display = "block";
                        if (forgotOtpInput) forgotOtpInput.value = "";
                        var sec = data.remaining_seconds || 60;
                        if (resendOtpBtn) { resendOtpBtn.disabled = true; resendOtpBtn.textContent = "Resend OTP (" + sec + "s)"; }
                        clearResendTimer();
                        resendCountdown = setInterval(function () {
                            sec--;
                            if (sec <= 0) { clearResendTimer(); return; }
                            if (resendOtpBtn) resendOtpBtn.textContent = "Resend OTP (" + sec + "s)";
                        }, 1000);
                        step2Msg.textContent = data.message || "";
                        step2Msg.style.color = "#0a0";
                    } else {
                        step2Msg.textContent = data.message || "Could not send OTP.";
                        step2Msg.style.color = "#c00";
                    }
                })
                .catch(function () { step2Msg.textContent = "Network error."; step2Msg.style.color = "#c00"; });
        };

        if (resendOtpBtn) resendOtpBtn.onclick = function () {
            var fd = new FormData(); fd.append("action", "send_otp");
            fetch(window.FORGOT_PASSWORD_API, { method: "POST", body: fd })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.status === "success" || data.status === "existing_otp") {
                        var sec = data.remaining_seconds || 60;
                        resendOtpBtn.disabled = true;
                        resendOtpBtn.textContent = "Resend OTP (" + sec + "s)";
                        clearResendTimer();
                        resendCountdown = setInterval(function () {
                            sec--;
                            if (sec <= 0) { clearResendTimer(); return; }
                            resendOtpBtn.textContent = "Resend OTP (" + sec + "s)";
                        }, 1000);
                        step2Msg.textContent = data.message || "New code sent.";
                        step2Msg.style.color = "#0a0";
                    } else {
                        step2Msg.textContent = data.message || "Resend failed.";
                        step2Msg.style.color = "#c00";
                    }
                });
        };

        if (verifyOtpBtn) verifyOtpBtn.onclick = function () {
            var otp = (forgotOtpInput && forgotOtpInput.value || "").trim();
            step2Msg.textContent = "";
            if (otp.length !== 6) { step2Msg.textContent = "Enter the 6-digit code."; step2Msg.style.color = "#c00"; return; }
            var fd = new FormData(); fd.append("action", "verify_otp"); fd.append("otp", otp);
            fetch(window.FORGOT_PASSWORD_API, { method: "POST", body: fd })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.status === "success") {
                        clearResendTimer();
                        showStep(3);
                        fd = new FormData(); fd.append("action", "get_security_question");
                        fetch(window.FORGOT_PASSWORD_API, { method: "POST", body: fd })
                            .then(function (r2) { return r2.json(); })
                            .then(function (d2) {
                                if (d2.status === "success" && secureQuestionLabel) secureQuestionLabel.textContent = d2.question || "Your security question";
                            });
                    } else {
                        step2Msg.textContent = data.message || "Invalid or expired OTP.";
                        step2Msg.style.color = "#c00";
                    }
                })
                .catch(function () { step2Msg.textContent = "Network error."; step2Msg.style.color = "#c00"; });
        };

        if (step3Btn) step3Btn.onclick = function () {
            var answerEl = document.getElementById("secure_answer");
            var answer = (answerEl && answerEl.value || "").trim();
            step3Msg.textContent = "";
            if (!answer) { step3Msg.textContent = "Enter your answer."; step3Msg.style.color = "#c00"; return; }
            var fd = new FormData(); fd.append("action", "verify_security_question"); fd.append("answer", answer);
            fetch(window.FORGOT_PASSWORD_API, { method: "POST", body: fd })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.status === "success") {
                        showStep(4);
                        if (newPwInput) newPwInput.focus();
                    } else {
                        step3Msg.textContent = data.message || "Incorrect answer.";
                        step3Msg.style.color = "#c00";
                    }
                })
                .catch(function () { step3Msg.textContent = "Network error."; step3Msg.style.color = "#c00"; });
        };

        if (step4Btn) step4Btn.onclick = function () {
            var np = (newPwInput && newPwInput.value) || "";
            var cp = (confirmPwInput && confirmPwInput.value) || "";
            step4Msg.textContent = "";
            if (np.length < 8 || np.length > 25) {
                step4Msg.textContent = "Password must be 8–25 characters.";
                step4Msg.style.color = "#c00";
                return;
            }
            if (np !== cp) {
                step4Msg.textContent = "Passwords do not match.";
                step4Msg.style.color = "#c00";
                return;
            }
            var fd = new FormData();
            fd.append("action", "change_password");
            fd.append("new_password", np);
            fd.append("confirm_password", cp);
            fetch(window.FORGOT_PASSWORD_API, { method: "POST", body: fd })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.status === "success") {
                        step4Msg.textContent = data.message || "Password changed. You can login now.";
                        step4Msg.style.color = "#0a0";
                        setTimeout(function () {
                            modal2.style.display = "none";
                            var vp = document.getElementById("validationMessPw");
                            if (vp) { vp.textContent = "Password changed. You can login now."; vp.style.display = "block"; }
                        }, 1500);
                    } else {
                        step4Msg.textContent = data.message || "Failed to change password.";
                        step4Msg.style.color = "#c00";
                    }
                })
                .catch(function () { step4Msg.textContent = "Network error."; step4Msg.style.color = "#c00"; });
        };
    } else {
        forgotPasswordLink.onclick = function () {
            modal2.style.display = "flex";
            if (resetIdInput) { resetIdInput.value = ""; setTimeout(function () { resetIdInput.focus(); }, 100); }
        };
    }
});