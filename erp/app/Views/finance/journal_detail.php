<?php
/**
 * Journal Entry Detail
 */
$e = $entry;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> — IndoOcean ERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif}</style>
</head>
<body class="bg-slate-50 min-h-screen">
    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>
    <main class="ml-64 flex-1">
        <div class="p-6 lg:p-8 max-w-4xl mx-auto">
            <nav class="mb-4 flex items-center gap-2 text-xs text-slate-500">
                <a href="<?= BASE_URL ?>finance" class="hover:text-blue-600">Keuangan</a>
                <span class="material-icons text-[14px]">chevron_right</span>
                <a href="<?= BASE_URL ?>finance/journal" class="hover:text-blue-600">General Ledger</a>
                <span class="material-icons text-[14px]">chevron_right</span>
                <span class="text-slate-700 font-medium"><?= htmlspecialchars($e['entry_no']) ?></span>
            </nav>

            <div class="flex items-center gap-3 mb-6">
                <h1 class="text-2xl font-bold text-slate-800"><?= htmlspecialchars($e['entry_no']) ?></h1>
                <span class="px-3 py-1 text-xs font-bold rounded-full <?= $e['is_auto'] ? 'bg-blue-100 text-blue-700' : 'bg-indigo-100 text-indigo-700' ?>">
                    <?= $e['is_auto'] ? 'AUTO' : 'MANUAL' ?>
                </span>
                <span class="px-3 py-1 text-xs font-bold rounded-full <?= $e['is_posted'] ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' ?>">
                    <?= $e['is_posted'] ? 'POSTED' : 'DRAFT' ?>
                </span>
            </div>

            <!-- Info -->
            <div class="bg-white rounded-2xl border border-slate-100 p-6 mb-6 shadow-sm">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div><p class="text-xs text-slate-500">Tanggal</p><p class="font-semibold text-slate-700"><?= date('d M Y', strtotime($e['entry_date'])) ?></p></div>
                    <div><p class="text-xs text-slate-500">Referensi</p><p class="font-semibold text-slate-700"><?= htmlspecialchars($e['reference_no'] ?? '-') ?></p></div>
                    <div><p class="text-xs text-slate-500">Sumber</p><p class="font-semibold text-slate-700 capitalize"><?= str_replace('_', ' ', $e['source_type']) ?></p></div>
                    <div><p class="text-xs text-slate-500">Deskripsi</p><p class="font-semibold text-slate-700"><?= htmlspecialchars($e['description'] ?? '-') ?></p></div>
                </div>
            </div>

            <!-- Journal Lines -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-6">
                <table class="w-full text-sm">
                    <thead><tr class="bg-slate-50 border-b border-slate-100">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-600 uppercase">Akun</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-600 uppercase">Keterangan</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-600 uppercase">Cost Center</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-600 uppercase">Debit</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-600 uppercase">Kredit</th>
                    </tr></thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($lines as $l): ?>
                        <tr>
                            <td class="px-5 py-3">
                                <span class="font-mono text-xs text-slate-500"><?= htmlspecialchars($l['account_code'] ?? '') ?></span>
                                <span class="text-slate-700 ml-1"><?= htmlspecialchars($l['account_name'] ?? 'Unknown') ?></span>
                            </td>
                            <td class="px-5 py-3 text-slate-600 text-xs"><?= htmlspecialchars($l['description'] ?? '-') ?></td>
                            <td class="px-5 py-3 text-slate-500 text-xs"><?= htmlspecialchars($l['cost_center_name'] ?? '-') ?></td>
                            <td class="px-5 py-3 text-right font-semibold <?= $l['debit'] > 0 ? 'text-slate-700' : 'text-slate-300' ?>"><?= number_format($l['debit'], 2) ?></td>
                            <td class="px-5 py-3 text-right font-semibold <?= $l['credit'] > 0 ? 'text-slate-700' : 'text-slate-300' ?>"><?= number_format($l['credit'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50 border-t-2 border-slate-200 font-bold text-sm">
                            <td colspan="3" class="px-5 py-3 text-right text-slate-600">TOTAL</td>
                            <td class="px-5 py-3 text-right text-slate-800"><?= number_format($e['total_debit'], 2) ?></td>
                            <td class="px-5 py-3 text-right text-slate-800"><?= number_format($e['total_credit'], 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <?php if (abs($e['total_debit'] - $e['total_credit']) < 0.01): ?>
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 text-sm text-emerald-700 flex items-center gap-2 mb-6">
                <span class="material-icons text-lg">check_circle</span> Jurnal seimbang — Debit = Kredit ✓
            </div>
            <?php else: ?>
            <div class="bg-red-50 border border-red-200 rounded-xl p-3 text-sm text-red-700 flex items-center gap-2 mb-6">
                <span class="material-icons text-lg">error</span> ⚠️ Jurnal TIDAK seimbang — Selisih: <?= number_format(abs($e['total_debit'] - $e['total_credit']), 2) ?>
            </div>
            <?php endif; ?>

            <a href="<?= BASE_URL ?>finance/journal" class="flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700">
                <span class="material-icons text-lg">arrow_back</span> Kembali ke General Ledger
            </a>
        </div>
    </main>
</body>
</html>
