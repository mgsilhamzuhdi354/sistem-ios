<?php
/**
 * Modern Employee Performance Dashboard
 * PT Indo Ocean - ERP System
 */
$currentPage = $currentPage ?? 'employee-performance';

// Calculate stats from performance data
$totalEmployees = 0;
$excellentCount = 0;
$goodCount = 0;
$needsImprovement = 0;

if (!empty($performanceData)) {
    $totalEmployees = count($performanceData);
    foreach ($performanceData as $emp) {
        $monthlyScore = $emp['monthly_score'] ?? 0;
        if ($monthlyScore >= 80) {
            $excellentCount++;
        } elseif ($monthlyScore >= 60) {
            $goodCount++;
        } else {
            $needsImprovement++;
        }
    }
}

// Avatar colors
$avatarColors = [
    'bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-300',
    'bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300',
    'bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300',
    'bg-fuchsia-100 dark:bg-fuchsia-900 text-fuchsia-600 dark:text-fuchsia-300',
    'bg-pink-100 dark:bg-pink-900 text-pink-600 dark:text-pink-300',
    'bg-cyan-100 dark:bg-cyan-900 text-cyan-600 dark:text-cyan-300',
    'bg-orange-100 dark:bg-orange-900 text-orange-600 dark:text-orange-300',
];
?>
<!DOCTYPE html>
<html lang="<?= session()->get('lang') ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('employees.performance_title') ?> | IndoOcean ERP</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#a855f7",
                        secondary: "#fbbf24",
                    },
                    fontFamily: {
                        display: ["Inter", "sans-serif"],
                    },
                },
            },
        };
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-progress-excellent { background: linear-gradient(90deg, #10b981 0%, #34d399 100%); }
        .gradient-progress-good { background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 100%); }
        .gradient-progress-bad { background: linear-gradient(90deg, #ef4444 0%, #f87171 100%); }
    </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="ml-64 flex-1 flex flex-col h-screen overflow-hidden">
            <!-- Header -->
            <header class="bg-white dark:bg-gray-800 shadow-sm z-20 px-6 py-4 flex items-center justify-between sticky top-0">
                <div class="flex items-center gap-4">
                    <div class="bg-primary/10 dark:bg-primary/20 p-2 rounded-lg">
                        <span class="material-icons text-primary text-2xl">analytics</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white"><?= __('employees.performance_title') ?></h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
                            Tracking KPI & performa <?= date('F Y', mktime(0, 0, 0, $month, 1, $year)) ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                <span class="w-1.5 h-1.5 mr-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                LIVE
                            </span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition" onclick="document.documentElement.classList.toggle('dark')">
                        <span class="material-icons text-gray-500 dark:text-gray-400">dark_mode</span>
                    </button>
                </div>
            </header>

            <!-- Filter Bar -->
            <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-3">
                <form method="GET" action="<?= BASE_URL ?>employees/performance" class="flex gap-3 items-center">
                    <select name="employee_id" class="form-select text-sm border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary">
                        <option value="">Semua Karyawan</option>
                        <?php if (!empty($employees)): ?>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?= $emp['id'] ?>" <?= (isset($selectedEmployee) && $selectedEmployee == $emp['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($emp['nama'] ?? $emp['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <input name="month" class="form-input text-sm border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary" 
                        type="month" value="<?= $year ?>-<?= str_pad($month, 2, '0', STR_PAD_LEFT) ?>">
                    <button type="submit" class="bg-primary hover:bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-md hover:shadow-lg flex items-center gap-2">
                        <span class="material-icons text-sm">filter_list</span> Filter
                    </button>
                </form>
            </div>

            <!-- Main Content Area -->
            <main class="flex-1 p-6 overflow-y-auto space-y-6">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <div class="lg:col-span-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow relative overflow-hidden">
                            <div class="absolute right-0 top-0 h-full w-1 bg-purple-200 dark:bg-purple-900"></div>
                            <div class="flex items-start gap-4">
                                <div class="bg-purple-50 dark:bg-purple-900/30 p-3 rounded-lg flex items-center justify-center h-12 w-12 text-primary">
                                    <span class="material-icons">groups</span>
                                </div>
                                <div>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-white"><?= $totalEmployees ?></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1">Total Karyawan</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow relative overflow-hidden">
                            <div class="absolute right-0 top-0 h-full w-1 bg-emerald-200 dark:bg-emerald-900"></div>
                            <div class="flex items-start gap-4">
                                <div class="bg-emerald-50 dark:bg-emerald-900/30 p-3 rounded-lg flex items-center justify-center h-12 w-12 text-emerald-600 dark:text-emerald-400">
                                    <span class="material-icons">verified</span>
                                </div>
                                <div>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-white"><?= $excellentCount ?></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1">Excellent (≥80)</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow relative overflow-hidden">
                            <div class="absolute right-0 top-0 h-full w-1 bg-amber-200 dark:bg-amber-900"></div>
                            <div class="flex items-start gap-4">
                                <div class="bg-amber-50 dark:bg-amber-900/30 p-3 rounded-lg flex items-center justify-center h-12 w-12 text-amber-600 dark:text-amber-400">
                                    <span class="material-icons">thumb_up</span>
                                </div>
                                <div>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-white"><?= $goodCount ?></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1">Good (60-79)</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow relative overflow-hidden">
                            <div class="absolute right-0 top-0 h-full w-1 bg-rose-200 dark:bg-rose-900"></div>
                            <div class="flex items-start gap-4">
                                <div class="bg-rose-50 dark:bg-rose-900/30 p-3 rounded-lg flex items-center justify-center h-12 w-12 text-rose-600 dark:text-rose-400">
                                    <span class="material-icons">warning</span>
                                </div>
                                <div>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-white"><?= $needsImprovement ?></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1">Perlu Perbaikan (<60)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lg:col-span-4 bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col justify-between">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white"><?= __('employees.performance_trends') ?></h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?= __('employees.avg_score_6months') ?></p>
                            </div>
                            <span class="text-xs font-bold text-green-500 flex items-center bg-green-50 dark:bg-green-900/30 px-2 py-1 rounded-full">
                                <span class="material-icons text-xs mr-1">trending_up</span> +2.4%
                            </span>
                        </div>
                        <div class="relative h-24 w-full">
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Performance Table -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col">
                    <div class="grid grid-cols-12 gap-4 px-6 py-4 bg-primary text-white text-xs font-semibold uppercase tracking-wider items-center">
                        <div class="col-span-3"><?= __('employees.employee_col') ?></div>
                        <div class="col-span-2 hidden md:block"><?= __('employees.position_col') ?></div>
                        <div class="col-span-2 text-center hidden sm:block"><?= __('employees.total_assessments') ?></div>
                        <div class="col-span-2 text-center hidden sm:block"><?= __('employees.running_score') ?></div>
                        <div class="col-span-2 text-center"><?= __('employees.monthly_score') ?></div>
                        <div class="col-span-1 text-center"><?= __('common.status') ?></div>
                    </div>
                    <?php if (!$success || empty($performanceData)): ?>
                        <div class="p-12 text-center">
                            <div class="inline-block p-4 bg-gray-50 dark:bg-gray-700 rounded-full mb-4">
                                <span class="material-icons text-5xl text-gray-400">analytics</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2"><?= __('employees.no_performance') ?></h3>
                            <p class="text-gray-500 dark:text-gray-400">Belum ada data performa untuk periode ini.</p>
                        </div>
                    <?php else: ?>
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            <?php 
                            $counter = 0;
                            foreach ($performanceData as $i => $emp): 
                                $nama = $emp['nama'] ?? $emp['name'] ?? '-';
                                $jabatan = $emp['jabatan'] ?? $emp['position'] ?? '-';
                                $totalAssessments = $emp['total_assessments'] ?? 0;
                                $runningScore = $emp['running_score'] ?? 0;
                                $monthlyScore = $emp['monthly_score'] ?? 0;
                                
                                $initials = strtoupper(substr($nama, 0, 1));
                                if (strpos($nama, ' ') !== false) {
                                    $parts = explode(' ', $nama);
                                    $initials = strtoupper(substr($parts[0], 0, 1));
                                }
                                
                                $colorClass = $avatarColors[$counter % count($avatarColors)];
                                $counter++;
                                
                                // Determine status
                                if ($monthlyScore >= 80) {
                                    $status = 'EXCELLENT';
                                    $statusClass = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200 border-emerald-200 dark:border-emerald-800';
                                    $progressClass = 'gradient-progress-excellent shadow-[0_0_10px_rgba(16,185,129,0.5)]';
                                    $scoreColor = 'text-emerald-500';
                                } elseif ($monthlyScore >= 60) {
                                    $status = 'GOOD';
                                    $statusClass = 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200 border-amber-200 dark:border-amber-800';
                                    $progressClass = 'gradient-progress-good shadow-[0_0_10px_rgba(245,158,11,0.5)]';
                                    $scoreColor = 'text-amber-500';
                                } else {
                                    $status = 'PERLU PERBAIKAN';
                                    $statusClass = 'bg-rose-100 text-rose-800 dark:bg-rose-900 dark:text-rose-200 border-rose-200 dark:border-rose-800';
                                    $progressClass = 'bg-gray-400';
                                    $scoreColor = 'text-rose-500';
                                }
                            ?>
                                <div class="grid grid-cols-12 gap-4 px-6 py-4 items-center hover:bg-gray-50 dark:hover:bg-gray-800/50 transition duration-150">
                                    <div class="col-span-12 sm:col-span-3 flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-full <?= $colorClass ?> flex items-center justify-center text-sm font-bold shadow-sm">
                                            <?= $initials ?>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($nama) ?></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 md:hidden"><?= htmlspecialchars($jabatan) ?></p>
                                        </div>
                                    </div>
                                    <div class="col-span-2 hidden md:block text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($jabatan) ?></div>
                                    <div class="col-span-2 hidden sm:block text-center text-sm font-medium text-gray-900 dark:text-white"><?= $totalAssessments ?> penilaian</div>
                                    <div class="col-span-2 hidden sm:block text-center text-sm font-bold <?= $scoreColor ?>"><?= $runningScore ?></div>
                                    <div class="col-span-6 sm:col-span-2 flex flex-col justify-center px-4">
                                        <div class="flex justify-between text-xs mb-1">
                                            <span class="font-bold <?= $scoreColor ?>"><?= $monthlyScore ?></span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                            <div class="<?= $progressClass ?> h-1.5 rounded-full" style="width: <?= min($monthlyScore, 100) ?>%"></div>
                                        </div>
                                    </div>
                                    <div class="col-span-6 sm:col-span-1 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-medium <?= $statusClass ?> border">
                                            <?= $status ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Init ChartJS
        const ctx = document.getElementById('trendChart')?.getContext('2d');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb'],
                    datasets: [{
                        label: 'Performance Score',
                        data: [75, 78, 76, 82, 85, 87],
                        borderColor: '#a855f7',
                        backgroundColor: 'rgba(168, 85, 247, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        pointRadius: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#a855f7',
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            titleColor: '#f9fafb',
                            bodyColor: '#f9fafb',
                            padding: 8,
                            cornerRadius: 8,
                            displayColors: false,
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: { font: { size: 10 }, color: '#9ca3af' }
                        },
                        y: { display: false, min: 60, max: 100 }
                    }
                }
            });
        }
    </script>
</body>
</html>
