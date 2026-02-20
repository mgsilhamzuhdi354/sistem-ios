<?php
/**
 * Modern User Management View
 * Clean white design with modern sidebar
 */
$currentPage = 'users';

$roleColors = [
    'super_admin' => ['bg-red-100 text-red-700', 'Super Admin'],
    'admin'       => ['bg-purple-100 text-purple-700', 'Admin'],
    'hr'          => ['bg-emerald-100 text-emerald-700', 'HR'],
    'finance'     => ['bg-blue-100 text-blue-700', 'Finance'],
    'manager'     => ['bg-amber-100 text-amber-700', 'Manager'],
    'viewer'      => ['bg-slate-100 text-slate-600', 'Viewer'],
];
?>
<!DOCTYPE html>
<html lang="<?= session()->get('lang') ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'User Management' ?> - IndoOcean ERP</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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
            <div>
                <h1 class="text-base font-bold text-slate-800 tracking-tight"><?= __('users.title') ?></h1>
                <p class="text-[11px] text-slate-400"><?= __('users.subtitle') ?></p>
            </div>
            <?php if (method_exists($this, 'checkPermission') && $this->checkPermission('users', 'create')): ?>
            <a href="<?= BASE_URL ?>users/create"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm">
                <span class="material-icons text-sm">person_add</span> <?= __('users.add_user') ?>
            </a>
            <?php endif; ?>
        </header>

        <div class="flex-1 overflow-y-auto p-6">
            <?php if (!empty($flash)): ?>
                <?php foreach ($flash as $type => $msg): ?>
                    <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium
                        <?= $type === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' ?>
                        <?= $type === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : '' ?>">
                        <?= $msg ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Filters -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 mb-6">
                <form method="GET" action="<?= BASE_URL ?>users" class="flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Cari</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="material-icons text-slate-400 text-lg">search</span>
                            </span>
                            <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                                   placeholder="Username, email, nama..."
                                   class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                        </div>
                    </div>
                    <div class="min-w-[140px]">
                        <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Role</label>
                        <select name="role" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                            <option value="">Semua Role</option>
                            <option value="super_admin" <?= ($filters['role'] ?? '') === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                            <option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="hr" <?= ($filters['role'] ?? '') === 'hr' ? 'selected' : '' ?>>HR</option>
                            <option value="finance" <?= ($filters['role'] ?? '') === 'finance' ? 'selected' : '' ?>>Finance</option>
                            <option value="manager" <?= ($filters['role'] ?? '') === 'manager' ? 'selected' : '' ?>>Manager</option>
                            <option value="viewer" <?= ($filters['role'] ?? '') === 'viewer' ? 'selected' : '' ?>>Viewer</option>
                        </select>
                    </div>
                    <div class="min-w-[110px]">
                        <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                            <option value="">Semua</option>
                            <option value="1" <?= isset($filters['is_active']) && $filters['is_active'] === 1 ? 'selected' : '' ?>>Aktif</option>
                            <option value="0" <?= isset($filters['is_active']) && $filters['is_active'] === 0 ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>
                    <button type="submit" class="inline-flex items-center gap-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors">
                        <span class="material-icons text-sm">filter_list</span> Filter
                    </button>
                    <a href="<?= BASE_URL ?>users" class="inline-flex items-center gap-1 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-lg transition-colors">
                        <span class="material-icons text-sm">clear</span> Reset
                    </a>
                </form>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider w-10">#</th>
                                <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider"><?= __('users.username') ?></th>
                                <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                                <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Role</th>
                                <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider"><?= __('users.last_login') ?></th>
                                <th class="px-5 py-2.5 text-center text-[11px] font-semibold text-slate-500 uppercase tracking-wider"><?= __('common.status') ?></th>
                                <th class="px-5 py-2.5 text-center text-[11px] font-semibold text-slate-500 uppercase tracking-wider w-28"><?= __('common.actions') ?></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="7" class="py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="bg-slate-100 p-5 rounded-full mb-4">
                                            <span class="material-icons text-4xl text-slate-300">people</span>
                                        </div>
                                        <h3 class="text-base font-semibold text-slate-700 mb-1">Tidak ada user ditemukan</h3>
                                        <p class="text-slate-400 text-sm">Coba ubah filter pencarian Anda.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($users as $i => $user):
                                    $ri = $roleColors[$user['role']] ?? ['bg-slate-100 text-slate-600', $user['role']];
                                ?>
                                <tr class="hover:bg-blue-50/40 transition-colors">
                                    <td class="px-5 py-3 text-xs text-slate-400 font-medium"><?= ($page - 1) * 20 + $i + 1 ?></td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                                <?= strtoupper(substr($user['full_name'] ?? 'U', 0, 1)) ?>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-slate-800"><?= htmlspecialchars($user['full_name']) ?></p>
                                                <p class="text-[11px] text-slate-400">@<?= htmlspecialchars($user['username']) ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 text-sm text-slate-600"><?= htmlspecialchars($user['email']) ?></td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold <?= $ri[0] ?>">
                                            <?= $ri[1] ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-sm text-slate-500">
                                        <?php if ($user['last_login']): ?>
                                            <?= date('d M Y H:i', strtotime($user['last_login'])) ?>
                                        <?php else: ?>
                                            <span class="text-slate-300">Belum pernah</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <?php if ($user['is_active']): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-700">Aktif</span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-red-100 text-red-600">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center justify-center gap-1">
                                            <a href="<?= BASE_URL ?>users/<?= $user['id'] ?>" title="View"
                                               class="p-1.5 rounded-lg hover:bg-blue-50 text-slate-400 hover:text-blue-600 transition-colors">
                                                <span class="material-icons text-lg">visibility</span>
                                            </a>
                                            <?php if (method_exists($this, 'checkPermission') && $this->checkPermission('users', 'edit')): ?>
                                            <a href="<?= BASE_URL ?>users/edit/<?= $user['id'] ?>" title="Edit"
                                               class="p-1.5 rounded-lg hover:bg-amber-50 text-slate-400 hover:text-amber-600 transition-colors">
                                                <span class="material-icons text-lg">edit</span>
                                            </a>
                                            <?php endif; ?>
                                            <?php if (method_exists($this, 'checkPermission') && $this->checkPermission('users', 'delete') && $user['id'] != ($this->getCurrentUser()['id'] ?? null)): ?>
                                            <button onclick="deleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>')" title="Delete"
                                                    class="p-1.5 rounded-lg hover:bg-red-50 text-slate-400 hover:text-red-500 transition-colors">
                                                <span class="material-icons text-lg">delete</span>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if (($total ?? 0) > 20): ?>
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50 flex justify-center gap-1">
                    <?php
                    $totalPages = ceil($total / 20);
                    for ($i = 1; $i <= $totalPages; $i++):
                    ?>
                    <a href="<?= BASE_URL ?>users?page=<?= $i ?>&role=<?= $filters['role'] ?? '' ?>&search=<?= $filters['search'] ?? '' ?>"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors <?= $i == $page ? 'bg-blue-600 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-blue-50 hover:text-blue-600' ?>">
                        <?= $i ?>
                    </a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>

                <!-- Footer -->
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50 flex justify-between items-center">
                    <p class="text-xs text-slate-400">
                        Menampilkan <?= count($users ?? []) ?> dari <?= $total ?? 0 ?> user
                    </p>
                </div>
            </div>

            <div class="mt-6 text-center"><p class="text-xs text-slate-400">© <?= date('Y') ?> IndoOcean ERP System</p></div>
        </div>
    </main>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl p-6 max-w-sm w-[90%]">
        <div class="flex items-center gap-2 mb-3">
            <div class="p-2 bg-red-100 rounded-lg"><span class="material-icons text-red-500">warning</span></div>
            <h3 class="text-sm font-bold text-red-700">Nonaktifkan User</h3>
        </div>
        <p id="deleteMessage" class="text-sm text-slate-600 mb-5"></p>
        <form id="deleteForm" method="POST">
            <input type="hidden" name="csrf_token" value="<?= method_exists($this, 'generateCsrfToken') ? $this->generateCsrfToken() : '' ?>">
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-lg">
                    <span class="material-icons text-sm align-middle mr-0.5">block</span> Nonaktifkan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function deleteUser(id, username) {
    document.getElementById('deleteMessage').textContent = 'Apakah Anda yakin ingin menonaktifkan user "' + username + '"?';
    document.getElementById('deleteForm').action = '<?= BASE_URL ?>users/delete/' + id;
    const m = document.getElementById('deleteModal');
    m.classList.remove('hidden'); m.classList.add('flex');
}
function closeDeleteModal() {
    const m = document.getElementById('deleteModal');
    m.classList.add('hidden'); m.classList.remove('flex');
}
document.getElementById('deleteModal').addEventListener('click', function(e) { if (e.target === this) closeDeleteModal(); });
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeDeleteModal(); });
</script>
</body>
</html>
