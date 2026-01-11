<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Dashboard' ?> - Crewing PIC | PT Indo Ocean Crew Services</title>
    <link rel="icon" type="image/jpeg" href="<?= asset('images/logo.jpg') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <style>
        /* Crewing PIC-specific color override - Yellow/Gold accent */
        .admin-sidebar { background: linear-gradient(180deg, #1e3a5f 0%, #0d1f33 100%); }
        .nav-link.active { background: linear-gradient(90deg, #eab308 0%, #ca8a04 100%); }
        .nav-link:hover { background: rgba(234, 179, 8, 0.2); }
        .stat-card.pic-accent { border-left: 4px solid #eab308; }
    </style>
</head>
<body class="admin-body">
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="<?= url('/crewing-pic') ?>" class="logo">
                <img src="<?= asset('images/logo.jpg') ?>" alt="Indo Ocean" style="width:32px;height:32px;object-fit:contain;">
                <span>Crewing PIC</span>
            </a>
        </div>
        
        <nav class="sidebar-nav">
            <ul>
                <li class="nav-item">
                    <a href="<?= url('/crewing-pic/dashboard') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/crewing-pic/dashboard') !== false ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/crewing-pic/requests') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/crewing-pic/requests') !== false ? 'active' : '' ?>">
                        <i class="fas fa-clipboard-check"></i>
                        <span>Approval Requests</span>
                        <?php $pending = count(getPendingRequests($_SESSION['user_id'] ?? 0)); if ($pending > 0): ?>
                            <span class="badge"><?= $pending ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/crewing/applications') ?>" class="nav-link">
                        <i class="fas fa-file-alt"></i>
                        <span>Applications</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/crewing/pipeline') ?>" class="nav-link">
                        <i class="fas fa-stream"></i>
                        <span>Pipeline</span>
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
                        <span><?= $_SESSION['user_name'] ?? 'Crewing PIC' ?></span>
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
            <?php include APPPATH . 'Views/crewing_pic/' . $content . '_content.php'; ?>
        </div>
    </div>

    <script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
