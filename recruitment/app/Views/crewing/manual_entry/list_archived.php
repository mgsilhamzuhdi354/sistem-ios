<!-- Archived Applicants List -->
<script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<script>
    tailwind.config = {
        darkMode: "class",
        corePlugins: { preflight: false },
        theme: {
            extend: {
                colors: {
                    primary: "#0f172a",
                    secondary: "#3b82f6",
                    "background-light": "#f8fafc",
                    "surface-light": "#ffffff",
                    "surface-dark": "#1e293b",
                    "border-light": "#e2e8f0",
                    "border-dark": "#334155",
                },
                fontFamily: {
                    display: ["Inter", "sans-serif"],
                    body: ["Inter", "sans-serif"],
                },
            },
        },
    };
</script>
<style>
    .analytics-content { font-family: 'Inter', sans-serif; }
    .admin-content.crewing-content { padding: 0 !important; max-width: 100% !important; }
    body.admin-body { display: flex !important; min-height: 100vh !important; }
    .admin-sidebar {
        width: 260px !important; min-width: 260px !important;
        display: flex !important; flex-direction: column !important;
        position: fixed !important; left: 0 !important; top: 0 !important; bottom: 0 !important;
        z-index: 1000 !important;
        background: linear-gradient(180deg, #0A2463 0%, #16213e 100%) !important;
    }
    .admin-main { margin-left: 260px !important; flex: 1 !important; }
    .admin-header { display: flex !important; position: sticky !important; top: 0 !important; z-index: 100 !important; }
    .sidebar-nav ul { list-style: none !important; padding: 0 !important; margin: 0 !important; }
    .sidebar-nav .nav-link {
        display: flex !important; align-items: center !important; gap: 15px !important;
        padding: 14px 25px !important; color: rgba(255,255,255,0.7) !important; text-decoration: none !important; font-size: 14px !important;
    }
    .sidebar-nav .nav-link.active { color: #fff !important; background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%) !important; }
    .sidebar-nav .nav-link:hover { color: #fff !important; }
    .sidebar-header .logo { display: flex !important; align-items: center !important; gap: 12px !important; color: #fff !important; text-decoration: none !important; }
    .sidebar-header .logo img { width: 32px !important; height: 32px !important; }
    .sidebar-footer { padding: 20px !important; border-top: 1px solid rgba(255,255,255,0.1) !important; }
    .sidebar-footer .crewing-info { color: rgba(255,255,255,0.7) !important; }
    .sidebar-footer .logout-btn { display: flex !important; align-items: center !important; gap: 12px !important; color: rgba(255,255,255,0.7) !important; text-decoration: none !important; }
    @keyframes modalSlideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="analytics-content text-slate-800 antialiased">

    <!-- Hero Banner -->
    <div class="mx-6 mt-6 mb-6 bg-gradient-to-r from-amber-500 to-orange-500 rounded-2xl p-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="relative z-10 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                    <span class="material-icons text-[28px]">inventory_2</span>
                    Arsip Pelamar
                </h1>
                <p class="text-amber-100 text-sm mt-1">Data pelamar yang telah diarsipkan. Anda bisa memulihkan kapan saja.</p>
            </div>
            <a href="<?= url('/crewing/manual-entries') ?>" class="px-4 py-2.5 bg-white/15 hover:bg-white/25 text-white rounded-xl font-medium text-sm border border-white/25 transition-all flex items-center gap-2 backdrop-blur">
                <span class="material-icons text-[18px]">arrow_back</span>
                Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Archive Table -->
    <?php if (empty($entries)): ?>
        <div class="text-center py-16 px-6">
            <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-icons text-4xl text-amber-400">inventory_2</span>
            </div>
            <h3 class="text-xl font-semibold text-slate-800 mb-2">Arsip Kosong</h3>
            <p class="text-slate-500 mb-6">Belum ada data pelamar yang diarsipkan.</p>
            <a href="<?= url('/crewing/manual-entries') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 text-white rounded-xl hover:bg-amber-600 transition-colors font-medium text-sm">
                <span class="material-icons text-[18px]">arrow_back</span>
                Kembali ke Daftar Pelamar
            </a>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mx-6 mb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Pelamar</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Saat Arsip</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Waktu Diarsipkan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Catatan</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        <?php foreach ($entries as $entry): ?>
                        <?php
                            // Format archive date in full Indonesian
                            $archivedTs = strtotime($entry['archived_at']);
                            $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                            $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            $dayName = $days[date('w', $archivedTs)];
                            $dateFormatted = date('d', $archivedTs) . ' ' . $months[date('n', $archivedTs) - 1] . ' ' . date('Y', $archivedTs);
                            $timeFormatted = date('H:i:s', $archivedTs) . ' WIB';
                        ?>
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        <?php if (!empty($entry['avatar'])): ?>
                                            <img class="h-12 w-12 rounded-xl object-cover shadow-sm border border-slate-200" src="<?= url('/' . $entry['avatar']) ?>" alt="<?= htmlspecialchars($entry['candidate_name']) ?>"/>
                                        <?php else: ?>
                                            <div class="h-12 w-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 font-bold text-sm">
                                                <?= strtoupper(substr($entry['candidate_name'], 0, 2)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-semibold text-slate-900"><?= htmlspecialchars($entry['candidate_name']) ?></div>
                                        <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-1">
                                            <span class="material-icons text-[13px]">work_outline</span>
                                            <?= htmlspecialchars($entry['vacancy_title']) ?>
                                        </div>
                                        <div class="text-xs text-slate-400 mt-0.5"><?= htmlspecialchars($entry['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" style="background-color: <?= $entry['status_color'] ?>20; color: <?= $entry['status_color'] ?>; border: 1px solid <?= $entry['status_color'] ?>50;">
                                    <?= strtoupper(htmlspecialchars($entry['status_name_id'] ?? $entry['status_name'])) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="material-icons text-[14px] text-blue-500">today</span>
                                        <span class="text-sm font-semibold text-slate-900"><?= $dayName ?>, <?= $dateFormatted ?></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="material-icons text-[14px] text-blue-500">schedule</span>
                                        <span class="text-sm text-slate-600"><?= $timeFormatted ?></span>
                                    </div>
                                    <div class="text-xs text-slate-400">
                                        <?php
                                            $diff = time() - $archivedTs;
                                            if ($diff < 3600) echo floor($diff / 60) . ' menit yang lalu';
                                            elseif ($diff < 86400) echo floor($diff / 3600) . ' jam yang lalu';
                                            else echo floor($diff / 86400) . ' hari yang lalu';
                                        ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <?php if (!empty($entry['archive_notes'])): ?>
                                    <div class="bg-amber-50 rounded-lg px-3 py-2 max-w-xs">
                                        <div class="text-xs text-amber-800 leading-relaxed"><?= nl2br(htmlspecialchars($entry['archive_notes'])) ?></div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-xs text-slate-400 italic">Tidak ada catatan</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="confirmRestore(<?= $entry['id'] ?>, '<?= htmlspecialchars(addslashes($entry['candidate_name'])) ?>')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-semibold transition-colors" title="Pulihkan">
                                        <span class="material-icons text-[16px]">restore</span>
                                        Pulihkan
                                    </button>
                                    <a href="<?= url('/crewing/manual-entries/detail/' . $entry['id']) ?>" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold transition-colors" title="Detail">
                                        <span class="material-icons text-[16px]">visibility</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Footer -->
            <div class="bg-slate-50 px-6 py-3 border-t border-slate-200">
                <p class="text-sm text-slate-500">
                    Menampilkan <span class="font-semibold text-slate-800"><?= count($entries) ?></span> pelamar yang diarsipkan
                </p>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Restore Confirmation Modal -->
<div id="restoreModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="backdrop-filter:blur(4px);">
    <div class="bg-white rounded-2xl shadow-2xl p-0 max-w-md w-full mx-4 overflow-hidden" style="animation: modalSlideUp 0.3s ease;">
        <div class="bg-gradient-to-r from-emerald-500 to-green-500 px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <span class="material-icons text-white text-[22px]">restore</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">Pulihkan Pelamar</h3>
                    <p class="text-sm text-emerald-100">Kembalikan data ke daftar utama</p>
                </div>
            </div>
        </div>
        <div class="px-6 py-5">
            <p class="text-slate-600 text-sm">
                Apakah Anda yakin ingin memulihkan data pelamar <strong id="restoreTargetName" class="text-slate-900"></strong> dari arsip?
            </p>
            <p class="text-xs text-emerald-600 mt-3 flex items-center gap-1.5">
                <span class="material-icons text-[16px]">check_circle</span>
                Data akan kembali tampil di daftar pelamar utama.
            </p>
        </div>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex gap-3">
            <button type="button" onclick="closeRestoreModal()" class="flex-1 px-4 py-2.5 border border-slate-300 rounded-xl text-slate-700 font-medium hover:bg-slate-100 transition-colors text-sm">
                Batal
            </button>
            <form id="restoreForm" method="POST" class="flex-1">
                <?= csrf_field() ?>
                <button type="submit" class="w-full px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-green-500 hover:from-emerald-600 hover:to-green-600 text-white rounded-xl font-semibold transition-all flex items-center justify-center gap-2 text-sm shadow-lg shadow-emerald-500/25">
                    <span class="material-icons text-[18px]">restore</span>
                    Pulihkan
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function confirmRestore(id, name) {
    document.getElementById('restoreTargetName').textContent = name;
    document.getElementById('restoreForm').action = '<?= url('/crewing/manual-entries/unarchive/') ?>' + id;
    document.getElementById('restoreModal').classList.remove('hidden');
    document.getElementById('restoreModal').classList.add('flex');
}

function closeRestoreModal() {
    document.getElementById('restoreModal').classList.add('hidden');
    document.getElementById('restoreModal').classList.remove('flex');
}

document.getElementById('restoreModal').addEventListener('click', function(e) {
    if (e.target === this) closeRestoreModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeRestoreModal();
});
</script>
