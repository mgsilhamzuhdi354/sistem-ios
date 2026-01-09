<?php
/**
 * Layout Template
 * Dark theme with gold accent
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'ERP' ?> - PT Indo Ocean</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #0A1628;
            --primary-navy: #0A2463;
            --primary-blue: #1E5AA8;
            --accent-gold: #D4AF37;
            --accent-gold-light: #E8C547;
            --text-primary: #FFFFFF;
            --text-secondary: #B8C5D3;
            --text-muted: #6B7C93;
            --card-bg: rgba(255, 255, 255, 0.05);
            --card-hover: rgba(255, 255, 255, 0.08);
            --border-color: rgba(255, 255, 255, 0.1);
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --info: #3B82F6;
            --border-radius: 12px;
            --border-radius-sm: 8px;
            --sidebar-width: 260px;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-navy) 100%);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
        }
        
        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            z-index: 100;
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 18px;
            font-weight: 700;
            color: var(--accent-gold);
        }
        
        .logo i { font-size: 24px; }
        
        .sidebar-nav {
            flex: 1;
            padding: 16px 0;
            overflow-y: auto;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        
        .nav-item:hover, .nav-item.active {
            background: var(--card-hover);
            color: var(--text-primary);
            border-left-color: var(--accent-gold);
        }
        
        .nav-item i { width: 20px; text-align: center; }
        
        .nav-divider {
            height: 1px;
            background: var(--border-color);
            margin: 16px 20px;
        }
        
        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid var(--border-color);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .user-info img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
        }
        
        .user-name { font-weight: 500; font-size: 14px; display: block; }
        .user-role { font-size: 11px; color: var(--text-muted); }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        
        .main-header {
            height: 70px;
            background: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 50;
            backdrop-filter: blur(10px);
        }
        
        .page-content {
            padding: 30px;
        }
        
        .page-header {
            margin-bottom: 24px;
        }
        
        .page-header h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .page-header p {
            color: var(--text-muted);
            font-size: 14px;
        }
        
        /* Cards */
        .card, .table-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 20px;
        }
        
        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 20px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--border-radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        
        .stat-icon.blue { background: rgba(59, 130, 246, 0.2); color: var(--info); }
        .stat-icon.gold { background: rgba(212, 175, 55, 0.2); color: var(--accent-gold); }
        .stat-icon.green { background: rgba(16, 185, 129, 0.2); color: var(--success); }
        .stat-icon.red { background: rgba(239, 68, 68, 0.2); color: var(--danger); }
        
        .stat-info h3 { font-size: 24px; font-weight: 700; margin-bottom: 4px; }
        .stat-info p { color: var(--text-muted); font-size: 13px; }
        
        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th, .data-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        
        .data-table th {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--text-muted);
            background: rgba(0, 0, 0, 0.2);
        }
        
        .data-table tr:hover {
            background: var(--card-hover);
        }
        
        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: var(--border-radius-sm);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--accent-gold), var(--accent-gold-light));
            color: var(--primary-dark);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.4);
        }
        
        .btn-secondary {
            background: var(--card-bg);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }
        
        .btn-danger { background: var(--danger); color: white; }
        .btn-success { background: var(--success); color: white; }
        
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .btn-icon { padding: 8px; border-radius: 6px; background: var(--card-bg); border: 1px solid var(--border-color); color: var(--text-secondary); cursor: pointer; }
        .btn-icon:hover { color: var(--accent-gold); }
        
        /* Status Badges */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-success { background: rgba(16, 185, 129, 0.2); color: var(--success); }
        .badge-warning { background: rgba(245, 158, 11, 0.2); color: var(--warning); }
        .badge-danger { background: rgba(239, 68, 68, 0.2); color: var(--danger); }
        .badge-info { background: rgba(59, 130, 246, 0.2); color: var(--info); }
        .badge-secondary { background: rgba(107, 124, 147, 0.2); color: var(--text-muted); }
        
        /* Forms */
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: var(--text-secondary); }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            color: var(--text-primary);
            font-size: 14px;
        }
        .form-control:focus { outline: none; border-color: var(--accent-gold); }
        .form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
        
        /* Alerts */
        .alert {
            padding: 14px 16px;
            border-radius: var(--border-radius-sm);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success { background: rgba(16, 185, 129, 0.2); border: 1px solid var(--success); color: var(--success); }
        .alert-danger, .alert-error { background: rgba(239, 68, 68, 0.2); border: 1px solid var(--danger); color: var(--danger); }
        .alert-warning { background: rgba(245, 158, 11, 0.2); border: 1px solid var(--warning); color: var(--warning); }
        
        /* Grid */
        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        
        /* Pagination */
        .pagination { display: flex; align-items: center; justify-content: space-between; margin-top: 20px; }
        .pagination-info { font-size: 13px; color: var(--text-muted); }
        .pagination-buttons { display: flex; gap: 4px; }
        .page-btn { width: 36px; height: 36px; border-radius: 6px; background: var(--card-bg); border: 1px solid var(--border-color); color: var(--text-secondary); cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .page-btn:hover, .page-btn.active { background: var(--accent-gold); color: var(--primary-dark); border-color: var(--accent-gold); }
        
        /* Premium Logout Modal */
        .logout-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0);
            backdrop-filter: blur(0px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .logout-overlay.active {
            opacity: 1;
            visibility: visible;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
        }
        
        .logout-modal {
            background: linear-gradient(145deg, rgba(20, 30, 48, 0.95), rgba(10, 20, 40, 0.98));
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 24px;
            padding: 40px;
            max-width: 420px;
            width: 90%;
            text-align: center;
            transform: scale(0.7) translateY(30px);
            opacity: 0;
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 
                0 25px 80px rgba(0, 0, 0, 0.5),
                0 0 60px rgba(212, 175, 55, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .logout-overlay.active .logout-modal {
            transform: scale(1) translateY(0);
            opacity: 1;
        }
        
        .logout-modal::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.03) 0%, transparent 60%);
            animation: shimmer 4s infinite linear;
            pointer-events: none;
        }
        
        @keyframes shimmer {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .logout-icon-wrapper {
            width: 100px;
            height: 100px;
            margin: 0 auto 24px;
            position: relative;
        }
        
        .logout-icon-bg {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(239, 68, 68, 0.05));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse-ring 2s ease-out infinite;
            position: relative;
        }
        
        .logout-icon-bg::before {
            content: '';
            position: absolute;
            width: 120%;
            height: 120%;
            border: 2px solid rgba(239, 68, 68, 0.2);
            border-radius: 50%;
            animation: ripple 2s ease-out infinite;
        }
        
        @keyframes pulse-ring {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
            70% { box-shadow: 0 0 0 20px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
        
        @keyframes ripple {
            0% { transform: scale(0.8); opacity: 1; }
            100% { transform: scale(1.3); opacity: 0; }
        }
        
        .logout-icon {
            font-size: 42px;
            color: #EF4444;
            animation: door-swing 1.5s ease-in-out infinite;
        }
        
        @keyframes door-swing {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-10deg); }
            75% { transform: rotate(10deg); }
        }
        
        .logout-title {
            font-size: 24px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 12px;
            background: linear-gradient(135deg, #fff, #B8C5D3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .logout-message {
            color: var(--text-secondary);
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 32px;
        }
        
        .logout-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
        }
        
        .logout-btn {
            padding: 14px 32px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }
        
        .logout-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .logout-btn:hover::before {
            left: 100%;
        }
        
        .logout-btn-cancel {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-secondary);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .logout-btn-cancel:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        }
        
        .logout-btn-confirm {
            background: linear-gradient(135deg, #EF4444, #DC2626);
            color: #fff;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
        }
        
        .logout-btn-confirm:hover {
            background: linear-gradient(135deg, #DC2626, #B91C1C);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.5);
        }
        
        .logout-btn-confirm:active {
            transform: translateY(0);
        }
        
        /* Floating particles */
        .logout-particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
        }
        
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(212, 175, 55, 0.6);
            border-radius: 50%;
            animation: float-up 3s ease-in-out infinite;
        }
        
        .particle:nth-child(1) { left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { left: 30%; animation-delay: 0.5s; }
        .particle:nth-child(3) { left: 50%; animation-delay: 1s; }
        .particle:nth-child(4) { left: 70%; animation-delay: 1.5s; }
        .particle:nth-child(5) { left: 90%; animation-delay: 2s; }
        
        @keyframes float-up {
            0% { bottom: -10px; opacity: 0; transform: scale(0); }
            20% { opacity: 1; transform: scale(1); }
            80% { opacity: 1; }
            100% { bottom: 100%; opacity: 0; transform: scale(0.5); }
        }
        
        /* Responsive */
        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 16px;
            left: 16px;
            z-index: 200;
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: var(--primary-navy);
            border: 1px solid var(--border-color);
            color: var(--accent-gold);
            font-size: 20px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
        }
        
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.6);
            z-index: 90;
        }
        
        .sidebar-close {
            display: none;
            position: absolute;
            top: 16px;
            right: 16px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--card-bg);
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
        }
        
        .hide-mobile { display: inline; }
        
        @media (max-width: 1200px) { 
            .grid-4 { grid-template-columns: repeat(2, 1fr); } 
        }
        
        @media (max-width: 992px) {
            .charts-grid { grid-template-columns: 1fr; }
            .alerts-grid { grid-template-columns: 1fr !important; }
        }
        
        @media (max-width: 768px) {
            .mobile-menu-btn { display: flex; }
            .sidebar-overlay.active { display: block; }
            .sidebar-close { display: flex; align-items: center; justify-content: center; }
            
            .sidebar { 
                transform: translateX(-100%); 
                transition: transform 0.3s ease;
            }
            .sidebar.open { transform: translateX(0); }
            
            .main-content { 
                margin-left: 0; 
                padding: 70px 16px 20px;
            }
            
            .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr; }
            .form-row { grid-template-columns: 1fr; }
            
            .page-header { flex-direction: column; align-items: flex-start; }
            .page-header h1 { font-size: 22px; }
            
            .stats-grid { grid-template-columns: repeat(2, 1fr) !important; gap: 12px !important; }
            .stat-card { padding: 16px !important; }
            .stat-value { font-size: 22px !important; }
            
            .data-table { font-size: 12px; }
            .data-table th, .data-table td { padding: 10px 8px; }
            
            .hide-mobile { display: none; }
            
            .btn { padding: 10px 14px; font-size: 13px; }
            .btn-sm { padding: 6px 10px; font-size: 12px; }
        }
        
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr !important; }
            .card { padding: 16px; }
        }
    </style>
</head>
<body>
    <!-- Mobile Menu Button -->
    <button class="mobile-menu-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" onclick="closeSidebar()"></div>
    
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <button class="sidebar-close" onclick="closeSidebar()">
            <i class="fas fa-times"></i>
        </button>
        <div class="sidebar-header">
            <div class="logo"><i class="fas fa-anchor"></i><span>IndoOcean ERP</span></div>
        </div>
        <nav class="sidebar-nav">
            <a href="<?= BASE_URL ?>" class="nav-item <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-th-large"></i><span data-translate="nav_dashboard">Dashboard</span>
            </a>
            <a href="<?= BASE_URL ?>contracts" class="nav-item <?= ($currentPage ?? '') === 'contracts' ? 'active' : '' ?>">
                <i class="fas fa-file-contract"></i><span data-translate="nav_contracts">Contracts</span>
            </a>
            <a href="<?= BASE_URL ?>vessels" class="nav-item <?= ($currentPage ?? '') === 'vessels' ? 'active' : '' ?>">
                <i class="fas fa-ship"></i><span data-translate="nav_vessels">Vessels</span>
            </a>
            <a href="<?= BASE_URL ?>clients" class="nav-item <?= ($currentPage ?? '') === 'clients' ? 'active' : '' ?>">
                <i class="fas fa-building"></i><span data-translate="nav_clients">Clients</span>
            </a>
            <a href="<?= BASE_URL ?>payroll" class="nav-item <?= ($currentPage ?? '') === 'payroll' ? 'active' : '' ?>">
                <i class="fas fa-money-bill-wave"></i><span data-translate="nav_payroll">Payroll</span>
            </a>
            <a href="<?= BASE_URL ?>reports" class="nav-item <?= ($currentPage ?? '') === 'reports' ? 'active' : '' ?>">
                <i class="fas fa-chart-bar"></i><span data-translate="nav_reports">Reports</span>
            </a>
            <a href="<?= BASE_URL ?>contracts/expiring" class="nav-item" style="color: var(--warning);">
                <i class="fas fa-clock"></i><span data-translate="nav_expiring">Expiring Contracts</span>
            </a>
            <div class="nav-divider"></div>
            <a href="<?= BASE_URL ?>notifications" class="nav-item <?= ($currentPage ?? '') === 'notifications' ? 'active' : '' ?>">
                <i class="fas fa-bell"></i><span data-translate="nav_notifications">Notifications</span>
            </a>
            <a href="<?= BASE_URL ?>settings" class="nav-item <?= ($currentPage ?? '') === 'settings' ? 'active' : '' ?>">
                <i class="fas fa-cog"></i><span data-translate="nav_settings">Settings</span>
            </a>
            <a href="<?= BASE_URL ?>users" class="nav-item <?= ($currentPage ?? '') === 'users' ? 'active' : '' ?>">
                <i class="fas fa-users-cog"></i><span data-translate="nav_users">Users</span>
            </a>
        </nav>
        <div class="sidebar-footer">
            <?php $currentUser = $this->getCurrentUser(); ?>
            <?php if ($currentUser): ?>
            <div class="user-info" style="display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($currentUser['full_name'] ?? 'User') ?>&background=D4AF37&color=fff" alt="User">
                    <div>
                        <span class="user-name"><?= htmlspecialchars($currentUser['full_name'] ?? 'User') ?></span>
                        <span class="user-role"><?= ucfirst(str_replace('_', ' ', $currentUser['role'] ?? 'Guest')) ?></span>
                    </div>
                </div>
                <a href="javascript:void(0)" title="Logout" style="color: var(--danger); padding: 8px;" onclick="showLogoutModal()">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
            <?php else: ?>
            <a href="<?= BASE_URL ?>auth/login" class="btn btn-primary" style="width: 100%; text-align: center;">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
            <?php endif; ?>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header class="main-header">
            <div class="header-left">
                <h2 style="font-size: 16px;"><?= $title ?? 'Dashboard' ?></h2>
            </div>
            <div class="header-right" style="position: relative;">
                <a href="<?= BASE_URL ?>notifications" class="btn-icon notification-btn" style="position: relative;">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge" id="notif-count" style="display: none; position: absolute; top: -5px; right: -5px; background: var(--danger); color: white; font-size: 10px; padding: 2px 6px; border-radius: 10px;">0</span>
                </a>
            </div>
        </header>

        <div class="page-content">
            <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= $flash['type'] ?>">
                    <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'error' ? 'exclamation-circle' : 'exclamation-triangle') ?>"></i>
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>
            
            <?= $content ?? '' ?>
        </div>
    </main>
    
    <!-- Theme and Language Script -->
    <script src="<?= BASE_URL ?>assets/js/app.js"></script>
    <script>
        // Apply theme immediately to prevent flash
        (function() {
            const theme = localStorage.getItem('erp_theme') || 'gold';
            const themeColors = {
                gold: { accent: '#D4AF37', accentLight: '#E8C547' },
                blue: { accent: '#3B82F6', accentLight: '#60A5FA' },
                green: { accent: '#10B981', accentLight: '#34D399' },
                purple: { accent: '#8B5CF6', accentLight: '#A78BFA' },
                red: { accent: '#EF4444', accentLight: '#F87171' },
                teal: { accent: '#14B8A6', accentLight: '#2DD4BF' }
            };
            const colors = themeColors[theme] || themeColors['gold'];
            document.documentElement.style.setProperty('--accent-gold', colors.accent);
            document.documentElement.style.setProperty('--accent-gold-light', colors.accentLight);
            
            // Update language selector
            document.addEventListener('DOMContentLoaded', function() {
                const langSelect = document.getElementById('languageSelect');
                if (langSelect) {
                    langSelect.value = localStorage.getItem('erp_language') || 'id';
                }
                
                // Highlight active theme button
                document.querySelectorAll('.theme-btn').forEach(btn => {
                    if (btn.dataset.theme === theme) {
                        btn.style.borderColor = '#fff';
                        btn.style.boxShadow = '0 0 10px rgba(255,255,255,0.5)';
                    }
                    btn.addEventListener('click', function() {
                        document.querySelectorAll('.theme-btn').forEach(b => {
                            b.style.borderColor = 'transparent';
                            b.style.boxShadow = 'none';
                        });
                        this.style.borderColor = '#fff';
                        this.style.boxShadow = '0 0 10px rgba(255,255,255,0.5)';
                    });
                });
            });
        })();
    </script>
    
    <!-- Mobile Sidebar Toggle & Dashboard Auto-refresh -->
    <script>
        // Sidebar Toggle for Mobile
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.querySelector('.sidebar-overlay').classList.toggle('active');
        }
        
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.querySelector('.sidebar-overlay').classList.remove('active');
        }
        
        // Close sidebar when clicking nav items on mobile
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    closeSidebar();
                }
            });
        });
        
        // Dashboard Auto-refresh (every 5 minutes)
        let lastRefreshTime = new Date();
        
        function updateLastRefreshDisplay() {
            const now = new Date();
            const diffMinutes = Math.floor((now - lastRefreshTime) / 60000);
            const display = document.getElementById('lastUpdate');
            if (display) {
                if (diffMinutes === 0) {
                    display.textContent = 'Just now';
                } else if (diffMinutes === 1) {
                    display.textContent = '1 min ago';
                } else {
                    display.textContent = diffMinutes + ' mins ago';
                }
            }
        }
        
        function refreshDashboard() {
            const period = document.getElementById('periodFilter');
            if (period) {
                // Add spinning animation to refresh icon
                const icon = document.querySelector('#refreshIndicator i');
                if (icon) {
                    icon.classList.add('fa-spin');
                    setTimeout(() => icon.classList.remove('fa-spin'), 1000);
                }
                
                // Reload with period parameter
                const url = new URL(window.location.href);
                url.searchParams.set('period', period.value);
                window.location.href = url.toString();
            }
        }
        
        // Auto-refresh dashboard every 5 minutes
        if (document.getElementById('periodFilter')) {
            setInterval(() => {
                updateLastRefreshDisplay();
            }, 60000);
            
            // Optional: auto-refresh data
            // setInterval(refreshDashboard, 300000); // 5 minutes
        }
    </script>
    
    <!-- Premium Logout Modal -->
    <div class="logout-overlay" id="logoutModal" onclick="closeLogoutModal(event)">
        <div class="logout-modal" onclick="event.stopPropagation()">
            <div class="logout-particles">
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
            </div>
            <div class="logout-icon-wrapper">
                <div class="logout-icon-bg">
                    <i class="fas fa-sign-out-alt logout-icon"></i>
                </div>
            </div>
            <h2 class="logout-title">Keluar dari Sistem?</h2>
            <p class="logout-message">
                Anda akan keluar dari sesi aktif. Pastikan semua pekerjaan sudah tersimpan sebelum melanjutkan.
            </p>
            <div class="logout-buttons">
                <button class="logout-btn logout-btn-cancel" onclick="closeLogoutModal()">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </button>
                <a href="<?= BASE_URL ?>auth/logout" class="logout-btn logout-btn-confirm">
                    <i class="fas fa-sign-out-alt"></i>
                    Ya, Keluar
                </a>
            </div>
        </div>
    </div>
    
    <script>
        // Logout Modal Functions
        function showLogoutModal() {
            document.getElementById('logoutModal').classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent scroll
        }
        
        function closeLogoutModal(event) {
            if (event && event.target !== event.currentTarget) return;
            document.getElementById('logoutModal').classList.remove('active');
            document.body.style.overflow = ''; // Restore scroll
        }
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('logoutModal').classList.contains('active')) {
                closeLogoutModal();
            }
        });
    </script>
</body>
</html>
