<?php
// This page uses the 'main' layout
$layout = 'main';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - PT Indo Ocean Crew Services</title>
    <link rel="icon" type="image/jpeg" href="<?= asset('images/logo.jpg') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <style>
    /* About Page Styles */
    .about-hero {
        background: linear-gradient(135deg, #0A2463 0%, #1E5AA8 50%, #0A2463 100%);
        color: white;
        padding: 120px 0 80px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .about-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url('<?= asset('images/hero-cargo-ship.png') ?>') center/cover;
        opacity: 0.15;
    }
    .about-hero .container { position: relative; z-index: 1; }
    .about-hero h1 {
        font-size: 48px;
        font-weight: 700;
        margin-bottom: 20px;
    }
    .about-hero p {
        font-size: 20px;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto;
    }

    .about-section {
        padding: 80px 0;
    }
    .about-section:nth-child(even) {
        background: #f8f9fa;
    }

    .about-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
    }
    .about-grid.reverse { direction: rtl; }
    .about-grid.reverse > * { direction: ltr; }

    .about-text h2 {
        font-size: 36px;
        color: #0A2463;
        margin-bottom: 20px;
        font-weight: 700;
    }
    .about-text p {
        font-size: 16px;
        color: #555;
        line-height: 1.8;
        margin-bottom: 15px;
    }

    .about-image {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    .about-image img {
        width: 100%;
        height: 400px;
        object-fit: cover;
    }

    /* Values Grid */
    .values-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        margin-top: 50px;
    }
    .value-card {
        background: white;
        padding: 40px 30px;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.06);
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
    }
    .value-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 50px rgba(10,36,99,0.12);
    }
    .value-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0A2463, #1E5AA8);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 28px;
        color: white;
    }
    .value-card h3 {
        font-size: 20px;
        color: #1a1a2e;
        margin-bottom: 12px;
    }
    .value-card p {
        color: #6c757d;
        line-height: 1.7;
        font-size: 15px;
    }

    /* Certifications */
    .cert-badges {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        margin-top: 25px;
    }
    .cert-badge {
        display: flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, #0A2463, #1E5AA8);
        color: white;
        padding: 12px 24px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 500;
    }
    .cert-badge i {
        color: #D4AF37;
    }

    /* Stats Bar */
    .stats-bar {
        background: linear-gradient(135deg, #0A2463, #1E5AA8);
        padding: 60px 0;
        color: white;
    }
    .stats-row {
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;
        gap: 30px;
    }
    .stat-item {
        text-align: center;
    }
    .stat-item .number {
        font-size: 48px;
        font-weight: 700;
        display: block;
        background: linear-gradient(135deg, #D4AF37, #FFD700);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .stat-item .label {
        font-size: 14px;
        opacity: 0.85;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: 8px;
        display: block;
    }

    @media (max-width: 768px) {
        .about-hero h1 { font-size: 32px; }
        .about-grid { grid-template-columns: 1fr; gap: 30px; }
        .about-grid.reverse { direction: ltr; }
        .stat-item .number { font-size: 36px; }
    }
    </style>
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
                    <li><a href="<?= url('/jobs') ?>">Jobs</a></li>
                    <li><a href="<?= url('/about') ?>" class="active">About</a></li>
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
                <button class="hamburger" id="hamburger">
                    <span></span><span></span><span></span>
                </button>
            </nav>
        </div>
    </header>

    <!-- Hero -->
    <section class="about-hero">
        <div class="container">
            <h1>About PT Indo Ocean Crew Services</h1>
            <p>Professional maritime crew recruitment and management services since 2010</p>
        </div>
    </section>

    <!-- Company Overview -->
    <section class="about-section">
        <div class="container">
            <div class="about-grid">
                <div class="about-text">
                    <h2>Siapa Kami</h2>
                    <p>PT Indo Ocean Crew Services adalah perusahaan penyedia jasa awak kapal profesional yang berkomitmen menyediakan tenaga kerja maritim berkualitas tinggi untuk industri pelayaran nasional dan internasional.</p>
                    <p>Dengan pengalaman lebih dari 14 tahun, kami telah berhasil menempatkan ribuan pelaut terampil di berbagai jenis kapal, mulai dari kapal tanker, cargo, offshore, hingga kapal pesiar.</p>
                    <div class="cert-badges">
                        <span class="cert-badge"><i class="fas fa-certificate"></i> SIUPPAK</span>
                        <span class="cert-badge"><i class="fas fa-check-circle"></i> ISO 9001:2015</span>
                        <span class="cert-badge"><i class="fas fa-shield-alt"></i> MLC 2006</span>
                    </div>
                </div>
                <div class="about-image">
                    <img src="<?= asset('images/hero-cargo-ship.png') ?>" alt="Maritime Operations">
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Bar -->
    <section class="stats-bar">
        <div class="container">
            <div class="stats-row">
                <div class="stat-item">
                    <span class="number">14+</span>
                    <span class="label">Tahun Pengalaman</span>
                </div>
                <div class="stat-item">
                    <span class="number">5000+</span>
                    <span class="label">Pelaut Ditempatkan</span>
                </div>
                <div class="stat-item">
                    <span class="number">50+</span>
                    <span class="label">Partner Perusahaan</span>
                </div>
                <div class="stat-item">
                    <span class="number">24/7</span>
                    <span class="label">Dukungan</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Vision & Mission -->
    <section class="about-section">
        <div class="container">
            <div class="about-grid reverse">
                <div class="about-text">
                    <h2>Visi & Misi</h2>
                    <p><strong>Visi:</strong> Menjadi perusahaan penyedia awak kapal terbaik dan terpercaya di Indonesia yang diakui secara internasional.</p>
                    <p><strong>Misi:</strong></p>
                    <p>• Menyediakan tenaga kerja maritim yang kompeten, profesional, dan berdedikasi<br>
                    • Menjalin kemitraan jangka panjang dengan perusahaan pelayaran global<br>
                    • Meningkatkan kesejahteraan dan pengembangan karier pelaut Indonesia<br>
                    • Menerapkan standar keselamatan dan kualitas internasional</p>
                </div>
                <div class="about-image">
                    <img src="<?= asset('images/hero-cargo-ship.png') ?>" alt="Our Vision">
                </div>
            </div>
        </div>
    </section>

    <!-- Values -->
    <section class="about-section">
        <div class="container">
            <div class="section-header" style="text-align:center; margin-bottom:20px;">
                <h2 style="font-size:36px; color:#0A2463; font-weight:700;">Nilai-Nilai Kami</h2>
                <p style="color:#6c757d; font-size:18px;">Prinsip yang kami pegang teguh dalam setiap layanan</p>
            </div>
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon"><i class="fas fa-handshake"></i></div>
                    <h3>Integritas</h3>
                    <p>Kami menjalankan bisnis dengan kejujuran dan transparansi penuh kepada semua pihak.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon"><i class="fas fa-award"></i></div>
                    <h3>Profesionalisme</h3>
                    <p>Standar tinggi dalam rekrutmen, pelatihan, dan penempatan awak kapal.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon"><i class="fas fa-users"></i></div>
                    <h3>Kepedulian</h3>
                    <p>Kesejahteraan pelaut dan kepuasan klien adalah prioritas utama kami.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon"><i class="fas fa-lightbulb"></i></div>
                    <h3>Inovasi</h3>
                    <p>Terus berinovasi dengan teknologi terbaru untuk proses rekrutmen yang lebih efisien.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section" style="background: linear-gradient(135deg, #0A2463, #1E5AA8); padding: 80px 0; text-align: center; color: white;">
        <div class="container">
            <h2 style="font-size: 36px; margin-bottom: 20px;">Bergabunglah Bersama Kami</h2>
            <p style="font-size: 18px; opacity: 0.9; margin-bottom: 30px; max-width: 500px; margin-left: auto; margin-right: auto;">Mulai karier maritim Anda bersama PT Indo Ocean Crew Services.</p>
            <a href="<?= url('/register') ?>" class="btn btn-light btn-lg" style="background: linear-gradient(135deg, #D4AF37, #FFD700); color: #1a1a2e; padding: 16px 40px; border-radius: 50px; font-weight: 600; text-decoration: none; display: inline-block;">
                <i class="fas fa-rocket"></i> Daftar Sekarang
            </a>
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

    <script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
