<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $title ?> - PT Indo Ocean
    </title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            blue: "#1e40af",
                            gold: "#fbbf24",
                            dark: "#0f172a",
                        },
                        surface: {
                            DEFAULT: "#ffffff",
                            subtle: "#f8fafc",
                        }
                    },
                    fontFamily: {
                        sans: ["Inter", "sans-serif"],
                    },
                    boxShadow: {
                        'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
                        'card': '0 0 0 1px rgba(0,0,0,0.03), 0 2px 8px rgba(0,0,0,0.04)',
                    }
                },
            },
        };
    </script>
    <style type="text/tailwindcss">
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-surface-subtle text-slate-600 antialiased selection:bg-brand-blue selection:text-white">

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php $currentPage = 'contracts';
        include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="ml-64 flex-1 flex flex-col min-w-0 overflow-hidden bg-surface-subtle">
            <!-- Header -->
            <header
                class="h-20 flex items-center justify-between px-8 bg-white/80 backdrop-blur-xl border-b border-slate-200/60 z-10 sticky top-0">
                <div class="flex flex-col">
                    <h2 class="text-xl font-bold text-slate-800"><?= __('contracts.title') ?></h2>
                    <p class="text-xs text-slate-500"><?= __('contracts.subtitle') ?></p>
                </div>

                <div class="flex items-center gap-5">
                    <!-- Mode Toggle -->
                    <div class="flex items-center bg-slate-100 rounded-lg p-1">
                        <a href="<?= BASE_URL ?>contracts/toggleMode?mode=classic"
                            class="px-3 py-1.5 text-xs font-bold text-slate-600 hover:bg-white rounded-md transition-all flex items-center gap-1">
                            <span class="material-icons-round text-sm">view_compact</span> <?= __('common.classic') ?>
                        </a>
                        <span
                            class="px-3 py-1.5 text-xs font-bold text-brand-blue bg-white rounded-md shadow-sm flex items-center gap-1">
                            <span class="material-icons-round text-sm">auto_awesome</span> <?= __('common.modern') ?>
                        </span>
                    </div>
                    <div class="h-6 w-px bg-slate-200"></div>
                    <button
                        class="relative p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full transition-all">
                        <span class="material-icons-round">notifications</span>
                        <?php if (!empty($flash['success']) || !empty($flash['error'])): ?>
                            <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
                        <?php endif; ?>
                    </button>
                    <a href="<?= BASE_URL ?>contracts/create"
                        class="bg-brand-gold hover:bg-yellow-400 text-slate-900 pl-4 pr-5 py-2.5 rounded-lg font-bold text-sm flex items-center gap-2 transition-all active:scale-95 shadow-lg shadow-yellow-500/20">
                        <span class="material-icons-round text-lg">add_circle</span>
                        <?= __('dashboard.new_contract') ?>
                    </a>
                </div>
            </header>

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto custom-scrollbar p-8 space-y-6">
                <!-- Filters Card -->
                <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-soft space-y-4">
                    <form method="GET" action="<?= BASE_URL ?>contracts" class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <div class="md:col-span-5">
                            <label
                                class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 block"><?= __('common.search') ?></label>
                            <div class="relative group">
                                <span
                                    class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-brand-blue transition-colors text-xl">search</span>
                                <input
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700 placeholder-slate-400 focus:ring-2 focus:ring-brand-blue/10 focus:border-brand-blue transition-all"
                                    placeholder="<?= __('contracts.contract_number') ?>..." type="text" name="search"
                                    value="<?= htmlspecialchars($filters['search'] ?? '') ?>" />
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label
                                class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 block"><?= __('common.status') ?></label>
                            <select name="status"
                                class="w-full py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700 focus:ring-2 focus:ring-brand-blue/10 focus:border-brand-blue transition-all">
                                <option value=""><?= __('common.all') ?> <?= __('common.status') ?></option>
                                <?php foreach ($statuses as $key => $label): ?>
                                    <option value="<?= $key ?>" <?= ($filters['status'] ?? '') === $key ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label
                                class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 block"><?= __('contracts.vessel') ?></label>
                            <select name="vessel_id"
                                class="w-full py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700 focus:ring-2 focus:ring-brand-blue/10 focus:border-brand-blue transition-all">
                                <option value=""><?= __('common.all') ?> <?= __('sidebar.vessels') ?></option>
                                <?php foreach ($vessels as $v): ?>
                                    <option value="<?= $v['id'] ?>" <?= ($filters['vessel_id'] ?? '') == $v['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($v['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label
                                class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 block"><?= __('contracts.client') ?></label>
                            <select name="client_id"
                                class="w-full py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700 focus:ring-2 focus:ring-brand-blue/10 focus:border-brand-blue transition-all">
                                <option value=""><?= __('common.all') ?> <?= __('sidebar.clients') ?></option>
                                <?php foreach ($clients as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= ($filters['client_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="md:col-span-1 flex items-end gap-2">
                            <button type="submit"
                                class="w-full h-[42px] flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg transition-all">
                                <span class="material-icons-round text-xl">filter_list</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Contracts Table -->
                <div class="bg-white rounded-xl border border-slate-100 shadow-soft overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr
                                    class="bg-slate-50/80 border-b border-slate-100 text-[10px] uppercase tracking-wider text-slate-500 font-bold">
                                    <th class="px-6 py-4"><?= __('contracts.contract_number') ?></th>
                                    <th class="px-6 py-4"><?= __('crews.crew_name') ?></th>
                                    <th class="px-6 py-4"><?= __('contracts.rank') ?></th>
                                    <th class="px-6 py-4"><?= __('contracts.vessel') ?></th>
                                    <th class="px-6 py-4"><?= __('contracts.client') ?></th>
                                    <th class="px-6 py-4"><?= __('contracts.sign_on_date') ?> / <?= __('contracts.sign_off_date') ?></th>
                                    <th class="px-6 py-4"><?= __('common.status') ?></th>
                                    <th class="px-6 py-4 text-center"><?= __('common.actions') ?></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php if (empty($contracts)): ?>
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                            <span class="material-icons-round text-4xl mb-2 block">inbox</span>
                                            <?= __('contracts.no_contracts') ?>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($contracts as $contract):
                                        $statusColors = [
                                            'draft' => 'slate',
                                            'pending_approval' => 'amber',
                                            'active' => 'green',
                                            'onboard' => 'blue',
                                            'completed' => 'blue',
                                            'terminated' => 'red',
                                            'cancelled' => 'slate'
                                        ];
                                        $statusColor = $statusColors[$contract['status']] ?? 'slate';

                                        $days = $contract['days_remaining'] ?? null;
                                        $daysColor = $days <= 7 ? 'red' : ($days <= 30 ? 'yellow' : 'green');

                                        // Generate initials for avatar
                                        $nameParts = explode(' ', $contract['crew_name']);
                                        $initials = strtoupper(substr($nameParts[0] ?? '', 0, 1) . substr($nameParts[1] ?? '', 0, 1));
                                        if (strlen($initials) < 2)
                                            $initials = strtoupper(substr($contract['crew_name'], 0, 2));

                                        $avatarColors = ['amber', 'blue', 'green', 'purple', 'pink', 'indigo'];
                                        $avatarColor = $avatarColors[array_sum(str_split(ord($contract['crew_name'][0] ?? 'A'))) % count($avatarColors)];
                                        ?>
                                        <tr class="hover:bg-slate-50/80 transition-all group">
                                            <td
                                                class="px-6 py-5 text-sm font-semibold text-brand-blue hover:underline cursor-pointer">
                                                <a href="<?= BASE_URL ?>contracts/<?= $contract['id'] ?>">
                                                    <?= htmlspecialchars($contract['contract_no']) ?>
                                                </a>
                                            </td>
                                            <td class="px-6 py-5">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-8 h-8 rounded-full bg-<?= $avatarColor ?>-100 flex items-center justify-center text-[10px] font-bold text-<?= $avatarColor ?>-700 ring-2 ring-white shadow-sm">
                                                        <?= $initials ?>
                                                    </div>
                                                    <span class="text-sm font-medium text-slate-700">
                                                        <?= htmlspecialchars($contract['crew_name']) ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-5 text-sm text-slate-600">
                                                <?= htmlspecialchars($contract['rank_name'] ?? '-') ?>
                                            </td>
                                            <td class="px-6 py-5 text-sm text-slate-600">
                                                <?= htmlspecialchars($contract['vessel_name'] ?? '-') ?>
                                            </td>
                                            <td class="px-6 py-5 text-sm text-slate-600">
                                                <?= htmlspecialchars($contract['client_name'] ?? '-') ?>
                                            </td>
                                            <td class="px-6 py-5">
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-bold text-slate-700">
                                                        <?= $contract['sign_on_date'] ? date('d M Y', strtotime($contract['sign_on_date'])) : '-' ?>
                                                    </span>
                                                    <span class="text-[10px] text-slate-400">
                                                        <?= $contract['sign_off_date'] ? date('d M Y', strtotime($contract['sign_off_date'])) : '-' ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-5">
                                                <span
                                                    class="px-2.5 py-1 rounded-full bg-<?= $statusColor ?>-50 text-<?= $statusColor ?>-600 border border-<?= $statusColor ?>-100 text-[10px] font-bold uppercase tracking-tight">
                                                    <?= ucfirst(str_replace('_', ' ', $contract['status'])) ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-5 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <a href="<?= BASE_URL ?>contracts/<?= $contract['id'] ?>"
                                                        class="p-1.5 text-slate-400 hover:text-brand-blue hover:bg-blue-50 rounded-lg transition-all">
                                                        <span class="material-icons-round text-xl">visibility</span>
                                                    </a>
                                                    <a href="<?= BASE_URL ?>contracts/edit/<?= $contract['id'] ?>"
                                                        class="p-1.5 text-slate-400 hover:text-brand-blue hover:bg-blue-50 rounded-lg transition-all">
                                                        <span class="material-icons-round text-xl">edit</span>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total > $perPage):
                        $totalPages = ceil($total / $perPage);
                        ?>
                        <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
                            <p class="text-xs text-slate-500 font-medium">
                                <?= __('common.showing') ?>
                                <?= (($page - 1) * $perPage) + 1 ?> - 
                                <?= min($page * $perPage, $total) ?> <?= __('common.of') ?>
                                <?= $total ?> <?= __('common.entries') ?>
                            </p>
                            <div class="flex items-center gap-2">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?= $page - 1 ?>&<?= http_build_query(array_filter($filters)) ?>"
                                        class="p-1.5 text-slate-400 hover:bg-white border border-transparent hover:border-slate-200 rounded-lg transition-all">
                                        <span class="material-icons-round">chevron_left</span>
                                    </a>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= min($totalPages, 5); $i++): ?>
                                    <a href="?page=<?= $i ?>&<?= http_build_query(array_filter($filters)) ?>"
                                        class="w-8 h-8 text-xs font-bold <?= $i === $page ? 'bg-brand-blue text-white' : 'text-slate-600 hover:bg-white border border-transparent hover:border-slate-200' ?> rounded-lg transition-all flex items-center justify-center">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>

                                <?php if ($page < $totalPages): ?>
                                    <a href="?page=<?= $page + 1 ?>&<?= http_build_query(array_filter($filters)) ?>"
                                        class="p-1.5 text-slate-400 hover:bg-white border border-transparent hover:border-slate-200 rounded-lg transition-all">
                                        <span class="material-icons-round">chevron_right</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

</body>

</html>