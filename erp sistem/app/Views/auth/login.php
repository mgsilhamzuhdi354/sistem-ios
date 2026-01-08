<?php
/**
 * Login Page - Standalone (no layout)
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PT Indo Ocean ERP</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0A1628 0%, #0A2463 50%, #1E5AA8 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            width: 100%;
            max-width: 420px;
        }
        
        .login-card {
            background: rgba(15, 25, 45, 0.95);
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(212, 175, 55, 0.2);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .login-logo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 0 auto 20px;
            overflow: hidden;
            border: 3px solid rgba(212, 175, 55, 0.5);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            background: #fff;
        }
        
        .login-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }
        
        .login-header h1 {
            color: #fff;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .login-header p {
            color: #94A3B8;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-label {
            display: block;
            color: #CBD5E1;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748B;
            font-size: 16px;
        }
        
        .form-control {
            width: 100%;
            padding: 14px 16px 14px 48px;
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(71, 85, 105, 0.5);
            border-radius: 12px;
            color: #fff;
            font-size: 15px;
            transition: all 0.2s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #D4AF37;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }
        
        .form-control::placeholder {
            color: #64748B;
        }
        
        .remember-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #94A3B8;
            font-size: 13px;
            cursor: pointer;
        }
        
        .checkbox-label input {
            width: 16px;
            height: 16px;
            accent-color: #D4AF37;
        }
        
        .forgot-link {
            color: #D4AF37;
            font-size: 13px;
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .forgot-link:hover {
            color: #E8C547;
        }
        
        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #D4AF37, #E8C547);
            border: none;
            border-radius: 12px;
            color: #0A1628;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(212, 175, 55, 0.3);
        }
        
        .alert {
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #FCA5A5;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6EE7B7;
        }
        
        .footer-text {
            text-align: center;
            margin-top: 32px;
            color: #64748B;
            font-size: 12px;
        }
        
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #64748B;
            cursor: pointer;
            padding: 0;
        }
        
        .password-toggle:hover {
            color: #D4AF37;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <img src="<?= BASE_URL ?>assets/images/logo.jpg" alt="PT Indo Oceancrew Services">
                </div>
                <h1>PT Indo Oceancrew Services</h1>
                <p>Ship Crewing Company</p>
            </div>
            
            <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= $flash['type'] === 'error' ? 'error' : 'success' ?>">
                    <i class="fas fa-<?= $flash['type'] === 'error' ? 'exclamation-circle' : 'check-circle' ?>"></i>
                    <?= $flash['message'] ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?= BASE_URL ?>auth/authenticate">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
                
                <div class="form-group">
                    <label class="form-label">Username atau Email</label>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" name="login" class="form-control" placeholder="Masukkan username atau email" required autofocus>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="remember-row">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember">
                        Ingat saya
                    </label>
                    <a href="<?= BASE_URL ?>auth/forgot-password" class="forgot-link">Lupa password?</a>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i>
                    Masuk
                </button>
            </form>
            
            <div class="footer-text">
                &copy; <?= date('Y') ?> PT Indo Ocean. All rights reserved.
            </div>
        </div>
    </div>
    
    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
