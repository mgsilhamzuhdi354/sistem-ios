<?php
/**
 * Modern Contracts by Client Report
 */
$currentPage = 'reports';
?>
<!DOCTYPE html>
<html lang="<?= session()->get('lang') ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Contracts by Client' ?> - IndoOcean ERP</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','sans-serif']}}}}</script>
    <style>
        @keyframes fadeInUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
        .animate-fade-in{animation:fadeInUp .4s ease-out forwards}
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans">
<div class="flex h-screen overflow-hidden">
    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>
    <main class="flex-1 flex flex-col h-screen overflow-hidden ml-64">
        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-6 flex-shrink-0">
            <div>
                <h1 class="text-base font-bold text-slate-800 tracking-tight"><?= __('reports.by_client') ?></h1>
                <p class="text-[11px] text-slate-400"><?= __('reports.by_client_desc') ?></p>
            </div>
            <a href="<?= BASE_URL ?>reports" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-lg transition-colors">
                <span class="material-icons text-sm">arrow_back</span> <?= __('common.back') ?>
            </a>
        </header>
        <div class="flex-1 overflow-y-auto p-6">
            <?php if (empty($clients)): ?>
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-10 text-center">
                    <div class="bg-slate-100 p-5 rounded-full inline-block mb-4"><span class="material-icons text-4xl text-slate-300">business</span></div>
                    <h3 class="text-base font-semibold text-slate-700 mb-1"><?= __('common.no_data') ?></h3>
                </div>
            <?php else: ?>
                <?php foreach ($clients as $i => $client): ?>
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-5 opacity-0 animate-fade-in" style="animation-delay:<?= $i * 0.05 ?>s">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2.5">
                                <div class="p-2 bg-indigo-50 rounded-lg"><span class="material-icons text-indigo-600">business</span></div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-800"><?= htmlspecialchars($client['name']) ?></h3>
                                    <p class="text-[11px] text-slate-400"><?= htmlspecialchars($client['country'] ?? '') ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-700"><?= $client['active_contracts'] ?? 0 ?> Active</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600"><?= $client['vessel_count'] ?? 0 ?> Vessels</span>
                        </div>
                    </div>
                    <?php if (empty($client['contracts'])): ?>
                        <div class="px-5 py-6 text-center text-sm text-slate-400">No active contracts</div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead><tr class="bg-slate-50 border-b border-slate-200">
                                    <th class="px-5 py-2 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Vessel</th>
                                    <th class="px-5 py-2 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Crew Name</th>
                                    <th class="px-5 py-2 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Rank</th>
                                    <th class="px-5 py-2 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Contract No</th>
                                    <th class="px-5 py-2 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Sign Off</th>
                                </tr></thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php foreach ($client['contracts'] as $c): ?>
                                    <tr class="hover:bg-blue-50/40 transition-colors">
                                        <td class="px-5 py-2.5 text-sm text-slate-600"><?= htmlspecialchars($c['vessel_name'] ?? '-') ?></td>
                                        <td class="px-5 py-2.5 text-sm font-semibold text-slate-800"><?= htmlspecialchars($c['crew_name']) ?></td>
                                        <td class="px-5 py-2.5 text-sm text-slate-600"><?= htmlspecialchars($c['rank_name'] ?? '-') ?></td>
                                        <td class="px-5 py-2.5"><a href="<?= BASE_URL ?>contracts/<?= $c['id'] ?>" class="text-sm font-semibold text-blue-600 hover:underline"><?= htmlspecialchars($c['contract_no']) ?></a></td>
                                        <td class="px-5 py-2.5 text-sm text-slate-500"><?= $c['sign_off_date'] ? date('d M Y', strtotime($c['sign_off_date'])) : '-' ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <div class="mt-6 text-center"><p class="text-xs text-slate-400">© <?= date('Y') ?> IndoOcean ERP System</p></div>
        </div>
    </main>
</div>
</body>
</html>
