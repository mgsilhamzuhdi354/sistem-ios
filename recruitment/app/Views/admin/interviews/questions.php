<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question Banks - Admin | PT Indo Ocean Crew</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="admin-body">
    <aside class="admin-sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="<?= url('/admin') ?>" class="logo"><img src="<?= asset('images/logo.jpg') ?>" alt="Indo Ocean" style="width:32px;height:32px;object-fit:contain;"><span>Recruitment</span></a>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="<?= url('/admin/dashboard') ?>" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li><a href="<?= url('/admin/vacancies') ?>" class="nav-link"><i class="fas fa-briefcase"></i><span>Job Vacancies</span></a></li>
                <li><a href="<?= url('/admin/applicants') ?>" class="nav-link"><i class="fas fa-users"></i><span>Applicants</span></a></li>
                <li><a href="<?= url('/admin/applicants/pipeline') ?>" class="nav-link"><i class="fas fa-stream"></i><span>Pipeline</span></a></li>
                <li><a href="<?= url('/admin/interviews') ?>" class="nav-link active"><i class="fas fa-robot"></i><span>AI Interviews</span></a></li>
                <li><a href="<?= url('/admin/documents') ?>" class="nav-link"><i class="fas fa-file-alt"></i><span>Documents</span></a></li>
                <li><a href="<?= url('/admin/medical') ?>" class="nav-link"><i class="fas fa-heartbeat"></i><span>Medical</span></a></li>
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
        <?php if ($error = flash('error')): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
        <?php endif; ?>

        <div class="admin-content">
            <div class="page-header">
                <h1><i class="fas fa-question-circle"></i> Question Banks</h1>
                <a href="<?= url('/admin/interviews/questions/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Bank
                </a>
            </div>

            <?php if (empty($banks)): ?>
                <div class="empty-state">
                    <i class="fas fa-database"></i>
                    <h3>No Question Banks</h3>
                    <p>Create your first question bank to start building AI interviews.</p>
                    <a href="<?= url('/admin/interviews/questions/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Question Bank
                    </a>
                </div>
            <?php else: ?>
                <div class="banks-grid">
                    <?php foreach ($banks as $bank): ?>
                        <div class="bank-card">
                            <div class="bank-header">
                                <div class="bank-icon">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <div class="bank-info">
                                    <h3><?= htmlspecialchars($bank['name']) ?></h3>
                                    <?php if ($bank['department_name']): ?>
                                        <span class="department"><?= htmlspecialchars($bank['department_name']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <p class="bank-desc"><?= htmlspecialchars($bank['description'] ?? 'No description') ?></p>
                            <div class="bank-stats">
                                <div class="stat">
                                    <span class="stat-value"><?= $bank['question_count'] ?? 0 ?></span>
                                    <span class="stat-label">Questions</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-value"><?= $bank['is_active'] ? 'Active' : 'Inactive' ?></span>
                                    <span class="stat-label">Status</span>
                                </div>
                            </div>
                            <div class="bank-actions">
                                <a href="<?= url('/admin/interviews/questions/' . $bank['id']) ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Manage Questions
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .page-header h1 { font-size: 24px; color: #1a1a2e; display: flex; align-items: center; gap: 12px; }
    
    .empty-state { background: white; padding: 60px 40px; border-radius: 12px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .empty-state i { font-size: 60px; color: #ddd; margin-bottom: 20px; }
    .empty-state h3 { font-size: 22px; margin-bottom: 10px; color: #1a1a2e; }
    .empty-state p { color: #666; margin-bottom: 25px; }
    
    .banks-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px; }
    
    .bank-card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: all 0.3s; }
    .bank-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
    
    .bank-header { display: flex; gap: 15px; margin-bottom: 15px; }
    .bank-icon { width: 50px; height: 50px; background: linear-gradient(135deg, #0A2463, #1E5AA8); border-radius: 12px; display: flex; align-items: center; justify-content: center; }
    .bank-icon i { font-size: 20px; color: white; }
    .bank-info h3 { font-size: 16px; color: #1a1a2e; margin-bottom: 5px; }
    .department { font-size: 12px; color: #666; background: #f0f0f0; padding: 3px 10px; border-radius: 20px; }
    
    .bank-desc { font-size: 13px; color: #666; line-height: 1.6; margin-bottom: 20px; min-height: 40px; }
    
    .bank-stats { display: flex; gap: 30px; padding: 15px 0; border-top: 1px solid #f0f0f0; border-bottom: 1px solid #f0f0f0; margin-bottom: 20px; }
    .stat { text-align: center; }
    .stat-value { display: block; font-size: 20px; font-weight: 700; color: #0A2463; }
    .stat-label { font-size: 11px; color: #999; text-transform: uppercase; }
    
    .bank-actions { display: flex; gap: 10px; }
    .btn-sm { padding: 10px 16px; font-size: 13px; }
    
    .alert-danger { background: #f8d7da; color: #721c24; padding: 15px 20px; border-radius: 8px; margin: 20px; display: flex; align-items: center; gap: 10px; }
    </style>
    
    <script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
