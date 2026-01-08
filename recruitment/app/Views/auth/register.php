<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - PT Indo Ocean Crew Services Recruitment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/auth.css') ?>">
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-image register-image">
            <div class="overlay"></div>
            <div class="image-content">
                <h1><i class="fas fa-ship"></i> Start Your Maritime Career</h1>
                <p>Register now and join thousands of professional seafarers worldwide.</p>
                <ul class="benefits-list">
                    <li><i class="fas fa-check"></i> Access to global job opportunities</li>
                    <li><i class="fas fa-check"></i> Competitive salary packages</li>
                    <li><i class="fas fa-check"></i> Professional career development</li>
                    <li><i class="fas fa-check"></i> 24/7 support team</li>
                </ul>
            </div>
        </div>
        
        <div class="auth-form-container">
            <div class="auth-form-wrapper">
                <div class="auth-header">
                    <h2>Create Account</h2>
                    <p>Join our maritime community</p>
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
                            <i class="fas fa-user"></i> Full Name
                        </label>
                        <input type="text" id="full_name" name="full_name" 
                               value="<?= old('full_name') ?>" 
                               placeholder="Enter your full name" required>
                        <?php if (isset($_SESSION['errors']['full_name'])): ?>
                            <span class="error"><?= $_SESSION['errors']['full_name'] ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <input type="email" id="email" name="email" 
                               value="<?= old('email') ?>" 
                               placeholder="Enter your email" required>
                        <?php if (isset($_SESSION['errors']['email'])): ?>
                            <span class="error"><?= $_SESSION['errors']['email'] ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">
                            <i class="fas fa-phone"></i> Phone Number
                        </label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?= old('phone') ?>" 
                               placeholder="Enter your phone number">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">
                                <i class="fas fa-lock"></i> Password
                            </label>
                            <input type="password" id="password" name="password" 
                                   placeholder="Min. 6 characters" required>
                            <?php if (isset($_SESSION['errors']['password'])): ?>
                                <span class="error"><?= $_SESSION['errors']['password'] ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirm">
                                <i class="fas fa-lock"></i> Confirm Password
                            </label>
                            <input type="password" id="password_confirm" name="password_confirm" 
                                   placeholder="Confirm password" required>
                            <?php if (isset($_SESSION['errors']['password_confirm'])): ?>
                                <span class="error"><?= $_SESSION['errors']['password_confirm'] ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="agree_terms" required>
                            <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></span>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>
                </form>
                
                <div class="auth-footer">
                    <p>Already have an account? <a href="<?= url('/login') ?>">Sign In</a></p>
                </div>
                
                <div class="back-link">
                    <a href="/PT_indoocean/"><i class="fas fa-arrow-left"></i> Back to Main Website</a>
                </div>
            </div>
        </div>
    </div>
    
    <?php unset($_SESSION['errors'], $_SESSION['old']); ?>
</body>
</html>
