<?php
/**
 * Modern Recruitment Pipeline View
 * Using existing modern_sidebar.php partial
 */
$currentPage = 'recruitment-pipeline';
$candidates = $candidates ?? [];
$stats = $stats ?? [];

// Calculate stats from candidates
$total = count($candidates);
$newApps = count(array_filter($candidates, fn($c) => in_array($c['status_name'] ?? '', ['Applied', 'New', 'Screening', 'Pending'])));
$interview = count(array_filter($candidates, fn($c) => in_array($c['status_name'] ?? '', ['Interview', 'Technical Test', 'Final Review'])));
$approved = count(array_filter($candidates, fn($c) => in_array($c['status_name'] ?? '', ['Approved', 'Hired', 'Accepted'])));
$adminReview = count(array_filter($candidates, fn($c) => in_array($c['status_name'] ?? '', ['Admin Review', 'Processing'])));
$onBoard = count(array_filter($candidates, fn($c) => ($c['status_name'] ?? '') === 'On Board'));

// Status color mapping
function getStatusBadgeClasses($status) {
    $map = [
        'Applied' => 'bg-blue-100 text-blue-700',
        'New' => 'bg-blue-100 text-blue-700',
        'Pending' => 'bg-yellow-100 text-yellow-700',
        'Screening' => 'bg-indigo-100 text-indigo-700',
        'Document Review' => 'bg-cyan-100 text-cyan-700',
        'Review' => 'bg-cyan-100 text-cyan-700',
        'Interview' => 'bg-amber-100 text-amber-700',
        'Technical Test' => 'bg-purple-100 text-purple-700',
        'Final Review' => 'bg-orange-100 text-orange-700',
        'Admin Review' => 'bg-sky-100 text-sky-700',
        'Processing' => 'bg-amber-100 text-amber-700',
        'Approved' => 'bg-emerald-100 text-emerald-700',
        'Hired' => 'bg-green-100 text-green-700',
        'Accepted' => 'bg-green-100 text-green-700',
        'On Board' => 'bg-teal-100 text-teal-700',
        'Rejected' => 'bg-red-100 text-red-700',
        'Withdrawn' => 'bg-slate-100 text-slate-700',
    ];
    return $map[$status] ?? 'bg-slate-100 text-slate-600';
}

function getStatusIcon($status) {
    $map = [
        'Pending' => 'hourglass_empty',
        'Admin Review' => 'fact_check',
        'Processing' => 'sync',
        'Approved' => 'check_circle',
        'On Board' => 'sailing',
        'Rejected' => 'cancel',
        'Interview' => 'question_answer',
    ];
    return $map[$status] ?? 'circle';
}
?>
<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Recruitment Pipeline' ?> - IndoOcean ERP</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                }
            }
        };
    </script>
    <style>
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background-color: #e2e8f0; border-radius: 20px; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out forwards; }
        .animate-fade-in-d1 { animation-delay: 0.05s; }
        .animate-fade-in-d2 { animation-delay: 0.1s; }
        .animate-fade-in-d3 { animation-delay: 0.15s; }
        .animate-fade-in-d4 { animation-delay: 0.2s; }
        tr { transition: background-color 0.15s ease; }
        @keyframes bookFlip { 0%,100%{transform:rotateY(0deg)} 25%{transform:rotateY(-15deg)} 50%{transform:rotateY(15deg)} 75%{transform:rotateY(-8deg)} }
        @keyframes slideIn { from{opacity:0;transform:translateX(30px)} to{opacity:1;transform:translateX(0)} }
        .animate-slide-in { animation: slideIn 0.3s ease-out; }
        @keyframes popupIn { from{opacity:0;transform:scale(0.85) translateY(20px)} to{opacity:1;transform:scale(1) translateY(0)} }
        @keyframes overlayIn { from{opacity:0} to{opacity:1} }
        .popup-overlay{position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;animation:overlayIn 0.25s ease}
        .popup-box{background:#fff;border-radius:20px;padding:32px;max-width:440px;width:90%;box-shadow:0 25px 60px rgba(0,0,0,0.3);animation:popupIn 0.4s cubic-bezier(0.34,1.56,0.64,1);text-align:center;position:relative}
        .popup-close{position:absolute;top:12px;right:16px;background:none;border:none;font-size:1.5rem;color:#94a3b8;cursor:pointer;padding:4px 8px;border-radius:8px;transition:all 0.2s}
        .popup-close:hover{color:#ef4444;background:#fef2f2}
        .popup-icon{width:80px;height:80px;border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;perspective:200px}
        .popup-icon .material-icons{font-size:40px;animation:bookFlip 2s ease-in-out infinite}
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans">
<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden ml-64">
        <!-- Header -->
        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-6 flex-shrink-0 z-10">
            <div class="flex items-center gap-3">
                <h1 class="text-base font-bold text-slate-800 tracking-tight"><?= __('recruitment.pipeline_title') ?></h1>
                <span class="px-2 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-600 rounded-full uppercase tracking-wide">Live</span>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="showAnimatedPopup('onboarding')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-all shadow-sm">
                    <span class="material-icons text-sm">person_add</span>
                    <?= __('recruitment.onboarding_title') ?>
                </button>
            </div>
        </header>

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto p-6">
            <!-- Flash Messages -->
            <?php if (!empty($flash)): ?>
                <?php foreach ($flash as $type => $msg): ?>
                    <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium 
                        <?= $type === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' ?>
                        <?= $type === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : '' ?>
                        <?= $type === 'warning' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' ?>">
                        <?= $msg ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Page Title -->
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-6">
                <div>
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="material-icons text-blue-600 text-2xl">diversity_3</span>
                        <h2 class="text-2xl font-bold text-slate-800 tracking-tight"><?= __('recruitment.pipeline_title') ?></h2>
                    </div>
                    <p class="text-slate-500 text-sm"><?= __('recruitment.pipeline_subtitle') ?></p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Total Candidates -->
                <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm opacity-0 animate-fade-in">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide"><?= __('recruitment.candidate') ?></p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1"><?= $total ?></h3>
                        </div>
                        <div class="p-2 bg-blue-50 rounded-lg">
                            <span class="material-icons text-blue-600">people</span>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center text-xs text-slate-400">
                        <span class="material-icons text-sm mr-0.5">database</span>
                        <span><?= __('recruitment.total_source') ?></span>
                    </div>
                </div>

                <!-- New Applications -->
                <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm opacity-0 animate-fade-in animate-fade-in-d1">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide"><?= __('recruitment.new_applications') ?></p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1"><?= $newApps ?></h3>
                        </div>
                        <div class="p-2 bg-orange-50 rounded-lg">
                            <span class="material-icons text-orange-500">new_releases</span>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center text-xs text-slate-400">
                        <span><?= __('recruitment.applied_screening') ?></span>
                    </div>
                </div>

                <!-- Interview Stage -->
                <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm opacity-0 animate-fade-in animate-fade-in-d2">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide"><?= __('recruitment.interview_stage') ?></p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1"><?= $interview ?></h3>
                        </div>
                        <div class="p-2 bg-purple-50 rounded-lg">
                            <span class="material-icons text-purple-600">question_answer</span>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center text-xs text-slate-400">
                        <span><?= __('recruitment.active_interview') ?></span>
                    </div>
                </div>

                <!-- Approved -->
                <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm opacity-0 animate-fade-in animate-fade-in-d3">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide"><?= __('recruitment.approve') ?></p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1"><?= $approved + $onBoard ?></h3>
                        </div>
                        <div class="p-2 bg-emerald-50 rounded-lg">
                            <span class="material-icons text-emerald-600">check_circle</span>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center text-xs text-slate-400">
                        <span><?= __('recruitment.ready_to_deploy') ?></span>
                        <?php if ($onBoard > 0): ?>
                            <span class="ml-2 px-1.5 py-0.5 bg-teal-100 text-teal-600 rounded font-semibold"><?= $onBoard ?> On Board</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Candidates Table Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in animate-fade-in-d4" x-data="pipelineTable()">
                <!-- Search & Filter Bar -->
                <div class="px-5 py-3 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-3">
                    <div class="relative w-full sm:w-72">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="material-icons text-slate-400 text-lg">search</span>
                        </div>
                        <input type="text" x-model="search" @input="filterCandidates()"
                               class="block w-full pl-9 pr-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-colors"
                               placeholder="<?= __('common.search') ?>...">
                    </div>
                    <div class="flex gap-2">
                        <select x-model="statusFilter" @change="filterCandidates()"
                                class="px-3 py-2 border border-slate-200 rounded-lg text-xs font-medium text-slate-600 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                            <option value="">Semua</option>
                            <option value="Pending">Pending</option>
                            <option value="Admin Review">Admin Review</option>
                            <option value="Processing">Processing</option>
                            <option value="Screening">Screening</option>
                            <option value="Interview">Interview</option>
                            <option value="Approved">Approved</option>
                            <option value="On Board">On Board</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                        <button onclick="window.location.reload()"
                                class="inline-flex items-center gap-1 px-3 py-2 border border-slate-200 rounded-lg text-xs font-medium text-slate-600 bg-white hover:bg-slate-50 transition-colors">
                            <span class="material-icons text-sm">refresh</span>
                            Refresh
                        </button>
                    </div>
                </div>

                <!-- Table Header -->
                <div class="bg-slate-50 border-b border-slate-200">
                    <div class="grid grid-cols-12 px-5 py-2.5 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <div class="col-span-1"></div>
                        <div class="col-span-3">Nama</div>
                        <div class="col-span-2">Posisi</div>
                        <div class="col-span-2">Departemen</div>
                        <div class="col-span-2">Status</div>
                        <div class="col-span-2 text-right">Aksi</div>
                    </div>
                </div>

                <!-- Table Body -->
                <div class="divide-y divide-slate-100">
                    <?php if (!empty($candidates)): ?>
                        <?php foreach ($candidates as $idx => $candidate): ?>
                            <div class="grid grid-cols-12 items-center px-5 py-3 hover:bg-blue-50/40 transition-colors group candidate-row"
                                 data-name="<?= strtolower(htmlspecialchars($candidate['full_name'] ?? '')) ?>"
                                 data-position="<?= strtolower(htmlspecialchars($candidate['vacancy_title'] ?? '')) ?>"
                                 data-status="<?= htmlspecialchars($candidate['status_name'] ?? '') ?>">
                                 
                                <!-- Avatar -->
                                <div class="col-span-1">
                                    <?php if (!empty($candidate['avatar'])): ?>
                                        <?php
                                        $avatarVal = $candidate['avatar'];
                                        // If avatar contains a path (e.g. uploads/documents/...), use it relative to recruitment public root
                                        if (strpos($avatarVal, 'uploads/') === 0) {
                                            $avatarUrl = BASE_URL . '../recruitment/public/' . htmlspecialchars($avatarVal);
                                        } else {
                                            $avatarUrl = BASE_URL . '../recruitment/public/uploads/avatars/' . htmlspecialchars($avatarVal);
                                        }
                                        ?>
                                        <img src="<?= $avatarUrl ?>"
                                             alt="Avatar"
                                             class="w-14 h-14 rounded-full object-cover border-2 border-white shadow-sm"
                                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-sm shadow-sm" style="display:none;">
                                            <?= strtoupper(substr($candidate['full_name'] ?? 'N', 0, 1)) ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                            <?= strtoupper(substr($candidate['full_name'] ?? 'N', 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Name & Email -->
                                <div class="col-span-3 min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 truncate"><?= htmlspecialchars($candidate['full_name'] ?? '') ?></p>
                                    <p class="text-xs text-slate-400 truncate"><?= htmlspecialchars($candidate['email'] ?? '') ?></p>
                                </div>

                                <!-- Position -->
                                <div class="col-span-2">
                                    <p class="text-sm text-slate-600 truncate"><?= htmlspecialchars($candidate['vacancy_title'] ?? '-') ?></p>
                                </div>

                                <!-- Department -->
                                <div class="col-span-2">
                                    <p class="text-sm text-slate-600 truncate"><?= htmlspecialchars($candidate['department_name'] ?? '-') ?></p>
                                </div>

                                <!-- Status -->
                                <div class="col-span-2">
                                    <div class="flex flex-col gap-1">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold <?= getStatusBadgeClasses($candidate['status_name'] ?? '') ?>">
                                            <span class="material-icons text-[12px]"><?= getStatusIcon($candidate['status_name'] ?? '') ?></span>
                                            <?= htmlspecialchars($candidate['status_name'] ?? 'Unknown') ?>
                                        </span>
                                        <?php 
                                        $progress = intval($candidate['checklist_progress'] ?? 0);
                                        if (in_array($candidate['status_name'] ?? '', ['Admin Review', 'Processing']) && $progress >= 0): 
                                        ?>
                                            <div class="flex items-center gap-1.5">
                                                <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                                    <div class="h-full rounded-full transition-all duration-300 <?= $progress >= 6 ? 'bg-emerald-500' : ($progress >= 3 ? 'bg-amber-400' : 'bg-blue-400') ?>" 
                                                         style="width: <?= round(($progress / 6) * 100) ?>%"></div>
                                                </div>
                                                <span class="text-[10px] text-slate-400 font-medium"><?= $progress ?>/6</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="col-span-2 text-right">
                                    <?php $statusName = $candidate['status_name'] ?? ''; ?>
                                    <?php if (in_array($statusName, ['Pending', 'Applied', 'New']) && empty($candidate['sent_to_erp_at'])): ?>
                                        <!-- INLINE APPROVE/REJECT for Pending candidates -->
                                        <div class="flex items-center justify-end gap-1" id="inline-actions-<?= $candidate['id'] ?>">
                                            <a href="<?= BASE_URL ?>recruitment/candidate/<?= $candidate['id'] ?>"
                                               class="p-1.5 rounded-md hover:bg-blue-100 text-slate-400 hover:text-blue-600 transition-colors"
                                               title="Lihat Detail">
                                                <span class="material-icons text-lg">visibility</span>
                                            </a>
                                            <button onclick="doApproveInline(<?= $candidate['id'] ?>, this)"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-500 hover:bg-emerald-600 text-white text-[11px] font-semibold rounded-md transition-colors shadow-sm"
                                                    title="Approve Kandidat">
                                                <span class="material-icons text-sm">check</span>
                                                Approve
                                            </button>
                                            <button onclick="doRejectInline(<?= $candidate['id'] ?>, '<?= htmlspecialchars(addslashes($candidate['full_name'] ?? '')) ?>', this)"
                                                    class="inline-flex items-center gap-1 px-2 py-1 bg-white hover:bg-red-50 text-red-500 text-[11px] font-semibold rounded-md transition-colors border border-red-200"
                                                    title="Reject Kandidat">
                                                <span class="material-icons text-sm">close</span>
                                                Reject
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <!-- Normal actions for non-Pending candidates -->
                                        <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <a href="<?= BASE_URL ?>recruitment/candidate/<?= $candidate['id'] ?>"
                                               class="p-1.5 rounded-md hover:bg-blue-100 text-slate-400 hover:text-blue-600 transition-colors"
                                               title="Lihat Detail">
                                                <span class="material-icons text-lg">visibility</span>
                                            </a>
                                            <?php if ($statusName === 'Approved' && !empty($candidate['erp_crew_id'])): ?>
                                                <?php if (!empty($candidate['has_contract'])): ?>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-slate-100 text-slate-500 text-[10px] font-semibold rounded-full">
                                                        <span class="material-icons text-[12px]">task_alt</span>
                                                        Sudah Kontrak
                                                    </span>
                                                <?php else: ?>
                                                    <a href="<?= BASE_URL ?>contracts/create?crew_id=<?= $candidate['erp_crew_id'] ?>"
                                                       class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-semibold rounded-md transition-colors shadow-sm"
                                                       title="Buat Kontrak">
                                                        <span class="material-icons text-sm">description</span>
                                                        Kontrak
                                                    </a>
                                                <?php endif; ?>
                                            <?php elseif (!empty($candidate['sent_to_erp_at']) && empty($candidate['erp_crew_id'])): ?>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-semibold rounded-full">
                                                    <span class="material-icons text-[12px]">hourglass_empty</span>
                                                    Pending ERP
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($statusName === 'Rejected'): ?>
                                            <button onclick="restoreCandidate(<?= $candidate['id'] ?>, '<?= htmlspecialchars(addslashes($candidate['full_name'] ?? '')) ?>')"
                                               class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white text-[11px] font-semibold rounded-md transition-colors shadow-sm mt-1"
                                               title="Kembalikan Kandidat">
                                                <span class="material-icons text-sm">restore</span>
                                                Kembalikan
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <span class="text-[10px] text-slate-400 group-hover:hidden">
                                        <?= !empty($candidate['submitted_at']) ? date('d M Y', strtotime($candidate['submitted_at'])) : '-' ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Empty State -->
                        <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                            <div class="bg-slate-100 p-5 rounded-full mb-4">
                                <span class="material-icons text-4xl text-slate-300">inbox</span>
                            </div>
                            <h3 class="text-base font-semibold text-slate-700 mb-1"><?= __('recruitment.no_candidates') ?></h3>
                            <p class="text-slate-400 max-w-sm text-sm mb-6">
                                Belum ada data kandidat yang masuk ke dalam pipeline rekrutmen saat ini. 
                                Pastikan sistem recruitment terhubung dengan benar.
                            </p>
                            <div class="flex gap-2">
                                <button onclick="window.location.reload()"
                                        class="inline-flex items-center gap-1 px-3 py-2 border border-slate-300 text-sm font-medium rounded-lg text-slate-600 bg-white hover:bg-slate-50">
                                    <span class="material-icons text-sm">refresh</span>
                                    Refresh
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Table Footer / Info -->
                <?php if (!empty($candidates)): ?>
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50 flex justify-between items-center">
                    <p class="text-xs text-slate-400">
                        Menampilkan <span class="font-semibold text-slate-600" x-text="visibleCount"><?= count($candidates) ?></span> 
                        dari <span class="font-semibold text-slate-600"><?= count($candidates) ?></span> kandidat
                    </p>
                    <p class="text-xs text-slate-400">
                        <span class="material-icons text-[12px] align-middle">schedule</span>
                        Data dari recruitment DB
                    </p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Footer -->
            <div class="mt-6 text-center">
                <p class="text-xs text-slate-400">© <?= date('Y') ?> IndoOcean ERP System. All rights reserved.</p>
            </div>
        </div>
    </main>
</div>

<?php
// Build candidate data for JS popups
$pendingCandidates = array_filter($candidates, fn($c) => in_array($c['status_name'] ?? '', ['Pending', 'Applied', 'New']));
$approvedCandidates = array_filter($candidates, fn($c) => in_array($c['status_name'] ?? '', ['Approved', 'Hired', 'Accepted', 'On Board', 'Admin Review', 'Processing']));
$rejectedCandidates = array_filter($candidates, fn($c) => ($c['status_name'] ?? '') === 'Rejected');
?>

<script>
function pipelineTable() {
    return {
        search: '',
        statusFilter: '',
        visibleCount: <?= count($candidates) ?>,
        filterCandidates() {
            const rows = document.querySelectorAll('.candidate-row');
            let count = 0;
            const searchLower = this.search.toLowerCase();
            rows.forEach(row => {
                const name = row.dataset.name || '';
                const position = row.dataset.position || '';
                const status = row.dataset.status || '';
                const matchSearch = !searchLower || name.includes(searchLower) || position.includes(searchLower);
                const matchStatus = !this.statusFilter || status === this.statusFilter;
                if (matchSearch && matchStatus) { row.style.display = ''; count++; }
                else { row.style.display = 'none'; }
            });
            this.visibleCount = count;
        }
    };
}

// === RESTORE CANDIDATE (Kembalikan) ===
async function restoreCandidate(applicationId, name) {
    // Show animated confirm popup
    const existing = document.querySelector('.popup-overlay');
    if (existing) existing.remove();

    const overlay = document.createElement('div');
    overlay.className = 'popup-overlay';
    overlay.onclick = function(e) { if (e.target === overlay) overlay.remove(); };
    overlay.innerHTML = `
        <div class="popup-box" style="max-width:400px">
            <button class="popup-close" onclick="this.closest('.popup-overlay').remove()">&times;</button>
            <div class="popup-icon" style="background:#fef3c7">
                <span class="material-icons" style="color:#d97706">restore</span>
            </div>
            <h3 style="font-size:1.15rem;font-weight:700;color:#1e293b;margin-bottom:8px">Kembalikan Kandidat?</h3>
            <p style="color:#64748b;font-size:0.85rem;line-height:1.5;margin-bottom:20px">
                Apakah Anda yakin ingin mengembalikan <strong>"${name}"</strong> dari status Rejected?<br>
                Kandidat akan dikembalikan ke tahap review untuk diproses ulang.
            </p>
            <div style="display:flex;gap:10px;justify-content:center">
                <button id="btnDoRestore" style="padding:10px 24px;border-radius:12px;border:none;background:#d97706;color:#fff;font-weight:600;font-size:0.85rem;cursor:pointer;display:flex;align-items:center;gap:6px">
                    <span class="material-icons" style="font-size:16px">check</span> Ya, Kembalikan
                </button>
                <button onclick="this.closest('.popup-overlay').remove()" style="padding:10px 20px;border-radius:12px;border:1px solid #e2e8f0;background:#f8fafc;color:#475569;font-weight:600;font-size:0.85rem;cursor:pointer">Batal</button>
            </div>
        </div>
    `;
    document.body.appendChild(overlay);

    document.getElementById('btnDoRestore').onclick = async function() {
        this.innerHTML = '<span class="material-icons" style="font-size:16px;animation:spin 1s linear infinite">sync</span> Memproses...';
        this.disabled = true;
        try {
            const fd = new FormData();
            fd.append('application_id', applicationId);
            const res = await fetch('<?= BASE_URL ?>recruitment/restore-rejected', { method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'} });
            const json = await res.json();
            overlay.remove();
            showToast(json.success ? 'success' : 'error', json.message || 'Unknown error');
            if (json.success) setTimeout(() => window.location.reload(), 1200);
        } catch(e) {
            overlay.remove();
            showToast('error', 'Error: ' + e.message);
        }
    };
}

// === TOAST NOTIFICATION ===
function showToast(type, msg) {
    const colors = { success: 'bg-emerald-600', error: 'bg-red-600', info: 'bg-blue-600' };
    const icons = { success: 'check_circle', error: 'error', info: 'info' };
    const toast = document.createElement('div');
    toast.className = 'fixed top-6 right-6 ' + (colors[type]||colors.info) + ' text-white px-5 py-3 rounded-xl shadow-xl z-[99999] flex items-center gap-2 animate-slide-in';
    toast.innerHTML = `<span class="material-icons">${icons[type]||'info'}</span> ${msg}`;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity 0.3s'; setTimeout(() => toast.remove(), 300); }, 3000);
}

// === POPUP HANDLER ===
function showAnimatedPopup(type) {
    const existing = document.querySelector('.popup-overlay');
    if (existing) existing.remove();

    if (type === 'onboarding') { showOnboardingPopup(); return; }
}

// Approval popup removed — approve/reject is now inline in the table

// === INLINE ONBOARDING POPUP ===
function showOnboardingPopup() {
    const overlay = document.createElement('div');
    overlay.className = 'popup-overlay';
    overlay.onclick = function(e) { if (e.target === overlay) overlay.remove(); };

    let listHtml = '';
    <?php foreach (array_values($approvedCandidates) as $ac):
        $statusColors = ['Approved'=>'#10b981','Admin Review'=>'#3b82f6','Processing'=>'#f59e0b','On Board'=>'#14b8a6','Hired'=>'#10b981','Accepted'=>'#10b981'];
        $sc = $statusColors[$ac['status_name']] ?? '#64748b';
    ?>
    listHtml += `
        <div class="flex items-center justify-between py-3 px-4 rounded-lg hover:bg-slate-50 border border-slate-100 mb-2">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-sm"><?= strtoupper(substr($ac['full_name'] ?? 'N', 0, 1)) ?></div>
                <div>
                    <p style="font-weight:600;color:#1e293b;font-size:0.85rem"><?= htmlspecialchars($ac['full_name'] ?? '') ?></p>
                    <p style="color:#94a3b8;font-size:0.75rem"><?= htmlspecialchars($ac['vacancy_title'] ?? '-') ?></p>
                </div>
            </div>
            <span style="padding:3px 10px;border-radius:20px;font-size:0.7rem;font-weight:600;color:#fff;background:<?= $sc ?>"><?= htmlspecialchars($ac['status_name'] ?? '') ?></span>
        </div>
    `;
    <?php endforeach; ?>

    if (!listHtml) listHtml = '<div style="text-align:center;padding:20px;color:#94a3b8"><span class="material-icons" style="font-size:32px;display:block;margin-bottom:8px">inbox</span>Tidak ada kandidat onboarding</div>';

    overlay.innerHTML = `
        <div class="popup-box" style="max-width:560px;text-align:left">
            <button class="popup-close" onclick="this.closest('.popup-overlay').remove()">&times;</button>
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
                <div class="popup-icon" style="width:50px;height:50px;background:#dbeafe;margin:0;border-radius:14px">
                    <span class="material-icons" style="font-size:28px;color:#2563eb">menu_book</span>
                </div>
                <div>
                    <h3 style="font-size:1.15rem;font-weight:700;color:#1e293b;margin:0">Onboarding Center</h3>
                    <p style="color:#64748b;font-size:0.8rem;margin:0">Kandidat yang sudah disetujui dan dalam proses</p>
                </div>
            </div>
            <div style="background:#f8fafc;border-radius:12px;padding:4px;max-height:350px;overflow-y:auto">
                ${listHtml}
            </div>
            <div style="margin-top:16px;text-align:right">
                <a href="<?= BASE_URL ?>recruitment/onboarding" style="padding:8px 16px;border-radius:10px;border:none;background:#2563eb;color:#fff;font-weight:600;font-size:0.85rem;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:6px;margin-right:8px">
                    <span class="material-icons" style="font-size:14px">open_in_new</span> Lihat Detail Lengkap
                </a>
                <button onclick="this.closest('.popup-overlay').remove()" style="padding:8px 20px;border-radius:10px;border:1px solid #e2e8f0;background:#fff;color:#475569;font-weight:600;font-size:0.85rem;cursor:pointer">Tutup</button>
            </div>
        </div>
    `;
    document.body.appendChild(overlay);
}

// === INLINE APPROVE/REJECT (directly in pipeline table) ===
async function doApproveInline(appId, btn) {
    // Show confirmation modal first
    const existing = document.querySelector('.popup-overlay');
    if (existing) existing.remove();

    const overlay = document.createElement('div');
    overlay.className = 'popup-overlay';
    overlay.onclick = function(e) { if (e.target === overlay) overlay.remove(); };
    overlay.innerHTML = `
        <div class="popup-box" style="max-width:400px">
            <button class="popup-close" onclick="this.closest('.popup-overlay').remove()">&times;</button>
            <div class="popup-icon" style="background:#dcfce7">
                <span class="material-icons" style="color:#16a34a">verified_user</span>
            </div>
            <h3 style="font-size:1.15rem;font-weight:700;color:#1e293b;margin-bottom:8px">Approve Kandidat?</h3>
            <p style="color:#64748b;font-size:0.85rem;line-height:1.5;margin-bottom:20px">
                Kandidat akan diimport ke ERP dan lanjut ke tahap <strong>Admin Checklist</strong>.
            </p>
            <div style="margin-bottom:16px">
                <label style="display:block;font-size:0.8rem;font-weight:600;color:#374151;margin-bottom:6px">Catatan (opsional):</label>
                <textarea id="approveNotesInput" rows="2" placeholder="Catatan approval..." style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:10px;font-size:0.85rem;resize:none;outline:none"></textarea>
            </div>
            <div style="display:flex;gap:10px;justify-content:center">
                <button id="btnDoApprove" style="padding:10px 24px;border-radius:12px;border:none;background:#16a34a;color:#fff;font-weight:600;font-size:0.85rem;cursor:pointer;display:flex;align-items:center;gap:6px">
                    <span class="material-icons" style="font-size:16px">check_circle</span> Ya, Approve
                </button>
                <button onclick="this.closest('.popup-overlay').remove()" style="padding:10px 20px;border-radius:12px;border:1px solid #e2e8f0;background:#f8fafc;color:#475569;font-weight:600;font-size:0.85rem;cursor:pointer">Batal</button>
            </div>
        </div>
    `;
    document.body.appendChild(overlay);

    document.getElementById('btnDoApprove').onclick = async function() {
        const notes = document.getElementById('approveNotesInput').value || 'Approved via Pipeline';
        this.innerHTML = '<span class="material-icons" style="font-size:16px;animation:spin 1s linear infinite">sync</span> Memproses...';
        this.disabled = true;
        overlay.remove();

        // Show spinner on original button
        btn.innerHTML = '<span class="material-icons" style="font-size:14px;animation:spin 1s linear infinite">sync</span>';
        btn.disabled = true;

        try {
            const fd = new FormData();
            fd.append('approval_notes', notes);
            const res = await fetch('<?= BASE_URL ?>recruitment/approve-recruitment/' + appId, { method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'} });
            const text = await res.text();
            let json;
            try { json = JSON.parse(text); } catch(pe) {
                console.error('Non-JSON response:', text);
                showToast('error', 'Server error - cek PHP error log');
                btn.innerHTML = '<span class="material-icons" style="font-size:14px">check</span> Approve';
                btn.disabled = false;
                return;
            }
            const actionsDiv = document.getElementById('inline-actions-' + appId);
            if (json.success) {
                if (actionsDiv) {
                    actionsDiv.innerHTML = '<span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-100 text-emerald-700 text-[11px] font-semibold rounded-full"><span class="material-icons text-sm">check_circle</span> Processing...</span>';
                }
                showToast('success', json.message || 'Kandidat berhasil di-approve! Redirect ke Admin Checklist...');
                // Follow the redirect if provided, else reload
                if (json.redirect_url) {
                    setTimeout(() => { window.location.href = json.redirect_url; }, 1500);
                } else {
                    setTimeout(() => window.location.reload(), 1500);
                }
            } else {
                showToast('error', json.message || 'Gagal approve');
                btn.innerHTML = '<span class="material-icons" style="font-size:14px">check</span> Approve';
                btn.disabled = false;
            }
        } catch(e) {
            showToast('error', 'Network error: ' + e.message);
            btn.innerHTML = '<span class="material-icons" style="font-size:14px">check</span> Approve';
            btn.disabled = false;
        }
    };
}

async function doRejectInline(appId, name, btn) {
    // Professional reject modal
    const existing = document.querySelector('.popup-overlay');
    if (existing) existing.remove();

    const overlay = document.createElement('div');
    overlay.className = 'popup-overlay';
    overlay.onclick = function(e) { if (e.target === overlay) overlay.remove(); };
    overlay.innerHTML = `
        <div class="popup-box" style="max-width:400px">
            <button class="popup-close" onclick="this.closest('.popup-overlay').remove()">&times;</button>
            <div class="popup-icon" style="background:#fee2e2">
                <span class="material-icons" style="color:#dc2626">cancel</span>
            </div>
            <h3 style="font-size:1.15rem;font-weight:700;color:#1e293b;margin-bottom:8px">Reject Kandidat?</h3>
            <p style="color:#64748b;font-size:0.85rem;line-height:1.5;margin-bottom:16px">
                Tolak aplikasi <strong>"${name}"</strong>? Kandidat akan dipindahkan ke arsip dan bisa dikembalikan.
            </p>
            <div style="margin-bottom:16px">
                <label style="display:block;font-size:0.8rem;font-weight:600;color:#374151;margin-bottom:6px">Alasan penolakan <span style="color:#dc2626">*</span>:</label>
                <textarea id="rejectReasonInput" rows="3" placeholder="Contoh: Tidak memenuhi kualifikasi, dokumen tidak lengkap..." style="width:100%;padding:8px 12px;border:1px solid #fca5a5;border-radius:10px;font-size:0.85rem;resize:none;outline:none"></textarea>
            </div>
            <div style="display:flex;gap:10px;justify-content:center">
                <button id="btnDoReject" style="padding:10px 24px;border-radius:12px;border:none;background:#dc2626;color:#fff;font-weight:600;font-size:0.85rem;cursor:pointer;display:flex;align-items:center;gap:6px">
                    <span class="material-icons" style="font-size:16px">cancel</span> Ya, Tolak
                </button>
                <button onclick="this.closest('.popup-overlay').remove()" style="padding:10px 20px;border-radius:12px;border:1px solid #e2e8f0;background:#f8fafc;color:#475569;font-weight:600;font-size:0.85rem;cursor:pointer">Batal</button>
            </div>
        </div>
    `;
    document.body.appendChild(overlay);
    setTimeout(() => document.getElementById('rejectReasonInput')?.focus(), 100);

    document.getElementById('btnDoReject').onclick = async function() {
        const reason = (document.getElementById('rejectReasonInput').value || '').trim();
        if (!reason) {
            document.getElementById('rejectReasonInput').style.borderColor = '#dc2626';
            document.getElementById('rejectReasonInput').placeholder = 'Alasan wajib diisi!';
            return;
        }
        this.innerHTML = '<span class="material-icons" style="font-size:16px;animation:spin 1s linear infinite">sync</span> Memproses...';
        this.disabled = true;
        overlay.remove();

        btn.innerHTML = '<span class="material-icons" style="font-size:14px;animation:spin 1s linear infinite">sync</span>';
        btn.disabled = true;

        try {
            const fd = new FormData();
            fd.append('rejection_reason', reason);
            const res = await fetch('<?= BASE_URL ?>recruitment/reject-recruitment/' + appId, { method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'} });
            const text = await res.text();
            let json;
            try { json = JSON.parse(text); } catch(pe) {
                showToast('error', 'Server error - cek PHP error log');
                btn.innerHTML = '<span class="material-icons" style="font-size:14px">close</span> Reject';
                btn.disabled = false;
                return;
            }
            const actionsDiv = document.getElementById('inline-actions-' + appId);
            if (json.success) {
                if (actionsDiv) {
                    actionsDiv.innerHTML = '<span class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-100 text-red-700 text-[11px] font-semibold rounded-full"><span class="material-icons text-sm">cancel</span> Rejected</span>';
                }
                showToast('success', json.message || 'Kandidat berhasil di-reject');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast('error', json.message || 'Gagal reject');
                btn.innerHTML = '<span class="material-icons" style="font-size:14px">close</span> Reject';
                btn.disabled = false;
            }
        } catch(e) {
            showToast('error', 'Network error: ' + e.message);
            btn.innerHTML = '<span class="material-icons" style="font-size:14px">close</span> Reject';
            btn.disabled = false;
        }
    };
}
</script>
<style>@keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }</style>
</body>
</html>
