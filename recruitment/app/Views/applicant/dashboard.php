<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PT Indo Ocean Crew Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/applicant.css') ?>">
    <style>
        .language-selector {
            display: flex;
            align-items: center;
            gap: 8px;
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
                    <a href="<?= url('/applicant/dashboard') ?>" class="nav-link active">
                        <i class="fas fa-home"></i>
                        <span data-translate="nav.dashboard">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/jobs') ?>" class="nav-link">
                        <i class="fas fa-briefcase"></i>
                        <span data-translate="nav.jobs">Job Vacancies</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/applicant/applications') ?>" class="nav-link">
                        <i class="fas fa-file-alt"></i>
                        <span data-translate="nav.applications">My Applications</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/applicant/documents') ?>" class="nav-link">
                        <i class="fas fa-folder"></i>
                        <span data-translate="nav.documents">Documents</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/applicant/interview') ?>" class="nav-link">
                        <i class="fas fa-video"></i>
                        <span data-translate="nav.interview">Interview</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/applicant/profile') ?>" class="nav-link">
                        <i class="fas fa-user"></i>
                        <span data-translate="nav.profile">Profile</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="sidebar-footer">
            <a href="<?= url('/logout') ?>" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span data-translate="nav.logout">Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="applicant-main">
        <!-- Header -->
        <header class="applicant-header">
            <div class="welcome-text">
                <h1><span data-translate="dashboard.welcome">Welcome back</span>, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Applicant') ?>!</h1>
                <p data-translate="dashboard.subtitle">Track your applications and manage your profile</p>
            </div>
            <div class="header-actions">
                <a href="<?= url('/jobs') ?>" class="btn btn-primary">
                    <i class="fas fa-search"></i> <span data-translate="dashboard.browseJobs">Browse Jobs</span>
                </a>
                <div class="language-selector">
                    <select id="langSelect">
                        <option value="en">🇺🇸 EN</option>
                        <option value="id">🇮🇩 ID</option>
                        <option value="zh">🇨🇳 中文</option>
                    </select>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <?php if ($success = flash('success')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= $success ?>
            </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $stats['active_applications'] ?></h3>
                    <p data-translate="dashboard.activeApplications">Active Applications</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $stats['pending_documents'] ?></h3>
                    <p data-translate="dashboard.pendingDocuments">Pending Documents</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-video"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $stats['scheduled_interviews'] ?></h3>
                    <p data-translate="dashboard.scheduledInterviews">Scheduled Interviews</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $stats['profile_completion'] ?>%</h3>
                    <p data-translate="dashboard.profileCompletion">Profile Complete</p>
                </div>
            </div>
        </div>

        <!-- Profile Completion Alert -->
        <?php if ($stats['profile_completion'] < 100): ?>
        <div class="alert alert-warning">
            <div class="alert-content">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <strong data-translate="dashboard.completeProfile">Complete your profile</strong>
                    <p data-translate="dashboard.completeProfileDesc">Upload all required documents to increase your chances of getting hired.</p>
                </div>
            </div>
            <a href="<?= url('/applicant/documents') ?>" class="btn btn-sm btn-warning" data-translate="dashboard.uploadDocuments">Upload Documents</a>
        </div>
        <?php endif; ?>

        <!-- Grid Layout -->
        <div class="dashboard-grid">
            <!-- Applications -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-file-alt"></i> <span data-translate="nav.applications">My Applications</span></h3>
                    <a href="<?= url('/applicant/applications') ?>" data-translate="common.viewAll">View All</a>
                </div>
                <div class="card-body">
                    <?php if (empty($applications)): ?>
                        <div class="empty-state">
                            <i class="fas fa-folder-open"></i>
                            <h4 data-translate="application.noApplications">No applications yet</h4>
                            <p data-translate="application.startApplying">Start applying to open positions</p>
                            <a href="<?= url('/jobs') ?>" class="btn btn-primary" data-translate="dashboard.browseJobs">Browse Jobs</a>
                        </div>
                    <?php else: ?>
                        <div class="applications-list">
                            <?php foreach (array_slice($applications, 0, 5) as $app): ?>
                                <div class="application-item">
                                    <div class="app-info">
                                        <h4><?= htmlspecialchars($app['vacancy_title']) ?></h4>
                                        <span class="department"><?= htmlspecialchars($app['department_name'] ?? 'General') ?></span>
                                    </div>
                                    <span class="status-badge" style="background: <?= $app['status_color'] ?>20; color: <?= $app['status_color'] ?>">
                                        <?= $app['status_name'] ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Notifications -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-bell"></i> <span data-translate="dashboard.notifications">Notifications</span></h3>
                </div>
                <div class="card-body">
                    <?php if (empty($notifications)): ?>
                        <div class="empty-state small">
                            <i class="fas fa-bell-slash"></i>
                            <p data-translate="dashboard.noNotifications">No new notifications</p>
                        </div>
                    <?php else: ?>
                        <div class="notifications-list">
                            <?php foreach ($notifications as $notif): ?>
                                <div class="notification-item <?= $notif['is_read'] ? '' : 'unread' ?>">
                                    <div class="notif-icon <?= $notif['type'] ?>">
                                        <i class="fas fa-<?= $notif['type'] === 'success' ? 'check' : ($notif['type'] === 'error' ? 'times' : 'info') ?>"></i>
                                    </div>
                                    <div class="notif-content">
                                        <strong><?= htmlspecialchars($notif['title']) ?></strong>
                                        <p><?= htmlspecialchars($notif['message']) ?></p>
                                        <span class="notif-time"><?= date('d M, H:i', strtotime($notif['created_at'])) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Translation Script -->
    <script src="<?= asset('js/translate-recruitment.js') ?>"></script>
</body>
</html>
