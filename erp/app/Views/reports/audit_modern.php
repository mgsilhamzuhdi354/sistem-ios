<?php
/**
 * Modern Audit Log Report
 */
$currentPage = 'reports';
$actionStyles = [
    'created' => 'bg-emerald-100 text-emerald-700',
    'updated' => 'bg-blue-100 text-blue-700',
    'approved' => 'bg-emerald-100 text-emerald-700',
    'rejected' => 'bg-red-100 text-red-700',
    'terminated' => 'bg-red-100 text-red-700',
    'renewed' => 'bg-amber-100 text-amber-700',
];
?>
<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Audit Log' ?> - IndoOcean ERP</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','sans-serif']}}}}</script>
    <style>
        @keyframes fadeInUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
        .animate-fade-in{animation:fadeInUp .4s ease-out forwards}
        .animate-d1{animation-delay:.05s}
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans">
<div class="flex h-screen overflow-hidden">
    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>
    <main class="flex-1 flex flex-col h-screen overflow-hidden ml-64">
        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-6 flex-shrink-0">
            <div>
                <h1 class="text-base font-bold text-slate-800 tracking-tight"><?= __('reports.contract_log') ?></h1>
                <p class="text-[11px] text-slate-400"><?= __('reports.contract_log_desc') ?></p>
            </div>
            <a href="<?= BASE_URL ?>reports" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-lg transition-colors">
                <span class="material-icons text-sm">arrow_back</span> <?= __('common.back') ?>
            </a>
        </header>
        <div class="flex-1 overflow-y-auto p-6">
            <!-- Filter -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 mb-5 opacity-0 animate-fade-in">
                <form method="GET" action="<?= BASE_URL ?>reports/audit" class="flex items-center gap-3">
                    <span class="text-xs font-semibold text-slate-500">Filter by Contract ID:</span>
                    <input type="number" name="contract_id" value="<?= htmlspecialchars($contractId ?? '') ?>" placeholder="All contracts"
                           class="px-3 py-1.5 w-36 border border-slate-200 rounded-lg text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                    <button type="submit" class="px-3 py-1.5 bg-slate-800 text-white text-xs font-semibold rounded-lg hover:bg-slate-700 transition-colors">Filter</button>
                    <?php if (!empty($contractId)): ?>
                        <a href="<?= BASE_URL ?>reports/audit" class="px-3 py-1.5 bg-red-50 text-red-500 text-xs font-semibold rounded-lg hover:bg-red-100 transition-colors">Clear</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in animate-d1">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead><tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Timestamp</th>
                            <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Contract</th>
                            <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Action</th>
                            <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Field</th>
                            <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Old Value</th>
                            <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">New Value</th>
                            <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">User</th>
                            <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">IP Address</th>
                        </tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($logs)): ?>
                            <tr><td colspan="8" class="py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="bg-slate-100 p-5 rounded-full mb-4"><span class="material-icons text-4xl text-slate-300">history</span></div>
                                    <h3 class="text-base font-semibold text-slate-700"><?= __('reports.no_data') ?></h3>
                                </div>
                            </td></tr>
                            <?php else: ?>
                                <?php foreach ($logs as $log):
                                    $actionColor = 'bg-slate-100 text-slate-600';
                                    foreach ($actionStyles as $key => $style) {
                                        if (stripos($log['action'], $key) !== false) { $actionColor = $style; break; }
                                    }
                                ?>
                                <tr class="hover:bg-blue-50/40 transition-colors">
                                    <td class="px-4 py-2.5 whitespace-nowrap">
                                        <p class="text-xs font-medium text-slate-800"><?= date('d M Y', strtotime($log['created_at'])) ?></p>
                                        <p class="text-[11px] text-slate-400"><?= date('H:i:s', strtotime($log['created_at'])) ?></p>
                                    </td>
                                    <td class="px-4 py-2.5"><a href="<?= BASE_URL ?>contracts/<?= $log['contract_id'] ?>" class="text-xs font-semibold text-blue-600 hover:underline"><?= htmlspecialchars($log['contract_no'] ?? '#'.$log['contract_id']) ?></a></td>
                                    <td class="px-4 py-2.5"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold <?= $actionColor ?>"><?= ucfirst(str_replace('_', ' ', $log['action'])) ?></span></td>
                                    <td class="px-4 py-2.5 text-xs text-slate-600"><?= htmlspecialchars($log['field_changed'] ?? '-') ?></td>
                                    <td class="px-4 py-2.5 text-xs text-slate-400 max-w-[120px] truncate"><?= htmlspecialchars($log['old_value'] ?? '-') ?></td>
                                    <td class="px-4 py-2.5 text-xs text-slate-700 font-medium max-w-[120px] truncate"><?= htmlspecialchars($log['new_value'] ?? '-') ?></td>
                                    <td class="px-4 py-2.5 text-xs text-slate-600"><?= htmlspecialchars($log['user_name'] ?? 'System') ?></td>
                                    <td class="px-4 py-2.5 text-[11px] font-mono text-slate-400"><?= htmlspecialchars($log['ip_address'] ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50"><p class="text-xs text-slate-400">Showing <?= count($logs ?? []) ?> audit entries</p></div>
            </div>
            <div class="mt-6 text-center"><p class="text-xs text-slate-400">© <?= date('Y') ?> IndoOcean ERP System</p></div>
        </div>
    </main>
</div>
</body>
</html>
