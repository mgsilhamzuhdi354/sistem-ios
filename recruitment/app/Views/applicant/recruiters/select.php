<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - PT Indo Ocean</title>
    <link rel="icon" type="image/jpeg" href="<?= asset('images/logo.jpg') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/applicant.css') ?>">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important; margin: 0; }

        /* ── Main Content Area ── */
        .recruiter-main {
            background: linear-gradient(160deg, #0f172a 0%, #1e3a5f 35%, #0d9488 70%, #065f46 100%);
            min-height: 100vh;
            padding: 2rem 2.5rem;
            position: relative;
            overflow-x: hidden;
        }
        .recruiter-main::before {
            content: '';
            position: absolute;
            top: -200px; right: -200px;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(14,165,233,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .recruiter-main::after {
            content: '';
            position: absolute;
            bottom: -150px; left: -100px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(168,85,247,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        /* ── Hero Banner ── */
        .hero-banner {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 28px;
            padding: 2.5rem 2.5rem 2rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        .hero-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            z-index: -1;
        }
        .hero-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .hero-banner h1 {
            font-size: 2.2rem;
            font-weight: 800;
            color: white;
            margin: 0 0 0.4rem;
            letter-spacing: -0.02em;
        }
        .hero-banner h1 i { margin-right: 0.75rem; opacity: 0.9; }
        .hero-banner p {
            color: rgba(255,255,255,0.7);
            font-size: 1rem;
            margin: 0;
        }
        .btn-back {
            padding: 10px 22px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 14px;
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.25s ease;
            flex-shrink: 0;
        }
        .btn-back:hover { background: rgba(255,255,255,0.2); color: white; transform: translateY(-1px); }

        /* ── Vacancy Badge ── */
        .vacancy-badge-modern {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 16px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .vacancy-icon {
            width: 48px; height: 48px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .vacancy-badge-modern h4 {
            margin: 0 0 2px;
            color: white;
            font-size: 1.05rem;
            font-weight: 700;
        }
        .vacancy-badge-modern span {
            color: rgba(255,255,255,0.6);
            font-size: 0.82rem;
        }

        /* ── Random Selection CTA ── */
        .random-cta {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
        }
        .btn-random {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 16px 40px;
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            border-radius: 50px;
            color: white;
            font-size: 1.1rem;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 8px 25px rgba(16,185,129,0.35);
            transition: all 0.3s ease;
            animation: gentlePulse 3s ease-in-out infinite;
        }
        .btn-random:hover {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 12px 35px rgba(16,185,129,0.5);
            animation: none;
        }
        @keyframes gentlePulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }
        .random-cta p {
            color: rgba(255,255,255,0.6);
            font-size: 0.85rem;
            margin: 0.75rem 0 0;
        }
        .random-cta p i { margin-right: 4px; }

        /* ── Divider ── */
        .section-divider {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
        }
        .section-divider span {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 8px 28px;
            border-radius: 30px;
            color: rgba(255,255,255,0.7);
            font-size: 0.85rem;
            font-weight: 600;
            border: 1px solid rgba(255,255,255,0.15);
        }

        /* ── Recruiter Grid ── */
        .recruiter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 1.5rem;
            position: relative;
            z-index: 1;
        }

        /* ── Recruiter Card ── */
        .recruiter-card-modern {
            background: rgba(255,255,255,0.97);
            border-radius: 24px;
            padding: 0;
            overflow: hidden;
            box-shadow: 0 12px 40px rgba(0,0,0,0.12);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
        }
        .recruiter-card-modern:hover {
            transform: translateY(-8px) scale(1.01);
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }
        .card-accent {
            height: 6px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899);
            transition: height 0.3s ease;
        }
        .recruiter-card-modern:hover .card-accent { height: 8px; }
        .card-body-modern {
            padding: 2rem;
        }
        .card-top {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            margin-bottom: 1.25rem;
        }
        .recruiter-avatar {
            width: 80px; height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: 800;
            color: white;
            flex-shrink: 0;
            border: 4px solid white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        .recruiter-card-modern:hover .recruiter-avatar {
            transform: scale(1.08);
            box-shadow: 0 6px 20px rgba(0,0,0,0.18);
        }
        .recruiter-avatar img {
            width: 100%; height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        .recruiter-info h3 {
            margin: 0 0 4px;
            font-size: 1.15rem;
            font-weight: 700;
            color: #1e293b;
        }
        .badge-online-m {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #dcfce7;
            color: #16a34a;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .badge-online-m i { font-size: 0.5rem; animation: blink 2s infinite; }
        .badge-offline-m {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            color: #94a3b8;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .badge-offline-m i { font-size: 0.5rem; }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
        .recruiter-spec {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #64748b;
            font-size: 0.85rem;
            margin-top: 6px;
        }
        .recruiter-spec i { color: #f59e0b; }

        /* ── Bio ── */
        .recruiter-bio {
            color: #64748b;
            font-size: 0.88rem;
            line-height: 1.6;
            margin-bottom: 1.25rem;
            padding: 0.75rem 1rem;
            background: #f8fafc;
            border-radius: 12px;
            border-left: 3px solid #e2e8f0;
        }

        /* ── Rating ── */
        .rating-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1rem;
        }
        .rating-stars-m { color: #fbbf24; font-size: 1rem; }
        .rating-stars-m .empty { color: #e2e8f0; }
        .rating-count {
            color: #94a3b8;
            font-size: 0.8rem;
        }

        /* ── Workload Bar ── */
        .workload-section { margin-bottom: 1.5rem; }
        .workload-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }
        .workload-header span {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 500;
        }
        .workload-header strong {
            font-size: 0.85rem;
            color: #1e293b;
        }
        .workload-track {
            height: 10px;
            background: #f1f5f9;
            border-radius: 10px;
            overflow: hidden;
        }
        .workload-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 1.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        .workload-fill.low { background: linear-gradient(90deg, #22c55e, #16a34a); }
        .workload-fill.medium { background: linear-gradient(90deg, #f59e0b, #d97706); }
        .workload-fill.high { background: linear-gradient(90deg, #ef4444, #dc2626); }
        .workload-fill::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shimmer 2.5s infinite;
        }
        @keyframes shimmer { 0% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }

        /* ── Select Button ── */
        .btn-select-recruiter {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border: none;
            border-radius: 16px;
            color: white;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(79,70,229,0.3);
            transition: all 0.3s ease;
            font-family: inherit;
        }
        .btn-select-recruiter:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79,70,229,0.45);
            background: linear-gradient(135deg, #4338ca, #6d28d9);
        }

        /* ── Empty State ── */
        .empty-state-recruiter {
            background: rgba(255,255,255,0.95);
            border-radius: 24px;
            padding: 3.5rem 2rem;
            text-align: center;
            box-shadow: 0 12px 40px rgba(0,0,0,0.1);
            position: relative;
            z-index: 1;
        }
        .empty-state-recruiter i {
            font-size: 3.5rem;
            color: #f59e0b;
            margin-bottom: 1rem;
        }
        .empty-state-recruiter h3 {
            color: #1e293b;
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0 0 0.5rem;
        }
        .empty-state-recruiter p {
            color: #64748b;
            font-size: 0.9rem;
            margin: 0;
        }

        /* ── Flash Messages ── */
        .flash-msg {
            padding: 1rem 1.5rem;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            z-index: 1;
        }
        .flash-msg.success { background: rgba(34,197,94,0.15); color: #16a34a; border: 1px solid rgba(34,197,94,0.3); }
        .flash-msg.error { background: rgba(239,68,68,0.15); color: #dc2626; border: 1px solid rgba(239,68,68,0.3); }
        .flash-msg.warning { background: rgba(245,158,11,0.15); color: #d97706; border: 1px solid rgba(245,158,11,0.3); }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .recruiter-main { padding: 1rem 1.25rem; }
            .hero-banner { padding: 1.5rem; border-radius: 20px; }
            .hero-banner h1 { font-size: 1.5rem; }
            .hero-top { flex-direction: column; }
            .recruiter-grid { grid-template-columns: 1fr; }
            .card-body-modern { padding: 1.5rem; }
            .card-top { flex-direction: column; text-align: center; }
            .btn-random { padding: 14px 30px; font-size: 1rem; }
        }
    </style>
</head>
<body class="applicant-body">
    <!-- Sidebar -->
    <aside class="applicant-sidebar">
        <div class="sidebar-header">
            <a href="<?= url('/') ?>" class="logo">
                <img src="<?= asset('images/logo.jpg') ?>" alt="Indo Ocean" style="width:32px;height:32px;object-fit:contain;">
                <span>Indo Ocean</span>
            </a>
        </div>
        
        <nav class="sidebar-nav">
            <ul>
                <li>
                    <a href="<?= url('/applicant/dashboard') ?>" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/jobs') ?>" class="nav-link active">
                        <i class="fas fa-briefcase"></i>
                        <span>Job Vacancies</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/applicant/applications') ?>" class="nav-link">
                        <i class="fas fa-file-alt"></i>
                        <span>My Applications</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/applicant/documents') ?>" class="nav-link">
                        <i class="fas fa-folder"></i>
                        <span>Documents</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/applicant/interview') ?>" class="nav-link">
                        <i class="fas fa-video"></i>
                        <span>Interview</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/applicant/profile') ?>" class="nav-link">
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="sidebar-footer">
            <a href="<?= url('/logout') ?>" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="applicant-main">
        <div class="recruiter-main">

            <!-- Flash Messages -->
            <?php if ($success = flash('success')): ?>
                <div class="flash-msg success"><i class="fas fa-check-circle"></i> <?= $success ?></div>
            <?php endif; ?>
            <?php if ($error = flash('error')): ?>
                <div class="flash-msg error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
            <?php endif; ?>
            <?php if ($warning = flash('warning')): ?>
                <div class="flash-msg warning"><i class="fas fa-exclamation-triangle"></i> <?= $warning ?></div>
            <?php endif; ?>

            <!-- Hero Banner -->
            <div class="hero-banner">
                <div class="hero-top">
                    <div>
                        <h1><i class="fas fa-user-tie"></i>Pilih Rekruter Anda</h1>
                        <p>Pilih perekrut yang akan memandu proses rekrutmen Anda secara personal</p>
                    </div>
                    <a href="<?= url('/jobs/' . $vacancy['id']) ?>" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="vacancy-badge-modern">
                    <div class="vacancy-icon"><i class="fas fa-briefcase"></i></div>
                    <div>
                        <h4><?= htmlspecialchars($vacancy['title']) ?></h4>
                        <span><i class="fas fa-building" style="margin-right:4px;"></i><?= htmlspecialchars($vacancy['department_name'] ?? 'Umum') ?></span>
                    </div>
                </div>
            </div>

            <!-- Random Selection CTA -->
            <div class="random-cta">
                <a href="<?= url('/applicant/random-recruiter/' . $vacancy['id']) ?>" class="btn-random">
                    <i class="fas fa-shuffle"></i> Pilihkan Secara Acak
                </a>
                <p><i class="fas fa-info-circle"></i> Kami akan menugaskan Anda ke rekruter dengan beban kerja terendah</p>
            </div>

            <!-- Divider -->
            <div class="section-divider">
                <span>— ATAU Pilih Manual —</span>
            </div>

            <!-- Recruiters Grid -->
            <?php if (empty($recruiters)): ?>
                <div class="empty-state-recruiter">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>Tidak Ada Rekruter Tersedia</h3>
                    <p>Semua rekruter sedang penuh saat ini. Silakan coba lagi nanti atau gunakan pilihan Acak.</p>
                </div>
            <?php else: ?>
                <div class="recruiter-grid">
                    <?php 
                    $avatarGradients = [
                        'linear-gradient(135deg, #3b82f6, #1d4ed8)',
                        'linear-gradient(135deg, #8b5cf6, #7c3aed)',
                        'linear-gradient(135deg, #ec4899, #be185d)',
                        'linear-gradient(135deg, #f59e0b, #d97706)',
                        'linear-gradient(135deg, #10b981, #059669)',
                        'linear-gradient(135deg, #06b6d4, #0891b2)',
                    ];
                    foreach ($recruiters as $index => $recruiter): 
                        $initial = strtoupper(substr($recruiter['full_name'], 0, 1));
                        $gradient = $avatarGradients[$index % count($avatarGradients)];
                        $workloadPercent = min($recruiter['workload_percent'], 100);
                        $workloadClass = $workloadPercent < 50 ? 'low' : ($workloadPercent < 80 ? 'medium' : 'high');
                        $rating = round($recruiter['avg_rating']);
                        $isOnline = !empty($recruiter['is_online']);
                        $hasPhoto = !empty($recruiter['photo']);
                        $hasAvatar = !empty($recruiter['avatar']);
                    ?>
                    <div class="recruiter-card-modern">
                        <div class="card-accent"></div>
                        <div class="card-body-modern">
                            <div class="card-top">
                                <div class="recruiter-avatar" style="background: <?= $gradient ?>;">
                                    <?php if ($hasPhoto): ?>
                                        <img src="<?= asset('uploads/recruiters/' . $recruiter['photo']) ?>" 
                                             alt="<?= htmlspecialchars($recruiter['full_name']) ?>">
                                    <?php elseif ($hasAvatar): ?>
                                        <img src="<?= asset($recruiter['avatar']) ?>" 
                                             alt="<?= htmlspecialchars($recruiter['full_name']) ?>">
                                    <?php else: ?>
                                        <?= $initial ?>
                                    <?php endif; ?>
                                </div>
                                <div class="recruiter-info">
                                    <h3><?= htmlspecialchars($recruiter['full_name']) ?></h3>
                                    <?php if ($isOnline): ?>
                                        <span class="badge-online-m"><i class="fas fa-circle"></i> Online</span>
                                    <?php else: ?>
                                        <span class="badge-offline-m"><i class="fas fa-circle"></i> Offline</span>
                                    <?php endif; ?>
                                    <?php if (!empty($recruiter['specialization'])): ?>
                                    <div class="recruiter-spec">
                                        <i class="fas fa-star"></i>
                                        <?= htmlspecialchars($recruiter['specialization']) ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if (!empty($recruiter['bio'])): ?>
                            <div class="recruiter-bio">
                                <?= htmlspecialchars(substr($recruiter['bio'], 0, 120)) ?><?= strlen($recruiter['bio']) > 120 ? '...' : '' ?>
                            </div>
                            <?php endif; ?>

                            <!-- Rating -->
                            <div class="rating-row">
                                <span class="rating-stars-m">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="<?= $i <= $rating ? 'fas' : 'far empty' ?> fa-star"></i>
                                    <?php endfor; ?>
                                </span>
                                <span class="rating-count">(<?= $recruiter['total_ratings'] ?> ulasan)</span>
                            </div>

                            <!-- Workload -->
                            <div class="workload-section">
                                <div class="workload-header">
                                    <span>Beban Kerja</span>
                                    <strong><?= $recruiter['active_count'] ?>/<?= $recruiter['max_applications'] ?: 50 ?></strong>
                                </div>
                                <div class="workload-track">
                                    <div class="workload-fill <?= $workloadClass ?>" style="width: 0%" data-width="<?= $workloadPercent ?>%"></div>
                                </div>
                            </div>

                            <!-- Select Button -->
                            <form method="POST" action="<?= url('/applicant/select-recruiter/' . $vacancy['id'] . '/' . $recruiter['id']) ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-select-recruiter">
                                    <i class="fas fa-check-circle"></i> Pilih Rekruter Ini
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <script>
        // Animate workload bars on load
        window.addEventListener('load', () => {
            setTimeout(() => {
                document.querySelectorAll('.workload-fill').forEach(bar => {
                    bar.style.width = bar.dataset.width;
                });
            }, 300);
        });

        // Card entrance animation
        document.querySelectorAll('.recruiter-card-modern').forEach((card, i) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 200 + (i * 120));
        });
    </script>
</body>
</html>
