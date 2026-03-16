<?php
/**
 * Invoice Detail View
 */
$statusColors = [
    'draft' => 'bg-slate-100 text-slate-600', 'sent' => 'bg-blue-100 text-blue-700',
    'unpaid' => 'bg-amber-100 text-amber-700', 'partial' => 'bg-indigo-100 text-indigo-700',
    'paid' => 'bg-emerald-100 text-emerald-700', 'overdue' => 'bg-red-100 text-red-700',
    'cancelled' => 'bg-red-50 text-red-500', 'void' => 'bg-slate-50 text-slate-400',
];
$inv = $invoice;
$remaining = $inv['total'] - $inv['amount_paid'];
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
            <!-- Breadcrumb -->
            <nav class="mb-4 flex items-center gap-2 text-xs text-slate-500">
                <a href="<?= BASE_URL ?>finance" class="hover:text-blue-600">Keuangan</a>
                <span class="material-icons text-[14px]">chevron_right</span>
                <a href="<?= BASE_URL ?>finance/invoices" class="hover:text-blue-600">Invoices</a>
                <span class="material-icons text-[14px]">chevron_right</span>
                <span class="text-slate-700 font-medium"><?= htmlspecialchars($inv['invoice_no']) ?></span>
            </nav>

            <!-- Flash -->
            <?php if (!empty($flash)): ?>
            <div class="mb-4 p-4 rounded-xl border <?= $flash['type'] === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-700' ?>">
                <div class="flex items-center gap-2"><span class="material-icons text-lg"><?= $flash['type'] === 'success' ? 'check_circle' : 'error' ?></span> <?= htmlspecialchars($flash['message']) ?></div>
            </div>
            <?php endif; ?>

            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-slate-800"><?= htmlspecialchars($inv['invoice_no']) ?></h1>
                        <span class="px-3 py-1 text-xs font-bold rounded-full <?= $statusColors[$inv['status']] ?? $statusColors['draft'] ?>"><?= strtoupper($inv['status']) ?></span>
                    </div>
                    <p class="text-sm text-slate-500 mt-1"><?= htmlspecialchars($inv['client_name'] ?? '-') ?></p>
                </div>
                <div class="flex gap-2">
                    <?php if ($inv['status'] === 'draft'): ?>
                    <form method="POST" action="<?= BASE_URL ?>finance/mark-invoice-sent/<?= $inv['id'] ?>" class="inline">
                        <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl flex items-center gap-1">
                            <span class="material-icons text-sm">send</span> Kirim
                        </button>
                    </form>
                    <a href="<?= BASE_URL ?>finance/edit-invoice/<?= $inv['id'] ?>" class="px-4 py-2 border border-slate-200 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-50 flex items-center gap-1">
                        <span class="material-icons text-sm">edit</span> Edit
                    </a>
                    <?php endif; ?>
                    <?php if (in_array($inv['status'], ['draft', 'unpaid'])): ?>
                    <form method="POST" action="<?= BASE_URL ?>finance/cancel-invoice/<?= $inv['id'] ?>" onsubmit="return confirm('Batalkan invoice ini?')">
                        <button class="px-4 py-2 border border-red-200 text-red-600 text-sm font-medium rounded-xl hover:bg-red-50 flex items-center gap-1">
                            <span class="material-icons text-sm">cancel</span> Batal
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left: Invoice Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Info -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div><p class="text-xs text-slate-500">Tanggal Invoice</p><p class="font-semibold text-slate-700"><?= date('d M Y', strtotime($inv['invoice_date'])) ?></p></div>
                            <div><p class="text-xs text-slate-500">Jatuh Tempo</p><p class="font-semibold <?= strtotime($inv['due_date']) < time() && !in_array($inv['status'], ['paid','cancelled']) ? 'text-red-600' : 'text-slate-700' ?>"><?= date('d M Y', strtotime($inv['due_date'])) ?></p></div>
                            <div><p class="text-xs text-slate-500">Mata Uang</p><p class="font-semibold text-slate-700"><?= $inv['currency_code'] ?></p></div>
                            <div><p class="text-xs text-slate-500">Vessel</p><p class="font-semibold text-slate-700"><?= htmlspecialchars($inv['vessel_name'] ?? '-') ?></p></div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-slate-50 border-b border-slate-100">
                                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-600 uppercase">Deskripsi</th>
                                <th class="text-right px-5 py-3 text-xs font-semibold text-slate-600 uppercase">Qty</th>
                                <th class="text-right px-5 py-3 text-xs font-semibold text-slate-600 uppercase">Harga</th>
                                <th class="text-right px-5 py-3 text-xs font-semibold text-slate-600 uppercase">Amount</th>
                            </tr></thead>
                            <tbody class="divide-y divide-slate-50">
                                <?php foreach ($items as $it): ?>
                                <tr class="hover:bg-blue-50/30">
                                    <td class="px-5 py-3 text-slate-700"><?= htmlspecialchars($it['description']) ?></td>
                                    <td class="px-5 py-3 text-right text-slate-600"><?= number_format($it['quantity'], 2) ?></td>
                                    <td class="px-5 py-3 text-right text-slate-600"><?= number_format($it['unit_price'], 2) ?></td>
                                    <td class="px-5 py-3 text-right font-semibold text-slate-700"><?= number_format($it['amount'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="border-t border-slate-100 p-5">
                            <div class="max-w-xs ml-auto space-y-1 text-sm">
                                <div class="flex justify-between"><span class="text-slate-500">Subtotal</span><span class="text-slate-700"><?= number_format($inv['subtotal'], 2) ?></span></div>
                                <?php if ($inv['discount_amount'] > 0): ?>
                                <div class="flex justify-between"><span class="text-slate-500">Diskon (<?= $inv['discount_percent'] ?>%)</span><span class="text-red-600">-<?= number_format($inv['discount_amount'], 2) ?></span></div>
                                <?php endif; ?>
                                <?php if ($inv['tax_amount'] > 0): ?>
                                <div class="flex justify-between"><span class="text-slate-500">PPN (<?= $inv['tax_percent'] ?>%)</span><span class="text-slate-700"><?= number_format($inv['tax_amount'], 2) ?></span></div>
                                <?php endif; ?>
                                <hr class="border-slate-100 my-2">
                                <div class="flex justify-between text-base font-bold"><span class="text-slate-700">Total</span><span class="text-slate-800"><?= $inv['currency_code'] ?> <?= number_format($inv['total'], 2) ?></span></div>
                                <div class="flex justify-between text-emerald-600"><span>Terbayar</span><span><?= number_format($inv['amount_paid'], 2) ?></span></div>
                                <div class="flex justify-between font-bold <?= $remaining > 0 ? 'text-amber-600' : 'text-emerald-600' ?>"><span>Sisa</span><span><?= number_format($remaining, 2) ?></span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <?php if (!empty($inv['terms']) || !empty($inv['notes'])): ?>
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                        <?php if ($inv['terms']): ?><div class="mb-3"><p class="text-xs font-semibold text-slate-500 mb-1">Syarat & Ketentuan</p><p class="text-sm text-slate-600"><?= nl2br(htmlspecialchars($inv['terms'])) ?></p></div><?php endif; ?>
                        <?php if ($inv['notes']): ?><div><p class="text-xs font-semibold text-slate-500 mb-1">Catatan</p><p class="text-sm text-slate-600"><?= nl2br(htmlspecialchars($inv['notes'])) ?></p></div><?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Right: Payments -->
                <div class="space-y-6">
                    <!-- Payment History -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
                        <h3 class="text-sm font-semibold text-slate-700 mb-3 flex items-center gap-2">
                            <span class="material-icons text-emerald-500 text-lg">payments</span> Riwayat Pembayaran
                        </h3>
                        <?php if (!empty($payments)): ?>
                        <div class="space-y-2">
                            <?php foreach ($payments as $pmt): ?>
                            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-100">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-xs font-mono text-emerald-700"><?= htmlspecialchars($pmt['payment_no']) ?></p>
                                        <p class="text-xs text-emerald-600"><?= date('d M Y', strtotime($pmt['payment_date'])) ?></p>
                                    </div>
                                    <p class="text-sm font-bold text-emerald-700">+<?= number_format($pmt['amount'], 2) ?></p>
                                </div>
                                <?php if ($pmt['reference_number']): ?><p class="text-[10px] text-emerald-500 mt-1">Ref: <?= htmlspecialchars($pmt['reference_number']) ?></p><?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada pembayaran</p>
                        <?php endif; ?>
                    </div>

                    <!-- Record Payment -->
                    <?php if (in_array($inv['status'], ['unpaid', 'partial', 'overdue']) && $remaining > 0): ?>
                    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm" x-data="{ show: false }">
                        <button @click="show = !show" class="w-full flex items-center justify-between text-sm font-semibold text-blue-600 hover:text-blue-700">
                            <span class="flex items-center gap-2"><span class="material-icons text-lg">add_circle</span> Catat Pembayaran</span>
                            <span class="material-icons text-sm" :class="show && 'rotate-180'">expand_more</span>
                        </button>
                        <form x-show="show" x-collapse method="POST" action="<?= BASE_URL ?>finance/record-invoice-payment" class="mt-4 space-y-3">
                            <input type="hidden" name="invoice_id" value="<?= $inv['id'] ?>">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Jumlah Bayar</label>
                                <input type="number" name="amount" value="<?= $remaining ?>" step="0.01" max="<?= $remaining ?>" required class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal</label>
                                <input type="date" name="payment_date" value="<?= date('Y-m-d') ?>" required class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Metode</label>
                                <select name="payment_method" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                    <option value="bank_transfer">Transfer Bank</option>
                                    <option value="cash">Tunai</option>
                                    <option value="cheque">Cek / Giro</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Akun Bank</label>
                                <select name="bank_account_id" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                    <?php foreach ($bank_accounts ?? [] as $ba): ?>
                                    <option value="<?= $ba['id'] ?>">[<?= $ba['code'] ?>] <?= htmlspecialchars($ba['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">No. Referensi</label>
                                <input type="text" name="reference_number" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-blue-500" placeholder="No. transfer / cek">
                            </div>
                            <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-colors">
                                💵 Catat Pembayaran
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
