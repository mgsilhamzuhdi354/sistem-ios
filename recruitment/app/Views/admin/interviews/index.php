<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interviews - <?= isMasterAdmin() ? 'Master Admin' : 'Admin' ?> | PT Indo Ocean Crew</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <?php if (isMasterAdmin()): ?>
    <style>
        /* Master Admin-specific color override - Red accent */
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
                <li><a href="<?= url('/admin/vacancies') ?>" class="nav-link"><i class="fas fa-briefcase"></i><span>Job Vacancies</span></a></li>
                <li><a href="<?= url('/admin/applicants') ?>" class="nav-link"><i class="fas fa-users"></i><span>Applicants</span></a></li>
                <li><a href="<?= url('/admin/applicants/pipeline') ?>" class="nav-link"><i class="fas fa-stream"></i><span>Pipeline</span></a></li>
                <li><a href="<?= url('/admin/interviews') ?>" class="nav-link active"><i class="fas fa-robot"></i><span>AI Interviews</span></a></li>
                <li><a href="<?= url('/admin/documents') ?>" class="nav-link"><i class="fas fa-file-alt"></i><span>Documents</span></a></li>
                <li><a href="<?= url('/admin/medical') ?>" class="nav-link"><i class="fas fa-heartbeat"></i><span>Medical</span></a></li>
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
                <div class="user-dropdown">
                    <button class="user-btn">
                        <img src="<?= asset('images/avatar-default.svg') ?>" alt="">
                        <span><?= $_SESSION['user_name'] ?? 'Admin' ?></span>
                    </button>
                </div>
            </div>
        </header>

        <?php if ($success = flash('success')): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div>
        <?php endif; ?>

        <div class="admin-content">
            <div class="page-header">
                <h1><i class="fas fa-robot"></i> AI Interviews</h1>
            </div>

            <!-- Tabs -->
            <div class="tabs-container">
                <div class="tabs-nav">
                    <button class="tab-btn active" onclick="showTab('sessions')">
                        <i class="fas fa-video"></i> Interview Sessions
                    </button>
                    <button class="tab-btn" onclick="showTab('questions')">
                        <i class="fas fa-question-circle"></i> Question Banks
                    </button>
                </div>

                <!-- Sessions Tab -->
                <div class="tab-content active" id="sessions">
                    <div class="card">
                        <div class="card-header">
                            <h3>Recent Interview Sessions</h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($sessions)): ?>
                                <div class="empty-state">
                                    <i class="fas fa-video"></i>
                                    <h3>No Interviews Yet</h3>
                                    <p>Interview sessions will appear here once applicants complete them.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="data-table">
                                        <thead>
                                            <tr>
                                                <th>Applicant</th>
                                                <th>Position</th>
                                                <th>Status</th>
                                                <th>Score</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($sessions as $session): ?>
                                                <tr>
                                                    <td>
                                                        <div class="user-badge">
                                                            <img src="<?= asset('images/avatar-default.svg') ?>" alt="">
                                                            <div class="user-info">
                                                                <strong><?= htmlspecialchars($session['full_name']) ?></strong>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?= htmlspecialchars($session['vacancy_title'] ?? '-') ?></td>
                                                    <td>
                                                        <?php 
                                                        $colors = ['pending' => '#ffc107', 'in_progress' => '#17a2b8', 'completed' => '#28a745', 'expired' => '#dc3545'];
                                                        $color = $colors[$session['status']] ?? '#6c757d';
                                                        ?>
                                                        <span class="badge" style="background: <?= $color ?>20; color: <?= $color ?>">
                                                            <?= ucfirst(str_replace('_', ' ', $session['status'])) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($session['total_score'] !== null): ?>
                                                            <strong class="score"><?= $session['total_score'] ?></strong><span class="text-muted">/100</span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= date('d M Y', strtotime($session['created_at'])) ?></td>
                                                    <td>
                                                        <a href="<?= url('/admin/interviews/review/' . $session['id']) ?>" class="btn btn-sm btn-outline">
                                                            <i class="fas fa-eye"></i> Review
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Question Banks Tab -->
                <div class="tab-content" id="questions">
                    <div class="card">
                        <div class="card-header">
                            <h3>Question Banks</h3>
                            <a href="<?= url('/admin/interviews/questions/create') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add Question Bank
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($questionBanks)): ?>
                                <div class="empty-state">
                                    <i class="fas fa-question-circle"></i>
                                    <h3>No Question Banks</h3>
                                    <p>Create question banks to use in AI interviews.</p>
                                    <a href="<?= url('/admin/interviews/questions/create') ?>" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Create Question Bank
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="banks-grid">
                                    <?php foreach ($questionBanks as $bank): ?>
                                        <div class="bank-card">
                                            <div class="bank-header">
                                                <h4><?= htmlspecialchars($bank['name']) ?></h4>
                                                <span class="question-count"><?= $bank['question_count'] ?? 0 ?> questions</span>
                                            </div>
                                            <p class="bank-desc"><?= htmlspecialchars($bank['description'] ?? 'No description') ?></p>
                                            <div class="bank-meta">
                                                <span><i class="fas fa-clock"></i> <?= $bank['time_limit_minutes'] ?? 30 ?> min</span>
                                                <span class="badge badge-<?= !empty($bank['is_active']) ? 'success' : 'secondary' ?>">
                                                    <?= !empty($bank['is_active']) ? 'Active' : 'Inactive' ?>
                                                </span>
                                            </div>
                                            <div class="bank-actions">
                                                <a href="<?= url('/admin/interviews/questions/' . $bank['id']) ?>" class="btn btn-outline btn-sm">
                                                    <i class="fas fa-edit"></i> Manage
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .tabs-container { margin-top: 10px; }
    .tabs-nav { display: flex; gap: 10px; margin-bottom: 20px; }
    .tab-btn { padding: 12px 24px; border: none; background: white; border-radius: 8px; cursor: pointer; font-family: inherit; font-size: 14px; font-weight: 500; color: #666; display: flex; align-items: center; gap: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); transition: all 0.3s; }
    .tab-btn:hover { color: #0A2463; }
    .tab-btn.active { background: #0A2463; color: white; }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    .score { font-size: 18px; color: #0A2463; }
    .banks-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
    .bank-card { background: #f8f9fa; padding: 20px; border-radius: 10px; border: 1px solid #e0e0e0; }
    .bank-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
    .bank-header h4 { font-size: 16px; color: #1a1a2e; margin: 0; }
    .question-count { font-size: 12px; background: #e0e0e0; padding: 4px 12px; border-radius: 20px; }
    .bank-desc { font-size: 13px; color: #666; margin-bottom: 15px; line-height: 1.6; }
    .bank-meta { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
    .bank-meta span { font-size: 12px; color: #666; display: flex; align-items: center; gap: 5px; }
    .bank-actions { display: flex; gap: 10px; }
    .text-muted { color: #6c757d; }
    </style>

    <script>
    function showTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        document.getElementById(tabId).classList.add('active');
        event.target.closest('.tab-btn').classList.add('active');
    }
    </script>
    <script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
