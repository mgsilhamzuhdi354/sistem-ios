<?php
/**
 * Forgot Password Page
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - PT Indo Ocean ERP</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0A1628 0%, #0A2463 50%, #1E5AA8 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-container { width: 100%; max-width: 420px; }
        .login-card {
            background: rgba(15, 25, 45, 0.95);
            border-radius: 24px;
            padding: 48px 40px;
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
        .form-control:focus { outline: none; border-color: #3B82F6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
        .form-control::placeholder { color: #64748B; }
        .btn-primary {
            width: 100%; padding: 16px;
            background: linear-gradient(135deg, #3B82F6, #60A5FA);
            border: none; border-radius: 12px; color: #fff;
            font-size: 16px; font-weight: 600; cursor: pointer;
            transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3); }
        .alert { padding: 14px 16px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; font-size: 14px; }
        .alert-error { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #FCA5A5; }
        .alert-success { background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #6EE7B7; }
        .alert-info { background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #93C5FD; }
        .back-link { display: block; text-align: center; margin-top: 24px; color: #D4AF37; text-decoration: none; font-size: 14px; }
        .back-link:hover { color: #E8C547; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <img src="<?= BASE_URL ?>assets/images/logo.png" alt="PT Indo Oceancrew Services">
                </div>
                <h1>Lupa Password?</h1>
                <p>Masukkan email yang terdaftar. Kami akan mengirimkan link untuk reset password Anda.</p>
            </div>
            
            <?php if (!empty($flash)): ?>
                <?php 
                $alertClass = $flash['type'] === 'error' ? 'error' : ($flash['type'] === 'info' ? 'info' : 'success');
                $alertIcon = $flash['type'] === 'error' ? 'exclamation-circle' : ($flash['type'] === 'info' ? 'info-circle' : 'check-circle');
                ?>
                <div class="alert alert-<?= $alertClass ?>">
                    <i class="fas fa-<?= $alertIcon ?>"></i>
                    <div><?= $flash['message'] ?></div>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?= BASE_URL ?>auth/send-reset-link">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-control" placeholder="Masukkan email Anda" required autofocus>
                    </div>
                </div>
                
                <button type="submit" class="btn-primary">
                    <i class="fas fa-paper-plane"></i>
                    Kirim Link Reset
                </button>
            </form>
            
            <a href="<?= BASE_URL ?>auth/login" class="back-link">
                <i class="fas fa-arrow-left"></i> Kembali ke Login
            </a>
        </div>
    </div>
</body>
</html>
