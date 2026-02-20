<?php
/**
 * Modern Payroll Summary (Financial) Report
 * Clean white design with modern sidebar
 */
$currentPage = 'reports';

$totalGross = array_sum(array_column($periods ?? [], 'total_gross'));
$totalNet = array_sum(array_column($periods ?? [], 'total_net'));
$totalTax = array_sum(array_column($periods ?? [], 'total_tax'));
$totalDeductions = array_sum(array_column($periods ?? [], 'total_deductions'));
$months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
?>
<!DOCTYPE html>
<html lang="<?= session()->get('lang') ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Payroll Summary' ?> - IndoOcean ERP</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','sans-serif']}}}}</script>
    <style>
        @keyframes fadeInUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
        .animate-fade-in{animation:fadeInUp .4s ease-out forwards}
        .animate-d1{animation-delay:.05s}.animate-d2{animation-delay:.1s}.animate-d3{animation-delay:.15s}
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans">
<div class="flex h-screen overflow-hidden">
    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

    <main class="flex-1 flex flex-col h-screen overflow-hidden ml-64">
        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-6 flex-shrink-0">
            <div>
                <h1 class="text-base font-bold text-slate-800 tracking-tight"><?= __('reports.payroll_summary') ?> — <?= $year ?? date('Y') ?></h1>
                <p class="text-[11px] text-slate-400"><?= __('reports.payroll_summary_desc') ?></p>
            </div>
            <a href="<?= BASE_URL ?>reports"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-lg transition-colors">
                <span class="material-icons text-sm">arrow_back</span> <?= __('common.back') ?>
            </a>
        </header>

        <div class="flex-1 overflow-y-auto p-6">
            <!-- Year Filter -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 mb-6 inline-flex items-center gap-3">
                <span class="text-xs font-semibold text-slate-500">Year:</span>
                <form method="GET" action="<?= BASE_URL ?>reports/payroll-summary">
                    <select name="year" onchange="this.form.submit()"
                            class="px-3 py-1.5 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                        <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                            <option value="<?= $y ?>" <?= ($year ?? date('Y')) == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </form>
            </div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 opacity-0 animate-fade-in">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Gross (YTD)</p>
                            <p class="text-2xl font-extrabold text-emerald-600 mt-1">$<?= number_format($totalGross, 0) ?></p>
                        </div>
                        <div class="p-3 bg-emerald-50 rounded-xl"><span class="material-icons text-emerald-600 text-2xl">trending_up</span></div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 opacity-0 animate-fade-in animate-d1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Tax (YTD)</p>
                            <p class="text-2xl font-extrabold text-red-500 mt-1">$<?= number_format($totalTax, 0) ?></p>
                        </div>
                        <div class="p-3 bg-red-50 rounded-xl"><span class="material-icons text-red-500 text-2xl">receipt_long</span></div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 opacity-0 animate-fade-in animate-d2">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Net (YTD)</p>
                            <p class="text-2xl font-extrabold text-blue-600 mt-1">$<?= number_format($totalNet, 0) ?></p>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-xl"><span class="material-icons text-blue-600 text-2xl">account_balance_wallet</span></div>
                    </div>
                </div>
            </div>

            <!-- Payroll Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in animate-d3">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                    <div class="p-2 bg-blue-50 rounded-lg"><span class="material-icons text-blue-600">table_chart</span></div>
                    <h3 class="text-sm font-bold text-slate-800"><?= __('reports.payroll_summary') ?></h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Period</th>
                                <th class="px-5 py-2.5 text-center text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-2.5 text-center text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Crew</th>
                                <th class="px-5 py-2.5 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Gross</th>
                                <th class="px-5 py-2.5 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Deductions</th>
                                <th class="px-5 py-2.5 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Tax</th>
                                <th class="px-5 py-2.5 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Net</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($periods)): ?>
                            <tr>
                                <td colspan="7" class="py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="bg-slate-100 p-5 rounded-full mb-4">
                                            <span class="material-icons text-4xl text-slate-300">payments</span>
                                        </div>
                                        <h3 class="text-base font-semibold text-slate-700 mb-1"><?= __('reports.no_data') ?></h3>
                                    </div>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($periods as $p):
                                    $sc = $p['status'] === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($p['status'] === 'processing' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500');
                                ?>
                                <tr class="hover:bg-blue-50/40 transition-colors">
                                    <td class="px-5 py-3 text-sm font-semibold text-slate-800"><?= $months[$p['period_month']] ?> <?= $p['period_year'] ?></td>
                                    <td class="px-5 py-3 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold <?= $sc ?>"><?= ucfirst($p['status']) ?></span>
                                    </td>
                                    <td class="px-5 py-3 text-sm text-slate-600 text-center"><?= $p['total_crew'] ?? 0 ?></td>
                                    <td class="px-5 py-3 text-sm text-slate-800 text-right font-medium">$<?= number_format($p['total_gross'] ?? 0, 2) ?></td>
                                    <td class="px-5 py-3 text-sm text-red-500 text-right">-$<?= number_format($p['total_deductions'] ?? 0, 2) ?></td>
                                    <td class="px-5 py-3 text-sm text-amber-600 text-right">-$<?= number_format($p['total_tax'] ?? 0, 2) ?></td>
                                    <td class="px-5 py-3 text-sm text-blue-700 text-right font-bold">$<?= number_format($p['total_net'] ?? 0, 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($periods)): ?>
                        <tfoot>
                            <tr class="bg-slate-50 border-t-2 border-slate-300">
                                <td colspan="3" class="px-5 py-3 text-sm font-bold text-slate-800"><?= __('common.year') ?> Total</td>
                                <td class="px-5 py-3 text-sm text-right font-bold text-slate-800">$<?= number_format($totalGross, 2) ?></td>
                                <td class="px-5 py-3 text-sm text-right font-bold text-red-500">-$<?= number_format($totalDeductions, 2) ?></td>
                                <td class="px-5 py-3 text-sm text-right font-bold text-amber-600">-$<?= number_format($totalTax, 2) ?></td>
                                <td class="px-5 py-3 text-sm text-right font-extrabold text-blue-700">$<?= number_format($totalNet, 2) ?></td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <div class="mt-6 text-center"><p class="text-xs text-slate-400">© <?= date('Y') ?> IndoOcean ERP System</p></div>
        </div>
    </main>
</div>
</body>
</html>
