<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Dashboard' ?> - Admin | PT Indo Ocean Crew Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <style>
        .language-selector {
            display: flex;
            align-items: center;
            margin-right: 15px;
        }
        .language-selector select {
            padding: 6px 10px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            background: white;
            font-size: 13px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="admin-body">
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="<?= url('/admin') ?>" class="logo">
                <img src="<?= asset('images/logo.jpg') ?>" alt="Indo Ocean" style="width:32px;height:32px;object-fit:contain;">
                <span>Recruitment</span>
            </a>
        </div>
        
        <nav class="sidebar-nav">
            <ul>
                <li class="nav-item">
                    <a href="<?= url('/admin/dashboard') ?>" class="nav-link active">
                        <i class="fas fa-tachometer-alt"></i>
                        <span data-translate="admin.dashboard">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/admin/vacancies') ?>" class="nav-link">
                        <i class="fas fa-briefcase"></i>
                        <span data-translate="admin.vacancies">Job Vacancies</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/admin/applicants') ?>" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span data-translate="admin.applicants">Applicants</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/admin/applicants/pipeline') ?>" class="nav-link">
                        <i class="fas fa-stream"></i>
                        <span data-translate="admin.pipeline">Pipeline</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/admin/interviews') ?>" class="nav-link">
                        <i class="fas fa-robot"></i>
                        <span data-translate="admin.interviews">AI Interviews</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/admin/documents') ?>" class="nav-link">
                        <i class="fas fa-file-alt"></i>
                        <span data-translate="admin.documents">Documents</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/admin/medical') ?>" class="nav-link">
                        <i class="fas fa-heartbeat"></i>
                        <span data-translate="admin.medical">Medical</span>
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
    <div class="admin-main">
        <!-- Top Header -->
        <header class="admin-header">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="header-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search...">
            </div>
            
            <div class="header-actions">
                <div class="language-selector">
                    <select id="langSelect">
                        <option value="en">🇺🇸 EN</option>
                        <option value="id">🇮🇩 ID</option>
                        <option value="zh">🇨🇳 中文</option>
                    </select>
                </div>
                <div class="user-dropdown">
                    <button class="user-btn">
                        <img src="<?= asset('images/avatar-default.svg') ?>" alt="Avatar">
                        <span><?= $_SESSION['user_name'] ?? 'Admin' ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <?php if ($success = flash('success')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= $success ?>
                <button class="alert-close">&times;</button>
            </div>
        <?php endif; ?>
        <?php if ($error = flash('error')): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                <button class="alert-close">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Page Content -->
        <div class="admin-content">
            <div class="page-header">
                <h1>Dashboard</h1>
                <span class="date-today"><i class="fas fa-calendar"></i> <?= date('l, d F Y') ?></span>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= number_format($stats['total_applicants'] ?? 0) ?></h3>
                        <p>Total Applicants</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon info">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= number_format($stats['new_today'] ?? 0) ?></h3>
                        <p>New Today</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= number_format($stats['pending_review'] ?? 0) ?></h3>
                        <p>Pending Review</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon secondary">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= number_format($stats['in_interview'] ?? 0) ?></h3>
                        <p>In Interview</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= number_format($stats['hired_month'] ?? 0) ?></h3>
                        <p>Hired This Month</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon danger">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= number_format($stats['total_vacancies'] ?? 0) ?></h3>
                        <p>Active Vacancies</p>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="charts-row">
                <div class="chart-container">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-line"></i> Recruitment Pipeline</h3>
                    </div>
                    <div class="pipeline-mini">
                        <?php if (!empty($pipeline)): ?>
                            <?php foreach ($pipeline as $stage): ?>
                                <div class="pipeline-stage">
                                    <div class="stage-bar" style="background: <?= $stage['color'] ?? '#0A2463' ?>">
                                        <span class="count"><?= $stage['count'] ?? 0 ?></span>
                                    </div>
                                    <span class="stage-name"><?= htmlspecialchars($stage['name'] ?? '') ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No pipeline data</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="chart-container">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-pie"></i> By Department</h3>
                    </div>
                    <div class="department-list">
                        <?php if (!empty($departments)): ?>
                            <?php foreach ($departments as $dept): ?>
                                <div class="dept-item">
                                    <span class="dept-name"><?= htmlspecialchars($dept['name'] ?? '') ?></span>
                                    <span class="dept-count"><?= $dept['count'] ?? 0 ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No department data</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Applications -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-file-alt"></i> Recent Applications</h3>
                    <a href="<?= url('/admin/applicants') ?>" class="btn btn-sm btn-outline">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Applicant</th>
                                    <th>Position</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentApplications)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No applications yet</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recentApplications as $app): ?>
                                        <tr>
                                            <td>
                                                <div class="user-badge">
                                                    <img src="<?= asset('images/avatar-default.svg') ?>" alt="">
                                                    <div class="user-info">
                                                        <strong><?= htmlspecialchars($app['full_name']) ?></strong>
                                                        <span><?= htmlspecialchars($app['email']) ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($app['vacancy_title']) ?></td>
                                            <td>
                                                <span class="badge" style="background: <?= $app['status_color'] ?>20; color: <?= $app['status_color'] ?>">
                                                    <?= $app['status_name'] ?>
                                                </span>
                                            </td>
                                            <td><?= date('d M Y', strtotime($app['submitted_at'])) ?></td>
                                            <td>
                                                <div class="action-btns">
                                                    <a href="<?= url('/admin/applicants/' . $app['id']) ?>" class="action-btn view" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .pipeline-mini {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .pipeline-stage {
        flex: 1;
        min-width: 100px;
        text-align: center;
    }
    .stage-bar {
        padding: 20px 10px;
        border-radius: 8px;
        color: white;
        margin-bottom: 8px;
    }
    .stage-bar .count {
        font-size: 24px;
        font-weight: 700;
    }
    .stage-name {
        font-size: 11px;
        color: #666;
        display: block;
    }
    .department-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .dept-item {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .dept-name {
        font-weight: 500;
    }
    .dept-count {
        background: #f0f0f0;
        padding: 3px 12px;
        border-radius: 20px;
        font-size: 12px;
    }
    .date-today {
        color: #666;
        font-size: 14px;
    }
    .text-muted {
        color: #6c757d;
    }
    .text-center {
        text-align: center;
    }
    </style>

    <script src="<?= asset('js/admin.js') ?>"></script>
    <script src="<?= asset('js/translate-recruitment.js') ?>"></script>
</body>
</html>
