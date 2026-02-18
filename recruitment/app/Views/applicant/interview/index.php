<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Interview — PT Indo Ocean Crew Services</title>
    <link rel="icon" type="image/jpeg" href="<?= asset('images/logo.jpg') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/applicant.css') ?>">
    <style>
        .interview-page {
            padding: 2rem;
            min-height: 100vh;
            background: linear-gradient(135deg, #0a0e1a 0%, #111827 100%);
            position: relative;
            overflow: hidden;
        }
        .interview-page::before {
            content: '';
            position: absolute;
            top: -200px;
            right: -200px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(99,102,241,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        .interview-page::after {
            content: '';
            position: absolute;
            bottom: -150px;
            left: -100px;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(139,92,246,0.06) 0%, transparent 70%);
            border-radius: 50%;
        }

        /* Hero */
        .ai-hero {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 3rem 2rem 2rem;
            margin-bottom: 2.5rem;
        }
        .ai-hero .robot-icon {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
            color: white;
            box-shadow: 0 8px 40px rgba(99,102,241,0.4);
            animation: heroFloat 3s ease-in-out infinite;
        }
        @keyframes heroFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        .ai-hero h1 {
            font-family: 'Inter', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            color: white;
            margin-bottom: 0.75rem;
            letter-spacing: -0.02em;
        }
        .ai-hero p {
            color: rgba(255,255,255,0.45);
            font-size: 1rem;
            max-width: 500px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Alerts */
        .alert-area { position: relative; z-index:1; max-width: 700px; margin: 0 auto 2rem; }
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.875rem;
            backdrop-filter: blur(10px);
            border: 1px solid;
        }
        .alert-success { background: rgba(16,185,129,0.1); border-color: rgba(16,185,129,0.2); color: #10b981; }
        .alert-info { background: rgba(59,130,246,0.1); border-color: rgba(59,130,246,0.2); color: #60a5fa; }
        .alert-warning { background: rgba(245,158,11,0.1); border-color: rgba(245,158,11,0.2); color: #f59e0b; }

        /* Session Cards Grid */
        .sessions-grid {
            position: relative;
            z-index: 1;
            display: grid;
            gap: 1.5rem;
            max-width: 900px;
            margin: 0 auto;
        }

        /* Session Card */
        .session-card {
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .session-card:hover {
            transform: translateY(-4px);
            border-color: rgba(99,102,241,0.2);
            box-shadow: 0 12px 40px rgba(99,102,241,0.1);
        }

        /* Retry Banner */
        .retry-banner {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
            font-weight: 700;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .card-inner { padding: 1.75rem; }
        .card-top-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }
        .card-top-row .job-title {
            font-family: 'Inter', sans-serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.35rem;
        }
        .card-top-row .bank-name {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.4);
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        /* Status Badges */
        .status-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.4rem 0.9rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border: 1px solid;
        }
        .status-chip.pending {
            background: rgba(245,158,11,0.1);
            border-color: rgba(245,158,11,0.3);
            color: #f59e0b;
        }
        .status-chip.in_progress {
            background: rgba(59,130,246,0.1);
            border-color: rgba(59,130,246,0.3);
            color: #60a5fa;
        }
        .status-chip.completed {
            background: rgba(16,185,129,0.1);
            border-color: rgba(16,185,129,0.3);
            color: #10b981;
        }
        .status-chip.expired {
            background: rgba(239,68,68,0.1);
            border-color: rgba(239,68,68,0.3);
            color: #ef4444;
        }
        .status-chip .dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        /* Details Row */
        .details-row {
            display: flex;
            gap: 1.5rem;
            padding: 1.25rem 0;
            border-top: 1px solid rgba(255,255,255,0.06);
            border-bottom: 1px solid rgba(255,255,255,0.06);
            margin-bottom: 1.5rem;
        }
        .detail-pill {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
            color: rgba(255,255,255,0.5);
        }
        .detail-pill i {
            width: 28px; height: 28px;
            background: rgba(99,102,241,0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #818cf8;
            font-size: 0.7rem;
        }

        /* Score Display */
        .score-area {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            margin-bottom: 1.5rem;
            padding: 1rem 1.25rem;
            background: rgba(255,255,255,0.03);
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,0.06);
        }
        .score-ring {
            width: 70px; height: 70px;
            position: relative;
            flex-shrink: 0;
        }
        .score-ring svg { width: 100%; height: 100%; transform: rotate(-90deg); }
        .score-ring .bg { fill: none; stroke: rgba(255,255,255,0.08); stroke-width: 6; }
        .score-ring .fill-ring { fill: none; stroke-width: 6; stroke-linecap: round; }
        .score-ring .val {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            font-size: 1.25rem;
            font-weight: 800;
            color: white;
        }
        .score-ring .val small { font-size: 0.6rem; font-weight: 400; color: rgba(255,255,255,0.4); display:block; }
        .score-text h4 { color: white; font-size: 1rem; margin: 0 0 0.25rem; }
        .score-text p { color: rgba(255,255,255,0.4); font-size: 0.8rem; margin: 0; }

        /* Action Buttons */
        .btn-action {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            width: 100%;
            padding: 1rem 1.5rem;
            border: none;
            border-radius: 14px;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        .btn-action::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 200%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.08), transparent);
            transition: left 0.5s;
        }
        .btn-action:hover::before { left: 100%; }
        .btn-start-interview {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
        }
        .btn-start-interview:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(99,102,241,0.4);
        }
        .btn-retry-interview {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            animation: retryGlow 2s infinite;
        }
        @keyframes retryGlow {
            0%,100%{ box-shadow: 0 0 0 0 rgba(245,158,11,0.3); }
            50%{ box-shadow: 0 0 20px 8px rgba(245,158,11,0); }
        }
        .btn-retry-interview:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(245,158,11,0.4);
        }
        .btn-completed {
            background: rgba(16,185,129,0.1);
            border: 1px solid rgba(16,185,129,0.2);
            color: #10b981;
            cursor: default;
        }
        .btn-expired {
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.2);
            color: #ef4444;
            cursor: default;
        }

        /* Empty State */
        .empty-state {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 4rem 2rem;
            max-width: 500px;
            margin: 0 auto;
        }
        .empty-state .empty-icon {
            width: 100px; height: 100px;
            background: rgba(99,102,241,0.08);
            border: 1px solid rgba(99,102,241,0.15);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
            color: rgba(99,102,241,0.5);
        }
        .empty-state h3 {
            color: white;
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }
        .empty-state p {
            color: rgba(255,255,255,0.4);
            line-height: 1.6;
            font-size: 0.9rem;
        }

        /* Language Selector */
        .lang-select {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            color: white;
            padding: 0.4rem 0.6rem;
            font-size: 0.8rem;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
        }
        .lang-select option { background: #1f2937; color: white; }

        @media (max-width: 768px) {
            .interview-page { padding: 1rem; }
            .ai-hero { padding: 2rem 1rem; }
            .ai-hero h1 { font-size: 1.5rem; }
            .details-row { flex-wrap: wrap; gap: 0.75rem; }
        }
    </style>
</head>
<body class="applicant-body">
    <aside class="applicant-sidebar">
        <div class="sidebar-header">
            <a href="<?= url('/') ?>" class="logo"><img src="<?= asset('images/logo.jpg') ?>" alt="Indo Ocean" style="width:32px;height:32px;object-fit:contain;"><span>Indo Ocean</span></a>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="<?= url('/applicant/dashboard') ?>" class="nav-link"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
                <li><a href="<?= url('/jobs') ?>" class="nav-link"><i class="fas fa-briefcase"></i><span>Job Vacancies</span></a></li>
                <li><a href="<?= url('/applicant/applications') ?>" class="nav-link"><i class="fas fa-file-alt"></i><span>My Applications</span></a></li>
                <li><a href="<?= url('/applicant/documents') ?>" class="nav-link"><i class="fas fa-folder"></i><span>Documents</span></a></li>
                <li><a href="<?= url('/applicant/interview') ?>" class="nav-link active"><i class="fas fa-robot"></i><span>AI Interview</span></a></li>
                <li><a href="<?= url('/applicant/profile') ?>" class="nav-link"><i class="fas fa-user"></i><span>Profile</span></a></li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="<?= url('/logout') ?>" class="logout-btn"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>
    </aside>

    <main class="applicant-main">
        <div class="interview-page">
            <!-- Hero -->
            <div class="ai-hero">
                <div class="robot-icon"><i class="fas fa-robot"></i></div>
                <h1>AI Interview Center</h1>
                <p>Complete your AI-powered interview sessions below. Answer questions thoughtfully — our AI will evaluate your responses.</p>
            </div>

            <!-- Alerts -->
            <div class="alert-area">
                <?php if ($success = flash('success')): ?>
                    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div>
                <?php endif; ?>
                <?php if ($info = flash('info')): ?>
                    <div class="alert alert-info"><i class="fas fa-info-circle"></i> <?= $info ?></div>
                <?php endif; ?>
                <?php if ($warning = flash('warning')): ?>
                    <div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> <?= $warning ?></div>
                <?php endif; ?>
            </div>

            <?php if (empty($sessions)): ?>
                <div class="empty-state">
                    <div class="empty-icon"><i class="fas fa-robot"></i></div>
                    <h3>No Interview Sessions Yet</h3>
                    <p>After your application reaches the interview stage, AI interview sessions will appear here. Stay tuned!</p>
                </div>
            <?php else: ?>
                <div class="sessions-grid">
                    <?php foreach ($sessions as $session): ?>
                        <?php 
                        $isRetry = isset($session['retry_count']) && $session['retry_count'] > 0;
                        $retryCount = $session['retry_count'] ?? 0;
                        $score = $session['total_score'] ?? 0;
                        $circumference = 2 * 3.14159 * 28;
                        $offset = $circumference - ($score / 100) * $circumference;
                        $strokeColor = $score >= 80 ? '#10b981' : ($score >= 50 ? '#f59e0b' : '#ef4444');
                        ?>
                        <div class="session-card">
                            <?php if ($isRetry && $session['status'] !== 'completed'): ?>
                                <div class="retry-banner">
                                    <i class="fas fa-redo"></i>
                                    RETRY INTERVIEW — Attempt #<?= $retryCount + 1 ?>
                                </div>
                            <?php endif; ?>

                            <div class="card-inner">
                                <div class="card-top-row">
                                    <div>
                                        <div class="job-title"><?= htmlspecialchars($session['vacancy_title']) ?></div>
                                        <div class="bank-name">
                                            <i class="fas fa-database"></i>
                                            <?= htmlspecialchars($session['question_bank_name']) ?>
                                        </div>
                                    </div>
                                    <div class="status-chip <?= $session['status'] ?>">
                                        <span class="dot"></span>
                                        <?php if ($session['status'] === 'pending'): ?>Not Started
                                        <?php elseif ($session['status'] === 'in_progress'): ?>In Progress
                                        <?php elseif ($session['status'] === 'completed'): ?>Completed
                                        <?php else: ?>Expired<?php endif; ?>
                                    </div>
                                </div>

                                <div class="details-row">
                                    <div class="detail-pill">
                                        <i class="fas fa-question-circle"></i>
                                        <span><?= $session['total_questions'] ?> Questions</span>
                                    </div>
                                    <div class="detail-pill">
                                        <i class="fas fa-clock"></i>
                                        <span>
                                            <?php if ($session['status'] === 'completed' && $session['completed_at']): ?>
                                                Completed <?= date('d M Y', strtotime($session['completed_at'])) ?>
                                            <?php elseif ($session['expires_at']): ?>
                                                Deadline: <?= date('d M Y', strtotime($session['expires_at'])) ?>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <?php if ($isRetry): ?>
                                    <div class="detail-pill">
                                        <i class="fas fa-history"></i>
                                        <span>Retried <?= $retryCount ?>x</span>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <?php if ($session['status'] === 'completed' && $session['total_score'] !== null): ?>
                                <div class="score-area">
                                    <div class="score-ring">
                                        <svg viewBox="0 0 70 70">
                                            <circle class="bg" cx="35" cy="35" r="28"/>
                                            <circle class="fill-ring" cx="35" cy="35" r="28"
                                                stroke="<?= $strokeColor ?>"
                                                stroke-dasharray="<?= $circumference ?>"
                                                stroke-dashoffset="<?= $offset ?>"/>
                                        </svg>
                                        <div class="val">
                                            <?= $score ?>
                                            <small>/ 100</small>
                                        </div>
                                    </div>
                                    <div class="score-text">
                                        <?php if ($score >= 80): ?>
                                            <h4 style="color:#10b981;">Excellent!</h4>
                                            <p>Outstanding performance</p>
                                        <?php elseif ($score >= 60): ?>
                                            <h4 style="color:#f59e0b;">Good</h4>
                                            <p>Solid performance with room to grow</p>
                                        <?php else: ?>
                                            <h4 style="color:#ef4444;">Needs Improvement</h4>
                                            <p>Consider reviewing your responses</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div>
                                    <?php if ($session['status'] === 'pending' || $session['status'] === 'in_progress'): ?>
                                        <a href="<?= url('/applicant/interview/start/' . $session['id']) ?>" 
                                           class="btn-action <?= $isRetry ? 'btn-retry-interview' : 'btn-start-interview' ?>">
                                            <i class="fas fa-<?= $isRetry ? 'redo' : ($session['status'] === 'pending' ? 'play-circle' : 'arrow-right') ?>"></i>
                                            <?php if ($isRetry): ?>Start Retry Interview
                                            <?php elseif ($session['status'] === 'pending'): ?>Begin AI Interview
                                            <?php else: ?>Continue Interview<?php endif; ?>
                                        </a>
                                    <?php elseif ($session['status'] === 'completed'): ?>
                                        <div class="btn-action btn-completed">
                                            <i class="fas fa-check-circle"></i> Interview Completed
                                        </div>
                                    <?php else: ?>
                                        <div class="btn-action btn-expired">
                                            <i class="fas fa-times-circle"></i> Session Expired
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
