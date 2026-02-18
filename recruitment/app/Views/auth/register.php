<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="auth.registerTitle">Register - PT Indo Ocean Crew Services Recruitment</title>
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
        <div class="auth-image register-image">
            <div class="overlay"></div>
            <div class="image-content">
                <h1><i class="fas fa-ship"></i> <span data-translate="auth.registerHeroTitle">Start Your Maritime Career</span></h1>
                <p data-translate="auth.registerHeroSubtitle">Register now and join thousands of professional seafarers worldwide.</p>
                <ul class="benefits-list">
                    <li><i class="fas fa-check"></i> <span data-translate="auth.benefit1">Access to global job opportunities</span></li>
                    <li><i class="fas fa-check"></i> <span data-translate="auth.benefit2">Competitive salary packages</span></li>
                    <li><i class="fas fa-check"></i> <span data-translate="auth.benefit3">Professional career development</span></li>
                    <li><i class="fas fa-check"></i> <span data-translate="auth.benefit4">24/7 support team</span></li>
                </ul>
            </div>
        </div>
        
        <div class="auth-form-container">
            <div class="auth-form-wrapper">
                <div class="auth-header">
                    <h2 data-translate="auth.registerTitle">Create Account</h2>
                    <p data-translate="auth.registerSubtitle">Join our maritime community</p>
                </div>
                
                <?php if (isset($_SESSION['errors']['register'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= $_SESSION['errors']['register'] ?>
                    </div>
                <?php endif; ?>
                
                <form action="<?= url('/register') ?>" method="POST" class="auth-form">
                    <?= csrf_field() ?>
                    
                    <div class="form-group">
                        <label for="full_name">
                            <i class="fas fa-user"></i> <span data-translate="auth.fullName">Full Name</span>
                        </label>
                        <input type="text" id="full_name" name="full_name" 
                               value="<?= old('full_name') ?>" 
                               placeholder="Enter your full name" data-placeholder="auth.fullNamePlaceholder" required>
                        <?php if (isset($_SESSION['errors']['full_name'])): ?>
                            <span class="error"><?= $_SESSION['errors']['full_name'] ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i> <span data-translate="auth.email">Email Address</span>
                        </label>
                        <input type="email" id="email" name="email" 
                               value="<?= old('email') ?>" 
                               placeholder="Enter your email" data-placeholder="auth.emailPlaceholder" required>
                        <?php if (isset($_SESSION['errors']['email'])): ?>
                            <span class="error"><?= $_SESSION['errors']['email'] ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">
                            <i class="fas fa-phone"></i> <span data-translate="auth.phone">Phone Number</span>
                        </label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?= old('phone') ?>" 
                               placeholder="Enter your phone number" data-placeholder="auth.phonePlaceholder">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">
                                <i class="fas fa-lock"></i> <span data-translate="auth.password">Password</span>
                            </label>
                            <input type="password" id="password" name="password" 
                                   placeholder="Min. 6 characters" data-placeholder="auth.passwordPlaceholder" required>
                            <?php if (isset($_SESSION['errors']['password'])): ?>
                                <span class="error"><?= $_SESSION['errors']['password'] ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirm">
                                <i class="fas fa-lock"></i> <span data-translate="auth.confirmPassword">Confirm Password</span>
                            </label>
                            <input type="password" id="password_confirm" name="password_confirm" 
                                   placeholder="Confirm password" data-placeholder="auth.confirmPasswordPlaceholder" required>
                            <?php if (isset($_SESSION['errors']['password_confirm'])): ?>
                                <span class="error"><?= $_SESSION['errors']['password_confirm'] ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="agree_terms" required>
                            <span data-translate="auth.agreeTerms">I agree to the Terms of Service and Privacy Policy</span>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-user-plus"></i> <span data-translate="auth.registerBtn">Create Account</span>
                    </button>
                </form>
                
                <div class="auth-footer">
                    <p><span data-translate="auth.hasAccount">Already have an account?</span> <a href="<?= url('/login') ?>" data-translate="auth.signIn">Sign In</a></p>
                </div>
                
                <div class="back-link">
                    <?php 
                    $hostReg = $_SERVER['HTTP_HOST'] ?? 'localhost';
                    $isLaragonReg = (strpos($hostReg, '.test') !== false || strpos($hostReg, '.local') !== false);
                    $backUrlReg = $isLaragonReg ? '/' : '/indoocean/';
                    ?>
                    <a href="<?= $backUrlReg ?>"><i class="fas fa-arrow-left"></i> <span data-translate="common.back">Back to Main Website</span></a>
                </div>
            </div>
        </div>
    </div>
    
    <?php unset($_SESSION['errors'], $_SESSION['old']); ?>
    
    <!-- Translation Script -->
    <script src="<?= asset('js/translate-recruitment.js') ?>"></script>
</body>
</html>
