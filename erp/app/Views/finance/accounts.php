<?php
/**
 * Chart of Accounts — Enhanced with balances and transaction data
 */

// Type metadata
$typeInfo = [
    'asset'     => ['label' => 'Aset',          'icon' => 'account_balance_wallet', 'color' => 'blue',   'emoji' => '🔵', 'normalSide' => 'Debit'],
    'liability' => ['label' => 'Kewajiban',     'icon' => 'credit_card',            'color' => 'red',    'emoji' => '🔴', 'normalSide' => 'Credit'],
    'equity'    => ['label' => 'Ekuitas',        'icon' => 'savings',                'color' => 'purple', 'emoji' => '🟣', 'normalSide' => 'Credit'],
    'revenue'   => ['label' => 'Pendapatan',     'icon' => 'trending_up',            'color' => 'emerald','emoji' => '🟢', 'normalSide' => 'Credit'],
    'cogs'      => ['label' => 'HPP (COGS)',     'icon' => 'inventory_2',            'color' => 'orange', 'emoji' => '🟠', 'normalSide' => 'Debit'],
    'expense'   => ['label' => 'Beban Operasi',  'icon' => 'receipt_long',           'color' => 'rose',   'emoji' => '🔴', 'normalSide' => 'Debit'],
];

$typeOrder = ['asset', 'liability', 'equity', 'revenue', 'cogs', 'expense'];
$totalAccounts = 0;
$totalWithBalance = 0;
foreach ($accounts as $type => $accs) {
    $totalAccounts += count($accs);
    foreach ($accs as $a) { if (($a['balance'] ?? 0) != 0) $totalWithBalance++; }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Chart of Accounts' ?> — IndoOcean ERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Round" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family:'Inter',sans-serif; } [x-cloak] { display:none!important; }
        @keyframes fadeInUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
        .anim { animation: fadeInUp .4s ease both; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>
    <main class="ml-64 flex-1">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto" x-data="{ showForm: false, editItem: null }">

            <!-- Header -->
            <div class="flex items-center justify-between mb-6 anim">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">📋 Bagan Akun (Chart of Accounts)</h1>
                    <p class="text-slate-500 text-sm mt-1">Standar IFRS/PSAK — <?= $totalAccounts ?> akun terdaftar, <?= $totalWithBalance ?> akun memiliki saldo</p>
                </div>
                <a href="<?= BASE_URL ?>finance/create-account" class="flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors">
                    <span class="material-icons text-lg">add</span> Tambah Akun
                </a>
            </div>

            <?php if (!empty($flash)): ?>
            <div class="mb-4 p-4 rounded-xl border <?= $flash['type'] === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-700' ?>">
                <div class="flex items-center gap-2"><span class="material-icons text-lg"><?= $flash['type'] === 'success' ? 'check_circle' : 'error' ?></span> <?= htmlspecialchars($flash['message']) ?></div>
            </div>
            <?php endif; ?>

            <!-- ═══════ BALANCE SUMMARY ═══════ -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6 anim">
                <?php foreach ($typeOrder as $type):
                    $ti = $typeInfo[$type];
                    $bal = $totals[$type] ?? 0;
                    $cnt = count($accounts[$type] ?? []);
                ?>
                <div class="bg-white rounded-xl border border-slate-100 p-4 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-icons text-<?= $ti['color'] ?>-500 text-base"><?= $ti['icon'] ?></span>
                        <span class="text-[10px] font-semibold text-slate-500"><?= $ti['label'] ?></span>
                    </div>
                    <p class="text-sm font-bold text-slate-800">Rp <?= number_format(abs($bal), 0, ',', '.') ?></p>
                    <p class="text-[10px] text-slate-400"><?= $cnt ?> akun</p>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- ═══════ ACCOUNTING EQUATION ═══════ -->
            <?php
            $totalAssets = $totals['asset'] ?? 0;
            $totalLiabilities = $totals['liability'] ?? 0;
            $totalEquity = $totals['equity'] ?? 0;
            ?>
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-100 p-5 mb-6 anim">
                <h3 class="text-xs font-semibold text-blue-700 mb-3">⚖️ Persamaan Akuntansi (Accounting Equation)</h3>
                <div class="flex items-center justify-center gap-3 flex-wrap">
                    <div class="text-center">
                        <p class="text-xs text-slate-500">Aset</p>
                        <p class="text-lg font-bold text-blue-700">Rp <?= number_format(abs($totalAssets), 0, ',', '.') ?></p>
                    </div>
                    <span class="text-xl font-bold text-slate-400">=</span>
                    <div class="text-center">
                        <p class="text-xs text-slate-500">Kewajiban</p>
                        <p class="text-lg font-bold text-red-600">Rp <?= number_format(abs($totalLiabilities), 0, ',', '.') ?></p>
                    </div>
                    <span class="text-xl font-bold text-slate-400">+</span>
                    <div class="text-center">
                        <p class="text-xs text-slate-500">Ekuitas</p>
                        <p class="text-lg font-bold text-purple-700">Rp <?= number_format(abs($totalEquity), 0, ',', '.') ?></p>
                    </div>
                    <?php
                    $diff = abs($totalAssets) - (abs($totalLiabilities) + abs($totalEquity));
                    if (abs($diff) < 1): ?>
                    <span class="ml-4 px-3 py-1 text-xs font-bold bg-emerald-100 text-emerald-700 rounded-full">✓ Balanced</span>
                    <?php else: ?>
                    <span class="ml-4 px-3 py-1 text-xs font-bold bg-amber-100 text-amber-700 rounded-full">Δ Rp <?= number_format(abs($diff), 0, ',', '.') ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ═══════ ACCOUNT GROUPS ═══════ -->
            <?php foreach ($typeOrder as $type):
                $ti = $typeInfo[$type];
                $accs = $accounts[$type] ?? [];
                if (empty($accs)) continue;
                $groupTotal = $totals[$type] ?? 0;
            ?>
            <div class="mb-5 anim" x-data="{ open: true }">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <!-- Group header -->
                    <button @click="open = !open" class="w-full flex items-center justify-between px-5 py-4 bg-<?= $ti['color'] ?>-50 hover:bg-<?= $ti['color'] ?>-100 transition-colors cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-<?= $ti['color'] ?>-100 flex items-center justify-center">
                                <span class="material-icons text-<?= $ti['color'] ?>-600 text-sm"><?= $ti['icon'] ?></span>
                            </div>
                            <div class="text-left">
                                <span class="text-sm font-bold text-slate-800"><?= $ti['emoji'] ?> <?= $ti['label'] ?></span>
                                <span class="text-xs text-slate-500 ml-2">(<?= count($accs) ?> akun)</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-bold text-<?= $ti['color'] ?>-700">Rp <?= number_format(abs($groupTotal), 0, ',', '.') ?></span>
                            <span class="material-icons text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                        </div>
                    </button>

                    <!-- Account table -->
                    <div x-show="open" x-collapse>
                        <table class="w-full">
                            <thead>
                                <tr class="text-[10px] uppercase text-slate-400 border-b border-slate-50">
                                    <th class="px-5 py-2 text-left">Kode</th>
                                    <th class="px-3 py-2 text-left">Nama Akun</th>
                                    <th class="px-3 py-2 text-left">Name (EN)</th>
                                    <th class="px-3 py-2 text-center">Transaksi</th>
                                    <th class="px-3 py-2 text-right">Debit</th>
                                    <th class="px-3 py-2 text-right">Credit</th>
                                    <th class="px-3 py-2 text-right">Saldo</th>
                                    <th class="px-3 py-2 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($accs as $acc):
                                    $hasBalance = abs($acc['balance']) > 0;
                                    $hasTx = $acc['tx_count'] > 0;
                                ?>
                                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors <?= $hasBalance ? '' : 'opacity-60' ?>">
                                    <td class="px-5 py-3">
                                        <span class="font-mono text-xs font-semibold text-<?= $ti['color'] ?>-600"><?= htmlspecialchars($acc['code']) ?></span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-xs font-medium text-slate-700"><?= htmlspecialchars($acc['name']) ?></span>
                                            <?php if ($acc['is_system']): ?>
                                            <span class="px-1.5 py-0.5 text-[9px] font-bold bg-slate-100 text-slate-500 rounded">SYSTEM</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-xs text-slate-400"><?= htmlspecialchars($acc['name_en'] ?? '') ?></td>
                                    <td class="px-3 py-3 text-center">
                                        <?php if ($hasTx): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-semibold bg-blue-50 text-blue-600 rounded-full">
                                            <span class="material-icons text-[10px]">swap_horiz</span> <?= $acc['tx_count'] ?>
                                        </span>
                                        <?php else: ?>
                                        <span class="text-[10px] text-slate-300">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-3 py-3 text-right">
                                        <?php if ($acc['total_debit'] > 0): ?>
                                        <span class="text-xs font-medium text-slate-600"><?= number_format($acc['total_debit'], 0, ',', '.') ?></span>
                                        <?php else: ?>
                                        <span class="text-xs text-slate-300">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-3 py-3 text-right">
                                        <?php if ($acc['total_credit'] > 0): ?>
                                        <span class="text-xs font-medium text-slate-600"><?= number_format($acc['total_credit'], 0, ',', '.') ?></span>
                                        <?php else: ?>
                                        <span class="text-xs text-slate-300">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-3 py-3 text-right">
                                        <?php if ($hasBalance): ?>
                                        <span class="text-xs font-bold <?= $acc['balance'] >= 0 ? 'text-slate-800' : 'text-red-600' ?>">
                                            Rp <?= number_format(abs($acc['balance']), 0, ',', '.') ?>
                                        </span>
                                        <?php else: ?>
                                        <span class="text-xs text-slate-300">Rp 0</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="px-2 py-0.5 text-[9px] font-bold rounded-full <?= $acc['is_active'] ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>">
                                            <?= $acc['is_active'] ? 'AKTIF' : 'OFF' ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="bg-<?= $ti['color'] ?>-50/50">
                                    <td colspan="4" class="px-5 py-3 text-xs font-bold text-slate-600">Total <?= $ti['label'] ?></td>
                                    <td class="px-3 py-3 text-right text-xs font-bold text-slate-700">
                                        <?php $gDebit = array_sum(array_column($accs, 'total_debit')); ?>
                                        <?= $gDebit > 0 ? number_format($gDebit, 0, ',', '.') : '—' ?>
                                    </td>
                                    <td class="px-3 py-3 text-right text-xs font-bold text-slate-700">
                                        <?php $gCredit = array_sum(array_column($accs, 'total_credit')); ?>
                                        <?= $gCredit > 0 ? number_format($gCredit, 0, ',', '.') : '—' ?>
                                    </td>
                                    <td class="px-3 py-3 text-right text-xs font-bold text-<?= $ti['color'] ?>-700">
                                        Rp <?= number_format(abs($groupTotal), 0, ',', '.') ?>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- ═══════ LEGEND ═══════ -->
            <div class="mt-6 bg-slate-100 rounded-xl p-4 anim">
                <h4 class="text-xs font-semibold text-slate-600 mb-2">📖 Keterangan</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-[11px] text-slate-500">
                    <div class="flex items-center gap-1.5">
                        <span class="px-1.5 py-0.5 text-[9px] font-bold bg-slate-100 text-slate-500 rounded">SYSTEM</span>
                        <span>Akun bawaan sistem, tidak bisa dihapus</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="material-icons text-blue-500 text-xs">swap_horiz</span>
                        <span>Jumlah transaksi jurnal pada akun</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="font-bold text-slate-700">Saldo</span>
                        <span>= Debit - Credit (atau sebaliknya)</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs">⚖️</span>
                        <span>Aset = Kewajiban + Ekuitas (harus balance)</span>
                    </div>
                </div>
            </div>

        </div>
    </main>
</body>
</html>
