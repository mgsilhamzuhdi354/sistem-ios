<?php
/**
 * Operational - Detail/Edit View (Stage 3)
 * Form to input hotel, transport, ticket, airport data
 */
$currentPage = 'operational';
$op = $op ?? [];
?>
<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Operational' ?> - IndoOcean ERP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } } };</script>
    <style>
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.3s ease forwards; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans">
<div class="flex h-screen overflow-hidden">

    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

    <main class="flex-1 flex flex-col h-screen overflow-hidden ml-64">
        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-6 flex-shrink-0 z-10">
            <div class="flex items-center gap-3">
                <a href="<?= BASE_URL ?>Operational" class="text-slate-400 hover:text-slate-600">
                    <span class="material-icons text-lg">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-base font-bold text-slate-800"><?= htmlspecialchars($op['full_name'] ?? '') ?></h1>
                    <p class="text-xs text-slate-400"><?= $op['rank_name'] ?? '' ?> • <?= $op['employee_id'] ?? '' ?> • Operational</p>
                </div>
            </div>
            <div>
                <?php if ($op['status'] === 'completed'): ?>
                    <span class="px-3 py-1 bg-green-100 text-green-700 font-bold text-xs rounded-full">✅ Completed</span>
                <?php else: ?>
                    <span class="px-3 py-1 bg-amber-100 text-amber-700 font-bold text-xs rounded-full">⏳ Pending</span>
                <?php endif; ?>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-6">
            <!-- Flash Messages -->
            <?php if (!empty($flash)): ?>
                <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium
                    <?= ($flash['type'] ?? '') === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' ?>
                    <?= ($flash['type'] ?? '') === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : '' ?>
                    <?= ($flash['type'] ?? '') === 'info' ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' ?>">
                    <?= htmlspecialchars($flash['message'] ?? '') ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- LEFT: Operational Form -->
                <div class="lg:col-span-2">
                    <form action="<?= BASE_URL ?>Operational/update/<?= $op['crew_id'] ?>" method="POST">
                        <input type="hidden" name="_action" id="formAction" value="">
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm animate-fade-in">
                            <div class="px-6 py-4 border-b border-slate-100">
                                <div class="flex items-center gap-2">
                                    <span class="material-icons text-green-600">edit_note</span>
                                    <h2 class="font-semibold text-slate-800">Data Keberangkatan</h2>
                                </div>
                            </div>

                            <div class="p-6 space-y-5">
                                <!-- Hotel -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                            <span class="material-icons text-sm align-middle mr-1">hotel</span>Nama Hotel
                                        </label>
                                        <input type="text" name="hotel_name" value="<?= htmlspecialchars($op['hotel_name'] ?? '') ?>"
                                               class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-green-400 focus:ring-1 focus:ring-green-100"
                                               placeholder="Nama hotel tempat crew menginap">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                            <span class="material-icons text-sm align-middle mr-1">event</span>Tanggal Keluar Hotel
                                        </label>
                                        <input type="date" name="hotel_checkout_date" value="<?= $op['hotel_checkout_date'] ?? '' ?>"
                                               class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-green-400 focus:ring-1 focus:ring-green-100">
                                    </div>
                                </div>

                                <!-- Transport -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                        <span class="material-icons text-sm align-middle mr-1">directions_car</span>Transport to Airport
                                    </label>
                                    <input type="text" name="transport_to_airport" value="<?= htmlspecialchars($op['transport_to_airport'] ?? '') ?>"
                                           class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-green-400 focus:ring-1 focus:ring-green-100"
                                           placeholder="Detail transportasi ke bandara (mobil, bus, dll)">
                                </div>

                                <!-- Ticket -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                        <span class="material-icons text-sm align-middle mr-1">confirmation_number</span>Code Book Tiket
                                    </label>
                                    <input type="text" name="ticket_booking_code" value="<?= htmlspecialchars($op['ticket_booking_code'] ?? '') ?>"
                                           class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-green-400 focus:ring-1 focus:ring-green-100 font-mono"
                                           placeholder="Kode booking tiket pesawat">
                                </div>

                                <!-- Airports -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                            <span class="material-icons text-sm align-middle mr-1">flight_takeoff</span>Airport Depart
                                        </label>
                                        <input type="text" name="airport_depart" value="<?= htmlspecialchars($op['airport_depart'] ?? '') ?>"
                                               class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-green-400 focus:ring-1 focus:ring-green-100"
                                               placeholder="Bandara keberangkatan (contoh: CGK)">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                            <span class="material-icons text-sm align-middle mr-1">flight_land</span>Airport Arrival
                                        </label>
                                        <input type="text" name="airport_arrival" value="<?= htmlspecialchars($op['airport_arrival'] ?? '') ?>"
                                               class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-green-400 focus:ring-1 focus:ring-green-100"
                                               placeholder="Bandara tujuan (contoh: SIN)">
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                        <span class="material-icons text-sm align-middle mr-1">note</span>Catatan Tambahan
                                    </label>
                                    <textarea name="notes" rows="3"
                                              class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-green-400 focus:ring-1 focus:ring-green-100"
                                              placeholder="Catatan tambahan..."><?= htmlspecialchars($op['notes'] ?? '') ?></textarea>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="px-6 py-4 border-t border-slate-100 flex flex-wrap items-center gap-3">
                                <button type="submit" class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold text-sm rounded-lg flex items-center gap-2 transition-colors">
                                    <span class="material-icons text-sm">save</span> Simpan Data
                                </button>

                                <?php if ($op['status'] !== 'completed'): ?>
                                <button type="button" onclick="validateAndComplete()" 
                                   class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg flex items-center gap-2 transition-colors">
                                    <span class="material-icons text-sm">check_circle</span> Selesai — On Board
                                </button>
                                <?php endif; ?>

                                <!-- Kembalikan ke Admin Checklist -->
                                <button type="button" onclick="showReturnToChecklistModal(<?= $op['crew_id'] ?>, '<?= htmlspecialchars(addslashes($op['full_name'] ?? '')) ?>')"
                                        class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-sm rounded-lg flex items-center gap-2 transition-colors ml-auto">
                                    <span class="material-icons text-sm">undo</span> Kembalikan ke Admin Checklist
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- RIGHT: Crew Info -->
                <div>
                    <div class="bg-white rounded-xl border border-slate-200 p-5 animate-fade-in">
                        <div class="text-center mb-4">
                            <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white font-bold text-xl mb-3">
                                <?= strtoupper(substr($op['full_name'] ?? 'U', 0, 2)) ?>
                            </div>
                            <h3 class="font-bold text-slate-800"><?= htmlspecialchars($op['full_name'] ?? '') ?></h3>
                            <p class="text-sm text-green-600 font-medium"><?= $op['rank_name'] ?? 'N/A' ?></p>
                            <p class="text-xs text-slate-400"><?= $op['employee_id'] ?? '' ?></p>
                        </div>

                        <div class="space-y-2 text-xs border-t border-slate-100 pt-3">
                            <?php if ($op['email'] ?? false): ?>
                            <div class="flex items-center gap-2">
                                <span class="material-icons text-slate-300 text-sm">email</span>
                                <span class="text-slate-600 truncate"><?= htmlspecialchars($op['email']) ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($op['phone'] ?? false): ?>
                            <div class="flex items-center gap-2">
                                <span class="material-icons text-slate-300 text-sm">phone</span>
                                <span class="text-slate-600"><?= htmlspecialchars($op['phone']) ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($op['nationality'] ?? false): ?>
                            <div class="flex items-center gap-2">
                                <span class="material-icons text-slate-300 text-sm">flag</span>
                                <span class="text-slate-600"><?= htmlspecialchars($op['nationality']) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <a href="<?= BASE_URL ?>crews/view/<?= $op['crew_id'] ?>" class="text-xs text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1">
                                <span class="material-icons text-sm">open_in_new</span> Lihat Profil Lengkap
                            </a>
                        </div>
                    </div>

                    <!-- Stage Progress -->
                    <div class="bg-white rounded-xl border border-slate-200 p-5 mt-4">
                        <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wide mb-3">Progress Flow</h3>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center">
                                    <span class="material-icons text-white text-xs">check</span>
                                </div>
                                <span class="text-xs text-green-700 font-medium">Crewing Data ✓</span>
                            </div>
                            <div class="ml-3 w-0.5 h-3 bg-green-200"></div>
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center">
                                    <span class="material-icons text-white text-xs">check</span>
                                </div>
                                <span class="text-xs text-green-700 font-medium">Admin Checklist ✓</span>
                            </div>
                            <div class="ml-3 w-0.5 h-3 bg-green-200"></div>
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded-full <?= $op['status'] === 'completed' ? 'bg-green-500' : 'bg-blue-500 animate-pulse' ?> flex items-center justify-center">
                                    <?php if ($op['status'] === 'completed'): ?>
                                        <span class="material-icons text-white text-xs">check</span>
                                    <?php else: ?>
                                        <span class="text-white text-xs font-bold">3</span>
                                    <?php endif; ?>
                                </div>
                                <span class="text-xs <?= $op['status'] === 'completed' ? 'text-green-700' : 'text-blue-700' ?> font-bold">
                                    Operational <?= $op['status'] === 'completed' ? '✓' : '(Aktif)' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="h-8"></div>
        </div>
    </main>
</div>

<!-- Return to Checklist Modal -->
<div id="returnToChecklistModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:20px;padding:32px;max-width:440px;width:90%;box-shadow:0 25px 60px rgba(0,0,0,0.3);position:relative;">
        <button onclick="document.getElementById('returnToChecklistModal').style.display='none'" style="position:absolute;top:12px;right:16px;background:none;border:none;font-size:1.5rem;color:#94a3b8;cursor:pointer;">×</button>
        <div style="width:70px;height:70px;border-radius:18px;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <span class="material-icons" style="font-size:36px;color:#d97706;">undo</span>
        </div>
        <h3 style="font-size:1.1rem;font-weight:700;color:#1e293b;margin-bottom:8px;text-align:center;">Kembalikan ke Admin Checklist?</h3>
        <p style="color:#64748b;font-size:0.85rem;line-height:1.6;margin-bottom:16px;text-align:center;"
           id="returnModalDesc">Crew akan dikembalikan ke tahap Admin Checklist untuk diproses ulang.</p>
        <div style="margin-bottom:16px;">
            <label style="display:block;font-size:0.8rem;font-weight:600;color:#374151;margin-bottom:6px;">Alasan:</label>
            <textarea id="returnReasonInput" rows="2" placeholder="Contoh: Dokumen perlu dilengkapi kembali..."
                      style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:10px;font-size:0.85rem;resize:none;outline:none;"></textarea>
        </div>
        <div style="display:flex;gap:10px;justify-content:center;">
            <button id="btnDoReturn" onclick="doReturnToChecklist()" 
                    style="padding:10px 24px;border-radius:12px;border:none;background:#d97706;color:#fff;font-weight:600;font-size:0.85rem;cursor:pointer;display:flex;align-items:center;gap:6px;">
                <span class="material-icons" style="font-size:16px;">undo</span> Ya, Kembalikan
            </button>
            <button onclick="document.getElementById('returnToChecklistModal').style.display='none'" 
                    style="padding:10px 20px;border-radius:12px;border:1px solid #e2e8f0;background:#f8fafc;color:#475569;font-weight:600;font-size:0.85rem;cursor:pointer;">Batal</button>
        </div>
    </div>
</div>

<script>
function validateAndComplete() {
    const hotel = document.querySelector('input[name="hotel_name"]');
    const airDepart = document.querySelector('input[name="airport_depart"]');
    const airArrival = document.querySelector('input[name="airport_arrival"]');
    
    const missing = [];
    [hotel, airDepart, airArrival].forEach(el => {
        el.style.borderColor = '';
        el.style.boxShadow = '';
    });
    
    if (!hotel.value.trim()) missing.push({el: hotel, name: 'Nama Hotel'});
    if (!airDepart.value.trim()) missing.push({el: airDepart, name: 'Airport Depart'});
    if (!airArrival.value.trim()) missing.push({el: airArrival, name: 'Airport Arrival'});
    
    if (missing.length > 0) {
        const names = missing.map(m => m.name).join(', ');
        missing.forEach(m => {
            m.el.style.borderColor = '#ef4444';
            m.el.style.boxShadow = '0 0 0 3px rgba(239,68,68,0.15)';
        });
        
        let toast = document.getElementById('validationToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'validationToast';
            toast.style.cssText = 'position:fixed;top:20px;right:20px;background:#ef4444;color:#fff;padding:14px 24px;border-radius:14px;z-index:99999;font-weight:600;font-size:0.9rem;box-shadow:0 8px 30px rgba(239,68,68,0.3);max-width:400px;transition:all 0.3s ease;';
            document.body.appendChild(toast);
        }
        toast.innerHTML = '⚠️ Isi dulu: <strong>' + names + '</strong> sebelum menyelesaikan!';
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
        setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateY(-20px)'; }, 4000);
        
        missing[0].el.focus();
        missing[0].el.scrollIntoView({behavior: 'smooth', block: 'center'});
        return;
    }
    
    // No confirm needed — user already filled data. Show loading and submit.
    const btn = document.querySelector('button[onclick="validateAndComplete()"]');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="material-icons text-sm" style="animation:spin 1s linear infinite">sync</span> Memproses...';
    }
    document.getElementById('formAction').value = 'complete';
    document.querySelector('form').submit();
}

var _returnCrewId = null;

function showReturnToChecklistModal(crewId, name) {
    _returnCrewId = crewId;
    document.getElementById('returnModalDesc').innerHTML = 'Kembalikan <strong>"' + name + '"</strong> dari Operational kembali ke Admin Checklist?';
    document.getElementById('returnReasonInput').value = '';
    document.getElementById('returnToChecklistModal').style.display = 'flex';
    setTimeout(() => document.getElementById('returnReasonInput').focus(), 100);
}

async function doReturnToChecklist() {
    if (!_returnCrewId) return;
    const reason = document.getElementById('returnReasonInput').value.trim() || 'Dikembalikan ke Admin Checklist';
    const btn = document.getElementById('btnDoReturn');
    btn.innerHTML = '<span class="material-icons" style="font-size:16px;animation:spin 1s linear infinite;">sync</span> Memproses...';
    btn.disabled = true;

    try {
        const fd = new FormData();
        fd.append('reason', reason);
        const res = await fetch('<?= BASE_URL ?>Operational/return-to-checklist/' + _returnCrewId, {
            method: 'POST', body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const text = await res.text();
        let json;
        try { json = JSON.parse(text); } catch(e) {
            console.error('Non-JSON response:', text);
            alert('Server error. Cek error log.');
            btn.innerHTML = '<span class="material-icons" style="font-size:16px;">undo</span> Ya, Kembalikan';
            btn.disabled = false;
            return;
        }
        document.getElementById('returnToChecklistModal').style.display = 'none';
        if (json.success) {
            // Show success and redirect to admin checklist
            const toast = document.createElement('div');
            toast.style.cssText = 'position:fixed;top:20px;right:20px;background:#d97706;color:#fff;padding:12px 20px;border-radius:12px;z-index:99999;font-weight:600;font-size:0.9rem;box-shadow:0 4px 20px rgba(0,0,0,0.2);';
            toast.innerHTML = '↩️ ' + json.message;
            document.body.appendChild(toast);
            setTimeout(() => {
                if (json.redirect_url) {
                    window.location.href = json.redirect_url;
                } else {
                    window.location.href = '<?= BASE_URL ?>AdminChecklist';
                }
            }, 1500);
        } else {
            alert('Error: ' + (json.message || 'Gagal kembalikan'));
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
