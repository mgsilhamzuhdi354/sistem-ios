<?php
/**
 * Verify OTP Page - 2FA Authentication
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP - PT Indo Ocean ERP</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0A1628 0%, #0A2463 50%, #1E5AA8 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 20px;
        }
        .login-container { width: 100%; max-width: 420px; }
        .login-card {
            background: rgba(15, 25, 45, 0.95);
            border-radius: 24px; padding: 48px 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(212, 175, 55, 0.2);
        }
        .login-header { text-align: center; margin-bottom: 40px; }
        .login-logo {
            width: 100px; height: 100px;
            border-radius: 50%;
            margin: 0 auto 20px;
            overflow: hidden;
            border: 3px solid rgba(212, 175, 55, 0.5);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            background: #fff;
        }
        .login-logo img { width: 100%; height: 100%; object-fit: cover; }
        .login-header h1 { color: #fff; font-size: 24px; font-weight: 700; margin-bottom: 8px; }
        .login-header p { color: #94A3B8; font-size: 14px; line-height: 1.5; }
        .email-display {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            color: #93C5FD;
            font-size: 14px;
            margin-top: 15px;
        }
        .form-group { margin-bottom: 24px; }
        .form-label { display: block; color: #CBD5E1; font-size: 13px; font-weight: 500; margin-bottom: 8px; }
        
        .otp-inputs {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .otp-input {
            width: 50px; height: 60px;
            background: rgba(30, 41, 59, 0.8);
            border: 2px solid rgba(71, 85, 105, 0.5);
            border-radius: 12px;
            color: #fff;
            font-size: 24px;
            font-weight: 700;
            text-align: center;
            transition: all 0.2s;
        }
        .otp-input:focus {
            outline: none;
            border-color: #D4AF37;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
        }
        
        .btn-primary {
            width: 100%; padding: 16px;
            background: linear-gradient(135deg, #D4AF37, #E8C547);
            border: none; border-radius: 12px; color: #0A1628;
            font-size: 16px; font-weight: 600; cursor: pointer;
            transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(212, 175, 55, 0.3); }
        .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
        
        .alert { padding: 14px 16px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; font-size: 14px; }
        .alert-error { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #FCA5A5; }
        .alert-success { background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #6EE7B7; }
        
        .resend-section {
            text-align: center;
            margin-top: 24px;
            color: #94A3B8;
            font-size: 13px;
        }
        .resend-link {
            color: #D4AF37;
            text-decoration: none;
            font-weight: 500;
        }
        .resend-link:hover { color: #E8C547; }
        .resend-link.disabled {
            color: #64748B;
            pointer-events: none;
        }
        
        .timer {
            display: inline-block;
            color: #94A3B8;
            font-size: 13px;
            margin-top: 16px;
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin-top: 24px;
            color: #64748B;
            text-decoration: none;
            font-size: 13px;
        }
        .back-link:hover { color: #94A3B8; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <img src="<?= BASE_URL ?>assets/images/logo.jpg" alt="PT Indo Oceancrew Services">
                </div>
                <h1>Verifikasi OTP</h1>
                <p>Masukkan kode 6 digit yang telah dikirim ke email Anda</p>
                <div class="email-display">
                    <i class="fas fa-envelope"></i> <?= htmlspecialchars($email) ?>
                </div>
            </div>
            
            <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= $flash['type'] === 'error' ? 'error' : 'success' ?>">
                    <i class="fas fa-<?= $flash['type'] === 'error' ? 'exclamation-circle' : 'check-circle' ?>"></i>
                    <?= $flash['message'] ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?= BASE_URL ?>auth/verify-otp" id="otpForm">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
                <input type="hidden" name="otp_code" id="otpCode">
                
                <div class="form-group">
                    <label class="form-label" style="text-align:center;">Kode OTP</label>
                    <div class="otp-inputs">
                        <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autofocus>
                        <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                        <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                        <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                        <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                        <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                    </div>
                </div>
                
                <button type="submit" class="btn-primary" id="verifyBtn" disabled>
                    <i class="fas fa-shield-alt"></i>
                    Verifikasi
                </button>
            </form>
            
            <div class="resend-section">
                <p>Tidak menerima kode?</p>
                <a href="<?= BASE_URL ?>auth/resend-otp" class="resend-link" id="resendLink">Kirim Ulang Kode</a>
                <div class="timer" id="timer"></div>
            </div>
            
            <a href="<?= BASE_URL ?>auth/login" class="back-link">
                <i class="fas fa-arrow-left"></i> Kembali ke Login
            </a>
        </div>
    </div>
    
    <script>
        // OTP Input Handling
        const inputs = document.querySelectorAll('.otp-input');
        const otpCodeInput = document.getElementById('otpCode');
        const verifyBtn = document.getElementById('verifyBtn');
        const form = document.getElementById('otpForm');
        
        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                // Only allow numbers
                input.value = input.value.replace(/[^0-9]/g, '');
                
                if (input.value && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                
                updateOtpCode();
            });
            
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });
            
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                const digits = paste.replace(/[^0-9]/g, '').split('');
                
                digits.forEach((digit, i) => {
                    if (inputs[i]) {
                        inputs[i].value = digit;
                    }
                });
                
                updateOtpCode();
                
                if (digits.length >= 6) {
                    inputs[5].focus();
                }
            });
        });
        
        function updateOtpCode() {
            let code = '';
            inputs.forEach(input => code += input.value);
            otpCodeInput.value = code;
            verifyBtn.disabled = code.length !== 6;
        }
        
        // Timer for resend
        let countdown = 60;
        const timerEl = document.getElementById('timer');
        const resendLink = document.getElementById('resendLink');
        
        function updateTimer() {
            if (countdown > 0) {
                timerEl.textContent = `Kirim ulang dalam ${countdown} detik`;
                resendLink.classList.add('disabled');
                countdown--;
                setTimeout(updateTimer, 1000);
            } else {
                timerEl.textContent = '';
                resendLink.classList.remove('disabled');
            }
        }
        
        updateTimer();
    </script>
</body>
</html>
