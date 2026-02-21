<?php
// Contact page
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - PT Indo Ocean Crew Services</title>
    <link rel="icon" type="image/jpeg" href="<?= asset('images/logo.jpg') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <style>
    /* Contact Page Styles */
    .contact-hero {
        background: linear-gradient(135deg, #0A2463 0%, #1E5AA8 50%, #0A2463 100%);
        color: white;
        padding: 120px 0 80px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .contact-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url('<?= asset('images/hero-cargo-ship.png') ?>') center/cover;
        opacity: 0.12;
    }
    .contact-hero .container { position: relative; z-index: 1; }
    .contact-hero h1 {
        font-size: 48px;
        font-weight: 700;
        margin-bottom: 20px;
    }
    .contact-hero p {
        font-size: 20px;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto;
    }

    .contact-section {
        padding: 80px 0;
        background: #f8f9fa;
    }

    .contact-grid {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 50px;
        align-items: start;
    }

    /* Contact Info Cards */
    .contact-info {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .info-card {
        background: white;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.06);
        display: flex;
        align-items: flex-start;
        gap: 20px;
        transition: transform 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
    }
    .info-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 45px rgba(10,36,99,0.1);
    }
    .info-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        background: linear-gradient(135deg, #0A2463, #1E5AA8);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: white;
        flex-shrink: 0;
    }
    .info-content h3 {
        font-size: 18px;
        color: #1a1a2e;
        margin-bottom: 6px;
        font-weight: 600;
    }
    .info-content p {
        color: #6c757d;
        font-size: 15px;
        line-height: 1.6;
    }
    .info-content a {
        color: #1E5AA8;
        text-decoration: none;
        font-weight: 500;
    }
    .info-content a:hover {
        text-decoration: underline;
    }

    /* Contact Form */
    .contact-form-card {
        background: white;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 15px 50px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
    }
    .contact-form-card h2 {
        font-size: 28px;
        color: #0A2463;
        margin-bottom: 8px;
        font-weight: 700;
    }
    .contact-form-card .subtitle {
        color: #6c757d;
        margin-bottom: 30px;
        font-size: 15px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 8px;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 14px 18px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 15px;
        font-family: 'Poppins', sans-serif;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        background: #fafbfc;
        box-sizing: border-box;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #1E5AA8;
        box-shadow: 0 0 0 4px rgba(30,90,168,0.1);
        background: white;
    }
    .form-group textarea {
        resize: vertical;
        min-height: 120px;
    }

    .btn-submit {
        background: linear-gradient(135deg, #0A2463, #1E5AA8);
        color: white;
        padding: 16px 40px;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        font-family: 'Poppins', sans-serif;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(10,36,99,0.25);
    }

    /* Map Section */
    .map-section {
        padding: 0;
        background: white;
    }
    .map-section iframe {
        width: 100%;
        height: 400px;
        border: none;
    }

    /* Working Hours */
    .hours-section {
        padding: 60px 0;
        background: white;
    }
    .hours-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        max-width: 800px;
        margin: 0 auto;
    }
    .hours-card {
        text-align: center;
        padding: 30px;
        background: linear-gradient(135deg, #f8f9fa, #fff);
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.06);
    }
    .hours-card h3 {
        font-size: 18px;
        color: #0A2463;
        margin-bottom: 10px;
    }
    .hours-card p {
        color: #6c757d;
        font-size: 15px;
        line-height: 1.8;
    }

    @media (max-width: 768px) {
        .contact-hero h1 { font-size: 32px; }
        .contact-grid { grid-template-columns: 1fr; }
        .form-row { grid-template-columns: 1fr; }
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
                    <li><a href="<?= url('/about') ?>">About</a></li>
                    <li><a href="<?= url('/contact') ?>" class="active">Contact</a></li>
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
    <section class="contact-hero">
        <div class="container">
            <h1>Hubungi Kami</h1>
            <p>Kami siap membantu Anda. Jangan ragu untuk menghubungi tim kami.</p>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="contact-section">
        <div class="container">
            <div class="contact-grid">
                <!-- Contact Info -->
                <div class="contact-info">
                    <div class="info-card">
                        <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="info-content">
                            <h3>Alamat Kantor</h3>
                            <p>Jakarta, Indonesia</p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
                        <div class="info-content">
                            <h3>Telepon</h3>
                            <p><a href="tel:+622112345678">+62 21 1234 5678</a></p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-icon"><i class="fas fa-envelope"></i></div>
                        <div class="info-content">
                            <h3>Email</h3>
                            <p><a href="mailto:recruitment@indoceancrew.com">recruitment@indoceancrew.com</a></p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-icon"><i class="fab fa-whatsapp"></i></div>
                        <div class="info-content">
                            <h3>WhatsApp</h3>
                            <p><a href="https://wa.me/622112345678" target="_blank">Chat via WhatsApp</a></p>
                        </div>
                    </div>

                    <!-- Working Hours -->
                    <div class="info-card" style="flex-direction: column; gap: 15px;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div class="info-icon"><i class="fas fa-clock"></i></div>
                            <div class="info-content">
                                <h3>Jam Operasional</h3>
                            </div>
                        </div>
                        <div style="padding-left: 76px;">
                            <p style="color: #555; line-height: 1.8; font-size: 14px;">
                                <strong>Senin - Jumat:</strong> 08:00 - 17:00 WIB<br>
                                <strong>Sabtu:</strong> 08:00 - 12:00 WIB<br>
                                <strong>Minggu & Hari Libur:</strong> Tutup
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="contact-form-card">
                    <h2>Kirim Pesan</h2>
                    <p class="subtitle">Silakan isi form di bawah dan kami akan segera merespon.</p>
                    
                    <form id="contactForm" onsubmit="return handleContactSubmit(event)">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Nama Lengkap *</label>
                                <input type="text" id="name" name="name" required placeholder="Masukkan nama lengkap">
                            </div>
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" required placeholder="contoh@email.com">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">No. Telepon</label>
                                <input type="tel" id="phone" name="phone" placeholder="+62 812 xxxx xxxx">
                            </div>
                            <div class="form-group">
                                <label for="subject">Subjek *</label>
                                <select id="subject" name="subject" required>
                                    <option value="">Pilih subjek</option>
                                    <option value="recruitment">Informasi Rekrutmen</option>
                                    <option value="partnership">Kerjasama / Partnership</option>
                                    <option value="complaint">Keluhan / Saran</option>
                                    <option value="general">Pertanyaan Umum</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="message">Pesan *</label>
                            <textarea id="message" name="message" required placeholder="Tulis pesan Anda di sini..."></textarea>
                        </div>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-paper-plane"></i> Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Map -->
    <section class="map-section">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d253840.65295072!2d106.68943!3d-6.229728!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3e945e34b9d%3A0x100! 2sJakarta!5e0!3m2!1sid!2sid!4v1" allowfullscreen loading="lazy"></iframe>
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
    <script>
    function handleContactSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const name = form.querySelector('#name').value;
        const email = form.querySelector('#email').value;
        const subject = form.querySelector('#subject');
        const subjectText = subject.options[subject.selectedIndex].text;
        const message = form.querySelector('#message').value;
        const phone = form.querySelector('#phone').value;
        
        // Build mailto link
        const mailBody = `Nama: ${name}\nEmail: ${email}\nTelepon: ${phone}\n\nPesan:\n${message}`;
        const mailtoLink = `mailto:recruitment@indoceancrew.com?subject=${encodeURIComponent(subjectText + ' - ' + name)}&body=${encodeURIComponent(mailBody)}`;
        
        window.location.href = mailtoLink;
        
        // Show confirmation
        alert('Terima kasih! Silakan kirim email melalui aplikasi email Anda yang telah dibuka.');
        return false;
    }
    </script>
</body>
</html>
