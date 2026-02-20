<?php
/**
 * Modern Employee Report View
 * Clean white design with modern sidebar
 */
$currentPage = 'reports';
?>
<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Employee Report' ?> - IndoOcean ERP</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','sans-serif']}}}}</script>
    <style>
        @keyframes fadeInUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
        .animate-fade-in{animation:fadeInUp .4s ease-out forwards}
        .animate-d1{animation-delay:.05s}.animate-d2{animation-delay:.1s}.animate-d3{animation-delay:.15s}
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans" x-data="{ search: '' }">
<div class="flex h-screen overflow-hidden">
    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

    <main class="flex-1 flex flex-col h-screen overflow-hidden ml-64">
        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-6 flex-shrink-0">
            <div>
                <h1 class="text-base font-bold text-slate-800 tracking-tight"><?= __('reports.employee_reports') ?></h1>
                <p class="text-[11px] text-slate-400"><?= __('reports.employee_data_desc') ?></p>
            </div>
            <a href="<?= BASE_URL ?>reports"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-lg transition-colors">
                <span class="material-icons text-sm">arrow_back</span> <?= __('common.back') ?>
            </a>
        </header>

        <div class="flex-1 overflow-y-auto p-6">
            <?php if (!empty($error)): ?>
                <!-- Error State -->
                <div class="bg-white rounded-xl border border-amber-200 shadow-sm p-10 text-center">
                    <div class="bg-amber-50 p-5 rounded-full inline-block mb-4">
                        <span class="material-icons text-4xl text-amber-500">warning</span>
                    </div>
                    <h3 class="text-base font-semibold text-slate-800 mb-2">Failed to load employee data from HRIS</h3>
                    <p class="text-sm text-red-500"><?= htmlspecialchars($error) ?></p>
                </div>
            <?php elseif (empty($employees)): ?>
                <!-- Empty State -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-10 text-center">
                    <div class="bg-slate-100 p-5 rounded-full inline-block mb-4">
                        <span class="material-icons text-4xl text-slate-300">people</span>
                    </div>
                    <h3 class="text-base font-semibold text-slate-700 mb-1">No employees found</h3>
                    <p class="text-sm text-slate-400">No employee data available from HRIS</p>
                </div>
            <?php else: ?>
                <!-- Stat Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 opacity-0 animate-fade-in">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Employees</p>
                                <p class="text-3xl font-extrabold text-blue-600 mt-1"><?= $totalEmployees ?></p>
                            </div>
                            <div class="p-3 bg-blue-50 rounded-xl"><span class="material-icons text-blue-600 text-2xl">people</span></div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 opacity-0 animate-fade-in animate-d1">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Departments</p>
                                <p class="text-3xl font-extrabold text-indigo-600 mt-1"><?= count($byDepartment) ?></p>
                            </div>
                            <div class="p-3 bg-indigo-50 rounded-xl"><span class="material-icons text-indigo-600 text-2xl">business</span></div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 opacity-0 animate-fade-in animate-d2">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Status</p>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 mt-2">
                                    <span class="material-icons text-sm mr-1">check_circle</span> Active
                                </span>
                            </div>
                            <div class="p-3 bg-emerald-50 rounded-xl"><span class="material-icons text-emerald-600 text-2xl">verified</span></div>
                        </div>
                    </div>
                </div>

                <!-- Department Breakdown -->
                <?php if (!empty($byDepartment)): ?>
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-6 opacity-0 animate-fade-in animate-d2">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                        <div class="p-2 bg-indigo-50 rounded-lg"><span class="material-icons text-indigo-600">pie_chart</span></div>
                        <h3 class="text-sm font-bold text-slate-800"><?= __('reports.employee_data') ?></h3>
                    </div>
                    <div class="p-5 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                        <?php foreach ($byDepartment as $dept => $count): ?>
                        <div class="bg-slate-50 rounded-lg p-3 flex items-center justify-between border border-slate-100 hover:border-blue-200 hover:bg-blue-50/30 transition-colors">
                            <span class="text-sm font-medium text-slate-700"><?= htmlspecialchars($dept) ?></span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-700"><?= $count ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Search -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 mb-4">
                    <div class="relative max-w-sm">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="material-icons text-slate-400 text-lg">search</span>
                        </span>
                        <input type="text" x-model="search" placeholder="Search employees..."
                               class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                    </div>
                </div>

                <!-- Employee List -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in animate-d3">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200">
                                    <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider w-12">No</th>
                                    <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Name</th>
                                    <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                                    <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Department</th>
                                    <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Position</th>
                                    <th class="px-5 py-2.5 text-center text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php $no = 1; foreach ($employees as $emp): ?>
                                <tr class="hover:bg-blue-50/40 transition-colors"
                                    x-show="search === '' || 
                                        '<?= strtolower(addslashes($emp['name'] ?? '')) ?>'.includes(search.toLowerCase()) ||
                                        '<?= strtolower(addslashes($emp['department'] ?? '')) ?>'.includes(search.toLowerCase()) ||
                                        '<?= strtolower(addslashes($emp['position'] ?? '')) ?>'.includes(search.toLowerCase())">
                                    <td class="px-5 py-3 text-xs text-slate-400 font-medium"><?= $no++ ?></td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                                <?= strtoupper(substr($emp['name'] ?? 'U', 0, 1)) ?>
                                            </div>
                                            <span class="text-sm font-semibold text-slate-800"><?= htmlspecialchars($emp['name'] ?? '-') ?></span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 text-sm text-slate-500"><?= htmlspecialchars($emp['email'] ?? '-') ?></td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700"><?= htmlspecialchars($emp['department'] ?? '-') ?></span>
                                    </td>
                                    <td class="px-5 py-3 text-sm text-slate-600"><?= htmlspecialchars($emp['position'] ?? '-') ?></td>
                                    <td class="px-5 py-3 text-center">
                                        <?php 
                                        $status = $emp['status'] ?? 'unknown';
                                        $sBadge = $status === 'aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500';
                                        ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold <?= $sBadge ?>"><?= ucfirst($status) ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="px-5 py-3 border-t border-slate-100 bg-slate-50">
                        <p class="text-xs text-slate-400">Showing <?= $totalEmployees ?> employees from HRIS</p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mt-6 text-center"><p class="text-xs text-slate-400">© <?= date('Y') ?> IndoOcean ERP System</p></div>
        </div>
    </main>
</div>
</body>
</html>
