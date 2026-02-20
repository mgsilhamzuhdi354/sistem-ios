<?php
/**
 * Modern Expiring Contracts Report
 */
$currentPage = 'reports';
?>
<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Expiring Contracts' ?> - IndoOcean ERP</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','sans-serif']}}}}</script>
    <style>
        @keyframes fadeInUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
        .animate-fade-in{animation:fadeInUp .4s ease-out forwards}
        .animate-d1{animation-delay:.05s}.animate-d2{animation-delay:.1s}
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans">
<div class="flex h-screen overflow-hidden">
    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>
    <main class="flex-1 flex flex-col h-screen overflow-hidden ml-64">
        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-6 flex-shrink-0">
            <div>
                <h1 class="text-base font-bold text-slate-800 tracking-tight"><?= __('reports.expiring_contracts') ?></h1>
                <p class="text-[11px] text-slate-400">Contracts expiring within <?= $days ?? 60 ?> days</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?= BASE_URL ?>reports/export/expiring" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-600 text-xs font-semibold rounded-lg transition-colors">
                    <span class="material-icons text-sm">download</span> Export CSV
                </a>
                <a href="<?= BASE_URL ?>reports" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-lg transition-colors">
                    <span class="material-icons text-sm">arrow_back</span> <?= __('common.back') ?>
                </a>
            </div>
        </header>
        <div class="flex-1 overflow-y-auto p-6">
            <!-- Filter -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 mb-5 inline-flex items-center gap-3 opacity-0 animate-fade-in">
                <span class="text-xs font-semibold text-slate-500">Show expiring within:</span>
                <form method="GET" action="<?= BASE_URL ?>reports/expiring">
                    <select name="days" onchange="this.form.submit()" class="px-3 py-1.5 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                        <?php foreach ([7,30,60,90] as $d): ?>
                            <option value="<?= $d ?>" <?= ($days ?? 60) == $d ? 'selected' : '' ?>><?= $d ?> days</option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <!-- Stat -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 opacity-0 animate-fade-in animate-d1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider"><?= __('reports.expiring_contracts') ?></p>
                            <p class="text-3xl font-extrabold text-amber-500 mt-1"><?= count($contracts ?? []) ?></p>
                        </div>
                        <div class="p-3 bg-amber-50 rounded-xl"><span class="material-icons text-amber-500 text-2xl">schedule</span></div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in animate-d2">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead><tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-5 py-2.5 text-center text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Days Left</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Contract No</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Crew Name</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Rank</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Vessel</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Sign Off</th>
                        </tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($contracts)): ?>
                            <tr><td colspan="6" class="py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="bg-slate-100 p-5 rounded-full mb-4"><span class="material-icons text-4xl text-slate-300">event_available</span></div>
                                    <h3 class="text-base font-semibold text-slate-700"><?= __('reports.no_expiring_contracts') ?></h3>
                                </div>
                            </td></tr>
                            <?php else: ?>
                                <?php foreach ($contracts as $c):
                                    $d = $c['days_remaining'] ?? 0;
                                    $uc = $d <= 7 ? 'bg-red-100 text-red-700' : ($d <= 30 ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700');
                                ?>
                                <tr class="hover:bg-blue-50/40 transition-colors">
                                    <td class="px-5 py-3 text-center"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold <?= $uc ?>"><?= $d ?> days</span></td>
                                    <td class="px-5 py-3"><a href="<?= BASE_URL ?>contracts/<?= $c['id'] ?>" class="text-sm font-semibold text-blue-600 hover:underline"><?= htmlspecialchars($c['contract_no']) ?></a></td>
                                    <td class="px-5 py-3 text-sm font-semibold text-slate-800"><?= htmlspecialchars($c['crew_name']) ?></td>
                                    <td class="px-5 py-3 text-sm text-slate-600"><?= htmlspecialchars($c['rank_name'] ?? '-') ?></td>
                                    <td class="px-5 py-3 text-sm text-slate-600"><?= htmlspecialchars($c['vessel_name'] ?? '-') ?></td>
                                    <td class="px-5 py-3 text-sm text-slate-500"><?= $c['sign_off_date'] ? date('d M Y', strtotime($c['sign_off_date'])) : '-' ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50"><p class="text-xs text-slate-400">Showing <?= count($contracts ?? []) ?> expiring contracts</p></div>
            </div>
            <div class="mt-6 text-center"><p class="text-xs text-slate-400">© <?= date('Y') ?> IndoOcean ERP System</p></div>
        </div>
    </main>
</div>
</body>
</html>
