<?php
/**
 * Modern Activity Log Dashboard
 * PT Indo Ocean - ERP System
 * Command Center Design
 */
$currentPage = 'activity';
$logs = $logs ?? [];
$system = $system ?? 'all';
$limit = $_GET['limit'] ?? 100;
$search = $_GET['search'] ?? '';
$totalLogs = count($logs);

// System badge colors
$systemColors = [
    'erp' => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-700 dark:text-blue-400', 'dot' => 'bg-blue-500'],
    'hris' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400', 'dot' => 'bg-emerald-500'],
    'recruitment' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-400', 'dot' => 'bg-amber-500'],
    'company_profile' => ['bg' => 'bg-purple-100 dark:bg-purple-900/30', 'text' => 'text-purple-700 dark:text-purple-400', 'dot' => 'bg-purple-500'],
];

// Action icon mapping
$actionIcons = [
    'create' => 'add_circle',
    'update' => 'edit',
    'delete' => 'delete',
    'login' => 'login',
    'logout' => 'logout',
    'sync' => 'sync',
    'import' => 'cloud_download',
    'export' => 'cloud_upload',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log | IndoOcean ERP</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#1e3a8a",
                        secondary: "#D4AF37",
                        "background-light": "#f3f4f6",
                        "background-dark": "#0f172a",
                        "surface-light": "#ffffff",
                        "surface-dark": "#1e293b",
                    },
                    fontFamily: {
                        display: ["Inter", "sans-serif"],
                        mono: ["Fira Code", "monospace"],
                    },
                },
            },
        };
    </script>

    <style>
        .scrollbar-thin::-webkit-scrollbar { width: 6px; }
        .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background-color: rgba(148,163,184,0.3); border-radius: 3px; }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover { background-color: rgba(148,163,184,0.5); }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-in { animation: slideIn 0.3s ease-out forwards; }
        
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
        .pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200 font-display">

<div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 ml-64 flex flex-col h-screen overflow-hidden">
        <!-- Top Bar -->
        <div class="h-16 border-b border-slate-200 dark:border-slate-700 bg-white dark:bg-surface-dark flex items-center justify-between px-8 flex-shrink-0">
            <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400">
                <a href="<?= BASE_URL ?>" class="hover:text-slate-700">Home</a>
                <span class="material-icons text-xs">chevron_right</span>
                <a href="<?= BASE_URL ?>monitoring/visitors" class="hover:text-slate-700">Monitoring</a>
                <span class="material-icons text-xs">chevron_right</span>
                <span class="text-slate-800 dark:text-white font-medium">Activity Log</span>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="document.documentElement.classList.toggle('dark')" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    <span class="material-icons text-xl">dark_mode</span>
                </button>
                <a href="<?= BASE_URL ?>notifications" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors relative">
                    <span class="material-icons text-xl">notifications</span>
                </a>
            </div>
        </div>

        <!-- Page Content -->
        <div class="flex-1 overflow-y-auto p-8 scrollbar-thin">
            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                        <span class="material-icons-round text-primary text-3xl">history_edu</span>
                        Activity Log
                    </h1>
                    <p class="mt-2 text-slate-500 dark:text-slate-400">Monitor centralized system events and integrations in real-time.</p>
                </div>
                <div class="mt-4 md:mt-0 flex gap-3">
                    <a href="<?= BASE_URL ?>monitoring/activity?<?= http_build_query(array_merge($_GET, ['export' => 'csv'])) ?>"
                       class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-surface-dark border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm">
                        <span class="material-icons-round text-base">file_download</span>
                        Export
                    </a>
                    <a href="<?= BASE_URL ?>monitoring/activity"
                       class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-xl text-sm font-medium hover:bg-blue-900 transition-colors shadow-md">
                        <span class="material-icons-round text-base text-secondary">refresh</span>
                        Refresh Data
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <form method="GET" class="bg-white dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm mb-6">
                <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-end justify-between">
                    <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
                        <!-- Search -->
                        <div class="relative w-full sm:w-72">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="material-icons-round text-slate-400 text-lg">search</span>
                            </span>
                            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                                   class="block w-full pl-10 pr-3 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition"
                                   placeholder="Search by action, entity, or details...">
                        </div>

                        <!-- System Filter -->
                        <div class="relative w-full sm:w-48">
                            <select name="system" onchange="this.form.submit()"
                                    class="block w-full pl-3 pr-10 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm">
                                <option value="all" <?= $system === 'all' ? 'selected' : '' ?>>All Systems</option>
                                <option value="erp" <?= $system === 'erp' ? 'selected' : '' ?>>ERP</option>
                                <option value="hris" <?= $system === 'hris' ? 'selected' : '' ?>>HRIS</option>
                                <option value="recruitment" <?= $system === 'recruitment' ? 'selected' : '' ?>>Recruitment</option>
                                <option value="company_profile" <?= $system === 'company_profile' ? 'selected' : '' ?>>Company Profile</option>
                            </select>
                        </div>
                    </div>

                    <!-- Limit -->
                    <div class="flex items-center gap-3 w-full lg:w-auto justify-end border-t lg:border-t-0 border-slate-200 dark:border-slate-700 pt-4 lg:pt-0">
                        <span class="text-sm text-slate-500 dark:text-slate-400 font-medium">Rows:</span>
                        <select name="limit" onchange="this.form.submit()"
                                class="form-select block w-24 pl-3 pr-8 py-2 border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm">
                            <option value="25" <?= $limit == 25 ? 'selected' : '' ?>>25</option>
                            <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                            <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>100</option>
                            <option value="200" <?= $limit == 200 ? 'selected' : '' ?>>200</option>
                        </select>
                    </div>
                </div>
            </form>

            <!-- Activity Table -->
            <div class="bg-white dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-800/50">
                            <tr>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-48">Timestamp</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-40">System</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-48">Action</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Entity</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                            <?php if (empty($logs)): ?>
                                <!-- Empty State -->
                                <tr>
                                    <td class="px-6 py-20 text-center" colspan="5">
                                        <div class="flex flex-col items-center justify-center animate-slide-in">
                                            <div class="h-24 w-24 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-6">
                                                <span class="material-icons-round text-5xl text-slate-300 dark:text-slate-600">folder_open</span>
                                            </div>
                                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">No recent logs found</h3>
                                            <p class="text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-6">
                                                The system is active, but there are no recorded activities matching your current filters.
                                                Recording will start automatically once integrations are active.
                                            </p>
                                            <div class="flex gap-3">
                                                <a href="<?= BASE_URL ?>monitoring/activity"
                                                   class="px-5 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                                    Clear Filters
                                                </a>
                                                <a href="<?= BASE_URL ?>monitoring/integration"
                                                   class="px-5 py-2.5 text-primary hover:text-blue-700 dark:hover:text-blue-400 text-sm font-medium transition-colors flex items-center gap-1">
                                                    Check Integration <span class="material-icons-round text-sm">open_in_new</span>
                                                </a>
                                            </div>

                                            <!-- Ghost Rows Preview -->
                                            <div class="mt-8 w-full max-w-2xl opacity-20 space-y-3">
                                                <div class="flex items-center gap-4 px-4 py-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                                                    <div class="w-32 h-3 bg-slate-300 dark:bg-slate-600 rounded"></div>
                                                    <div class="w-20 h-5 bg-blue-100 dark:bg-blue-900/30 rounded-full"></div>
                                                    <div class="w-24 h-3 bg-slate-300 dark:bg-slate-600 rounded"></div>
                                                    <div class="flex-1 h-3 bg-slate-200 dark:bg-slate-700 rounded"></div>
                                                </div>
                                                <div class="flex items-center gap-4 px-4 py-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                                                    <div class="w-32 h-3 bg-slate-300 dark:bg-slate-600 rounded"></div>
                                                    <div class="w-20 h-5 bg-emerald-100 dark:bg-emerald-900/30 rounded-full"></div>
                                                    <div class="w-28 h-3 bg-slate-300 dark:bg-slate-600 rounded"></div>
                                                    <div class="flex-1 h-3 bg-slate-200 dark:bg-slate-700 rounded"></div>
                                                </div>
                                                <div class="flex items-center gap-4 px-4 py-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                                                    <div class="w-32 h-3 bg-slate-300 dark:bg-slate-600 rounded"></div>
                                                    <div class="w-20 h-5 bg-amber-100 dark:bg-amber-900/30 rounded-full"></div>
                                                    <div class="w-20 h-3 bg-slate-300 dark:bg-slate-600 rounded"></div>
                                                    <div class="flex-1 h-3 bg-slate-200 dark:bg-slate-700 rounded"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($logs as $i => $log): ?>
                                    <?php
                                    $sys = $log['source_system'] ?? 'erp';
                                    $colors = $systemColors[$sys] ?? $systemColors['erp'];
                                    $action = strtolower($log['action'] ?? '');
                                    $icon = 'event_note';
                                    foreach ($actionIcons as $key => $ic) {
                                        if (str_contains($action, $key)) { $icon = $ic; break; }
                                    }
                                    ?>
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors" style="animation-delay: <?= $i * 20 ?>ms;">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full <?= $colors['dot'] ?> pulse-dot flex-shrink-0"></span>
                                                <span class="text-sm font-mono text-slate-600 dark:text-slate-400">
                                                    <?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold <?= $colors['bg'] ?> <?= $colors['text'] ?>">
                                                <span class="w-1.5 h-1.5 rounded-full <?= $colors['dot'] ?>"></span>
                                                <?= strtoupper($sys) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <span class="material-icons-round text-base text-slate-400"><?= $icon ?></span>
                                                <span class="text-sm font-semibold text-slate-900 dark:text-white">
                                                    <?= htmlspecialchars($log['action']) ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-slate-600 dark:text-slate-300">
                                                <?= htmlspecialchars($log['entity_type'] ?? '-') ?>
                                            </span>
                                            <?php if (!empty($log['entity_id'])): ?>
                                                <code class="ml-1 px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 text-xs rounded text-slate-500 font-mono">#<?= $log['entity_id'] ?></code>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php if (!empty($log['details'])): ?>
                                                <?php $details = json_decode($log['details'], true); ?>
                                                <?php if ($details): ?>
                                                    <div x-data="{ expanded: false }">
                                                        <button @click="expanded = !expanded" class="text-xs text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 flex items-center gap-1 transition-colors">
                                                            <span class="material-icons-round text-xs" x-text="expanded ? 'unfold_less' : 'unfold_more'">unfold_more</span>
                                                            <span x-text="expanded ? 'Hide' : 'Show details'">Show details</span>
                                                        </button>
                                                        <div x-show="expanded" x-transition class="mt-2">
                                                            <pre class="text-xs bg-slate-50 dark:bg-slate-800 p-3 rounded-lg overflow-x-auto font-mono text-slate-600 dark:text-slate-400 max-w-md"><?= htmlspecialchars(json_encode($details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-xs text-slate-400 italic"><?= htmlspecialchars(mb_substr($log['details'], 0, 60)) ?>...</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-slate-300 dark:text-slate-600">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Footer -->
                <div class="bg-slate-50 dark:bg-slate-800/30 border-t border-slate-200 dark:border-slate-700 px-6 py-3.5 flex items-center justify-between">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Showing <span class="font-semibold text-slate-700 dark:text-slate-300"><?= $totalLogs ?></span> activities
                        <?php if ($system !== 'all'): ?>
                            from <span class="font-semibold text-slate-700 dark:text-slate-300"><?= strtoupper($system) ?></span>
                        <?php endif; ?>
                    </p>
                    <div class="flex items-center gap-2">
                        <span class="flex items-center gap-1.5 text-xs text-slate-400">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 pulse-dot"></span>
                            System Online
                        </span>
                    </div>
                </div>
            </div>

            <!-- Bottom spacing -->
            <div class="h-8"></div>
        </div>
    </main>
</div>

</body>
</html>
