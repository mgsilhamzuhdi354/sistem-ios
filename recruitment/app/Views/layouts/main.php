<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Recruitment' ?> - PT Indo Ocean Crew Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <nav class="navbar">
                <a href="<?= url('/') ?>" class="logo">
                    <img src="<?= asset('images/logo.jpg') ?>" alt="Indo Ocean" style="height: 28px;">
                    <span>Indo Ocean Crew</span>
                </a>
                
                <ul class="nav-menu">
                    <li><a href="<?= url('/') ?>">Home</a></li>
                    <li><a href="<?= url('/jobs') ?>">Job Vacancies</a></li>
                    <li><a href="/PT_indoocean/about.html">About</a></li>
                    <li><a href="/PT_indoocean/contact.html">Contact</a></li>
                </ul>
                
                <div class="nav-actions">
                    <?php if (isLoggedIn()): ?>
                        <a href="<?= isAdmin() ? url('/admin/dashboard') : url('/applicant/dashboard') ?>" class="btn btn-outline-light">
                            <i class="fas fa-user"></i> Dashboard
                        </a>
                        <a href="<?= url('/logout') ?>" class="btn btn-light">Logout</a>
                    <?php else: ?>
                        <a href="<?= url('/login') ?>" class="btn btn-outline-light">Login</a>
                        <a href="<?= url('/register') ?>" class="btn btn-light">Register</a>
                    <?php endif; ?>
                </div>
                
                <button class="hamburger" id="hamburger">
                    <span></span><span></span><span></span>
                </button>
            </nav>
        </div>
    </header>

    <!-- Flash Messages -->
    <?php if ($success = flash('success')): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= $success ?>
        </div>
    <?php endif; ?>
    <?php if ($error = flash('error')): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="main-content">
        <?php include APPPATH . 'Views/' . $content . '.php'; ?>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-about">
                    <h3><img src="<?= asset('images/logo.jpg') ?>" alt="Indo Ocean" style="height: 24px; margin-right: 8px;"> PT Indo Ocean Crew Services</h3>
                    <p>Professional maritime recruitment services for global shipping industry.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="<?= url('/jobs') ?>">Job Vacancies</a></li>
                        <li><a href="<?= url('/register') ?>">Apply Now</a></li>
                        <li><a href="/PT_indoocean/about.html">About Us</a></li>
                        <li><a href="/PT_indoocean/contact.html">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h4>Contact Us</h4>
                    <p><i class="fas fa-map-marker-alt"></i> Jakarta, Indonesia</p>
                    <p><i class="fas fa-phone"></i> +62 21 1234 5678</p>
                    <p><i class="fas fa-envelope"></i> recruitment@indoceancrew.com</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> PT Indo Ocean Crew Services. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
