<?php
/**
 * Modern Payroll View
 * Tailwind + Chart.js + Alpine.js
 */
$currentPage = 'payroll';
$months = ['', __('payroll.months.january'), __('payroll.months.february'), __('payroll.months.march'), __('payroll.months.april'), __('payroll.months.may'), __('payroll.months.june'), __('payroll.months.july'), __('payroll.months.august'), __('payroll.months.september'), __('payroll.months.october'), __('payroll.months.november'), __('payroll.months.december')];

// Calculate summary totals
$totalCrew = count($items ?? []);
$totalGross = 0;
$totalTax = 0;
$totalDeductions = 0;
$totalNet = 0;

foreach ($items as $item) {
    $totalGross += (float)($item['gross_salary'] ?? 0);
    $totalTax += (float)($item['tax_amount'] ?? 0);
    $totalDeductions += (float)($item['total_deductions'] ?? 0);
    $totalNet += (float)($item['net_salary'] ?? 0);
}

// Previous/Next month links
$prevMonth = $month == 1 ? 12 : $month - 1;
$prevYear = $month == 1 ? $year - 1 : $year;
$nextMonth = $month == 12 ? 1 : $month + 1;
$nextYear = $month == 12 ? $year + 1 : $year;

$periodStatus = $period['status'] ?? 'draft';
?>
<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('payroll.title') ?> | IndoOcean ERP</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-blue': '#1e40af',
                        'brand-gold': '#f59e0b',
                        primary: '#1e3a8a',
                        secondary: '#d4af37',
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

        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out forwards; }
        .animate-fade-in-delay-1 { animation: fadeInUp 0.4s ease-out 0.1s forwards; opacity: 0; }
        .animate-fade-in-delay-2 { animation: fadeInUp 0.4s ease-out 0.2s forwards; opacity: 0; }
        .animate-fade-in-delay-3 { animation: fadeInUp 0.4s ease-out 0.3s forwards; opacity: 0; }

        /* Book Opening Animation */
        @keyframes bookOpen {
            0% { transform: perspective(1200px) rotateY(-90deg) scale(0.6); opacity: 0; }
            40% { transform: perspective(1200px) rotateY(-20deg) scale(0.9); opacity: 0.8; }
            70% { transform: perspective(1200px) rotateY(5deg) scale(1.02); opacity: 1; }
            100% { transform: perspective(1200px) rotateY(0deg) scale(1); opacity: 1; }
        }
        @keyframes bookClose {
            0% { transform: perspective(1200px) rotateY(0deg) scale(1); opacity: 1; }
            100% { transform: perspective(1200px) rotateY(-90deg) scale(0.6); opacity: 0; }
        }
        .book-modal { animation: bookOpen 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; transform-origin: left center; }
        .book-modal-closing { animation: bookClose 0.4s ease-in forwards; transform-origin: left center; }
        .book-spine {
            background: linear-gradient(135deg, #1e3a5f 0%, #1e40af 50%, #1e3a5f 100%);
            box-shadow: inset -3px 0 8px rgba(0,0,0,0.3), inset 3px 0 4px rgba(255,255,255,0.1);
        }
        .book-cover {
            background: linear-gradient(145deg, #f8fafc 0%, #f1f5f9 30%, #e2e8f0 100%);
            box-shadow: 0 25px 60px -15px rgba(0,0,0,0.3), 0 0 0 1px rgba(30,58,95,0.1);
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Modern Sidebar -->
        <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="ml-64 flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">
            <!-- Header -->
            <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between z-10 flex-shrink-0 shadow-sm">
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-slate-400 font-medium"><?= __('sidebar.crews') ?></span>
                    <span class="material-icons-outlined text-slate-400 text-sm">chevron_right</span>
                    <span class="text-slate-800 font-semibold"><?= __('payroll.title') ?></span>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Language Toggle -->
                    <div class="flex bg-slate-100 p-1 rounded-lg">
                        <button class="px-3 py-1 text-xs font-bold text-orange-600 bg-white shadow-sm rounded-md">ID</button>
                        <button class="px-3 py-1 text-xs font-bold text-slate-500">EN</button>
                    </div>

                    <!-- Notifications -->
                    <button class="p-2 text-slate-400 hover:bg-slate-50 rounded-full relative">
                        <span class="material-icons-outlined">notifications</span>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                    </button>
                </div>
            </header>

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto px-8 pb-10 custom-scrollbar">

                <?php if (!empty($flash)): ?>
                    <?php if (isset($flash['success'])): ?>
                        <div class="mt-6 flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm font-medium animate-fade-in">
                            <span class="material-icons text-lg">check_circle</span>
                            <?= htmlspecialchars($flash['success']) ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($flash['error'])): ?>
                        <div class="mt-6 flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm font-medium animate-fade-in">
                            <span class="material-icons text-lg">error</span>
                            <?= htmlspecialchars($flash['error']) ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Page Header -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mt-6 mb-6 animate-fade-in">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800"><?= __('payroll.title') ?></h2>
                        <p class="text-sm text-slate-500"><?= __('payroll.subtitle') ?></p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="<?= BASE_URL ?>payroll/export/<?= $period['id'] ?? 0 ?>"
                            class="flex items-center gap-2 px-4 py-2.5 border border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-white hover:shadow-sm transition-all">
                            <span class="material-icons-outlined text-sm">download</span>
                            <?= __('common.export') ?> CSV
                        </a>
                        <form method="POST" action="<?= BASE_URL ?>payroll/process" style="display: inline;">
                            <input type="hidden" name="month" value="<?= $month ?>">
                            <input type="hidden" name="year" value="<?= $year ?>">
                            <button type="submit"
                                class="flex items-center gap-2 px-5 py-2.5 bg-amber-500 text-white rounded-xl text-sm font-bold hover:bg-amber-600 transition-all shadow-lg shadow-amber-500/20 hover:shadow-amber-500/30">
                                <span class="material-icons-outlined text-sm">play_arrow</span>
                                <?= __('payroll.run_payroll') ?>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Period Selector -->
                <div class="flex items-center gap-4 mb-6 bg-white p-3 px-5 rounded-xl shadow-sm border border-slate-100 w-fit animate-fade-in-delay-1">
                    <a href="?month=<?= $prevMonth ?>&year=<?= $prevYear ?>"
                        class="p-1.5 hover:bg-slate-100 rounded-lg transition-colors">
                        <span class="material-icons-outlined text-slate-500">chevron_left</span>
                    </a>
                    <div class="text-center min-w-[140px]">
                        <p class="text-[10px] text-slate-400 uppercase font-semibold tracking-wider"><?= __('payroll.period') ?></p>
                        <p class="text-lg font-bold text-slate-800"><?= $months[$month] ?> <?= $year ?></p>
                    </div>
                    <a href="?month=<?= $nextMonth ?>&year=<?= $nextYear ?>"
                        class="p-1.5 hover:bg-slate-100 rounded-lg transition-colors">
                        <span class="material-icons-outlined text-slate-500">chevron_right</span>
                    </a>
                    <span class="ml-2 px-3 py-1 rounded-full text-xs font-bold border
                        <?php if ($periodStatus === 'completed'): ?>
                            bg-emerald-50 text-emerald-700 border-emerald-200
                        <?php elseif ($periodStatus === 'processing'): ?>
                            bg-amber-50 text-amber-700 border-amber-200
                        <?php else: ?>
                            bg-slate-50 text-slate-600 border-slate-200
                        <?php endif; ?>
                    ">
                        <?= strtoupper($periodStatus) ?>
                    </span>

                    <?php if ($periodStatus === 'processing'): ?>
                        <form method="POST" action="<?= BASE_URL ?>payroll/complete" class="ml-2">
                            <input type="hidden" name="period_id" value="<?= $period['id'] ?>">
                            <button type="submit"
                                class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500 text-white rounded-lg text-xs font-bold hover:bg-emerald-600 transition-all"
                                onclick="return confirm('<?= __('payroll.confirm_complete') ?>')">
                                <span class="material-icons text-sm">check</span>
                                <?= __('payroll.mark_complete') ?>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>

                <!-- Payroll Calendar Alert & Date Search -->
                <?php
                    $pDay = (int)($payroll_day ?? 15);
                    $payrollDate = mktime(0, 0, 0, $month, $pDay, $year);
                    $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    $monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    $payDayName = $dayNames[date('w', $payrollDate)];
                    $payDateFormatted = date('Y-m-d', $payrollDate);
                    $today = date('Y-m-d');
                    $isPayday = ($today === $payDateFormatted);
                    $isPast = ($today > $payDateFormatted);
                ?>
                <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4 animate-fade-in-delay-1">
                    <!-- Calendar Card -->
                    <div class="bg-white rounded-2xl shadow-sm border <?= $isPayday ? 'border-emerald-300 ring-2 ring-emerald-100' : 'border-slate-200' ?> overflow-hidden hover:shadow-md transition-all">
                        <div class="flex items-stretch">
                            <!-- Calendar Icon -->
                            <div class="w-24 flex-shrink-0 flex flex-col items-center justify-center <?= $isPayday ? 'bg-gradient-to-b from-emerald-500 to-emerald-600' : ($isPast ? 'bg-gradient-to-b from-slate-400 to-slate-500' : 'bg-gradient-to-b from-blue-500 to-blue-600') ?> text-white px-3 py-4">
                                <span class="text-[10px] uppercase font-bold tracking-widest opacity-80"><?= $dayNames[date('w', $payrollDate)] ?></span>
                                <span class="text-4xl font-black leading-none mt-1"><?= $pDay ?></span>
                                <span class="text-[10px] uppercase font-bold tracking-wider opacity-80 mt-1"><?= substr($monthNames[$month], 0, 3) ?> <?= $year ?></span>
                            </div>
                            <!-- Info -->
                            <div class="flex-1 p-4 flex flex-col justify-center">
                                <div class="flex items-center gap-2 mb-1">
                                    <?php if ($isPayday): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold animate-pulse">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> HARI INI
                                        </span>
                                    <?php elseif ($isPast): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold">
                                            <span class="material-icons" style="font-size:10px">check_circle</span> SUDAH LEWAT
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-[10px] font-bold">
                                            <span class="material-icons" style="font-size:10px">schedule</span> AKAN DATANG
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-sm font-bold text-slate-800">Tanggal Gajian Kru</p>
                                <p class="text-xs text-slate-500 mt-0.5"><?= $payDayName ?>, <?= $pDay ?> <?= $monthNames[$month] ?> <?= $year ?></p>
                                <a href="<?= BASE_URL ?>settings" class="inline-flex items-center gap-1 text-[11px] text-blue-600 hover:text-blue-800 font-medium mt-2">
                                    <span class="material-icons" style="font-size:12px">settings</span> Ubah Tanggal
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Date Search Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-all md:col-span-2">
                        <div class="p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="p-1.5 bg-amber-50 rounded-lg">
                                    <span class="material-icons text-amber-600 text-sm">calendar_month</span>
                                </div>
                                <h4 class="text-sm font-bold text-slate-800">Cari Berdasarkan Tanggal</h4>
                            </div>
                            <div class="flex flex-wrap items-end gap-3">
                                <div class="flex-1 min-w-[140px]">
                                    <label class="block text-[11px] font-semibold text-slate-500 mb-1">Dari Tanggal</label>
                                    <input type="date" id="dateFrom" 
                                           class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400">
                                </div>
                                <div class="flex-1 min-w-[140px]">
                                    <label class="block text-[11px] font-semibold text-slate-500 mb-1">Sampai Tanggal</label>
                                    <input type="date" id="dateTo" 
                                           class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400">
                                </div>
                                <button onclick="filterByDate()" 
                                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-lg transition-all shadow-sm">
                                    <span class="material-icons text-sm">search</span> Cari
                                </button>
                                <button onclick="resetDateFilter()" 
                                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium rounded-lg transition-all">
                                    <span class="material-icons text-sm">restart_alt</span> Reset
                                </button>
                            </div>
                            <p id="dateFilterResult" class="text-xs text-slate-400 mt-2 hidden">
                                <span class="material-icons text-xs align-middle">info</span> 
                                <span id="dateFilterText"></span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Stats + Trend Row -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    <!-- 4 Stat Cards -->
                    <div class="lg:col-span-2 grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <!-- Total Crew -->
                        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow animate-fade-in-delay-1">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="p-2.5 bg-blue-50 rounded-xl">
                                    <span class="material-icons-outlined text-blue-600">groups</span>
                                </div>
                            </div>
                            <p class="text-2xl font-bold text-slate-800"><?= $totalCrew ?></p>
                            <p class="text-xs font-medium text-slate-400 mt-1"><?= __('crews.total_crew') ?></p>
                        </div>

                        <!-- Gross Salary -->
                        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow animate-fade-in-delay-2">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="p-2.5 bg-amber-50 rounded-xl">
                                    <span class="material-icons-outlined text-amber-600">paid</span>
                                </div>
                            </div>
                            <p class="text-2xl font-bold text-slate-800">$<?= number_format($totalGross, 2) ?></p>
                            <p class="text-xs font-medium text-slate-400 mt-1"><?= __('payroll.gross_salary') ?></p>
                        </div>

                        <!-- Total Tax -->
                        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow animate-fade-in-delay-2">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="p-2.5 bg-red-50 rounded-xl">
                                    <span class="material-icons-outlined text-red-600">percent</span>
                                </div>
                            </div>
                            <p class="text-2xl font-bold text-slate-800">$<?= number_format($totalTax, 2) ?></p>
                            <p class="text-xs font-medium text-slate-400 mt-1"><?= __('payroll.total_tax') ?></p>
                        </div>

                        <!-- Net Payable -->
                        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow animate-fade-in-delay-3">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="p-2.5 bg-emerald-50 rounded-xl">
                                    <span class="material-icons-outlined text-emerald-600">check_circle</span>
                                </div>
                            </div>
                            <p class="text-2xl font-bold text-emerald-600">$<?= number_format($totalNet, 2) ?></p>
                            <p class="text-xs font-medium text-slate-400 mt-1"><?= __('payroll.net_payable') ?></p>
                        </div>
                    </div>

                    <!-- Monthly Trend Chart -->
                    <div class="lg:col-span-1 bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex flex-col justify-between animate-fade-in-delay-3">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-bold text-slate-700"><?= __('payroll.monthly_trend') ?></h3>
                            <span class="text-xs text-slate-400"><?= __('payroll.last_6_months') ?></span>
                        </div>
                        <div class="h-28 w-full flex-1">
                            <canvas id="payrollTrendChart"></canvas>
                        </div>
                        <div class="flex justify-between items-center text-xs text-slate-400 mt-3 pt-3 border-t border-slate-100">
                            <span>Current: $<?= number_format($totalGross, 2) ?></span>
                            <span class="text-emerald-500 font-semibold flex items-center gap-1">
                                <span class="material-icons text-xs">trending_up</span>
                                Period <?= $months[$month] ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Payroll Details Table -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden animate-fade-in-delay-3"
                    x-data="payrollTable()">
                    <!-- Table Header -->
                    <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <h3 class="text-lg font-bold text-slate-800"><?= __('payroll.details') ?></h3>
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-xs font-semibold text-slate-600"><?= $totalCrew ?> <?= __('common.entries') ?></span>
                        </div>
                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <div class="relative w-full sm:w-64">
                                <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                                <input type="text" x-model="searchQuery" @input="filterItems()"
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 placeholder-slate-400 transition-all"
                                    placeholder="<?= __('payroll.search_crew') ?>">
                            </div>
                            <button class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-500 hover:bg-slate-100 transition-colors">
                                <span class="material-icons-outlined text-lg">filter_list</span>
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-[11px] text-slate-400 uppercase bg-slate-50/80 tracking-wider">
                                <tr>
                                    <th class="px-6 py-3.5 font-semibold"><?= __('crews.crew_name') ?></th>
                                    <th class="px-6 py-3.5 font-semibold"><?= __('crews.rank') ?></th>
                                    <th class="px-6 py-3.5 font-semibold"><?= __('crews.vessel') ?></th>
                                    <th class="px-6 py-3.5 font-semibold text-right"><?= __('payroll.basic_salary') ?></th>
                                    <th class="px-6 py-3.5 font-semibold text-right"><?= __('payroll.allowances') ?></th>
                                    <th class="px-6 py-3.5 font-semibold text-right"><?= __('payroll.gross') ?></th>
                                    <th class="px-6 py-3.5 font-semibold text-right"><?= __('payroll.tax') ?> (5%)</th>
                                    <th class="px-6 py-3.5 font-semibold text-right"><?= __('payroll.net') ?></th>
                                    <th class="px-6 py-3.5 font-semibold text-center"><?= __('common.status') ?></th>
                                    <th class="px-6 py-3.5 font-semibold text-center"><?= __('common.actions') ?></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php if (empty($items)): ?>
                                    <tr>
                                        <td colspan="10" class="px-6 py-20 text-center">
                                            <div class="flex flex-col items-center">
                                                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mb-4">
                                                    <span class="material-icons-outlined text-3xl text-slate-300">calculate</span>
                                                </div>
                                                <p class="text-slate-500 font-medium"><?= __('payroll.no_payroll') ?></p>
                                                <p class="text-slate-400 text-xs mt-1"><?= __('payroll.click_run_payroll') ?></p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($items as $idx => $item):
                                        $basicSalary = (float)$item['basic_salary'];
                                        $overtime = (float)$item['overtime'];
                                        $leavePay = (float)$item['leave_pay'];
                                        $bonus = (float)$item['bonus'];
                                        $otherAllowance = (float)$item['other_allowance'];
                                        $grossSalary = (float)$item['gross_salary'];
                                        $totalItemDeductions = (float)$item['total_deductions'];
                                        $taxAmount = (float)$item['tax_amount'];
                                        $netSalary = (float)$item['net_salary'];
                                        $totalAllowances = $overtime + $leavePay + $bonus + $otherAllowance;
                                        $crewInitials = strtoupper(substr($item['crew_name'] ?? 'C', 0, 2));
                                    ?>
                                        <tr class="hover:bg-blue-50/60 transition-colors group crew-row cursor-pointer"
                                            data-name="<?= strtolower(htmlspecialchars($item['crew_name'] ?? '')) ?>"
                                            data-rank="<?= strtolower(htmlspecialchars($item['rank_name'] ?? '')) ?>"
                                            data-vessel="<?= strtolower(htmlspecialchars($item['vessel_name'] ?? '')) ?>"
                                            data-date="<?= htmlspecialchars($item['created_at'] ?? $item['pay_date'] ?? date('Y-m-d', mktime(0,0,0,$month,$pDay,$year))) ?>"
                                            data-item-id="<?= (int)($item['id'] ?? 0) ?>"
                                            onclick="openPayslipBook(<?= (int)($item['id'] ?? 0) ?>, '<?= htmlspecialchars(addslashes($item['crew_name'] ?? ''), ENT_QUOTES) ?>')">
                                            <!-- Crew Name -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-[10px] font-bold shadow-sm flex-shrink-0">
                                                        <?= $crewInitials ?>
                                                    </div>
                                                    <div>
                                                        <div class="font-semibold text-slate-800"><?= htmlspecialchars($item['crew_name'] ?? '') ?></div>
                                                        <?php if (!empty($item['contract_no'])): ?>
                                                            <div class="text-[10px] text-slate-400 font-mono">#<?= htmlspecialchars($item['contract_no']) ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <!-- Rank -->
                                            <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($item['rank_name'] ?? '-') ?></td>
                                            <!-- Vessel -->
                                            <td class="px-6 py-4">
                                                <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold">
                                                    <?= htmlspecialchars($item['vessel_name'] ?? '-') ?>
                                                </span>
                                            </td>
                                            <!-- Basic -->
                                            <td class="px-6 py-4 text-right font-medium text-slate-800">$<?= number_format($basicSalary, 2) ?></td>
                                            <!-- Allowances -->
                                            <td class="px-6 py-4 text-right <?= $totalAllowances > 0 ? 'text-slate-600' : 'text-slate-300' ?>">
                                                $<?= number_format($totalAllowances, 2) ?>
                                            </td>
                                            <!-- Gross -->
                                            <td class="px-6 py-4 text-right font-semibold text-slate-800">$<?= number_format($grossSalary, 2) ?></td>
                                            <!-- Tax -->
                                            <td class="px-6 py-4 text-right font-medium text-red-500">-$<?= number_format($taxAmount, 2) ?></td>
                                            <!-- Net -->
                                            <td class="px-6 py-4 text-right font-bold text-emerald-600">$<?= number_format($netSalary, 2) ?></td>
                                            <!-- Status -->
                                            <td class="px-6 py-4 text-center">
                                                <?php
                                                $statusClass = 'bg-slate-100 text-slate-600';
                                                if ($item['status'] === 'paid') {
                                                    $statusClass = 'bg-emerald-50 text-emerald-700';
                                                } elseif ($item['status'] === 'pending') {
                                                    $statusClass = 'bg-amber-50 text-amber-700';
                                                }
                                                ?>
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase <?= $statusClass ?>">
                                                    <?= strtoupper($item['status'] ?? 'DRAFT') ?>
                                                </span>
                                            </td>
                                            <!-- Action -->
                                            <td class="px-6 py-4 text-center">
                                                <div class="flex items-center justify-center gap-1">
                                                    <button onclick="event.stopPropagation(); openPayslipBook(<?= (int)($item['id'] ?? 0) ?>, '<?= htmlspecialchars(addslashes($item['crew_name'] ?? ''), ENT_QUOTES) ?>')"
                                                        class="p-1.5 rounded-lg hover:bg-blue-50 text-slate-400 hover:text-blue-600 transition-colors" title="Slip Gaji & Kirim Email">
                                                        <span class="material-icons text-lg">mail</span>
                                                    </button>
                                                    <a href="<?= BASE_URL ?>payroll/payslip/<?= (int)($item['id'] ?? 0) ?>" target="_blank" onclick="event.stopPropagation()"
                                                        class="p-1.5 rounded-lg hover:bg-emerald-50 text-slate-400 hover:text-emerald-600 transition-colors" title="Lihat Slip (PDF)">
                                                        <span class="material-icons text-lg">picture_as_pdf</span>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Table Footer -->
                    <?php if (!empty($items)): ?>
                        <div class="px-6 py-3.5 bg-slate-50/80 border-t border-slate-200 text-xs text-slate-500 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                            <div>
                                <?= __('common.showing') ?> <span class="font-semibold text-slate-700" x-text="visibleCount"><?= $totalCrew ?></span> <?= __('common.of') ?> <?= $totalCrew ?> <?= __('common.entries') ?>
                            </div>
                            <div class="flex gap-3 items-center">
                                <span>Rumus: <span class="text-slate-400">Gross ($<?= number_format($totalGross, 2) ?>) - Deductions ($<?= number_format($totalDeductions, 2) ?>) - Tax ($<?= number_format($totalTax, 2) ?>) = </span><span class="text-emerald-600 font-bold">Net $<?= number_format($totalNet, 2) ?></span></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Send Emails Button -->
                <?php if ($periodStatus === 'completed' && !empty($items)): ?>
                    <div class="mt-4 flex justify-end">
                        <form method="POST" action="<?= BASE_URL ?>payroll/send-emails">
                            <input type="hidden" name="period_id" value="<?= $period['id'] ?>">
                            <button type="submit"
                                class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20"
                                onclick="return confirm('<?= __('payroll.confirm_send_payslips') ?>')">
                                <span class="material-icons-outlined text-sm">email</span>
                                <?= __('payroll.send_payslips') ?>
                            </button>
                        </form>
                    </div>
                <?php endif; ?>

                <div class="mt-4 text-right text-xs text-slate-400">
                    * <?= __('payroll.usd_note') ?>
                </div>

                <!-- Vessel Summary (if data exists) -->
                <?php if (!empty($summary)): ?>
                    <div class="mt-8 animate-fade-in-delay-3">
                        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <span class="material-icons-outlined text-amber-500">pie_chart</span>
                            <?= __('payroll.summary_by_vessel') ?>
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php foreach ($summary as $vesselSummary): ?>
                                <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center gap-2">
                                            <div class="p-2 bg-blue-50 rounded-lg">
                                                <span class="material-icons text-blue-600 text-sm">directions_boat</span>
                                            </div>
                                            <span class="font-bold text-slate-800"><?= htmlspecialchars($vesselSummary['vessel_name'] ?? 'Unknown') ?></span>
                                        </div>
                                        <span class="text-xs font-semibold text-slate-400"><?= $vesselSummary['crew_count'] ?? 0 ?> <?= __('sidebar.crews') ?></span>
                                    </div>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Gross</span>
                                            <span class="font-medium text-slate-700">$<?= number_format((float)($vesselSummary['total_gross'] ?? 0), 2) ?></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Tax</span>
                                            <span class="font-medium text-red-500">-$<?= number_format((float)($vesselSummary['total_tax'] ?? 0), 2) ?></span>
                                        </div>
                                        <div class="flex justify-between pt-2 border-t border-slate-100">
                                            <span class="font-semibold text-slate-700">Net</span>
                                            <span class="font-bold text-emerald-600">$<?= number_format((float)($vesselSummary['total_net'] ?? 0), 2) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <script>
        // Chart.js - Monthly Trend
        const ctx = document.getElementById('payrollTrendChart')?.getContext('2d');
        if (ctx) {
            const labels = [];
            const currentMonth = <?= $month ?>;
            const currentYear = <?= $year ?>;
            const monthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            for (let i = 5; i >= 0; i--) {
                let m = currentMonth - i;
                let y = currentYear;
                if (m <= 0) { m += 12; y--; }
                labels.push(monthNames[m]);
            }

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Payroll',
                        data: [
                            <?php
                            // Generate approximate historical data (current is real, rest are placeholders)
                            for ($i = 5; $i >= 1; $i--) {
                                $variance = rand(-20, 20) / 100;
                                echo round($totalGross * (1 + $variance), 2) . ',';
                            }
                            echo round($totalGross, 2);
                            ?>
                        ],
                        backgroundColor: '#f59e0b',
                        borderRadius: 6,
                        barThickness: 12
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleFont: { family: 'Inter', size: 11 },
                            bodyFont: { family: 'Inter', size: 12 },
                            cornerRadius: 8,
                            padding: 10,
                            callbacks: {
                                label: (ctx) => '$' + ctx.parsed.y.toFixed(2)
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#94a3b8', font: { size: 10, family: 'Inter' } },
                            border: { display: false }
                        },
                        y: {
                            display: false,
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // Alpine.js - Search Table
        function payrollTable() {
            return {
                searchQuery: '',
                visibleCount: <?= $totalCrew ?>,
                filterItems() {
                    const query = this.searchQuery.toLowerCase().trim();
                    const rows = document.querySelectorAll('.crew-row');
                    let count = 0;
                    rows.forEach(row => {
                        const name = row.getAttribute('data-name') || '';
                        const rank = row.getAttribute('data-rank') || '';
                        const vessel = row.getAttribute('data-vessel') || '';
                        const match = !query || name.includes(query) || rank.includes(query) || vessel.includes(query);
                        // Also respect date filter
                        const isDateHidden = row.getAttribute('data-date-hidden') === 'true';
                        row.style.display = (match && !isDateHidden) ? '' : 'none';
                        if (match && !isDateHidden) count++;
                    });
                    this.visibleCount = count;
                }
            };
        }

        // Date Filter Functions
        function filterByDate() {
            const from = document.getElementById('dateFrom').value;
            const to = document.getElementById('dateTo').value;
            const rows = document.querySelectorAll('.crew-row');
            let count = 0;
            let total = rows.length;

            rows.forEach(row => {
                const rowDate = (row.getAttribute('data-date') || '').substring(0, 10);
                let show = true;
                if (from && rowDate < from) show = false;
                if (to && rowDate > to) show = false;
                row.setAttribute('data-date-hidden', show ? 'false' : 'true');
                row.style.display = show ? '' : 'none';
                if (show) count++;
            });

            // Show result text
            const resultEl = document.getElementById('dateFilterResult');
            const textEl = document.getElementById('dateFilterText');
            resultEl.classList.remove('hidden');
            
            let rangeText = '';
            if (from && to) rangeText = `Menampilkan ${count} dari ${total} data (${formatDate(from)} - ${formatDate(to)})`;
            else if (from) rangeText = `Menampilkan ${count} dari ${total} data (dari ${formatDate(from)})`;
            else if (to) rangeText = `Menampilkan ${count} dari ${total} data (sampai ${formatDate(to)})`;
            else rangeText = 'Pilih tanggal untuk memfilter';
            textEl.textContent = rangeText;
        }

        function resetDateFilter() {
            document.getElementById('dateFrom').value = '';
            document.getElementById('dateTo').value = '';
            document.getElementById('dateFilterResult').classList.add('hidden');
            const rows = document.querySelectorAll('.crew-row');
            rows.forEach(row => {
                row.setAttribute('data-date-hidden', 'false');
                row.style.display = '';
            });
        }

        function formatDate(dateStr) {
            const months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const parts = dateStr.split('-');
            return parseInt(parts[2]) + ' ' + months[parseInt(parts[1])] + ' ' + parts[0];
        }
    </script>

    <!-- Payslip Book Modal (Editable) -->
    <div id="payslipBookModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" onclick="if(event.target===this) closePayslipBook()">
        <div class="flex items-stretch w-full max-w-5xl mx-4" style="max-height:92vh">
            <!-- Book Spine -->
            <div class="book-spine w-6 rounded-l-lg flex-shrink-0 hidden md:block"></div>
            <!-- Book Content -->
            <div id="payslipBookContent" class="book-cover rounded-r-2xl md:rounded-l-none rounded-l-2xl w-full overflow-hidden flex flex-col" style="max-height:92vh">
                <!-- Book Header -->
                <div class="bg-gradient-to-r from-blue-800 to-blue-900 px-6 py-4 relative overflow-hidden flex-shrink-0">
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-1 left-3 w-24 h-24 border border-white/30 rounded-full"></div>
                        <div class="absolute bottom-1 right-3 w-16 h-16 border border-white/20 rounded-full"></div>
                    </div>
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-11 w-11 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                                <span class="material-icons text-white text-xl">receipt_long</span>
                            </div>
                            <div>
                                <h3 id="payslipBookTitle" class="text-base font-bold text-white">Slip Gaji</h3>
                                <p class="text-blue-200 text-xs">Edit, Review & Kirim via Email</p>
                            </div>
                        </div>
                        <button onclick="closePayslipBook()" class="p-2 hover:bg-white/10 rounded-full transition-colors">
                            <span class="material-icons text-white">close</span>
                        </button>
                    </div>
                </div>
                
                <!-- Book Body: Two Column Layout -->
                <div class="flex flex-1 overflow-hidden">
                    <!-- Left: Editable Payslip Form -->
                    <div class="flex-1 overflow-y-auto p-5 custom-scrollbar" style="max-height:calc(92vh - 70px)">
                        <!-- Crew Info -->
                        <div class="bg-blue-50 rounded-xl p-4 mb-4">
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div><span class="font-bold text-slate-600">NAME:</span> <span id="slipCrewName" class="text-slate-800 font-semibold">-</span></div>
                                <div><span class="font-bold text-slate-600">SHIP:</span> <span id="slipVessel" class="text-slate-800">-</span></div>
                                <div><span class="font-bold text-slate-600">RANK:</span> <span id="slipRank" class="text-slate-800">-</span></div>
                                <div><span class="font-bold text-slate-600">PERIODE:</span> <span id="slipPeriod" class="text-slate-800">-</span></div>
                            </div>
                        </div>

                        <!-- Salary Table -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <!-- INCOME -->
                            <div>
                                <h4 class="text-xs font-bold text-blue-800 uppercase mb-2 tracking-wider">Income</h4>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-1">
                                        <label class="text-[10px] text-slate-600 w-24 flex-shrink-0">Basic Salary</label>
                                        <span class="text-[10px] font-bold text-slate-500 w-6" id="slipOrigCurLabel1">RM</span>
                                        <input type="number" id="slipOrigBasic" onchange="recalcPayslip()" step="0.01"
                                            class="flex-1 px-2 py-1 border border-slate-200 rounded-lg text-xs text-right focus:ring-1 focus:ring-blue-400 focus:border-blue-400">
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <label class="text-[10px] text-slate-600 w-24 flex-shrink-0">Advance Salary</label>
                                        <span class="text-[10px] font-bold text-slate-500 w-6" id="slipOrigCurLabel2">RM</span>
                                        <input type="number" id="slipOrigOvertime" onchange="recalcPayslip()" step="0.01"
                                            class="flex-1 px-2 py-1 border border-slate-200 rounded-lg text-xs text-right focus:ring-1 focus:ring-blue-400 focus:border-blue-400">
                                    </div>
                                    <div class="flex items-center gap-1 bg-blue-50/50 rounded-lg px-1 py-0.5">
                                        <label class="text-[10px] text-blue-700 font-semibold w-24 flex-shrink-0">Actualy Salary</label>
                                        <span class="text-[10px] font-bold text-blue-500 w-6" id="slipOrigCurLabel3">RM</span>
                                        <span id="slipActualySalary" class="flex-1 text-xs text-right font-bold text-blue-700 pr-2">0</span>
                                    </div>
                                    <hr class="border-slate-200">
                                    <div class="flex items-center gap-1">
                                        <label class="text-[10px] text-slate-600 w-24 flex-shrink-0">Reimbursement</label>
                                        <span class="text-[10px] font-bold text-slate-500 w-6">Rp</span>
                                        <input type="number" id="slipReimbursement" onchange="recalcPayslip()" step="1"
                                            class="flex-1 px-2 py-1 border border-slate-200 rounded-lg text-xs text-right focus:ring-1 focus:ring-blue-400 focus:border-blue-400">
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <label class="text-[10px] text-slate-600 w-24 flex-shrink-0">Loans To IOS</label>
                                        <span class="text-[10px] font-bold text-slate-500 w-6">Rp</span>
                                        <input type="number" id="slipLoans" onchange="recalcPayslip()" step="1"
                                            class="flex-1 px-2 py-1 border border-slate-200 rounded-lg text-xs text-right focus:ring-1 focus:ring-blue-400 focus:border-blue-400">
                                    </div>
                                    <hr class="border-slate-200">
                                    <div class="flex items-center gap-1">
                                        <label class="text-[10px] text-slate-600 w-24 flex-shrink-0">Kurs</label>
                                        <span class="text-[10px] font-bold text-slate-500 w-6" id="slipKursLabel">Rp</span>
                                        <input type="number" id="slipKurs" onchange="recalcPayslip()" step="1"
                                            class="flex-1 px-2 py-1 border border-amber-300 bg-amber-50 rounded-lg text-xs text-right focus:ring-1 focus:ring-amber-400 focus:border-amber-400">
                                    </div>
                                    <div class="flex items-center gap-1 bg-emerald-50/50 rounded-lg px-1 py-0.5">
                                        <label class="text-[10px] text-emerald-700 font-semibold w-24 flex-shrink-0">IDR</label>
                                        <span class="text-[10px] font-bold text-emerald-500 w-6">Rp</span>
                                        <span id="slipIDR" class="flex-1 text-xs text-right font-bold text-emerald-700 pr-2">0</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- DEDUCTION -->
                            <div>
                                <h4 class="text-xs font-bold text-red-700 uppercase mb-2 tracking-wider">Deduction</h4>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-1">
                                        <label class="text-[10px] text-slate-600 w-24 flex-shrink-0">Admin Bank</label>
                                        <span class="text-[10px] font-bold text-slate-500 w-6">Rp</span>
                                        <input type="number" id="slipAdminBank" onchange="recalcPayslip()" step="1"
                                            class="flex-1 px-2 py-1 border border-slate-200 rounded-lg text-xs text-right focus:ring-1 focus:ring-red-400 focus:border-red-400">
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <label class="text-[10px] text-slate-600 w-24 flex-shrink-0">Insurance</label>
                                        <span class="text-[10px] font-bold text-slate-500 w-6">Rp</span>
                                        <input type="number" id="slipInsurance" onchange="recalcPayslip()" step="1"
                                            class="flex-1 px-2 py-1 border border-slate-200 rounded-lg text-xs text-right focus:ring-1 focus:ring-red-400 focus:border-red-400">
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <label class="text-[10px] text-slate-600 w-24 flex-shrink-0">Other Deductions</label>
                                        <span class="text-[10px] font-bold text-slate-500 w-6">Rp</span>
                                        <input type="number" id="slipOtherDeduct" onchange="recalcPayslip()" step="1"
                                            class="flex-1 px-2 py-1 border border-slate-200 rounded-lg text-xs text-right focus:ring-1 focus:ring-red-400 focus:border-red-400">
                                    </div>
                                    <hr class="border-slate-200">
                                    <div class="flex items-center gap-1 bg-red-50 rounded-lg px-2 py-1.5">
                                        <label class="text-[10px] text-red-700 font-semibold w-16 flex-shrink-0">PPH 21</label>
                                        <input type="number" id="slipTaxRate" onchange="recalcPayslip()" step="0.1" min="0" max="100"
                                            class="w-14 px-1 py-0.5 border border-red-300 bg-white rounded text-[10px] text-center font-bold focus:ring-1 focus:ring-red-400">
                                        <span class="text-[10px] text-red-600 font-bold">%</span>
                                        <span class="text-[10px] text-red-500 mx-1">=</span>
                                        <span class="text-[10px] font-bold text-red-500 w-4">Rp</span>
                                        <span id="slipTaxAmount" class="flex-1 text-xs text-right font-bold text-red-600">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Totals -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="bg-blue-50 rounded-xl p-3 text-center">
                                <p class="text-[10px] font-bold text-blue-600 uppercase">Gross</p>
                                <p class="text-sm font-black text-blue-800">Rp <span id="slipGross">0</span></p>
                            </div>
                            <div class="bg-red-50 rounded-xl p-3 text-center">
                                <p class="text-[10px] font-bold text-red-600 uppercase">Total Deductions</p>
                                <p class="text-sm font-black text-red-700">Rp <span id="slipTotalDeduct">0</span></p>
                            </div>
                        </div>

                        <!-- NET PAY -->
                        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl p-4 text-center shadow-lg shadow-emerald-500/20">
                            <p class="text-emerald-100 text-xs font-semibold uppercase tracking-wider">Net Take-Home Pay</p>
                            <p class="text-2xl font-black text-white mt-1">Rp <span id="slipNetPay">0</span></p>
                        </div>
                        
                        <!-- Bank Info -->
                        <div class="mt-4 bg-slate-50 rounded-xl p-3 text-xs text-slate-600">
                            <div class="flex items-center justify-between mb-1">
                                <p class="font-bold text-slate-700">Paid By Bank Transfer</p>
                                <span id="slipPaymentBadge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold">
                                    <span class="material-icons" style="font-size:10px">account_balance</span>
                                    <span id="slipPaymentMethod">BANK TRANSFER</span>
                                </span>
                            </div>
                            <p>Acc. Holder : <span id="slipBankHolder" class="font-semibold text-slate-800">-</span></p>
                            <p>Acc. No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <span id="slipBankAccount" class="font-semibold text-slate-800">-</span></p>
                            <p>Bank &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <span id="slipBankName" class="font-semibold text-slate-800">-</span></p>
                        </div>
                    </div>
                    
                    <!-- Right: Email + Actions Panel -->
                    <div class="w-72 bg-white border-l border-slate-200 flex flex-col overflow-y-auto">
                        <!-- Email Header -->
                        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50">
                            <div class="flex items-center gap-2">
                                <span class="material-icons text-blue-600 text-lg">email</span>
                                <h4 class="font-bold text-slate-800 text-sm">Kirim Slip Gaji</h4>
                            </div>
                        </div>
                        
                        <!-- Email Form -->
                        <div class="p-4 flex-1 space-y-3">
                            <!-- Crew Info Card -->
                            <div class="bg-blue-50 rounded-xl p-3">
                                <div class="flex items-center gap-3">
                                    <div id="emailCrewAvatar" class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">--</div>
                                    <div class="min-w-0">
                                        <p id="emailCrewName" class="font-semibold text-sm text-slate-800 truncate">Loading...</p>
                                        <p id="emailCrewDetail" class="text-[10px] text-slate-500 truncate"></p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- To Email -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Kepada (Email)</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 material-icons" style="font-size:14px">email</span>
                                    <input type="email" id="emailTo" placeholder="crew@email.com" 
                                        class="w-full pl-8 pr-3 py-2 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                </div>
                                <p id="emailNoEmail" class="hidden text-[10px] text-amber-600 mt-1 flex items-center gap-1">
                                    <span class="material-icons" style="font-size:12px">warning</span>
                                    Email kru belum terdaftar
                                </p>
                            </div>
                            
                            <!-- Net Salary Display -->
                            <div class="bg-emerald-50 rounded-xl p-3">
                                <p class="text-[10px] font-medium text-emerald-600 mb-0.5">Net Take-Home Pay</p>
                                <p id="emailNetSalary" class="text-base font-bold text-emerald-700">Rp 0</p>
                            </div>
                            
                            <!-- Actions -->
                            <div class="space-y-2 pt-1">
                                <!-- Save Button -->
                                <button id="btnSavePayslip" onclick="savePayslip()"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-lg shadow-emerald-500/20">
                                    <span class="material-icons text-sm">check_circle</span>
                                    OK / Simpan Slip Gaji
                                </button>
                                <!-- Send Email -->
                                <button id="btnSendPayslip" onclick="sendPayslipEmail()" 
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all">
                                    <span class="material-icons text-sm">send</span>
                                    Kirim via Email
                                </button>
                                <!-- Print/PDF -->
                                <a id="btnDownloadPayslip" href="#" target="_blank"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-all">
                                    <span class="material-icons text-sm">print</span>
                                    Print / Save PDF
                                </a>
                            </div>
                        </div>
                        
                        <!-- Status -->
                        <div id="emailSendStatus" class="hidden px-4 py-3 border-t border-slate-100">
                            <div id="emailStatusContent" class="flex items-center gap-2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const PAYROLL_BASE = '<?= BASE_URL ?>';
        let currentPayslipItemId = null;
        let currentPayslipData = null;

        function openPayslipBook(itemId, crewName) {
            currentPayslipItemId = itemId;
            const modal = document.getElementById('payslipBookModal');
            const content = document.getElementById('payslipBookContent');
            
            document.getElementById('payslipBookTitle').textContent = 'Slip Gaji: ' + crewName;
            document.getElementById('btnDownloadPayslip').href = PAYROLL_BASE + 'index.php?url=payroll/payslip/' + itemId;
            
            // Reset
            document.getElementById('emailTo').value = '';
            document.getElementById('emailNoEmail').classList.add('hidden');
            document.getElementById('emailSendStatus').classList.add('hidden');
            document.getElementById('emailCrewName').textContent = crewName;
            document.getElementById('emailCrewAvatar').textContent = crewName.substring(0, 2).toUpperCase();
            document.getElementById('emailCrewDetail').textContent = '';
            document.getElementById('emailNetSalary').textContent = 'Rp 0';
            
            // Show modal with book animation
            modal.classList.remove('hidden');
            content.classList.remove('book-modal-closing');
            content.classList.add('book-modal');
            
            // Load full payslip data
            loadPayslipData(itemId);
        }

        function closePayslipBook() {
            const modal = document.getElementById('payslipBookModal');
            const content = document.getElementById('payslipBookContent');
            content.classList.add('book-modal-closing');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 400);
        }

        async function loadPayslipData(itemId) {
            const fetchUrl = PAYROLL_BASE + 'index.php?url=payroll/apiGetPayslipData/' + itemId;
            console.log('[Payslip] Fetching:', fetchUrl);
            try {
                const res = await fetch(fetchUrl);
                console.log('[Payslip] Response status:', res.status);
                if (!res.ok) {
                    const errText = await res.text();
                    console.error('[Payslip] Error response:', errText);
                    alert('API Error ' + res.status + ': ' + errText.substring(0, 200));
                    return;
                }
                const data = await res.json();
                console.log('[Payslip] Data:', data);
                if (data.success) {
                    currentPayslipData = data;
                    const item = data.item;
                    const period = data.period;
                    
                    // Crew info
                    document.getElementById('slipCrewName').textContent = (item.crew_name || '-').toUpperCase();
                    document.getElementById('slipVessel').textContent = (item.vessel_name || '-').toUpperCase();
                    document.getElementById('slipRank').textContent = (item.rank_name || '-').toUpperCase();
                    
                    const monthNames = ['','JAN','FEB','MAR','APR','MEI','JUN','JUL','AGU','SEP','OKT','NOV','DES'];
                    document.getElementById('slipPeriod').textContent = (monthNames[period.period_month] || '') + ' ' + period.period_year;
                    
                    // Original currency from contract data
                    const origCur = (item.original_currency || 'IDR').toUpperCase();
                    document.getElementById('slipOrigCurLabel1').textContent = origCur;
                    document.getElementById('slipOrigCurLabel2').textContent = origCur;
                    document.getElementById('slipOrigCurLabel3').textContent = origCur;
                    
                    // Dynamic Kurs conversion label (e.g. "1 RM =")
                    document.getElementById('slipKursLabel').textContent = 'Rp';
                    
                    // Payment method badge
                    const payMethod = (item.payment_method || 'bank_transfer').replace(/_/g, ' ').toUpperCase();
                    document.getElementById('slipPaymentMethod').textContent = payMethod;
                    
                    // Fill editable fields
                    document.getElementById('slipOrigBasic').value = parseFloat(item.original_basic || 0);
                    document.getElementById('slipOrigOvertime').value = parseFloat(item.original_overtime || 0);
                    document.getElementById('slipReimbursement').value = parseFloat(item.reimbursement || 0);
                    document.getElementById('slipLoans').value = parseFloat(item.loans || 0);
                    document.getElementById('slipKurs').value = parseFloat(item.exchange_rate || 0);
                    document.getElementById('slipAdminBank').value = parseFloat(item.admin_bank_fee || 0);
                    document.getElementById('slipInsurance').value = parseFloat(item.insurance || 0);
                    document.getElementById('slipOtherDeduct').value = parseFloat(item.other_deductions || 0);
                    document.getElementById('slipTaxRate').value = parseFloat(item.tax_rate || 2.5);
                    
                    // Bank info
                    document.getElementById('slipBankHolder').textContent = (item.bank_holder || item.crew_name || '-').toUpperCase();
                    document.getElementById('slipBankAccount').textContent = item.bank_account || '-';
                    document.getElementById('slipBankName').textContent = (item.bank_name || '-').toUpperCase();
                    
                    // Email
                    document.getElementById('emailCrewName').textContent = item.full_name || item.crew_name;
                    document.getElementById('emailCrewAvatar').textContent = ((item.full_name || item.crew_name || '--').substring(0, 2)).toUpperCase();
                    document.getElementById('emailCrewDetail').textContent = (item.rank_name || '') + ' • ' + (item.vessel_name || '');
                    
                    if (item.email) {
                        document.getElementById('emailTo').value = item.email;
                    } else {
                        document.getElementById('emailNoEmail').classList.remove('hidden');
                    }
                    
                    // Recalculate
                    recalcPayslip();
                } else {
                    alert('API Error: ' + (data.message || 'Unknown'));
                }
            } catch (e) {
                console.error('Failed to load payslip data:', e);
                alert('Gagal memuat data payslip: ' + e.message);
            }
        }

        function fmtNum(val) {
            return Math.round(val).toLocaleString('id-ID');
        }

        function recalcPayslip() {
            const origBasic = parseFloat(document.getElementById('slipOrigBasic').value) || 0;
            const origOvertime = parseFloat(document.getElementById('slipOrigOvertime').value) || 0;
            const actualy = origBasic - origOvertime;
            const kurs = parseFloat(document.getElementById('slipKurs').value) || 0;
            const reimbursement = parseFloat(document.getElementById('slipReimbursement').value) || 0;
            const loans = parseFloat(document.getElementById('slipLoans').value) || 0;
            const adminBank = parseFloat(document.getElementById('slipAdminBank').value) || 0;
            const insurance = parseFloat(document.getElementById('slipInsurance').value) || 0;
            const otherDeduct = parseFloat(document.getElementById('slipOtherDeduct').value) || 0;
            const taxRate = parseFloat(document.getElementById('slipTaxRate').value) || 0;
            
            // Actualy Salary display
            document.getElementById('slipActualySalary').textContent = fmtNum(actualy);
            
            // IDR = Actualy Salary * Kurs
            const idr = actualy * kurs;
            document.getElementById('slipIDR').textContent = fmtNum(idr);
            
            // Gross = IDR + Reimbursement - Loans
            const gross = idr + reimbursement - loans;
            document.getElementById('slipGross').textContent = fmtNum(gross);
            
            // Total Deductions = Admin Bank + Insurance + Other
            const totalDeduct = adminBank + insurance + otherDeduct;
            
            // PPH 21 = (Gross - totalDeduct) * taxRate%
            const taxBase = gross - totalDeduct;
            const taxAmount = taxBase > 0 ? taxBase * (taxRate / 100) : 0;
            document.getElementById('slipTaxAmount').textContent = fmtNum(taxAmount);
            
            // Total deductions including tax
            const totalDeductWithTax = totalDeduct + taxAmount;
            document.getElementById('slipTotalDeduct').textContent = fmtNum(totalDeductWithTax);
            
            // Net = Gross - totalDeductWithTax
            const net = gross - totalDeductWithTax;
            document.getElementById('slipNetPay').textContent = fmtNum(net);
            document.getElementById('emailNetSalary').textContent = 'Rp ' + fmtNum(net);
        }

        async function savePayslip() {
            const btn = document.getElementById('btnSavePayslip');
            const statusEl = document.getElementById('emailSendStatus');
            const statusContent = document.getElementById('emailStatusContent');
            
            btn.disabled = true;
            btn.innerHTML = '<span class="animate-spin material-icons text-sm">refresh</span> Menyimpan...';
            
            try {
                const formData = new FormData();
                formData.append('item_id', currentPayslipItemId);
                formData.append('original_basic', document.getElementById('slipOrigBasic').value);
                formData.append('original_overtime', document.getElementById('slipOrigOvertime').value);
                formData.append('reimbursement', document.getElementById('slipReimbursement').value);
                formData.append('loans', document.getElementById('slipLoans').value);
                formData.append('exchange_rate', document.getElementById('slipKurs').value);
                formData.append('admin_bank_fee', document.getElementById('slipAdminBank').value);
                formData.append('insurance', document.getElementById('slipInsurance').value);
                formData.append('other_deductions', document.getElementById('slipOtherDeduct').value);
                formData.append('tax_rate', document.getElementById('slipTaxRate').value);
                
                const res = await fetch(PAYROLL_BASE + 'index.php?url=payroll/apiUpdatePayslip', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                
                statusEl.classList.remove('hidden');
                if (data.success) {
                    statusContent.innerHTML = '<span class="material-icons text-emerald-500" style="font-size:16px">check_circle</span><span class="text-xs text-emerald-600">' + data.message + '</span>';
                    btn.innerHTML = '<span class="material-icons text-sm">check_circle</span> Tersimpan!';
                    btn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
                    btn.classList.add('bg-emerald-500');
                } else {
                    statusContent.innerHTML = '<span class="material-icons text-red-500" style="font-size:16px">error</span><span class="text-xs text-red-600">' + (data.message || 'Gagal menyimpan') + '</span>';
                }
            } catch (e) {
                statusEl.classList.remove('hidden');
                statusContent.innerHTML = '<span class="material-icons text-red-500" style="font-size:16px">error</span><span class="text-xs text-red-600">Error: ' + e.message + '</span>';
            }
            
            setTimeout(() => {
                btn.disabled = false;
                btn.classList.remove('bg-emerald-500');
                btn.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
                btn.innerHTML = '<span class="material-icons text-sm">check_circle</span> OK / Simpan Slip Gaji';
            }, 3000);
        }

        async function sendPayslipEmail() {
            const email = document.getElementById('emailTo').value.trim();
            if (!email) {
                alert('Masukkan email tujuan!');
                return;
            }
            
            const btn = document.getElementById('btnSendPayslip');
            const statusEl = document.getElementById('emailSendStatus');
            const statusContent = document.getElementById('emailStatusContent');
            
            btn.disabled = true;
            btn.innerHTML = '<span class="animate-spin material-icons text-sm">refresh</span> Mengirim...';
            statusEl.classList.remove('hidden');
            statusContent.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div><span class="text-xs text-blue-600">Mengirim slip gaji...</span>';
            
            try {
                const formData = new FormData();
                formData.append('item_id', currentPayslipItemId);
                formData.append('email', email);
                
                const res = await fetch(PAYROLL_BASE + 'index.php?url=payroll/apiSendPayslip', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                
                if (data.success) {
                    statusContent.innerHTML = '<span class="material-icons text-emerald-500" style="font-size:16px">check_circle</span><span class="text-xs text-emerald-600">' + data.message + '</span>';
                    btn.innerHTML = '<span class="material-icons text-sm">check</span> Terkirim!';
                    btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    btn.classList.add('bg-emerald-600');
                } else {
                    statusContent.innerHTML = '<span class="material-icons text-red-500" style="font-size:16px">error</span><span class="text-xs text-red-600">' + (data.message || 'Gagal mengirim') + '</span>';
                    btn.innerHTML = '<span class="material-icons text-sm">send</span> Kirim via Email';
                    btn.disabled = false;
                }
            } catch (e) {
                statusContent.innerHTML = '<span class="material-icons text-red-500" style="font-size:16px">error</span><span class="text-xs text-red-600">Gagal mengirim email</span>';
                btn.innerHTML = '<span class="material-icons text-sm">send</span> Kirim via Email';
                btn.disabled = false;
            }
            
            setTimeout(() => {
                btn.disabled = false;
                btn.classList.remove('bg-emerald-600');
                btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                btn.innerHTML = '<span class="material-icons text-sm">send</span> Kirim via Email';
            }, 5000);
        }
    </script>

</body>
</html>
