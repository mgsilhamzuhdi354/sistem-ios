<?php
/**
 * General Ledger — Journal Entries List
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Journal' ?> — IndoOcean ERP</title>
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
        <div class="p-6 lg:p-8 max-w-7xl mx-auto">
            <div class="flex items-center justify-between mb-6 animate-fade-in">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">📒 General Ledger</h1>
                    <p class="text-slate-500 text-sm mt-1">Jurnal double-entry otomatis & manual</p>
                </div>
                <a href="<?= BASE_URL ?>finance/create-journal" class="flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors">
                    <span class="material-icons text-lg">add</span> Jurnal Manual
                </a>
            </div>

            <?php if (!empty($flash)): ?>
            <div class="mb-4 p-4 rounded-xl border <?= $flash['type'] === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-700' ?>">
                <div class="flex items-center gap-2"><span class="material-icons text-lg"><?= $flash['type'] === 'success' ? 'check_circle' : 'error' ?></span> <?= htmlspecialchars($flash['message']) ?></div>
            </div>
            <?php endif; ?>

            <!-- Filters -->
            <div class="bg-white rounded-2xl border border-slate-100 p-4 mb-6 shadow-sm" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 text-sm font-medium text-slate-600"><span class="material-icons text-lg">filter_list</span> Filter</button>
                <form x-show="open" x-collapse method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                    <input type="date" name="date_from" value="<?= $filters['date_from'] ?? '' ?>" class="text-sm rounded-lg border border-slate-200 px-3 py-2">
                    <input type="date" name="date_to" value="<?= $filters['date_to'] ?? '' ?>" class="text-sm rounded-lg border border-slate-200 px-3 py-2">
                    <select name="source_type" class="text-sm rounded-lg border border-slate-200 px-3 py-2">
                        <option value="">Semua Sumber</option>
                        <?php foreach (['manual' => 'Manual', 'invoice' => 'Invoice', 'invoice_payment' => 'Bayar Invoice', 'bill' => 'Bill', 'bill_payment' => 'Bayar Bill', 'payroll' => 'Payroll'] as $sv => $sl): ?>
                        <option value="<?= $sv ?>" <?= ($filters['source_type'] ?? '') === $sv ? 'selected' : '' ?>><?= $sl ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg px-4 py-2">Filter</button>
                        <a href="<?= BASE_URL ?>finance/journal" class="px-3 py-2 border border-slate-200 text-slate-600 rounded-lg text-sm hover:bg-slate-50">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden animate-fade-in">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="bg-slate-50 border-b border-slate-100">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-600 uppercase">No Jurnal</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-600 uppercase">Tanggal</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-600 uppercase">Deskripsi</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-slate-600 uppercase">Sumber</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-slate-600 uppercase">Debit</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-slate-600 uppercase">Kredit</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-slate-600 uppercase">Status</th>
                        </tr></thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php if (empty($entries)): ?>
                            <tr><td colspan="7" class="text-center py-12 text-slate-400"><span class="material-icons text-4xl mb-2 block">menu_book</span>Belum ada jurnal</td></tr>
                            <?php else: ?>
                            <?php
                            $sourceLabels = ['manual' => ['Manual', 'bg-indigo-100 text-indigo-700'], 'invoice' => ['Invoice', 'bg-emerald-100 text-emerald-700'], 'invoice_payment' => ['Bayar AR', 'bg-blue-100 text-blue-700'], 'bill' => ['Bill', 'bg-rose-100 text-rose-700'], 'bill_payment' => ['Bayar AP', 'bg-amber-100 text-amber-700'], 'payroll' => ['Payroll', 'bg-purple-100 text-purple-700']];
                            foreach ($entries as $e): ?>
                            <tr class="hover:bg-indigo-50/30 transition-colors cursor-pointer" onclick="window.location='<?= BASE_URL ?>finance/view-journal/<?= $e['id'] ?>'">
                                <td class="px-5 py-3"><span class="text-indigo-600 font-semibold font-mono text-xs"><?= htmlspecialchars($e['entry_no']) ?></span></td>
                                <td class="px-5 py-3 text-slate-600 text-xs"><?= date('d M Y', strtotime($e['entry_date'])) ?></td>
                                <td class="px-5 py-3 text-slate-700 max-w-xs truncate"><?= htmlspecialchars($e['description'] ?? '-') ?></td>
                                <td class="px-5 py-3 text-center">
                                    <?php $src = $sourceLabels[$e['source_type']] ?? ['Other', 'bg-slate-100 text-slate-600']; ?>
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full <?= $src[1] ?>"><?= $src[0] ?></span>
                                </td>
                                <td class="px-5 py-3 text-right font-semibold text-slate-700"><?= number_format($e['total_debit'], 2) ?></td>
                                <td class="px-5 py-3 text-right font-semibold text-slate-700"><?= number_format($e['total_credit'], 2) ?></td>
                                <td class="px-5 py-3 text-center">
                                    <?php if ($e['is_posted']): ?>
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-700">POSTED</span>
                                    <?php else: ?>
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-600">DRAFT</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
