<?php
/**
 * Modern Sidebar - Stable, no-jump, pure CSS/JS
 * No Alpine.js dependency for sidebar toggles
 * Dropdowns open/close via server-side detection + vanilla JS
 */

$currentPage = $currentPage ?? '';
$currentUser = $_SESSION['user'] ?? null;

// === ACTIVE STATE DETECTION ===
$_sidebarUri = $_SERVER['REQUEST_URI'] ?? '';
$_sidebarPath = (string)(parse_url($_sidebarUri, PHP_URL_PATH) ?? '');

$_isActive = function($path) use ($_sidebarPath) {
    return str_contains($_sidebarPath, $path);
};

$act = [
    'dashboard'       => $currentPage === 'dashboard' || $_sidebarPath === '/PT_indoocean/PT_indoocean/erp/' || $_sidebarPath === '/PT_indoocean/PT_indoocean/erp',
    'contracts'       => $_isActive('/contracts'),
    'contracts-list'  => str_ends_with($_sidebarPath, '/contracts') || str_ends_with($_sidebarPath, '/contracts/'),
    'contracts-create'=> $_isActive('/contracts/create'),
    'contracts-expiring' => $_isActive('/contracts/expiring'),
    'crew-monitoring' => $_isActive('/contracts/timeline'),
    'vessels'         => $_isActive('/vessels') && !$_isActive('/finance') && !$_isActive('/vessels/profit'),
    'vessels-list'    => str_ends_with($_sidebarPath, '/vessels') || str_ends_with($_sidebarPath, '/vessels/'),
    'clients'         => $_isActive('/clients'),
    'ranks'           => $_isActive('/ranks'),
    'crews'           => $_isActive('/crews') && !$_isActive('/crews/performance'),
    'crews-list'      => (str_ends_with($_sidebarPath, '/crews') || str_ends_with($_sidebarPath, '/crews/')) && !$_isActive('/crews/performance'),
    'skill-matrix'    => $_isActive('/skill-matrix') || $_isActive('/skill_matrix'),
    'crew-payroll'    => $_isActive('/payroll') && !$_isActive('/employees/payroll'),
    'crew-payroll-mgmt' => (str_ends_with($_sidebarPath, '/payroll') || str_ends_with($_sidebarPath, '/payroll/')) && !$_isActive('/employees/payroll'),
    'crew-payroll-history' => $_isActive('/payroll/history') && !$_isActive('/employees'),
    'documents'       => $_isActive('/documents'),
    'doc-parser'      => $_isActive('/DocumentParser'),
    'smart-import'    => $_isActive('/SmartImport') || $_isActive('/smart-import'),
    'crew-performance'=> $_isActive('/crews/performance'),
    'employees'       => $_isActive('/employees') && !$_isActive('/employees/attendance') && !$_isActive('/employees/payroll') && !$_isActive('/employees/performance'),
    'attendance'      => $_isActive('/employees/attendance'),
    'emp-payroll'     => $_isActive('/employees/payroll'),
    'emp-performance' => $_isActive('/employees/performance'),
    'pipeline'        => $_isActive('/recruitment/pipeline'),
    'approval'        => $_isActive('/recruitment/approval'),
    'onboarding'      => $_isActive('/recruitment/onboarding'),
    'admin-checklist' => $_isActive('/AdminChecklist') || $_isActive('/admin-checklist'),
    'operational'     => $_isActive('/Operational') || $_isActive('/operational'),
    'recruiter-perf'  => $_isActive('/RecruiterPerformance') || $_isActive('/recruiter-performance'),
    'visitors'        => $_isActive('/monitoring/visitors'),
    'activity'        => $_isActive('/monitoring/activity'),
    'integration'     => $_isActive('/monitoring/integration'),
    'finance'          => $_isActive('/finance') || $_isActive('/vessels/profit') || $_isActive('/reports/payroll-summary'),
    'finance-dash'     => str_ends_with($_sidebarPath, '/finance') || str_ends_with($_sidebarPath, '/finance/'),
    'finance-invoices' => $_isActive('/finance/invoices') || $_isActive('/finance/invoice') || $_isActive('/finance/create-invoice') || $_isActive('/finance/edit-invoice'),
    'finance-bills'    => $_isActive('/finance/bills') || $_isActive('/finance/bill') || $_isActive('/finance/create-bill') || $_isActive('/finance/edit-bill'),
    'finance-journal'  => $_isActive('/finance/journal') || $_isActive('/finance/create-journal') || $_isActive('/finance/journal-detail'),
    'finance-cc'       => $_isActive('/finance/cost-centers'),
    'finance-accounts' => $_isActive('/finance/accounts'),
    'finance-cashflow' => $_isActive('/finance/cashflow'),
    'finance-pnl'      => $_isActive('/finance/profit-loss'),
    'finance-vessel'   => $_isActive('/vessels/profit'),
    'finance-payroll'  => $_isActive('/reports/payroll-summary'),
    'reports'         => $_isActive('/reports') && !$_isActive('/reports/payroll-summary'),
    'reports-overview'=> str_ends_with($_sidebarPath, '/reports') || str_ends_with($_sidebarPath, '/reports/'),
    'reports-vessel'  => $_isActive('/reports/by-vessel'),
    'reports-emp'     => $_isActive('/reports/employees'),
    'notifications'   => $_isActive('/notifications'),
    'settings'        => $_isActive('/settings'),
    'users'           => $_isActive('/users'),
];
?>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<style>
    /* === SIDEBAR CORE STYLES === */
    #erpSidebar { scrollbar-width: none; -ms-overflow-style: none; }
    #erpSidebar::-webkit-scrollbar { width: 0; display: none; }

    /* Menu item text truncation */
    .sb-text { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block; }

    /* Dropdown submenu - no animation, instant show/hide */
    .sb-sub { display: none; margin-left: 36px; margin-top: 2px; }
    .sb-sub.sb-open { display: block; }

    /* Dropdown arrow rotation */
    .sb-arrow { flex-shrink: 0; font-size: 16px; transition: transform 0.15s ease; }
    .sb-arrow.sb-rotated { transform: rotate(180deg); }

    /* Icon fixed width */
    .sb-icon { flex-shrink: 0; width: 20px; text-align: center; }

    /* Badge fixed */
    .sb-badge { flex-shrink: 0; white-space: nowrap; }

    /* Menu item base */
    .sb-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 7px 12px;
        border-radius: 8px;
        font-size: 13px;
        line-height: 1.4;
        text-decoration: none;
        color: #475569;
        border-left: 3px solid transparent;
        cursor: pointer;
        width: 100%;
        min-width: 0;
    }
    .sb-item:hover { background: #f8fafc; color: #1e293b; }

    /* Active state */
    .sb-item.sb-active {
        background: #eff6ff;
        color: #1d4ed8;
        font-weight: 600;
        border-left-color: #2563eb;
    }
    .sb-item.sb-active .sb-icon { color: #2563eb; }

    /* Sub-item */
    .sb-sub a {
        display: block;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 12px;
        color: #64748b;
        text-decoration: none;
        line-height: 1.5;
    }
    .sb-sub a:hover { color: #334155; background: #f8fafc; }
    .sb-sub a.sb-sub-active {
        color: #1d4ed8;
        font-weight: 600;
        background: rgba(239, 246, 255, 0.8);
    }

    /* Section header */
    .sb-section {
        padding: 14px 12px 6px;
        font-size: 10px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }
    .sb-section:first-child { padding-top: 4px; }

    /* Dropdown button - reset */
    .sb-dropdown-btn {
        background: none;
        border: none;
        font-family: inherit;
        font-size: inherit;
        text-align: left;
        outline: none;
    }
    .sb-dropdown-btn .sb-text { flex: 1; min-width: 0; }
</style>

<aside style="width:256px;height:100vh;background:#fff;border-right:1px solid rgba(226,232,240,0.6);flex-shrink:0;position:fixed;left:0;top:0;display:flex;flex-direction:column;z-index:40;">
    <!-- Logo -->
    <div style="padding:14px 16px;border-bottom:1px solid #f1f5f9;flex-shrink:0;">
        <a href="<?= BASE_URL ?>" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
            <div style="width:38px;height:38px;border-radius:10px;overflow:hidden;flex-shrink:0;box-shadow:0 2px 8px rgba(37,99,235,0.15);">
                <img src="<?= BASE_URL ?>assets/images/logo.png" alt="IndoOcean" style="width:100%;height:100%;object-fit:cover;">
            </div>
            <div>
                <div style="font-size:15px;font-weight:700;color:#1e293b;line-height:1.2;">IndoOcean</div>
                <div style="font-size:10px;color:#2563eb;font-weight:700;letter-spacing:0.5px;">MARITIME ERP</div>
            </div>
        </a>
    </div>

    <!-- Navigation -->
    <nav style="flex:1;overflow-y:auto;padding:8px 10px;" id="erpSidebar">

        <!-- OVERVIEW -->
        <div class="sb-section"><?= __('sidebar.overview') ?></div>

        <a href="<?= BASE_URL ?>" class="sb-item <?= $act['dashboard'] ? 'sb-active' : '' ?>">
            <span class="material-icons sb-icon" style="font-size:18px;"><?= $act['dashboard'] ? 'dashboard' : 'dashboard' ?></span>
            <span class="sb-text"><?= __('sidebar.dashboard') ?></span>
        </a>

        <!-- MANAJEMEN -->
        <div class="sb-section"><?= __('sidebar.management') ?></div>

        <!-- Kontrak -->
        <button class="sb-item sb-dropdown-btn <?= $act['contracts'] ? 'sb-active' : '' ?>" onclick="sbToggle('sub-contracts')">
            <span class="material-icons sb-icon" style="font-size:18px;">description</span>
            <span class="sb-text"><?= __('sidebar.contracts') ?></span>
            <span class="material-icons sb-arrow <?= $act['contracts'] ? 'sb-rotated' : '' ?>" id="arrow-sub-contracts">expand_more</span>
        </button>
        <div class="sb-sub <?= $act['contracts'] ? 'sb-open' : '' ?>" id="sub-contracts">
            <a href="<?= BASE_URL ?>contracts" class="<?= $act['contracts-list'] ? 'sb-sub-active' : '' ?>"><?= __('sidebar.all_contracts') ?></a>
            <a href="<?= BASE_URL ?>contracts/create" class="<?= $act['contracts-create'] ? 'sb-sub-active' : '' ?>"><?= __('sidebar.create_contract') ?></a>
            <a href="<?= BASE_URL ?>contracts/expiring" class="<?= $act['contracts-expiring'] ? 'sb-sub-active' : '' ?>"><?= __('sidebar.expiring_soon') ?></a>
            <a href="<?= BASE_URL ?>contracts/timeline" class="<?= $act['crew-monitoring'] ? 'sb-sub-active' : '' ?>">📊 Crew Monitoring</a>
        </div>

        <!-- Klien -->
        <button class="sb-item sb-dropdown-btn <?= $act['clients'] ? 'sb-active' : '' ?>" onclick="sbToggle('sub-clients')">
            <span class="material-icons sb-icon" style="font-size:18px;">business</span>
            <span class="sb-text"><?= __('sidebar.clients') ?></span>
            <span class="material-icons sb-arrow <?= $act['clients'] ? 'sb-rotated' : '' ?>" id="arrow-sub-clients">expand_more</span>
        </button>
        <div class="sb-sub <?= $act['clients'] ? 'sb-open' : '' ?>" id="sub-clients">
            <a href="<?= BASE_URL ?>clients" class="<?= $act['clients'] ? 'sb-sub-active' : '' ?>"><?= __('sidebar.client_management') ?></a>
        </div>

        <!-- Master Jabatan -->
        <a href="<?= BASE_URL ?>ranks" class="sb-item <?= $act['ranks'] ? 'sb-active' : '' ?>">
            <span class="material-icons sb-icon" style="font-size:18px;">military_tech</span>
            <span class="sb-text"><?= __('sidebar.master_ranks') ?></span>
        </a>

        <!-- KREW -->
        <div class="sb-section"><?= __('sidebar.crew') ?></div>

        <!-- Data Krew -->
        <button class="sb-item sb-dropdown-btn <?= ($act['crews'] || $act['documents']) ? 'sb-active' : '' ?>" onclick="sbToggle('sub-crews')">
            <span class="material-icons sb-icon" style="font-size:18px;">badge</span>
            <span class="sb-text"><?= __('sidebar.data_crew') ?></span>
            <span class="material-icons sb-arrow <?= ($act['crews'] || $act['documents']) ? 'sb-rotated' : '' ?>" id="arrow-sub-crews">expand_more</span>
        </button>
        <div class="sb-sub <?= ($act['crews'] || $act['documents']) ? 'sb-open' : '' ?>" id="sub-crews">
            <a href="<?= BASE_URL ?>crews" class="<?= $act['crews-list'] ? 'sb-sub-active' : '' ?>"><?= __('sidebar.all_crew') ?></a>
            <a href="<?= BASE_URL ?>crews/skill-matrix" class="<?= $act['skill-matrix'] ? 'sb-sub-active' : '' ?>"><?= __('sidebar.skill_matrix') ?></a>
            <a href="<?= BASE_URL ?>documents" class="<?= $act['documents'] ? 'sb-sub-active' : '' ?>"><?= __('sidebar.documents') ?></a>
        </div>

        <!-- Payroll Krew -->
        <button class="sb-item sb-dropdown-btn <?= $act['crew-payroll'] ? 'sb-active' : '' ?>" onclick="sbToggle('sub-crewpay')">
            <span class="material-icons sb-icon" style="font-size:18px;">account_balance_wallet</span>
            <span class="sb-text"><?= __('sidebar.crew_payroll') ?></span>
            <span class="material-icons sb-arrow <?= $act['crew-payroll'] ? 'sb-rotated' : '' ?>" id="arrow-sub-crewpay">expand_more</span>
        </button>
        <div class="sb-sub <?= $act['crew-payroll'] ? 'sb-open' : '' ?>" id="sub-crewpay">
            <a href="<?= BASE_URL ?>payroll" class="<?= $act['crew-payroll-mgmt'] ? 'sb-sub-active' : '' ?>"><?= __('sidebar.management_label') ?></a>
            <a href="<?= BASE_URL ?>payroll/history" class="<?= $act['crew-payroll-history'] ? 'sb-sub-active' : '' ?>"><?= __('sidebar.history') ?></a>
        </div>

        <!-- Scan Sertifikat AI -->
        <a href="<?= BASE_URL ?>DocumentParser" class="sb-item <?= $act['doc-parser'] ? 'sb-active' : '' ?>">
            <span class="material-icons sb-icon" style="font-size:18px;">smart_toy</span>
            <span class="sb-text"><?= __('sidebar.ai_scan_cert') ?></span>
            <span class="sb-badge" style="padding:2px 6px;font-size:9px;font-weight:700;background:#ede9fe;color:#7c3aed;border-radius:10px;">AI</span>
        </a>

        <!-- Smart Import -->
        <a href="<?= BASE_URL ?>SmartImport" class="sb-item <?= $act['smart-import'] ? 'sb-active' : '' ?>">
            <span class="material-icons sb-icon" style="font-size:18px;">upload_file</span>
            <span class="sb-text">Smart Import</span>
            <span class="sb-badge" style="padding:2px 6px;font-size:9px;font-weight:700;background:linear-gradient(135deg,#dbeafe,#e0e7ff);color:#4338ca;border-radius:10px;">SMART</span>
        </a>

        <!-- Performa Krew -->
        <a href="<?= BASE_URL ?>crews/performance" class="sb-item <?= $act['crew-performance'] ? 'sb-active' : '' ?>">
            <span class="material-icons sb-icon" style="font-size:18px;">insights</span>
            <span class="sb-text"><?= __('sidebar.performance') ?></span>
            <?php if (!$act['crew-performance']): ?>
                <span class="sb-badge" style="padding:2px 6px;font-size:9px;font-weight:700;background:#dbeafe;color:#2563eb;border-radius:10px;"><?= __('common.new') ?></span>
            <?php endif; ?>
        </a>

        <!-- REKRUTMEN -->
        <div class="sb-section"><?= __('sidebar.recruitment') ?></div>

        <a href="<?= BASE_URL ?>recruitment/pipeline" class="sb-item <?= $act['pipeline'] ? 'sb-active' : '' ?>">
            <span class="material-icons sb-icon" style="font-size:18px;">filter_alt</span>
            <span class="sb-text"><?= __('sidebar.pipeline') ?></span>
        </a>

        <a href="<?= BASE_URL ?>AdminChecklist" class="sb-item <?= $act['admin-checklist'] ? 'sb-active' : '' ?>">
            <span class="material-icons sb-icon" style="font-size:18px;">checklist</span>
            <span class="sb-text">Admin Checklist</span>
            <span class="sb-badge" style="padding:2px 6px;font-size:9px;font-weight:700;background:#dbeafe;color:#2563eb;border-radius:10px;">Stage 2</span>
        </a>

        <a href="<?= BASE_URL ?>Operational" class="sb-item <?= $act['operational'] ? 'sb-active' : '' ?>">
            <span class="material-icons sb-icon" style="font-size:18px;">flight_takeoff</span>
            <span class="sb-text">Operational</span>
            <span class="sb-badge" style="padding:2px 6px;font-size:9px;font-weight:700;background:#dcfce7;color:#16a34a;border-radius:10px;">Stage 3</span>
        </a>

        <a href="<?= BASE_URL ?>RecruiterPerformance" class="sb-item <?= $act['recruiter-perf'] ? 'sb-active' : '' ?>">
            <span class="material-icons sb-icon" style="font-size:18px;">emoji_events</span>
            <span class="sb-text">Kinerja Perekrut</span>
            <span class="sb-badge" style="padding:2px 6px;font-size:9px;font-weight:700;background:linear-gradient(135deg,#fef3c7,#fde68a);color:#92400e;border-radius:10px;">PTS</span>
        </a>

        <!-- KARYAWAN -->
        <div class="sb-section"><?= __('sidebar.employee') ?></div>

        <a href="<?= BASE_URL ?>employees" class="sb-item <?= $act['employees'] ? 'sb-active' : '' ?>">
            <span class="material-icons sb-icon" style="font-size:18px;">person</span>
            <span class="sb-text"><?= __('sidebar.employee_data') ?></span>
            <span class="sb-badge" style="padding:2px 6px;font-size:9px;font-weight:700;background:#dcfce7;color:#16a34a;border-radius:10px;">HRIS</span>
        </a>

        <a href="<?= BASE_URL ?>employees/attendance" class="sb-item <?= $act['attendance'] ? 'sb-active' : '' ?>">
            <span class="material-icons sb-icon" style="font-size:18px;">schedule</span>
            <span class="sb-text"><?= __('sidebar.attendance') ?></span>
        </a>

        <a href="<?= BASE_URL ?>employees/payroll" class="sb-item <?= $act['emp-payroll'] ? 'sb-active' : '' ?>">
            <span class="material-icons sb-icon" style="font-size:18px;">receipt_long</span>
            <span class="sb-text"><?= __('sidebar.payroll') ?></span>
        </a>

        <a href="<?= BASE_URL ?>employees/performance" class="sb-item <?= $act['emp-performance'] ? 'sb-active' : '' ?>">
            <span class="material-icons sb-icon" style="font-size:18px;">trending_up</span>
            <span class="sb-text"><?= __('sidebar.performance') ?></span>
        </a>


        <!-- MONITORING -->
        <div class="sb-section"><?= __('sidebar.monitoring') ?></div>

        <a href="<?= BASE_URL ?>monitoring/visitors" class="sb-item <?= $act['visitors'] ? 'sb-active' : '' ?>">
            <span class="material-icons sb-icon" style="font-size:18px;">visibility</span>
            <span class="sb-text"><?= __('sidebar.visitor_cp') ?></span>
        </a>

        <a href="<?= BASE_URL ?>monitoring/activity" class="sb-item <?= $act['activity'] ? 'sb-active' : '' ?>">
            <span class="material-icons sb-icon" style="font-size:18px;">list_alt</span>
            <span class="sb-text"><?= __('sidebar.activity_log') ?></span>
        </a>

        <a href="<?= BASE_URL ?>monitoring/integration" class="sb-item <?= $act['integration'] ? 'sb-active' : '' ?>">
            <span class="material-icons sb-icon" style="font-size:18px;">extension</span>
            <span class="sb-text"><?= __('sidebar.integration') ?></span>
        </a>

        <!-- KEUANGAN -->
        <div class="sb-section">💰 Keuangan</div>

        <button class="sb-item sb-dropdown-btn <?= $act['finance'] ? 'sb-active' : '' ?>" onclick="sbToggle('sub-finance')">
            <span class="material-icons sb-icon" style="font-size:18px;">account_balance</span>
            <span class="sb-text">Keuangan &amp; Akuntansi</span>
            <span class="material-icons sb-arrow <?= $act['finance'] ? 'sb-rotated' : '' ?>" id="arrow-sub-finance">expand_more</span>
        </button>
        <div class="sb-sub <?= $act['finance'] ? 'sb-open' : '' ?>" id="sub-finance">
            <a href="<?= BASE_URL ?>finance" class="<?= $act['finance-dash'] ? 'sb-sub-active' : '' ?>">Dasbor Keuangan</a>
            <a href="<?= BASE_URL ?>finance/invoices" class="<?= $act['finance-invoices'] ? 'sb-sub-active' : '' ?>">Invoice (AR)</a>
            <a href="<?= BASE_URL ?>finance/bills" class="<?= $act['finance-bills'] ? 'sb-sub-active' : '' ?>">Bills (AP)</a>
            <a href="<?= BASE_URL ?>finance/journal" class="<?= $act['finance-journal'] ? 'sb-sub-active' : '' ?>">General Ledger</a>
            <a href="<?= BASE_URL ?>finance/accounts" class="<?= $act['finance-accounts'] ? 'sb-sub-active' : '' ?>">Bagan Akun (COA)</a>
            <a href="<?= BASE_URL ?>finance/cost-centers" class="<?= $act['finance-cc'] ? 'sb-sub-active' : '' ?>">Cost Center</a>
            <a href="<?= BASE_URL ?>vessels/profit" class="<?= $act['finance-vessel'] ? 'sb-sub-active' : '' ?>">Profit per Vessel</a>
            <a href="<?= BASE_URL ?>finance/cashflow" class="<?= $act['finance-cashflow'] ? 'sb-sub-active' : '' ?>">Arus Kas</a>
            <a href="<?= BASE_URL ?>finance/profit-loss" class="<?= $act['finance-pnl'] ? 'sb-sub-active' : '' ?>">Laba / Rugi (P&amp;L)</a>
            <a href="<?= BASE_URL ?>reports/payroll-summary" class="<?= $act['finance-payroll'] ? 'sb-sub-active' : '' ?>">Laporan Payroll</a>
        </div>

        <!-- LAPORAN -->
        <div class="sb-section"><?= __('sidebar.reports') ?></div>

        <button class="sb-item sb-dropdown-btn <?= $act['reports'] ? 'sb-active' : '' ?>" onclick="sbToggle('sub-reports')">
            <span class="material-icons sb-icon" style="font-size:18px;">assessment</span>
            <span class="sb-text"><?= __('sidebar.reports') ?></span>
            <span class="material-icons sb-arrow <?= $act['reports'] ? 'sb-rotated' : '' ?>" id="arrow-sub-reports">expand_more</span>
        </button>
        <div class="sb-sub <?= $act['reports'] ? 'sb-open' : '' ?>" id="sub-reports">
            <a href="<?= BASE_URL ?>reports" class="<?= $act['reports-overview'] ? 'sb-sub-active' : '' ?>"><?= __('sidebar.report_overview') ?></a>
            <a href="<?= BASE_URL ?>reports/by-vessel" class="<?= $act['reports-vessel'] ? 'sb-sub-active' : '' ?>"><?= __('sidebar.crew_report') ?></a>
            <a href="<?= BASE_URL ?>reports/employees" class="<?= $act['reports-emp'] ? 'sb-sub-active' : '' ?>">
                <?= __('sidebar.employee_report') ?>
                <span style="padding:1px 4px;font-size:8px;font-weight:700;background:#dcfce7;color:#16a34a;border-radius:4px;margin-left:4px;"><?= __('common.new') ?></span>
            </a>
        </div>

        <!-- PENGATURAN -->
        <div class="sb-section"><?= __('sidebar.settings_section') ?></div>

        <a href="<?= BASE_URL ?>notifications" class="sb-item <?= $act['notifications'] ? 'sb-active' : '' ?>">
            <span class="material-icons sb-icon" style="font-size:18px;">notifications</span>
            <span class="sb-text"><?= __('sidebar.notifications') ?></span>
        </a>

        <a href="<?= BASE_URL ?>settings" class="sb-item <?= $act['settings'] ? 'sb-active' : '' ?>">
            <span class="material-icons sb-icon" style="font-size:18px;">settings</span>
            <span class="sb-text"><?= __('sidebar.settings') ?></span>
        </a>

        <a href="<?= BASE_URL ?>users" class="sb-item <?= $act['users'] ? 'sb-active' : '' ?>">
            <span class="material-icons sb-icon" style="font-size:18px;">manage_accounts</span>
            <span class="sb-text"><?= __('sidebar.users') ?></span>
        </a>

        <div style="height:16px;"></div>
    </nav>

    <!-- User Footer -->
    <div style="padding:10px 12px;border-top:1px solid #f1f5f9;background:#fff;flex-shrink:0;">
        <?php if ($currentUser): ?>
            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                <div style="display:flex;align-items:center;gap:8px;min-width:0;flex:1;">
                    <div style="width:32px;height:32px;border-radius:50%;background:#2563eb;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:12px;flex-shrink:0;">
                        <?= strtoupper(substr($currentUser['full_name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div style="min-width:0;flex:1;">
                        <div style="font-size:12px;font-weight:600;color:#1e293b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($currentUser['full_name'] ?? 'User') ?></div>
                        <div style="font-size:10px;color:#94a3b8;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($currentUser['email'] ?? '') ?></div>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:2px;flex-shrink:0;">
                    <button onclick="toggleLanguage()" style="padding:4px 6px;background:none;border:none;cursor:pointer;border-radius:6px;color:#94a3b8;font-size:10px;font-weight:700;" title="<?= __('settings.language') ?>">
                        <?= strtoupper(getLanguage()) ?>
                    </button>
                    <a href="<?= BASE_URL ?>auth/logout" style="padding:4px;color:#94a3b8;border-radius:6px;display:flex;align-items:center;" title="<?= __('sidebar.logout') ?>">
                        <span class="material-icons" style="font-size:18px;">logout</span>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <a href="<?= BASE_URL ?>auth/login" style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:8px;background:#2563eb;color:#fff;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;">
                <span class="material-icons" style="font-size:16px;">login</span> <?= __('sidebar.login') ?>
            </a>
        <?php endif; ?>
    </div>
</aside>

<script>
// Sidebar dropdown toggle - pure vanilla JS, no animations
function sbToggle(id) {
    var sub = document.getElementById(id);
    var arrow = document.getElementById('arrow-' + id);
    if (!sub) return;
    var isOpen = sub.classList.contains('sb-open');
    if (isOpen) {
        sub.classList.remove('sb-open');
        if (arrow) arrow.classList.remove('sb-rotated');
    } else {
        sub.classList.add('sb-open');
        if (arrow) arrow.classList.add('sb-rotated');
    }
}

// Language toggle
function toggleLanguage() {
    var current = '<?= getLanguage() ?>';
    var newLang = current === 'en' ? 'id' : 'en';

    fetch('<?= BASE_URL ?>settings/change-language', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: 'language=' + newLang
    })
    .then(function(r) { return r.json(); })
    .then(function(data) { if (data.success) location.reload(); })
    .catch(function() { location.href = '<?= BASE_URL ?>settings/change-language?language=' + newLang; });
}

// Save & restore sidebar scroll position
(function() {
    var KEY = 'sidebar_scroll_pos';
    var nav = document.getElementById('erpSidebar');
    if (!nav) return;
    var saved = sessionStorage.getItem(KEY);
    if (saved !== null) nav.scrollTop = parseInt(saved, 10);
    var t;
    nav.addEventListener('scroll', function() {
        clearTimeout(t);
        t = setTimeout(function() { sessionStorage.setItem(KEY, nav.scrollTop); }, 50);
    });
    window.addEventListener('beforeunload', function() { sessionStorage.setItem(KEY, nav.scrollTop); });
})();
</script>
