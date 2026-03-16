<?php
/**
 * Bill Detail View
 */
$statusColors = ['unpaid' => 'bg-amber-100 text-amber-700', 'partial' => 'bg-indigo-100 text-indigo-700', 'paid' => 'bg-emerald-100 text-emerald-700', 'overdue' => 'bg-red-100 text-red-700', 'cancelled' => 'bg-red-50 text-red-500'];
$catLabels = ['mcu' => 'MCU', 'travel' => 'Travel', 'supplier' => 'Supplier', 'crew_welfare' => 'Crew Welfare', 'office' => 'Office', 'ship_chandler' => 'Ship Chandler', 'other' => 'Lainnya'];
$b = $bill;
$remaining = $b['total'] - $b['amount_paid'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> — IndoOcean ERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>body{font-family:'Inter',sans-serif} [x-cloak]{display:none!important}</style>
</head>
<body class="bg-slate-50 min-h-screen">
    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>
    <main class="ml-64 flex-1">
        <div class="p-6 lg:p-8 max-w-5xl mx-auto">
            <nav class="mb-4 flex items-center gap-2 text-xs text-slate-500">
                <a href="<?= BASE_URL ?>finance" class="hover:text-blue-600">Keuangan</a>
                <span class="material-icons text-[14px]">chevron_right</span>
                <a href="<?= BASE_URL ?>finance/bills" class="hover:text-blue-600">Bills</a>
                <span class="material-icons text-[14px]">chevron_right</span>
                <span class="text-slate-700 font-medium"><?= htmlspecialchars($b['bill_no']) ?></span>
            </nav>

            <?php if (!empty($flash)): ?>
            <div class="mb-4 p-4 rounded-xl border <?= $flash['type'] === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-700' ?>">
                <div class="flex items-center gap-2"><span class="material-icons text-lg"><?= $flash['type'] === 'success' ? 'check_circle' : 'error' ?></span> <?= htmlspecialchars($flash['message']) ?></div>
            </div>
            <?php endif; ?>

            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-slate-800"><?= htmlspecialchars($b['bill_no']) ?></h1>
                        <span class="px-3 py-1 text-xs font-bold rounded-full <?= $statusColors[$b['status']] ?? 'bg-slate-100 text-slate-600' ?>"><?= strtoupper($b['status']) ?></span>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-600"><?= $catLabels[$b['category']] ?? $b['category'] ?></span>
                    </div>
                    <p class="text-sm text-slate-500 mt-1"><?= htmlspecialchars($b['vendor_name']) ?></p>
                </div>
                <div class="flex gap-2">
                    <?php if ($b['status'] !== 'paid'): ?>
                    <a href="<?= BASE_URL ?>finance/edit-bill/<?= $b['id'] ?>" class="px-4 py-2 border border-slate-200 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-50 flex items-center gap-1">
                        <span class="material-icons text-sm">edit</span> Edit
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <!-- Info -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div><p class="text-xs text-slate-500">Tanggal Bill</p><p class="font-semibold text-slate-700"><?= date('d M Y', strtotime($b['bill_date'])) ?></p></div>
                            <div><p class="text-xs text-slate-500">Jatuh Tempo</p><p class="font-semibold text-slate-700"><?= date('d M Y', strtotime($b['due_date'])) ?></p></div>
                            <div><p class="text-xs text-slate-500">Mata Uang</p><p class="font-semibold text-slate-700"><?= $b['currency_code'] ?></p></div>
                            <div><p class="text-xs text-slate-500">Vendor</p><p class="font-semibold text-slate-700"><?= htmlspecialchars($b['vendor_name']) ?></p></div>
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-slate-50 border-b border-slate-100">
                                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-600">DESKRIPSI</th>
                                <th class="text-right px-5 py-3 text-xs font-semibold text-slate-600">QTY</th>
                                <th class="text-right px-5 py-3 text-xs font-semibold text-slate-600">HARGA</th>
                                <th class="text-right px-5 py-3 text-xs font-semibold text-slate-600">AMOUNT</th>
                            </tr></thead>
                            <tbody class="divide-y divide-slate-50">
                                <?php foreach ($items as $it): ?>
                                <tr><td class="px-5 py-3 text-slate-700"><?= htmlspecialchars($it['description']) ?></td><td class="px-5 py-3 text-right"><?= number_format($it['quantity'], 2) ?></td><td class="px-5 py-3 text-right"><?= number_format($it['unit_price'], 2) ?></td><td class="px-5 py-3 text-right font-semibold"><?= number_format($it['amount'], 2) ?></td></tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="border-t border-slate-100 p-5">
                            <div class="max-w-xs ml-auto space-y-1 text-sm">
                                <div class="flex justify-between"><span class="text-slate-500">Subtotal</span><span><?= number_format($b['subtotal'], 2) ?></span></div>
                                <?php if ($b['tax_amount'] > 0): ?><div class="flex justify-between"><span class="text-slate-500">Pajak (<?= $b['tax_percent'] ?>%)</span><span><?= number_format($b['tax_amount'], 2) ?></span></div><?php endif; ?>
                                <hr class="border-slate-100 my-2">
                                <div class="flex justify-between font-bold text-base"><span>Total</span><span><?= $b['currency_code'] ?> <?= number_format($b['total'], 2) ?></span></div>
                                <div class="flex justify-between text-emerald-600"><span>Terbayar</span><span><?= number_format($b['amount_paid'], 2) ?></span></div>
                                <div class="flex justify-between font-bold <?= $remaining > 0 ? 'text-amber-600' : 'text-emerald-600' ?>"><span>Sisa</span><span><?= number_format($remaining, 2) ?></span></div>
                            </div>
                        </div>
                    </div>

                    <?php if ($b['receipt_file']): ?>
                    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
                        <p class="text-xs font-semibold text-slate-500 mb-2">📎 Bukti / Receipt</p>
                        <a href="<?= BASE_URL ?>uploads/finance/receipts/<?= $b['receipt_file'] ?>" target="_blank" class="text-sm text-blue-600 hover:underline flex items-center gap-1">
                            <span class="material-icons text-sm">attach_file</span> <?= htmlspecialchars($b['receipt_file']) ?>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Payments -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
                        <h3 class="text-sm font-semibold text-slate-700 mb-3 flex items-center gap-2"><span class="material-icons text-rose-500 text-lg">payments</span> Riwayat Pembayaran</h3>
                        <?php if (!empty($payments)): ?>
                        <div class="space-y-2">
                            <?php foreach ($payments as $pmt): ?>
                            <div class="p-3 rounded-xl bg-rose-50 border border-rose-100">
                                <div class="flex justify-between">
                                    <div><p class="text-xs font-mono text-rose-700"><?= htmlspecialchars($pmt['payment_no']) ?></p><p class="text-xs text-rose-600"><?= date('d M Y', strtotime($pmt['payment_date'])) ?></p></div>
                                    <p class="text-sm font-bold text-rose-700"><?= number_format($pmt['amount'], 2) ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?><p class="text-xs text-slate-400 text-center py-4">Belum ada pembayaran</p><?php endif; ?>
                    </div>

                    <?php if (in_array($b['status'], ['unpaid', 'partial', 'overdue']) && $remaining > 0): ?>
                    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm" x-data="{ show: false }">
                        <button @click="show = !show" class="w-full flex items-center justify-between text-sm font-semibold text-rose-600">
                            <span class="flex items-center gap-2"><span class="material-icons text-lg">add_circle</span> Catat Pembayaran</span>
                            <span class="material-icons text-sm" :class="show && 'rotate-180'">expand_more</span>
                        </button>
                        <form x-show="show" x-collapse method="POST" action="<?= BASE_URL ?>finance/record-bill-payment" class="mt-4 space-y-3">
                            <input type="hidden" name="bill_id" value="<?= $b['id'] ?>">
                            <div><label class="block text-xs font-medium text-slate-600 mb-1">Jumlah</label><input type="number" name="amount" value="<?= $remaining ?>" step="0.01" max="<?= $remaining ?>" required class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2"></div>
                            <div><label class="block text-xs font-medium text-slate-600 mb-1">Tanggal</label><input type="date" name="payment_date" value="<?= date('Y-m-d') ?>" required class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2"></div>
                            <div><label class="block text-xs font-medium text-slate-600 mb-1">Metode</label>
                                <select name="payment_method" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2"><option value="bank_transfer">Transfer Bank</option><option value="cash">Tunai</option><option value="cheque">Cek</option></select>
                            </div>
                            <div><label class="block text-xs font-medium text-slate-600 mb-1">Akun Bank</label>
                                <select name="bank_account_id" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2">
                                    <?php foreach ($bank_accounts ?? [] as $ba): ?><option value="<?= $ba['id'] ?>">[<?= $ba['code'] ?>] <?= htmlspecialchars($ba['name']) ?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div><label class="block text-xs font-medium text-slate-600 mb-1">No. Referensi</label><input type="text" name="reference_number" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2"></div>
                            <button type="submit" class="w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-xl">💵 Bayar</button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
