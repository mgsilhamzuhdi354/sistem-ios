<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply - <?= htmlspecialchars($vacancy['title']) ?> - PT Indo Ocean</title>
    <link rel="icon" type="image/jpeg" href="<?= asset('images/logo.jpg') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/applicant.css') ?>">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important; margin: 0; }

        .apply-main {
            background: linear-gradient(160deg, #0f172a 0%, #1e3a5f 35%, #0d9488 70%, #065f46 100%);
            min-height: 100vh;
            padding: 2rem 2.5rem;
            position: relative;
        }

        .apply-card {
            max-width: 700px;
            margin: 0 auto;
            background: rgba(255,255,255,0.97);
            border-radius: 24px;
            padding: 0;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }

        .apply-card-header {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            padding: 2rem 2.5rem;
            color: white;
        }

        .apply-card-header h1 {
            margin: 0 0 0.5rem;
            font-size: 1.5rem;
            font-weight: 800;
        }
        .apply-card-header p {
            margin: 0;
            opacity: 0.85;
            font-size: 0.9rem;
        }

        .apply-card-body {
            padding: 2.5rem;
        }

        /* Recruiter info */
        .recruiter-selected {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 16px;
            margin-bottom: 2rem;
        }
        .recruiter-selected i {
            font-size: 1.5rem;
            color: #16a34a;
        }
        .recruiter-selected .info h4 {
            margin: 0 0 2px;
            font-size: 0.95rem;
            font-weight: 700;
            color: #166534;
        }
        .recruiter-selected .info span {
            font-size: 0.8rem;
            color: #4ade80;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            font-size: 0.88rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }
        .form-group label .optional {
            color: #94a3b8;
            font-weight: 400;
            font-size: 0.8rem;
        }
        .form-group textarea,
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.9rem;
            font-family: inherit;
            transition: border-color 0.2s;
            outline: none;
        }
        .form-group textarea:focus,
        .form-group input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .btn-submit-apply {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #16a34a, #059669);
            border: none;
            border-radius: 16px;
            color: white;
            font-size: 1.05rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(22,163,74,0.3);
            transition: all 0.3s ease;
            font-family: inherit;
        }
        .btn-submit-apply:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(22,163,74,0.45);
        }

        .btn-back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #64748b;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 500;
            margin-top: 1.25rem;
            transition: color 0.2s;
        }
        .btn-back-link:hover { color: #1e293b; }

        .flash-msg {
            max-width: 700px;
            margin: 0 auto 1.5rem;
            padding: 1rem 1.5rem;
            border-radius: 16px;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .flash-msg.success { background: rgba(34,197,94,0.15); color: #16a34a; border: 1px solid rgba(34,197,94,0.3); }
        .flash-msg.error { background: rgba(239,68,68,0.15); color: #dc2626; border: 1px solid rgba(239,68,68,0.3); }
        .flash-msg.warning { background: rgba(245,158,11,0.15); color: #d97706; border: 1px solid rgba(245,158,11,0.3); }

        @media (max-width: 768px) {
            .apply-main { padding: 1rem; }
            .apply-card-body { padding: 1.5rem; }
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
                <li><a href="<?= url('/applicant/dashboard') ?>" class="nav-link"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
                <li><a href="<?= url('/jobs') ?>" class="nav-link active"><i class="fas fa-briefcase"></i><span>Job Vacancies</span></a></li>
                <li><a href="<?= url('/applicant/applications') ?>" class="nav-link"><i class="fas fa-file-alt"></i><span>My Applications</span></a></li>
                <li><a href="<?= url('/applicant/documents') ?>" class="nav-link"><i class="fas fa-folder"></i><span>Documents</span></a></li>
                <li><a href="<?= url('/applicant/interview') ?>" class="nav-link"><i class="fas fa-video"></i><span>Interview</span></a></li>
                <li><a href="<?= url('/applicant/profile') ?>" class="nav-link"><i class="fas fa-user"></i><span>Profile</span></a></li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="<?= url('/logout') ?>" class="logout-btn"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="applicant-main">
        <div class="apply-main">

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

            <div class="apply-card">
                <div class="apply-card-header">
                    <h1><i class="fas fa-paper-plane" style="margin-right:10px;"></i>Kirim Lamaran</h1>
                    <p><?= htmlspecialchars($vacancy['title']) ?> — <?= htmlspecialchars($vacancy['department_name'] ?? 'Umum') ?></p>
                </div>

                <div class="apply-card-body">
                    <!-- Recruiter info -->
                    <?php if (!empty($recruiterName)): ?>
                    <div class="recruiter-selected">
                        <i class="fas fa-user-check"></i>
                        <div class="info">
                            <h4>Perekrut: <?= htmlspecialchars($recruiterName) ?></h4>
                            <span><?= $recruiterType === 'random' ? 'Dipilihkan secara acak' : 'Pilihan Anda' ?></span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= url('/applicant/applications/apply/' . $vacancy['id']) ?>">
                        <?= csrf_field() ?>

                        <div class="form-group">
                            <label>Cover Letter <span class="optional">(opsional)</span></label>
                            <textarea name="cover_letter" placeholder="Ceritakan tentang diri Anda dan mengapa Anda cocok untuk posisi ini..."></textarea>
                        </div>

                        <div class="form-group">
                            <label>Expected Salary <span class="optional">(opsional, USD/bulan)</span></label>
                            <input type="number" name="expected_salary" placeholder="e.g. 2000" step="100">
                        </div>

                        <div class="form-group">
                            <label>Tanggal Tersedia <span class="optional">(opsional)</span></label>
                            <input type="date" name="available_date" min="<?= date('Y-m-d') ?>">
                        </div>

                        <button type="submit" class="btn-submit-apply">
                            <i class="fas fa-paper-plane"></i> Kirim Lamaran Sekarang
                        </button>
                    </form>

                    <div style="text-align:center;">
                        <a href="<?= url('/jobs/' . $vacancy['id']) ?>" class="btn-back-link">
                            <i class="fas fa-arrow-left"></i> Kembali ke Detail Lowongan
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </main>
</body>
</html>
