<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PT Indo Ocean Crew Services Recruitment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/auth.css') ?>">
    <style>
        .language-selector {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 100;
        }
        .language-selector select {
            padding: 8px 12px;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 8px;
            background: rgba(255,255,255,0.9);
            font-size: 14px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
        }
        .language-selector select:focus {
            outline: none;
            border-color: #0A2463;
        }
    </style>
</head>
<body class="auth-body">
    <!-- Language Selector -->
    <div class="language-selector">
        <select id="langSelect">
            <option value="en">🇺🇸 English</option>
            <option value="id">🇮🇩 Indonesia</option>
            <option value="zh">🇨🇳 中文</option>
        </select>
    </div>

    <div class="auth-container">
        <div class="auth-image">
            <div class="overlay"></div>
            <div class="image-content">
                <h1><img src="<?= asset('images/logo.jpg') ?>" alt="Indo Ocean" style="height: 32px; vertical-align: middle;"> PT Indo Ocean Crew Services</h1>
                <p data-translate="auth.tagline">Join our professional team of seafarers and embark on an exciting maritime career.</p>
            </div>
        </div>
        
        <div class="auth-form-container">
            <div class="auth-form-wrapper">
                <div class="auth-header">
                    <h2 data-translate="auth.loginTitle">Sign In</h2>
                    <p data-translate="auth.loginSubtitle">Welcome back! Please sign in to your account</p>
                </div>
                
                <?php if (isset($_SESSION['errors']['login'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= $_SESSION['errors']['login'] ?>
                    </div>
                    <?php unset($_SESSION['errors']); ?>
                <?php endif; ?>
                
                <?php if ($success = flash('success')): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?= $success ?>
                    </div>
                <?php endif; ?>
                
                <form action="<?= url('/login') ?>" method="POST" class="auth-form">
                    <?= csrf_field() ?>
                    
                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i> <span data-translate="auth.email">Email Address</span>
                        </label>
                        <input type="email" id="email" name="email" 
                               value="<?= old('email') ?>" 
                               data-translate-placeholder="auth.email"
                               placeholder="Enter your email" required>
                        <?php if (isset($_SESSION['errors']['email'])): ?>
                            <span class="error"><?= $_SESSION['errors']['email'] ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i> <span data-translate="auth.password">Password</span>
                        </label>
                        <div class="password-input">
                            <input type="password" id="password" name="password" 
                                   data-translate-placeholder="auth.password"
                                   placeholder="Enter your password" required>
                            <button type="button" class="toggle-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember">
                            <span data-translate="auth.rememberMe">Remember me</span>
                        </label>
                        <a href="<?= url('/forgot-password') ?>" class="forgot-link" data-translate="auth.forgotPassword">Forgot Password?</a>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-sign-in-alt"></i> <span data-translate="auth.loginBtn">Sign In</span>
                    </button>
                </form>
                
                <div class="auth-footer">
                    <p><span data-translate="auth.noAccount">Don't have an account?</span> <a href="<?= url('/register') ?>" data-translate="auth.signUp">Create Account</a></p>
                </div>
                
                <div class="back-link">
                    <a href="/PT_indoocean/"><i class="fas fa-arrow-left"></i> <span data-translate="common.back">Back to Main Website</span></a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Translation Script -->
    <script src="<?= asset('js/translate-recruitment.js') ?>"></script>
    <script>
        // Toggle password visibility
        document.querySelector('.toggle-password').addEventListener('click', function() {
            const input = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
</body>
</html>
