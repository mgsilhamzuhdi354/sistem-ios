<?php
/**
 * Modern Approval Center View - Enhanced
 * Features: detail modal, approve/reject modals, Buat Kontrak button
 */
$currentPage = 'recruitment-approval';
$pendingApprovals = $pendingApprovals ?? [];
$stats = $stats ?? [];
?>
<!DOCTYPE html>
<html lang="<?= session()->get('lang') ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Approval Center' ?> - IndoOcean ERP</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary": "#13ec5b",
                        "background-light": "#f6f8f6",
                        "paper-light": "#ffffff",
                    },
                    fontFamily: { display: ['Inter', 'sans-serif'] },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                        'card': '0 2px 8px rgba(0, 0, 0, 0.04), 0 0 1px rgba(0,0,0,0.1)',
                        'modal': '0 25px 50px -12px rgba(0,0,0,0.25)',
                    }
                }
            }
        };
    </script>
    <style>
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background-color: #e2e8f0; border-radius: 20px; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(30px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out forwards; }
        .animate-fade-in-d1 { animation-delay: 0.05s; }
        .animate-fade-in-d2 { animation-delay: 0.1s; }
        .animate-fade-in-d3 { animation-delay: 0.15s; }
        .modal-overlay { animation: fadeIn 0.2s ease-out; }
        .modal-content { animation: slideUp 0.3s ease-out; }
        .completeness-bar { transition: width 0.6s ease-out; }
    </style>
</head>
<body class="font-display bg-background-light text-slate-800 min-h-screen flex overflow-hidden">

<!-- Sidebar -->
<?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

<!-- Main Content -->
<main class="flex-1 flex flex-col h-screen overflow-y-auto bg-background-light ml-64">
    <!-- Header -->
    <header class="px-8 py-8 flex flex-col gap-4">
        <a href="<?= BASE_URL ?>recruitment/pipeline"
           class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-800 transition-colors text-sm font-medium w-fit">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <?= __('recruitment.back_to_pipeline') ?>
        </a>
        <div class="flex items-end justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight"><?= __('recruitment.approval_title') ?></h1>
                <p class="text-slate-500 mt-1"><?= __('recruitment.approval_subtitle') ?></p>
            </div>
            <div class="flex gap-3">
                <a href="<?= BASE_URL ?>contracts/create"
                   class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:translate-y-[-1px] transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">description</span>
                    <?= __('contracts.create_contract') ?>
                </a>
                <button onclick="window.location.reload()"
                        class="bg-slate-900 text-white px-5 py-2.5 rounded-lg text-sm font-semibold shadow-lg shadow-slate-200 hover:translate-y-[-1px] transition-transform flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">refresh</span>
                    <?= __('common.refresh') ?>
                </button>
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    <?php if (!empty($flash)): ?>
        <div class="px-8">
            <?php foreach ($flash as $type => $msg): ?>
                <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium 
                    <?= $type === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' ?>
                    <?= $type === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : '' ?>
                    <?= $type === 'warning' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' ?>">
                    <?= $msg ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Scrollable Content Area -->
    <div class="flex-1 px-8 pb-12">
        <div class="max-w-7xl mx-auto space-y-8">

            <!-- KPI Cards -->
            <section class="grid grid-cols-1 md:grid-cols-4 gap-5">
                <!-- Pending -->
                <div class="bg-paper-light rounded-2xl p-6 shadow-soft border border-slate-100 flex items-center justify-between group hover:border-blue-100 transition-colors opacity-0 animate-fade-in">
                    <div class="flex flex-col gap-1">
                        <span class="text-slate-500 text-sm font-medium"><?= __('recruitment.pending_approvals') ?></span>
                        <span class="text-3xl font-bold text-slate-900"><?= $stats['pending_count'] ?? 0 ?></span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined">hourglass_empty</span>
                    </div>
                </div>

                <!-- Approved -->
                <div class="bg-paper-light rounded-2xl p-6 shadow-soft border border-slate-100 flex items-center justify-between group hover:border-emerald-100 transition-colors opacity-0 animate-fade-in animate-fade-in-d1">
                    <div class="flex flex-col gap-1">
                        <span class="text-slate-500 text-sm font-medium"><?= __('recruitment.approved_30d') ?></span>
                        <span class="text-3xl font-bold text-emerald-600"><?= $stats['approved_count'] ?? 0 ?></span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined">check_circle</span>
                    </div>
                </div>

                <!-- Rejected -->
                <div class="bg-paper-light rounded-2xl p-6 shadow-soft border border-slate-100 flex items-center justify-between group hover:border-red-100 transition-colors opacity-0 animate-fade-in animate-fade-in-d2">
                    <div class="flex flex-col gap-1">
                        <span class="text-slate-500 text-sm font-medium"><?= __('recruitment.rejected_30d') ?></span>
                        <span class="text-3xl font-bold text-red-500"><?= $stats['rejected_count'] ?? 0 ?></span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-red-500 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined">cancel</span>
                    </div>
                </div>

                <!-- Total Processed -->
                <div class="bg-paper-light rounded-2xl p-6 shadow-soft border border-slate-100 flex items-center justify-between group hover:border-amber-100 transition-colors opacity-0 animate-fade-in animate-fade-in-d3">
                    <div class="flex flex-col gap-1">
                        <span class="text-slate-500 text-sm font-medium"><?= __('recruitment.total_processed') ?></span>
                        <span class="text-3xl font-bold text-slate-900"><?= $stats['total_processed'] ?? 0 ?></span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined">bolt</span>
                    </div>
                </div>
            </section>

            <?php if (empty($pendingApprovals)): ?>
            <!-- Empty State -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 min-h-[400px] opacity-0 animate-fade-in animate-fade-in-d3">
                <div class="lg:col-span-2 bg-paper-light rounded-2xl shadow-card border border-slate-100 flex flex-col items-center justify-center p-12 text-center relative overflow-hidden">
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-primary/5 rounded-full blur-3xl"></div>
                    <div class="relative z-10 flex flex-col items-center max-w-md">
                        <div class="w-28 h-28 rounded-full bg-emerald-50 flex items-center justify-center mb-6">
                            <span class="material-symbols-outlined text-5xl text-emerald-400" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-900 mb-3">Semua Clear! 🎉</h2>
                        <p class="text-slate-500 leading-relaxed">
                            Tidak ada approval yang pending saat ini.<br>
                            Semua pengajuan sudah diproses.
                        </p>
                    </div>
                </div>

                <div class="lg:col-span-1 flex flex-col gap-6">
                    <h3 class="text-lg font-bold text-slate-900"><?= __('common.quick_actions') ?></h3>
                    <a href="<?= BASE_URL ?>recruitment/pipeline"
                       class="bg-paper-light p-5 rounded-xl shadow-sm border border-slate-100 hover:shadow-md hover:border-indigo-200 transition-all group block">
                        <div class="flex items-start gap-4">
                            <div class="p-3 rounded-lg bg-indigo-50 text-indigo-600 group-hover:bg-indigo-100 transition-colors">
                                <span class="material-symbols-outlined">network_node</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-slate-900 group-hover:text-indigo-600 transition-colors">Pipeline</h4>
                                <p class="text-xs text-slate-500 mt-1">Lihat kandidat aktif</p>
                            </div>
                            <span class="material-symbols-outlined text-slate-300 group-hover:translate-x-1 transition-transform">chevron_right</span>
                        </div>
                    </a>
                    <a href="<?= BASE_URL ?>contracts/create"
                       class="bg-paper-light p-5 rounded-xl shadow-sm border border-slate-100 hover:shadow-md hover:border-emerald-200 transition-all group block">
                        <div class="flex items-start gap-4">
                            <div class="p-3 rounded-lg bg-emerald-50 text-emerald-600 group-hover:bg-emerald-100 transition-colors">
                                <span class="material-symbols-outlined">description</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-slate-900 group-hover:text-emerald-600 transition-colors">Buat Kontrak</h4>
                                <p class="text-xs text-slate-500 mt-1">Buat kontrak crew baru</p>
                            </div>
                            <span class="material-symbols-outlined text-slate-300 group-hover:translate-x-1 transition-transform">chevron_right</span>
                        </div>
                    </a>
                    <a href="<?= BASE_URL ?>recruitment/onboarding"
                       class="bg-paper-light p-5 rounded-xl shadow-sm border border-slate-100 hover:shadow-md hover:border-teal-200 transition-all group block">
                        <div class="flex items-start gap-4">
                            <div class="p-3 rounded-lg bg-teal-50 text-teal-600 group-hover:bg-teal-100 transition-colors">
                                <span class="material-symbols-outlined">person_add</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-slate-900 group-hover:text-teal-600 transition-colors">Onboarding</h4>
                                <p class="text-xs text-slate-500 mt-1">Import dari recruitment</p>
                            </div>
                            <span class="material-symbols-outlined text-slate-300 group-hover:translate-x-1 transition-transform">chevron_right</span>
                        </div>
                    </a>
                </div>
            </div>

            <?php else: ?>
            <!-- Pending Approvals Cards -->
            <div class="opacity-0 animate-fade-in animate-fade-in-d3">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <h3 class="text-lg font-bold text-slate-900"><?= __('recruitment.pending_approvals') ?></h3>
                        <span class="px-2.5 py-0.5 bg-blue-100 text-blue-700 text-xs font-bold rounded-full"><?= count($pendingApprovals) ?></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    <?php foreach ($pendingApprovals as $idx => $app): ?>
                    <div class="bg-paper-light rounded-2xl shadow-card border border-slate-100 p-6 hover:shadow-soft hover:border-blue-100 transition-all group"
                         id="card-<?= $app['id'] ?>">
                        <!-- Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-4">
                                <?php if (!empty($app['photo'])): ?>
                                    <img src="<?= BASE_URL ?>uploads/crew_photos/<?= htmlspecialchars($app['photo']) ?>"
                                         alt="Photo" class="w-14 h-14 rounded-xl object-cover border-2 border-white shadow-sm">
                                <?php else: ?>
                                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                                        <?= strtoupper(substr($app['applicant_name'] ?? 'N', 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <h4 class="text-base font-bold text-slate-900"><?= htmlspecialchars($app['applicant_name'] ?? '') ?></h4>
                                    <p class="text-sm text-slate-500"><?= htmlspecialchars($app['rank_name'] ?? 'Rank belum ditentukan') ?></p>
                                    <p class="text-xs text-slate-400 mt-0.5"><?= htmlspecialchars($app['employee_id'] ?? '') ?></p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-700">
                                <span class="material-symbols-outlined text-[12px] mr-1">schedule</span>
                                Pending
                            </span>
                        </div>

                        <!-- Info Grid -->
                        <div class="grid grid-cols-2 gap-3 mb-4 text-sm">
                            <div class="flex items-center gap-2 text-slate-500">
                                <span class="material-symbols-outlined text-[16px]">mail</span>
                                <span class="truncate"><?= htmlspecialchars($app['email'] ?? '-') ?></span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-500">
                                <span class="material-symbols-outlined text-[16px]">call</span>
                                <span><?= htmlspecialchars($app['phone'] ?? '-') ?></span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-500">
                                <span class="material-symbols-outlined text-[16px]">public</span>
                                <span><?= htmlspecialchars($app['nationality'] ?? '-') ?></span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-500">
                                <span class="material-symbols-outlined text-[16px]">folder</span>
                                <span><?= $app['doc_count'] ?? 0 ?> dokumen</span>
                            </div>
                        </div>

                        <!-- Completeness Bar -->
                        <div class="mb-5">
                            <div class="flex justify-between items-center mb-1.5">
                                <span class="text-xs font-semibold text-slate-500">Kelengkapan Data</span>
                                <span class="text-xs font-bold <?= ($app['completeness'] ?? 0) >= 80 ? 'text-emerald-600' : (($app['completeness'] ?? 0) >= 50 ? 'text-amber-600' : 'text-red-500') ?>">
                                    <?= $app['completeness'] ?? 0 ?>%
                                </span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="completeness-bar h-2 rounded-full <?= ($app['completeness'] ?? 0) >= 80 ? 'bg-emerald-500' : (($app['completeness'] ?? 0) >= 50 ? 'bg-amber-400' : 'bg-red-400') ?>"
                                     style="width: <?= $app['completeness'] ?? 0 ?>%"></div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2">
                            <button onclick="viewDetail(<?= $app['id'] ?>)"
                                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold rounded-xl transition-all">
                                <span class="material-symbols-outlined text-[16px]">visibility</span>
                                Detail
                            </button>
                            <button onclick="showApproveModal(<?= $app['id'] ?>, '<?= htmlspecialchars(addslashes($app['applicant_name'] ?? ''), ENT_QUOTES) ?>')"
                                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl transition-all shadow-sm">
                                <span class="material-symbols-outlined text-[16px]">check</span>
                                Approve
                            </button>
                            <button onclick="showRejectModal(<?= $app['id'] ?>, '<?= htmlspecialchars(addslashes($app['applicant_name'] ?? ''), ENT_QUOTES) ?>')"
                                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-xl transition-all shadow-sm">
                                <span class="material-symbols-outlined text-[16px]">close</span>
                                Reject
                            </button>
                        </div>

                        <!-- Submitted Date -->
                        <p class="text-[10px] text-slate-400 mt-3 text-right">
                            Diajukan: <?= !empty($app['created_at']) ? date('d M Y H:i', strtotime($app['created_at'])) : '-' ?>
                        </p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Footer -->
    <div class="px-8 py-4 text-center">
        <p class="text-xs text-slate-400">© <?= date('Y') ?> IndoOcean ERP System. All rights reserved.</p>
    </div>
</main>

<!-- ==================== MODALS ==================== -->

<!-- Detail Modal -->
<div id="detailModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm modal-overlay" onclick="closeModal('detailModal')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-modal w-full max-w-2xl max-h-[85vh] overflow-hidden modal-content">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined">person</span>
                    <h3 class="text-lg font-bold">Detail Kandidat</h3>
                </div>
                <button onclick="closeModal('detailModal')" class="p-1 hover:bg-white/20 rounded-lg transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[calc(85vh-140px)]" id="detailContent">
                <div class="flex items-center justify-center py-12">
                    <div class="animate-spin w-8 h-8 border-4 border-blue-200 border-t-blue-600 rounded-full"></div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 flex justify-between items-center bg-slate-50">
                <button onclick="closeModal('detailModal')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-100 transition-colors">Tutup</button>
                <div class="flex gap-2" id="detailActions"></div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm modal-overlay" onclick="closeModal('approveModal')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-modal w-full max-w-md modal-content">
            <div class="px-6 py-4 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined">verified</span>
                    <h3 class="text-lg font-bold">Approve Kandidat</h3>
                </div>
                <button onclick="closeModal('approveModal')" class="p-1 hover:bg-white/20 rounded-lg transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-6">
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-5">
                    <p class="text-sm text-emerald-700">
                        <span class="material-symbols-outlined text-[16px] align-middle mr-1">info</span>
                        Anda akan menyetujui <strong id="approveCrewName"></strong>. Status crew akan berubah menjadi <strong>Available</strong> dan siap untuk dibuatkan kontrak.
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Catatan (opsional)</label>
                    <textarea id="approvalNotes" rows="2"
                              class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-400 transition-colors resize-none"
                              placeholder="Catatan tambahan..."></textarea>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 flex justify-end gap-3 bg-slate-50">
                <input type="hidden" id="approveCrewId">
                <button onclick="closeModal('approveModal')" class="px-4 py-2.5 border border-slate-200 text-slate-600 rounded-xl text-sm font-medium hover:bg-slate-100 transition-colors">Batal</button>
                <button onclick="submitApprove()" id="approveSubmitBtn"
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold shadow-sm transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    Ya, Approve
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm modal-overlay" onclick="closeModal('rejectModal')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-modal w-full max-w-md modal-content">
            <div class="px-6 py-4 bg-gradient-to-r from-red-500 to-red-600 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined">block</span>
                    <h3 class="text-lg font-bold">Tolak Kandidat</h3>
                </div>
                <button onclick="closeModal('rejectModal')" class="p-1 hover:bg-white/20 rounded-lg transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-6">
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5">
                    <p class="text-sm text-red-700">
                        <span class="material-symbols-outlined text-[16px] align-middle mr-1">warning</span>
                        Anda akan menolak <strong id="rejectCrewName"></strong>. Tindakan ini akan mengubah status crew menjadi <strong>Rejected</strong>.
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea id="rejectionReason" rows="3"
                              class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-400 transition-colors resize-none"
                              placeholder="Berikan alasan penolakan..." required></textarea>
                    <p class="text-xs text-slate-400 mt-1.5">Wajib diisi sebagai catatan audit.</p>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 flex justify-end gap-3 bg-slate-50">
                <input type="hidden" id="rejectCrewId">
                <button onclick="closeModal('rejectModal')" class="px-4 py-2.5 border border-slate-200 text-slate-600 rounded-xl text-sm font-medium hover:bg-slate-100 transition-colors">Batal</button>
                <button onclick="submitReject()" id="rejectSubmitBtn"
                        class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-bold shadow-sm transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">block</span>
                    Ya, Tolak
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed bottom-6 right-6 z-[100] hidden">
    <div class="px-5 py-3 rounded-xl shadow-lg text-sm font-semibold flex items-center gap-2" id="toastContent"></div>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';

// ========== Modal Controls ==========
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.body.style.overflow = '';
}

// ========== Toast ==========
function showToast(msg, type = 'success') {
    const toast = document.getElementById('toast');
    const content = document.getElementById('toastContent');
    content.className = 'px-5 py-3 rounded-xl shadow-lg text-sm font-semibold flex items-center gap-2 ' +
        (type === 'success' ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white');
    content.innerHTML = `<span class="material-symbols-outlined text-[18px]">${type === 'success' ? 'check_circle' : 'error'}</span> ${msg}`;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 4000);
}

// ========== View Detail ==========
function viewDetail(crewId) {
    const content = document.getElementById('detailContent');
    const actions = document.getElementById('detailActions');
    content.innerHTML = '<div class="flex items-center justify-center py-12"><div class="animate-spin w-8 h-8 border-4 border-blue-200 border-t-blue-600 rounded-full"></div></div>';
    actions.innerHTML = '';
    openModal('detailModal');

    fetch(BASE_URL + 'recruitment/candidateDetail/' + crewId)
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                content.innerHTML = `<p class="text-red-500 text-center py-8">${data.error}</p>`;
                return;
            }
            const c = data.crew;
            const docs = data.documents || [];
            
            let photoHtml = '';
            if (c.photo) {
                photoHtml = `<img src="${BASE_URL}uploads/crew_photos/${c.photo}" alt="Photo" class="w-20 h-20 rounded-xl object-cover border-2 border-white shadow-md">`;
            } else {
                photoHtml = `<div class="w-20 h-20 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-2xl shadow-md">${(c.full_name||'N').charAt(0).toUpperCase()}</div>`;
            }

            content.innerHTML = `
                <div class="flex items-start gap-5 mb-6">
                    ${photoHtml}
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-slate-900">${c.full_name || '-'}</h3>
                        <p class="text-sm text-slate-500">${c.rank_name || '-'} · ${c.employee_id || ''}</p>
                        <span class="inline-flex items-center px-2.5 py-1 mt-2 rounded-full text-[11px] font-bold ${c.status === 'pending_approval' ? 'bg-amber-100 text-amber-700' : c.status === 'available' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'}">
                            ${(c.status || '').replace(/_/g,' ').toUpperCase()}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-slate-50 p-3.5 rounded-xl">
                        <p class="text-[10px] font-semibold text-slate-400 uppercase mb-1">Email</p>
                        <p class="text-sm text-slate-700">${c.email || '-'}</p>
                    </div>
                    <div class="bg-slate-50 p-3.5 rounded-xl">
                        <p class="text-[10px] font-semibold text-slate-400 uppercase mb-1">Telepon</p>
                        <p class="text-sm text-slate-700">${c.phone || '-'}</p>
                    </div>
                    <div class="bg-slate-50 p-3.5 rounded-xl">
                        <p class="text-[10px] font-semibold text-slate-400 uppercase mb-1">Gender</p>
                        <p class="text-sm text-slate-700">${c.gender === 'male' ? 'Laki-laki' : c.gender === 'female' ? 'Perempuan' : '-'}</p>
                    </div>
                    <div class="bg-slate-50 p-3.5 rounded-xl">
                        <p class="text-[10px] font-semibold text-slate-400 uppercase mb-1">Tanggal Lahir</p>
                        <p class="text-sm text-slate-700">${c.birth_date || '-'}</p>
                    </div>
                    <div class="bg-slate-50 p-3.5 rounded-xl">
                        <p class="text-[10px] font-semibold text-slate-400 uppercase mb-1">Kebangsaan</p>
                        <p class="text-sm text-slate-700">${c.nationality || '-'}</p>
                    </div>
                    <div class="bg-slate-50 p-3.5 rounded-xl">
                        <p class="text-[10px] font-semibold text-slate-400 uppercase mb-1">Pengalaman Laut</p>
                        <p class="text-sm text-slate-700">${c.total_sea_time_months ? c.total_sea_time_months + ' bulan' : '-'}</p>
                    </div>
                </div>

                ${c.address ? `
                <div class="bg-slate-50 p-3.5 rounded-xl mb-6">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase mb-1">Alamat</p>
                    <p class="text-sm text-slate-700">${c.address}${c.city ? ', ' + c.city : ''}${c.province ? ', ' + c.province : ''}${c.postal_code ? ' ' + c.postal_code : ''}</p>
                </div>` : ''}

                ${c.emergency_name ? `
                <div class="bg-orange-50 border border-orange-100 p-3.5 rounded-xl mb-6">
                    <p class="text-[10px] font-semibold text-orange-500 uppercase mb-1">Kontak Darurat</p>
                    <p class="text-sm text-slate-700">${c.emergency_name} (${c.emergency_relation || '-'}) — ${c.emergency_phone || '-'}</p>
                </div>` : ''}

                ${docs.length > 0 ? `
                <div>
                    <p class="text-sm font-bold text-slate-700 mb-3">Dokumen (${docs.length})</p>
                    <div class="space-y-2">
                        ${docs.map(d => `
                            <div class="flex items-center justify-between bg-slate-50 px-4 py-2.5 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[16px] text-slate-400">description</span>
                                    <span class="text-sm text-slate-700">${d.document_name || d.document_type || 'Document'}</span>
                                </div>
                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full ${d.status === 'valid' ? 'bg-emerald-100 text-emerald-700' : d.status === 'expired' ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-600'}">
                                    ${d.status || 'pending'}
                                </span>
                            </div>
                        `).join('')}
                    </div>
                </div>` : '<p class="text-sm text-slate-400 text-center py-4">Belum ada dokumen</p>'}

                ${c.notes ? `
                <div class="mt-6 bg-blue-50 border border-blue-100 p-3.5 rounded-xl">
                    <p class="text-[10px] font-semibold text-blue-500 uppercase mb-1">Catatan</p>
                    <p class="text-sm text-slate-700 whitespace-pre-line">${c.notes}</p>
                </div>` : ''}
            `;

            // Actions based on status
            if (c.status === 'pending_approval') {
                actions.innerHTML = `
                    <button onclick="closeModal('detailModal'); showApproveModal(${crewId}, '${(c.full_name||'').replace(/'/g, "\\'")}')"
                            class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">check</span> Approve
                    </button>
                    <button onclick="closeModal('detailModal'); showRejectModal(${crewId}, '${(c.full_name||'').replace(/'/g, "\\'")}')"
                            class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-bold transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">close</span> Reject
                    </button>`;
            } else if (c.status === 'available') {
                actions.innerHTML = `
                    <a href="${BASE_URL}contracts/create?crew_id=${crewId}"
                       class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold transition-colors flex items-center gap-2 no-underline">
                        <span class="material-symbols-outlined text-[16px]">description</span> Buat Kontrak
                    </a>`;
            }
        })
        .catch(err => {
            content.innerHTML = `<p class="text-red-500 text-center py-8">Error: ${err.message}</p>`;
        });
}

// ========== Approve ==========
function showApproveModal(crewId, name) {
    document.getElementById('approveCrewId').value = crewId;
    document.getElementById('approveCrewName').textContent = name;
    document.getElementById('approvalNotes').value = '';
    openModal('approveModal');
}

function submitApprove() {
    const crewId = document.getElementById('approveCrewId').value;
    const notes = document.getElementById('approvalNotes').value;
    const btn = document.getElementById('approveSubmitBtn');

    btn.disabled = true;
    btn.innerHTML = '<span class="animate-spin material-symbols-outlined text-[18px]">progress_activity</span> Memproses...';

    fetch(BASE_URL + 'recruitment/approve/' + crewId, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: 'approval_notes=' + encodeURIComponent(notes)
    })
    .then(r => r.json())
    .then(data => {
        closeModal('approveModal');
        if (data.success) {
            showToast(data.message, 'success');
            // Remove card with animation
            const card = document.getElementById('card-' + crewId);
            if (card) {
                card.style.transition = 'all 0.4s ease';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.95)';
                setTimeout(() => card.remove(), 400);
            }
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message || 'Gagal approve', 'error');
        }
    })
    .catch(err => {
        closeModal('approveModal');
        showToast('Error: ' + err.message, 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">check_circle</span> Ya, Approve';
    });
}

// ========== Reject ==========
function showRejectModal(crewId, name) {
    document.getElementById('rejectCrewId').value = crewId;
    document.getElementById('rejectCrewName').textContent = name;
    document.getElementById('rejectionReason').value = '';
    openModal('rejectModal');
}

function submitReject() {
    const crewId = document.getElementById('rejectCrewId').value;
    const reason = document.getElementById('rejectionReason').value.trim();
    const btn = document.getElementById('rejectSubmitBtn');

    if (!reason) {
        document.getElementById('rejectionReason').style.borderColor = '#ef4444';
        document.getElementById('rejectionReason').focus();
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="animate-spin material-symbols-outlined text-[18px]">progress_activity</span> Memproses...';

    fetch(BASE_URL + 'recruitment/reject/' + crewId, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: 'rejection_reason=' + encodeURIComponent(reason)
    })
    .then(r => r.json())
    .then(data => {
        closeModal('rejectModal');
        if (data.success) {
            showToast(data.message, 'success');
            const card = document.getElementById('card-' + crewId);
            if (card) {
                card.style.transition = 'all 0.4s ease';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.95)';
                setTimeout(() => card.remove(), 400);
            }
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message || 'Gagal reject', 'error');
        }
    })
    .catch(err => {
        closeModal('rejectModal');
        showToast('Error: ' + err.message, 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">block</span> Ya, Tolak';
    });
}

// Close modals on ESC
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        ['detailModal', 'approveModal', 'rejectModal'].forEach(id => closeModal(id));
    }
});
</script>
</body>
</html>
