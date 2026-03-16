<?php
/**
 * Cash Flow Statement
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Cash Flow' ?> — IndoOcean ERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <style>body{font-family:'Inter',sans-serif}
    @keyframes fadeInUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}} .animate-fade-in{animation:fadeInUp .5s ease both}</style>
</head>
<body class="bg-slate-50 min-h-screen">
    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>
    <main class="ml-64 flex-1">
        <div class="p-6 lg:p-8 max-w-6xl mx-auto">
            <h1 class="text-2xl font-bold text-slate-800 mb-2 animate-fade-in">💸 Arus Kas (Cash Flow)</h1>
            <p class="text-slate-500 text-sm mb-8">Ringkasan aliran kas masuk & keluar 12 bulan terakhir</p>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
                <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
                    <div class="flex items-center gap-3 mb-2"><div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center"><span class="material-icons text-emerald-600">arrow_downward</span></div><span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Inflow</span></div>
                    <p class="text-2xl font-bold text-slate-800">Rp <?= number_format($total_inflow, 0, ',', '.') ?></p>
                    <p class="text-xs text-slate-500 mt-1">Total kas masuk</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
                    <div class="flex items-center gap-3 mb-2"><div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center"><span class="material-icons text-rose-600">arrow_upward</span></div><span class="text-xs font-semibold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full">Outflow</span></div>
                    <p class="text-2xl font-bold text-slate-800">Rp <?= number_format($total_outflow, 0, ',', '.') ?></p>
                    <p class="text-xs text-slate-500 mt-1">Total kas keluar</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
                    <div class="flex items-center gap-3 mb-2"><div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center"><span class="material-icons text-blue-600">account_balance</span></div><span class="text-xs font-semibold <?= $net_cashflow >= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-red-600 bg-red-50' ?> px-2 py-0.5 rounded-full"><?= $net_cashflow >= 0 ? 'Surplus' : 'Deficit' ?></span></div>
                    <p class="text-2xl font-bold <?= $net_cashflow >= 0 ? 'text-emerald-700' : 'text-red-600' ?>">Rp <?= number_format(abs($net_cashflow), 0, ',', '.') ?></p>
                    <p class="text-xs text-slate-500 mt-1">Arus kas bersih</p>
                </div>
            </div>

            <!-- Chart -->
            <div class="bg-white rounded-2xl border border-slate-100 p-6 mb-8 shadow-sm animate-fade-in">
                <h3 class="text-sm font-semibold text-slate-700 mb-4">📊 Tren Arus Kas Bulanan</h3>
                <canvas id="cashflowChart" height="100"></canvas>
            </div>

            <!-- Monthly Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden animate-fade-in">
                <table class="w-full text-sm">
                    <thead><tr class="bg-slate-50 border-b border-slate-100">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-600">BULAN</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-emerald-600">KAS MASUK</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-rose-600">KAS KELUAR</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-700">NET FLOW</th>
                    </tr></thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($trend as $t): ?>
                        <tr class="hover:bg-blue-50/30">
                            <td class="px-5 py-3 font-medium text-slate-700"><?= htmlspecialchars($t['label']) ?></td>
                            <td class="px-5 py-3 text-right text-emerald-600 font-semibold">Rp <?= number_format($t['revenue'], 0, ',', '.') ?></td>
                            <td class="px-5 py-3 text-right text-rose-600 font-semibold">Rp <?= number_format($t['expense'], 0, ',', '.') ?></td>
                            <?php $net = $t['revenue'] - $t['expense']; ?>
                            <td class="px-5 py-3 text-right font-bold <?= $net >= 0 ? 'text-emerald-700' : 'text-red-600' ?>">
                                <?= $net >= 0 ? '+' : '' ?>Rp <?= number_format($net, 0, ',', '.') ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot><tr class="bg-slate-50 border-t-2 border-slate-200 font-bold">
                        <td class="px-5 py-3 text-slate-700">TOTAL</td>
                        <td class="px-5 py-3 text-right text-emerald-700">Rp <?= number_format($total_inflow, 0, ',', '.') ?></td>
                        <td class="px-5 py-3 text-right text-rose-700">Rp <?= number_format($total_outflow, 0, ',', '.') ?></td>
                        <td class="px-5 py-3 text-right <?= $net_cashflow >= 0 ? 'text-emerald-700' : 'text-red-600' ?>">Rp <?= number_format($net_cashflow, 0, ',', '.') ?></td>
                    </tr></tfoot>
                </table>
            </div>
        </div>
    </main>
    <script>
    const td = <?= json_encode($trend ?? []) ?>;
    if (td.length > 0) {
        new Chart(document.getElementById('cashflowChart'), {
            type: 'line',
            data: {
                labels: td.map(d => d.label),
                datasets: [
                    { label: 'Kas Masuk', data: td.map(d => d.revenue), borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,.1)', fill: true, tension: .4 },
                    { label: 'Kas Keluar', data: td.map(d => d.expense), borderColor: '#f43f5e', backgroundColor: 'rgba(244,63,94,.1)', fill: true, tension: .4 },
                    { label: 'Net Flow', data: td.map(d => d.revenue - d.expense), borderColor: '#3b82f6', borderDash: [5,5], tension: .4, pointRadius: 4 }
                ]
            },
            options: { responsive: true, plugins: { legend: { position: 'top', labels: { usePointStyle: true, font: { size: 11, family: 'Inter' } } } }, scales: { y: { ticks: { callback: v => 'Rp ' + (v/1e6).toFixed(0) + 'M' }, grid: { color: 'rgba(0,0,0,.04)' } }, x: { grid: { display: false } } } }
        });
    }
    </script>
</body>
</html>
