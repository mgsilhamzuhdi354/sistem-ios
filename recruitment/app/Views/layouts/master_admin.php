<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Dashboard' ?> - Master Admin | PT Indo Ocean Crew Services</title>
    <link rel="icon" type="image/jpeg" href="<?= asset('images/logo.jpg') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <style>
        /* Master Admin-specific color override - Red accent */
        .admin-sidebar { background: linear-gradient(180deg, #1e3a5f 0%, #0d1f33 100%); }
        .nav-link.active { background: linear-gradient(90deg, #dc2626 0%, #b91c1c 100%); }
        .nav-link:hover { background: rgba(220, 38, 38, 0.2); }
        .stat-card.master-accent { border-left: 4px solid #dc2626; }
        
        /* Dropdown Menu Styles */
        .nav-section { margin-bottom: 5px; }
        .nav-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            color: rgba(255,255,255,0.7);
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.3s;
            border-radius: 8px;
            margin: 0 10px;
        }
        .nav-section-header:hover {
            background: rgba(220, 38, 38, 0.15);
            color: #fff;
        }
        .nav-section-header i.section-icon { margin-right: 10px; font-size: 14px; }
        .nav-section-header i.toggle-icon { transition: transform 0.3s; font-size: 10px; }
        .nav-section.open .nav-section-header i.toggle-icon { transform: rotate(180deg); }
        .nav-section.open .nav-section-header { color: #fff; background: rgba(220, 38, 38, 0.2); }
        
        .nav-submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            padding-left: 15px;
        }
        .nav-section.open .nav-submenu { max-height: 500px; }
        
        .nav-submenu .nav-link {
            padding: 10px 15px 10px 25px;
            font-size: 13px;
        }
        .nav-submenu .nav-link i { font-size: 12px; width: 18px; }
        
        .nav-divider {
            height: 1px;
            background: rgba(255,255,255,0.1);
            margin: 10px 20px;
        }
    </style>
</head>
<body class="admin-body">
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="<?= url('/master-admin') ?>" class="logo">
                <img src="<?= asset('images/logo.jpg') ?>" alt="Indo Ocean" style="width:32px;height:32px;object-fit:contain;">
                <span>Master Admin</span>
            </a>
        </div>
        
        <nav class="sidebar-nav">
            <?php
            $uri = $_SERVER['REQUEST_URI'];
            $isUsersSection = strpos($uri, '/master-admin/users') !== false;
            $isRecruitmentSection = strpos($uri, '/master-admin/vacancies') !== false || strpos($uri, '/master-admin/pipeline') !== false || strpos($uri, '/master-admin/requests') !== false;
            $isApplicationsSection = strpos($uri, '/admin/interviews') !== false || strpos($uri, '/admin/documents') !== false || strpos($uri, '/admin/medical') !== false || strpos($uri, '/master-admin/archive') !== false;
            $isSystemSection = strpos($uri, '/master-admin/reports') !== false || strpos($uri, '/master-admin/settings') !== false || strpos($uri, '/master-admin/email-settings') !== false || strpos($uri, '/master-admin/profile') !== false;
            
            // Get pending requests count
            global $db;
            $pendingCount = 0;
            if ($db) {
                $result = @$db->query("SELECT COUNT(*) as c FROM status_change_requests WHERE status = 'pending'");
                if ($result) {
                    $pendingCount = $result->fetch_assoc()['c'];
                }
            }
            ?>
            
            <!-- Dashboard - Standalone -->
            <ul>
                <li class="nav-item">
                    <a href="<?= url('/master-admin/dashboard') ?>" class="nav-link <?= strpos($uri, '/master-admin/dashboard') !== false ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            </ul>
            
            <div class="nav-divider"></div>
            
            <!-- Users Section -->
            <div class="nav-section <?= $isUsersSection ? 'open' : '' ?>">
                <div class="nav-section-header">
                    <span><i class="fas fa-users section-icon"></i> Pengguna</span>
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </div>
                <ul class="nav-submenu">
                    <li class="nav-item">
                        <a href="<?= url('/master-admin/users') ?>" class="nav-link <?= strpos($uri, '/master-admin/users') !== false && strpos($uri, '/online') === false ? 'active' : '' ?>">
                            <i class="fas fa-users-cog"></i>
                            <span>Manajemen User</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/master-admin/users/online') ?>" class="nav-link <?= strpos($uri, '/users/online') !== false ? 'active' : '' ?>">
                            <i class="fas fa-signal"></i>
                            <span>Aktivitas User</span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Recruitment Section -->
            <div class="nav-section <?= $isRecruitmentSection ? 'open' : '' ?>">
                <div class="nav-section-header">
                    <span><i class="fas fa-briefcase section-icon"></i> Rekrutmen</span>
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </div>
                <ul class="nav-submenu">
                    <li class="nav-item">
                        <a href="<?= url('/master-admin/vacancies') ?>" class="nav-link <?= strpos($uri, '/master-admin/vacancies') !== false ? 'active' : '' ?>">
                            <i class="fas fa-briefcase"></i>
                            <span>Lowongan Kerja</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/master-admin/pipeline') ?>" class="nav-link <?= strpos($uri, '/master-admin/pipeline') !== false ? 'active' : '' ?>">
                            <i class="fas fa-stream"></i>
                            <span>Pipeline</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/master-admin/requests') ?>" class="nav-link <?= strpos($uri, '/master-admin/requests') !== false ? 'active' : '' ?>">
                            <i class="fas fa-clipboard-check"></i>
                            <span>Permintaan</span>
                            <?php if ($pendingCount > 0): ?>
                            <span class="badge-pending"><?= $pendingCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Applications Section -->
            <div class="nav-section <?= $isApplicationsSection ? 'open' : '' ?>">
                <div class="nav-section-header">
                    <span><i class="fas fa-folder-open section-icon"></i> Lamaran</span>
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </div>
                <ul class="nav-submenu">
                    <li class="nav-item">
                        <a href="<?= url('/admin/interviews') ?>" class="nav-link <?= strpos($uri, '/admin/interviews') !== false ? 'active' : '' ?>">
                            <i class="fas fa-robot"></i>
                            <span>AI Interview</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/admin/documents') ?>" class="nav-link <?= strpos($uri, '/admin/documents') !== false ? 'active' : '' ?>">
                            <i class="fas fa-file-alt"></i>
                            <span>Dokumen</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/admin/medical') ?>" class="nav-link <?= strpos($uri, '/admin/medical') !== false ? 'active' : '' ?>">
                            <i class="fas fa-heartbeat"></i>
                            <span>Medical</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/master-admin/archive') ?>" class="nav-link <?= strpos($uri, '/master-admin/archive') !== false ? 'active' : '' ?>">
                            <i class="fas fa-archive"></i>
                            <span>Arsip</span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- System Section -->
            <div class="nav-section <?= $isSystemSection ? 'open' : '' ?>">
                <div class="nav-section-header">
                    <span><i class="fas fa-cog section-icon"></i> Sistem</span>
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </div>
                <ul class="nav-submenu">
                    <li class="nav-item">
                        <a href="<?= url('/master-admin/reports') ?>" class="nav-link <?= strpos($uri, '/master-admin/reports') !== false ? 'active' : '' ?>">
                            <i class="fas fa-chart-bar"></i>
                            <span>Laporan</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/master-admin/settings') ?>" class="nav-link <?= strpos($uri, '/master-admin/settings') !== false ? 'active' : '' ?>">
                            <i class="fas fa-cog"></i>
                            <span>Pengaturan</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/master-admin/email-settings') ?>" class="nav-link <?= strpos($uri, '/master-admin/email-settings') !== false ? 'active' : '' ?>">
                            <i class="fas fa-envelope"></i>
                            <span>Email</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/master-admin/permissions') ?>" class="nav-link <?= strpos($uri, '/master-admin/permissions') !== false ? 'active' : '' ?>">
                            <i class="fas fa-shield-alt"></i>
                            <span>Hak Akses</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/master-admin/profile') ?>" class="nav-link <?= strpos($uri, '/master-admin/profile') !== false ? 'active' : '' ?>">
                            <i class="fas fa-user-circle"></i>
                            <span>Profil Saya</span>
                        </a>
                    </li>
                </ul>
            </div>
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
                </button>
                
                <div class="user-dropdown">
                    <button class="user-btn">
                        <div class="user-avatar">
                            <?php 
                            $userName = $_SESSION['user_name'] ?? 'Master Admin';
                            $initials = strtoupper(substr($userName, 0, 2));
                            ?>
                            <span><?= $initials ?></span>
                        </div>
                        <span class="user-name"><?= $userName ?></span>
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
            <?php include APPPATH . 'Views/master_admin/' . $content . '_content.php'; ?>
        </div>
    </div>

    <script src="<?= asset('js/admin.js') ?>"></script>
    <script>
        // Dropdown menu toggle
        document.querySelectorAll('.nav-section-header').forEach(header => {
            header.addEventListener('click', function() {
                const section = this.parentElement;
                section.classList.toggle('open');
            });
        });
    </script>
</body>
</html>
