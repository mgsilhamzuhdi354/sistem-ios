<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Dashboard' ?> - Leader | PT Indo Ocean Crew Services</title>
    <link rel="icon" type="image/jpeg" href="<?= asset('images/logo.jpg') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <style>
        /* Leader-specific color override - Orange accent */
        .admin-sidebar { background: linear-gradient(180deg, #1e3a5f 0%, #0d1f33 100%); }
        .nav-link.active { background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%); }
        .nav-link:hover { background: rgba(245, 158, 11, 0.2); }
        .stat-card.leader-accent { border-left: 4px solid #f59e0b; }
    </style>
</head>
<body class="admin-body">
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="<?= url('/leader') ?>" class="logo">
                <img src="<?= asset('images/logo.jpg') ?>" alt="Indo Ocean" style="width:32px;height:32px;object-fit:contain;">
                <span>Leader Panel</span>
            </a>
        </div>
        
        <nav class="sidebar-nav">
            <ul>
                <li class="nav-item">
                    <a href="<?= url('/leader/dashboard') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/leader/dashboard') !== false ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/leader/requests') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/leader/requests') !== false ? 'active' : '' ?>">
                        <i class="fas fa-clipboard-check"></i>
                        <span>Approval Requests</span>
                        <?php $pending = count(getPendingRequests(null)); if ($pending > 0): ?>
                            <span class="badge"><?= $pending ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/leader/pipeline') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/leader/pipeline') !== false ? 'active' : '' ?>">
                        <i class="fas fa-stream"></i>
                        <span>Pipeline</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/leader/team') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/leader/team') !== false ? 'active' : '' ?>">
                        <i class="fas fa-users-cog"></i>
                        <span>Team Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/leader/profile') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/leader/profile') !== false ? 'active' : '' ?>">
                        <i class="fas fa-user-circle"></i>
                        <span>My Profile</span>
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
                <button class="notification-btn">
                    <i class="fas fa-bell"></i>
                    <span class="badge">0</span>
                </button>
                
                <div class="user-dropdown">
                    <button class="user-btn">
                        <img src="<?= asset('images/avatar-default.png') ?>" alt="Avatar">
                        <span><?= $_SESSION['user_name'] ?? 'Leader' ?></span>
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
            <?php include APPPATH . 'Views/leader/' . $content . '_content.php'; ?>
        </div>
    </div>

    <script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
