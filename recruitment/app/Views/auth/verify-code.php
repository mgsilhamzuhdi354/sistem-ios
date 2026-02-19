<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Kode - PT Indo Ocean</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .verify-container {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 480px;
            width: 100%;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo i {
            font-size: 3.5rem;
            color: #667eea;
            margin-bottom: 0.5rem;
        }
        
        .otp-inputs {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin: 25px 0;
        }
        
        .otp-inputs input {
            width: 48px;
            height: 56px;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            transition: all 0.3s ease;
            color: #333;
        }
        
        .otp-inputs input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            outline: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        
        .email-badge {
            background: #f0f4ff;
            color: #667eea;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .resend-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .resend-link:hover {
            text-decoration: underline;
        }
        
        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .fallback-code {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-size: 28px;
            letter-spacing: 10px;
            padding: 15px 30px;
            border-radius: 12px;
            font-weight: bold;
            text-align: center;
            margin: 15px 0;
        }

        .timer {
            color: #888;
            font-size: 13px;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="logo">
            <i class="fas fa-shield-halved"></i>
            <h2>Verifikasi Kode</h2>
            <p class="text-muted">Masukkan kode 6 digit yang dikirim ke email Anda</p>
            <div class="email-badge">
                <i class="fas fa-envelope"></i> <?= htmlspecialchars($email ?? '') ?>
            </div>
        </div>

        <?php $flashError = flash('error'); if ($flashError): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i> <?= $flashError ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php $flashSuccess = flash('success'); if ($flashSuccess): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?= $flashSuccess ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php $flashInfo = flash('info'); if ($flashInfo): ?>
            <div class="alert alert-info alert-dismissible fade show">
                <i class="fas fa-info-circle"></i> <?= $flashInfo ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($otpFallback)): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> 
                <strong>Email tidak terkirim.</strong> Gunakan kode berikut:
                <div class="fallback-code"><?= htmlspecialchars($otpFallback) ?></div>
                <small class="text-muted">Kode berlaku 15 menit</small>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('/verify-reset-code') ?>" id="otpForm">
            <?= csrf_field() ?>
            <input type="hidden" name="otp_code" id="otpHidden">
            
            <div class="otp-inputs">
                <input type="text" maxlength="1" class="otp-digit" data-index="0" inputmode="numeric" autofocus>
                <input type="text" maxlength="1" class="otp-digit" data-index="1" inputmode="numeric">
                <input type="text" maxlength="1" class="otp-digit" data-index="2" inputmode="numeric">
                <input type="text" maxlength="1" class="otp-digit" data-index="3" inputmode="numeric">
                <input type="text" maxlength="1" class="otp-digit" data-index="4" inputmode="numeric">
                <input type="text" maxlength="1" class="otp-digit" data-index="5" inputmode="numeric">
            </div>

            <button type="submit" class="btn btn-primary w-100" id="verifyBtn" disabled>
                <i class="fas fa-check-circle"></i> Verifikasi Kode
            </button>
        </form>

        <div class="timer" id="timer">
            Kode berlaku: <span id="countdown">15:00</span>
        </div>

        <div class="text-center mt-3">
            <span class="text-muted">Belum menerima kode?</span>
            <a href="<?= url('/resend-reset-code') ?>" class="resend-link">Kirim Ulang</a>
        </div>

        <div class="back-link">
            <a href="<?= url('/forgot-password') ?>">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // OTP input handling
        const digits = document.querySelectorAll('.otp-digit');
        const otpHidden = document.getElementById('otpHidden');
        const verifyBtn = document.getElementById('verifyBtn');

        function updateOTP() {
            let code = '';
            digits.forEach(d => code += d.value);
            otpHidden.value = code;
            verifyBtn.disabled = code.length < 6;
        }

        digits.forEach((input, idx) => {
            input.addEventListener('input', (e) => {
                const val = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = val;
                if (val && idx < 5) digits[idx + 1].focus();
                updateOTP();
            });
            
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && idx > 0) {
                    digits[idx - 1].focus();
                }
            });

            // Handle paste
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasted = (e.clipboardData.getData('text') || '').replace(/[^0-9]/g, '').slice(0, 6);
                for (let i = 0; i < pasted.length && i < 6; i++) {
                    digits[i].value = pasted[i];
                }
                if (pasted.length > 0) digits[Math.min(pasted.length, 5)].focus();
                updateOTP();
            });
        });

        // Countdown timer (15 minutes)
        let seconds = 15 * 60;
        const countdownEl = document.getElementById('countdown');
        const timer = setInterval(() => {
            seconds--;
            if (seconds <= 0) {
                clearInterval(timer);
                countdownEl.textContent = 'Kedaluwarsa';
                countdownEl.style.color = '#e53e3e';
                return;
            }
            const m = Math.floor(seconds / 60);
            const s = seconds % 60;
            countdownEl.textContent = m + ':' + (s < 10 ? '0' : '') + s;
        }, 1000);
    </script>
</body>
</html>
