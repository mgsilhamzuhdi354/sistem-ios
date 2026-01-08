<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Interview - PT Indo Ocean Crew Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/applicant.css') ?>">
    <style>
        .interview-page { padding: 30px; }
        .page-title { font-size: 28px; color: #1a1a2e; margin-bottom: 30px; display: flex; align-items: center; gap: 15px; justify-content: space-between; }
        .page-title-left { display: flex; align-items: center; gap: 15px; }
        .page-title i { color: #0A2463; }
        .language-selector select {
            padding: 6px 10px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            background: white;
            font-size: 13px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
        }
        
        .interview-list { display: grid; gap: 25px; }
        
        .interview-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        .interview-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }
        
        /* Retry Banner */
        .retry-banner {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            animation: pulse-bg 2s infinite;
        }
        @keyframes pulse-bg {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.85; }
        }
        .retry-banner i { font-size: 18px; }
        
        .card-content { padding: 25px; }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        
        .job-info h3 {
            font-size: 18px;
            color: #1a1a2e;
            margin-bottom: 5px;
        }
        .job-info span {
            font-size: 13px;
            color: #666;
        }
        
        .status-badge {
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-in_progress { background: #cce5ff; color: #004085; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-expired { background: #f8d7da; color: #721c24; }
        
        .card-details {
            display: flex;
            gap: 30px;
            padding: 20px 0;
            border-top: 1px solid #f0f0f0;
            border-bottom: 1px solid #f0f0f0;
            margin-bottom: 20px;
        }
        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #666;
        }
        .detail-item i { color: #0A2463; }
        
        .score-section {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        .score-circle {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0A2463, #1E5AA8);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .score-circle .score-value { font-size: 24px; font-weight: 700; }
        .score-circle .score-label { font-size: 10px; opacity: 0.8; }
        .score-info { font-size: 14px; color: #666; }
        
        /* Action Buttons */
        .card-actions { margin-top: 20px; }
        
        .btn-start {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            padding: 16px 24px;
            border: none;
            border-radius: 10px;
            font-family: inherit;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-start-new {
            background: linear-gradient(135deg, #0A2463, #1E5AA8);
            color: white;
        }
        .btn-start-new:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(10,36,99,0.3);
        }
        
        .btn-start-retry {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
            animation: pulse-btn 2s infinite;
        }
        @keyframes pulse-btn {
            0%, 100% { box-shadow: 0 0 0 0 rgba(243, 156, 18, 0.4); }
            50% { box-shadow: 0 0 20px 10px rgba(243, 156, 18, 0); }
        }
        .btn-start-retry:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(243,156,18,0.4);
        }
        
        .btn-completed {
            background: #e9ecef;
            color: #6c757d;
            cursor: default;
        }
        
        .btn-expired {
            background: #f8d7da;
            color: #721c24;
            cursor: default;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 40px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .empty-state i {
            font-size: 80px;
            color: #ddd;
            margin-bottom: 25px;
        }
        .empty-state h3 {
            font-size: 24px;
            color: #1a1a2e;
            margin-bottom: 10px;
        }
        .empty-state p {
            color: #666;
            max-width: 400px;
            margin: 0 auto;
        }

        /* Alert */
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-info { background: #d1ecf1; color: #0c5460; }
        .alert-warning { background: #fff3cd; color: #856404; }
    </style>
</head>
<body class="applicant-body">
    <aside class="applicant-sidebar">
        <div class="sidebar-header">
            <a href="<?= url('/') ?>" class="logo"><img src="<?= asset('images/logo.jpg') ?>" alt="Indo Ocean" style="width:32px;height:32px;object-fit:contain;"><span>Indo Ocean</span></a>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="<?= url('/applicant/dashboard') ?>" class="nav-link"><i class="fas fa-home"></i><span data-translate="nav.dashboard">Dashboard</span></a></li>
                <li><a href="<?= url('/jobs') ?>" class="nav-link"><i class="fas fa-briefcase"></i><span data-translate="nav.jobs">Job Vacancies</span></a></li>
                <li><a href="<?= url('/applicant/applications') ?>" class="nav-link"><i class="fas fa-file-alt"></i><span data-translate="nav.applications">My Applications</span></a></li>
                <li><a href="<?= url('/applicant/documents') ?>" class="nav-link"><i class="fas fa-folder"></i><span data-translate="nav.documents">Documents</span></a></li>
                <li><a href="<?= url('/applicant/interview') ?>" class="nav-link active"><i class="fas fa-video"></i><span data-translate="nav.interview">Interview</span></a></li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="<?= url('/logout') ?>" class="logout-btn"><i class="fas fa-sign-out-alt"></i><span data-translate="nav.logout">Logout</span></a>
        </div>
    </aside>

    <main class="applicant-main">
        <div class="interview-page">
            <h1 class="page-title">
                <div class="page-title-left">
                    <i class="fas fa-robot"></i> <span data-translate="interview.title">AI Interview</span>
                </div>
                <div class="language-selector">
                    <select id="langSelect">
                        <option value="en">🇺🇸 EN</option>
                        <option value="id">🇮🇩 ID</option>
                        <option value="zh">🇨🇳 中文</option>
                    </select>
                </div>
            </h1>

            <?php if ($success = flash('success')): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div>
            <?php endif; ?>
            <?php if ($info = flash('info')): ?>
                <div class="alert alert-info"><i class="fas fa-info-circle"></i> <?= $info ?></div>
            <?php endif; ?>
            <?php if ($warning = flash('warning')): ?>
                <div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> <?= $warning ?></div>
            <?php endif; ?>

            <?php if (empty($sessions)): ?>
                <div class="empty-state">
                    <i class="fas fa-video"></i>
                    <h3 data-translate="interview.noSessions">No Interview Sessions Yet</h3>
                    <p data-translate="interview.noSessionsDesc">You don't have any interview sessions. After your application reaches the interview stage, you will see interview sessions here.</p>
                </div>
            <?php else: ?>
                <div class="interview-list">
                    <?php foreach ($sessions as $session): ?>
                        <?php 
                        $isRetry = isset($session['retry_count']) && $session['retry_count'] > 0;
                        $retryCount = $session['retry_count'] ?? 0;
                        ?>
                        <div class="interview-card">
                            <?php if ($isRetry && $session['status'] !== 'completed'): ?>
                                <div class="retry-banner">
                                    <i class="fas fa-redo"></i>
                                    <span><span data-translate="interview.retryBanner">RETRY INTERVIEW</span> (<span data-translate="interview.attempt">Attempt</span> #<?= $retryCount + 1 ?>)</span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-content">
                                <div class="card-header">
                                    <div class="job-info">
                                        <h3><?= htmlspecialchars($session['vacancy_title']) ?></h3>
                                        <span><?= htmlspecialchars($session['question_bank_name']) ?></span>
                                    </div>
                                    <span class="status-badge status-<?= $session['status'] ?>">
                                        <?php if ($session['status'] === 'pending'): ?>
                                            <span data-translate="interview.pending">Not Started</span>
                                        <?php elseif ($session['status'] === 'in_progress'): ?>
                                            <span data-translate="interview.inProgress">In Progress</span>
                                        <?php elseif ($session['status'] === 'completed'): ?>
                                            <span data-translate="interview.completed">Completed</span>
                                        <?php else: ?>
                                            <span data-translate="interview.expired">Expired</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                
                                <div class="card-details">
                                    <div class="detail-item">
                                        <i class="fas fa-question-circle"></i>
                                        <span><?= $session['total_questions'] ?> <span data-translate="interview.questions">Questions</span></span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-clock"></i>
                                        <span>
                                            <?php if ($session['status'] === 'completed' && $session['completed_at']): ?>
                                                <span data-translate="interview.completedOn">Completed</span> <?= date('d M Y', strtotime($session['completed_at'])) ?>
                                            <?php elseif ($session['expires_at']): ?>
                                                <span data-translate="interview.deadline">Deadline</span>: <?= date('d M Y', strtotime($session['expires_at'])) ?>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <?php if ($isRetry): ?>
                                        <div class="detail-item">
                                            <i class="fas fa-history"></i>
                                            <span><span data-translate="interview.retried">Retried</span> <?= $retryCount ?>x</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($session['status'] === 'completed' && $session['total_score'] !== null): ?>
                                    <div class="score-section">
                                        <div class="score-circle">
                                            <span class="score-value"><?= $session['total_score'] ?></span>
                                            <span class="score-label" data-translate="interview.score">SCORE</span>
                                        </div>
                                        <div class="score-info">
                                            <?php if ($session['total_score'] >= 80): ?>
                                                <strong style="color: #28a745;" data-translate="interview.excellent">Excellent!</strong><br>
                                            <?php elseif ($session['total_score'] >= 60): ?>
                                                <strong style="color: #17a2b8;" data-translate="interview.good">Good</strong><br>
                                            <?php else: ?>
                                                <strong style="color: #dc3545;" data-translate="interview.needsImprovement">Needs Improvement</strong><br>
                                            <?php endif; ?>
                                            <small><span data-translate="interview.scoreOutOf">Score out of</span> 100</small>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="card-actions">
                                    <?php if ($session['status'] === 'pending' || $session['status'] === 'in_progress'): ?>
                                        <a href="<?= url('/applicant/interview/start/' . $session['id']) ?>" 
                                           class="btn-start <?= $isRetry ? 'btn-start-retry' : 'btn-start-new' ?>">
                                            <i class="fas fa-<?= $isRetry ? 'redo' : 'play' ?>"></i>
                                            <?php if ($isRetry): ?>
                                                <span data-translate="interview.startRetry">Start Retry Interview</span>
                                            <?php elseif ($session['status'] === 'pending'): ?>
                                                <span data-translate="interview.start">Start Interview</span>
                                            <?php else: ?>
                                                <span data-translate="interview.continue">Continue Interview</span>
                                            <?php endif; ?>
                                        </a>
                                    <?php elseif ($session['status'] === 'completed'): ?>
                                        <div class="btn-start btn-completed">
                                            <i class="fas fa-check-circle"></i>
                                            <span data-translate="interview.interviewCompleted">Interview Completed</span>
                                        </div>
                                        <?php if ($isRetry): ?>
                                            <div class="retry-indicator" style="display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 12px; padding: 10px 20px; background: linear-gradient(135deg, #f39c12, #e67e22); color: white; border-radius: 8px; font-weight: 600;">
                                                <i class="fas fa-redo"></i>
                                                <span data-translate="interview.retryCount">Retry</span> (<?= $retryCount ?>x)
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="btn-start btn-expired">
                                            <i class="fas fa-times-circle"></i>
                                            <span data-translate="interview.sessionExpired">Session Expired</span>
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
    
    <script src="<?= asset('js/translate-recruitment.js') ?>"></script>
</body>
</html>
