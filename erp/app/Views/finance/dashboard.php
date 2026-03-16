<?php
/**
 * Finance Dashboard
 * Integrated: Contract Revenue, Crew Costs, Invoices, Bills, Payroll
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Finance Dashboard' ?> — IndoOcean ERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Round" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        @keyframes fadeInUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
        .animate-fade-in { animation: fadeInUp .5s ease both; }
        .animate-fade-in-delay-1 { animation: fadeInUp .5s ease .1s both; }
        .animate-fade-in-delay-2 { animation: fadeInUp .5s ease .2s both; }
        .animate-fade-in-delay-3 { animation: fadeInUp .5s ease .3s both; }
        .animate-fade-in-delay-4 { animation: fadeInUp .5s ease .4s both; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

    <main class="ml-64 flex-1">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto">

            <!-- Header -->
            <div class="mb-8 animate-fade-in">
                <h1 class="text-2xl font-bold text-slate-800">💰 Finance Dashboard</h1>
                <p class="text-slate-500 text-sm mt-1">Ringkasan keuangan & pendapatan perkapalan — <?= date('F Y') ?></p>
            </div>

            <!-- Flash Messages -->
            <?php if (!empty($flash)): ?>
            <div class="mb-6 p-4 rounded-xl border <?= $flash['type'] === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-700' ?>">
                <div class="flex items-center gap-2">
                    <span class="material-icons text-lg"><?= $flash['type'] === 'success' ? 'check_circle' : 'error' ?></span>
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- ═══════ TOP KPI CARDS ═══════ -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
                <!-- Total Revenue (Contract + Invoice) -->
                <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition-all animate-fade-in">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                            <span class="material-icons text-emerald-600">trending_up</span>
                        </div>
                        <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Revenue</span>
                    </div>
                    <p class="text-2xl font-bold text-slate-800">Rp <?= number_format($stats['total_revenue'] ?? 0, 0, ',', '.') ?></p>
                    <p class="text-xs text-slate-500 mt-1">Total Pendapatan /bulan</p>
                    <div class="mt-2 flex gap-2 text-[10px]">
                        <span class="px-1.5 py-0.5 bg-emerald-50 text-emerald-600 rounded">Kontrak: Rp <?= number_format($stats['contract_revenue'] ?? 0, 0, ',', '.') ?></span>
                        <span class="px-1.5 py-0.5 bg-blue-50 text-blue-600 rounded">Invoice: Rp <?= number_format($stats['invoice_revenue'] ?? 0, 0, ',', '.') ?></span>
                    </div>
                </div>

                <!-- Total Expenses -->
                <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition-all animate-fade-in-delay-1">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center">
                            <span class="material-icons text-rose-600">trending_down</span>
                        </div>
                        <span class="text-xs font-semibold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full">Expense</span>
                    </div>
                    <p class="text-2xl font-bold text-slate-800">Rp <?= number_format($stats['total_expenses'] ?? 0, 0, ',', '.') ?></p>
                    <p class="text-xs text-slate-500 mt-1">Total Pengeluaran /bulan</p>
                    <div class="mt-2 flex flex-wrap gap-1.5 text-[10px]">
                        <span class="px-1.5 py-0.5 bg-orange-50 text-orange-600 rounded">Crew: Rp <?= number_format($stats['crew_cost'] ?? 0, 0, ',', '.') ?></span>
                        <span class="px-1.5 py-0.5 bg-rose-50 text-rose-600 rounded">Bill: Rp <?= number_format($stats['bill_expenses'] ?? 0, 0, ',', '.') ?></span>
                    </div>
                </div>

                <!-- Net Profit -->
                <?php $netProfit = $stats['net_profit'] ?? 0; ?>
                <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition-all animate-fade-in-delay-2">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl <?= $netProfit >= 0 ? 'bg-blue-100' : 'bg-red-100' ?> flex items-center justify-center">
                            <span class="material-icons <?= $netProfit >= 0 ? 'text-blue-600' : 'text-red-600' ?>">account_balance</span>
                        </div>
                        <span class="text-xs font-semibold <?= $netProfit >= 0 ? 'text-blue-600 bg-blue-50' : 'text-red-600 bg-red-50' ?> px-2 py-0.5 rounded-full">
                            <?= $netProfit >= 0 ? 'Profit' : 'Loss' ?>
                        </span>
                    </div>
                    <p class="text-2xl font-bold <?= $netProfit >= 0 ? 'text-slate-800' : 'text-red-600' ?>">
                        Rp <?= number_format(abs($netProfit), 0, ',', '.') ?>
                    </p>
                    <p class="text-xs text-slate-500 mt-1">Laba bersih /bulan</p>
                    <?php if (($stats['contract_revenue'] ?? 0) > 0): ?>
                    <div class="mt-2 text-[10px]">
                        <span class="px-1.5 py-0.5 bg-indigo-50 text-indigo-600 rounded">
                            Margin: <?= $stats['total_revenue'] > 0 ? round(($netProfit / $stats['total_revenue']) * 100, 1) : 0 ?>%
                        </span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Operational Stats -->
                <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition-all animate-fade-in-delay-3">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-violet-100 flex items-center justify-center">
                            <span class="material-icons text-violet-600">directions_boat</span>
                        </div>
                        <?php if (($stats['overdue_count'] ?? 0) > 0): ?>
                        <span class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-0.5 rounded-full">
                            <?= $stats['overdue_count'] ?> Overdue
                        </span>
                        <?php else: ?>
                        <span class="text-xs font-semibold text-violet-600 bg-violet-50 px-2 py-0.5 rounded-full">Operasional</span>
                        <?php endif; ?>
                    </div>
                    <p class="text-2xl font-bold text-slate-800"><?= $stats['active_contracts'] ?? 0 ?> <span class="text-sm font-medium text-slate-500">Kontrak</span></p>
                    <p class="text-xs text-slate-500 mt-1"><?= $stats['total_crew'] ?? 0 ?> crew aktif di kapal</p>
                    <?php if (($stats['outstanding_receivable'] ?? 0) > 0): ?>
                    <div class="mt-2 text-[10px]">
                        <span class="px-1.5 py-0.5 bg-amber-50 text-amber-600 rounded">
                            Piutang: Rp <?= number_format($stats['outstanding_receivable'], 0, ',', '.') ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ═══════ REVENUE BREAKDOWN MINI CARDS ═══════ -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8 animate-fade-in-delay-1">
                <?php
                $breakdowns = [
                    ['label' => 'Revenue Kontrak', 'val' => $stats['contract_revenue'] ?? 0, 'icon' => 'sailing', 'color' => 'emerald'],
                    ['label' => 'Biaya Crew', 'val' => $stats['crew_cost'] ?? 0, 'icon' => 'groups', 'color' => 'orange'],
                    ['label' => 'Gross Margin', 'val' => $stats['gross_margin'] ?? 0, 'icon' => 'show_chart', 'color' => ($stats['gross_margin'] ?? 0) >= 0 ? 'blue' : 'red'],
                    ['label' => 'Payroll Bulan Ini', 'val' => $stats['payroll_expenses'] ?? 0, 'icon' => 'payments', 'color' => 'rose'],
                ];
                foreach ($breakdowns as $bd): ?>
                <div class="bg-white rounded-xl border border-slate-100 p-4 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-icons text-<?= $bd['color'] ?>-500 text-base"><?= $bd['icon'] ?></span>
                        <span class="text-xs font-medium text-slate-500"><?= $bd['label'] ?></span>
                    </div>
                    <p class="text-lg font-bold text-slate-800">Rp <?= number_format(abs($bd['val']), 0, ',', '.') ?></p>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- ═══════ CHART + AGING SUMMARY ═══════ -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Revenue vs Expense Chart -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 p-6 shadow-sm animate-fade-in-delay-1">
                    <h3 class="text-sm font-semibold text-slate-700 mb-4">📈 Tren Pendapatan vs Pengeluaran (termasuk Kontrak Perkapalan)</h3>
                    <canvas id="trendChart" height="120"></canvas>
                </div>

                <!-- Aging Summary -->
                <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm animate-fade-in-delay-2">
                    <h3 class="text-sm font-semibold text-slate-700 mb-4">⏰ Aging Piutang</h3>
                    <div class="space-y-3">
                        <?php
                        $agingData = [
                            ['label' => 'Belum Jatuh Tempo', 'key' => 'not_yet_due', 'color' => 'emerald'],
                            ['label' => '0-30 hari', 'key' => 'aging_0_30', 'color' => 'amber'],
                            ['label' => '31-60 hari', 'key' => 'aging_31_60', 'color' => 'orange'],
                            ['label' => '61-90 hari', 'key' => 'aging_61_90', 'color' => 'rose'],
                            ['label' => '> 90 hari', 'key' => 'aging_90_plus', 'color' => 'red'],
                        ];
                        $totalAging = max(1, array_sum(array_map(fn($a) => floatval($aging[$a['key']] ?? 0), $agingData)));
                        foreach ($agingData as $ag):
                            $val = floatval($aging[$ag['key']] ?? 0);
                            $pct = $totalAging > 0 ? ($val / $totalAging) * 100 : 0;
                        ?>
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-slate-600"><?= $ag['label'] ?></span>
                                <span class="font-semibold text-slate-700">Rp <?= number_format($val, 0, ',', '.') ?></span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="bg-<?= $ag['color'] ?>-500 h-2 rounded-full transition-all" style="width: <?= round($pct) ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <a href="<?= BASE_URL ?>finance/invoices?status=unpaid" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                            Lihat semua invoice <span class="material-icons text-sm">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- ═══════ TOP CLIENTS & TOP VESSELS ═══════ -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Top Clients by Revenue -->
                <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm animate-fade-in-delay-2">
                    <h3 class="text-sm font-semibold text-slate-700 mb-4">🏢 Revenue per Klien (Kontrak Aktif)</h3>
                    <?php if (!empty($topClients)): ?>
                    <div class="space-y-3">
                        <?php 
                        $maxClientRev = max(1, max(array_column($topClients, 'revenue')));
                        foreach ($topClients as $idx => $client): 
                            $pct = ($client['revenue'] / $maxClientRev) * 100;
                        ?>
                        <div>
                            <div class="flex justify-between items-center text-xs mb-1">
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-bold"><?= $idx + 1 ?></span>
                                    <span class="font-medium text-slate-700"><?= htmlspecialchars($client['name']) ?></span>
                                    <span class="text-slate-400">(<?= $client['crew_count'] ?> crew)</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-semibold text-slate-700">Rp <?= number_format($client['revenue'], 0, ',', '.') ?></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-slate-100 rounded-full h-2">
                                    <div class="bg-emerald-500 h-2 rounded-full transition-all" style="width: <?= round($pct) ?>%"></div>
                                </div>
                                <span class="text-[10px] font-semibold <?= $client['margin'] >= 0 ? 'text-emerald-600' : 'text-red-600' ?>"><?= $client['margin'] ?>%</span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-6">
                        <span class="material-icons text-3xl text-slate-300">business</span>
                        <p class="text-xs text-slate-400 mt-2">Belum ada kontrak aktif</p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Top Vessels by Revenue -->
                <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm animate-fade-in-delay-3">
                    <h3 class="text-sm font-semibold text-slate-700 mb-4">🚢 Revenue per Kapal (Kontrak Aktif)</h3>
                    <?php if (!empty($topVessels)): ?>
                    <div class="space-y-3">
                        <?php 
                        $maxVesselRev = max(1, max(array_column($topVessels, 'revenue')));
                        foreach ($topVessels as $idx => $vessel): 
                            $pct = ($vessel['revenue'] / $maxVesselRev) * 100;
                        ?>
                        <div>
                            <div class="flex justify-between items-center text-xs mb-1">
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px] font-bold"><?= $idx + 1 ?></span>
                                    <span class="font-medium text-slate-700"><?= htmlspecialchars($vessel['vessel_name']) ?></span>
                                    <span class="text-slate-400">(<?= $vessel['crew_count'] ?> crew)</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-semibold text-slate-700">Rp <?= number_format($vessel['revenue'], 0, ',', '.') ?></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-slate-100 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full transition-all" style="width: <?= round($pct) ?>%"></div>
                                </div>
                                <span class="text-[10px] font-semibold <?= $vessel['margin'] >= 0 ? 'text-emerald-600' : 'text-red-600' ?>"><?= $vessel['margin'] ?>%</span>
                            </div>
                            <p class="text-[10px] text-slate-400 ml-7">Klien: <?= htmlspecialchars($vessel['client_name']) ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-6">
                        <span class="material-icons text-3xl text-slate-300">directions_boat</span>
                        <p class="text-xs text-slate-400 mt-2">Belum ada kapal dengan kontrak aktif</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ═══════ QUICK ACTIONS + RECENT TRANSACTIONS ═══════ -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm animate-fade-in-delay-3">
                    <h3 class="text-sm font-semibold text-slate-700 mb-4">⚡ Aksi Cepat</h3>
                    <div class="space-y-2">
                        <a href="<?= BASE_URL ?>finance/create-invoice" class="flex items-center gap-3 p-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 transition-colors">
                            <span class="material-icons">add_circle</span>
                            <span class="text-sm font-medium">Buat Invoice Baru</span>
                        </a>
                        <a href="<?= BASE_URL ?>finance/create-bill" class="flex items-center gap-3 p-3 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 transition-colors">
                            <span class="material-icons">receipt</span>
                            <span class="text-sm font-medium">Catat Tagihan (Bill)</span>
                        </a>
                        <a href="<?= BASE_URL ?>finance/create-journal" class="flex items-center gap-3 p-3 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 transition-colors">
                            <span class="material-icons">edit_note</span>
                            <span class="text-sm font-medium">Jurnal Manual</span>
                        </a>
                        <a href="<?= BASE_URL ?>finance/profit-loss" class="flex items-center gap-3 p-3 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 transition-colors">
                            <span class="material-icons">analytics</span>
                            <span class="text-sm font-medium">Laporan Laba/Rugi</span>
                        </a>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 p-6 shadow-sm animate-fade-in-delay-4">
                    <h3 class="text-sm font-semibold text-slate-700 mb-4">🕒 Transaksi Terbaru</h3>
                    <?php if (!empty($recent)): ?>
                    <div class="space-y-2">
                        <?php foreach ($recent as $tx): ?>
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center <?= $tx['direction'] === 'in' ? 'bg-emerald-100' : 'bg-rose-100' ?>">
                                    <span class="material-icons text-sm <?= $tx['direction'] === 'in' ? 'text-emerald-600' : 'text-rose-600' ?>">
                                        <?= $tx['direction'] === 'in' ? 'arrow_downward' : 'arrow_upward' ?>
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-700"><?= htmlspecialchars($tx['ref_no']) ?></p>
                                    <p class="text-xs text-slate-500"><?= htmlspecialchars($tx['description'] ?? '-') ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold <?= $tx['direction'] === 'in' ? 'text-emerald-600' : 'text-rose-600' ?>">
                                    <?= $tx['direction'] === 'in' ? '+' : '-' ?> Rp <?= number_format($tx['amount'], 0, ',', '.') ?>
                                </p>
                                <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded-full
                                    <?php
                                    $statusColors = [
                                        'draft' => 'bg-slate-100 text-slate-600',
                                        'unpaid' => 'bg-amber-100 text-amber-700',
                                        'partial' => 'bg-blue-100 text-blue-700',
                                        'paid' => 'bg-emerald-100 text-emerald-700',
                                        'cancelled' => 'bg-red-100 text-red-600',
                                    ];
                                    echo $statusColors[$tx['status']] ?? 'bg-slate-100 text-slate-600';
                                    ?>">
                                    <?= ucfirst($tx['status']) ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-8">
                        <span class="material-icons text-4xl text-slate-300">account_balance_wallet</span>
                        <p class="text-sm text-slate-400 mt-2">Belum ada transaksi invoice/bill</p>
                        <p class="text-xs text-slate-400">Buat invoice atau catat tagihan untuk memulai</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>

    <script>
    // Revenue vs Expense Trend Chart
    const trendData = <?= json_encode($trend ?? []) ?>;
    if (trendData.length > 0) {
        new Chart(document.getElementById('trendChart'), {
            type: 'bar',
            data: {
                labels: trendData.map(d => d.label),
                datasets: [
                    {
                        label: 'Revenue Kontrak',
                        data: trendData.map(d => d.contract_revenue || 0),
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderRadius: 6,
                        borderSkipped: false,
                        stack: 'revenue',
                    },
                    {
                        label: 'Revenue Invoice',
                        data: trendData.map(d => d.invoice_revenue || 0),
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderRadius: 6,
                        borderSkipped: false,
                        stack: 'revenue',
                    },
                    {
                        label: 'Biaya Crew',
                        data: trendData.map(d => d.crew_cost || 0),
                        backgroundColor: 'rgba(251, 146, 60, 0.7)',
                        borderRadius: 6,
                        borderSkipped: false,
                        stack: 'expense',
                    },
                    {
                        label: 'Pengeluaran Lainnya',
                        data: trendData.map(d => (d.bill_expense || 0) + (d.payroll_expense || 0)),
                        backgroundColor: 'rgba(244, 63, 94, 0.7)',
                        borderRadius: 6,
                        borderSkipped: false,
                        stack: 'expense',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top', labels: { font: { size: 11, family: 'Inter' }, usePointStyle: true, pointStyle: 'rectRounded' } },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.dataset.label + ': Rp ' + ctx.raw.toLocaleString('id-ID')
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        stacked: true,
                        ticks: { callback: v => 'Rp ' + (v/1000000).toFixed(0) + 'M', font: { size: 10 } },
                        grid: { color: 'rgba(0,0,0,0.04)' }
                    },
                    x: { stacked: true, grid: { display: false }, ticks: { font: { size: 10 } } }
                }
            }
        });
    }
    </script>
</body>
</html>
