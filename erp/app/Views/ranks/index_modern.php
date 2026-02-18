<?php
/**
 * Modern Master Ranks (Pangkat) Dashboard
 * PT Indo Ocean - ERP System
 */
$currentPage = 'ranks';
$ranks = $ranks ?? [];
$total = $total ?? 0;
$page = $page ?? 1;
$perPage = $perPage ?? 50;
$filters = $filters ?? ['search' => '', 'department' => ''];
$departments = $departments ?? ['Deck', 'Engine', 'Galley', 'Hotel', 'Other'];
$flash = $flash ?? null;
$totalPages = $perPage > 0 ? ceil($total / $perPage) : 1;

// Department color mapping
$deptStyles = [
    'Deck'   => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-700 dark:text-blue-400', 'border' => 'border-blue-200 dark:border-blue-800'],
    'Engine' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-400', 'border' => 'border-red-200 dark:border-red-800'],
    'Galley' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-400', 'border' => 'border-amber-200 dark:border-amber-800'],
    'Hotel'  => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400', 'border' => 'border-emerald-200 dark:border-emerald-800'],
    'Other'  => ['bg' => 'bg-slate-100 dark:bg-slate-800', 'text' => 'text-slate-600 dark:text-slate-400', 'border' => 'border-slate-200 dark:border-slate-700'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Pangkat | IndoOcean ERP</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#F59E0B",
                        secondary: "#1E3A8A",
                        "background-light": "#F1F5F9",
                        "background-dark": "#0F172A",
                        "surface-light": "#FFFFFF",
                        "surface-dark": "#1E293B",
                    },
                    fontFamily: {
                        display: ["Inter", "sans-serif"],
                    },
                },
            },
        };
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .scrollbar-thin::-webkit-scrollbar { width: 4px; }
        .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background-color: rgba(148,163,184,0.3); border-radius: 3px; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200 antialiased">

<div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 ml-64 flex flex-col h-screen overflow-hidden">
        <!-- Top Bar -->
        <header class="h-16 border-b border-slate-200 dark:border-slate-700 bg-white dark:bg-surface-dark flex items-center justify-between px-8 flex-shrink-0 shadow-sm z-10">
            <div class="flex items-center text-sm text-slate-500 dark:text-slate-400">
                <span>Master Data</span>
                <span class="material-icons-round text-sm mx-2">chevron_right</span>
                <span class="font-medium text-secondary dark:text-primary">Master Pangkat</span>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="document.documentElement.classList.toggle('dark')" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" title="Toggle Theme">
                    <span class="material-icons text-xl">dark_mode</span>
                </button>
                <a href="<?= BASE_URL ?>notifications" class="relative p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    <span class="material-icons text-xl">notifications</span>
                </a>
            </div>
        </header>

        <!-- Page Content -->
        <div class="flex-1 overflow-y-auto p-8 scrollbar-thin bg-slate-50/50 dark:bg-background-dark/50">

            <!-- Flash Messages -->
            <?php if ($flash): ?>
                <div class="mb-6 px-5 py-4 rounded-xl border <?= ($flash['type'] ?? '') === 'success' ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400' : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-700 dark:text-red-400' ?>" x-data="{ show: true }" x-show="show" x-transition>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="material-icons-round text-lg"><?= ($flash['type'] ?? '') === 'success' ? 'check_circle' : 'error' ?></span>
                            <span class="text-sm font-medium"><?= htmlspecialchars($flash['message'] ?? '') ?></span>
                        </div>
                        <button @click="show = false" class="p-1 hover:bg-black/5 rounded"><span class="material-icons-round text-sm">close</span></button>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-primary/10 rounded-xl">
                        <span class="material-icons-round text-primary text-3xl">military_tech</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Master Pangkat (Ranks)</h2>
                        <p class="text-slate-500 dark:text-slate-400 mt-0.5 text-sm">Manajemen daftar pangkat, departemen, dan urutan.</p>
                    </div>
                </div>
                <a href="<?= BASE_URL ?>ranks/create" class="bg-primary hover:bg-amber-600 text-slate-900 font-semibold px-5 py-2.5 rounded-xl shadow-lg shadow-primary/30 flex items-center gap-2 transition-all transform hover:-translate-y-0.5 w-fit">
                    <span class="material-icons-round text-lg">add</span>
                    Tambah Pangkat
                </a>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-surface-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-5 mb-6">
                <form action="" method="GET">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                        <div class="md:col-span-8">
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Cari Pangkat</label>
                            <div class="relative group">
                                <span class="material-icons-round absolute left-3 top-2.5 text-slate-400 group-focus-within:text-primary transition-colors">search</span>
                                <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all"
                                       placeholder="Nama pangkat...">
                            </div>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Department</label>
                            <select name="department" class="w-full pl-3 pr-10 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                                <option value="">Semua Dept</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?= $dept ?>" <?= ($filters['department'] ?? '') === $dept ? 'selected' : '' ?>><?= $dept ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="md:col-span-1 flex gap-2">
                            <button type="submit" class="w-full bg-slate-800 dark:bg-slate-700 hover:bg-slate-700 dark:hover:bg-slate-600 text-white font-medium py-2.5 rounded-xl transition-colors shadow-sm">
                                Filter
                            </button>
                        </div>
                    </div>
                    <?php if (!empty($filters['search']) || !empty($filters['department'])): ?>
                        <div class="mt-3 flex items-center gap-2">
                            <span class="text-xs text-slate-500">Active filters:</span>
                            <?php if (!empty($filters['search'])): ?>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-primary/10 text-primary text-xs font-medium rounded-full">
                                    "<?= htmlspecialchars($filters['search']) ?>"
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($filters['department'])): ?>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-secondary/10 text-secondary text-xs font-medium rounded-full">
                                    <?= $filters['department'] ?>
                                </span>
                            <?php endif; ?>
                            <a href="<?= BASE_URL ?>ranks" class="text-xs text-red-500 hover:text-red-700 font-medium ml-1">&times; Reset</a>
                        </div>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Data Table -->
            <div class="bg-white dark:bg-surface-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-semibold">
                                <th class="px-6 py-4 w-20 text-center">Level</th>
                                <th class="px-6 py-4">Nama Pangkat</th>
                                <th class="px-6 py-4">Code</th>
                                <th class="px-6 py-4">Department</th>
                                <th class="px-6 py-4 text-center">Officer?</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                            <?php if (empty($ranks)): ?>
                                <tr>
                                    <td class="px-6 py-16 text-center" colspan="7">
                                        <div class="flex flex-col items-center">
                                            <div class="h-20 w-20 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                                                <span class="material-icons-round text-4xl text-slate-300 dark:text-slate-600">military_tech</span>
                                            </div>
                                            <h3 class="text-base font-semibold text-slate-800 dark:text-white mb-1">Tidak ada data pangkat</h3>
                                            <p class="text-slate-500 dark:text-slate-400 text-sm mb-4">Tidak ada data yang sesuai dengan filter.</p>
                                            <?php if (!empty($filters['search']) || !empty($filters['department'])): ?>
                                                <a href="<?= BASE_URL ?>ranks" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                                    Reset Filter
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ranks as $rank): ?>
                                    <?php
                                    $dept = $rank['department'] ?? 'Other';
                                    $ds = $deptStyles[$dept] ?? $deptStyles['Other'];
                                    ?>
                                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition-colors group">
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold text-xs">
                                                <?= $rank['level'] ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="font-semibold text-slate-800 dark:text-slate-200"><?= htmlspecialchars($rank['name']) ?></span>
                                        </td>
                                        <td class="px-6 py-4 text-slate-500 dark:text-slate-400 font-mono text-sm"><?= htmlspecialchars($rank['code'] ?? '-') ?></td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md border text-xs font-medium uppercase <?= $ds['bg'] ?> <?= $ds['text'] ?> <?= $ds['border'] ?>">
                                                <?= htmlspecialchars($dept) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <?php if ($rank['is_officer']): ?>
                                                <span class="material-icons-round text-emerald-500 bg-emerald-100 dark:bg-emerald-500/10 rounded-full p-1 text-sm">check</span>
                                            <?php else: ?>
                                                <span class="material-icons-round text-slate-300 dark:text-slate-600 text-sm">remove</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <?php if ($rank['is_active']): ?>
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                                    ACTIVE
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400 border border-red-200 dark:border-red-500/20">
                                                    INACTIVE
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <a href="<?= BASE_URL ?>ranks/edit/<?= $rank['id'] ?>" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="Edit">
                                                    <span class="material-icons-round text-lg">edit</span>
                                                </a>
                                                <a href="<?= BASE_URL ?>ranks/delete/<?= $rank['id'] ?>" onclick="return confirm('Yakin hapus pangkat ini?')" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Hapus">
                                                    <span class="material-icons-round text-lg">delete</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                <div class="px-6 py-4 bg-white dark:bg-surface-dark border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
                    <span class="text-xs text-slate-500 dark:text-slate-400">
                        Showing <span class="font-semibold text-slate-700 dark:text-slate-300"><?= min(($page - 1) * $perPage + 1, $total) ?></span>
                        to <span class="font-semibold text-slate-700 dark:text-slate-300"><?= min($page * $perPage, $total) ?></span>
                        of <span class="font-semibold text-slate-700 dark:text-slate-300"><?= $total ?></span> results
                    </span>
                    <?php if ($totalPages > 1): ?>
                        <div class="flex items-center gap-1">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($filters['search'] ?? '') ?>&department=<?= urlencode($filters['department'] ?? '') ?>"
                                   class="p-1.5 rounded-lg text-slate-500 hover:text-primary hover:bg-primary/5 transition-colors">
                                    <span class="material-icons-round text-xl">chevron_left</span>
                                </a>
                            <?php else: ?>
                                <span class="p-1.5 text-slate-300 dark:text-slate-600"><span class="material-icons-round text-xl">chevron_left</span></span>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php if ($i == $page): ?>
                                    <span class="px-3 py-1 text-xs font-bold bg-primary text-slate-900 rounded-lg"><?= $i ?></span>
                                <?php elseif ($i <= 2 || $i >= $totalPages - 1 || abs($i - $page) <= 1): ?>
                                    <a href="?page=<?= $i ?>&search=<?= urlencode($filters['search'] ?? '') ?>&department=<?= urlencode($filters['department'] ?? '') ?>"
                                       class="px-3 py-1 text-xs font-medium text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors"><?= $i ?></a>
                                <?php elseif ($i == 3 || $i == $totalPages - 2): ?>
                                    <span class="px-1 text-slate-400">…</span>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($filters['search'] ?? '') ?>&department=<?= urlencode($filters['department'] ?? '') ?>"
                                   class="p-1.5 rounded-lg text-slate-500 hover:text-primary hover:bg-primary/5 transition-colors">
                                    <span class="material-icons-round text-xl">chevron_right</span>
                                </a>
                            <?php else: ?>
                                <span class="p-1.5 text-slate-300 dark:text-slate-600"><span class="material-icons-round text-xl">chevron_right</span></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Footer -->
            <footer class="mt-8 mb-4 text-center text-xs text-slate-400 dark:text-slate-600">
                &copy; 2026 IndoOcean ERP. All rights reserved.
            </footer>
        </div>
    </main>
</div>

</body>
</html>
