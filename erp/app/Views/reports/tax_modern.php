<?php
/**
 * Modern Tax Report (PPh 21)
 */
$currentPage = 'reports';
$monthNames = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
$taxLabels = ['pph21' => 'PPh 21 (NPWP)', 'pph21_non_npwp' => 'PPh 21 (Non-NPWP)', 'exempt' => 'Exempt', 'foreign' => 'Foreign'];
?>
<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Tax Report' ?> - IndoOcean ERP</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','sans-serif']}}}}</script>
    <style>
        @keyframes fadeInUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
        .animate-fade-in{animation:fadeInUp .4s ease-out forwards}
        .animate-d1{animation-delay:.05s}.animate-d2{animation-delay:.1s}
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans">
<div class="flex h-screen overflow-hidden">
    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>
    <main class="flex-1 flex flex-col h-screen overflow-hidden ml-64">
        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-6 flex-shrink-0">
            <div>
                <h1 class="text-base font-bold text-slate-800 tracking-tight"><?= __('reports.tax_report') ?></h1>
                <p class="text-[11px] text-slate-400"><?= date('F Y', strtotime("$year-$month-01")) ?></p>
            </div>
            <a href="<?= BASE_URL ?>reports" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-lg transition-colors">
                <span class="material-icons text-sm">arrow_back</span> <?= __('common.back') ?>
            </a>
        </header>
        <div class="flex-1 overflow-y-auto p-6">
            <!-- Period Filter -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 mb-5 inline-flex items-center gap-3 opacity-0 animate-fade-in">
                <span class="text-xs font-semibold text-slate-500">Period:</span>
                <form method="GET" action="<?= BASE_URL ?>reports/tax" class="flex items-center gap-2">
                    <select name="month" class="px-3 py-1.5 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?= $m ?>" <?= ($month ?? date('n')) == $m ? 'selected' : '' ?>><?= $monthNames[$m] ?></option>
                        <?php endfor; ?>
                    </select>
                    <select name="year" class="px-3 py-1.5 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
                            <option value="<?= $y ?>" <?= ($year ?? date('Y')) == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                    <button type="submit" class="px-3 py-1.5 bg-slate-800 text-white text-xs font-semibold rounded-lg hover:bg-slate-700 transition-colors">View</button>
                </form>
            </div>

            <!-- Stat -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 opacity-0 animate-fade-in animate-d1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total PPh 21</p>
                            <p class="text-2xl font-extrabold text-red-500 mt-1">$<?= number_format($totalTax ?? 0, 2) ?></p>
                        </div>
                        <div class="p-3 bg-red-50 rounded-xl"><span class="material-icons text-red-500 text-2xl">receipt_long</span></div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in animate-d2">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead><tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Crew Name</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Rank</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Vessel</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Tax Type</th>
                            <th class="px-5 py-2.5 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Gross Salary</th>
                            <th class="px-5 py-2.5 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Tax Amount</th>
                        </tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($items)): ?>
                            <tr><td colspan="6" class="py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="bg-slate-100 p-5 rounded-full mb-4"><span class="material-icons text-4xl text-slate-300">receipt</span></div>
                                    <h3 class="text-base font-semibold text-slate-700"><?= __('reports.no_data') ?></h3>
                                </div>
                            </td></tr>
                            <?php else: ?>
                                <?php foreach ($items as $item): ?>
                                <tr class="hover:bg-blue-50/40 transition-colors">
                                    <td class="px-5 py-3 text-sm font-semibold text-slate-800"><?= htmlspecialchars($item['crew_name'] ?? '-') ?></td>
                                    <td class="px-5 py-3 text-sm text-slate-600"><?= htmlspecialchars($item['rank_name'] ?? '-') ?></td>
                                    <td class="px-5 py-3 text-sm text-slate-600"><?= htmlspecialchars($item['vessel_name'] ?? '-') ?></td>
                                    <td class="px-5 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600"><?= $taxLabels[$item['tax_type']] ?? $item['tax_type'] ?></span></td>
                                    <td class="px-5 py-3 text-sm text-right text-slate-800">$<?= number_format($item['gross_salary'] ?? 0, 2) ?></td>
                                    <td class="px-5 py-3 text-sm text-right font-bold text-red-500">$<?= number_format($item['tax_amount'] ?? 0, 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($items)): ?>
                        <tfoot>
                            <tr class="bg-slate-50 border-t-2 border-slate-300">
                                <td colspan="4" class="px-5 py-3 text-sm font-bold text-slate-800">Total</td>
                                <td class="px-5 py-3 text-sm text-right font-bold text-slate-800">$<?= number_format(array_sum(array_column($items, 'gross_salary')), 2) ?></td>
                                <td class="px-5 py-3 text-sm text-right font-extrabold text-red-500">$<?= number_format($totalTax ?? 0, 2) ?></td>
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
