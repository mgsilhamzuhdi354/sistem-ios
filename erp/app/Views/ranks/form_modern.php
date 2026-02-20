<?php
/**
 * Modern Rank Form (Create/Edit)
 * Clean white design with modern sidebar
 */
$currentPage = 'ranks';
$isEdit = isset($rank);
?>
<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Rank Form' ?> - IndoOcean ERP</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','sans-serif']}}}}</script>
    <style>
        @keyframes fadeInUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
        .animate-fade-in{animation:fadeInUp .4s ease-out forwards}
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans">
<div class="flex h-screen overflow-hidden">
    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

    <main class="flex-1 flex flex-col h-screen overflow-hidden ml-64">
        <!-- Top Bar -->
        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-6 flex-shrink-0">
            <div class="flex items-center text-sm text-slate-500">
                <a href="<?= BASE_URL ?>ranks" class="hover:text-blue-600 transition-colors"><?= __('ranks.master_title') ?></a>
                <span class="material-icons-round text-sm mx-2">chevron_right</span>
                <span class="font-medium text-slate-800"><?= $isEdit ? __('common.edit') : __('common.add_new') ?></span>
            </div>
            <a href="<?= BASE_URL ?>ranks"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-lg transition-colors">
                <span class="material-icons text-sm">arrow_back</span> <?= __('common.back') ?>
            </a>
        </header>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-8">
            <div class="max-w-2xl mx-auto">
                <!-- Page Header -->
                <div class="flex items-center gap-3 mb-8 opacity-0 animate-fade-in">
                    <div class="p-2.5 bg-amber-50 rounded-xl">
                        <span class="material-icons-round text-amber-600 text-3xl"><?= $isEdit ? 'edit' : 'add_circle' ?></span>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800"><?= $isEdit ? __('ranks.edit_rank') : __('ranks.add_rank') ?></h2>
                        <p class="text-sm text-slate-400"><?= $isEdit ? 'Perbarui data pangkat ' . htmlspecialchars($rank['name']) : 'Isi form untuk menambahkan pangkat baru' ?></p>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 opacity-0 animate-fade-in" style="animation-delay:.1s">
                    <form action="<?= $isEdit ? BASE_URL . 'ranks/update/' . $rank['id'] : BASE_URL . 'ranks/store' ?>" method="POST">
                        
                        <!-- Row: Name + Code -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                                    <?= __('ranks.rank_name') ?> <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" required
                                       value="<?= htmlspecialchars($rank['name'] ?? '') ?>"
                                       placeholder="e.g. Chief Officer"
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                                    <?= __('ranks.rank_code') ?> <span class="text-slate-300">(<?= __('common.optional') ?>)</span>
                                </label>
                                <input type="text" name="code"
                                       value="<?= htmlspecialchars($rank['code'] ?? '') ?>"
                                       placeholder="e.g. C/O"
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all">
                            </div>
                        </div>

                        <!-- Row: Department + Level -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                                    <?= __('ranks.department') ?> <span class="text-red-500">*</span>
                                </label>
                                <select name="department" required
                                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all">
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?= $dept ?>" <?= ($rank['department'] ?? '') === $dept ? 'selected' : '' ?>><?= $dept ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                                    <?= __('ranks.level_order') ?> <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="level" required min="1"
                                       value="<?= $rank['level'] ?? 99 ?>"
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all">
                                <p class="text-[11px] text-slate-400 mt-1.5">Semakin kecil angkanya, semakin tinggi jabatannya (muncul paling atas).</p>
                            </div>
                        </div>

                        <!-- Officer Checkbox -->
                        <div class="mb-5">
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Officer?</label>
                            <label class="relative inline-flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" name="is_officer" value="1"
                                       <?= ($rank['is_officer'] ?? 0) ? 'checked' : '' ?>
                                       class="sr-only peer">
                                <div class="w-10 h-5 bg-slate-200 peer-checked:bg-blue-600 peer-focus:ring-4 peer-focus:ring-blue-500/20 rounded-full transition-colors after:content-[''] after:absolute after:left-0.5 after:top-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-transform peer-checked:after:translate-x-5 after:shadow-sm"></div>
                                <span class="text-sm font-medium text-slate-600 group-hover:text-slate-800 transition-colors">Ya, ini jabatan Officer (Perwira)</span>
                            </label>
                        </div>

                        <!-- Description -->
                        <div class="mb-5">
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                                Deskripsi / Catatan
                            </label>
                            <textarea name="description" rows="3"
                                      placeholder="Deskripsi singkat tentang pangkat ini..."
                                      class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all resize-none"><?= htmlspecialchars($rank['description'] ?? '') ?></textarea>
                        </div>

                        <!-- Status (Edit only) -->
                        <?php if ($isEdit): ?>
                        <div class="mb-6">
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Status</label>
                            <select name="is_active"
                                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all">
                                <option value="1" <?= $rank['is_active'] ? 'selected' : '' ?>><?= __('common.active') ?></option>
                                <option value="0" <?= !$rank['is_active'] ? 'selected' : '' ?>><?= __('common.inactive') ?></option>
                            </select>
                        </div>
                        <?php endif; ?>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                            <a href="<?= BASE_URL ?>ranks"
                               class="px-5 py-2.5 border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors">
                                Batal
                            </a>
                            <button type="submit"
                                    class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-blue-600/20 transition-all transform hover:-translate-y-0.5 inline-flex items-center gap-2">
                                <span class="material-icons text-sm">save</span>
                                <?= __('ranks.save_rank') ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-8 text-center"><p class="text-xs text-slate-400">© <?= date('Y') ?> IndoOcean ERP System</p></div>
        </div>
    </main>
</div>
</body>
</html>
