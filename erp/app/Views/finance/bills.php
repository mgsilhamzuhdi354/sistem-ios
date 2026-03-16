<?php
/**
 * Bills List - Accounts Payable
 */
$statusColors = [
    'draft' => 'bg-slate-100 text-slate-600', 'unpaid' => 'bg-amber-100 text-amber-700',
    'partial' => 'bg-indigo-100 text-indigo-700', 'paid' => 'bg-emerald-100 text-emerald-700',
    'overdue' => 'bg-red-100 text-red-700', 'cancelled' => 'bg-red-50 text-red-500',
];
$catLabels = ['mcu' => 'MCU', 'travel' => 'Perjalanan', 'supplier' => 'Supplier', 'crew_welfare' => 'Crew Welfare', 'office' => 'Kantor', 'ship_chandler' => 'Ship Chandler', 'other' => 'Lainnya'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Bills' ?> — IndoOcean ERP</title>
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
                    <h1 class="text-2xl font-bold text-slate-800">🧾 Bills — Accounts Payable</h1>
                    <p class="text-slate-500 text-sm mt-1">Catat tagihan dari vendor & supplier</p>
                </div>
                <a href="<?= BASE_URL ?>finance/create-bill" class="flex items-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors">
                    <span class="material-icons text-lg">add</span> Catat Bill
                </a>
            </div>

            <?php if (!empty($flash)): ?>
            <div class="mb-4 p-4 rounded-xl border <?= $flash['type'] === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-700' ?>">
                <div class="flex items-center gap-2"><span class="material-icons text-lg"><?= $flash['type'] === 'success' ? 'check_circle' : 'error' ?></span> <?= htmlspecialchars($flash['message']) ?></div>
            </div>
            <?php endif; ?>

            <!-- Filters -->
            <div class="bg-white rounded-2xl border border-slate-100 p-4 mb-6 shadow-sm" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-slate-800">
                    <span class="material-icons text-lg">filter_list</span> Filter
                    <span class="material-icons text-sm" :class="open && 'rotate-180'">expand_more</span>
                </button>
                <form x-show="open" x-collapse method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                    <select name="status" class="text-sm rounded-lg border border-slate-200 px-3 py-2">
                        <option value="">Semua Status</option>
                        <?php foreach (['unpaid','partial','paid','cancelled'] as $s): ?>
                        <option value="<?= $s ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="category" class="text-sm rounded-lg border border-slate-200 px-3 py-2">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($catLabels as $ck => $cl): ?>
                        <option value="<?= $ck ?>" <?= ($filters['category'] ?? '') === $ck ? 'selected' : '' ?>><?= $cl ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="date" name="date_from" value="<?= $filters['date_from'] ?? '' ?>" class="text-sm rounded-lg border border-slate-200 px-3 py-2">
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-rose-600 hover:bg-rose-700 text-white text-sm font-medium rounded-lg px-4 py-2">Terapkan</button>
                        <a href="<?= BASE_URL ?>finance/bills" class="px-3 py-2 border border-slate-200 text-slate-600 rounded-lg text-sm hover:bg-slate-50">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden animate-fade-in">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="bg-slate-50 border-b border-slate-100">
                            <th class="text-left px-5 py-3 font-semibold text-slate-600 text-xs uppercase">No Bill</th>
                            <th class="text-left px-5 py-3 font-semibold text-slate-600 text-xs uppercase">Vendor</th>
                            <th class="text-left px-5 py-3 font-semibold text-slate-600 text-xs uppercase">Kategori</th>
                            <th class="text-left px-5 py-3 font-semibold text-slate-600 text-xs uppercase">Tanggal</th>
                            <th class="text-right px-5 py-3 font-semibold text-slate-600 text-xs uppercase">Total</th>
                            <th class="text-right px-5 py-3 font-semibold text-slate-600 text-xs uppercase">Sisa</th>
                            <th class="text-center px-5 py-3 font-semibold text-slate-600 text-xs uppercase">Status</th>
                            <th class="text-center px-5 py-3 font-semibold text-slate-600 text-xs uppercase">Aksi</th>
                        </tr></thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php if (empty($bills)): ?>
                            <tr><td colspan="8" class="text-center py-12 text-slate-400">
                                <span class="material-icons text-4xl mb-2 block">receipt</span>
                                Belum ada tagihan. <a href="<?= BASE_URL ?>finance/create-bill" class="text-rose-600 hover:underline">Catat sekarang</a>
                            </td></tr>
                            <?php else: ?>
                            <?php foreach ($bills as $b): ?>
                            <tr class="hover:bg-rose-50/30 transition-colors">
                                <td class="px-5 py-3"><a href="<?= BASE_URL ?>finance/bill/<?= $b['id'] ?>" class="text-rose-600 font-semibold hover:underline"><?= htmlspecialchars($b['bill_no']) ?></a></td>
                                <td class="px-5 py-3 text-slate-700"><?= htmlspecialchars($b['vendor_name']) ?></td>
                                <td class="px-5 py-3"><span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-600"><?= $catLabels[$b['category']] ?? $b['category'] ?></span></td>
                                <td class="px-5 py-3 text-slate-600 text-xs"><?= date('d M Y', strtotime($b['bill_date'])) ?></td>
                                <td class="px-5 py-3 text-right font-semibold text-slate-700"><?= number_format($b['total'], 2) ?></td>
                                <td class="px-5 py-3 text-right text-slate-500"><?= number_format($b['total'] - $b['amount_paid'], 2) ?></td>
                                <td class="px-5 py-3 text-center"><span class="px-2 py-1 text-[10px] font-bold rounded-full <?= $statusColors[$b['status']] ?? 'bg-slate-100 text-slate-600' ?>"><?= strtoupper($b['status']) ?></span></td>
                                <td class="px-5 py-3 text-center"><a href="<?= BASE_URL ?>finance/bill/<?= $b['id'] ?>" class="text-rose-600 hover:text-rose-800"><span class="material-icons text-lg">visibility</span></a></td>
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
