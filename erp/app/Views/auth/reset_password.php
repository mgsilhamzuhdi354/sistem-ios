<?php
/**
 * Reset Password Page
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - PT Indo Ocean ERP</title>
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
        .login-header p { color: #94A3B8; font-size: 14px; }
        .form-group { margin-bottom: 24px; }
        .form-label { display: block; color: #CBD5E1; font-size: 13px; font-weight: 500; margin-bottom: 8px; }
        .input-group { position: relative; }
        .input-group i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #64748B; font-size: 16px; }
        .form-control {
            width: 100%; padding: 14px 16px 14px 48px;
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(71, 85, 105, 0.5);
            border-radius: 12px; color: #fff; font-size: 15px; transition: all 0.2s;
        }
        .form-control:focus { outline: none; border-color: #10B981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1); }
        .form-control::placeholder { color: #64748B; }
        .btn-primary {
            width: 100%; padding: 16px;
            background: linear-gradient(135deg, #10B981, #34D399);
            border: none; border-radius: 12px; color: #fff;
            font-size: 16px; font-weight: 600; cursor: pointer;
            transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3); }
        .alert { padding: 14px 16px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; font-size: 14px; }
        .alert-error { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #FCA5A5; }
        .password-strength { margin-top: 8px; font-size: 12px; }
        .strength-bar { height: 4px; border-radius: 2px; background: rgba(71, 85, 105, 0.5); margin-top: 4px; }
        .strength-fill { height: 100%; border-radius: 2px; transition: all 0.3s; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <img src="<?= BASE_URL ?>assets/images/logo.png" alt="PT Indo Oceancrew Services">
                </div>
                <h1>Reset Password</h1>
                <p>Masukkan password baru Anda</p>
            </div>
            
            <?php if (!empty($flash)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= $flash['message'] ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?= BASE_URL ?>auth/update-password">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
                
                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 8 karakter" required minlength="8" oninput="checkStrength(this.value)">
                    </div>
                    <div class="password-strength">
                        <span id="strengthText" style="color: #64748B;">Kekuatan password</span>
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthBar" style="width: 0%; background: #EF4444;"></div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Ulangi password" required>
                    </div>
                </div>
                
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i>
                    Simpan Password Baru
                </button>
            </form>
        </div>
    </div>
    
    <script>
        function checkStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            const bar = document.getElementById('strengthBar');
            const text = document.getElementById('strengthText');
            
            const widths = ['20%', '40%', '60%', '80%', '100%'];
            const colors = ['#EF4444', '#F59E0B', '#EAB308', '#84CC16', '#10B981'];
            const texts = ['Sangat Lemah', 'Lemah', 'Cukup', 'Kuat', 'Sangat Kuat'];
            
            bar.style.width = widths[strength - 1] || '0%';
            bar.style.background = colors[strength - 1] || '#EF4444';
            text.textContent = texts[strength - 1] || 'Kekuatan password';
            text.style.color = colors[strength - 1] || '#64748B';
        }
    </script>
</body>
</html>
