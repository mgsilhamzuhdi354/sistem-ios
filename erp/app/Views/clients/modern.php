<!DOCTYPE html>
<html lang="en">

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
                        <span class="text-slate-400 font-normal">Command Center /</span> Client Management
                    </h1>
                </div>

                <div class="flex items-center space-x-4">
                    <div
                        class="hidden md:flex bg-slate-50 rounded-lg px-3 py-1.5 items-center border border-slate-200 focus-within:border-primary focus-within:ring-1 focus-within:ring-primary/20 transition-all w-72">
                        <i class="ph ph-magnifying-glass text-slate-400 mr-2"></i>
                        <input
                            class="bg-transparent border-none focus:ring-0 text-sm text-text-main-light w-full placeholder-slate-400 p-0"
                            placeholder="Search clients, KPIs, vessels..." type="text" />
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-hidden flex flex-col md:flex-row">
                <!-- Left: Main Client List -->
                <div class="flex-1 overflow-y-auto p-6 scroll-smooth">
                    <?php
                    // Calculate aggregated KPIs
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
                    $satisfaction = 4.9; // Mock data
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
                                <span
                                    class="text-xs font-medium px-2 py-1 rounded-full bg-green-100 text-green-700 flex items-center">
                                    <i class="ph-bold ph-arrow-up-right mr-1"></i> 12.5%
                                </span>
                            </div>
                            <p class="text-sm text-slate-500 font-medium">Total Managed Revenue</p>
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
                                <span
                                    class="text-xs font-medium px-2 py-1 rounded-full bg-green-100 text-green-700 flex items-center">
                                    <i class="ph-bold ph-arrow-up-right mr-1"></i> 3.2%
                                </span>
                            </div>
                            <p class="text-sm text-slate-500 font-medium">Overall Profit Margin</p>
                            <h3 class="text-2xl font-bold text-slate-900 mt-1">
                                <?= number_format($avgMargin, 1) ?>%
                            </h3>
                        </div>

                        <!-- Satisfaction -->
                        <div
                            class="bg-surface-light p-5 rounded-xl border border-border-light shadow-soft hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div class="p-2 bg-purple-50 rounded-lg">
                                    <i class="ph-fill ph-smiley text-purple-600 text-xl"></i>
                                </div>
                                <span class="text-xs font-medium px-2 py-1 rounded-full bg-slate-100 text-slate-600">
                                    Top 5%
                                </span>
                            </div>
                            <p class="text-sm text-slate-500 font-medium">Client Satisfaction</p>
                            <div class="flex items-center mt-1">
                                <h3 class="text-2xl font-bold text-slate-900 mr-2">
                                    <?= number_format($satisfaction, 1) ?>
                                </h3>
                                <div class="flex text-yellow-400 text-sm">
                                    <i class="ph-fill ph-star"></i>
                                    <i class="ph-fill ph-star"></i>
                                    <i class="ph-fill ph-star"></i>
                                    <i class="ph-fill ph-star"></i>
                                    <i class="ph-fill ph-star-half"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Header -->
                    <div class="flex flex-col sm:flex-row justify-between items-end mb-4 gap-4 px-1">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Active Clients</h2>
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
                                <i class="ph-bold ph-plus mr-2"></i> Add Client
                            </a>
                        </div>
                    </div>

                    <!-- Table Header -->
                    <div
                        class="grid grid-cols-12 gap-4 px-5 py-3 bg-white rounded-t-xl text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-border-light sticky top-0 backdrop-blur-sm z-10">
                        <div class="col-span-4">Client / Principal</div>
                        <div class="col-span-3">Profit Contribution</div>
                        <div class="col-span-2 text-right">Revenue</div>
                        <div class="col-span-2 text-right">Margin</div>
                        <div class="col-span-1 text-center">Action</div>
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
                                <div class="col-span-1 flex justify-center">
                                    <a href="<?= BASE_URL ?>clients/<?= $client['id'] ?>"
                                        class="p-1.5 hover:bg-slate-100 rounded-full text-slate-400 hover:text-primary transition-colors">
                                        <i class="ph-bold ph-caret-right"></i>
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
                            <i class="ph-fill ph-chart-line-up text-primary mr-2"></i> Profit Analytics
                        </h3>
                    </div>

                    <div class="p-6 overflow-y-auto flex-1">
                        <!-- Trend Chart -->
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Profit Per Client
                                    Trend</h4>
                                <select
                                    class="text-xs border-none bg-transparent text-primary font-bold focus:ring-0 p-0 cursor-pointer">
                                    <option>Last 6 Months</option>
                                    <option>This Year</option>
                                </select>
                            </div>
                            <div
                                class="relative h-48 w-full bg-slate-50 rounded-lg border border-border-light p-2 overflow-hidden">
                                <svg class="w-full h-full overflow-visible" preserveAspectRatio="none"
                                    viewBox="0 0 300 120">
                                    <defs>
                                        <linearGradient id="gradientProfit" x1="0" x2="0" y1="0" y2="1">
                                            <stop offset="0%" stop-color="#EAB308" stop-opacity="0.25"></stop>
                                            <stop offset="100%" stop-color="#EAB308" stop-opacity="0"></stop>
                                        </linearGradient>
                                    </defs>
                                    <line stroke="#cbd5e1" stroke-dasharray="4 4" stroke-width="0.5" x1="0" x2="300"
                                        y1="30" y2="30"></line>
                                    <line stroke="#cbd5e1" stroke-dasharray="4 4" stroke-width="0.5" x1="0" x2="300"
                                        y1="60" y2="60"></line>
                                    <line stroke="#cbd5e1" stroke-dasharray="4 4" stroke-width="0.5" x1="0" x2="300"
                                        y1="90" y2="90"></line>
                                    <path class="chart-gradient-area"
                                        d="M0,80 Q50,70 80,40 T160,50 T240,30 T300,10 V120 H0 Z"></path>
                                    <path d="M0,80 Q50,70 80,40 T160,50 T240,30 T300,10" fill="none" stroke="#EAB308"
                                        stroke-linecap="round" stroke-width="2.5"></path>
                                    <circle cx="80" cy="40" fill="#EAB308" r="3" stroke-width="2" stroke="white">
                                    </circle>
                                    <circle cx="160" cy="50" fill="#EAB308" r="3" stroke-width="2" stroke="white">
                                    </circle>
                                    <circle cx="240" cy="30" fill="#EAB308" r="3" stroke-width="2" stroke="white">
                                    </circle>
                                </svg>
                            </div>
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

                        <!-- Quick Actions -->
                        <div class="mt-8 border-t border-border-light pt-6">
                            <h4 class="text-xs font-bold text-slate-900 mb-4">Quick Actions</h4>
                            <div class="grid grid-cols-2 gap-3">
                                <button
                                    class="p-3 border border-border-light rounded-lg hover:border-primary hover:text-primary transition-colors text-xs font-medium text-slate-600 flex flex-col items-center justify-center gap-2">
                                    <i class="ph ph-file-pdf text-xl"></i>
                                    Export Report
                                </button>
                                <button
                                    class="p-3 border border-border-light rounded-lg hover:border-primary hover:text-primary transition-colors text-xs font-medium text-slate-600 flex flex-col items-center justify-center gap-2">
                                    <i class="ph ph-envelope-simple text-xl"></i>
                                    Email Clients
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>