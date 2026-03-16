<?php
/**
 * Invoices List - Accounts Receivable
 */
$statusColors = [
    'draft' => 'bg-slate-100 text-slate-600',
    'sent' => 'bg-blue-100 text-blue-700',
    'unpaid' => 'bg-amber-100 text-amber-700',
    'partial' => 'bg-indigo-100 text-indigo-700',
    'paid' => 'bg-emerald-100 text-emerald-700',
    'overdue' => 'bg-red-100 text-red-700',
    'cancelled' => 'bg-red-50 text-red-500',
    'void' => 'bg-slate-50 text-slate-400',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Invoices' ?> — IndoOcean ERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Round" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>body{font-family:'Inter',sans-serif} [x-cloak]{display:none!important}
    @keyframes fadeInUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}} .animate-fade-in{animation:fadeInUp .5s ease both}</style>
</head>
<body class="bg-slate-50 min-h-screen">
    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

    <main class="ml-64 flex-1">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6 animate-fade-in">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">📄 Invoice — Accounts Receivable</h1>
                    <p class="text-slate-500 text-sm mt-1">Kelola tagihan ke klien</p>
                </div>
                <a href="<?= BASE_URL ?>finance/create-invoice" class="flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors">
                    <span class="material-icons text-lg">add</span> Buat Invoice
                </a>
            </div>

            <!-- Flash -->
            <?php if (!empty($flash)): ?>
            <div class="mb-4 p-4 rounded-xl border <?= $flash['type'] === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-700' ?>">
                <div class="flex items-center gap-2"><span class="material-icons text-lg"><?= $flash['type'] === 'success' ? 'check_circle' : 'error' ?></span> <?= htmlspecialchars($flash['message']) ?></div>
            </div>
            <?php endif; ?>

            <!-- Filters -->
            <div class="bg-white rounded-2xl border border-slate-100 p-4 mb-6 shadow-sm" x-data="{ open: <?= !empty($filters['status'] ?? $filters['date_from'] ?? '') ? 'true' : 'false' ?> }">
                <button @click="open = !open" class="flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-slate-800">
                    <span class="material-icons text-lg">filter_list</span> Filter
                    <span class="material-icons text-sm" :class="open && 'rotate-180'">expand_more</span>
                </button>
                <form x-show="open" x-collapse method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                    <select name="status" class="text-sm rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <?php foreach (['draft','unpaid','partial','paid','cancelled'] as $s): ?>
                        <option value="<?= $s ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="date" name="date_from" value="<?= $filters['date_from'] ?? '' ?>" class="text-sm rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-blue-500" placeholder="From">
                    <input type="date" name="date_to" value="<?= $filters['date_to'] ?? '' ?>" class="text-sm rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-blue-500" placeholder="To">
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg px-4 py-2">Terapkan</button>
                        <a href="<?= BASE_URL ?>finance/invoices" class="px-3 py-2 border border-slate-200 text-slate-600 rounded-lg text-sm hover:bg-slate-50">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden animate-fade-in">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="text-left px-5 py-3 font-semibold text-slate-600 text-xs uppercase">No Invoice</th>
                                <th class="text-left px-5 py-3 font-semibold text-slate-600 text-xs uppercase">Klien</th>
                                <th class="text-left px-5 py-3 font-semibold text-slate-600 text-xs uppercase">Vessel</th>
                                <th class="text-left px-5 py-3 font-semibold text-slate-600 text-xs uppercase">Tanggal</th>
                                <th class="text-left px-5 py-3 font-semibold text-slate-600 text-xs uppercase">Jatuh Tempo</th>
                                <th class="text-right px-5 py-3 font-semibold text-slate-600 text-xs uppercase">Total</th>
                                <th class="text-right px-5 py-3 font-semibold text-slate-600 text-xs uppercase">Sisa</th>
                                <th class="text-center px-5 py-3 font-semibold text-slate-600 text-xs uppercase">Status</th>
                                <th class="text-center px-5 py-3 font-semibold text-slate-600 text-xs uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php if (empty($invoices)): ?>
                            <tr><td colspan="9" class="text-center py-12 text-slate-400">
                                <span class="material-icons text-4xl mb-2 block">description</span>
                                Belum ada invoice. <a href="<?= BASE_URL ?>finance/create-invoice" class="text-blue-600 hover:underline">Buat sekarang</a>
                            </td></tr>
                            <?php else: ?>
                            <?php foreach ($invoices as $inv): ?>
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="px-5 py-3">
                                    <a href="<?= BASE_URL ?>finance/invoice/<?= $inv['id'] ?>" class="text-blue-600 font-semibold hover:underline"><?= htmlspecialchars($inv['invoice_no']) ?></a>
                                </td>
                                <td class="px-5 py-3 text-slate-700"><?= htmlspecialchars($inv['client_name'] ?? '-') ?></td>
                                <td class="px-5 py-3 text-slate-500 text-xs"><?= htmlspecialchars($inv['vessel_name'] ?? '-') ?></td>
                                <td class="px-5 py-3 text-slate-600 text-xs"><?= date('d M Y', strtotime($inv['invoice_date'])) ?></td>
                                <td class="px-5 py-3 text-xs <?= strtotime($inv['due_date']) < time() && !in_array($inv['status'], ['paid','cancelled']) ? 'text-red-600 font-semibold' : 'text-slate-600' ?>">
                                    <?= date('d M Y', strtotime($inv['due_date'])) ?>
                                </td>
                                <td class="px-5 py-3 text-right font-semibold text-slate-700">
                                    <?= $inv['currency_code'] ?> <?= number_format($inv['total'], 2) ?>
                                </td>
                                <td class="px-5 py-3 text-right text-slate-500">
                                    <?= number_format($inv['total'] - $inv['amount_paid'], 2) ?>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-block px-2.5 py-1 text-[10px] font-bold rounded-full <?= $statusColors[$inv['status']] ?? $statusColors['draft'] ?>">
                                        <?= strtoupper($inv['status']) ?>
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <a href="<?= BASE_URL ?>finance/invoice/<?= $inv['id'] ?>" class="text-blue-600 hover:text-blue-800" title="Detail">
                                        <span class="material-icons text-lg">visibility</span>
                                    </a>
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
