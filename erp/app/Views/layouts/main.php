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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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

        .logo i {
            font-size: 24px;
        }

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

        .nav-item:hover,
        .nav-item.active {
            background: var(--card-hover);
            color: var(--text-primary);
            border-left-color: var(--accent-gold);
        }

        .nav-item i {
            width: 20px;
            text-align: center;
        }

        .nav-divider {
            height: 1px;
            background: var(--border-color);
            margin: 16px 20px;
        }

        /* Section Header */
        .nav-section-header {
            padding: 16px 20px 8px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
        }

        /* Dropdown Menu Styles */
        .nav-dropdown {
            position: relative;
        }

        .nav-dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            cursor: pointer;
            width: 100%;
            background: none;
            border-right: none;
            border-top: none;
            border-bottom: none;
            font-size: 14px;
            font-family: inherit;
        }

        .nav-dropdown-toggle:hover {
            background: var(--card-hover);
            color: var(--text-primary);
        }

        .nav-dropdown-toggle i:first-child {
            width: 20px;
            text-align: center;
        }

        .nav-dropdown-toggle .dropdown-arrow {
            margin-left: auto;
            transition: transform 0.3s ease;
            font-size: 12px;
        }

        .nav-dropdown.open .dropdown-arrow {
            transform: rotate(180deg);
        }

        .nav-dropdown.open>.nav-dropdown-toggle {
            background: var(--card-hover);
            color: var(--accent-gold);
            border-left-color: var(--accent-gold);
        }

        .nav-dropdown-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background: rgba(0, 0, 0, 0.15);
        }

        .nav-dropdown.open>.nav-dropdown-menu {
            max-height: 500px;
        }

        .nav-dropdown-menu .nav-item {
            padding: 10px 20px 10px 52px;
            font-size: 13px;
            border-left: none;
        }

        .nav-dropdown-menu .nav-item:hover,
        .nav-dropdown-menu .nav-item.active {
            background: rgba(212, 175, 55, 0.1);
            color: var(--accent-gold);
        }

        /* Nested Dropdown (Level 2) */
        .nav-dropdown-menu .nav-dropdown-toggle {
            padding: 10px 20px 10px 52px;
            font-size: 13px;
        }

        .nav-dropdown-menu .nav-dropdown-menu .nav-item {
            padding-left: 72px;
            font-size: 12px;
        }

        /* Section Colors */
        .nav-section-crew .nav-dropdown-toggle:hover,
        .nav-section-crew .nav-dropdown.open>.nav-dropdown-toggle {
            border-left-color: var(--info);
            color: var(--info);
        }

        .nav-section-karyawan .nav-dropdown-toggle:hover,
        .nav-section-karyawan .nav-dropdown.open>.nav-dropdown-toggle {
            border-left-color: var(--success);
            color: var(--success);
        }

        .nav-section-recruitment .nav-dropdown-toggle:hover,
        .nav-section-recruitment .nav-dropdown.open>.nav-dropdown-toggle {
            border-left-color: var(--warning);
            color: var(--warning);
        }

        .nav-section-monitoring .nav-dropdown-toggle:hover,
        .nav-section-monitoring .nav-dropdown.open>.nav-dropdown-toggle {
            border-left-color: #8B5CF6;
            color: #8B5CF6;
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

        .user-name {
            font-weight: 500;
            font-size: 14px;
            display: block;
        }

        .user-role {
            font-size: 11px;
            color: var(--text-muted);
        }

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
        .card,
        .table-card {
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

        .stat-icon.blue {
            background: rgba(59, 130, 246, 0.2);
            color: var(--info);
        }

        .stat-icon.gold {
            background: rgba(212, 175, 55, 0.2);
            color: var(--accent-gold);
        }

        .stat-icon.green {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success);
        }

        .stat-icon.red {
            background: rgba(239, 68, 68, 0.2);
            color: var(--danger);
        }

        .stat-info h3 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .stat-info p {
            color: var(--text-muted);
            font-size: 13px;
        }

        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
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

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        .btn-icon {
            padding: 8px;
            border-radius: 6px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            cursor: pointer;
        }

        .btn-icon:hover {
            color: var(--accent-gold);
        }

        /* Status Badges */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-success {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success);
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.2);
            color: var(--warning);
        }

        .badge-danger {
            background: rgba(239, 68, 68, 0.2);
            color: var(--danger);
        }

        .badge-info {
            background: rgba(59, 130, 246, 0.2);
            color: var(--info);
        }

        .badge-secondary {
            background: rgba(107, 124, 147, 0.2);
            color: var(--text-muted);
        }

        /* Forms */
        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 6px;
            color: var(--text-secondary);
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            color: var(--text-primary);
            font-size: 14px;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-gold);
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        /* Alerts */
        .alert {
            padding: 14px 16px;
            border-radius: var(--border-radius-sm);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid var(--success);
            color: var(--success);
        }

        .alert-danger,
        .alert-error {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid var(--danger);
            color: var(--danger);
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.2);
            border: 1px solid var(--warning);
            color: var(--warning);
        }

        /* Grid */
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        /* Pagination */
        .pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
        }

        .pagination-info {
            font-size: 13px;
            color: var(--text-muted);
        }

        .pagination-buttons {
            display: flex;
            gap: 4px;
        }

        .page-btn {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .page-btn:hover,
        .page-btn.active {
            background: var(--accent-gold);
            color: var(--primary-dark);
            border-color: var(--accent-gold);
        }

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
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
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
            0% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
            }

            70% {
                box-shadow: 0 0 0 20px rgba(239, 68, 68, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        @keyframes ripple {
            0% {
                transform: scale(0.8);
                opacity: 1;
            }

            100% {
                transform: scale(1.3);
                opacity: 0;
            }
        }

        .logout-icon {
            font-size: 42px;
            color: #EF4444;
            animation: door-swing 1.5s ease-in-out infinite;
        }

        @keyframes door-swing {

            0%,
            100% {
                transform: rotate(0deg);
            }

            25% {
                transform: rotate(-10deg);
            }

            75% {
                transform: rotate(10deg);
            }
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
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
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

        .particle:nth-child(1) {
            left: 10%;
            animation-delay: 0s;
        }

        .particle:nth-child(2) {
            left: 30%;
            animation-delay: 0.5s;
        }

        .particle:nth-child(3) {
            left: 50%;
            animation-delay: 1s;
        }

        .particle:nth-child(4) {
            left: 70%;
            animation-delay: 1.5s;
        }

        .particle:nth-child(5) {
            left: 90%;
            animation-delay: 2s;
        }

        @keyframes float-up {
            0% {
                bottom: -10px;
                opacity: 0;
                transform: scale(0);
            }

            20% {
                opacity: 1;
                transform: scale(1);
            }

            80% {
                opacity: 1;
            }

            100% {
                bottom: 100%;
                opacity: 0;
                transform: scale(0.5);
            }
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
            background: rgba(0, 0, 0, 0.6);
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

        .hide-mobile {
            display: inline;
        }

        @media (max-width: 1200px) {
            .grid-4 {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 992px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }

            .alerts-grid {
                grid-template-columns: 1fr !important;
            }
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: flex;
            }

            .sidebar-overlay.active {
                display: block;
            }

            .sidebar-close {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 70px 16px 20px;
            }

            .grid-2,
            .grid-3,
            .grid-4 {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-header h1 {
                font-size: 22px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 12px !important;
            }

            .stat-card {
                padding: 16px !important;
            }

            .stat-value {
                font-size: 22px !important;
            }

            .data-table {
                font-size: 12px;
            }

            .data-table th,
            .data-table td {
                padding: 10px 8px;
            }

            .hide-mobile {
                display: none;
            }

            .btn {
                padding: 10px 14px;
                font-size: 13px;
            }

            .btn-sm {
                padding: 6px 10px;
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr !important;
            }

            .card {
                padding: 16px;
            }
        }

        /* Premium Success Popup Modal */
        .success-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0);
            backdrop-filter: blur(0px);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .success-modal-overlay.active {
            opacity: 1;
            visibility: visible;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
        }

        .success-modal {
            background: linear-gradient(145deg, rgba(20, 30, 48, 0.95), rgba(10, 20, 40, 0.98));
            border: 1px solid rgba(16, 185, 129, 0.3);
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
                0 0 60px rgba(16, 185, 129, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }

        .success-modal-overlay.active .success-modal {
            transform: scale(1) translateY(0);
            opacity: 1;
        }

        .success-modal::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.05) 0%, transparent 60%);
            animation: success-shimmer 4s infinite linear;
            pointer-events: none;
        }

        @keyframes success-shimmer {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .success-icon-wrapper {
            width: 100px;
            height: 100px;
            margin: 0 auto 24px;
            position: relative;
        }

        .success-icon-bg {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(16, 185, 129, 0.05));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: success-pulse 2s ease-out infinite;
            position: relative;
        }

        .success-icon-bg::before {
            content: '';
            position: absolute;
            width: 120%;
            height: 120%;
            border: 2px solid rgba(16, 185, 129, 0.2);
            border-radius: 50%;
            animation: success-ripple 2s ease-out infinite;
        }

        @keyframes success-pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
            }

            70% {
                box-shadow: 0 0 0 20px rgba(16, 185, 129, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        @keyframes success-ripple {
            0% {
                transform: scale(0.8);
                opacity: 1;
            }

            100% {
                transform: scale(1.3);
                opacity: 0;
            }
        }

        .success-icon {
            font-size: 42px;
            color: #10B981;
            animation: success-pop 0.6s ease-out;
        }

        @keyframes success-pop {
            0% {
                transform: scale(0);
                opacity: 0;
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .success-checkmark {
            stroke-dasharray: 100;
            stroke-dashoffset: 100;
            animation: success-draw 0.8s ease-out 0.3s forwards;
        }

        @keyframes success-draw {
            to {
                stroke-dashoffset: 0;
            }
        }

        .success-title {
            font-size: 24px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 12px;
            background: linear-gradient(135deg, #10B981, #34D399);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .success-message {
            color: var(--text-secondary);
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .success-btn {
            padding: 14px 40px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            background: linear-gradient(135deg, #10B981, #059669);
            color: #fff;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
            position: relative;
            overflow: hidden;
        }

        .success-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .success-btn:hover::before {
            left: 100%;
        }

        .success-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5);
        }

        .success-confetti {
            position: absolute;
            width: 8px;
            height: 8px;
            border-radius: 2px;
            animation: confetti-fall 3s ease-out forwards;
        }

        @keyframes confetti-fall {
            0% {
                transform: translateY(-100px) rotate(0deg);
                opacity: 1;
            }

            100% {
                transform: translateY(300px) rotate(720deg);
                opacity: 0;
            }
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
            <!-- Dashboard -->
            <a href="<?= BASE_URL ?>" class="nav-item <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-th-large"></i><span data-translate="nav_dashboard">Dasbor</span>
            </a>

            <!-- Contracts Dropdown -->
            <div class="nav-dropdown" data-dropdown="contracts">
                <button class="nav-dropdown-toggle">
                    <i class="fas fa-file-contract"></i>
                    <span data-translate="nav_contracts">Kontrak</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </button>
                <div class="nav-dropdown-menu">
                    <a href="<?= BASE_URL ?>contracts"
                        class="nav-item <?= ($currentPage ?? '') === 'contracts' ? 'active' : '' ?>">
                        <i class="fas fa-list"></i> <span data-translate="nav_all_contracts">Semua Kontrak</span>
                    </a>
                    <a href="<?= BASE_URL ?>contracts/create" class="nav-item">
                        <i class="fas fa-plus"></i> <span data-translate="nav_new_contract">Buat Kontrak</span>
                    </a>
                    <a href="<?= BASE_URL ?>contracts/expiring" class="nav-item" style="color: var(--warning);">
                        <i class="fas fa-clock"></i> <span data-translate="nav_expiring">Kontrak Expire</span>
                    </a>
                </div>
            </div>

            <!-- Vessels Dropdown -->
            <div class="nav-dropdown" data-dropdown="vessels">
                <button class="nav-dropdown-toggle">
                    <i class="fas fa-ship"></i>
                    <span data-translate="nav_vessels">Kapal</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </button>
                <div class="nav-dropdown-menu">
                    <a href="<?= BASE_URL ?>vessels"
                        class="nav-item <?= ($currentPage ?? '') === 'vessels' ? 'active' : '' ?>">
                        <i class="fas fa-list"></i> <span data-translate="vessel_list">Daftar Kapal</span>
                    </a>
                    <a href="<?= BASE_URL ?>vessels/profit" class="nav-item">
                        <i class="fas fa-chart-line"></i> <span data-translate="profit_per_vessel">Profit per
                            Vessel</span>
                    </a>
                </div>
            </div>

            <!-- Clients Dropdown -->
            <div class="nav-dropdown" data-dropdown="clients">
                <button class="nav-dropdown-toggle">
                    <i class="fas fa-building"></i>
                    <span data-translate="nav_clients">Klien</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </button>
                <div class="nav-dropdown-menu">
                    <a href="<?= BASE_URL ?>clients"
                        class="nav-item <?= ($currentPage ?? '') === 'clients' ? 'active' : '' ?>">
                        <i class="fas fa-list"></i> <span data-translate="client_list">Daftar Klien</span>
                    </a>
                    <a href="<?= BASE_URL ?>clients/profit" class="nav-item">
                        <i class="fas fa-chart-pie"></i> <span data-translate="profit_per_client">Profit per
                            Client</span>
                    </a>
                </div>
            </div>

            <a href="<?= BASE_URL ?>ranks" class="nav-item <?= ($currentPage ?? '') === 'ranks' ? 'active' : '' ?>">
                <i class="fas fa-medal"></i><span data-translate="nav_ranks">Master Jabatan</span>
            </a>

            <div class="nav-divider"></div>

            <!-- CREW MANAGEMENT SECTION -->
            <div class="nav-section-header">👨‍✈️ <span data-translate="crew_management">Manajemen Crew</span></div>
            <div class="nav-section-crew">
                <!-- Data Crew -->
                <div class="nav-dropdown" data-dropdown="crew-data">
                    <button class="nav-dropdown-toggle">
                        <i class="fas fa-users"></i>
                        <span data-translate="nav_data_crew">Data Crew</span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </button>
                    <div class="nav-dropdown-menu">
                        <a href="<?= BASE_URL ?>crews"
                            class="nav-item <?= ($currentPage ?? '') === 'crews' ? 'active' : '' ?>">
                            <i class="fas fa-list"></i> <span data-translate="nav_all_crew">Semua Crew</span>
                        </a>
                        <a href="<?= BASE_URL ?>crews/skill-matrix" class="nav-item">
                            <i class="fas fa-th"></i> <span data-translate="nav_skill_matrix">Matriks Skill</span>
                        </a>
                    </div>
                </div>

                <!-- Payroll Crew -->
                <div class="nav-dropdown" data-dropdown="crew-payroll">
                    <button class="nav-dropdown-toggle">
                        <i class="fas fa-money-bill-wave"></i>
                        <span data-translate="nav_payroll_crew">Payroll Crew</span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </button>
                    <div class="nav-dropdown-menu">
                        <a href="<?= BASE_URL ?>payroll"
                            class="nav-item <?= ($currentPage ?? '') === 'payroll' ? 'active' : '' ?>">
                            <i class="fas fa-calculator"></i> <span data-translate="nav_generate_payroll">Generate
                                Payroll</span>
                        </a>
                        <a href="<?= BASE_URL ?>payroll/history" class="nav-item">
                            <i class="fas fa-history"></i> <span data-translate="nav_payroll_history">Histori
                                Payroll</span>
                        </a>
                    </div>
                </div>

                <!-- Dokumen Crew -->
                <a href="<?= BASE_URL ?>documents"
                    class="nav-item <?= ($currentPage ?? '') === 'documents' ? 'active' : '' ?>">
                    <i class="fas fa-folder-open"></i><span data-translate="nav_crew_documents">Dokumen Crew</span>
                </a>

                <!-- Performa Crew -->
                <a href="<?= BASE_URL ?>crews/performance"
                    class="nav-item <?= ($currentPage ?? '') === 'crew-performance' ? 'active' : '' ?>">
                    <i class="fas fa-chart-line"></i><span data-translate="nav_crew_performance">Performa Crew</span>
                    <span
                        style="background: var(--info); color: white; font-size: 9px; padding: 2px 6px; border-radius: 10px; margin-left: auto;">NEW</span>
                </a>
            </div>

            <div class="nav-divider"></div>

            <!-- KARYAWAN MANAGEMENT SECTION (NEW) -->
            <div class="nav-section-header">👔 <span data-translate="employee_management">Manajemen Karyawan</span>
            </div>
            <div class="nav-section-karyawan">
                <a href="<?= BASE_URL ?>employees" class="nav-item">
                    <i class="fas fa-user-tie"></i><span data-translate="nav_employee_data">Data Karyawan</span>
                    <span
                        style="background: var(--success); color: white; font-size: 9px; padding: 2px 6px; border-radius: 10px; margin-left: auto;">HRIS</span>
                </a>
                <a href="<?= BASE_URL ?>employees/attendance" class="nav-item">
                    <i class="fas fa-clock"></i><span data-translate="nav_attendance">Absensi</span>
                </a>
                <a href="<?= BASE_URL ?>employees/payroll" class="nav-item">
                    <i class="fas fa-money-check-alt"></i><span data-translate="nav_employee_payroll">Payroll
                        Karyawan</span>
                </a>
                <a href="<?= BASE_URL ?>employees/performance" class="nav-item">
                    <i class="fas fa-chart-line"></i><span data-translate="nav_employee_performance">Performa</span>
                </a>
            </div>

            <div class="nav-divider"></div>

            <!-- RECRUITMENT SECTION (NEW) -->
            <div class="nav-section-header">🚀 <span data-translate="recruitment">Rekrutmen</span></div>
            <div class="nav-section-recruitment">
                <a href="<?= BASE_URL ?>recruitment/pipeline"
                    class="nav-item <?= (isset($currentPage) && $currentPage === 'recruitment-pipeline') ? 'active' : '' ?>">
                    <i class="fas fa-funnel-dollar"></i><span data-translate="nav_pipeline">Pipeline</span>
                </a>
                <a href="<?= BASE_URL ?>recruitment/approval"
                    class="nav-item <?= (isset($currentPage) && $currentPage === 'recruitment-approval') ? 'active' : '' ?>">
                    <i class="fas fa-check-circle"></i><span data-translate="nav_approval_center">Approval Center</span>
                </a>
                <a href="<?= BASE_URL ?>recruitment/onboarding"
                    class="nav-item <?= (isset($currentPage) && $currentPage === 'recruitment-onboarding') ? 'active' : '' ?>">
                    <i class="fas fa-user-plus"></i><span data-translate="nav_onboarding">Auto-Onboarding</span>
                </a>
            </div>

            <div class="nav-divider"></div>

            <!-- MONITORING SECTION (NEW) -->
            <div class="nav-section-header">📊 <span data-translate="monitoring">Pemantauan</span></div>
            <div class="nav-section-monitoring">
                <a href="<?= BASE_URL ?>monitoring/visitors" class="nav-item">
                    <i class="fas fa-eye"></i><span data-translate="nav_visitor_cp">Visitor CP</span>
                </a>
                <a href="<?= BASE_URL ?>monitoring/activity" class="nav-item">
                    <i class="fas fa-list-alt"></i><span data-translate="nav_activity_log">Log Aktivitas</span>
                </a>
                <a href="<?= BASE_URL ?>monitoring/integration" class="nav-item">
                    <i class="fas fa-plug"></i><span data-translate="nav_integration_status">Integration Status</span>
                </a>
            </div>

            <div class="nav-divider"></div>

            <!-- Reports Dropdown -->
            <div class="nav-dropdown" data-dropdown="reports">
                <button class="nav-dropdown-toggle">
                    <i class="fas fa-chart-bar"></i>
                    <span data-translate="nav_reports">Laporan</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </button>
                <div class="nav-dropdown-menu">
                    <a href="<?= BASE_URL ?>reports"
                        class="nav-item <?= ($currentPage ?? '') === 'reports' ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i> <span data-translate="nav_overview">Ringkasan</span>
                    </a>
                    <a href="<?= BASE_URL ?>reports/by-vessel" class="nav-item">
                        <i class="fas fa-ship"></i> <span data-translate="nav_crew_report">Laporan Crew</span>
                    </a>
                    <a href="<?= BASE_URL ?>reports/employees" class="nav-item">
                        <i class="fas fa-user-tie"></i> <span data-translate="nav_employee_report">Laporan
                            Karyawan</span>
                        <span
                            style="background: var(--success); color: white; font-size: 9px; padding: 2px 6px; border-radius: 10px; margin-left: 4px;">NEW</span>
                    </a>
                    <a href="<?= BASE_URL ?>reports/payroll-summary" class="nav-item">
                        <i class="fas fa-money-bill"></i> <span data-translate="nav_financial_report">Laporan
                            Keuangan</span>
                    </a>

                </div>
            </div>

            <div class="nav-divider"></div>

            <a href="<?= BASE_URL ?>notifications"
                class="nav-item <?= ($currentPage ?? '') === 'notifications' ? 'active' : '' ?>">
                <i class="fas fa-bell"></i><span data-translate="nav_notifications">Notifications</span>
            </a>
            <a href="<?= BASE_URL ?>settings"
                class="nav-item <?= ($currentPage ?? '') === 'settings' ? 'active' : '' ?>">
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
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($currentUser['full_name'] ?? 'User') ?>&background=D4AF37&color=fff"
                            alt="User">
                        <div>
                            <span class="user-name"><?= htmlspecialchars($currentUser['full_name'] ?? 'User') ?></span>
                            <span
                                class="user-role"><?= ucfirst(str_replace('_', ' ', $currentUser['role'] ?? 'Guest')) ?></span>
                        </div>
                    </div>
                    <a href="javascript:void(0)" title="Logout" style="color: var(--danger); padding: 8px;"
                        onclick="showLogoutModal()">
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
            <div class="header-right" style="position: relative; display: flex; align-items: center; gap: 12px;">
                <!-- Language Switcher -->
                <div class="lang-switcher" style="display: flex; gap: 4px;">
                    <button onclick="TranslationService.setLanguage('id')" id="lang-id-btn"
                        style="padding: 6px 10px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-secondary); cursor: pointer; font-size: 12px; font-weight: 500; transition: all 0.2s;"
                        title="Bahasa Indonesia">
                        🇮🇩 ID
                    </button>
                    <button onclick="TranslationService.setLanguage('en')" id="lang-en-btn"
                        style="padding: 6px 10px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-secondary); cursor: pointer; font-size: 12px; font-weight: 500; transition: all 0.2s;"
                        title="English">
                        🇺🇸 EN
                    </button>
                </div>
                <a href="<?= BASE_URL ?>notifications" class="btn-icon notification-btn" style="position: relative;">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge" id="notif-count"
                        style="display: none; position: absolute; top: -5px; right: -5px; background: var(--danger); color: white; font-size: 10px; padding: 2px 6px; border-radius: 10px;">0</span>
                </a>
            </div>
        </header>

        <div class="page-content">
            <?php if (!empty($flash) && $flash['type'] !== 'success'): ?>
                <div class="alert alert-<?= $flash['type'] ?>">
                    <i class="fas fa-<?= $flash['type'] === 'error' ? 'exclamation-circle' : 'exclamation-triangle' ?>"></i>
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </div>

        <!-- Premium Success Modal -->
        <div class="success-modal-overlay" id="successModal" onclick="closeSuccessModal(event)">
            <div class="success-modal" onclick="event.stopPropagation()">
                <div id="confettiContainer"></div>
                <div class="success-icon-wrapper">
                    <div class="success-icon-bg">
                        <i class="fas fa-check success-icon"></i>
                    </div>
                </div>
                <h2 class="success-title">Berhasil!</h2>
                <p class="success-message" id="successMessage"></p>
                <button class="success-btn" onclick="closeSuccessModal()">
                    <i class="fas fa-thumbs-up"></i> Lanjutkan
                </button>
            </div>
        </div>

        <?php if (!empty($flash) && $flash['type'] === 'success'): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    showSuccessModal('<?= addslashes($flash['message']) ?>');
                });
            </script>
        <?php endif; ?>
    </main>

    <!-- Theme and Language Script -->
    <script src="<?= BASE_URL ?>assets/js/app.js"></script>
    <script>
        // Apply theme immediately to prevent flash
        (function () {
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
            document.addEventListener('DOMContentLoaded', function () {
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
                    btn.addEventListener('click', function () {
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
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && document.getElementById('logoutModal').classList.contains('active')) {
                closeLogoutModal();
            }
            if (e.key === 'Escape' && document.getElementById('successModal').classList.contains('active')) {
                closeSuccessModal();
            }
        });

        // Success Modal Functions
        function showSuccessModal(message) {
            document.getElementById('successMessage').textContent = message;
            document.getElementById('successModal').classList.add('active');
            document.body.style.overflow = 'hidden';

            // Create confetti
            createConfetti();
        }

        function closeSuccessModal(event) {
            if (event && event.target !== event.currentTarget) return;
            document.getElementById('successModal').classList.remove('active');
            document.body.style.overflow = '';

            // Clear confetti
            document.getElementById('confettiContainer').innerHTML = '';
        }

        function createConfetti() {
            const container = document.getElementById('confettiContainer');
            const colors = ['#10B981', '#D4AF37', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6'];

            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'success-confetti';
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.animationDelay = Math.random() * 1 + 's';
                confetti.style.animationDuration = (Math.random() * 2 + 2) + 's';
                confetti.style.opacity = Math.random();
                container.appendChild(confetti);
            }
        }

        // ===== DROPDOWN MENU FUNCTIONALITY =====
        document.addEventListener('DOMContentLoaded', function () {
            const dropdowns = document.querySelectorAll('.nav-dropdown');
            const STORAGE_KEY = 'erp_menu_state';

            // Load saved state from localStorage
            function loadMenuState() {
                try {
                    const saved = localStorage.getItem(STORAGE_KEY);
                    return saved ? JSON.parse(saved) : {};
                } catch (e) {
                    return {};
                }
            }

            // Save state to localStorage
            function saveMenuState(state) {
                try {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
                } catch (e) {
                    console.warn('Could not save menu state');
                }
            }

            // Initialize dropdowns
            const menuState = loadMenuState();

            dropdowns.forEach(dropdown => {
                const toggle = dropdown.querySelector('.nav-dropdown-toggle');
                const key = dropdown.dataset.dropdown;

                // Restore saved state
                if (menuState[key]) {
                    dropdown.classList.add('open');
                }

                // Auto-open if contains active item
                const hasActive = dropdown.querySelector('.nav-item.active');
                if (hasActive) {
                    dropdown.classList.add('open');
                    menuState[key] = true;
                }

                // Toggle click handler
                toggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const isOpen = dropdown.classList.toggle('open');
                    menuState[key] = isOpen;
                    saveMenuState(menuState);
                });
            });

            // Save initial state
            saveMenuState(menuState);

            // ===== SIDEBAR SCROLL POSITION PERSISTENCE (NO AUTO-SCROLL) =====
            const sidebarNav = document.querySelector('.sidebar-nav');
            const SCROLL_KEY = 'erp_sidebar_scroll_pos';

            // Restore saved scroll position (NO auto-centering)
            const savedPos = localStorage.getItem(SCROLL_KEY);
            if (savedPos && sidebarNav) {
                sidebarNav.scrollTop = parseInt(savedPos, 10);
            }

            // Save scroll position on every scroll
            if (sidebarNav) {
                sidebarNav.addEventListener('scroll', function () {
                    localStorage.setItem(SCROLL_KEY, sidebarNav.scrollTop);
                });
            }

            // Save scroll position before clicking any menu
            document.querySelectorAll('.sidebar a, .sidebar button').forEach(el => {
                el.addEventListener('click', function () {
                    if (sidebarNav) {
                        localStorage.setItem(SCROLL_KEY, sidebarNav.scrollTop);
                    }
                });
            });
        });
    </script>

    <!-- Translation Service -->
    <script src="<?= BASE_URL ?>assets/js/translate-erp.js"></script>
    <script>
        // Apply translations and highlight active language button
        document.addEventListener('DOMContentLoaded', function () {
            if (window.TranslationService) {
                TranslationService.applyTranslations();

                // Highlight active language button
                function updateLangButtons() {
                    const lang = TranslationService.currentLang;
                    const idBtn = document.getElementById('lang-id-btn');
                    const enBtn = document.getElementById('lang-en-btn');

                    if (idBtn && enBtn) {
                        if (lang === 'id') {
                            idBtn.style.background = 'var(--accent-gold)';
                            idBtn.style.color = 'white';
                            idBtn.style.borderColor = 'var(--accent-gold)';
                            enBtn.style.background = 'var(--card-bg)';
                            enBtn.style.color = 'var(--text-secondary)';
                            enBtn.style.borderColor = 'var(--border-color)';
                        } else {
                            enBtn.style.background = 'var(--accent-gold)';
                            enBtn.style.color = 'white';
                            enBtn.style.borderColor = 'var(--accent-gold)';
                            idBtn.style.background = 'var(--card-bg)';
                            idBtn.style.color = 'var(--text-secondary)';
                            idBtn.style.borderColor = 'var(--border-color)';
                        }
                    }
                }

                updateLangButtons();

                // Listen for language changes
                window.addEventListener('languageChanged', updateLangButtons);
            }
        });
    </script>

</body>

</html>