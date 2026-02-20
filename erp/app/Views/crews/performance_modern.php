<?php
/**
 * Modern Crew Performance View
 * Based on user's HTML mockup design
 */
$currentPage = 'crew-performance';

// Calculate stats
$totalCrew = count($performanceData ?? []);
$totalScore = 0;
foreach ($performanceData as $d) { $totalScore += $d['score']; }
$avgScore = $totalCrew > 0 ? round($totalScore / $totalCrew) : 0;
$excellentCount = count(array_filter($performanceData, fn($d) => $d['score'] >= 80));
$needImprovementCount = count(array_filter($performanceData, fn($d) => $d['score'] < 60));
?>
<!DOCTYPE html>
<html lang="<?= session()->get('lang') ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('crews.performance_title') ?> | IndoOcean ERP</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-blue': '#1e40af',
                        'brand-gold': '#f59e0b',
                        primary: '#0f172a',
                        secondary: '#3b82f6',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                },
            },
        };
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out forwards; }
        .animate-fade-in-delay-1 { animation: fadeInUp 0.4s ease-out 0.1s forwards; opacity: 0; }
        .animate-fade-in-delay-2 { animation: fadeInUp 0.4s ease-out 0.2s forwards; opacity: 0; }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="ml-64 flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">
            <!-- Header -->
            <header class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between z-10 flex-shrink-0 shadow-sm">
                <div class="flex items-center gap-4">
                    <h1 class="text-xl font-bold text-slate-800 tracking-tight"><?= __('crews.performance_title') ?></h1>
                    <div class="hidden sm:flex items-center px-3 py-1 bg-green-50 text-green-700 rounded-full text-xs font-medium border border-green-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-2 animate-pulse"></span>
                        System Online
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Language Toggle -->
                    <div class="flex bg-slate-100 p-1 rounded-lg">
                        <button class="px-3 py-1 text-xs font-medium bg-primary text-white rounded shadow-sm">ID</button>
                        <button class="px-3 py-1 text-xs font-medium text-slate-600 hover:bg-slate-200 rounded transition-colors">EN</button>
                    </div>
                    <!-- Notifications -->
                    <button class="p-2 text-slate-400 hover:text-primary transition-colors relative">
                        <span class="material-icons-outlined">notifications</span>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                    </button>
                </div>
            </header>

            <!-- Filter Bar -->
            <div class="bg-white border-b border-slate-200 px-6 py-4 flex flex-col md:flex-row md:items-center justify-between gap-4 flex-shrink-0">
                <div class="flex flex-col gap-1">
                    <h2 class="text-sm font-semibold text-slate-900"><?= __('crews.filter_performance') ?></h2>
                    <p class="text-xs text-slate-500"><?= __('crews.filter_performance_desc') ?></p>
                </div>
                <form method="GET" class="flex flex-wrap items-end gap-3">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider"><?= __('crews.crew_member') ?></label>
                        <div class="relative group">
                            <select name="crew_id" class="appearance-none bg-slate-50 border border-slate-200 text-slate-700 py-2.5 pl-4 pr-10 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary w-64 cursor-pointer transition-shadow hover:shadow-sm text-sm">
                                <option value="">-- <?= __('crews.all_crew_onboard') ?> --</option>
                                <?php foreach ($crews as $crew): ?>
                                    <option value="<?= $crew['id'] ?>" <?= ($selectedCrew == $crew['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($crew['full_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="material-icons-outlined absolute right-3 top-2.5 text-slate-400 pointer-events-none text-lg">expand_more</span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider"><?= __('crews.period') ?></label>
                        <input type="month" name="period" value="<?= $year ?>-<?= str_pad($month, 2, '0', STR_PAD_LEFT) ?>"
                            class="bg-slate-50 border border-slate-200 text-slate-700 py-2.5 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary cursor-pointer transition-shadow hover:shadow-sm text-sm">
                    </div>
                    <button type="submit" class="bg-primary hover:bg-slate-800 text-white px-5 py-2.5 rounded-lg text-sm font-medium shadow-sm shadow-primary/30 transition-all active:scale-95 flex items-center gap-2 h-[42px]">
                        <span class="material-icons-outlined text-lg">filter_list</span>
                        <?= __('common.filter') ?>
                    </button>
                </form>
            </div>

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto bg-slate-50 p-6 md:p-8 custom-scrollbar">
                <?php if (empty($performanceData)): ?>
                <!-- Empty State -->
                <div class="flex items-center justify-center h-full">
                    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden flex flex-col md:flex-row min-h-[400px] animate-fade-in">
                        <!-- Left Panel -->
                        <div class="md:w-5/12 bg-gradient-to-br from-blue-50 to-indigo-50 flex flex-col items-center justify-center p-8 text-center border-r border-slate-200 relative overflow-hidden">
                            <div class="absolute top-0 left-0 w-32 h-32 bg-blue-200/20 rounded-full blur-2xl -translate-x-10 -translate-y-10"></div>
                            <div class="absolute bottom-0 right-0 w-40 h-40 bg-indigo-200/20 rounded-full blur-3xl translate-x-10 translate-y-10"></div>
                            <div class="bg-white p-4 rounded-2xl shadow-lg mb-6 transform rotate-3 hover:rotate-0 transition-transform duration-500 relative z-10">
                                <span class="material-icons-outlined text-6xl text-secondary">insights</span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 mb-2 relative z-10"><?= __('crews.data_visualization') ?></h3>
                            <p class="text-sm text-slate-500 relative z-10">
                                <?= __('crews.kpi_tracking_desc') ?>
                            </p>
                        </div>
                        <!-- Right Panel -->
                        <div class="md:w-7/12 p-8 md:p-12 flex flex-col justify-center">
                            <div class="mb-6">
                                <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-secondary mb-4">
                                    <span class="material-icons-outlined">search</span>
                                </div>
                                <h2 class="text-2xl font-bold text-slate-900 mb-2"><?= __('crews.ready_analyze') ?></h2>
                                <p class="text-slate-600 text-sm leading-relaxed">
                                    <?= __('crews.no_performance_data') ?>
                                </p>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-start gap-3">
                                    <span class="material-icons-outlined text-green-500 mt-0.5">check_circle</span>
                                    <div>
                                        <h4 class="text-sm font-semibold text-slate-800"><?= __('crews.select_crew_step') ?></h4>
                                        <p class="text-xs text-slate-500"><?= __('crews.select_crew_step_desc') ?></p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="material-icons-outlined text-green-500 mt-0.5">check_circle</span>
                                    <div>
                                        <h4 class="text-sm font-semibold text-slate-800"><?= __('crews.set_period') ?></h4>
                                        <p class="text-xs text-slate-500"><?= __('crews.set_period_desc') ?></p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3 opacity-50">
                                    <span class="material-icons-outlined text-slate-400 mt-0.5">radio_button_unchecked</span>
                                    <div>
                                        <h4 class="text-sm font-semibold text-slate-800"><?= __('crews.view_report') ?></h4>
                                        <p class="text-xs text-slate-500"><?= __('crews.view_report_desc') ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-8 pt-6 border-t border-slate-200 flex justify-between items-center">
                                <span class="text-xs font-medium text-slate-400"><?= __('crews.need_help') ?> <a class="text-secondary hover:underline" href="#"><?= __('crews.view_docs') ?></a></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <!-- Performance Data -->
                <div class="max-w-7xl mx-auto space-y-6">
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-in">
                        <!-- Total Crew -->
                        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1"><?= __('crews.total_crew') ?></p>
                                <h3 class="text-3xl font-bold text-slate-900"><?= $totalCrew ?></h3>
                            </div>
                            <div class="h-12 w-12 rounded-lg bg-blue-50 flex items-center justify-center text-secondary group-hover:scale-110 transition-transform">
                                <span class="material-icons">groups</span>
                            </div>
                        </div>
                        <!-- Avg Performance -->
                        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1"><?= __('crews.avg_performance') ?></p>
                                <h3 class="text-3xl font-bold <?= $avgScore >= 80 ? 'text-green-600' : ($avgScore >= 60 ? 'text-yellow-600' : 'text-red-600') ?>"><?= $avgScore ?>%</h3>
                            </div>
                            <div class="h-12 w-12 rounded-lg <?= $avgScore >= 80 ? 'bg-green-50 text-green-500' : ($avgScore >= 60 ? 'bg-yellow-50 text-yellow-500' : 'bg-red-50 text-red-500') ?> flex items-center justify-center group-hover:scale-110 transition-transform">
                                <span class="material-icons">trending_up</span>
                            </div>
                        </div>
                        <!-- Excellent -->
                        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Excellent</p>
                                <h3 class="text-3xl font-bold text-green-600"><?= $excellentCount ?></h3>
                            </div>
                            <div class="h-12 w-12 rounded-lg bg-green-50 flex items-center justify-center text-green-500 group-hover:scale-110 transition-transform">
                                <span class="material-icons">verified</span>
                            </div>
                        </div>
                        <!-- Need Improvement -->
                        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1"><?= __('crews.needs_improvement') ?></p>
                                <h3 class="text-3xl font-bold text-red-600"><?= $needImprovementCount ?></h3>
                            </div>
                            <div class="h-12 w-12 rounded-lg bg-red-50 flex items-center justify-center text-red-500 group-hover:scale-110 transition-transform">
                                <span class="material-icons">warning</span>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Table -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden animate-fade-in-delay-1">
                        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-bold text-slate-900"><?= __('crews.detail_performance') ?></h3>
                                <p class="text-xs text-slate-500 mt-0.5">Periode: <?= $year ?>-<?= str_pad($month, 2, '0', STR_PAD_LEFT) ?></p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button class="px-3 py-1.5 text-xs font-medium text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors flex items-center gap-1">
                                    <span class="material-icons-outlined text-sm">download</span>
                                    <?= __('common.export') ?>
                                </button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-100">
                                        <th class="py-3 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider"><?= __('crews.crew_name') ?></th>
                                        <th class="py-3 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider"><?= __('ranks.position') ?></th>
                                        <th class="py-3 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider"><?= __('crews.vessel') ?></th>
                                        <th class="py-3 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider"><?= __('crews.attendance') ?></th>
                                        <th class="py-3 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider"><?= __('crews.skills') ?></th>
                                        <th class="py-3 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider"><?= __('crews.discipline') ?></th>
                                        <th class="py-3 px-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider"><?= __('crews.score') ?></th>
                                        <th class="py-3 px-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider"><?= __('common.status') ?></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php foreach ($performanceData as $data): ?>
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="py-3.5 px-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white text-xs font-bold">
                                                        <?= strtoupper(substr($data['crew']['full_name'] ?? 'X', 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-semibold text-slate-900"><?= htmlspecialchars($data['crew']['full_name'] ?? '-') ?></div>
                                                        <div class="text-xs text-slate-400"><?= htmlspecialchars($data['crew']['employee_id'] ?? '') ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-4 text-sm text-slate-600"><?= htmlspecialchars($data['crew']['rank_name'] ?? $data['crew']['position'] ?? '-') ?></td>
                                            <td class="py-3.5 px-4 text-sm text-slate-600"><?= htmlspecialchars($data['crew']['vessel_name'] ?? '-') ?></td>
                                            <td class="py-3.5 px-4">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-20 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                                        <div class="h-full bg-green-500 rounded-full" style="width: <?= $data['attendance'] ?>%"></div>
                                                    </div>
                                                    <span class="text-xs font-medium text-slate-600"><?= $data['attendance'] ?>%</span>
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-20 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                                        <div class="h-full bg-blue-500 rounded-full" style="width: <?= $data['skills'] ?>%"></div>
                                                    </div>
                                                    <span class="text-xs font-medium text-slate-600"><?= $data['skills'] ?>%</span>
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-20 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                                        <div class="h-full bg-yellow-500 rounded-full" style="width: <?= $data['discipline'] ?>%"></div>
                                                    </div>
                                                    <span class="text-xs font-medium text-slate-600"><?= $data['discipline'] ?>%</span>
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-4 text-center">
                                                <span class="text-lg font-bold text-slate-900"><?= $data['score'] ?></span>
                                            </td>
                                            <td class="py-3.5 px-4 text-center">
                                                <?php
                                                $score = $data['score'];
                                                if ($score >= 80):
                                                ?>
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                                        Excellent
                                                    </span>
                                                <?php elseif ($score >= 60): ?>
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700 border border-yellow-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5"></span>
                                                        Good
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>
                                                        Needs Improvement
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
