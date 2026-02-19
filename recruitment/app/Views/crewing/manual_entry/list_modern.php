<!-- Analytics-Rich Manual Entries List - Modern Table Design -->
<script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<script>
    tailwind.config = {
        darkMode: "class",
        corePlugins: {
            preflight: false,
        },
        theme: {
            extend: {
                colors: {
                    primary: "#0f172a",
                    secondary: "#3b82f6",
                    "background-light": "#f8fafc",
                    "background-dark": "#0f172a",
                    "surface-light": "#ffffff",
                    "surface-dark": "#1e293b",
                    "border-light": "#e2e8f0",
                    "border-dark": "#334155",
                },
                fontFamily: {
                    display: ["Inter", "sans-serif"],
                    body: ["Inter", "sans-serif"],
                },
                borderRadius: {
                    DEFAULT: "0.5rem",
                },
            },
        },
    };
</script>
<style>
    /* Scope Inter font only to the content area */
    .analytics-content {
        font-family: 'Inter', sans-serif;
    }
    .analytics-content ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    .analytics-content ::-webkit-scrollbar-track {
        background: transparent;
    }
    .analytics-content ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .analytics-content ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    /* Override admin content padding for this page */
    .admin-content.crewing-content {
        padding: 0 !important;
        max-width: 100% !important;
    }
    /* === SIDEBAR RESTORATION === */
    body.admin-body {
        display: flex !important;
        min-height: 100vh !important;
    }
    .admin-sidebar {
        width: 260px !important;
        min-width: 260px !important;
        display: flex !important;
        flex-direction: column !important;
        position: fixed !important;
        left: 0 !important;
        top: 0 !important;
        bottom: 0 !important;
        z-index: 1000 !important;
        background: linear-gradient(180deg, #0A2463 0%, #16213e 100%) !important;
    }
    .admin-main {
        margin-left: 260px !important;
        flex: 1 !important;
    }
    .admin-header {
        display: flex !important;
        position: sticky !important;
        top: 0 !important;
        z-index: 100 !important;
    }
    .sidebar-nav ul {
        list-style: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .sidebar-nav .nav-link {
        display: flex !important;
        align-items: center !important;
        gap: 15px !important;
        padding: 14px 25px !important;
        color: rgba(255,255,255,0.7) !important;
        text-decoration: none !important;
        font-size: 14px !important;
    }
    .sidebar-nav .nav-link.active {
        color: #fff !important;
        background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%) !important;
    }
    .sidebar-nav .nav-link:hover {
        color: #fff !important;
    }
    .sidebar-header .logo {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        color: #fff !important;
        text-decoration: none !important;
    }
    .sidebar-header .logo img {
        width: 32px !important;
        height: 32px !important;
    }
    .sidebar-footer {
        padding: 20px !important;
        border-top: 1px solid rgba(255,255,255,0.1) !important;
    }
    .sidebar-footer .crewing-info {
        color: rgba(255,255,255,0.7) !important;
    }
    .sidebar-footer .logout-btn {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        color: rgba(255,255,255,0.7) !important;
        text-decoration: none !important;
    }
</style>


<!-- Main Content Area -->
<div class="analytics-content text-slate-800 dark:text-slate-200 antialiased">
    
    <!-- Statistics Dashboard -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8 px-6 pt-6">
        <div class="bg-surface-light dark:bg-surface-dark p-4 rounded-xl border border-border-light dark:border-border-dark shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Total Pelamar</span>
                <span class="p-1 bg-blue-50 dark:bg-blue-900/20 rounded text-blue-600 dark:text-blue-400">
                    <span class="material-icons text-[16px]">groups</span>
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-800 dark:text-white"><?= $stats['total'] ?></div>
        </div>

        <div class="bg-surface-light dark:bg-surface-dark p-4 rounded-xl border border-border-light dark:border-border-dark shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Input Manual</span>
                <span class="p-1 bg-purple-50 dark:bg-purple-900/20 rounded text-purple-600 dark:text-purple-400">
                    <span class="material-icons text-[16px]">keyboard</span>
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-800 dark:text-white"><?= $stats['manual'] ?></div>
        </div>

        <div class="bg-surface-light dark:bg-surface-dark p-4 rounded-xl border border-border-light dark:border-border-dark shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Daftar Online</span>
                <span class="p-1 bg-green-50 dark:bg-green-900/20 rounded text-green-600 dark:text-green-400">
                    <span class="material-icons text-[16px]">public</span>
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-800 dark:text-white"><?= $stats['online'] ?></div>
        </div>

        <div class="bg-surface-light dark:bg-surface-dark p-4 rounded-xl border border-border-light dark:border-border-dark shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Approved</span>
                <span class="p-1 bg-emerald-50 dark:bg-emerald-900/20 rounded text-emerald-600 dark:text-emerald-400">
                    <span class="material-icons text-[16px]">check_circle</span>
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-800 dark:text-white"><?= $stats['approved'] ?></div>
        </div>

        <div class="bg-surface-light dark:bg-surface-dark p-4 rounded-xl border border-border-light dark:border-border-dark shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">In Progress</span>
                <span class="p-1 bg-orange-50 dark:bg-orange-900/20 rounded text-orange-600 dark:text-orange-400">
                    <span class="material-icons text-[16px]">pending</span>
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-800 dark:text-white"><?= $stats['in_progress'] ?></div>
        </div>

        <div class="bg-surface-light dark:bg-surface-dark p-4 rounded-xl border border-border-light dark:border-border-dark shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Synced ERP</span>
                <span class="p-1 bg-indigo-50 dark:bg-indigo-900/20 rounded text-indigo-600 dark:text-indigo-400">
                    <span class="material-icons text-[16px]">sync</span>
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-800 dark:text-white"><?= $stats['synced'] ?? 0 ?></div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6 px-6">
        <div class="bg-surface-light dark:bg-surface-dark p-1 rounded-lg border border-border-light dark:border-border-dark inline-flex shadow-sm">
            <button onclick="filterBySource('')" id="tabAll" class="px-4 py-2 rounded-md text-sm font-medium bg-primary text-white shadow-sm transition-all">
                Semua <span class="ml-1 opacity-80 text-xs"><?= $stats['total'] ?></span>
            </button>
            <button onclick="filterBySource('manual')" id="tabManual" class="px-4 py-2 rounded-md text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                Manual <span class="ml-1 opacity-60 text-xs bg-slate-200 dark:bg-slate-700 px-1.5 py-0.5 rounded-full"><?= $stats['manual'] ?></span>
            </button>
            <button onclick="filterBySource('online')" id="tabOnline" class="px-4 py-2 rounded-md text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                Online <span class="ml-1 opacity-60 text-xs bg-slate-200 dark:bg-slate-700 px-1.5 py-0.5 rounded-full"><?= $stats['online'] ?></span>
            </button>
            <a href="<?= url('/crewing/manual-entries/archived') ?>" class="px-4 py-2 rounded-md text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-all flex items-center gap-1">
                <span class="material-icons text-[16px]">inventory_2</span>
                Arsip <span class="ml-1 opacity-60 text-xs bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 px-1.5 py-0.5 rounded-full"><?= $stats['archived'] ?? 0 ?></span>
            </a>
        </div>
        <div class="relative w-full sm:w-80">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="material-icons text-slate-400 text-[18px]">filter_list</span>
            </span>
            <input 
                class="block w-full pl-10 pr-3 py-2.5 border border-border-light dark:border-border-dark rounded-lg bg-surface-light dark:bg-surface-dark text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm transition-shadow shadow-sm" 
                placeholder="Cari nama, email, posisi..." 
                type="text"
                id="searchInput"
                oninput="filterTable()"
            />
        </div>
    </div>

    <!-- Data Table -->
    <?php if (empty($entries)): ?>
        <div class="text-center py-12 px-6">
            <span class="material-icons text-6xl text-slate-400 mb-4">inbox</span>
            <h3 class="text-xl font-semibold text-slate-800 dark:text-white mb-2">Belum Ada Pelamar</h3>
            <p class="text-slate-600 dark:text-slate-400 mb-6">Anda belum memiliki pelamar yang ditugaskan atau diinput manual.</p>
            <a href="<?= url('/crewing/manual-entry') ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                <span class="material-icons text-[20px]">add_circle</span>
                Tambah Pelamar Pertama
            </a>
        </div>
    <?php else: ?>
        <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-light dark:border-border-dark overflow-hidden mx-6 mb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border-light dark:divide-border-dark" id="entriesTable">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-10">
                                <input class="rounded border-slate-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500 dark:bg-slate-700" type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"/>
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Candidate</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Contact Info</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Demographics</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Source</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-surface-light dark:bg-surface-dark divide-y divide-border-light dark:divide-border-dark">
                        <?php foreach ($entries as $entry): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group" data-source="<?= $entry['entry_source'] ?>">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input class="rounded border-slate-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500 dark:bg-slate-700 row-checkbox" type="checkbox" value="<?= $entry['id'] ?>"/>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-16 w-16">
                                        <?php if (!empty($entry['avatar'])): ?>
                                            <img class="h-16 w-16 rounded-xl object-cover object-top shadow-md border-2 border-slate-200" src="<?= url('/' . $entry['avatar']) ?>" alt="<?= htmlspecialchars($entry['candidate_name']) ?>"/>
                                        <?php else: ?>
                                            <div class="h-16 w-16 rounded-xl bg-<?= $entry['entry_source'] === 'manual' ? 'emerald' : 'blue' ?>-100 dark:bg-<?= $entry['entry_source'] === 'manual' ? 'emerald' : 'blue' ?>-900/50 flex items-center justify-center text-<?= $entry['entry_source'] === 'manual' ? 'emerald' : 'blue' ?>-600 dark:text-<?= $entry['entry_source'] === 'manual' ? 'emerald' : 'blue' ?>-400 font-bold text-lg shadow-sm">
                                                <?= strtoupper(substr($entry['candidate_name'], 0, 2)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-slate-900 dark:text-white"><?= htmlspecialchars($entry['candidate_name']) ?></div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1">
                                            <span class="material-icons text-[14px]">work_outline</span>
                                            <?= htmlspecialchars($entry['vacancy_title']) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-900 dark:text-slate-200"><?= htmlspecialchars($entry['email']) ?></div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1">
                                    <span class="material-icons text-[14px] text-slate-400">phone</span>
                                    <?= htmlspecialchars($entry['phone']) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1">
                                    <div class="text-xs text-slate-700 dark:text-slate-300 flex items-center gap-2">
                                        <span class="material-icons text-[16px] text-<?= $entry['gender'] == 'Male' ? 'blue' : 'pink' ?>-400"><?= $entry['gender'] == 'Male' ? 'male' : 'female' ?></span>
                                        <?= $entry['gender'] == 'Male' ? 'Laki-laki' : 'Perempuan' ?>
                                    </div>
                                    <?php if (!empty($entry['date_of_birth'])): ?>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                        <span class="material-icons text-[16px] text-slate-400">cake</span>
                                        <?= date('d M Y', strtotime($entry['date_of_birth'])) ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" style="background-color: <?= $entry['status_color'] ?>20; color: <?= $entry['status_color'] ?>; border: 1px solid <?= $entry['status_color'] ?>50;">
                                    <?= strtoupper(htmlspecialchars($entry['status_name_id'] ?? $entry['status_name'])) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 inline-flex items-center gap-1 text-xs leading-4 font-medium rounded bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-<?= $entry['entry_source'] === 'manual' ? 'blue' : 'green' ?>-500"></span>
                                    <?= $entry['entry_source'] === 'manual' ? 'Manual' : 'Online' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="<?= url('/crewing/manual-entries/detail/' . $entry['id']) ?>" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 p-1.5 rounded-full hover:bg-blue-50 dark:hover:bg-blue-900/30" title="View Details">
                                        <span class="material-icons text-[20px]">visibility</span>
                                    </a>
                                    <a href="<?= url('/crewing/manual-entries/edit/' . $entry['id']) ?>" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 p-1.5 rounded-full hover:bg-green-50 dark:hover:bg-green-900/30" title="Edit">
                                        <span class="material-icons text-[20px]">edit</span>
                                    </a>
                                    <button onclick="confirmDelete(<?= $entry['id'] ?>, '<?= htmlspecialchars(addslashes($entry['candidate_name'])) ?>')" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 p-1.5 rounded-full hover:bg-red-50 dark:hover:bg-red-900/30" title="Delete">
                                        <span class="material-icons text-[20px]">delete</span>
                                    </button>
                                    <button onclick="openArchiveModal(<?= $entry['id'] ?>, '<?= htmlspecialchars(addslashes($entry['candidate_name'])) ?>', '<?= htmlspecialchars($entry['status_name_id'] ?? $entry['status_name']) ?>', '<?= htmlspecialchars($entry['vacancy_title']) ?>', '<?= $entry['status_color'] ?>')" class="text-amber-600 dark:text-amber-400 hover:text-amber-900 dark:hover:text-amber-300 p-1.5 rounded-full hover:bg-amber-50 dark:hover:bg-amber-900/30" title="Arsipkan">
                                        <span class="material-icons text-[20px]">inventory_2</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Results Footer -->
            <div class="bg-surface-light dark:bg-surface-dark px-4 py-3 flex items-center justify-between border-t border-border-light dark:border-border-dark sm:px-6">
                <div class="flex-1 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-700 dark:text-slate-400">
                            Menampilkan
                            <span class="font-medium text-slate-900 dark:text-white"><?= count($entries) ?></span>
                            pelamar
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <footer class="text-center text-xs text-slate-400 pb-6 px-6">
            © 2026 IndoOcean Recruitment Platform. All rights reserved.
        </footer>
    <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-center w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full mx-auto mb-4">
            <span class="material-icons text-red-600 dark:text-red-400 text-[28px]">warning</span>
        </div>
        <h3 class="text-xl font-bold text-slate-900 dark:text-white text-center mb-2">Konfirmasi Hapus</h3>
        <p class="text-slate-600 dark:text-slate-400 text-center mb-4">
            Apakah Anda yakin ingin menghapus data pelamar <strong id="deleteTargetName" class="text-slate-900 dark:text-white"></strong>?
        </p>
        <p class="text-sm text-red-600 dark:text-red-400 text-center mb-6">⚠️ Semua data akan dihapus permanen dan tidak dapat dikembalikan.</p>
        <form id="deleteForm" method="POST">
            <?= csrf_field() ?>
            <div class="flex gap-3">
                <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                    <span class="material-icons text-[18px]">delete</span>
                    Hapus Permanen
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Archive Confirmation Modal -->
<div id="archiveModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="backdrop-filter:blur(4px);">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl p-0 max-w-lg w-full mx-4 overflow-hidden" style="animation: modalSlideUp 0.3s ease;">
        <!-- Header -->
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                    <span class="material-icons text-white text-[22px]">inventory_2</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">Arsipkan Pelamar</h3>
                    <p class="text-sm text-amber-100">Pindahkan data ke arsip</p>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="px-6 py-5">
            <!-- Applicant Info Card -->
            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4 mb-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                        <span class="material-icons text-blue-600 text-[20px]">person</span>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-slate-900 dark:text-white text-sm" id="archiveName"></div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5" id="archivePosition"></div>
                        <div class="mt-2">
                            <span class="px-2.5 py-0.5 inline-flex text-xs font-semibold rounded-full" id="archiveStatusBadge"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date/Time Info -->
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 mb-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-icons text-blue-600 text-[18px]">schedule</span>
                    <span class="text-xs font-semibold text-blue-700 dark:text-blue-300 uppercase">Waktu Arsip</span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">Hari</div>
                        <div class="text-sm font-semibold text-slate-900 dark:text-white" id="archiveDay"></div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">Tanggal</div>
                        <div class="text-sm font-semibold text-slate-900 dark:text-white" id="archiveDate"></div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">Jam</div>
                        <div class="text-sm font-semibold text-slate-900 dark:text-white" id="archiveTime"></div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">Zona Waktu</div>
                        <div class="text-sm font-semibold text-slate-900 dark:text-white">WIB (UTC+7)</div>
                    </div>
                </div>
            </div>

            <!-- Archive Notes -->
            <div class="mb-2">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Catatan Arsip (opsional)</label>
                <textarea id="archiveNotesInput" rows="3" class="block w-full px-3 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm transition-shadow" placeholder="Tulis alasan arsip, contoh: Sudah diterima dan diproses..."></textarea>
            </div>

            <!-- Warning -->
            <div class="flex items-start gap-2 text-xs text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 rounded-lg px-3 py-2">
                <span class="material-icons text-[16px] mt-0.5">info</span>
                <span>Data yang diarsip tidak akan tampil di daftar utama. Anda bisa memulihkan dari halaman Arsip kapan saja.</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700 flex gap-3">
            <button type="button" onclick="closeArchiveModal()" class="flex-1 px-4 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-700 dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-sm">
                Batal
            </button>
            <form id="archiveForm" method="POST" class="flex-1">
                <?= csrf_field() ?>
                <input type="hidden" name="archive_notes" id="archiveNotesHidden">
                <button type="submit" class="w-full px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-xl font-semibold transition-all flex items-center justify-center gap-2 text-sm shadow-lg shadow-amber-500/25">
                    <span class="material-icons text-[18px]">inventory_2</span>
                    Arsipkan Sekarang
                </button>
            </form>
        </div>
    </div>
</div>

<style>
@keyframes modalSlideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
// Filter by source (tabs)
let currentSourceFilter = '';

function filterBySource(source) {
    currentSourceFilter = source;
    
    // Update tab styles
    const tabs = {
        '': 'tabAll',
        'manual': 'tabManual',
        'online': 'tabOnline'
    };
    
    Object.entries(tabs).forEach(([filterSource, tabId]) => {
        const tab = document.getElementById(tabId);
        if (filterSource === source) {
            tab.className = 'px-4 py-2 rounded-md text-sm font-medium bg-primary text-white shadow-sm transition-all';
        } else {
            tab.className = 'px-4 py-2 rounded-md text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-all';
        }
    });
    
    filterTable();
}

// Filter table by search and source
function filterTable() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#entriesTable tbody tr');
    
    rows.forEach(row => {
        const rowSource = row.getAttribute('data-source');
        const candidateName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        const email = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        const position = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        
        const matchesSearch = !searchTerm || candidateName.includes(searchTerm) || email.includes(searchTerm) || position.includes(searchTerm);
        const matchesSource = !currentSourceFilter || rowSource === currentSourceFilter;
        
        row.style.display = (matchesSearch && matchesSource) ? '' : 'none';
    });
}

// Select all checkboxes
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
}

// Delete confirmation
function confirmDelete(id, name) {
    document.getElementById('deleteTargetName').textContent = name;
    document.getElementById('deleteForm').action = '<?= url('/crewing/manual-entries/delete/') ?>' + id;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
        closeArchiveModal();
    }
});

// === Archive Modal ===
function openArchiveModal(id, name, status, position, statusColor) {
    document.getElementById('archiveName').textContent = name;
    document.getElementById('archivePosition').textContent = position;
    
    const badge = document.getElementById('archiveStatusBadge');
    badge.textContent = status;
    badge.style.backgroundColor = statusColor + '20';
    badge.style.color = statusColor;
    badge.style.border = '1px solid ' + statusColor + '50';
    
    // Set current date/time in Indonesian
    const now = new Date();
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    document.getElementById('archiveDay').textContent = days[now.getDay()];
    document.getElementById('archiveDate').textContent = now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
    document.getElementById('archiveTime').textContent = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0') + ':' + now.getSeconds().toString().padStart(2, '0') + ' WIB';
    
    document.getElementById('archiveForm').action = '<?= url('/crewing/manual-entries/archive/') ?>' + id;
    document.getElementById('archiveNotesInput').value = '';
    
    document.getElementById('archiveModal').classList.remove('hidden');
    document.getElementById('archiveModal').classList.add('flex');
}

function closeArchiveModal() {
    document.getElementById('archiveModal').classList.add('hidden');
    document.getElementById('archiveModal').classList.remove('flex');
}

document.getElementById('archiveModal').addEventListener('click', function(e) {
    if (e.target === this) closeArchiveModal();
});

// Sync notes textarea to hidden field on submit
document.getElementById('archiveForm').addEventListener('submit', function() {
    document.getElementById('archiveNotesHidden').value = document.getElementById('archiveNotesInput').value;
});
</script>
