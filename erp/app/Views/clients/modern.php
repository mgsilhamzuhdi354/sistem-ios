<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Management | IndoOcean ERP</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#EAB308",
                        "primary-hover": "#CA8A04",
                        "background-light": "#F1F5F9",
                        "surface-light": "#FFFFFF",
                        "border-light": "#E2E8F0",
                        "text-main-light": "#0F172A",
                        "text-sub-light": "#64748B",
                    },
                    fontFamily: {
                        sans: ["Inter", "sans-serif"],
                    },
                    boxShadow: {
                        'soft': '0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)',
                    }
                },
            },
        };
    </script>

    <style>
        body {
            font-family: 'Inter', 'Plus Jakarta Sans', sans-serif;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 3px;
        }

        .chart-gradient-area {
            fill: url(#gradientProfit);
        }

        .chart-line {
            stroke-dasharray: 1000;
            stroke-dashoffset: 1000;
            animation: draw 2s ease-out forwards;
        }

        @keyframes draw {
            to {
                stroke-dashoffset: 0;
            }
        }
    </style>
</head>

<body class="bg-background-light font-sans text-text-main-light antialiased overflow-hidden h-screen flex">
    <div class="flex h-screen w-full">
        <!-- Modern Sidebar -->
        <?php
        $currentPage = 'clients';
        include APPPATH . 'Views/partials/modern_sidebar.php';
        ?>

        <!-- Main Content -->
        <main class="ml-64 flex-1 flex flex-col min-w-0 bg-background-light">
            <!-- Header -->
            <header
                class="h-16 bg-surface-light border-b border-border-light flex items-center justify-between px-6 z-10 shadow-sm">
                <div class="flex items-center">
                    <h1 class="text-base font-bold text-text-main-light flex items-center gap-2">
                        <span class="text-slate-400 font-normal">Command Center /</span> <?= __('clients.title') ?>
                    </h1>
                </div>

                <div class="flex items-center space-x-4">
                    <div
                        class="hidden md:flex bg-slate-50 rounded-lg px-3 py-1.5 items-center border border-slate-200 focus-within:border-primary focus-within:ring-1 focus-within:ring-primary/20 transition-all w-72">
                        <i class="ph ph-magnifying-glass text-slate-400 mr-2"></i>
                        <input
                            class="bg-transparent border-none focus:ring-0 text-sm text-text-main-light w-full placeholder-slate-400 p-0"
                            placeholder="<?= __('common.search') ?>..." type="text" />
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-hidden flex flex-col md:flex-row">
                <!-- Left: Main Client List -->
                <div class="flex-1 overflow-y-auto p-6 scroll-smooth">
                    <?php
                    // Calculate aggregated KPIs from real data
                    $totalRevenue = 0;
                    $totalProfit = 0;
                    $clientCount = count($clients);
                    $profitableClients = 0;

                    foreach ($clients as $client) {
                        if (isset($client['total_revenue'])) {
                            $totalRevenue += $client['total_revenue'];
                        }
                        if (isset($client['total_profit'])) {
                            $totalProfit += $client['total_profit'];
                        }
                        if (isset($client['profit_margin']) && $client['profit_margin'] > 20) {
                            $profitableClients++;
                        }
                    }

                    $avgMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue * 100) : 0;

                    // Real KPI data from controller
                    $revenueGrowth = $revenueGrowth ?? 0;
                    $marginGrowth = $marginGrowth ?? 0;
                    $activeContractCount = $activeContracts ?? 0;
                    $contractGrowthPct = $contractGrowth ?? 0;
                    $trendData = $revenueTrend ?? [];
                    ?>

                    <!-- KPI Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <!-- Total Revenue -->
                        <div
                            class="bg-surface-light p-5 rounded-xl border border-border-light shadow-soft hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div class="p-2 bg-blue-50 rounded-lg">
                                    <i class="ph-fill ph-coins text-blue-600 text-xl"></i>
                                </div>
                                <?php if ($revenueGrowth != 0): ?>
                                <span class="text-xs font-medium px-2 py-1 rounded-full <?= $revenueGrowth > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' ?> flex items-center">
                                    <i class="ph-bold <?= $revenueGrowth > 0 ? 'ph-arrow-up-right' : 'ph-arrow-down-right' ?> mr-1"></i> <?= abs($revenueGrowth) ?>%
                                </span>
                                <?php else: ?>
                                <span class="text-xs font-medium px-2 py-1 rounded-full bg-slate-100 text-slate-500 flex items-center">
                                    <i class="ph ph-minus mr-1"></i> 0%
                                </span>
                                <?php endif; ?>
                            </div>
                            <p class="text-sm text-slate-500 font-medium"><?= __('dashboard.total_revenue') ?></p>
                            <h3 class="text-2xl font-bold text-slate-900 mt-1">
                                $
                                <?= number_format($totalRevenue / 1000, 1) ?>k
                            </h3>
                        </div>

                        <!-- Profit Margin -->
                        <div
                            class="bg-surface-light p-5 rounded-xl border border-border-light shadow-soft hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div class="p-2 bg-primary/10 rounded-lg">
                                    <i class="ph-fill ph-chart-bar text-primary text-xl"></i>
                                </div>
                                <?php if ($marginGrowth != 0): ?>
                                <span class="text-xs font-medium px-2 py-1 rounded-full <?= $marginGrowth > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' ?> flex items-center">
                                    <i class="ph-bold <?= $marginGrowth > 0 ? 'ph-arrow-up-right' : 'ph-arrow-down-right' ?> mr-1"></i> <?= abs($marginGrowth) ?>%
                                </span>
                                <?php else: ?>
                                <span class="text-xs font-medium px-2 py-1 rounded-full bg-slate-100 text-slate-500 flex items-center">
                                    <i class="ph ph-minus mr-1"></i> 0%
                                </span>
                                <?php endif; ?>
                            </div>
                            <p class="text-sm text-slate-500 font-medium"><?= __('dashboard.avg_margin') ?></p>
                            <h3 class="text-2xl font-bold text-slate-900 mt-1">
                                <?= number_format($avgMargin, 1) ?>%
                            </h3>
                        </div>

                        <!-- Active Contracts -->
                        <div
                            class="bg-surface-light p-5 rounded-xl border border-border-light shadow-soft hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div class="p-2 bg-purple-50 rounded-lg">
                                    <i class="ph-fill ph-file-text text-purple-600 text-xl"></i>
                                </div>
                                <?php if ($contractGrowthPct > 0): ?>
                                <span class="text-xs font-medium px-2 py-1 rounded-full bg-green-100 text-green-700 flex items-center">
                                    <i class="ph-bold ph-arrow-up-right mr-1"></i> <?= $contractGrowthPct ?>% new
                                </span>
                                <?php else: ?>
                                <span class="text-xs font-medium px-2 py-1 rounded-full bg-slate-100 text-slate-500">
                                    <?= $activeContractCount > 0 ? 'Stable' : 'No Data' ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            <p class="text-sm text-slate-500 font-medium"><?= __('dashboard.active_contracts') ?></p>
                            <div class="flex items-center mt-1">
                                <h3 class="text-2xl font-bold text-slate-900 mr-2">
                                    <?= $activeContractCount ?>
                                </h3>
                                <span class="text-sm text-slate-400">kontrak</span>
                            </div>
                        </div>
                    </div>

                    <!-- Section Header -->
                    <div class="flex flex-col sm:flex-row justify-between items-end mb-4 gap-4 px-1">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900"><?= __('sidebar.clients') ?></h2>
                            <p class="text-xs text-slate-500 mt-1">Monitoring
                                <?= $clientCount ?> principal accounts
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button
                                class="p-2 bg-white border border-border-light rounded-lg text-slate-500 hover:text-primary transition-colors">
                                <i class="ph ph-funnel"></i>
                            </button>
                            <button
                                class="p-2 bg-white border border-border-light rounded-lg text-slate-500 hover:text-primary transition-colors">
                                <i class="ph ph-export"></i>
                            </button>
                            <a href="<?= BASE_URL ?>clients/create"
                                class="px-3 py-2 bg-primary hover:bg-primary-hover text-white rounded-lg text-sm font-semibold shadow-md shadow-primary/20 transition-all flex items-center">
                                <i class="ph-bold ph-plus mr-2"></i> <?= __('common.add_new') ?>
                            </a>
                        </div>
                    </div>

                    <!-- Table Header -->
                    <div
                        class="grid grid-cols-12 gap-4 px-5 py-3 bg-white rounded-t-xl text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-border-light sticky top-0 backdrop-blur-sm z-10">
                        <div class="col-span-4"><?= __('contracts.client') ?></div>
                        <div class="col-span-3"><?= __('dashboard.total_profit') ?></div>
                        <div class="col-span-2 text-right"><?= __('vessels.revenue') ?></div>
                        <div class="col-span-2 text-right"><?= __('vessels.margin') ?></div>
                        <div class="col-span-1 text-center"><?= __('common.actions') ?></div>
                    </div>

                    <!-- Client List -->
                    <div class="space-y-2 pb-6">
                        <?php
                        $maxProfit = max(array_column($clients, 'total_profit'));

                        foreach ($clients as $client):
                            $profitPercent = $maxProfit > 0 ? ($client['total_profit'] / $maxProfit * 100) : 0;
                            $contributionLabel = $profitPercent > 60 ? 'High' : ($profitPercent > 30 ? 'Med' : 'Low');
                            $tierClass = $profitPercent > 60 ? 'bg-blue-50 text-blue-600' :
                                ($profitPercent > 30 ? 'bg-slate-100 text-slate-600' : 'bg-orange-50 text-orange-600');
                            $tierLabel = $profitPercent > 60 ? 'TIER 1' : ($profitPercent > 30 ? 'TIER 2' : 'TIER 3');
                            $marginColor = $client['profit_margin'] > 25 ? 'text-green-500' :
                                ($client['profit_margin'] > 15 ? 'text-slate-600' : 'text-red-500');
                            ?>
                            <div
                                class="grid grid-cols-12 gap-4 px-5 py-4 bg-surface-light rounded-lg border border-border-light items-center hover:shadow-lg hover:border-primary/30 transition-all group cursor-pointer relative overflow-hidden">
                                <div
                                    class="absolute left-0 top-0 bottom-0 w-1 bg-primary opacity-0 group-hover:opacity-100 transition-opacity">
                                </div>

                                <!-- Client Info -->
                                <div class="col-span-4 flex items-center">
                                    <div
                                        class="h-10 w-10 rounded-full bg-slate-100 border-2 border-white shadow-sm flex-shrink-0 mr-3 flex items-center justify-center text-slate-600 font-bold text-sm">
                                        <?= strtoupper(substr($client['name'], 0, 2)) ?>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-900">
                                            <?= htmlspecialchars($client['name']) ?>
                                        </h3>
                                        <div class="flex items-center text-xs text-slate-500 mt-0.5">
                                            <span
                                                class="<?= $tierClass ?> px-1.5 py-0.5 rounded text-[10px] font-semibold mr-2">
                                                <?= $tierLabel ?>
                                            </span>
                                            <span>
                                                <?= htmlspecialchars($client['address'] ?? 'N/A') ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Profit Contribution -->
                                <div class="col-span-3 pr-6">
                                    <div class="flex justify-between text-xs mb-1.5">
                                        <span class="text-slate-500 font-medium">Contribution</span>
                                        <span class="font-bold text-slate-900">
                                            <?= $contributionLabel ?>
                                        </span>
                                    </div>
                                    <div class="flex w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="bg-primary" style="width: <?= $profitPercent ?>%"></div>
                                    </div>
                                    <p class="text-[10px] text-slate-400 mt-1 text-right">
                                        <?= number_format($profitPercent, 1) ?>% of total profit
                                    </p>
                                </div>

                                <!-- Revenue -->
                                <div class="col-span-2 text-right">
                                    <span class="block text-sm font-bold text-slate-900">
                                        $
                                        <?= number_format($client['total_revenue'], 0) ?>
                                    </span>
                                    <?php if ($client['growth_percentage'] > 0): ?>
                                        <span class="text-[10px] text-green-600 font-medium flex items-center justify-end">
                                            <i class="ph-bold ph-trend-up mr-1"></i>
                                            <?= $client['growth_percentage'] ?>% new
                                        </span>
                                    <?php else: ?>
                                        <span class="text-[10px] text-slate-400 font-medium">
                                            Stable
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Margin -->
                                <div class="col-span-2 text-right">
                                    <span class="block text-sm font-bold text-slate-700">
                                        <?= number_format($client['profit_margin'], 1) ?>%
                                    </span>
                                    <span class="text-[10px] text-slate-400">
                                        <?= $client['profit_margin'] > 25 ? 'Above Avg' : ($client['profit_margin'] > 15 ? 'Avg' : 'Below Avg') ?>
                                    </span>
                                </div>

                                <!-- Action -->
                                <div class="col-span-1 flex justify-center items-center gap-1 relative z-10">
                                    <a href="<?= BASE_URL ?>clients/<?= $client['id'] ?>"
                                        class="p-1.5 hover:bg-slate-100 rounded-full text-slate-400 hover:text-primary transition-colors"
                                        title="Detail">
                                        <i class="ph-bold ph-caret-right"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>clients/confirm-delete/<?= $client['id'] ?>"
                                        class="p-1.5 hover:bg-red-50 rounded-full text-red-400 hover:text-red-600 transition-colors"
                                        title="Hapus Client">
                                        <i class="ph-bold ph-trash"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Right: Analytics Sidebar -->
                <div class="w-full md:w-80 lg:w-96 bg-white border-l border-border-light flex flex-col shadow-xl z-20">
                    <div class="p-5 border-b border-border-light flex justify-between items-center bg-slate-50/50">
                        <h3 class="font-bold text-slate-900 flex items-center">
                            <i class="ph-fill ph-chart-line-up text-primary mr-2"></i> <?= __('analytics.title') ?>
                        </h3>
                    </div>

                    <div class="p-6 overflow-y-auto flex-1">
                        <!-- Trend Chart (Real Data) -->
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Profit Trend</h4>
                                <span class="text-xs text-primary font-bold">Last 6 Months</span>
                            </div>
                            <?php
                            $hasChartData = false;
                            $maxProfit_chart = 0;
                            foreach ($trendData as $td) {
                                if ($td['profit'] > 0) $hasChartData = true;
                                if ($td['profit'] > $maxProfit_chart) $maxProfit_chart = $td['profit'];
                            }
                            ?>
                            <?php if ($hasChartData && count($trendData) > 0): ?>
                            <div class="relative h-48 w-full bg-slate-50 rounded-lg border border-border-light p-2 overflow-hidden">
                                <?php
                                $chartWidth = 300;
                                $chartHeight = 120;
                                $padding = 10;
                                $points = [];
                                $n = count($trendData);
                                for ($ci = 0; $ci < $n; $ci++) {
                                    $x = $padding + ($ci / max($n - 1, 1)) * ($chartWidth - 2 * $padding);
                                    $y = $maxProfit_chart > 0
                                        ? $chartHeight - $padding - (($trendData[$ci]['profit'] / $maxProfit_chart) * ($chartHeight - 2 * $padding))
                                        : $chartHeight - $padding;
                                    $points[] = ['x' => round($x, 1), 'y' => round($y, 1)];
                                }
                                $linePath = '';
                                $areaPath = '';
                                foreach ($points as $pi => $pt) {
                                    $linePath .= ($pi === 0 ? 'M' : 'L') . $pt['x'] . ',' . $pt['y'] . ' ';
                                }
                                $areaPath = $linePath . 'V' . $chartHeight . ' H' . $points[0]['x'] . ' Z';
                                ?>
                                <svg class="w-full h-full overflow-visible" preserveAspectRatio="none" viewBox="0 0 <?= $chartWidth ?> <?= $chartHeight ?>">
                                    <defs>
                                        <linearGradient id="gradientProfit" x1="0" x2="0" y1="0" y2="1">
                                            <stop offset="0%" stop-color="#EAB308" stop-opacity="0.25"></stop>
                                            <stop offset="100%" stop-color="#EAB308" stop-opacity="0"></stop>
                                        </linearGradient>
                                    </defs>
                                    <line stroke="#cbd5e1" stroke-dasharray="4 4" stroke-width="0.5" x1="0" x2="<?= $chartWidth ?>" y1="30" y2="30"></line>
                                    <line stroke="#cbd5e1" stroke-dasharray="4 4" stroke-width="0.5" x1="0" x2="<?= $chartWidth ?>" y1="60" y2="60"></line>
                                    <line stroke="#cbd5e1" stroke-dasharray="4 4" stroke-width="0.5" x1="0" x2="<?= $chartWidth ?>" y1="90" y2="90"></line>
                                    <path class="chart-gradient-area" d="<?= $areaPath ?>"></path>
                                    <path d="<?= $linePath ?>" fill="none" stroke="#EAB308" stroke-linecap="round" stroke-width="2.5"></path>
                                    <?php foreach ($points as $pt): ?>
                                    <circle cx="<?= $pt['x'] ?>" cy="<?= $pt['y'] ?>" fill="#EAB308" r="3" stroke-width="2" stroke="white"></circle>
                                    <?php endforeach; ?>
                                </svg>
                                <!-- Month Labels -->
                                <div class="flex justify-between px-1 mt-1">
                                    <?php foreach ($trendData as $td): ?>
                                    <span class="text-[9px] text-slate-400 font-medium"><?= $td['month'] ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="relative h-48 w-full bg-slate-50 rounded-lg border border-border-light flex items-center justify-center">
                                <div class="text-center">
                                    <i class="ph ph-chart-line text-4xl text-slate-300 mb-2"></i>
                                    <p class="text-sm text-slate-400">Belum ada data kontrak</p>
                                    <p class="text-xs text-slate-300 mt-1">Buat kontrak dengan client rate untuk melihat trend</p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Top/Bottom Performers -->
                        <div class="space-y-6">
                            <?php
                            $topClient = $clients[0] ?? null;
                            $bottomClient = end($clients);
                            ?>

                            <?php if ($topClient): ?>
                                <div class="bg-background-light rounded-xl p-4 border border-border-light">
                                    <div class="flex items-center mb-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3">
                                            <i class="ph-fill ph-chart-bar"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500 font-medium uppercase">Highest Margin</p>
                                            <p class="text-sm font-bold text-slate-900">
                                                <?= htmlspecialchars($topClient['name']) ?>
                                            </p>
                                        </div>
                                        <div class="ml-auto text-green-500 font-bold text-sm">
                                            <?= number_format($topClient['profit_margin'], 0) ?>%
                                        </div>
                                    </div>
                                    <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                        <div class="bg-blue-500 h-full rounded-full"
                                            style="width: <?= min($topClient['profit_margin'] * 2, 100) ?>%"></div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($bottomClient): ?>
                                <div class="bg-background-light rounded-xl p-4 border border-border-light">
                                    <div class="flex items-center mb-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center mr-3">
                                            <i class="ph-fill ph-warning"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500 font-medium uppercase">Needs Attention</p>
                                            <p class="text-sm font-bold text-slate-900">
                                                <?= htmlspecialchars($bottomClient['name']) ?>
                                            </p>
                                        </div>
                                        <div class="ml-auto text-orange-500 font-bold text-sm">
                                            <?= number_format($bottomClient['profit_margin'], 0) ?>%
                                        </div>
                                    </div>
                                    <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                        <div class="bg-orange-500 h-full rounded-full"
                                            style="width: <?= min($bottomClient['profit_margin'] * 2, 100) ?>%"></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>


                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>