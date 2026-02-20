<?php
/**
 * Modern User Form (Create/Edit)
 */
$currentPage = 'users';
$isEdit = !empty($user);
$roles = $roles ?? [];
?>
<!DOCTYPE html>
<html lang="<?= session()->get('lang') ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'User Form' ?> - IndoOcean ERP</title>
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
        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-6 flex-shrink-0">
            <div class="flex items-center text-sm text-slate-500">
                <a href="<?= BASE_URL ?>users" class="hover:text-blue-600 transition-colors"><?= __('users.title') ?></a>
                <span class="material-icons-round text-sm mx-2">chevron_right</span>
                <span class="font-medium text-slate-800"><?= $isEdit ? __('users.edit_title') : __('users.create_title') ?></span>
            </div>
            <a href="<?= BASE_URL ?>users" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-lg transition-colors">
                <span class="material-icons text-sm">arrow_back</span> Kembali
            </a>
        </header>
        <div class="flex-1 overflow-y-auto p-8">
            <div class="max-w-2xl mx-auto">
                <!-- Flash -->
                <?php if (!empty($flash)): ?>
                <div class="mb-5 px-4 py-3 rounded-xl border <?= ($flash['type'] ?? '') === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-700' ?> text-sm" x-data="{show:true}" x-show="show" x-transition>
                    <div class="flex justify-between items-center">
                        <span><?= $flash['message'] ?? '' ?></span>
                        <button @click="show=false" class="text-current opacity-50 hover:opacity-100">&times;</button>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Header -->
                <div class="flex items-center gap-3 mb-6 opacity-0 animate-fade-in">
                    <div class="p-2.5 bg-blue-50 rounded-xl">
                        <span class="material-icons-round text-blue-600 text-3xl"><?= $isEdit ? 'edit' : 'person_add' ?></span>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800"><?= $isEdit ? 'Edit User' : 'Tambah User Baru' ?></h2>
                        <p class="text-sm text-slate-400"><?= $isEdit ? 'Update data ' . htmlspecialchars($user['full_name']) : 'Isi form untuk membuat user baru' ?></p>
                    </div>
                </div>

                <!-- Form -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 opacity-0 animate-fade-in" style="animation-delay:.1s">
                    <form action="<?= $isEdit ? BASE_URL . 'users/update/' . $user['id'] : BASE_URL . 'users/store' ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">

                        <!-- Full Name -->
                        <div class="mb-5">
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="full_name" required value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" placeholder="John Doe"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all">
                        </div>

                        <!-- Username + Email -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Username <span class="text-red-500">*</span></label>
                                <input type="text" name="username" required minlength="3" value="<?= htmlspecialchars($user['username'] ?? '') ?>" placeholder="john.doe"
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" required value="<?= htmlspecialchars($user['email'] ?? '') ?>" placeholder="john@company.com"
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all">
                            </div>
                        </div>

                        <!-- Role + Phone -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Role <span class="text-red-500">*</span></label>
                                <select name="role" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all">
                                    <?php foreach ($roles as $key => $label): ?>
                                        <option value="<?= $key ?>" <?= ($user['role'] ?? '') === $key ? 'selected' : '' ?>><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Telepon</label>
                                <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="+62..."
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all">
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                                    Password <?= $isEdit ? '<span class="text-slate-300">(Kosongkan jika tidak diubah)</span>' : '<span class="text-red-500">*</span>' ?>
                                </label>
                                <input type="password" name="password" <?= $isEdit ? '' : 'required' ?> minlength="8" placeholder="Min 8 karakter"
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all">
                            </div>
                            <?php if (!$isEdit): ?>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Konfirmasi Password <span class="text-red-500">*</span></label>
                                <input type="password" name="confirm_password" required minlength="8" placeholder="Ulangi password"
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all">
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Status (edit only) -->
                        <?php if ($isEdit): ?>
                        <div class="mb-5">
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Status</label>
                            <select name="is_active" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all">
                                <option value="1" <?= $user['is_active'] ? 'selected' : '' ?>><?= __('common.active') ?></option>
                                <option value="0" <?= !$user['is_active'] ? 'selected' : '' ?>><?= __('common.inactive') ?></option>
                            </select>
                        </div>
                        <?php endif; ?>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                            <a href="<?= BASE_URL ?>users" class="px-5 py-2.5 border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors">Batal</a>
                            <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-blue-600/20 transition-all transform hover:-translate-y-0.5 inline-flex items-center gap-2">
                                <span class="material-icons text-sm">save</span>
                                <?= $isEdit ? __('users.update_user') : __('users.create_user') ?>
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
