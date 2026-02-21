<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Indo Ocean Crew Services - Recruitment</title>
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
                    <li><a href="<?= url('/') ?>" class="active">Home</a></li>
                    <li><a href="<?= url('/jobs') ?>">Jobs</a></li>
                    <li><a href="<?= url('/about') ?>">About</a></li>
                    <li><a href="<?= url('/contact') ?>">Contact</a></li>
                </ul>
                <div class="nav-actions">
                    <?php if (isLoggedIn()): ?>
                        <a href="<?= url('/applicant/dashboard') ?>" class="btn btn-outline-light">Dashboard</a>
                    <?php else: ?>
                        <a href="<?= url('/login') ?>" class="btn btn-outline-light">Login</a>
                        <a href="<?= url('/register') ?>" class="btn btn-light">Register</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section with Animated Background -->
    <section class="hero-section">
        <!-- Animated Image Background -->
        <div class="hero-bg-container">
            <div class="hero-bg-image"></div>
            <div class="hero-bg-waves"></div>
            <div class="hero-video-overlay"></div>
        </div>
        
        <div class="container">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="fas fa-anchor"></i>
                    <span>Maritime Excellence Since 2010</span>
                </div>
                <h1>Start Your <span class="gradient-text">Maritime Career</span> Today</h1>
                <p>Join thousands of professional seafarers working with world-class shipping companies across the globe. Competitive salaries, career growth, and worldwide opportunities await.</p>
                <div class="hero-actions">
                    <a href="<?= url('/jobs') ?>" class="btn btn-hero-primary btn-lg">
                        <i class="fas fa-compass"></i> Explore Jobs
                    </a>
                    <a href="<?= url('/register') ?>" class="btn btn-hero-secondary btn-lg">
                        <i class="fas fa-ship"></i> Join Our Crew
                    </a>
                </div>
                <div class="hero-stats-container">
                    <div class="hero-stats">
                        <div class="stat">
                            <div class="stat-icon"><i class="fas fa-briefcase"></i></div>
                            <span class="number"><?= $stats['total_vacancies'] ?>+</span>
                            <span class="label">Open Positions</span>
                        </div>
                        <div class="stat">
                            <div class="stat-icon"><i class="fas fa-users"></i></div>
                            <span class="number"><?= $stats['total_applicants'] ?>+</span>
                            <span class="label">Registered Seafarers</span>
                        </div>
                        <div class="stat">
                            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                            <span class="number"><?= $stats['total_hired'] ?>+</span>
                            <span class="label">Hired This Year</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="scroll-indicator">
            <div class="scroll-mouse">
                <div class="scroll-wheel"></div>
            </div>
            <span>Scroll Down</span>
        </div>
    </section>

    <!-- Departments Section -->
    <section class="departments-section">
        <div class="container">
            <div class="section-header">
                <h2>Explore Departments</h2>
                <p>Find opportunities in your area of expertise</p>
            </div>
            <div class="departments-grid">
                <?php foreach ($departments as $dept): ?>
                    <a href="<?= url('/jobs?department=' . $dept['id']) ?>" class="department-card">
                        <div class="dept-icon" style="background: <?= $dept['color'] ?? '#0A2463' ?>">
                            <i class="fas <?= $dept['icon'] ?? 'fa-ship' ?>"></i>
                        </div>
                        <h3><?= htmlspecialchars($dept['name']) ?></h3>
                        <p><?= htmlspecialchars($dept['description'] ?? 'Explore career opportunities') ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Jobs Section -->
    <?php if (!empty($featuredJobs)): ?>
    <section class="featured-section">
        <div class="container">
            <div class="section-header">
                <h2>Featured Positions</h2>
                <p>Hot opportunities waiting for you</p>
            </div>
            <div class="jobs-grid">
                <?php foreach ($featuredJobs as $job): ?>
                    <div class="job-card">
                        <div class="job-header">
                            <div class="job-icon" style="background: <?= $job['department_color'] ?? '#0A2463' ?>">
                                <i class="fas <?= $job['department_icon'] ?? 'fa-ship' ?>"></i>
                            </div>
                            <span class="featured-badge"><i class="fas fa-star"></i> Featured</span>
                        </div>
                        <h3 class="job-title"><?= htmlspecialchars($job['title']) ?></h3>
                        <div class="job-details">
                            <span><i class="fas fa-building"></i> <?= htmlspecialchars($job['department_name'] ?? 'General') ?></span>
                            <span><i class="fas fa-ship"></i> <?= htmlspecialchars($job['vessel_type'] ?? 'Various') ?></span>
                        </div>
                        <div class="job-salary">
                            <?php if ($job['salary_min'] && $job['salary_max']): ?>
                                $<?= number_format($job['salary_min']) ?> - $<?= number_format($job['salary_max']) ?>
                            <?php else: ?>
                                Negotiable
                            <?php endif; ?>
                            <span class="salary-period">/month</span>
                        </div>
                        <?php if (!empty($job['recruiter_name'])): ?>
                        <div class="job-recruiter">
                            <div class="recruiter-avatar">
                                <?php if (!empty($job['recruiter_photo'])): ?>
                                    <img src="<?= asset('uploads/recruiters/' . $job['recruiter_photo']) ?>" alt="<?= htmlspecialchars($job['recruiter_name']) ?>">
                                <?php else: ?>
                                    <i class="fas fa-user"></i>
                                <?php endif; ?>
                            </div>
                            <div class="recruiter-info">
                                <span class="recruiter-label">Posted by</span>
                                <span class="recruiter-name"><?= htmlspecialchars($job['recruiter_name']) ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="job-actions">
                            <a href="<?= url('/jobs/' . $job['id']) ?>" class="btn btn-primary btn-block">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="section-footer">
                <a href="<?= url('/jobs') ?>" class="btn btn-outline btn-lg">View All Positions</a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Why Choose Us Section -->
    <section class="why-section">
        <div class="container">
            <div class="section-header">
                <h2>Why Choose Indo Ocean Crew?</h2>
                <p>We connect you with the best maritime opportunities worldwide</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h3>Global Opportunities</h3>
                    <p>Access positions with leading shipping companies around the world.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h3>Competitive Packages</h3>
                    <p>Attractive salary and benefits aligned with international standards.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Career Growth</h3>
                    <p>Continuous training and clear pathways for career advancement.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>24/7 Support</h3>
                    <p>Dedicated support team available round the clock for all your needs.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Start Your Maritime Journey?</h2>
                <p>Register now and take the first step towards an exciting career at sea.</p>
                <a href="<?= url('/register') ?>" class="btn btn-light btn-lg">
                    <i class="fas fa-rocket"></i> Get Started
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-about">
                    <h3><img src="<?= asset('images/logo.jpg') ?>" alt="Indo Ocean" style="height: 24px; margin-right: 8px;"> PT Indo Ocean Crew Services</h3>
                    <p>Professional maritime recruitment services connecting skilled seafarers with world-class shipping opportunities.</p>
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
                        <li><a href="<?= url('/about') ?>">About Us</a></li>
                        <li><a href="<?= url('/contact') ?>">Contact</a></li>
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

    <style>
    /* Hero Section with Animated Background */
    .hero-section {
        position: relative;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        overflow: hidden;
    }
    
    /* Background Container */
    .hero-bg-container {
        position: absolute;
        inset: 0;
        z-index: 0;
    }
    
    /* Animated Background Image with Ken Burns Effect */
    .hero-bg-image {
        position: absolute;
        inset: -20px;
        background-image: url('<?= asset('images/hero-cargo-ship.png') ?>');
        background-size: cover;
        background-position: center;
        animation: kenBurnsZoom 30s ease-in-out infinite alternate;
    }
    
    @keyframes kenBurnsZoom {
        0% {
            transform: scale(1) translateX(0);
        }
        50% {
            transform: scale(1.1) translateX(-2%);
        }
        100% {
            transform: scale(1.05) translateX(1%);
        }
    }
    
    /* Animated Wave Overlay */
    .hero-bg-waves {
        position: absolute;
        inset: 0;
        background: 
            repeating-linear-gradient(
                90deg,
                transparent,
                transparent 100px,
                rgba(255, 255, 255, 0.03) 100px,
                rgba(255, 255, 255, 0.03) 200px
            );
        animation: waveMove 8s linear infinite;
        pointer-events: none;
    }
    
    .hero-bg-waves::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 200px;
        background: linear-gradient(
            to top,
            rgba(10, 36, 99, 0.9) 0%,
            transparent 100%
        );
    }
    
    @keyframes waveMove {
        0% { transform: translateX(0); }
        100% { transform: translateX(-200px); }
    }
    
    .hero-video-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(
            135deg,
            rgba(10, 36, 99, 0.75) 0%,
            rgba(30, 90, 168, 0.55) 50%,
            rgba(10, 36, 99, 0.8) 100%
        );
        z-index: 1;
    }
    
    /* Hero Content */
    .hero-content {
        position: relative;
        z-index: 2;
        max-width: 800px;
        text-align: center;
        animation: fadeInUp 1s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Hero Badge */
    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        padding: 12px 25px;
        border-radius: 50px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        margin-bottom: 30px;
        font-size: 14px;
        font-weight: 500;
        animation: fadeInUp 1s ease-out 0.2s backwards;
    }
    
    .hero-badge i {
        color: #D4AF37;
        font-size: 16px;
    }
    
    /* Hero Title */
    .hero-content h1 {
        font-size: 56px;
        font-weight: 700;
        margin-bottom: 25px;
        line-height: 1.15;
        text-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        animation: fadeInUp 1s ease-out 0.4s backwards;
    }
    
    .gradient-text {
        background: linear-gradient(135deg, #D4AF37, #FFD700, #FFA500);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .hero-content p {
        font-size: 20px;
        opacity: 0.95;
        margin-bottom: 40px;
        line-height: 1.8;
        max-width: 650px;
        margin-left: auto;
        margin-right: auto;
        animation: fadeInUp 1s ease-out 0.6s backwards;
    }
    
    /* Hero Actions */
    .hero-actions {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-bottom: 60px;
        animation: fadeInUp 1s ease-out 0.8s backwards;
    }
    
    .btn-hero-primary {
        background: linear-gradient(135deg, #D4AF37, #FFD700);
        color: #1a1a2e;
        padding: 18px 40px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 16px;
        transition: all 0.4s ease;
        box-shadow: 0 8px 30px rgba(212, 175, 55, 0.4);
        border: none;
        cursor: pointer;
    }
    
    .btn-hero-primary:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(212, 175, 55, 0.5);
    }
    
    .btn-hero-secondary {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        color: white;
        padding: 18px 40px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 16px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        transition: all 0.4s ease;
        cursor: pointer;
    }
    
    .btn-hero-secondary:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-5px);
    }
    
    /* Hero Stats Container */
    .hero-stats-container {
        animation: fadeInUp 1s ease-out 1s backwards;
    }
    
    .hero-stats {
        display: flex;
        justify-content: center;
        gap: 60px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(15px);
        padding: 35px 50px;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }
    
    .hero-stats .stat {
        text-align: center;
        position: relative;
    }
    
    .hero-stats .stat-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #D4AF37, #FFD700);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        font-size: 20px;
        color: #1a1a2e;
    }
    
    .hero-stats .number {
        display: block;
        font-size: 42px;
        font-weight: 700;
        background: linear-gradient(135deg, #fff, #D4AF37);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .hero-stats .label {
        font-size: 14px;
        opacity: 0.85;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    /* Scroll Indicator */
    .scroll-indicator {
        position: absolute;
        bottom: 40px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        animation: bounce 2s infinite;
        color: rgba(255, 255, 255, 0.7);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    
    .scroll-mouse {
        width: 26px;
        height: 42px;
        border: 2px solid rgba(255, 255, 255, 0.4);
        border-radius: 15px;
        display: flex;
        justify-content: center;
        padding-top: 8px;
    }
    
    .scroll-wheel {
        width: 4px;
        height: 10px;
        background: #D4AF37;
        border-radius: 2px;
        animation: scroll 2s infinite;
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
        40% { transform: translateX(-50%) translateY(-10px); }
        60% { transform: translateX(-50%) translateY(-5px); }
    }
    
    @keyframes scroll {
        0% { opacity: 1; transform: translateY(0); }
        50% { opacity: 0.5; transform: translateY(8px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    /* Departments Section */
    .departments-section {
        padding: 100px 0;
        background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
    }
    
    .section-header {
        text-align: center;
        margin-bottom: 60px;
    }
    
    .section-header h2 {
        font-size: 42px;
        color: #0A2463;
        margin-bottom: 15px;
        font-weight: 700;
    }
    
    .section-header p {
        color: #6c757d;
        font-size: 18px;
        max-width: 500px;
        margin: 0 auto;
    }
    
    .departments-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
    }
    
    .department-card {
        background: white;
        padding: 40px 30px;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.06);
        text-align: center;
        transition: all 0.4s ease;
        border: 1px solid rgba(0,0,0,0.05);
        position: relative;
        overflow: hidden;
    }
    
    .department-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, #0A2463, #1E5AA8);
        transform: scaleX(0);
        transition: transform 0.4s ease;
    }
    
    .department-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 60px rgba(10,36,99,0.15);
    }
    
    .department-card:hover::before {
        transform: scaleX(1);
    }
    
    .dept-icon {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        color: white;
        margin: 0 auto 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        transition: all 0.4s ease;
    }
    
    .department-card:hover .dept-icon {
        transform: scale(1.1) rotate(5deg);
    }
    
    .department-card h3 {
        font-size: 20px;
        color: #1a1a2e;
        margin-bottom: 12px;
        font-weight: 600;
    }
    
    .department-card p {
        font-size: 15px;
        color: #6c757d;
        line-height: 1.6;
    }

    /* Job Recruiter Info */
    .job-recruiter {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px 0;
        border-top: 1px solid #e9ecef;
        margin-top: 15px;
    }
    
    .recruiter-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        background: linear-gradient(135deg, #0A2463, #1E5AA8);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        flex-shrink: 0;
    }
    
    .recruiter-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .recruiter-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    
    .recruiter-label {
        font-size: 11px;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 500;
    }
    
    .recruiter-name {
        font-size: 14px;
        color: #1a1a2e;
        font-weight: 600;
    }

    /* Featured Section */
    .featured-section {
        padding: 100px 0;
        background: linear-gradient(135deg, #f0f4f8 0%, #e8ecf1 100%);
    }
    
    .section-footer {
        text-align: center;
        margin-top: 50px;
    }

    /* Why Section */
    .why-section {
        padding: 100px 0;
        background: white;
    }
    
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 40px;
    }
    
    .feature-card {
        text-align: center;
        padding: 50px 30px;
        background: linear-gradient(180deg, #fff 0%, #f8f9fa 100%);
        border-radius: 20px;
        transition: all 0.4s ease;
    }
    
    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 50px rgba(10,36,99,0.1);
    }
    
    .feature-icon {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0A2463, #1E5AA8);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        color: white;
        margin: 0 auto 30px;
        box-shadow: 0 15px 40px rgba(10,36,99,0.25);
        transition: all 0.4s ease;
    }
    
    .feature-card:hover .feature-icon {
        transform: scale(1.1);
        box-shadow: 0 20px 50px rgba(10,36,99,0.3);
    }
    
    .feature-card h3 {
        font-size: 22px;
        color: #1a1a2e;
        margin-bottom: 15px;
        font-weight: 600;
    }
    
    .feature-card p {
        color: #6c757d;
        line-height: 1.8;
        font-size: 15px;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .hero-content h1 { font-size: 42px; }
        .hero-stats { gap: 40px; padding: 25px 30px; }
        .hero-stats .number { font-size: 32px; }
    }
    
    @media (max-width: 768px) {
        .hero-section { min-height: 100vh; padding: 100px 20px; }
        .hero-content h1 { font-size: 32px; }
        .hero-content p { font-size: 16px; }
        .hero-actions { flex-direction: column; align-items: center; }
        .hero-stats { flex-direction: column; gap: 25px; padding: 25px; }
        .scroll-indicator { display: none; }
        .section-header h2 { font-size: 32px; }
    }
    </style>
</body>
</html>
