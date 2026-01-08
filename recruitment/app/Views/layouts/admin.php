<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Dashboard' ?> - Admin | PT Indo Ocean Crew Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
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
                    <a href="<?= url('/admin/dashboard') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/admin/vacancies') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/vacancies') !== false ? 'active' : '' ?>">
                        <i class="fas fa-briefcase"></i>
                        <span>Job Vacancies</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/admin/applicants') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/applicants') !== false ? 'active' : '' ?>">
                        <i class="fas fa-users"></i>
                        <span>Applicants</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/admin/applicants/pipeline') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/pipeline') !== false ? 'active' : '' ?>">
                        <i class="fas fa-stream"></i>
                        <span>Pipeline</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/admin/interviews') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/interviews') !== false ? 'active' : '' ?>">
                        <i class="fas fa-robot"></i>
                        <span>AI Interviews</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/admin/documents') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/documents') !== false ? 'active' : '' ?>">
                        <i class="fas fa-file-alt"></i>
                        <span>Documents</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/admin/medical') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/medical') !== false ? 'active' : '' ?>">
                        <i class="fas fa-heartbeat"></i>
                        <span>Medical</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/admin/reports') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/reports') !== false ? 'active' : '' ?>">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reports</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/admin/crewing') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/crewing') !== false ? 'active' : '' ?>">
                        <i class="fas fa-user-tie"></i>
                        <span>Crewing Staff</span>
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
                    <span class="badge">3</span>
                </button>
                
                <div class="user-dropdown">
                    <button class="user-btn">
                        <img src="<?= asset('images/avatar-default.png') ?>" alt="Avatar">
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
            <?php include APPPATH . 'Views/' . $content . '.php'; ?>
        </div>
    </div>

    <script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
