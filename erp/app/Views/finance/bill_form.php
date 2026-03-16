<?php
/**
 * Bill Form — Create / Edit (Accounts Payable)
 */
$b = $bill ?? [];
$isEdit = $mode === 'edit';
$catLabels = ['mcu' => 'MCU / Medical', 'travel' => 'Travel / Perjalanan', 'supplier' => 'Supplier', 'crew_welfare' => 'Crew Welfare', 'office' => 'Office / Kantor', 'ship_chandler' => 'Ship Chandler', 'other' => 'Lainnya'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Bill Form' ?> — IndoOcean ERP</title>
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
            <nav class="mb-6 flex items-center gap-2 text-xs text-slate-500">
                <a href="<?= BASE_URL ?>finance" class="hover:text-blue-600">Keuangan</a>
                <span class="material-icons text-[14px]">chevron_right</span>
                <a href="<?= BASE_URL ?>finance/bills" class="hover:text-blue-600">Bills</a>
                <span class="material-icons text-[14px]">chevron_right</span>
                <span class="text-slate-700 font-medium"><?= $isEdit ? 'Edit' : 'Buat Baru' ?></span>
            </nav>

            <h1 class="text-2xl font-bold text-slate-800 mb-6"><?= $isEdit ? '✏️ Edit Bill' : '🧾 Catat Bill Baru' ?></h1>

            <form action="<?= BASE_URL ?>finance/<?= $isEdit ? 'update-bill/' . $b['id'] : 'store-bill' ?>" method="POST" enctype="multipart/form-data"
                  x-data="billForm()">

                <!-- Vendor + Bill Info -->
                <div class="bg-white rounded-2xl border border-slate-100 p-6 mb-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-slate-700 mb-4 flex items-center gap-2">
                        <span class="material-icons text-rose-500 text-lg">store</span> Info Vendor & Bill
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">No. Bill</label>
                            <input type="text" name="bill_no" value="<?= htmlspecialchars($bill_no ?? '') ?>" readonly class="w-full text-sm rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Nama Vendor <span class="text-red-500">*</span></label>
                            <input type="text" name="vendor_name" value="<?= htmlspecialchars($b['vendor_name'] ?? '') ?>" required class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-rose-500" placeholder="PT Indofarma, dll.">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Kategori <span class="text-red-500">*</span></label>
                            <select name="category" required class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-rose-500">
                                <?php foreach ($catLabels as $ck => $cl): ?>
                                <option value="<?= $ck ?>" <?= ($b['category'] ?? 'other') === $ck ? 'selected' : '' ?>><?= $cl ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Email Vendor</label>
                            <input type="email" name="vendor_email" value="<?= htmlspecialchars($b['vendor_email'] ?? '') ?>" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-rose-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Telepon</label>
                            <input type="text" name="vendor_phone" value="<?= htmlspecialchars($b['vendor_phone'] ?? '') ?>" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-rose-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Bill <span class="text-red-500">*</span></label>
                            <input type="date" name="bill_date" value="<?= $b['bill_date'] ?? date('Y-m-d') ?>" required class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-rose-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Jatuh Tempo <span class="text-red-500">*</span></label>
                            <input type="date" name="due_date" value="<?= $b['due_date'] ?? date('Y-m-d', strtotime('+30 days')) ?>" required class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-rose-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Cost Center</label>
                            <select name="cost_center_id" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-rose-500">
                                <option value="">-- Opsional --</option>
                                <?php foreach ($cost_centers ?? [] as $cc): ?>
                                <option value="<?= $cc['id'] ?>" <?= ($b['cost_center_id'] ?? '') == $cc['id'] ? 'selected' : '' ?>>[<?= $cc['code'] ?>] <?= htmlspecialchars($cc['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Akun Beban</label>
                            <select name="expense_account_id" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-rose-500">
                                <option value="">-- Default --</option>
                                <?php foreach ($expense_accounts ?? [] as $ea): ?>
                                <option value="<?= $ea['id'] ?>" <?= ($b['expense_account_id'] ?? '') == $ea['id'] ? 'selected' : '' ?>>[<?= $ea['code'] ?>] <?= htmlspecialchars($ea['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Mata Uang</label>
                            <select name="currency_code" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-rose-500">
                                <option value="IDR">IDR</option>
                                <option value="USD" <?= ($b['currency_code'] ?? '') === 'USD' ? 'selected' : '' ?>>USD</option>
                                <option value="SGD" <?= ($b['currency_code'] ?? '') === 'SGD' ? 'selected' : '' ?>>SGD</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Bukti / Receipt</label>
                            <input type="file" name="receipt_file" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-rose-50 file:text-rose-600 hover:file:bg-rose-100">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-medium text-slate-600 mb-1">Alamat Vendor</label>
                            <input type="text" name="vendor_address" value="<?= htmlspecialchars($b['vendor_address'] ?? '') ?>" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-rose-500">
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <div class="bg-white rounded-2xl border border-slate-100 p-6 mb-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-slate-700 mb-4 flex items-center gap-2">
                        <span class="material-icons text-amber-500 text-lg">list_alt</span> Item Bill
                    </h3>
                    <div class="space-y-3">
                        <template x-for="(item, idx) in items" :key="idx">
                            <div class="grid grid-cols-12 gap-3 items-end p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="col-span-6">
                                    <label class="block text-[10px] font-medium text-slate-500 mb-1">Deskripsi</label>
                                    <input type="text" :name="'item_description['+idx+']'" x-model="item.desc" required class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-rose-500" placeholder="Deskripsi item">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-[10px] font-medium text-slate-500 mb-1">Qty</label>
                                    <input type="number" :name="'item_quantity['+idx+']'" x-model.number="item.qty" step="0.01" min="0.01" required class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2 text-right">
                                </div>
                                <div class="col-span-3">
                                    <label class="block text-[10px] font-medium text-slate-500 mb-1">Harga Satuan</label>
                                    <input type="number" :name="'item_price['+idx+']'" x-model.number="item.price" step="0.01" min="0" required class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2 text-right">
                                </div>
                                <div class="col-span-1 flex items-end">
                                    <button type="button" @click="removeItem(idx)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg" :class="{'opacity-30 pointer-events-none': items.length <= 1}">
                                        <span class="material-icons text-lg">delete</span>
                                    </button>
                                </div>
                                <div class="col-span-12 text-right text-xs text-slate-500">
                                    Subtotal: <span class="font-semibold text-slate-700" x-text="fmt(item.qty * item.price)"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="addItem()" class="mt-3 flex items-center gap-1 text-sm font-medium text-rose-600 hover:text-rose-700 px-3 py-1.5 rounded-lg hover:bg-rose-50">
                        <span class="material-icons text-lg">add</span> Tambah Item
                    </button>
                </div>

                <!-- Tax + Total -->
                <div class="bg-white rounded-2xl border border-slate-100 p-6 mb-6 shadow-sm">
                    <div class="max-w-md ml-auto space-y-3">
                        <div class="flex justify-between text-sm"><span class="text-slate-600">Subtotal</span><span class="font-semibold text-slate-700" x-text="fmt(subtotal)"></span></div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-sm text-slate-600">Pajak (%)</span>
                            <input type="number" name="tax_percent" x-model.number="taxPct" step="0.01" min="0" class="w-24 text-sm rounded-lg border border-slate-200 px-3 py-1.5 text-right focus:ring-2 focus:ring-rose-500">
                        </div>
                        <hr class="border-slate-100">
                        <div class="flex justify-between text-lg font-bold text-slate-800"><span>TOTAL</span><span x-text="fmt(grandTotal)"></span></div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="bg-white rounded-2xl border border-slate-100 p-6 mb-6 shadow-sm">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Catatan</label>
                    <textarea name="notes" rows="2" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-rose-500"><?= htmlspecialchars($b['notes'] ?? '') ?></textarea>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between">
                    <a href="<?= BASE_URL ?>finance/bills" class="flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700">
                        <span class="material-icons text-lg">arrow_back</span> Kembali
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors">
                        💾 Simpan Bill
                    </button>
                </div>
            </form>
        </div>
    </main>
    <script>
    function billForm() {
        const ex = <?= json_encode(array_map(fn($it) => ['desc' => $it['description'], 'qty' => floatval($it['quantity']), 'price' => floatval($it['unit_price'])], $items ?? [])) ?>;
        return {
            taxPct: <?= $b['tax_percent'] ?? 0 ?>,
            items: ex.length > 0 ? ex : [{ desc: '', qty: 1, price: 0 }],
            get subtotal() { return this.items.reduce((s, i) => s + (i.qty * i.price), 0); },
            get grandTotal() { return this.subtotal * (1 + this.taxPct / 100); },
            addItem() { this.items.push({ desc: '', qty: 1, price: 0 }); },
            removeItem(idx) { if (this.items.length > 1) this.items.splice(idx, 1); },
            fmt(v) { return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2 }).format(v); }
        };
    }
    </script>
</body>
</html>
