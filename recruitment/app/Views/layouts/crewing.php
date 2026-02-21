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
    <style>
        /* Collapsible Section Styles for Crewing */
        .nav-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.7);
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
            background: rgba(30, 90, 168, 0.15);
            color: #fff;
        }

        .nav-section-header i.section-icon {
            margin-right: 10px;
            font-size: 14px;
        }

        .nav-section-header i.toggle-icon {
            transition: transform 0.3s;
            font-size: 10px;
        }

        .nav-section.open .nav-section-header i.toggle-icon {
            transform: rotate(180deg);
        }

        .nav-section.open .nav-section-header {
            color: #fff;
            background: rgba(30, 90, 168, 0.2);
        }

        .nav-submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            padding-left: 15px;
        }

        .nav-section.open .nav-submenu {
            max-height: 500px;
        }

        .nav-submenu .nav-link {
            padding: 10px 15px 10px 25px;
            font-size: 13px;
        }

        .nav-submenu .nav-link i {
            font-size: 12px;
            width: 18px;
        }

        .nav-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: 10px 20px;
        }
    </style>
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
            <?php
            $uri = $_SERVER['REQUEST_URI'];
            $isRecruitmentSection = strpos($uri, '/crewing/pipeline') !== false || strpos($uri, '/crewing/vacancies') !== false || strpos($uri, '/crewing/interviews') !== false;
            $isOperationalSection = strpos($uri, '/crewing/manual-entr') !== false || strpos($uri, '/crewing/email') !== false || strpos($uri, '/crewing/daily-report') !== false || strpos($uri, '/crewing/applications') !== false;
            $isSettingsSection = strpos($uri, '/crewing/settings') !== false || strpos($uri, '/crewing/profile') !== false;
            ?>

            <!-- Dashboard - Standalone -->
            <ul>
                <li class="nav-item">
                    <a href="<?= url('/crewing/dashboard') ?>" class="nav-link <?= strpos($uri, '/crewing/dashboard') !== false ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        <span><?= t('nav.dashboard') ?></span>
                    </a>
                </li>
            </ul>

            <div class="nav-divider"></div>

            <!-- Rekrutmen Section -->
            <div class="nav-section <?= $isRecruitmentSection ? 'open' : '' ?>">
                <div class="nav-section-header">
                    <span><i class="fas fa-briefcase section-icon"></i> Rekrutmen</span>
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </div>
                <ul class="nav-submenu">
                    <li class="nav-item">
                        <a href="<?= url('/crewing/pipeline') ?>" class="nav-link <?= strpos($uri, '/crewing/pipeline') !== false ? 'active' : '' ?>">
                            <i class="fas fa-stream"></i>
                            <span><?= t('nav.pipeline') ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/crewing/vacancies') ?>" class="nav-link <?= strpos($uri, '/crewing/vacancies') !== false ? 'active' : '' ?>">
                            <i class="fas fa-briefcase"></i>
                            <span>Lowongan Kerja</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/crewing/interviews') ?>" class="nav-link <?= strpos($uri, '/crewing/interviews') !== false ? 'active' : '' ?>">
                            <i class="fas fa-robot"></i>
                            <span>AI Interview</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Operasional Section -->
            <div class="nav-section <?= $isOperationalSection ? 'open' : '' ?>">
                <div class="nav-section-header">
                    <span><i class="fas fa-clipboard-list section-icon"></i> Operasional</span>
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </div>
                <ul class="nav-submenu">
                    <li class="nav-item">
                        <a href="<?= url('/crewing/applications') ?>" class="nav-link <?= strpos($uri, '/crewing/applications') !== false ? 'active' : '' ?>">
                            <i class="fas fa-file-alt"></i>
                            <span>Lamaran Masuk</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/crewing/manual-entries') ?>" class="nav-link <?= strpos($uri, '/crewing/manual-entr') !== false ? 'active' : '' ?>">
                            <i class="fas fa-user-plus"></i>
                            <span>Input Manual</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/crewing/email') ?>" class="nav-link <?= strpos($uri, '/crewing/email') !== false ? 'active' : '' ?>">
                            <i class="fas fa-envelope"></i>
                            <span>Email Center</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/crewing/daily-report') ?>" class="nav-link <?= strpos($uri, '/crewing/daily-report') !== false ? 'active' : '' ?>">
                            <i class="fas fa-chart-line"></i>
                            <span>Laporan Harian</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Pengaturan Section -->
            <div class="nav-section <?= $isSettingsSection ? 'open' : '' ?>">
                <div class="nav-section-header">
                    <span><i class="fas fa-cog section-icon"></i> Pengaturan</span>
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </div>
                <ul class="nav-submenu">
                    <li class="nav-item">
                        <a href="<?= url('/crewing/profile') ?>" class="nav-link <?= strpos($uri, '/crewing/profile') !== false ? 'active' : '' ?>">
                            <i class="fas fa-user-circle"></i>
                            <span>Profil Saya</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/crewing/settings') ?>" class="nav-link <?= strpos($uri, '/crewing/settings') !== false ? 'active' : '' ?>">
                            <i class="fas fa-cog"></i>
                            <span><?= t('nav.settings') ?></span>
                        </a>
                    </li>
                </ul>
            </div>

            <?php if (isAdmin()): ?>
            <div class="nav-divider"></div>
            <ul>
                <li class="nav-item">
                    <a href="<?= url('/admin/dashboard') ?>" class="nav-link">
                        <i class="fas fa-user-shield"></i>
                        <span>Admin Panel</span>
                    </a>
                </li>
            </ul>
            <?php endif; ?>
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
            
            <div class="header-actions" style="margin-left: auto; display: flex; align-items: center; gap: 12px;">
                <!-- Notification Bell -->
                <div class="notification-wrapper" style="position: relative;">
                    <button class="notification-btn" id="notificationBtn" title="Notifikasi" style="background:none;border:none;cursor:pointer;position:relative;padding:8px;color:#666;font-size:18px;">
                        <i class="fas fa-bell"></i>
                        <span class="notif-badge" id="notifBadge" style="display:none;position:absolute;top:2px;right:2px;background:#dc2626;color:#fff;border-radius:50%;width:18px;height:18px;font-size:10px;font-weight:700;line-height:18px;text-align:center;">0</span>
                    </button>
                    
                    <!-- Notification Dropdown -->
                    <div class="notif-dropdown" id="notifDropdown" style="display:none;position:absolute;right:0;top:45px;width:380px;max-height:480px;background:#fff;border-radius:12px;box-shadow:0 10px 40px rgba(0,0,0,0.15);z-index:1000;overflow:hidden;">
                        <div style="padding:16px 20px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center;">
                            <h4 style="margin:0;font-size:15px;font-weight:600;color:#1e293b;">Notifikasi</h4>
                            <button id="markAllReadBtn" style="background:none;border:none;color:#3b82f6;font-size:12px;cursor:pointer;font-weight:500;">Tandai semua dibaca</button>
                        </div>
                        <div id="notifList" style="max-height:380px;overflow-y:auto;"></div>
                        <div id="notifEmpty" style="display:none;padding:40px 20px;text-align:center;color:#94a3b8;">
                            <i class="fas fa-bell-slash" style="font-size:32px;margin-bottom:12px;opacity:0.5;"></i>
                            <p style="margin:0;font-size:13px;">Belum ada notifikasi</p>
                        </div>
                    </div>
                </div>
                
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
    <script>
        // Collapsible section toggle
        document.querySelectorAll('.nav-section-header').forEach(header => {
            header.addEventListener('click', function () {
                const section = this.parentElement;
                section.classList.toggle('open');
            });
        });

        // ===========================
        // Notification Bell System
        // ===========================
        (function() {
            const bell = document.getElementById('notificationBtn');
            const dropdown = document.getElementById('notifDropdown');
            const badge = document.getElementById('notifBadge');
            const list = document.getElementById('notifList');
            const empty = document.getElementById('notifEmpty');
            const markAllBtn = document.getElementById('markAllReadBtn');
            const fetchUrl = '<?= url("/crewing/notifications/fetch") ?>';
            const markReadUrl = '<?= url("/crewing/notifications/mark-read") ?>';
            const markAllUrl = '<?= url("/crewing/notifications/mark-all-read") ?>';
            let isOpen = false;

            // Toggle dropdown
            bell.addEventListener('click', function(e) {
                e.stopPropagation();
                isOpen = !isOpen;
                dropdown.style.display = isOpen ? 'block' : 'none';
                if (isOpen) fetchNotifications();
            });

            // Close on outside click
            document.addEventListener('click', function(e) {
                if (!dropdown.contains(e.target) && e.target !== bell) {
                    isOpen = false;
                    dropdown.style.display = 'none';
                }
            });

            // Fetch notifications
            function fetchNotifications() {
                fetch(fetchUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => {
                    // Update badge
                    if (data.unread_count > 0) {
                        badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                        badge.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }

                    // Render notifications
                    if (data.notifications.length === 0) {
                        list.style.display = 'none';
                        empty.style.display = 'block';
                        return;
                    }

                    list.style.display = 'block';
                    empty.style.display = 'none';
                    list.innerHTML = data.notifications.map(n => {
                        const typeIcon = {
                            'info': 'fa-info-circle',
                            'success': 'fa-check-circle', 
                            'warning': 'fa-exclamation-triangle',
                            'error': 'fa-times-circle'
                        }[n.type] || 'fa-bell';
                        const typeColor = {
                            'info': '#3b82f6',
                            'success': '#10b981',
                            'warning': '#f59e0b',
                            'error': '#ef4444'
                        }[n.type] || '#6b7280';
                        const unreadBg = n.is_read ? '' : 'background:#f0f7ff;';
                        
                        return `
                            <div class="notif-item" data-id="${n.id}" style="padding:14px 20px;border-bottom:1px solid #f1f5f9;cursor:pointer;transition:background 0.2s;${unreadBg}" 
                                 onmouseover="this.style.background='#f8fafc'" 
                                 onmouseout="this.style.background='${n.is_read ? '' : '#f0f7ff'}'">
                                <div style="display:flex;gap:12px;align-items:flex-start;">
                                    <i class="fas ${typeIcon}" style="color:${typeColor};font-size:16px;margin-top:3px;flex-shrink:0;"></i>
                                    <div style="flex:1;min-width:0;">
                                        <div style="font-size:13px;font-weight:${n.is_read ? '400' : '600'};color:#1e293b;margin-bottom:4px;">${n.title}</div>
                                        <div style="font-size:12px;color:#64748b;line-height:1.4;margin-bottom:6px;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">${n.message}</div>
                                        <div style="font-size:11px;color:#94a3b8;">${n.time_ago}</div>
                                    </div>
                                    ${!n.is_read ? '<div style="width:8px;height:8px;background:#3b82f6;border-radius:50%;flex-shrink:0;margin-top:6px;"></div>' : ''}
                                </div>
                            </div>
                        `;
                    }).join('');

                    // Click handlers for individual notifications
                    list.querySelectorAll('.notif-item').forEach(item => {
                        item.addEventListener('click', function() {
                            const id = this.dataset.id;
                            const notif = data.notifications.find(n => n.id == id);
                            
                            // Mark as read
                            fetch(markReadUrl + '/' + id, {
                                method: 'POST',
                                headers: { 
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: 'csrf_token=<?= csrf_token() ?>'
                            }).then(() => fetchNotifications());

                            // Navigate if action URL exists
                            if (notif && notif.action_url) {
                                window.location.href = notif.action_url;
                            }
                        });
                    });
                })
                .catch(err => console.error('Notification fetch error:', err));
            }

            // Mark all as read
            markAllBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                fetch(markAllUrl, {
                    method: 'POST',
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'csrf_token=<?= csrf_token() ?>'
                }).then(() => fetchNotifications());
            });

            // Initial fetch + auto-refresh every 30s
            fetchNotifications();
            setInterval(fetchNotifications, 30000);
        })();
    </script>
</body>
</html>


