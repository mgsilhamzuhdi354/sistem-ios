<?php
/**
 * Manual Journal Entry Form
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Journal Form' ?> — IndoOcean ERP</title>
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
                <a href="<?= BASE_URL ?>finance/journal" class="hover:text-blue-600">General Ledger</a>
                <span class="material-icons text-[14px]">chevron_right</span>
                <span class="text-slate-700 font-medium">Jurnal Manual</span>
            </nav>

            <h1 class="text-2xl font-bold text-slate-800 mb-6">📝 Jurnal Manual — Double Entry</h1>

            <?php if (!empty($flash)): ?>
            <div class="mb-4 p-4 rounded-xl border <?= $flash['type'] === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-700' ?>">
                <div class="flex items-center gap-2"><span class="material-icons text-lg"><?= $flash['type'] === 'success' ? 'check_circle' : 'error' ?></span> <?= htmlspecialchars($flash['message']) ?></div>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>finance/store-journal" x-data="journalForm()">
                <!-- Header -->
                <div class="bg-white rounded-2xl border border-slate-100 p-6 mb-6 shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">No. Jurnal</label>
                            <input type="text" name="entry_no" value="<?= htmlspecialchars($entry_no ?? '') ?>" readonly class="w-full text-sm rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal <span class="text-red-500">*</span></label>
                            <input type="date" name="entry_date" value="<?= date('Y-m-d') ?>" required class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">No. Referensi</label>
                            <input type="text" name="reference_no" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-indigo-500" placeholder="Optional">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Deskripsi <span class="text-red-500">*</span></label>
                            <input type="text" name="description" required class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-indigo-500" placeholder="Keterangan jurnal">
                        </div>
                    </div>
                </div>

                <!-- Journal Lines -->
                <div class="bg-white rounded-2xl border border-slate-100 p-6 mb-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-slate-700 mb-4 flex items-center gap-2">
                        <span class="material-icons text-indigo-500 text-lg">view_list</span> Baris Jurnal (Double Entry)
                    </h3>

                    <div class="space-y-3">
                        <template x-for="(line, idx) in lines" :key="idx">
                            <div class="grid grid-cols-12 gap-3 items-end p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="col-span-3">
                                    <label class="block text-[10px] font-medium text-slate-500 mb-1">Akun</label>
                                    <select :name="'line_account_id['+idx+']'" x-model="line.account_id" required class="w-full text-xs rounded-lg border border-slate-200 px-2 py-2 focus:ring-2 focus:ring-indigo-500">
                                        <option value="">-- Pilih Akun --</option>
                                        <?php foreach ($accounts ?? [] as $acc): ?>
                                        <option value="<?= $acc['id'] ?>">[<?= $acc['code'] ?>] <?= htmlspecialchars($acc['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-span-3">
                                    <label class="block text-[10px] font-medium text-slate-500 mb-1">Keterangan</label>
                                    <input type="text" :name="'line_description['+idx+']'" x-model="line.description" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-[10px] font-medium text-slate-500 mb-1">Cost Center</label>
                                    <select :name="'line_cost_center_id['+idx+']'" x-model="line.cost_center_id" class="w-full text-xs rounded-lg border border-slate-200 px-2 py-2">
                                        <option value="">-</option>
                                        <?php foreach ($cost_centers ?? [] as $cc): ?>
                                        <option value="<?= $cc['id'] ?>">[<?= $cc['code'] ?>] <?= $cc['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-[10px] font-medium text-slate-500 mb-1">Debit</label>
                                    <input type="number" :name="'line_debit['+idx+']'" x-model.number="line.debit" step="0.01" min="0"
                                           @input="if(line.debit > 0) line.credit = 0"
                                           class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2 text-right focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div class="col-span-1">
                                    <label class="block text-[10px] font-medium text-slate-500 mb-1">Kredit</label>
                                    <input type="number" :name="'line_credit['+idx+']'" x-model.number="line.credit" step="0.01" min="0"
                                           @input="if(line.credit > 0) line.debit = 0"
                                           class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2 text-right focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div class="col-span-1 flex items-end">
                                    <button type="button" @click="removeLine(idx)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg" :class="{'opacity-30 pointer-events-none': lines.length <= 2}">
                                        <span class="material-icons text-lg">delete</span>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="addLine()" class="mt-3 flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-700 px-3 py-1.5 rounded-lg hover:bg-indigo-50">
                        <span class="material-icons text-lg">add</span> Tambah Baris
                    </button>

                    <!-- Balance Indicator -->
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <div class="flex justify-end gap-8 text-sm">
                            <div>Total Debit: <span class="font-bold text-slate-800" x-text="fmt(totalDebit)"></span></div>
                            <div>Total Kredit: <span class="font-bold text-slate-800" x-text="fmt(totalCredit)"></span></div>
                        </div>
                        <div class="mt-2 text-right">
                            <template x-if="Math.abs(totalDebit - totalCredit) < 0.01 && totalDebit > 0">
                                <span class="text-xs font-semibold text-emerald-600 flex items-center gap-1 justify-end">
                                    <span class="material-icons text-sm">check_circle</span> Seimbang ✓
                                </span>
                            </template>
                            <template x-if="Math.abs(totalDebit - totalCredit) >= 0.01">
                                <span class="text-xs font-semibold text-red-600 flex items-center gap-1 justify-end">
                                    <span class="material-icons text-sm">error</span> Selisih: <span x-text="fmt(Math.abs(totalDebit - totalCredit))"></span>
                                </span>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between">
                    <a href="<?= BASE_URL ?>finance/journal" class="flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700"><span class="material-icons text-lg">arrow_back</span> Kembali</a>
                    <button type="submit" :disabled="Math.abs(totalDebit - totalCredit) >= 0.01 || totalDebit === 0"
                            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                        📒 Posting Jurnal
                    </button>
                </div>
            </form>
        </div>
    </main>
    <script>
    function journalForm() {
        return {
            lines: [
                { account_id: '', description: '', cost_center_id: '', debit: 0, credit: 0 },
                { account_id: '', description: '', cost_center_id: '', debit: 0, credit: 0 }
            ],
            get totalDebit() { return this.lines.reduce((s, l) => s + (parseFloat(l.debit) || 0), 0); },
            get totalCredit() { return this.lines.reduce((s, l) => s + (parseFloat(l.credit) || 0), 0); },
            addLine() { this.lines.push({ account_id: '', description: '', cost_center_id: '', debit: 0, credit: 0 }); },
            removeLine(idx) { if (this.lines.length > 2) this.lines.splice(idx, 1); },
            fmt(v) { return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2 }).format(v); }
        };
    }
    </script>
</body>
</html>
