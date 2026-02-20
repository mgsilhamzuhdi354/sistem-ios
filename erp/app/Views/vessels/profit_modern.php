<!DOCTYPE html>
<html lang="<?= session()->get('lang') ?? 'en' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('vessels.profit_analysis') ?> | IndoOcean ERP</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary": "#137fec",
                        "primary-soft": "#e0f0ff",
                        "background-light": "#f8fafc",
                        "glass-border": "rgba(255, 255, 255, 0.5)",
                        "glass-bg": "rgba(255, 255, 255, 0.7)",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    boxShadow: {
                        'soft-glow': '0 4px 20px -2px rgba(19, 127, 236, 0.1)',
                        'glass': '0 8px 32px 0 rgba(31, 38, 135, 0.07)',
                    }
                },
            },
        }
    </script>

    <style>
        body {
            font-family: 'Inter', 'Plus Jakarta Sans', sans-serif;
        }

        .backdrop-blur-md {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .sparkline path {
            stroke-dasharray: 1000;
            stroke-dashoffset: 0;
            animation: dash 2s ease-in-out;
        }

        @keyframes dash {
            from {
                stroke-dashoffset: 1000;
            }

            to {
                stroke-dashoffset: 0;
            }
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>

<body class="bg-background-light text-slate-900 font-display antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Modern Compact Sidebar -->
        <?php
        $currentPage = 'vessels';
        include APPPATH . 'Views/partials/modern_sidebar.php';
        ?>

        <!-- Main Content -->
        <main class="ml-64 flex-1 flex flex-col h-full relative overflow-y-auto overflow-x-hidden bg-slate-50/50">
            <!-- Background Elements for Glassmorphism -->
            <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-b from-white to-transparent -z-10"></div>
            <div class="absolute -top-20 -right-20 w-96 h-96 bg-primary/5 rounded-full blur-3xl -z-10"></div>
            <div class="absolute top-40 left-10 w-72 h-72 bg-blue-400/5 rounded-full blur-3xl -z-10"></div>

            <div class="container mx-auto max-w-7xl p-6 md:p-10 flex flex-col gap-8">
                <!-- Header Section -->
                <header class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div class="flex flex-col gap-2">
                        <nav class="flex items-center gap-2 text-sm text-slate-400 font-medium">
                            <a href="<?= BASE_URL ?>" class="hover:text-primary transition-colors"><?= __('common.home') ?></a>
                            <span class="material-icons-round text-[10px]">chevron_right</span>
                            <a href="<?= BASE_URL ?>vessels" class="hover:text-primary transition-colors"><?= __('vessels.title') ?></a>
                            <span class="material-icons-round text-[10px]">chevron_right</span>
                            <span class="text-slate-600"><?= __('vessels.profit_analysis') ?></span>
                        </nav>
                        <h2 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight leading-none">
                            <?= __('vessels.profit_analysis') ?>
                        </h2>
                        <p class="text-slate-500 font-medium"><?= __('vessels.profit_subtitle') ?></p>
                    </div>

                    <div class="flex items-center gap-4">
                        <!-- Back Button -->
                        <a href="<?= BASE_URL ?>vessels"
                            class="flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 px-5 py-2.5 rounded-xl text-sm font-bold border border-slate-200 shadow-sm transition-all hover:shadow-md active:scale-95">
                            <span class="material-icons-round text-lg">arrow_back</span>
                            Back
                        </a>
                    </div>
                </header>

                <!-- KPI Cards Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                    <?php
                    // Calculate totals
                    $totalRevenue = 0;
                    $totalCost = 0;
                    $profitableCount = 0;

                    foreach ($profitData as $vessel) {
                        $totalRevenue += $vessel['revenue_usd'];
                        $totalCost += $vessel['cost_usd'];
                        if ($vessel['is_profitable'])
                            $profitableCount++;
                    }

                    $totalProfit = $totalRevenue - $totalCost;
                    $avgMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;
                    ?>

                    <!-- KPI 1: Total Revenue -->
                    <div
                        class="relative overflow-hidden bg-glass-bg backdrop-blur-md rounded-2xl p-6 border border-white shadow-glass hover:shadow-soft-glow transition-all group">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2 bg-blue-50 text-primary rounded-lg">
                                <span class="material-icons-round">attach_money</span>
                            </div>
                            <span
                                class="flex items-center text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full border border-emerald-100">
                                <span class="material-icons-round text-sm">trending_up</span>
                            </span>
                        </div>
                        <div class="flex flex-col">
                            <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider mb-1"><?= __('vessels.total_revenue') ?></p>
                            <h3 class="text-3xl font-black text-slate-900 tracking-tight">
                                $
                                <?= number_format($totalRevenue / 1000, 1) ?>k
                            </h3>
                        </div>
                        <svg class="absolute bottom-0 left-0 w-full h-16 opacity-30 group-hover:opacity-50 transition-opacity sparkline text-primary"
                            preserveAspectRatio="none" viewBox="0 0 200 60">
                            <path d="M0,45 C50,45 50,15 100,25 C150,35 150,5 200,15 L200,60 L0,60 Z"
                                fill="currentColor" />
                        </svg>
                    </div>

                    <!-- KPI 2: Total Cost -->
                    <div
                        class="relative overflow-hidden bg-glass-bg backdrop-blur-md rounded-2xl p-6 border border-white shadow-glass hover:shadow-soft-glow transition-all group">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2 bg-red-50 text-rose-500 rounded-lg">
                                <span class="material-icons-round">money_off</span>
                            </div>
                            <span
                                class="flex items-center text-xs font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded-full border border-rose-100">
                                <span class="material-icons-round text-sm">trending_up</span>
                            </span>
                        </div>
                        <div class="flex flex-col">
                            <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider mb-1"><?= __('vessels.total_cost') ?></p>
                            <h3 class="text-3xl font-black text-slate-900 tracking-tight">
                                $
                                <?= number_format($totalCost / 1000, 1) ?>k
                            </h3>
                        </div>
                        <svg class="absolute bottom-0 left-0 w-full h-16 opacity-30 group-hover:opacity-50 transition-opacity sparkline text-rose-500"
                            preserveAspectRatio="none" viewBox="0 0 200 60">
                            <path d="M0,50 C30,45 60,55 90,40 C120,25 150,45 200,30 L200,60 L0,60 Z"
                                fill="currentColor" />
                        </svg>
                    </div>

                    <!-- KPI 3: Net Profit -->
                    <div
                        class="relative overflow-hidden bg-gradient-to-br from-primary to-blue-600 rounded-2xl p-6 border border-primary shadow-xl shadow-blue-500/20 group text-white">
                        <div
                            class="absolute top-0 right-0 p-8 w-32 h-32 bg-white/10 rounded-full blur-2xl -mr-10 -mt-10">
                        </div>
                        <div class="flex justify-between items-start mb-4 relative z-10">
                            <div class="p-2 bg-white/20 text-white rounded-lg backdrop-blur-sm">
                                <span class="material-icons-round">account_balance_wallet</span>
                            </div>
                            <span
                                class="flex items-center text-xs font-bold text-white bg-white/20 px-2 py-1 rounded-full backdrop-blur-md">
                                <span class="material-icons-round text-sm">trending_up</span>
                            </span>
                        </div>
                        <div class="flex flex-col relative z-10">
                            <p class="text-blue-100 text-sm font-semibold uppercase tracking-wider mb-1"><?= __('vessels.net_profit') ?></p>
                            <h3 class="text-3xl font-black text-white tracking-tight">
                                $
                                <?= number_format($totalProfit / 1000, 1) ?>k
                            </h3>
                        </div>
                        <svg class="absolute bottom-0 left-0 w-full h-16 opacity-40 group-hover:opacity-60 transition-opacity sparkline text-white"
                            preserveAspectRatio="none" viewBox="0 0 200 60">
                            <path d="M0,55 C40,50 60,30 100,25 C140,20 160,5 200,10 L200,60 L0,60 Z"
                                fill="currentColor" />
                        </svg>
                    </div>

                    <!-- KPI 4: Average Margin -->
                    <div
                        class="relative overflow-hidden bg-glass-bg backdrop-blur-md rounded-2xl p-6 border border-white shadow-glass hover:shadow-soft-glow transition-all group">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2 bg-indigo-50 text-indigo-500 rounded-lg">
                                <span class="material-icons-round">percent</span>
                            </div>
                            <span
                                class="flex items-center text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full border border-emerald-100">
                                <span class="material-icons-round text-sm">trending_up</span>
                            </span>
                        </div>
                        <div class="flex flex-col">
                            <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider mb-1"><?= __('vessels.avg_margin') ?></p>
                            <h3 class="text-3xl font-black text-slate-900 tracking-tight">
                                <?= number_format($avgMargin, 1) ?>%
                            </h3>
                        </div>
                        <div
                            class="absolute bottom-0 right-0 w-24 h-24 bg-indigo-500/5 rounded-full blur-xl mr-[-20px] mb-[-20px]">
                        </div>
                    </div>
                </div>

                <!-- Main Content Split -->
                <div class="grid lg:grid-cols-3 gap-6 min-h-[500px]">
                    <!-- Left: Vessel Performance Table -->
                    <div
                        class="lg:col-span-2 bg-white/80 backdrop-blur-xl rounded-2xl border border-white/60 shadow-glass flex flex-col overflow-hidden">
                        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-slate-900"><?= __('vessels.vessel_performance') ?></h3>
                            <button
                                class="p-2 text-slate-400 hover:text-primary hover:bg-slate-50 rounded-lg transition-colors">
                                <span class="material-icons-round">filter_list</span>
                            </button>
                        </div>

                        <div class="flex-1 overflow-auto">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-slate-50/50 sticky top-0 z-10 backdrop-blur-sm">
                                    <tr>
                                        <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                            <?= __('vessels.vessel_name') ?></th>
                                        <th
                                            class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">
                                            Revenue</th>
                                        <th
                                            class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">
                                            Cost</th>
                                        <th
                                            class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">
                                            Profit</th>
                                        <th
                                            class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    <?php foreach ($profitData as $vessel): ?>
                                        <tr class="group hover:bg-blue-50/30 transition-colors">
                                            <td class="py-4 px-6">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="<?= $vessel['is_profitable'] ? 'bg-blue-100 text-blue-600' : 'bg-rose-100 text-rose-600' ?> rounded-lg p-1.5">
                                                        <span class="material-icons-round text-sm">
                                                            <?= $vessel['is_profitable'] ? 'directions_boat' : 'warning' ?>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-slate-800 text-sm">
                                                            <?= htmlspecialchars($vessel['name']) ?>
                                                        </p>
                                                        <p class="text-[10px] text-slate-400 uppercase font-semibold">
                                                            <?= htmlspecialchars($vessel['vessel_type']) ?> •
                                                            <?= $vessel['crew_count'] ?> Crew
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td
                                                class="py-4 px-6 text-right font-medium text-slate-600 text-sm tabular-nums">
                                                $
                                                <?= number_format($vessel['revenue_usd'], 0) ?>
                                            </td>
                                            <td
                                                class="py-4 px-6 text-right font-medium text-slate-600 text-sm tabular-nums">
                                                $
                                                <?= number_format($vessel['cost_usd'], 0) ?>
                                            </td>
                                            <td
                                                class="py-4 px-6 text-right font-bold <?= $vessel['is_profitable'] ? 'text-slate-900' : 'text-rose-600' ?> text-sm tabular-nums">
                                                <?= $vessel['is_profitable'] ? '$' : '-$' ?>
                                                <?= number_format(abs($vessel['profit_usd']), 0) ?>
                                            </td>
                                            <td class="py-4 px-6 text-center">
                                                <?php if ($vessel['is_profitable']): ?>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 shadow-sm border border-emerald-200/50">
                                                        Profit
                                                        <?= number_format($vessel['margin_percent'], 0) ?>%
                                                    </span>
                                                <?php else: ?>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700 shadow-sm border border-rose-200/50">
                                                        Loss
                                                        <?= number_format(abs($vessel['margin_percent']), 0) ?>%
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Right: Profit Comparison Chart -->
                    <div
                        class="lg:col-span-1 bg-white/80 backdrop-blur-xl rounded-2xl border border-white/60 shadow-glass p-6 flex flex-col">
                        <div class="mb-6 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900"><?= __('vessels.profit_comparison') ?></h3>
                                <p class="text-xs text-slate-500 font-medium mt-1"><?= __('vessels.profit_comparison_subtitle') ?></p>
                            </div>
                        </div>

                        <div class="flex-1 flex flex-col justify-end gap-4 relative pl-8 pb-6">
                            <?php
                            // Get top 4 profitable and 1 loss vessel for chart
                            $chartVessels = array_slice($profitData, 0, 5);
                            $maxProfit = max(array_map(fn($v) => abs($v['profit_usd']), $chartVessels));
                            ?>

                            <!-- Y-Axis Labels -->
                            <div
                                class="absolute left-0 top-0 bottom-6 w-8 flex flex-col justify-between text-[10px] text-slate-400 font-medium text-right pr-2">
                                <span>$
                                    <?= number_format($maxProfit / 1000, 0) ?>k
                                </span>
                                <span>$
                                    <?= number_format($maxProfit / 2000, 0) ?>k
                                </span>
                                <span>$0</span>
                                <span>-$
                                    <?= number_format($maxProfit / 5000, 0) ?>k
                                </span>
                            </div>

                            <!-- Chart Bars -->
                            <div
                                class="flex items-end justify-between h-64 border-b border-l border-slate-200 pl-2 pb-0 gap-2 w-full">
                                <?php foreach ($chartVessels as $vessel):
                                    $height = ($maxProfit > 0) ? (abs($vessel['profit_usd']) / $maxProfit * 100) : 0;
                                    $isNegative = $vessel['profit_usd'] < 0;
                                    ?>
                                    <div class="flex flex-col items-center gap-2 group w-full">
                                        <?php if (!$isNegative): ?>
                                            <div class="relative w-full bg-primary rounded-t-lg transition-all duration-500 hover:bg-blue-600 group-hover:scale-y-105 origin-bottom"
                                                style="height: <?= $height ?>%">
                                                <div
                                                    class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-bold py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                                    $
                                                    <?= number_format($vessel['profit_usd'] / 1000, 0) ?>k
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="relative w-full h-full"></div>
                                            <div class="absolute top-full left-0 w-full bg-rose-400 rounded-b-lg transition-all duration-500 hover:bg-rose-500 group-hover:scale-y-110 origin-top"
                                                style="height: <?= $height / 4 ?>px; margin-top: 1px;">
                                                <div
                                                    class="absolute top-full mt-2 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-bold py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity z-20 whitespace-nowrap">
                                                    -$
                                                    <?= number_format(abs($vessel['profit_usd']) / 1000, 0) ?>k
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <span
                                            class="text-[10px] font-bold text-slate-500 rotate-45 origin-left mt-2 whitespace-nowrap">
                                            <?= substr($vessel['name'], 0, 8) ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Zero Line -->
                            <div class="absolute bottom-[24px] left-8 right-0 h-px bg-slate-300 z-0"></div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
