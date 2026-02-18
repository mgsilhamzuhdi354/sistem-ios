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
$newApps = count(array_filter($candidates, fn($c) => in_array($c['status_name'] ?? '', ['Applied', 'New', 'Screening'])));
$interview = count(array_filter($candidates, fn($c) => in_array($c['status_name'] ?? '', ['Interview', 'Technical Test', 'Final Review'])));
$approved = count(array_filter($candidates, fn($c) => in_array($c['status_name'] ?? '', ['Approved', 'Hired', 'Accepted'])));

// Status color mapping
function getStatusBadgeClasses($status) {
    $map = [
        'Applied' => 'bg-blue-100 text-blue-700',
        'New' => 'bg-blue-100 text-blue-700',
        'Screening' => 'bg-indigo-100 text-indigo-700',
        'Document Review' => 'bg-cyan-100 text-cyan-700',
        'Interview' => 'bg-amber-100 text-amber-700',
        'Technical Test' => 'bg-purple-100 text-purple-700',
        'Final Review' => 'bg-orange-100 text-orange-700',
        'Approved' => 'bg-emerald-100 text-emerald-700',
        'Hired' => 'bg-green-100 text-green-700',
        'Accepted' => 'bg-green-100 text-green-700',
        'Rejected' => 'bg-red-100 text-red-700',
        'Withdrawn' => 'bg-slate-100 text-slate-700',
    ];
    return $map[$status] ?? 'bg-slate-100 text-slate-600';
}
?>
<!DOCTYPE html>
<html lang="id">
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
                <h1 class="text-base font-bold text-slate-800 tracking-tight">Recruitment Pipeline</h1>
                <span class="px-2 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-600 rounded-full uppercase tracking-wide">Live</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="<?= BASE_URL ?>recruitment/onboarding"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-all shadow-sm">
                    <span class="material-icons text-sm">person_add</span>
                    Onboarding
                </a>
                <a href="<?= BASE_URL ?>recruitment/approval"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-medium rounded-lg transition-all">
                    <span class="material-icons text-sm">check_circle</span>
                    Approval
                </a>
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
                        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Pipeline Rekrutmen</h2>
                    </div>
                    <p class="text-slate-500 text-sm">Manajemen kandidat dari sistem recruitment secara efisien.</p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Total Candidates -->
                <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm opacity-0 animate-fade-in">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total Kandidat</p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1"><?= $total ?></h3>
                        </div>
                        <div class="p-2 bg-blue-50 rounded-lg">
                            <span class="material-icons text-blue-600">people</span>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center text-xs text-slate-400">
                        <span class="material-icons text-sm mr-0.5">database</span>
                        <span>From recruitment system</span>
                    </div>
                </div>

                <!-- New Applications -->
                <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm opacity-0 animate-fade-in animate-fade-in-d1">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Lamaran Baru</p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1"><?= $newApps ?></h3>
                        </div>
                        <div class="p-2 bg-orange-50 rounded-lg">
                            <span class="material-icons text-orange-500">new_releases</span>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center text-xs text-slate-400">
                        <span>Applied / Screening</span>
                    </div>
                </div>

                <!-- Interview Stage -->
                <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm opacity-0 animate-fade-in animate-fade-in-d2">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Tahap Interview</p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1"><?= $interview ?></h3>
                        </div>
                        <div class="p-2 bg-purple-50 rounded-lg">
                            <span class="material-icons text-purple-600">question_answer</span>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center text-xs text-slate-400">
                        <span>Interview aktif</span>
                    </div>
                </div>

                <!-- Approved -->
                <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm opacity-0 animate-fade-in animate-fade-in-d3">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Disetujui</p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1"><?= $approved ?></h3>
                        </div>
                        <div class="p-2 bg-emerald-50 rounded-lg">
                            <span class="material-icons text-emerald-600">check_circle</span>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center text-xs text-slate-400">
                        <span>Siap onboarding</span>
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
                               placeholder="Cari nama atau posisi...">
                    </div>
                    <div class="flex gap-2">
                        <select x-model="statusFilter" @change="filterCandidates()"
                                class="px-3 py-2 border border-slate-200 rounded-lg text-xs font-medium text-slate-600 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                            <option value="">Semua Status</option>
                            <option value="Applied">Applied</option>
                            <option value="Screening">Screening</option>
                            <option value="Interview">Interview</option>
                            <option value="Technical Test">Technical Test</option>
                            <option value="Final Review">Final Review</option>
                            <option value="Approved">Approved</option>
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
                                        <img src="<?= BASE_URL ?>../recruitment/uploads/avatars/<?= htmlspecialchars($candidate['avatar']) ?>"
                                             alt="Avatar"
                                             class="w-9 h-9 rounded-full object-cover border-2 border-white shadow-sm">
                                    <?php else: ?>
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-xs shadow-sm">
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
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold <?= getStatusBadgeClasses($candidate['status_name'] ?? '') ?>">
                                        <?= htmlspecialchars($candidate['status_name'] ?? 'Unknown') ?>
                                    </span>

                                </div>

                                <!-- Actions -->
                                <div class="col-span-2 text-right">
                                    <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="<?= BASE_URL ?>recruitment/candidate/<?= $candidate['id'] ?>"
                                           class="p-1.5 rounded-md hover:bg-blue-100 text-slate-400 hover:text-blue-600 transition-colors"
                                           title="Lihat Detail">
                                            <span class="material-icons text-lg">visibility</span>
                                        </a>
                                        <?php if (($candidate['status_name'] ?? '') === 'Approved' && !empty($candidate['erp_crew_id'])): ?>
                                            <a href="<?= BASE_URL ?>contracts/create?crew_id=<?= $candidate['erp_crew_id'] ?>"
                                               class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-semibold rounded-md transition-colors shadow-sm"
                                               title="Buat Kontrak">
                                                <span class="material-icons text-sm">description</span>
                                                Kontrak
                                            </a>
                                        <?php elseif (!empty($candidate['sent_to_erp_at']) && empty($candidate['erp_crew_id'])): ?>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-semibold rounded-full">
                                                <span class="material-icons text-[12px]">hourglass_empty</span>
                                                Pending ERP
                                            </span>
                                        <?php endif; ?>
                                    </div>
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
                            <h3 class="text-base font-semibold text-slate-700 mb-1">Tidak ada kandidat ditemukan</h3>
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

                if (matchSearch && matchStatus) {
                    row.style.display = '';
                    count++;
                } else {
                    row.style.display = 'none';
                }
            });

            this.visibleCount = count;
        }
    };
}
</script>
</body>
</html>
