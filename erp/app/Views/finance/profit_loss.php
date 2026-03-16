<?php
/**
 * Profit & Loss Statement
 */
$r = $report ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Profit & Loss' ?> — IndoOcean ERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>body{font-family:'Inter',sans-serif} [x-cloak]{display:none!important}
    @keyframes fadeInUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}} .animate-fade-in{animation:fadeInUp .5s ease both}</style>
</head>
<body class="bg-slate-50 min-h-screen">
    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>
    <main class="ml-64 flex-1">
        <div class="p-6 lg:p-8 max-w-4xl mx-auto">
            <h1 class="text-2xl font-bold text-slate-800 mb-2 animate-fade-in">📊 Laporan Laba Rugi (P&L)</h1>
            <p class="text-slate-500 text-sm mb-6">Perhitungan laba rugi sesuai standar IFRS/PSAK</p>

            <!-- Filters -->
            <div class="bg-white rounded-2xl border border-slate-100 p-5 mb-6 shadow-sm animate-fade-in">
                <form method="GET" class="flex flex-wrap items-end gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="<?= $start_date ?>" class="text-sm rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="<?= $end_date ?>" class="text-sm rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Cost Center</label>
                        <select name="cost_center_id" class="text-sm rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua</option>
                            <?php foreach ($cost_centers ?? [] as $cc): ?>
                            <option value="<?= $cc['id'] ?>" <?= $cost_center_id == $cc['id'] ? 'selected' : '' ?>>[<?= $cc['code'] ?>] <?= htmlspecialchars($cc['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">Tampilkan</button>
                </form>
            </div>

            <!-- P&L Statement -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden animate-fade-in">
                <div class="p-5 border-b border-slate-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <h2 class="text-sm font-bold text-slate-700">PT Indo Ocean Crew Service</h2>
                    <p class="text-xs text-slate-500">Laporan Laba Rugi — <?= date('d M Y', strtotime($start_date)) ?> s/d <?= date('d M Y', strtotime($end_date)) ?></p>
                </div>

                <div class="p-6 space-y-4">
                    <!-- REVENUE -->
                    <div>
                        <h3 class="text-xs font-bold text-emerald-700 uppercase tracking-wider mb-2">PENDAPATAN (Revenue)</h3>
                        <?php if (!empty($r['revenue_details'])): ?>
                        <?php foreach ($r['revenue_details'] as $item): ?>
                        <div class="flex justify-between py-1 pl-4 text-sm">
                            <span class="text-slate-600"><?= htmlspecialchars($item['account_name']) ?></span>
                            <span class="text-slate-700">Rp <?= number_format($item['balance'], 0, ',', '.') ?></span>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        <div class="flex justify-between py-2 pl-4 border-t border-slate-100 font-bold text-sm">
                            <span class="text-emerald-700">Total Pendapatan</span>
                            <span class="text-emerald-700">Rp <?= number_format($r['total_revenue'] ?? 0, 0, ',', '.') ?></span>
                        </div>
                    </div>

                    <!-- COGS -->
                    <div>
                        <h3 class="text-xs font-bold text-amber-700 uppercase tracking-wider mb-2">HARGA POKOK (COGS)</h3>
                        <?php if (!empty($r['cogs_details'])): ?>
                        <?php foreach ($r['cogs_details'] as $item): ?>
                        <div class="flex justify-between py-1 pl-4 text-sm">
                            <span class="text-slate-600"><?= htmlspecialchars($item['account_name']) ?></span>
                            <span class="text-slate-700">Rp <?= number_format($item['balance'], 0, ',', '.') ?></span>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        <div class="flex justify-between py-2 pl-4 border-t border-slate-100 font-bold text-sm">
                            <span class="text-amber-700">Total COGS</span>
                            <span class="text-amber-700">Rp <?= number_format($r['total_cogs'] ?? 0, 0, ',', '.') ?></span>
                        </div>
                    </div>

                    <!-- GROSS PROFIT -->
                    <div class="bg-blue-50 rounded-xl p-4">
                        <div class="flex justify-between font-bold text-base">
                            <span class="text-blue-800">LABA KOTOR (Gross Profit)</span>
                            <span class="text-blue-800">Rp <?= number_format($r['gross_profit'] ?? 0, 0, ',', '.') ?></span>
                        </div>
                        <?php if (($r['total_revenue'] ?? 0) > 0): ?>
                        <p class="text-xs text-blue-600 mt-1">Margin: <?= number_format((($r['gross_profit'] ?? 0) / $r['total_revenue']) * 100, 1) ?>%</p>
                        <?php endif; ?>
                    </div>

                    <!-- OPERATING EXPENSES -->
                    <div>
                        <h3 class="text-xs font-bold text-rose-700 uppercase tracking-wider mb-2">BEBAN OPERASIONAL</h3>
                        <?php if (!empty($r['expense_details'])): ?>
                        <?php foreach ($r['expense_details'] as $item): ?>
                        <div class="flex justify-between py-1 pl-4 text-sm">
                            <span class="text-slate-600"><?= htmlspecialchars($item['account_name']) ?></span>
                            <span class="text-slate-700">Rp <?= number_format($item['balance'], 0, ',', '.') ?></span>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        <div class="flex justify-between py-2 pl-4 border-t border-slate-100 font-bold text-sm">
                            <span class="text-rose-700">Total Beban Operasional</span>
                            <span class="text-rose-700">Rp <?= number_format($r['total_expenses'] ?? 0, 0, ',', '.') ?></span>
                        </div>
                    </div>

                    <!-- NET PROFIT -->
                    <?php $netProfit = $r['net_profit'] ?? 0; ?>
                    <div class="rounded-xl p-5 <?= $netProfit >= 0 ? 'bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200' : 'bg-gradient-to-r from-red-50 to-rose-50 border border-red-200' ?>">
                        <div class="flex justify-between font-bold text-lg">
                            <span class="<?= $netProfit >= 0 ? 'text-emerald-800' : 'text-red-800' ?>">
                                <?= $netProfit >= 0 ? '✅ LABA BERSIH' : '❌ RUGI BERSIH' ?> (Net)
                            </span>
                            <span class="<?= $netProfit >= 0 ? 'text-emerald-800' : 'text-red-800' ?>">
                                Rp <?= number_format(abs($netProfit), 0, ',', '.') ?>
                            </span>
                        </div>
                        <?php if (($r['total_revenue'] ?? 0) > 0): ?>
                        <p class="text-xs <?= $netProfit >= 0 ? 'text-emerald-600' : 'text-red-600' ?> mt-1">
                            Net Profit Margin: <?= number_format(($netProfit / $r['total_revenue']) * 100, 1) ?>%
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Print -->
            <div class="mt-6 text-right">
                <button onclick="window.print()" class="px-4 py-2 border border-slate-200 text-slate-600 text-sm rounded-lg hover:bg-slate-50 flex items-center gap-1 ml-auto">
                    <span class="material-icons text-sm">print</span> Cetak
                </button>
            </div>
        </div>
    </main>
</body>
</html>
