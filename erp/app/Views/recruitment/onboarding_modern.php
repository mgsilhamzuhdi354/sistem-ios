<?php
/**
 * Modern Auto-Onboarding View
 * Using existing modern_sidebar.php partial
 */
$currentPage = 'recruitment-onboarding';
$approvedCrew = $approvedCrew ?? [];

// Calculate stats
$totalApproved = count($approvedCrew);
$readyToImport = count(array_filter($approvedCrew, fn($c) => empty($c['is_synced'])));
$totalProcessed = $totalApproved - $readyToImport;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Auto-Onboarding' ?> - IndoOcean ERP</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#D4A017",
                        secondary: "#0F172A",
                        "background-light": "#F8FAFC",
                        "surface-light": "#FFFFFF",
                        "border-light": "#E2E8F0",
                    },
                    fontFamily: { display: ['Inter', 'sans-serif'] },
                    boxShadow: {
                        'soft': '0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02)',
                        'card': '0 10px 15px -3px rgba(0, 0, 0, 0.03), 0 4px 6px -2px rgba(0, 0, 0, 0.01)',
                    }
                }
            }
        };
    </script>
    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out forwards; }
        .animate-fade-in-d1 { animation-delay: 0.05s; }
        .animate-fade-in-d2 { animation-delay: 0.1s; }
        .animate-fade-in-d3 { animation-delay: 0.15s; }
    </style>
</head>
<body class="bg-background-light text-slate-800 h-screen overflow-hidden flex font-display">

<!-- Sidebar -->
<?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

<!-- Main Content -->
<main class="flex-1 flex flex-col h-full overflow-hidden bg-background-light ml-64">
    <!-- Header -->
    <header class="h-16 bg-surface-light border-b border-border-light flex items-center justify-between px-8 flex-shrink-0 z-10 shadow-sm">
        <h1 class="text-lg font-semibold text-secondary">Auto-Onboarding</h1>
        <div class="flex items-center gap-4">
            <button onclick="window.location.reload()"
                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                <span class="material-icons text-sm">refresh</span>
                Refresh
            </button>
        </div>
    </header>

    <!-- Flash Messages -->
    <?php if (!empty($flash)): ?>
        <div class="px-8 pt-4">
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

    <!-- Content Area -->
    <div class="flex-1 overflow-y-auto p-8">
        <div class="max-w-7xl mx-auto space-y-8">

            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="p-2 bg-emerald-100 text-emerald-600 rounded-lg">
                            <span class="material-icons text-2xl">person_add_alt</span>
                        </span>
                        <h2 class="text-2xl font-bold text-secondary tracking-tight">Onboarding Kandidat</h2>
                    </div>
                    <p class="text-slate-500 text-sm pl-14">
                        Kandidat yang disetujui siap untuk import ke ERP. Kelola proses rekrutmen Anda di sini.
                    </p>
                </div>
                <div>
                    <a href="<?= BASE_URL ?>recruitment/pipeline"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-border-light rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                        <span class="material-icons text-base">arrow_back</span>
                        Kembali ke Pipeline
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Approved -->
                <div class="bg-surface-light p-6 rounded-xl shadow-soft border border-border-light flex flex-col justify-between h-32 relative overflow-hidden group opacity-0 animate-fade-in">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-icons text-6xl text-emerald-500">check_circle</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">Kandidat Disetujui</p>
                        <h3 class="text-3xl font-bold text-secondary"><?= $totalApproved ?></h3>
                    </div>
                    <div class="flex items-center gap-1 text-xs text-emerald-600">
                        <span class="material-icons text-sm">trending_flat</span>
                        <span>Status: Approved</span>
                    </div>
                </div>

                <!-- Ready to Import -->
                <div class="bg-surface-light p-6 rounded-xl shadow-soft border border-border-light flex flex-col justify-between h-32 relative overflow-hidden group opacity-0 animate-fade-in animate-fade-in-d1">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-icons text-6xl text-blue-500">cloud_download</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">Siap Import</p>
                        <h3 class="text-3xl font-bold text-secondary"><?= $readyToImport ?></h3>
                    </div>
                    <div class="flex items-center gap-1 text-xs text-blue-600">
                        <span class="material-icons text-sm">schedule</span>
                        <span>Menunggu proses</span>
                    </div>
                </div>

                <!-- Total Processed -->
                <div class="bg-surface-light p-6 rounded-xl shadow-soft border border-border-light flex flex-col justify-between h-32 relative overflow-hidden group opacity-0 animate-fade-in animate-fade-in-d2">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-icons text-6xl text-primary">analytics</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">Total Diproses</p>
                        <h3 class="text-3xl font-bold text-secondary"><?= $totalProcessed ?></h3>
                    </div>
                    <div class="flex items-center gap-1 text-xs text-slate-500">
                        <span class="material-icons text-sm">update</span>
                        <span>Updated hari ini</span>
                    </div>
                </div>
            </div>

            <!-- Candidates Table Card -->
            <div class="bg-surface-light rounded-xl shadow-card border border-border-light overflow-hidden flex flex-col min-h-[500px] opacity-0 animate-fade-in animate-fade-in-d3" x-data="onboardingTable()">
                <!-- Table Header -->
                <div class="p-6 border-b border-border-light flex flex-col sm:flex-row justify-between items-center gap-4">
                    <h3 class="font-semibold text-lg text-secondary flex items-center gap-2">
                        <span class="material-icons text-emerald-500">task_alt</span>
                        Kandidat Siap Import
                    </h3>
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <div class="relative w-full sm:w-64">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="material-icons text-slate-400 text-lg">search</span>
                            </span>
                            <input type="text" x-model="search" @input="filterCandidates()"
                                   class="block w-full pl-10 pr-3 py-2 border border-border-light rounded-lg leading-5 bg-slate-50 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary text-sm transition"
                                   placeholder="Cari kandidat..."/>
                        </div>
                        <button @click="bulkImport()" :disabled="selectedCount === 0"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-emerald-500 hover:bg-emerald-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                            <span class="material-icons text-lg mr-2">upload_file</span>
                            <span x-text="selectedCount > 0 ? 'Import ' + selectedCount : 'Bulk Import'"></span>
                        </button>
                    </div>
                </div>

                <?php if (!empty($approvedCrew)): ?>
                <!-- Table -->
                <div class="overflow-x-auto">
                    <!-- Table Header -->
                    <div class="bg-slate-50 border-b border-border-light px-6 py-3 grid grid-cols-12 gap-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <div class="col-span-1 flex items-center justify-center">
                            <input type="checkbox" @change="toggleAll()" x-model="selectAll"
                                   class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4 bg-white"/>
                        </div>
                        <div class="col-span-3">Candidate</div>
                        <div class="col-span-2">Posisi</div>
                        <div class="col-span-2">Kontak</div>
                        <div class="col-span-2">Dokumen</div>
                        <div class="col-span-2 text-right">Aksi</div>
                    </div>

                    <!-- Table Body -->
                    <div class="divide-y divide-border-light">
                        <?php foreach ($approvedCrew as $candidate): ?>
                        <div class="grid grid-cols-12 gap-4 px-6 py-4 hover:bg-slate-50 transition-colors candidate-row group"
                             data-name="<?= strtolower(htmlspecialchars($candidate['applicant_name'] ?? '')) ?>"
                             data-id="<?= $candidate['application_id'] ?? 0 ?>">
                            <!-- Checkbox -->
                            <div class="col-span-1 flex items-center justify-center">
                                <input type="checkbox" class="candidate-checkbox rounded border-gray-300 text-primary focus:ring-primary h-4 w-4"
                                       value="<?= $candidate['application_id'] ?? 0 ?>"/>
                            </div>

                            <!-- Candidate Info -->
                            <div class="col-span-3 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-yellow-600 flex items-center justify-center text-white font-bold text-sm shadow-sm flex-shrink-0">
                                    <?= strtoupper(substr($candidate['applicant_name'] ?? 'N', 0, 1)) ?>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 truncate"><?= htmlspecialchars($candidate['applicant_name'] ?? '') ?></p>
                                    <p class="text-xs text-slate-400 truncate">Applied: <?= !empty($candidate['applied_date']) ? date('d M Y', strtotime($candidate['applied_date'])) : '-' ?></p>
                                </div>
                            </div>

                            <!-- Position -->
                            <div class="col-span-2 flex items-center">
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold bg-primary/10 text-yellow-700">
                                    <?= htmlspecialchars($candidate['position_applied'] ?? '-') ?>
                                </span>
                            </div>

                            <!-- Contact -->
                            <div class="col-span-2 flex flex-col justify-center text-xs text-slate-600">
                                <div class="flex items-center gap-1 truncate">
                                    <span class="material-icons text-sm text-slate-400">email</span>
                                    <span class="truncate"><?= htmlspecialchars($candidate['email'] ?? '-') ?></span>
                                </div>
                                <div class="flex items-center gap-1 truncate">
                                    <span class="material-icons text-sm text-slate-400">phone</span>
                                    <span class="truncate"><?= htmlspecialchars($candidate['phone'] ?? '-') ?></span>
                                </div>
                            </div>

                            <!-- Documents -->
                            <div class="col-span-2 flex items-center">
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-600">
                                    <span class="material-icons text-sm mr-1">description</span>
                                    N/A
                                </span>
                            </div>

                            <!-- Actions -->
                            <div class="col-span-2 flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="<?= BASE_URL ?>recruitment/processOnboard/<?= $candidate['application_id'] ?? 0 ?>"
                                   onclick="return confirm('Import kandidat ini ke ERP?')"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm">
                                    <span class="material-icons text-sm">cloud_download</span>
                                    Import
                                </a>
                            </div>
                            <span class="col-span-2 flex items-center justify-end text-xs text-slate-400 group-hover:hidden">
                                Hover for actions
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php else: ?>
                <!-- Empty State -->
                <div class="flex-1 flex flex-col items-center justify-center p-12 text-center">
                    <div class="relative mb-6">
                        <div class="absolute inset-0 bg-primary opacity-10 blur-2xl rounded-full transform scale-150"></div>
                        <div class="relative bg-slate-50 p-6 rounded-full inline-block">
                            <span class="material-icons text-slate-300" style="font-size: 96px;">person</span>
                            <div class="absolute bottom-2 right-2 bg-white rounded-full p-1 shadow-md border border-slate-100">
                                <span class="material-icons text-emerald-500 text-3xl">check_circle</span>
                            </div>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-secondary mb-2">Tidak ada kandidat yang disetujui</h3>
                    <p class="text-slate-500 max-w-sm mb-8">
                        Kandidat akan muncul di sini setelah mereka disetujui di sistem recruitment dan siap untuk proses onboarding.
                    </p>
                    <a href="<?= BASE_URL ?>recruitment/pipeline"
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-primary hover:bg-yellow-600 transition-all transform hover:scale-105">
                        <span class="material-icons mr-2">visibility</span>
                        Lihat Semua Kandidat
                    </a>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
    
    <!-- Footer -->
    <div class="px-8 py-4 text-center border-t border-border-light bg-surface-light">
        <p class="text-xs text-slate-400">© <?= date('Y') ?> IndoOcean ERP System. All rights reserved.</p>
    </div>
</main>

<script>
function onboardingTable() {
    return {
        search: '',
        selectAll: false,
        selectedCount: 0,

        toggleAll() {
            const checkboxes = document.querySelectorAll('.candidate-checkbox');
            checkboxes.forEach(cb => cb.checked = this.selectAll);
            this.updateSelectedCount();
        },

        updateSelectedCount() {
            this.selectedCount = document.querySelectorAll('.candidate-checkbox:checked').length;
        },

        filterCandidates() {
            const rows = document.querySelectorAll('.candidate-row');
            const searchLower = this.search.toLowerCase();
            rows.forEach(row => {
                const name = row.dataset.name || '';
                const match = !searchLower || name.includes(searchLower);
                row.style.display = match ? '' : 'none';
            });
        },

        bulkImport() {
            const selected = Array.from(document.querySelectorAll('.candidate-checkbox:checked')).map(cb => cb.value);
            if (selected.length === 0) {
                alert('Pilih minimal satu kandidat');
                return;
            }
            if (confirm(`Import ${selected.length} kandidat ke ERP?`)) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= BASE_URL ?>recruitment/bulkOnboard';
                selected.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'candidate_ids[]';
                    input.value = id;
                    form.appendChild(input);
                });
                document.body.appendChild(form);
                form.submit();
            }
        },

        init() {
            // Listen to checkbox changes
            document.querySelectorAll('.candidate-checkbox').forEach(cb => {
                cb.addEventListener('change', () => this.updateSelectedCount());
            });
        }
    };
}
</script>
</body>
</html>
