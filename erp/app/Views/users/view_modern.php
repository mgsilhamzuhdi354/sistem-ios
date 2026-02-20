<?php
/**
 * Modern User Detail View
 */
$currentPage = 'users';
$roleColors = [
    'super_admin' => 'bg-red-100 text-red-700',
    'admin' => 'bg-blue-100 text-blue-700',
    'hr' => 'bg-emerald-100 text-emerald-700',
    'finance' => 'bg-amber-100 text-amber-700',
    'manager' => 'bg-indigo-100 text-indigo-700',
    'viewer' => 'bg-slate-100 text-slate-600',
];
$rc = $roleColors[$user['role']] ?? 'bg-slate-100 text-slate-600';
?>
<!DOCTYPE html>
<html lang="<?= session()->get('lang') ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($user['full_name']) ?> - IndoOcean ERP</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','sans-serif']}}}}</script>
    <style>
        @keyframes fadeInUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
        .animate-fade-in{animation:fadeInUp .4s ease-out forwards}
        .animate-d1{animation-delay:.05s}.animate-d2{animation-delay:.1s}.animate-d3{animation-delay:.15s}
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans">
<div class="flex h-screen overflow-hidden">
    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>
    <main class="flex-1 flex flex-col h-screen overflow-hidden ml-64">
        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-6 flex-shrink-0">
            <div class="flex items-center text-sm text-slate-500">
                <a href="<?= BASE_URL ?>users" class="hover:text-blue-600 transition-colors"><?= __('users.title') ?></a>
                <span class="material-icons-round text-sm mx-2">chevron_right</span>
                <span class="font-medium text-slate-800"><?= htmlspecialchars($user['full_name']) ?></span>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?= BASE_URL ?>users/edit/<?= $user['id'] ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors">
                    <span class="material-icons text-sm">edit</span> <?= __('common.edit') ?>
                </a>
                <a href="<?= BASE_URL ?>users" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-lg transition-colors">
                    <span class="material-icons text-sm">arrow_back</span> <?= __('common.back') ?>
                </a>
            </div>
        </header>
        <div class="flex-1 overflow-y-auto p-6">
            <!-- User Profile Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-5 opacity-0 animate-fade-in">
                <div class="flex items-start gap-5">
                    <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-2xl font-bold text-blue-600 flex-shrink-0">
                        <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-bold text-slate-800"><?= htmlspecialchars($user['full_name']) ?></h2>
                        <p class="text-sm text-slate-500 mt-0.5">@<?= htmlspecialchars($user['username']) ?></p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold <?= $rc ?>"><?= ucfirst(str_replace('_', ' ', $user['role'])) ?></span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold <?= $user['is_active'] ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' ?>"><?= $user['is_active'] ? __('common.active') : __('common.inactive') ?></span>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-5 pt-5 border-t border-slate-100">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Email</p>
                        <p class="text-sm text-slate-700 mt-0.5"><?= htmlspecialchars($user['email']) ?></p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Phone</p>
                        <p class="text-sm text-slate-700 mt-0.5"><?= htmlspecialchars($user['phone'] ?? '-') ?></p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Created</p>
                        <p class="text-sm text-slate-700 mt-0.5"><?= isset($user['created_at']) ? date('d M Y', strtotime($user['created_at'])) : '-' ?></p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Last Login</p>
                        <p class="text-sm text-slate-700 mt-0.5"><?= isset($user['last_login']) ? date('d M Y H:i', strtotime($user['last_login'])) : 'Never' ?></p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <!-- Login History -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in animate-d1">
                    <div class="px-5 py-3 border-b border-slate-100 flex items-center gap-2">
                        <span class="material-icons text-slate-400 text-lg">history</span>
                        <h3 class="text-sm font-bold text-slate-800"><?= __('users.login_history') ?></h3>
                    </div>
                    <div class="overflow-x-auto max-h-80 overflow-y-auto">
                        <table class="w-full">
                            <thead class="sticky top-0"><tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-4 py-2 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Time</th>
                                <th class="px-4 py-2 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-2 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">IP</th>
                            </tr></thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php if (empty($loginHistory)): ?>
                                <tr><td colspan="3" class="px-4 py-6 text-center text-sm text-slate-400"><?= __('users.no_login_history') ?></td></tr>
                                <?php else: ?>
                                    <?php foreach ($loginHistory as $lh): ?>
                                    <tr class="hover:bg-blue-50/40">
                                        <td class="px-4 py-2 text-xs text-slate-600 whitespace-nowrap"><?= date('d M Y H:i', strtotime($lh['login_at'])) ?></td>
                                        <td class="px-4 py-2"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold <?= $lh['status'] === 'success' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' ?>"><?= ucfirst($lh['status']) ?></span></td>
                                        <td class="px-4 py-2 text-xs text-slate-400 font-mono"><?= htmlspecialchars($lh['ip_address'] ?? '-') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Activity Log -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in animate-d2">
                    <div class="px-5 py-3 border-b border-slate-100 flex items-center gap-2">
                        <span class="material-icons text-slate-400 text-lg">timeline</span>
                        <h3 class="text-sm font-bold text-slate-800"><?= __('monitoring.activity_log') ?></h3>
                    </div>
                    <div class="overflow-x-auto max-h-80 overflow-y-auto">
                        <table class="w-full">
                            <thead class="sticky top-0"><tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-4 py-2 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Time</th>
                                <th class="px-4 py-2 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Action</th>
                                <th class="px-4 py-2 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Details</th>
                            </tr></thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php if (empty($activities)): ?>
                                <tr><td colspan="3" class="px-4 py-6 text-center text-sm text-slate-400"><?= __('users.no_activities') ?></td></tr>
                                <?php else: ?>
                                    <?php foreach ($activities as $act): ?>
                                    <tr class="hover:bg-blue-50/40">
                                        <td class="px-4 py-2 text-xs text-slate-600 whitespace-nowrap"><?= date('d M Y H:i', strtotime($act['created_at'])) ?></td>
                                        <td class="px-4 py-2"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-100 text-blue-700"><?= ucfirst($act['action']) ?></span></td>
                                        <td class="px-4 py-2 text-xs text-slate-600 max-w-xs truncate"><?= htmlspecialchars($act['description'] ?? '-') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-6 text-center"><p class="text-xs text-slate-400">© <?= date('Y') ?> IndoOcean ERP System</p></div>
        </div>
    </main>
</div>
</body>
</html>
