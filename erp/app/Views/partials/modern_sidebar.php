<?php
/**
 * Modern Sidebar - Multi-language support
 * No bouncing, no jumping, consistent active indicators
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
    'vessels'         => $_isActive('/vessels'),
    'vessels-list'    => str_ends_with($_sidebarPath, '/vessels') || str_ends_with($_sidebarPath, '/vessels/'),
    'vessels-profit'  => $_isActive('/vessels/profit'),
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
    'crew-performance'=> $_isActive('/crews/performance'),
    'employees'       => $_isActive('/employees') && !$_isActive('/employees/attendance') && !$_isActive('/employees/payroll') && !$_isActive('/employees/performance'),
    'attendance'      => $_isActive('/employees/attendance'),
    'emp-payroll'     => $_isActive('/employees/payroll'),
    'emp-performance' => $_isActive('/employees/performance'),
    'pipeline'        => $_isActive('/recruitment/pipeline'),
    'approval'        => $_isActive('/recruitment/approval'),
    'onboarding'      => $_isActive('/recruitment/onboarding'),
    'visitors'        => $_isActive('/monitoring/visitors'),
    'activity'        => $_isActive('/monitoring/activity'),
    'integration'     => $_isActive('/monitoring/integration'),
    'reports'         => $_isActive('/reports'),
    'reports-overview'=> str_ends_with($_sidebarPath, '/reports') || str_ends_with($_sidebarPath, '/reports/'),
    'reports-vessel'  => $_isActive('/reports/by-vessel'),
    'reports-emp'     => $_isActive('/reports/employees'),
    'reports-finance' => $_isActive('/reports/payroll-summary'),
    'notifications'   => $_isActive('/notifications'),
    'settings'        => $_isActive('/settings'),
    'users'           => $_isActive('/users'),
];

$activeClass = 'bg-blue-50 text-blue-700 font-semibold border-l-[3px] border-blue-600';
$inactiveClass = 'text-slate-600 hover:bg-slate-50 hover:text-slate-800 border-l-[3px] border-transparent';
$activeIcon = 'text-blue-600';
$inactiveIcon = 'text-slate-400';
$activeSub = 'text-blue-700 font-semibold bg-blue-50/80';
$inactiveSub = 'text-slate-500 hover:text-slate-700 hover:bg-slate-50';
?>
<style>
    [x-cloak] { display: none !important; }
    #sidebarNav { scrollbar-width: none; -ms-overflow-style: none; }
    #sidebarNav::-webkit-scrollbar { width: 0; display: none; }
</style>
<aside class="w-64 h-screen bg-white border-r border-slate-200/60 flex-shrink-0 fixed left-0 top-0 flex flex-col z-40">
    <!-- Logo -->
    <div class="px-4 py-4 border-b border-slate-100 flex-shrink-0">
        <a href="<?= BASE_URL ?>" class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center shadow-md">
                <span class="material-icons text-white text-xl">anchor</span>
            </div>
            <div>
                <h1 class="text-base font-bold text-slate-800 leading-tight">IndoOcean</h1>
                <span class="text-[10px] text-blue-600 font-bold tracking-wide">MARITIME ERP</span>
            </div>
        </a>
    </div>

    <!-- Navigation (scrollable) -->
    <nav class="flex-1 overflow-y-scroll py-3 px-3 space-y-0.5 text-[13px]" id="sidebarNav">

        <!-- OVERVIEW -->
        <div class="px-3 pt-1 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider"><?= __('sidebar.overview') ?></div>

        <a href="<?= BASE_URL ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $act['dashboard'] ? $activeClass : $inactiveClass ?>">
            <span class="material-icons text-[18px] <?= $act['dashboard'] ? $activeIcon : $inactiveIcon ?>">dashboard</span>
            <?= __('sidebar.dashboard') ?>
        </a>

        <!-- MANAGEMENT -->
        <div class="px-3 pt-4 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider"><?= __('sidebar.management') ?></div>

        <!-- Contracts -->
        <div x-data="{ open: <?= $act['contracts'] ? 'true' : 'false' ?> }" x-cloak>
            <button @click.prevent="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg <?= $act['contracts'] ? $activeClass : $inactiveClass ?>">
                <span class="flex items-center gap-3">
                    <span class="material-icons text-[18px] <?= $act['contracts'] ? $activeIcon : $inactiveIcon ?>">description</span>
                    <?= __('sidebar.contracts') ?>
                </span>
                <span class="material-icons text-[16px] transition-transform duration-200" :class="open && 'rotate-180'">expand_more</span>
            </button>
            <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="ml-9 mt-1 space-y-0.5">
                <a href="<?= BASE_URL ?>contracts" class="block px-3 py-1.5 rounded-md text-xs <?= $act['contracts-list'] ? $activeSub : $inactiveSub ?>"><?= __('sidebar.all_contracts') ?></a>
                <a href="<?= BASE_URL ?>contracts/create" class="block px-3 py-1.5 rounded-md text-xs <?= $act['contracts-create'] ? $activeSub : $inactiveSub ?>"><?= __('sidebar.create_contract') ?></a>
                <a href="<?= BASE_URL ?>contracts/expiring" class="block px-3 py-1.5 rounded-md text-xs <?= $act['contracts-expiring'] ? $activeSub : $inactiveSub ?>"><?= __('sidebar.expiring_soon') ?></a>
            </div>
        </div>

        <!-- Vessels -->
        <div x-data="{ open: <?= $act['vessels'] ? 'true' : 'false' ?> }" x-cloak>
            <button @click.prevent="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg <?= $act['vessels'] ? $activeClass : $inactiveClass ?>">
                <span class="flex items-center gap-3">
                    <span class="material-icons text-[18px] <?= $act['vessels'] ? $activeIcon : $inactiveIcon ?>">directions_boat</span>
                    <?= __('sidebar.vessels') ?>
                </span>
                <span class="material-icons text-[16px] transition-transform duration-200" :class="open && 'rotate-180'">expand_more</span>
            </button>
            <div x-show="open" x-collapse class="ml-9 mt-1 space-y-0.5">
                <a href="<?= BASE_URL ?>vessels" class="block px-3 py-1.5 rounded-md text-xs <?= $act['vessels-list'] ? $activeSub : $inactiveSub ?>"><?= __('sidebar.vessel_list') ?></a>
                <a href="<?= BASE_URL ?>vessels/profit" class="block px-3 py-1.5 rounded-md text-xs <?= $act['vessels-profit'] ? $activeSub : $inactiveSub ?>"><?= __('sidebar.profit_per_vessel') ?></a>
            </div>
        </div>

        <!-- Clients -->
        <div x-data="{ open: <?= $act['clients'] ? 'true' : 'false' ?> }" x-cloak>
            <button @click.prevent="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg <?= $act['clients'] ? $activeClass : $inactiveClass ?>">
                <span class="flex items-center gap-3">
                    <span class="material-icons text-[18px] <?= $act['clients'] ? $activeIcon : $inactiveIcon ?>">business</span>
                    <?= __('sidebar.clients') ?>
                </span>
                <span class="material-icons text-[16px] transition-transform duration-200" :class="open && 'rotate-180'">expand_more</span>
            </button>
            <div x-show="open" x-collapse class="ml-9 mt-1 space-y-0.5">
                <a href="<?= BASE_URL ?>clients" class="block px-3 py-1.5 rounded-md text-xs <?= $act['clients'] ? $activeSub : $inactiveSub ?>"><?= __('sidebar.client_management') ?></a>
            </div>
        </div>

        <!-- Master Ranks -->
        <a href="<?= BASE_URL ?>ranks" class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $act['ranks'] ? $activeClass : $inactiveClass ?>">
            <span class="material-icons text-[18px] <?= $act['ranks'] ? $activeIcon : $inactiveIcon ?>">military_tech</span>
            <?= __('sidebar.master_ranks') ?>
        </a>

        <!-- CREW -->
        <div class="px-3 pt-4 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider"><?= __('sidebar.crew') ?></div>

        <!-- Data Crew -->
        <div x-data="{ open: <?= $act['crews'] ? 'true' : 'false' ?> }" x-cloak>
            <button @click.prevent="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg <?= $act['crews'] ? $activeClass : $inactiveClass ?>">
                <span class="flex items-center gap-3">
                    <span class="material-icons text-[18px] <?= $act['crews'] ? $activeIcon : $inactiveIcon ?>">badge</span>
                    <?= __('sidebar.data_crew') ?>
                </span>
                <span class="material-icons text-[16px] transition-transform duration-200" :class="open && 'rotate-180'">expand_more</span>
            </button>
            <div x-show="open" x-collapse class="ml-9 mt-1 space-y-0.5">
                <a href="<?= BASE_URL ?>crews" class="block px-3 py-1.5 rounded-md text-xs <?= $act['crews-list'] ? $activeSub : $inactiveSub ?>"><?= __('sidebar.all_crew') ?></a>
                <a href="<?= BASE_URL ?>crews/skill-matrix" class="block px-3 py-1.5 rounded-md text-xs <?= $act['skill-matrix'] ? $activeSub : $inactiveSub ?>"><?= __('sidebar.skill_matrix') ?></a>
            </div>
        </div>

        <!-- Crew Payroll -->
        <div x-data="{ open: <?= $act['crew-payroll'] ? 'true' : 'false' ?> }" x-cloak>
            <button @click.prevent="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg <?= $act['crew-payroll'] ? $activeClass : $inactiveClass ?>">
                <span class="flex items-center gap-3">
                    <span class="material-icons text-[18px] <?= $act['crew-payroll'] ? $activeIcon : $inactiveIcon ?>">account_balance_wallet</span>
                    <?= __('sidebar.crew_payroll') ?>
                </span>
                <span class="material-icons text-[16px] transition-transform duration-200" :class="open && 'rotate-180'">expand_more</span>
            </button>
            <div x-show="open" x-collapse class="ml-9 mt-1 space-y-0.5">
                <a href="<?= BASE_URL ?>payroll" class="block px-3 py-1.5 rounded-md text-xs <?= $act['crew-payroll-mgmt'] ? $activeSub : $inactiveSub ?>"><?= __('sidebar.management_label') ?></a>
                <a href="<?= BASE_URL ?>payroll/history" class="block px-3 py-1.5 rounded-md text-xs <?= $act['crew-payroll-history'] ? $activeSub : $inactiveSub ?>"><?= __('sidebar.history') ?></a>
            </div>
        </div>

        <!-- Documents -->
        <a href="<?= BASE_URL ?>documents" class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $act['documents'] ? $activeClass : $inactiveClass ?>">
            <span class="material-icons text-[18px] <?= $act['documents'] ? $activeIcon : $inactiveIcon ?>">folder_open</span>
            <?= __('sidebar.documents') ?>
        </a>

        <!-- AI Document Scanner -->
        <a href="<?= BASE_URL ?>DocumentParser" class="flex items-center justify-between px-3 py-2 rounded-lg <?= $act['doc-parser'] ? $activeClass : $inactiveClass ?>">
            <span class="flex items-center gap-3">
                <span class="material-icons text-[18px] <?= $act['doc-parser'] ? $activeIcon : $inactiveIcon ?>">smart_toy</span>
                <?= __('sidebar.ai_scan_cert') ?>
            </span>
            <span class="px-1.5 py-0.5 text-[9px] font-bold bg-violet-100 text-violet-600 rounded-full leading-none">AI</span>
        </a>

        <!-- Performance -->
        <a href="<?= BASE_URL ?>crews/performance" class="flex items-center justify-between px-3 py-2 rounded-lg <?= $act['crew-performance'] ? $activeClass : $inactiveClass ?>">
            <span class="flex items-center gap-3">
                <span class="material-icons text-[18px] <?= $act['crew-performance'] ? $activeIcon : $inactiveIcon ?>">insights</span>
                <?= __('sidebar.performance') ?>
            </span>
            <?php if (!$act['crew-performance']): ?>
                <span class="px-1.5 py-0.5 text-[9px] font-bold bg-blue-100 text-blue-600 rounded-full leading-none"><?= __('common.new') ?></span>
            <?php endif; ?>
        </a>

        <!-- EMPLOYEE -->
        <div class="px-3 pt-4 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider"><?= __('sidebar.employee') ?></div>

        <a href="<?= BASE_URL ?>employees" class="flex items-center justify-between px-3 py-2 rounded-lg <?= $act['employees'] ? $activeClass : $inactiveClass ?>">
            <span class="flex items-center gap-3">
                <span class="material-icons text-[18px] <?= $act['employees'] ? $activeIcon : $inactiveIcon ?>">person</span>
                <?= __('sidebar.employee_data') ?>
            </span>
            <span class="px-1.5 py-0.5 text-[9px] font-bold bg-green-100 text-green-600 rounded-full leading-none">HRIS</span>
        </a>

        <a href="<?= BASE_URL ?>employees/attendance" class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $act['attendance'] ? $activeClass : $inactiveClass ?>">
            <span class="material-icons text-[18px] <?= $act['attendance'] ? $activeIcon : $inactiveIcon ?>">schedule</span>
            <?= __('sidebar.attendance') ?>
        </a>

        <a href="<?= BASE_URL ?>employees/payroll" class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $act['emp-payroll'] ? $activeClass : $inactiveClass ?>">
            <span class="material-icons text-[18px] <?= $act['emp-payroll'] ? $activeIcon : $inactiveIcon ?>">receipt_long</span>
            <?= __('sidebar.payroll') ?>
        </a>

        <a href="<?= BASE_URL ?>employees/performance" class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $act['emp-performance'] ? $activeClass : $inactiveClass ?>">
            <span class="material-icons text-[18px] <?= $act['emp-performance'] ? $activeIcon : $inactiveIcon ?>">trending_up</span>
            <?= __('sidebar.performance') ?>
        </a>

        <!-- RECRUITMENT -->
        <div class="px-3 pt-4 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider"><?= __('sidebar.recruitment') ?></div>

        <a href="<?= BASE_URL ?>recruitment/pipeline" class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $act['pipeline'] ? $activeClass : $inactiveClass ?>">
            <span class="material-icons text-[18px] <?= $act['pipeline'] ? $activeIcon : $inactiveIcon ?>">filter_alt</span>
            <?= __('sidebar.pipeline') ?>
        </a>

        <a href="<?= BASE_URL ?>recruitment/approval" class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $act['approval'] ? $activeClass : $inactiveClass ?>">
            <span class="material-icons text-[18px] <?= $act['approval'] ? $activeIcon : $inactiveIcon ?>">check_circle</span>
            <?= __('sidebar.approval') ?>
        </a>

        <a href="<?= BASE_URL ?>recruitment/onboarding" class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $act['onboarding'] ? $activeClass : $inactiveClass ?>">
            <span class="material-icons text-[18px] <?= $act['onboarding'] ? $activeIcon : $inactiveIcon ?>">person_add</span>
            <?= __('sidebar.onboarding') ?>
        </a>

        <!-- MONITORING -->
        <div class="px-3 pt-4 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider"><?= __('sidebar.monitoring') ?></div>

        <a href="<?= BASE_URL ?>monitoring/visitors" class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $act['visitors'] ? $activeClass : $inactiveClass ?>">
            <span class="material-icons text-[18px] <?= $act['visitors'] ? $activeIcon : $inactiveIcon ?>">visibility</span>
            <?= __('sidebar.visitor_cp') ?>
        </a>

        <a href="<?= BASE_URL ?>monitoring/activity" class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $act['activity'] ? $activeClass : $inactiveClass ?>">
            <span class="material-icons text-[18px] <?= $act['activity'] ? $activeIcon : $inactiveIcon ?>">list_alt</span>
            <?= __('sidebar.activity_log') ?>
        </a>

        <a href="<?= BASE_URL ?>monitoring/integration" class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $act['integration'] ? $activeClass : $inactiveClass ?>">
            <span class="material-icons text-[18px] <?= $act['integration'] ? $activeIcon : $inactiveIcon ?>">extension</span>
            <?= __('sidebar.integration') ?>
        </a>

        <!-- REPORTS -->
        <div class="px-3 pt-4 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider"><?= __('sidebar.reports') ?></div>

        <div x-data="{ open: <?= $act['reports'] ? 'true' : 'false' ?> }" x-cloak>
            <button @click.prevent="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg <?= $act['reports'] ? $activeClass : $inactiveClass ?>">
                <span class="flex items-center gap-3">
                    <span class="material-icons text-[18px] <?= $act['reports'] ? $activeIcon : $inactiveIcon ?>">assessment</span>
                    <?= __('sidebar.reports') ?>
                </span>
                <span class="material-icons text-[16px] transition-transform duration-200" :class="open && 'rotate-180'">expand_more</span>
            </button>
            <div x-show="open" x-collapse class="ml-9 mt-1 space-y-0.5">
                <a href="<?= BASE_URL ?>reports" class="block px-3 py-1.5 rounded-md text-xs <?= $act['reports-overview'] ? $activeSub : $inactiveSub ?>"><?= __('sidebar.report_overview') ?></a>
                <a href="<?= BASE_URL ?>reports/by-vessel" class="block px-3 py-1.5 rounded-md text-xs <?= $act['reports-vessel'] ? $activeSub : $inactiveSub ?>"><?= __('sidebar.crew_report') ?></a>
                <a href="<?= BASE_URL ?>reports/employees" class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs <?= $act['reports-emp'] ? $activeSub : $inactiveSub ?>">
                    <?= __('sidebar.employee_report') ?> <span class="px-1 text-[8px] font-bold bg-green-100 text-green-600 rounded"><?= __('common.new') ?></span>
                </a>
                <a href="<?= BASE_URL ?>reports/payroll-summary" class="block px-3 py-1.5 rounded-md text-xs <?= $act['reports-finance'] ? $activeSub : $inactiveSub ?>"><?= __('sidebar.financial') ?></a>
            </div>
        </div>

        <!-- SETTINGS -->
        <div class="px-3 pt-4 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider"><?= __('sidebar.settings_section') ?></div>

        <a href="<?= BASE_URL ?>notifications" class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $act['notifications'] ? $activeClass : $inactiveClass ?>">
            <span class="material-icons text-[18px] <?= $act['notifications'] ? $activeIcon : $inactiveIcon ?>">notifications</span>
            <?= __('sidebar.notifications') ?>
        </a>

        <a href="<?= BASE_URL ?>settings" class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $act['settings'] ? $activeClass : $inactiveClass ?>">
            <span class="material-icons text-[18px] <?= $act['settings'] ? $activeIcon : $inactiveIcon ?>">settings</span>
            <?= __('sidebar.settings') ?>
        </a>

        <a href="<?= BASE_URL ?>users" class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $act['users'] ? $activeClass : $inactiveClass ?>">
            <span class="material-icons text-[18px] <?= $act['users'] ? $activeIcon : $inactiveIcon ?>">manage_accounts</span>
            <?= __('sidebar.users') ?>
        </a>

        <!-- Bottom spacing -->
        <div class="h-4"></div>
    </nav>

    <!-- User Footer -->
    <div class="p-3 border-t border-slate-100 bg-white flex-shrink-0">
        <?php if ($currentUser): ?>
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2 min-w-0 flex-1">
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                        <?= strtoupper(substr($currentUser['full_name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold text-slate-800 truncate"><?= htmlspecialchars($currentUser['full_name'] ?? 'User') ?></p>
                        <p class="text-[10px] text-slate-500 truncate"><?= htmlspecialchars($currentUser['email'] ?? '') ?></p>
                    </div>
                </div>
                <!-- Language Toggle -->
                <button onclick="toggleLanguage()" class="p-1.5 text-slate-400 hover:text-blue-500 hover:bg-blue-50 rounded-lg" title="<?= __('settings.language') ?>">
                    <span class="text-[10px] font-bold"><?= strtoupper(getLanguage()) ?></span>
                </button>
                <a href="<?= BASE_URL ?>auth/logout" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg" title="<?= __('sidebar.logout') ?>">
                    <span class="material-icons text-lg">logout</span>
                </a>
            </div>
        <?php else: ?>
            <a href="<?= BASE_URL ?>auth/login" class="flex items-center justify-center gap-2 w-full px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold">
                <span class="material-icons text-base">login</span> <?= __('sidebar.login') ?>
            </a>
        <?php endif; ?>
    </div>
</aside>

<script>
// Language toggle
function toggleLanguage() {
    const current = '<?= getLanguage() ?>';
    const newLang = current === 'en' ? 'id' : 'en';
    
    fetch('<?= BASE_URL ?>settings/change-language', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: 'language=' + newLang
    })
    .then(r => r.json())
    .then(data => { if (data.success) location.reload(); })
    .catch(() => { location.href = '<?= BASE_URL ?>settings/change-language?language=' + newLang; });
}

// Save & restore sidebar scroll position
(function() {
    var STORAGE_KEY = 'sidebar_scroll_pos';
    var nav = document.getElementById('sidebarNav');
    if (!nav) return;
    var saved = sessionStorage.getItem(STORAGE_KEY);
    if (saved !== null) nav.scrollTop = parseInt(saved, 10);
    var debounce;
    nav.addEventListener('scroll', function() {
        clearTimeout(debounce);
        debounce = setTimeout(function() { sessionStorage.setItem(STORAGE_KEY, nav.scrollTop); }, 50);
    });
    window.addEventListener('beforeunload', function() { sessionStorage.setItem(STORAGE_KEY, nav.scrollTop); });
})();
</script>
