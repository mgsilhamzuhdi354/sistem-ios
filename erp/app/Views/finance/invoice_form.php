<?php
/**
 * Invoice Form — Create / Edit
 */
$inv = $invoice ?? [];
$isEdit = $mode === 'edit';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Invoice Form' ?> — IndoOcean ERP</title>
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
            <nav class="mb-6 flex items-center gap-2 text-xs text-slate-500">
                <a href="<?= BASE_URL ?>finance" class="hover:text-blue-600">Keuangan</a>
                <span class="material-icons text-[14px]">chevron_right</span>
                <a href="<?= BASE_URL ?>finance/invoices" class="hover:text-blue-600">Invoices</a>
                <span class="material-icons text-[14px]">chevron_right</span>
                <span class="text-slate-700 font-medium"><?= $isEdit ? 'Edit' : 'Buat Baru' ?></span>
            </nav>

            <h1 class="text-2xl font-bold text-slate-800 mb-6"><?= $isEdit ? '✏️ Edit Invoice' : '📝 Buat Invoice Baru' ?></h1>

            <form action="<?= BASE_URL ?>finance/<?= $isEdit ? 'update-invoice/' . $inv['id'] : 'store-invoice' ?>" method="POST"
                  x-data="invoiceForm()" @submit.prevent="submitForm($event)">

                <!-- Invoice Details -->
                <div class="bg-white rounded-2xl border border-slate-100 p-6 mb-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-slate-700 mb-4 flex items-center gap-2">
                        <span class="material-icons text-blue-500 text-lg">description</span> Detail Invoice
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">No. Invoice</label>
                            <input type="text" name="invoice_no" value="<?= htmlspecialchars($invoice_no ?? '') ?>" readonly
                                   class="w-full text-sm rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-700 font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Klien <span class="text-red-500">*</span></label>
                            <select name="client_id" required class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Klien --</option>
                                <?php foreach ($clients ?? [] as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= ($inv['client_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Vessel</label>
                            <select name="vessel_id" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Opsional --</option>
                                <?php foreach ($vessels ?? [] as $v): ?>
                                <option value="<?= $v['id'] ?>" <?= ($inv['vessel_id'] ?? '') == $v['id'] ? 'selected' : '' ?>><?= htmlspecialchars($v['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Invoice <span class="text-red-500">*</span></label>
                            <input type="date" name="invoice_date" value="<?= $inv['invoice_date'] ?? date('Y-m-d') ?>" required
                                   class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Jatuh Tempo <span class="text-red-500">*</span></label>
                            <input type="date" name="due_date" value="<?= $inv['due_date'] ?? date('Y-m-d', strtotime('+30 days')) ?>" required
                                   class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Cost Center</label>
                            <select name="cost_center_id" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Opsional --</option>
                                <?php foreach ($cost_centers ?? [] as $cc): ?>
                                <option value="<?= $cc['id'] ?>" <?= ($inv['cost_center_id'] ?? '') == $cc['id'] ? 'selected' : '' ?>>[<?= $cc['code'] ?>] <?= htmlspecialchars($cc['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Akun Pendapatan</label>
                            <select name="revenue_account_id" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Default --</option>
                                <?php foreach ($revenue_accounts ?? [] as $ra): ?>
                                <option value="<?= $ra['id'] ?>" <?= ($inv['revenue_account_id'] ?? '') == $ra['id'] ? 'selected' : '' ?>>[<?= $ra['code'] ?>] <?= htmlspecialchars($ra['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Mata Uang</label>
                            <select name="currency_code" x-model="currency" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-blue-500">
                                <option value="IDR">IDR — Rupiah</option>
                                <option value="USD" <?= ($inv['currency_code'] ?? '') === 'USD' ? 'selected' : '' ?>>USD — Dollar</option>
                                <option value="SGD" <?= ($inv['currency_code'] ?? '') === 'SGD' ? 'selected' : '' ?>>SGD — Singapore Dollar</option>
                            </select>
                        </div>
                        <div x-show="currency !== 'IDR'" x-cloak>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Kurs ke IDR</label>
                            <input type="number" name="exchange_rate" step="0.01" value="<?= $inv['exchange_rate'] ?? 1 ?>" x-model.number="exchangeRate"
                                   class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <div class="bg-white rounded-2xl border border-slate-100 p-6 mb-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-slate-700 mb-4 flex items-center gap-2">
                        <span class="material-icons text-emerald-500 text-lg">list_alt</span> Item Invoice
                    </h3>

                    <div class="space-y-3" id="items-container">
                        <template x-for="(item, idx) in items" :key="idx">
                            <div class="grid grid-cols-12 gap-3 items-end p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="col-span-6">
                                    <label class="block text-[10px] font-medium text-slate-500 mb-1">Deskripsi</label>
                                    <input type="text" :name="'item_description['+idx+']'" x-model="item.desc" required
                                           class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-blue-500" placeholder="Deskripsi item">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-[10px] font-medium text-slate-500 mb-1">Qty</label>
                                    <input type="number" :name="'item_quantity['+idx+']'" x-model.number="item.qty" step="0.01" min="0.01" required
                                           class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-blue-500 text-right">
                                </div>
                                <div class="col-span-3">
                                    <label class="block text-[10px] font-medium text-slate-500 mb-1">Harga Satuan</label>
                                    <input type="number" :name="'item_price['+idx+']'" x-model.number="item.price" step="0.01" min="0" required
                                           class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-blue-500 text-right">
                                </div>
                                <div class="col-span-1 flex items-end">
                                    <button type="button" @click="removeItem(idx)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg" :class="{'opacity-30 pointer-events-none': items.length <= 1}">
                                        <span class="material-icons text-lg">delete</span>
                                    </button>
                                </div>
                                <div class="col-span-12 text-right text-xs text-slate-500">
                                    Subtotal: <span class="font-semibold text-slate-700" x-text="formatNumber(item.qty * item.price)"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="addItem()" class="mt-3 flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-700 px-3 py-1.5 rounded-lg hover:bg-blue-50">
                        <span class="material-icons text-lg">add</span> Tambah Item
                    </button>
                </div>

                <!-- Totals + Discount/Tax -->
                <div class="bg-white rounded-2xl border border-slate-100 p-6 mb-6 shadow-sm">
                    <div class="max-w-md ml-auto space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Subtotal</span>
                            <span class="font-semibold text-slate-700" x-text="formatNumber(subtotal)"></span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-sm text-slate-600">Diskon (%)</span>
                            <input type="number" name="discount_percent" x-model.number="discountPct" step="0.01" min="0" max="100"
                                   class="w-24 text-sm rounded-lg border border-slate-200 px-3 py-1.5 text-right focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-sm text-slate-600">Pajak / PPN (%)</span>
                            <input type="number" name="tax_percent" x-model.number="taxPct" step="0.01" min="0"
                                   class="w-24 text-sm rounded-lg border border-slate-200 px-3 py-1.5 text-right focus:ring-2 focus:ring-blue-500">
                        </div>
                        <hr class="border-slate-100">
                        <div class="flex justify-between text-lg font-bold text-slate-800">
                            <span>TOTAL</span>
                            <span x-text="formatNumber(grandTotal)"></span>
                        </div>
                    </div>
                </div>

                <!-- Notes & Terms -->
                <div class="bg-white rounded-2xl border border-slate-100 p-6 mb-6 shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Syarat & Ketentuan</label>
                            <textarea name="terms" rows="3" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-blue-500" placeholder="Payment terms, e.g. Net 30"><?= htmlspecialchars($inv['terms'] ?? '') ?></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Catatan</label>
                            <textarea name="notes" rows="3" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-blue-500" placeholder="Catatan internal"><?= htmlspecialchars($inv['notes'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between">
                    <a href="<?= BASE_URL ?>finance/invoices" class="flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700">
                        <span class="material-icons text-lg">arrow_back</span> Kembali
                    </a>
                    <div class="flex gap-3">
                        <button type="submit" name="action" value="draft" class="px-5 py-2.5 border border-slate-300 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors">
                            💾 Simpan Draft
                        </button>
                        <button type="submit" name="action" value="send" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors">
                            📤 Kirim Invoice
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script>
    function invoiceForm() {
        const existingItems = <?= json_encode(array_map(fn($it) => ['desc' => $it['description'], 'qty' => floatval($it['quantity']), 'price' => floatval($it['unit_price'])], $items ?? [])) ?>;
        return {
            currency: '<?= $inv['currency_code'] ?? 'IDR' ?>',
            exchangeRate: <?= $inv['exchange_rate'] ?? 1 ?>,
            discountPct: <?= $inv['discount_percent'] ?? 0 ?>,
            taxPct: <?= $inv['tax_percent'] ?? 0 ?>,
            items: existingItems.length > 0 ? existingItems : [{ desc: '', qty: 1, price: 0 }],

            get subtotal() { return this.items.reduce((s, i) => s + (i.qty * i.price), 0); },
            get discountAmt() { return this.subtotal * (this.discountPct / 100); },
            get taxableAmt() { return this.subtotal - this.discountAmt; },
            get taxAmt() { return this.taxableAmt * (this.taxPct / 100); },
            get grandTotal() { return this.taxableAmt + this.taxAmt; },

            addItem() { this.items.push({ desc: '', qty: 1, price: 0 }); },
            removeItem(idx) { if (this.items.length > 1) this.items.splice(idx, 1); },
            formatNumber(v) { return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2 }).format(v); },
            submitForm(e) { e.target.submit(); }
        };
    }
    </script>
</body>
</html>
