<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interview Review - <?= isMasterAdmin() ? 'Master Admin' : 'Admin' ?> | PT Indo Ocean Crew</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <?php if (isMasterAdmin()): ?>
    <style>
        .admin-sidebar { background: linear-gradient(180deg, #1e3a5f 0%, #0d1f33 100%); }
        .nav-link.active { background: linear-gradient(90deg, #dc2626 0%, #b91c1c 100%); }
        .nav-link:hover { background: rgba(220, 38, 38, 0.2); }
    </style>
    <?php endif; ?>
</head>
<body class="admin-body">
    <aside class="admin-sidebar" id="sidebar">
        <div class="sidebar-header">
            <?php if (isMasterAdmin()): ?>
            <a href="<?= url('/master-admin') ?>" class="logo"><img src="<?= asset('images/logo.jpg') ?>" alt="Indo Ocean" style="width:32px;height:32px;object-fit:contain;"><span>Master Admin</span></a>
            <?php else: ?>
            <a href="<?= url('/admin') ?>" class="logo"><img src="<?= asset('images/logo.jpg') ?>" alt="Indo Ocean" style="width:32px;height:32px;object-fit:contain;"><span>Recruitment</span></a>
            <?php endif; ?>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <?php if (isMasterAdmin()): ?>
                <li class="nav-item">
                    <a href="<?= url('/master-admin/dashboard') ?>" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/master-admin/users') ?>" class="nav-link">
                        <i class="fas fa-users-cog"></i>
                        <span>User Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/master-admin/vacancies') ?>" class="nav-link">
                        <i class="fas fa-briefcase"></i>
                        <span>Job Vacancies</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/master-admin/pipeline') ?>" class="nav-link">
                        <i class="fas fa-stream"></i>
                        <span>Pipeline</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/master-admin/requests') ?>" class="nav-link">
                        <i class="fas fa-clipboard-check"></i>
                        <span>Requests</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/admin/interviews') ?>" class="nav-link active">
                        <i class="fas fa-robot"></i>
                        <span>AI Interview</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/admin/documents') ?>" class="nav-link">
                        <i class="fas fa-file-alt"></i>
                        <span>Documents</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/admin/medical') ?>" class="nav-link">
                        <i class="fas fa-heartbeat"></i>
                        <span>Medical</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/master-admin/reports') ?>" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reports</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/master-admin/settings') ?>" class="nav-link">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </li>
                <?php else: ?>
                <li><a href="<?= url('/admin/dashboard') ?>" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li><a href="<?= url('/admin/applicants') ?>" class="nav-link"><i class="fas fa-users"></i><span>Applicants</span></a></li>
                <li><a href="<?= url('/admin/interviews') ?>" class="nav-link active"><i class="fas fa-robot"></i><span>AI Interviews</span></a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="<?= url('/logout') ?>" class="logout-btn"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-header">
            <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
            <div class="header-actions">
                <a href="<?= url('/admin/interviews') ?>" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Back to Interviews
                </a>
            </div>
        </header>

        <?php if ($success = flash('success')): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div>
        <?php endif; ?>

        <div class="admin-content">
            <!-- Applicant Info Header -->
            <div class="review-header">
                <div class="applicant-info">
                    <div class="avatar">
                        <img src="<?= asset('images/avatar-default.svg') ?>" alt="">
                    </div>
                    <div class="info">
                        <h1><?= htmlspecialchars($session['full_name']) ?></h1>
                        <p><?= htmlspecialchars($session['vacancy_title']) ?></p>
                    </div>
                </div>
                <div class="score-summary">
                    <div class="score-box">
                        <span class="label">AI Score</span>
                        <span class="value <?= ($session['total_score'] ?? 0) >= 70 ? 'good' : 'low' ?>">
                            <?= $session['total_score'] ?? '-' ?><small>/100</small>
                        </span>
                    </div>
                    <div class="score-box">
                        <span class="label">Status</span>
                        <?php 
                        $colors = ['pending' => '#ffc107', 'in_progress' => '#17a2b8', 'completed' => '#28a745', 'expired' => '#dc3545'];
                        $color = $colors[$session['status']] ?? '#6c757d';
                        ?>
                        <span class="badge" style="background: <?= $color ?>20; color: <?= $color ?>">
                            <?= ucfirst(str_replace('_', ' ', $session['status'])) ?>
                        </span>
                    </div>
                    <div class="score-box">
                        <span class="label">Recommendation</span>
                        <?php 
                        $recColors = ['pass' => '#28a745', 'review' => '#ffc107', 'fail' => '#dc3545'];
                        $rec = $session['ai_recommendation'] ?? 'review';
                        ?>
                        <span class="badge" style="background: <?= $recColors[$rec] ?? '#6c757d' ?>20; color: <?= $recColors[$rec] ?? '#6c757d' ?>">
                            <?= ucfirst($rec) ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Answers Review -->
            <div class="answers-section">
                <h2><i class="fas fa-comments"></i> Interview Responses</h2>
                
                <?php if (empty($answers)): ?>
                    <div class="empty-state">
                        <i class="fas fa-hourglass-half"></i>
                        <h3>No Responses Yet</h3>
                        <p>The applicant hasn't completed the interview yet.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($answers as $index => $answer): ?>
                        <div class="answer-card">
                            <div class="question-header">
                                <span class="q-number">Q<?= $index + 1 ?></span>
                                <div class="q-text"><?= htmlspecialchars($answer['question_text']) ?></div>
                                <div class="q-score">
                                    <span class="score <?= ($answer['ai_score'] ?? 0) >= 70 ? 'good' : 'low' ?>">
                                        <?= $answer['ai_score'] ?? 0 ?>/<?= $answer['max_score'] ?>
                                    </span>
                                </div>
                            </div>
                            <div class="answer-content">
                                <?php if ($answer['question_type'] === 'multiple_choice'): ?>
                                    <div class="selected-option">
                                        <i class="fas fa-check-circle"></i> Selected: <strong><?= htmlspecialchars($answer['selected_option'] ?? 'No answer') ?></strong>
                                        <?php if ($answer['correct_answer'] && $answer['selected_option'] === $answer['correct_answer']): ?>
                                            <span class="correct">✓ Correct</span>
                                        <?php elseif ($answer['correct_answer']): ?>
                                            <span class="incorrect">✗ Correct answer: <?= htmlspecialchars($answer['correct_answer']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-answer">
                                        <?= nl2br(htmlspecialchars($answer['answer_text'] ?? 'No answer provided')) ?>
                                    </div>
                                    <?php if (!empty($answer['keyword_matches'])): ?>
                                        <div class="keyword-matches">
                                            <strong>Keywords Found:</strong>
                                            <?php 
                                            $keywords = json_decode($answer['keyword_matches'], true) ?: [];
                                            foreach ($keywords as $kw): ?>
                                                <span class="keyword"><?= htmlspecialchars($kw) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <div class="answer-meta">
                                <span><i class="fas fa-clock"></i> Time: <?= $answer['time_taken_seconds'] ?? 0 ?>s</span>
                                <span><i class="fas fa-star"></i> Relevance: <?= $answer['relevance_score'] ?? 0 ?>%</span>
                                <span><i class="fas fa-check-double"></i> Completeness: <?= $answer['completeness_score'] ?? 0 ?>%</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Admin Override Form -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3><i class="fas fa-gavel"></i> Admin Review & Override</h3>
                </div>
                <div class="card-body">
                    <form action="<?= url('/admin/interviews/score/' . $session['id']) ?>" method="POST" id="reviewForm">
                        <?= csrf_field() ?>
                        
                        <div class="form-row">
                            <div class="form-group col-4">
                                <label>Override Score</label>
                                <input type="number" name="override_score" class="form-control" min="0" max="100" value="<?= $session['admin_override_score'] ?? '' ?>" placeholder="Leave blank to use AI score">
                            </div>
                            <div class="form-group col-4">
                                <label>Final Recommendation</label>
                                <select name="recommendation" class="form-control">
                                    <option value="review" <?= ($session['ai_recommendation'] ?? '') === 'review' ? 'selected' : '' ?>>Review</option>
                                    <option value="pass" <?= ($session['ai_recommendation'] ?? '') === 'pass' ? 'selected' : '' ?>>Pass</option>
                                    <option value="fail" <?= ($session['ai_recommendation'] ?? '') === 'fail' ? 'selected' : '' ?>>Fail</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Admin Notes</label>
                            <textarea name="admin_notes" class="form-control" rows="3" placeholder="Add your observations..."><?= htmlspecialchars($session['admin_notes'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn btn-warning" onclick="showResetModal()">
                                <i class="fas fa-redo"></i> Reset Interview
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Confirmation Modal -->
    <div class="modal-overlay" id="resetModal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle" style="color: #f39c12;"></i> Konfirmasi Reset Interview</h3>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin <strong>mereset interview</strong> untuk pelamar ini?</p>
                <div class="alert-box warning">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <strong>Yang akan terjadi:</strong>
                        <ul>
                            <li>Semua jawaban interview akan dihapus</li>
                            <li>Pelamar harus mengulang interview dari awal</li>
                            <li>Pelamar akan mendapat notifikasi untuk interview ulang</li>
                            <li>Batas waktu baru: 7 hari dari sekarang</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideResetModal()">
                    <i class="fas fa-times"></i> Batal
                </button>
                <form action="<?= url('/admin/interviews/reset/' . $session['id']) ?>" method="POST" style="display: inline;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-redo"></i> Ya, Reset Interview
                    </button>
                </form>
            </div>
        </div>
    </div>

    <style>
    .review-header { display: flex; justify-content: space-between; align-items: center; background: white; padding: 25px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .applicant-info { display: flex; gap: 20px; align-items: center; }
    .applicant-info .avatar img { width: 60px; height: 60px; border-radius: 50%; border: 3px solid #0A2463; }
    .applicant-info h1 { font-size: 20px; margin: 0 0 5px; color: #1a1a2e; }
    .applicant-info p { margin: 0; color: #666; font-size: 14px; }
    .score-summary { display: flex; gap: 30px; }
    .score-box { text-align: center; }
    .score-box .label { display: block; font-size: 12px; color: #666; margin-bottom: 5px; }
    .score-box .value { font-size: 28px; font-weight: 700; }
    .score-box .value.good { color: #28a745; }
    .score-box .value.low { color: #dc3545; }
    .score-box .value small { font-size: 14px; color: #999; }
    
    .answers-section h2 { font-size: 18px; margin-bottom: 20px; color: #1a1a2e; display: flex; align-items: center; gap: 10px; }
    .answer-card { background: white; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .question-header { display: flex; gap: 15px; align-items: flex-start; margin-bottom: 20px; }
    .q-number { background: #0A2463; color: white; padding: 8px 15px; border-radius: 20px; font-weight: 600; font-size: 13px; }
    .q-text { flex: 1; font-size: 15px; color: #1a1a2e; line-height: 1.6; }
    .q-score .score { font-size: 18px; font-weight: 700; }
    .q-score .score.good { color: #28a745; }
    .q-score .score.low { color: #dc3545; }
    
    .answer-content { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 15px; }
    .text-answer { font-size: 14px; line-height: 1.8; color: #333; }
    .selected-option { font-size: 14px; }
    .selected-option .correct { color: #28a745; margin-left: 10px; }
    .selected-option .incorrect { color: #dc3545; margin-left: 10px; }
    .keyword-matches { margin-top: 15px; padding-top: 15px; border-top: 1px solid #e0e0e0; }
    .keyword { display: inline-block; background: #0A246320; color: #0A2463; padding: 4px 12px; border-radius: 20px; font-size: 12px; margin: 3px; }
    
    .answer-meta { display: flex; gap: 20px; }
    .answer-meta span { font-size: 12px; color: #666; display: flex; align-items: center; gap: 5px; }
    
    .form-row { display: flex; gap: 20px; }
    .form-group { margin-bottom: 1.5rem; flex: 1; }
    .col-4 { flex: 0 0 30%; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 0.5rem; }
    .form-control { width: 100%; padding: 12px 15px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; }
    .form-control:focus { outline: none; border-color: #0A2463; }
    .form-actions { display: flex; justify-content: flex-end; gap: 15px; }
    .mt-4 { margin-top: 2rem; }
    .btn-warning { background: #ffc107; color: #1a1a2e; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; }
    .btn-warning:hover { background: #e0a800; }
    
    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(4px);
    }
    .modal-content {
        background: white;
        border-radius: 16px;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: modalSlideIn 0.3s ease;
    }
    @keyframes modalSlideIn {
        from { transform: translateY(-30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .modal-header {
        padding: 25px 30px;
        border-bottom: 1px solid #eee;
    }
    .modal-header h3 {
        margin: 0;
        font-size: 18px;
        color: #1a1a2e;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .modal-body {
        padding: 25px 30px;
    }
    .modal-body p {
        margin-bottom: 20px;
        color: #333;
        font-size: 15px;
    }
    .alert-box {
        padding: 20px;
        border-radius: 10px;
        display: flex;
        gap: 15px;
    }
    .alert-box.warning {
        background: #fff8e1;
        border: 1px solid #ffcc80;
    }
    .alert-box i {
        color: #f39c12;
        font-size: 20px;
        margin-top: 3px;
    }
    .alert-box ul {
        margin: 10px 0 0 20px;
        padding: 0;
    }
    .alert-box li {
        margin-bottom: 8px;
        font-size: 14px;
        color: #666;
    }
    .modal-footer {
        padding: 20px 30px;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: flex-end;
        gap: 15px;
    }
    .btn-secondary {
        background: #6c757d;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }
    .btn-secondary:hover {
        background: #5a6268;
    }
    </style>
    
    <script>
    function showResetModal() {
        document.getElementById('resetModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function hideResetModal() {
        document.getElementById('resetModal').style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Close modal on overlay click
    document.getElementById('resetModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            hideResetModal();
        }
    });
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideResetModal();
        }
    });
    </script>
    
    <script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>

