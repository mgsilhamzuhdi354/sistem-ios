<?php
/**
 * Admin Checklist - Index (Stage 2 of Recruitment Flow)
 * Modern UI with Tailwind CSS
 */
$currentPage = 'admin-checklist';
$candidates = $candidates ?? [];
$rejectedList = $rejectedList ?? [];
$stats = $stats ?? ['total' => 0, 'in_progress' => 0, 'completed' => 0, 'rejected' => 0];
?>
<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Checklist - IndoOcean ERP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } } };</script>
    <style>
        .progress-bar { transition: width 0.5s ease; }
        .card-hover { transition: all 0.2s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.3s ease forwards; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans">
<div class="flex h-screen overflow-hidden">

    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

    <main class="flex-1 flex flex-col h-screen overflow-hidden ml-64">
        <!-- Header -->
        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-6 flex-shrink-0 z-10">
            <div class="flex items-center gap-3">
                <span class="material-icons text-blue-600">checklist</span>
                <div>
                    <h1 class="text-base font-bold text-slate-800">Admin Checklist</h1>
                    <p class="text-xs text-slate-400">Stage 2 — Verifikasi & Pengecekan Kandidat</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?= BASE_URL ?>recruitment/pipeline" class="px-3 py-1.5 text-xs font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 flex items-center gap-1">
                    <span class="material-icons text-sm">filter_alt</span> Pipeline
                </a>
                <a href="<?= BASE_URL ?>Operational" class="px-3 py-1.5 text-xs font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 flex items-center gap-1">
                    <span class="material-icons text-sm">flight_takeoff</span> Operational
                </a>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-6">
            <!-- Flash Messages -->
            <?php if (!empty($flash)): ?>
                <div class="mb-4 px-4 py-3 rounded-lg text-sm <?= ($flash['type'] ?? '') === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' ?>">
                    <?= $flash['message'] ?? '' ?>
                </div>
            <?php endif; ?>

            <!-- Flow Indicator -->
            <div class="mb-6 bg-white rounded-xl border border-slate-200 p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-8">
                        <div class="flex items-center gap-2 text-slate-400">
                            <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-sm font-bold text-slate-500">1</div>
                            <span class="text-xs font-medium">Crewing Data</span>
                        </div>
                        <div class="w-12 h-0.5 bg-slate-200"></div>
                        <div class="flex items-center gap-2 text-blue-600">
                            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-sm font-bold text-white">2</div>
                            <span class="text-xs font-bold">Admin Checklist</span>
                        </div>
                        <div class="w-12 h-0.5 bg-slate-200"></div>
                        <div class="flex items-center gap-2 text-slate-400">
                            <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-sm font-bold text-slate-500">3</div>
                            <span class="text-xs font-medium">Operational</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm animate-fade-in">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-2xl font-bold text-slate-800"><?= $stats['total'] ?></p>
                            <p class="text-xs text-slate-500 mt-1">Total Kandidat</p>
                        </div>
                        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                            <span class="material-icons text-blue-600">people</span>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm animate-fade-in">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-2xl font-bold text-amber-600"><?= $stats['in_progress'] ?></p>
                            <p class="text-xs text-slate-500 mt-1">Sedang Diproses</p>
                        </div>
                        <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center">
                            <span class="material-icons text-amber-600">pending</span>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm animate-fade-in">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-2xl font-bold text-green-600"><?= $stats['completed'] ?></p>
                            <p class="text-xs text-slate-500 mt-1">Selesai → Operational</p>
                        </div>
                        <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                            <span class="material-icons text-green-600">check_circle</span>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm animate-fade-in">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-2xl font-bold text-red-600"><?= $stats['rejected'] ?></p>
                            <p class="text-xs text-slate-500 mt-1">Ditolak (Arsip)</p>
                        </div>
                        <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center">
                            <span class="material-icons text-red-600">cancel</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Candidates In Progress -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm mb-6">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-icons text-blue-600 text-lg">assignment</span>
                        <h2 class="font-semibold text-slate-800">Kandidat — Proses Checklist</h2>
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-bold rounded-full"><?= count($candidates) ?></span>
                    </div>
                </div>
                
                <?php if (empty($candidates)): ?>
                    <div class="p-12 text-center">
                        <span class="material-icons text-slate-300 text-5xl mb-3">inbox</span>
                        <p class="text-slate-400 text-sm">Belum ada kandidat yang perlu diproses.</p>
                        <p class="text-slate-300 text-xs mt-1">Kandidat akan muncul setelah di-claim dari Pipeline Recruitment.</p>
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-slate-100">
                        <?php foreach ($candidates as $i => $c): ?>
                            <a href="<?= BASE_URL ?>AdminChecklist/detail/<?= $c['crew_id'] ?>" class="flex items-center gap-4 px-5 py-4 card-hover hover:bg-slate-50 cursor-pointer">
                                <!-- Avatar -->
                                <div class="w-11 h-11 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                    <?= strtoupper(substr($c['full_name'] ?? 'U', 0, 2)) ?>
                                </div>
                                
                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="font-semibold text-slate-800 truncate"><?= htmlspecialchars($c['full_name'] ?? '') ?></p>
                                        <?php if ($c['employee_id']): ?>
                                            <span class="text-xs text-slate-400"><?= $c['employee_id'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex items-center gap-3 mt-0.5">
                                        <span class="text-xs text-slate-500"><?= $c['rank_name'] ?? 'N/A' ?></span>
                                        <span class="text-xs text-slate-300">•</span>
                                        <span class="text-xs text-slate-400"><?= $c['email'] ?? '' ?></span>
                                    </div>
                                    <?php 
                                    $recruiterMap = $recruiterMap ?? [];
                                    $rec = $recruiterMap[$c['crew_id']] ?? null;
                                    if ($rec): ?>
                                        <div class="flex items-center gap-1.5 mt-1">
                                            <span class="material-icons text-xs text-indigo-500">person</span>
                                            <span class="text-xs font-medium text-indigo-600">PIC: <?= htmlspecialchars($rec['recruiter_name']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Progress -->
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    <div class="text-right">
                                        <p class="text-sm font-bold <?= $c['progress'] == 6 ? 'text-green-600' : 'text-slate-700' ?>"><?= $c['progress'] ?>/<?= $c['progress_total'] ?></p>
                                        <p class="text-xs text-slate-400">items done</p>
                                    </div>
                                    <div class="w-24 h-2 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full progress-bar <?= $c['progress'] == 6 ? 'bg-green-500' : ($c['progress'] >= 3 ? 'bg-blue-500' : 'bg-amber-500') ?>" style="width: <?= $c['progress_percent'] ?>%"></div>
                                    </div>
                                    <span class="material-icons text-slate-300 text-lg">chevron_right</span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Rejected Archive -->
            <?php if (!empty($rejectedList)): ?>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                    <span class="material-icons text-red-500 text-lg">archive</span>
                    <h2 class="font-semibold text-slate-800">Arsip Rejected</h2>
                    <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs font-bold rounded-full"><?= count($rejectedList) ?></span>
                    <span class="ml-auto text-xs text-slate-400">Klik "Kembalikan" untuk aktifkan kembali ke Admin Checklist</span>
                </div>
                <div class="divide-y divide-slate-100">
                    <?php foreach ($rejectedList as $r): ?>
                        <div class="flex items-center gap-4 px-5 py-3">
                            <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center text-red-600 font-bold text-xs flex-shrink-0">
                                <?= strtoupper(substr($r['full_name'] ?? 'U', 0, 2)) ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-700 truncate"><?= htmlspecialchars($r['full_name'] ?? '') ?></p>
                                <p class="text-xs text-slate-400"><?= $r['rank_name'] ?? '' ?> • <?= $r['employee_id'] ?? '' ?></p>
                                <?php if ($r['rejected_reason']): ?>
                                    <p class="text-xs text-red-400 mt-0.5 truncate" title="<?= htmlspecialchars($r['rejected_reason']) ?>">
                                        ❌ <?= htmlspecialchars(mb_strimwidth($r['rejected_reason'], 0, 60, '...')) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Rejected</span>
                                <p class="text-xs text-slate-400 mt-0.5"><?= $r['rejected_at'] ? date('d M Y', strtotime($r['rejected_at'])) : '' ?></p>
                            </div>
                            <!-- Restore Button -->
                            <button onclick="showRestoreModal(<?= $r['crew_id'] ?>, '<?= htmlspecialchars(addslashes($r['full_name'] ?? '')) ?>')"
                                    class="flex-shrink-0 flex items-center gap-1 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-lg transition-colors">
                                <span class="material-icons text-xs">undo</span> Kembalikan
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="h-8"></div>
        </div>
    </main>
</div>

<!-- Restore From Archive Modal -->
<div id="restoreArchiveModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:20px;padding:32px;max-width:420px;width:90%;box-shadow:0 25px 60px rgba(0,0,0,0.3);position:relative;">
        <button onclick="document.getElementById('restoreArchiveModal').style.display='none'" style="position:absolute;top:12px;right:16px;background:none;border:none;font-size:1.5rem;color:#94a3b8;cursor:pointer;">×</button>
        <div style="width:70px;height:70px;border-radius:18px;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <span class="material-icons" style="font-size:36px;color:#d97706;">undo</span>
        </div>
        <h3 style="font-size:1.1rem;font-weight:700;color:#1e293b;margin-bottom:8px;text-align:center;">Kembalikan ke Admin Checklist?</h3>
        <p style="color:#64748b;font-size:0.85rem;line-height:1.6;margin-bottom:16px;text-align:center;" id="restoreModalDesc">Kandidat akan diaktifkan kembali dan masuk ke proses Admin Checklist.</p>
        <div style="margin-bottom:16px;">
            <label style="display:block;font-size:0.8rem;font-weight:600;color:#374151;margin-bottom:6px;">Alasan / Catatan:</label>
            <textarea id="restoreReasonInput" rows="2" placeholder="Contoh: Kandidat ingin mencoba kembali, dokumen sudah dilengkapi..."
                      style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:10px;font-size:0.85rem;resize:none;outline:none;"></textarea>
        </div>
        <div style="display:flex;gap:10px;justify-content:center;">
            <button id="btnDoRestore" onclick="doRestoreFromArchive()"
                    style="padding:10px 24px;border-radius:12px;border:none;background:#d97706;color:#fff;font-weight:600;font-size:0.85rem;cursor:pointer;display:flex;align-items:center;gap:6px;">
                <span class="material-icons" style="font-size:16px;">undo</span> Ya, Kembalikan
            </button>
            <button onclick="document.getElementById('restoreArchiveModal').style.display='none'"
                    style="padding:10px 20px;border-radius:12px;border:1px solid #e2e8f0;background:#f8fafc;color:#475569;font-weight:600;font-size:0.85rem;cursor:pointer;">Batal</button>
        </div>
    </div>
</div>

<script>
var _restoreCrewId = null;
function showRestoreModal(crewId, name) {
    _restoreCrewId = crewId;
    document.getElementById('restoreModalDesc').innerHTML = 'Kembalikan <strong>"' + name + '"</strong> dari Arsip Rejected ke Admin Checklist?';
    document.getElementById('restoreReasonInput').value = '';
    document.getElementById('restoreArchiveModal').style.display = 'flex';
    setTimeout(() => document.getElementById('restoreReasonInput').focus(), 100);
}
async function doRestoreFromArchive() {
    if (!_restoreCrewId) return;
    const reason = document.getElementById('restoreReasonInput').value.trim() || 'Dikembalikan ke Admin Checklist';
    const btn = document.getElementById('btnDoRestore');
    btn.innerHTML = '<span class="material-icons" style="font-size:16px;animation:spin 1s linear infinite;">sync</span> Memproses...';
    btn.disabled = true;
    try {
        const fd = new FormData();
        fd.append('reason', reason);
        const res = await fetch('<?= BASE_URL ?>AdminChecklist/restore-from-archive/' + _restoreCrewId, {
            method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const text = await res.text();
        let json;
        try { json = JSON.parse(text); } catch(e) {
            alert('Server error - cek error log PHP'); btn.innerHTML = '<span class="material-icons" style="font-size:16px;">undo</span> Ya, Kembalikan'; btn.disabled = false; return;
        }
        document.getElementById('restoreArchiveModal').style.display = 'none';
        if (json.success) {
            // Show toast and reload
            const toast = document.createElement('div');
            toast.style.cssText = 'position:fixed;top:20px;right:20px;background:#16a34a;color:#fff;padding:12px 20px;border-radius:12px;z-index:99999;font-weight:600;font-size:0.9rem;box-shadow:0 4px 20px rgba(0,0,0,0.2);';
            toast.innerHTML = '↩️ ' + json.message;
            document.body.appendChild(toast);
            setTimeout(() => { if (json.redirect_url) window.location.href = json.redirect_url; else window.location.reload(); }, 1500);
        } else {
            alert('Error: ' + (json.message || 'Gagal mengembalikan'));
            btn.innerHTML = '<span class="material-icons" style="font-size:16px;">undo</span> Ya, Kembalikan';
            btn.disabled = false;
        }
    } catch(e) {
        alert('Network error: ' + e.message);
        btn.innerHTML = '<span class="material-icons" style="font-size:16px;">undo</span> Ya, Kembalikan';
        btn.disabled = false;
    }
}
</script>
<style>@keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }</style>
</body>
</html>
