<?php
/**
 * Cost Centers — Enhanced with expense tracking per division
 */

// Prepare chart data
$ccLabels = [];
$ccInvoices = [];
$ccBills = [];
$ccJournals = [];
$ccColors = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#06b6d4','#f97316'];
foreach ($cost_centers as $idx => $cc) {
    $ccLabels[] = $cc['code'];
    $ccInvoices[] = $cc['invoice_count'];
    $ccBills[] = $cc['bill_count'];
    $ccJournals[] = $cc['journal_count'];
}

// Icon map per code
$iconMap = [
    'CC-ADMIN' => ['icon' => 'admin_panel_settings', 'color' => 'blue'],
    'CC-CREW'  => ['icon' => 'groups', 'color' => 'emerald'],
    'CC-FINANCE' => ['icon' => 'account_balance', 'color' => 'amber'],
    'CC-HQ'    => ['icon' => 'business', 'color' => 'purple'],
    'CC-IT'    => ['icon' => 'computer', 'color' => 'cyan'],
    'CC-RECRUIT' => ['icon' => 'person_search', 'color' => 'rose'],
    'CC-SHIP'  => ['icon' => 'directions_boat', 'color' => 'orange'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Cost Centers' ?> — IndoOcean ERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Round" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body{font-family:'Inter',sans-serif} [x-cloak]{display:none!important}
        @keyframes fadeInUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
        .animate-fade-in{animation:fadeInUp .5s ease both}
        .animate-d1{animation:fadeInUp .5s ease .1s both}
        .animate-d2{animation:fadeInUp .5s ease .2s both}
        .animate-d3{animation:fadeInUp .5s ease .3s both}
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>
    <main class="ml-64 flex-1">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto" x-data="{ showForm: false, editItem: null }">

            <!-- Header -->
            <div class="flex items-center justify-between mb-6 animate-fade-in">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">🏢 Cost Center</h1>
                    <p class="text-slate-500 text-sm mt-1">Pelacakan pengeluaran & aktivitas keuangan per divisi — <?= date('F Y') ?></p>
                </div>
                <button @click="showForm = true; editItem = null" class="flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors">
                    <span class="material-icons text-lg">add</span> Tambah
                </button>
            </div>

            <?php if (!empty($flash)): ?>
            <div class="mb-4 p-4 rounded-xl border <?= $flash['type'] === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-700' ?>">
                <div class="flex items-center gap-2"><span class="material-icons text-lg"><?= $flash['type'] === 'success' ? 'check_circle' : 'error' ?></span> <?= htmlspecialchars($flash['message']) ?></div>
            </div>
            <?php endif; ?>

            <!-- ═══════ SUMMARY CARDS ═══════ -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 animate-fade-in">
                <div class="bg-white rounded-xl border border-slate-100 p-4 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-icons text-blue-500 text-base">category</span>
                        <span class="text-xs font-medium text-slate-500">Total Divisi</span>
                    </div>
                    <p class="text-2xl font-bold text-slate-800"><?= count($cost_centers) ?></p>
                </div>
                <div class="bg-white rounded-xl border border-slate-100 p-4 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-icons text-emerald-500 text-base">receipt_long</span>
                        <span class="text-xs font-medium text-slate-500">Total Invoice</span>
                    </div>
                    <p class="text-2xl font-bold text-slate-800"><?= $totals['total_invoices'] ?? 0 ?></p>
                    <p class="text-[10px] text-slate-400">Rp <?= number_format($totals['total_invoice_amount'] ?? 0, 0, ',', '.') ?></p>
                </div>
                <div class="bg-white rounded-xl border border-slate-100 p-4 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-icons text-rose-500 text-base">description</span>
                        <span class="text-xs font-medium text-slate-500">Total Bills</span>
                    </div>
                    <p class="text-2xl font-bold text-slate-800"><?= $totals['total_bills'] ?? 0 ?></p>
                    <p class="text-[10px] text-slate-400">Rp <?= number_format($totals['total_bill_amount'] ?? 0, 0, ',', '.') ?></p>
                </div>
                <div class="bg-white rounded-xl border border-slate-100 p-4 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-icons text-violet-500 text-base">menu_book</span>
                        <span class="text-xs font-medium text-slate-500">Jurnal Entries</span>
                    </div>
                    <p class="text-2xl font-bold text-slate-800"><?= $totals['total_journals'] ?? 0 ?></p>
                </div>
            </div>

            <!-- ═══════ ACTIVITY CHART ═══════ -->
            <?php if (array_sum($ccInvoices) + array_sum($ccBills) + array_sum($ccJournals) > 0): ?>
            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm mb-6 animate-d1">
                <h3 class="text-sm font-semibold text-slate-700 mb-4">📊 Aktivitas Keuangan per Divisi</h3>
                <canvas id="ccChart" height="80"></canvas>
            </div>
            <?php endif; ?>

            <!-- ═══════ COST CENTER CARDS ═══════ -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 animate-d2">
                <?php foreach ($cost_centers as $idx => $cc):
                    $code = strtoupper($cc['code'] ?? '');
                    $map = $iconMap[$code] ?? ['icon' => 'folder', 'color' => 'slate'];
                    $color = $map['color'];
                    $icon = $map['icon'];
                    $totalNet = $cc['invoice_revenue'] - $cc['bill_expense'];
                ?>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all overflow-hidden">
                    <!-- Header -->
                    <div class="bg-<?= $color ?>-50 px-5 py-4 border-b border-<?= $color ?>-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-<?= $color ?>-100 flex items-center justify-center">
                                    <span class="material-icons text-<?= $color ?>-600"><?= $icon ?></span>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-800"><?= htmlspecialchars($cc['name']) ?></h3>
                                    <span class="text-[10px] font-mono text-<?= $color ?>-600 bg-<?= $color ?>-100 px-1.5 py-0.5 rounded"><?= htmlspecialchars($cc['code']) ?></span>
                                    <?php if ($cc['name_en']): ?>
                                    <span class="text-[10px] text-slate-400 ml-1"><?= htmlspecialchars($cc['name_en']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full <?= $cc['is_active'] ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500' ?>">
                                <?= $cc['is_active'] ? 'AKTIF' : 'OFF' ?>
                            </span>
                        </div>
                    </div>

                    <!-- Stats Body -->
                    <div class="px-5 py-4 space-y-3">
                        <?php if ($cc['description']): ?>
                        <p class="text-xs text-slate-400"><?= htmlspecialchars($cc['description']) ?></p>
                        <?php endif; ?>

                        <!-- Activity counters -->
                        <div class="grid grid-cols-3 gap-2">
                            <div class="text-center p-2 rounded-lg bg-blue-50">
                                <p class="text-lg font-bold text-blue-700"><?= $cc['invoice_count'] ?></p>
                                <p class="text-[10px] text-blue-500">Invoice</p>
                            </div>
                            <div class="text-center p-2 rounded-lg bg-rose-50">
                                <p class="text-lg font-bold text-rose-700"><?= $cc['bill_count'] ?></p>
                                <p class="text-[10px] text-rose-500">Bill</p>
                            </div>
                            <div class="text-center p-2 rounded-lg bg-violet-50">
                                <p class="text-lg font-bold text-violet-700"><?= $cc['journal_count'] ?></p>
                                <p class="text-[10px] text-violet-500">Jurnal</p>
                            </div>
                        </div>

                        <!-- Financial summary -->
                        <?php if ($cc['invoice_revenue'] > 0 || $cc['bill_expense'] > 0): ?>
                        <div class="space-y-1.5">
                            <?php if ($cc['invoice_revenue'] > 0): ?>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-500">💰 Revenue</span>
                                <span class="font-semibold text-emerald-600">Rp <?= number_format($cc['invoice_revenue'], 0, ',', '.') ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($cc['bill_expense'] > 0): ?>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-500">📤 Expense</span>
                                <span class="font-semibold text-rose-600">Rp <?= number_format($cc['bill_expense'], 0, ',', '.') ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="border-t border-slate-100 pt-1.5 flex justify-between items-center text-xs">
                                <span class="text-slate-500">📊 Nett</span>
                                <span class="font-bold <?= $totalNet >= 0 ? 'text-blue-600' : 'text-red-600' ?>">
                                    Rp <?= number_format(abs($totalNet), 0, ',', '.') ?>
                                    <?= $totalNet < 0 ? '(deficit)' : '' ?>
                                </span>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Operational info for crew/ship divisions -->
                        <?php if ($cc['crew_count'] > 0 || $cc['operational_cost'] > 0): ?>
                        <div class="bg-amber-50 rounded-lg p-2.5">
                            <p class="text-[10px] font-semibold text-amber-700 mb-1">⚓ Data Operasional</p>
                            <?php if ($cc['crew_count'] > 0): ?>
                            <div class="flex justify-between text-xs">
                                <span class="text-amber-600"><?= $code === 'CC-SHIP' ? 'Kapal aktif' : 'Crew aktif' ?></span>
                                <span class="font-bold text-amber-700"><?= $cc['crew_count'] ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($cc['operational_cost'] > 0): ?>
                            <div class="flex justify-between text-xs">
                                <span class="text-amber-600">Biaya /bulan</span>
                                <span class="font-bold text-amber-700">Rp <?= number_format($cc['operational_cost'], 0, ',', '.') ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <!-- No activity indicator -->
                        <?php if ($cc['total_activity'] == 0 && $cc['crew_count'] == 0): ?>
                        <div class="text-center py-2">
                            <p class="text-[10px] text-slate-400">Belum ada aktivitas keuangan tercatat</p>
                            <p class="text-[10px] text-blue-500 mt-1">Buat invoice/bill dengan cost center ini untuk mulai tracking</p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Footer actions -->
                    <div class="px-5 py-3 border-t border-slate-50 bg-slate-50/50 flex items-center justify-between">
                        <div class="flex gap-2">
                            <button @click="editItem = <?= htmlspecialchars(json_encode($cc)) ?>; showForm = true" class="text-xs text-blue-600 hover:text-blue-800 flex items-center gap-0.5">
                                <span class="material-icons text-sm">edit</span> Edit
                            </button>
                            <form method="POST" action="<?= BASE_URL ?>finance/delete-cost-center/<?= $cc['id'] ?>" onsubmit="return confirm('Hapus cost center ini?')" class="inline">
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700 flex items-center gap-0.5">
                                    <span class="material-icons text-sm">delete</span> Hapus
                                </button>
                            </form>
                        </div>
                        <?php if ($cc['total_activity'] > 0): ?>
                        <a href="<?= BASE_URL ?>finance/journal?cost_center=<?= $cc['id'] ?>" class="text-[10px] text-blue-500 hover:text-blue-700 flex items-center gap-0.5">
                            Lihat jurnal <span class="material-icons text-xs">arrow_forward</span>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- ═══════ HOW IT WORKS ═══════ -->
            <div class="mt-8 bg-blue-50 rounded-2xl border border-blue-100 p-6 animate-d3">
                <h3 class="text-sm font-bold text-blue-800 mb-3">💡 Cara Kerja Cost Center</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs text-blue-700">
                    <div class="flex gap-2">
                        <span class="material-icons text-blue-500 text-base mt-0.5">receipt_long</span>
                        <div>
                            <p class="font-semibold">1. Saat Buat Invoice/Bill</p>
                            <p class="text-blue-600">Pilih cost center untuk assign ke divisi terkait</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <span class="material-icons text-blue-500 text-base mt-0.5">auto_stories</span>
                        <div>
                            <p class="font-semibold">2. Otomatis Tercatat</p>
                            <p class="text-blue-600">Jurnal dan laporan akan ter-tag per cost center</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <span class="material-icons text-blue-500 text-base mt-0.5">analytics</span>
                        <div>
                            <p class="font-semibold">3. Analisis per Divisi</p>
                            <p class="text-blue-600">Lihat berapa besar biaya tiap divisi di laporan P&L</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══════ MODAL FORM ═══════ -->
            <div x-show="showForm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm" @click.self="showForm = false">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 mx-4" @click.stop>
                    <h3 class="text-lg font-bold text-slate-800 mb-4" x-text="editItem ? 'Edit Cost Center' : 'Tambah Cost Center'"></h3>
                    <form method="POST" action="<?= BASE_URL ?>finance/store-cost-center" class="space-y-3">
                        <input type="hidden" name="id" :value="editItem?.id || ''">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Kode</label>
                            <input type="text" name="code" :value="editItem?.code || ''" required class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 font-mono uppercase focus:ring-2 focus:ring-blue-500" placeholder="e.g. CC-OPS">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Nama</label>
                            <input type="text" name="name" :value="editItem?.name || ''" required class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Nama (EN)</label>
                            <input type="text" name="name_en" :value="editItem?.name_en || ''" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Deskripsi</label>
                            <textarea name="description" rows="2" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-blue-500" x-text="editItem?.description || ''"></textarea>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1" :checked="editItem?.is_active !== 0" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <label class="text-sm text-slate-600">Aktif</label>
                        </div>
                        <div class="flex gap-3 justify-end pt-2">
                            <button type="button" @click="showForm = false" class="px-4 py-2 border border-slate-200 text-slate-600 text-sm rounded-lg hover:bg-slate-50">Batal</button>
                            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </main>

    <script>
    // Activity chart
    <?php if (array_sum($ccInvoices) + array_sum($ccBills) + array_sum($ccJournals) > 0): ?>
    new Chart(document.getElementById('ccChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($ccLabels) ?>,
            datasets: [
                { label: 'Invoice', data: <?= json_encode($ccInvoices) ?>, backgroundColor: 'rgba(59,130,246,0.7)', borderRadius: 6 },
                { label: 'Bill', data: <?= json_encode($ccBills) ?>, backgroundColor: 'rgba(244,63,94,0.7)', borderRadius: 6 },
                { label: 'Jurnal', data: <?= json_encode($ccJournals) ?>, backgroundColor: 'rgba(139,92,246,0.7)', borderRadius: 6 }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top', labels: { font: { size: 11, family: 'Inter' }, usePointStyle: true } } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } }, grid: { color: 'rgba(0,0,0,0.04)' } },
                x: { grid: { display: false }, ticks: { font: { size: 10, family: 'monospace' } } }
            }
        }
    });
    <?php endif; ?>
    </script>
</body>
</html>
