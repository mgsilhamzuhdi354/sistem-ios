<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Dashboard' ?> - Crewing | PT Indo Ocean Crew Services</title>
    <link rel="icon" type="image/jpeg" href="<?= asset('images/logo.jpg') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/crewing.css') ?>">
    <?php if (isset($_SESSION['ui_scale']) && $_SESSION['ui_scale'] != 1.00): ?>
    <style>
        .admin-main {
            transform: scale(<?= $_SESSION['ui_scale'] ?? 1.00 ?>);
            transform-origin: top left;
            width: <?= (100 / ($_SESSION['ui_scale'] ?? 1.00)) ?>%;
            min-height: <?= (100 / ($_SESSION['ui_scale'] ?? 1.00)) ?>vh;
        }
    </style>
    <?php endif; ?>
</head>
<body class="admin-body crewing-body">
    <!-- Sidebar -->
    <aside class="admin-sidebar crewing-sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="<?= url('/crewing/dashboard') ?>" class="logo">
                <div style="display:flex;flex-direction:column;align-items:center;gap:0.75rem;width:100%;">
                    <img src="<?= asset('images/logo.jpg') ?>" alt="Indo Ocean" style="width:80px;height:80px;object-fit:contain;border-radius:50%;border:3px solid rgba(255,255,255,0.2);background:white;padding:5px;box-shadow:0 4px 12px rgba(0,0,0,0.2);">
                    <div style="text-align:center;">
                        <div style="font-size:1.25rem;font-weight:700;color:#fff;letter-spacing:0.5px;">PT Indo Ocean</div>
                        <div style="font-size:0.875rem;color:rgba(255,255,255,0.8);font-weight:500;margin-top:0.25rem;">Crewing Services</div>
                    </div>
                </div>
            </a>
        </div>
        
        <nav class="sidebar-nav">
            <ul>
                <li class="nav-item">
                    <a href="<?= url('/crewing/dashboard') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/crewing/dashboard') !== false ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        <span><?= t('nav.dashboard') ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/crewing/applications') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/crewing/applications') !== false ? 'active' : '' ?>">
                        <i class="fas fa-file-alt"></i>
                        <span><?= t('nav.my_applications') ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/crewing/pipeline') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/crewing/pipeline') !== false ? 'active' : '' ?>">
                        <i class="fas fa-stream"></i>
                        <span><?= t('nav.pipeline') ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/crewing/daily-report') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/crewing/daily-report') !== false ? 'active' : '' ?>">
                        <i class="fas fa-chart-line"></i>
                        <span><?= t('nav.daily_report') ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/crewing/team') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/crewing/team') !== false ? 'active' : '' ?>">
                        <i class="fas fa-users-cog"></i>
                        <span><?= t('nav.team_workload') ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/crewing/manual-entry') ?>" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/crewing/manual-entry') !== false && strpos($_SERVER['REQUEST_URI'], '/crewing/manual-entries') === false) ? 'active' : '' ?>">
                        <i class="fas fa-user-plus"></i>
                        <span><?= t('nav.manual_entry') ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/crewing/manual-entries') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/crewing/manual-entries') !== false ? 'active' : '' ?>">
                        <i class="fas fa-list-alt"></i>
                        <span><?= t('nav.manual_entries_list') ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/crewing/vacancies') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/crewing/vacancies') !== false ? 'active' : '' ?>">
                        <i class="fas fa-briefcase"></i>
                        <span><?= t('nav.job_vacancies') ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/crewing/email') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/crewing/email') !== false ? 'active' : '' ?>">
                        <i class="fas fa-envelope"></i>
                        <span><?= t('nav.email_center') ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/crewing/interviews') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/crewing/interviews') !== false ? 'active' : '' ?>">
                        <i class="fas fa-robot"></i>
                        <span>AI Interview</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/crewing/settings') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/crewing/settings') !== false ? 'active' : '' ?>">
                        <i class="fas fa-cog"></i>
                        <span><?= t('nav.settings') ?></span>
                    </a>
                </li>
                
                <?php if (isAdmin()): ?>
                <li class="nav-divider"></li>
                <li class="nav-label">Admin</li>
                <li class="nav-item">
                    <a href="<?= url('/admin/dashboard') ?>" class="nav-link">
                        <i class="fas fa-user-shield"></i>
                        <span>Admin Panel</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/admin/crewing') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/crewing') !== false ? 'active' : '' ?>">
                        <i class="fas fa-user-tie"></i>
                        <span>Manage Crewing</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        
        <div class="sidebar-footer">
            <div class="crewing-info">
                <i class="fas fa-id-badge"></i>
                <span><?= $_SESSION['user_name'] ?? 'Crewing' ?></span>
            </div>
            <a href="<?= url('/logout') ?>" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span><?= t('nav.logout') ?></span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="admin-main">
        <!-- Top Header -->
        <header class="admin-header crewing-header">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="header-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="<?= t('btn.search', 'Search') ?>...">
            </div>
            
            <div class="header-actions">
                <button class="notification-btn" id="notificationBtn">
                    <i class="fas fa-bell"></i>
                    <span class="badge" id="notificationBadge">0</span>
                </button>
                
                <div class="user-dropdown">
                    <button class="user-btn">
                        <img src="<?= asset('images/avatar-default.svg') ?>" alt="Avatar">
                        <span><?= $_SESSION['user_name'] ?? 'Crewing' ?></span>
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
        <?php if ($warning = flash('warning')): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> <?= $warning ?>
                <button class="alert-close">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Page Content -->
        <div class="admin-content crewing-content">
            <?php include APPPATH . 'Views/' . $content . '.php'; ?>
        </div>
    </div>

    <script src="<?= asset('js/admin.js') ?>"></script>
    <script src="<?= asset('js/crewing.js') ?>"></script>
</body>
</html>
