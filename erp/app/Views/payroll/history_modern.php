<?php
/**
 * Modern Payroll History View
 * Professional design with sparkline charts and analytics
 */
$currentPage = 'payroll-history';

// Calculate YTD stats
$ytdTotal = 0;
$pendingCount = 0;
foreach ($periods as $period) {
    $ytdTotal += (float)($period['total_amount'] ?? 0);
    if (($period['status'] ?? '') === 'processing') {
        $pendingCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="<?= session()->get('lang') ?? 'en' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('payroll.history_title') ?> | IndoOcean ERP</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#0F172A",
                        secondary: "#3B82F6",
                        "background-light": "#F8FAFC",
                        "background-dark": "#0F172A",
                        "surface-light": "#FFFFFF",
                        "surface-dark": "#1E293B",
                        "text-light": "#334155",
                        "text-dark": "#E2E8F0",
                        "success": "#10B981",
                        "warning": "#F59E0B",
                    },
                    fontFamily: {
                        display: ["Inter", "sans-serif"],
                        body: ["Inter", "sans-serif"],
                    },
                    boxShadow: {
                        'soft': '0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)',
                        'deep': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
                    }
                },
            },
        };
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .sparkline { width: 100px; height: 30px; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark transition-colors duration-200">
    <div class="flex h-screen overflow-hidden">
        <!-- Modern Sidebar -->
        <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="ml-64 flex-1 overflow-y-auto focus:outline-none bg-background-light dark:bg-background-dark">
            <!-- Header -->
            <header class="bg-surface-light dark:bg-surface-dark border-b border-gray-200 dark:border-gray-700 h-16 flex items-center justify-between px-8 sticky top-0 z-10 shadow-sm">
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-gray-400 font-medium">Crew</span>
                    <span class="material-icons-outlined text-gray-400 text-sm">chevron_right</span>
                    <span class="text-gray-800 dark:text-white font-semibold"><?= __('payroll.history_title') ?></span>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="flex bg-gray-100 dark:bg-gray-800 rounded-lg p-1">
                        <button class="px-3 py-1 bg-white dark:bg-gray-700 shadow-sm rounded-md text-xs font-medium text-gray-700 dark:text-gray-200">ID</button>
                        <button class="px-3 py-1 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700">EN</button>
                    </div>
                    <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 relative">
                        <span class="material-icons">notifications</span>
                        <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-red-500 ring-2 ring-white dark:ring-surface-dark"></span>
                    </button>
                </div>
            </header>

            <!-- Content -->
            <div class="px-8 py-8 custom-scrollbar">
                <?php if (!empty($flash)): ?>
                    <?php if (isset($flash['success'])): ?>
                        <div class="mb-6 flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-700 dark:text-emerald-300 text-sm font-medium">
                            <span class="material-icons text-lg">check_circle</span>
                            <?= htmlspecialchars($flash['success']) ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Page Header -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2"><?= __('payroll.history_title') ?></h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Lihat semua periode payroll yang telah diproses</p>
                    <a href="<?= BASE_URL ?>payroll" class="inline-flex items-center text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 transition-colors">
                        <span class="material-icons text-sm mr-1">arrow_back</span>
                        <?= __('payroll.back_to_payroll') ?>
                    </a>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Total Disbursed YTD -->
                    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-soft hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400"><?= __('payroll.total_disbursed_ytd') ?></p>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">$<?= number_format($ytdTotal, 2) ?></h3>
                                <p class="text-xs text-green-600 mt-2 flex items-center">
                                    <span class="material-icons text-xs mr-1">trending_up</span>
                                    Total tahun berjalan
                                </p>
                            </div>
                            <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                                <span class="material-icons text-blue-600 dark:text-blue-400">account_balance_wallet</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Approvals -->
                    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-soft hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400"><?= __('payroll.pending_approvals') ?></p>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= $pendingCount ?> Periode</h3>
                                <p class="text-xs text-yellow-600 mt-2 flex items-center">
                                    <span class="material-icons text-xs mr-1">schedule</span>
                                    <?= $pendingCount > 0 ? 'Perlu perhatian' : 'Semua disetujui' ?>
                                </p>
                            </div>
                            <div class="p-3 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg">
                                <span class="material-icons text-yellow-600 dark:text-yellow-400">pending_actions</span>
                            </div>
                        </div>
                    </div>

                    <!-- Total Periods -->
                    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-soft hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400"><?= __('payroll.total_periods') ?></p>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= count($periods) ?></h3>
                                <p class="text-xs text-green-600 mt-2 flex items-center">
                                    <span class="material-icons text-xs mr-1">check_circle</span>
                                    Data historis
                                </p>
                            </div>
                            <div class="p-3 bg-purple-50 dark:bg-purple-900/30 rounded-lg">
                                <span class="material-icons text-purple-600 dark:text-purple-400">history</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters & Actions -->
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4" x-data="{ searchQuery: '' }">
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <div class="relative w-full sm:w-64">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="material-icons text-gray-400 text-sm">search</span>
                            </span>
                            <input type="text" x-model="searchQuery"
                                class="pl-10 pr-4 py-2 w-full border border-gray-200 dark:border-gray-700 rounded-lg text-sm bg-white dark:bg-surface-dark focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Cari periode atau status...">
                        </div>
                        <button class="p-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-surface-dark text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800">
                            <span class="material-icons text-sm">filter_list</span>
                        </button>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="flex items-center px-4 py-2 bg-white dark:bg-surface-dark border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                            <span class="material-icons text-sm mr-2">download</span>
                            <?= __('payroll.export_report') ?>
                        </button>
                    </div>
                </div>

                <!-- Periods Table -->
                <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-soft border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <?php if (empty($periods)): ?>
                        <!-- Empty State -->
                        <div class="flex flex-col items-center justify-center py-20 px-4">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-2xl flex items-center justify-center mb-4">
                                <span class="material-icons-outlined text-3xl text-gray-300 dark:text-gray-600">history</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white"><?= __('payroll.no_history') ?></h3>
                            <p class="text-slate-500 text-sm mt-1"><?= __('payroll.no_history_msg') ?></p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                                        <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Periode</th>
                                        <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Crew</th>
                                        <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Total Amount</th>
                                        <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                        <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    <?php
                                    $months = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                                    foreach ($periods as $idx => $period):
                                        $month = $period['period_month'] ?? 1;
                                        $year = $period['period_year'] ?? date('Y');
                                        $status = $period['status'] ?? 'draft';
                                        $totalItems = $period['total_items'] ?? 0;
                                        $totalAmount = (float)($period['total_amount'] ?? 0);

                                        $statusClass = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600';
                                        if ($status === 'completed') {
                                            $statusClass = 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300 border-green-200 dark:border-green-800';
                                        } elseif ($status === 'processing') {
                                            $statusClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300 border-yellow-200 dark:border-yellow-800';
                                        }
                                    ?>
                                        <tr class="group hover:bg-white dark:hover:bg-surface-dark hover:shadow-deep transition-all duration-200 cursor-pointer bg-white dark:bg-surface-dark relative z-0 hover:z-10">
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-bold text-gray-900 dark:text-white"><?= $months[$month] ?> <?= $year ?></span>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        <?= $months[$month] ?> 01 - <?= date('t', mktime(0, 0, 0, $month, 1, $year)) ?>, <?= $year ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6">
                                                <div class="flex items-center">
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300"><?= $totalItems ?></span>
                                                    <span class="text-xs text-gray-400 ml-2">(<?= $status === 'completed' ? 'Selesai' : 'Aktif' ?>)</span>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6 text-right">
                                                <span class="text-sm font-bold <?= $status === 'completed' ? 'text-gray-900 dark:text-white' : 'text-green-600 dark:text-green-400' ?>">
                                                    $<?= number_format($totalAmount, 2) ?>
                                                </span>
                                            </td>
                                            <td class="py-4 px-6">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusClass ?>">
                                                    <?= ucfirst($status) ?>
                                                </span>
                                            </td>
                                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                                <div class="flex items-center justify-end space-x-2">
                                                    <a href="<?= BASE_URL ?>payroll/show/<?= $period['id'] ?>" 
                                                        class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-full transition-colors" 
                                                        title="Lihat Detail">
                                                        <span class="material-icons text-lg">visibility</span>
                                                    </a>
                                                    <a href="<?= BASE_URL ?>payroll/export/<?= $period['id'] ?>" 
                                                        class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-full transition-colors" 
                                                        title="Download Laporan">
                                                        <span class="material-icons text-lg">download</span>
                                                    </a>
                                                    <?php if ($status !== 'completed'): ?>
                                                        <form method="POST" action="<?= BASE_URL ?>payroll/complete" style="display: inline;">
                                                            <input type="hidden" name="period_id" value="<?= $period['id'] ?>">
                                                            <button type="submit" 
                                                                class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 dark:hover:bg-green-900/30 rounded-full transition-colors" 
                                                                title="Tandai Selesai"
                                                                onclick="return confirm('Tandai periode ini sebagai selesai?')">
                                                                <span class="material-icons text-lg">check_circle</span>
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <button class="p-1.5 text-gray-300 cursor-not-allowed rounded-full" disabled title="Sudah selesai">
                                                            <span class="material-icons text-lg">check_circle</span>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Footer -->
                        <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                Menampilkan 1 sampai <?= min(count($periods), 20) ?> dari <?= count($periods) ?> entri
                            </span>
                            <div class="flex gap-1">
                                <button class="px-3 py-1 rounded border border-gray-200 dark:border-gray-700 bg-white dark:bg-surface-dark text-gray-500 dark:text-gray-400 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Previous
                                </button>
                                <button class="px-3 py-1 rounded border border-blue-500 bg-blue-50 dark:bg-blue-900 text-blue-600 dark:text-blue-200 text-sm">
                                    1
                                </button>
                                <button class="px-3 py-1 rounded border border-gray-200 dark:border-gray-700 bg-white dark:bg-surface-dark text-gray-500 dark:text-gray-400 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Next
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
