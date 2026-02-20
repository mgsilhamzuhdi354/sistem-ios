<?php
/**
 * PT Indo Ocean - ERP System
 * Modern Crew Database View
 */
$currentPage = 'crews';

// Calculate statistics
$stats = [
    'available' => 0,
    'onboard' => 0,
    'standby' => 0,
    'terminated' => 0,
    'total' => count($crews ?? [])
];

foreach ($crews ?? [] as $crew) {
    $status = strtolower($crew['status'] ?? '');
    if (isset($stats[$status])) {
        $stats[$status]++;
    }
}

// Helper function to get initials
function getInitials($name)
{
    $parts = explode(' ', trim($name));
    if (count($parts) == 1) {
        return strtoupper(substr($parts[0], 0, 2));
    }
    return strtoupper(substr($parts[0], 0, 1) . substr($parts[count($parts) - 1], 0, 1));
}

// Helper function for status badge
function getStatusBadge($status)
{
    $badges = [
        'available' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">AVAILABLE</span>',
        'onboard' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">ON BOARD</span>',
        'standby' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-700 border border-orange-200">STANDBY</span>',
        'terminated' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">TERMINATED</span>',
    ];
    return $badges[strtolower($status)] ?? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200">' . strtoupper($status) . '</span>';
}
?>
<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?= htmlspecialchars($title ?? 'Crew Database') ?> - PT Indo Ocean</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-blue': '#1e40af',
                        'brand-gold': '#fbbf24',
                    },
                    fontFamily: {
                        sans: ["Inter", "sans-serif"],
                    },
                },
            },
        };
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-600">

    <div class="flex h-screen overflow-hidden">
        <!-- Modern Sidebar (Reusable Partial) -->
        <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="ml-64 flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">
            <!-- Header -->
            <header
                class="h-20 flex items-center justify-between px-8 bg-white/80 backdrop-blur-xl border-b border-slate-200/60 z-10">
                <div class="flex flex-col justify-center">
                    <h2 class="text-lg font-bold text-slate-800"><?= __('crews.title') ?></h2>
                    <span class="text-xs text-slate-500"><?= __('crews.subtitle') ?></span>
                </div>
                <div class="flex items-center gap-4">
                    <button class="p-2 text-slate-500 hover:bg-slate-100 rounded-full transition-colors">
                        <span class="material-icons-round text-xl">search</span>
                    </button>
                    <button
                        class="relative p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full transition-all">
                        <span class="material-icons-round">notifications</span>
                    </button>
                    <a href="<?= BASE_URL ?>crews/create"
                        class="bg-brand-gold hover:bg-yellow-400 text-slate-900 pl-4 pr-5 py-2.5 rounded-lg font-bold text-sm flex items-center gap-2 transition-all active:scale-95 shadow-lg shadow-yellow-500/20">
                        <span class="material-icons-round text-lg">add</span>
                        <?= __('common.add_new') ?>
                    </a>
                </div>
            </header>

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto custom-scrollbar p-8 space-y-6">

                <!-- Flash Messages -->
                <?php if (!empty($flash)): ?>
                    <div
                        class="<?= $flash['type'] === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800' ?> border-l-4 p-4 rounded-lg">
                        <p class="font-medium"><?= htmlspecialchars($flash['message']) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Page Header -->
                <div class="flex items-end justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800"><?= __('crews.list_title') ?></h2>
                        <p class="text-slate-500 mt-1"><?= __('crews.subtitle') ?></p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div
                        class="bg-white p-6 rounded-xl border border-slate-100 shadow-soft hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-slate-500 text-sm font-medium mb-1"><?= __('crews.available') ?></p>
                                <h3 class="text-3xl font-bold text-slate-800"><?= $stats['available'] ?></h3>
                            </div>
                            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                                <span class="material-icons-round">person_check</span>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white p-6 rounded-xl border border-slate-100 shadow-soft hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-slate-500 text-sm font-medium mb-1"><?= __('crews.onboard') ?></p>
                                <h3 class="text-3xl font-bold text-slate-800"><?= $stats['onboard'] ?></h3>
                            </div>
                            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                                <span class="material-icons-round">directions_boat</span>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white p-6 rounded-xl border border-slate-100 shadow-soft hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-slate-500 text-sm font-medium mb-1"><?= __('crews.on_leave') ?></p>
                                <h3 class="text-3xl font-bold text-slate-800"><?= $stats['standby'] ?></h3>
                            </div>
                            <div class="p-3 bg-orange-50 text-orange-600 rounded-xl">
                                <span class="material-icons-round">beach_access</span>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white p-6 rounded-xl border border-slate-100 shadow-soft hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-slate-500 text-sm font-medium mb-1"><?= __('crews.total_crew') ?></p>
                                <h3 class="text-3xl font-bold text-slate-800"><?= $stats['total'] ?></h3>
                            </div>
                            <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                                <span class="material-icons-round">groups</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter Bar -->
                <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex flex-col md:flex-row gap-4">
                    <form method="GET" action="<?= BASE_URL ?>crews" class="flex-1 flex flex-col md:flex-row gap-4">
                        <div class="flex-1 relative">
                            <span
                                class="material-icons-round absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400">search</span>
                            <input name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                                class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue outline-none text-sm text-slate-700 placeholder-slate-400 transition-shadow"
                                placeholder="<?= __('common.search') ?>..." type="text" />
                        </div>

                        <div class="w-full md:w-48 relative">
                            <select name="status"
                                class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue outline-none text-sm text-slate-700 appearance-none cursor-pointer">
                                <option value=""><?= __('common.all') ?> <?= __('common.status') ?></option>
                                <option value="available" <?= ($filters['status'] ?? '') === 'available' ? 'selected' : '' ?>><?= __('crews.available') ?></option>
                                <option value="onboard" <?= ($filters['status'] ?? '') === 'onboard' ? 'selected' : '' ?>>
                                    <?= __('crews.onboard') ?></option>
                                <option value="standby" <?= ($filters['status'] ?? '') === 'standby' ? 'selected' : '' ?>>
                                    <?= __('crews.on_leave') ?></option>
                                <option value="terminated" <?= ($filters['status'] ?? '') === 'terminated' ? 'selected' : '' ?>><?= __('crews.terminated') ?></option>
                            </select>
                            <span
                                class="material-icons-round absolute right-2 top-1/2 transform -translate-y-1/2 text-slate-400 pointer-events-none text-lg">expand_more</span>
                        </div>

                        <div class="flex space-x-2">
                            <button type="submit"
                                class="px-4 py-2 bg-brand-blue text-white font-semibold rounded-lg flex items-center text-sm shadow-sm hover:bg-blue-800 transition-all">
                                <span class="material-icons-round mr-1 text-lg">filter_list</span>
                                <?= __('common.filter') ?>
                            </button>
                            <a href="<?= BASE_URL ?>crews"
                                class="px-4 py-2 bg-white border border-slate-200 text-slate-600 font-medium rounded-lg flex items-center text-sm hover:bg-slate-50 transition-all">
                                <span class="material-icons-round mr-1 text-lg">restart_alt</span>
                                <?= __('common.reset') ?>
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Data Table -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100">
                                    <th
                                        class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-slate-500 w-16">
                                        #</th>
                                    <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                        <?= __('crews.crew_name') ?></th>
                                    <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                        <?= __('employees.employee_id') ?></th>
                                    <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                        <?= __('crews.contact') ?></th>
                                    <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                        <?= __('crews.rank') ?></th>
                                    <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                        <?= __('common.status') ?></th>
                                    <th
                                        class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-slate-500 text-right">
                                        <?= __('common.actions') ?></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php if (empty($crews)): ?>
                                    <tr>
                                        <td colspan="7" class="py-12 px-6 text-center text-slate-500">
                                            <div class="flex flex-col items-center">
                                                <span
                                                    class="material-icons-round text-6xl mb-2 text-slate-300">group_off</span>
                                                <p class="text-lg font-medium"><?= __('crews.no_crew') ?></p>
                                                <p class="text-sm"><?= __('common.no_data') ?></p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $index = (($page ?? 1) - 1) * 20 + 1; ?>
                                    <?php foreach ($crews as $crew): ?>
                                        <tr class="group hover:bg-slate-50 transition-colors">
                                            <td class="py-4 px-6 text-sm text-slate-500"><?= $index++ ?></td>
                                            <td class="py-4 px-6">
                                                <div class="flex items-center">
                                                    <?php if (!empty($crew['photo_url'])): ?>
                                                        <img alt="<?= htmlspecialchars($crew['full_name']) ?>"
                                                            class="h-10 w-10 rounded-full object-cover border-2 border-white shadow-sm"
                                                            src="<?= BASE_URL . htmlspecialchars($crew['photo_url']) ?>" />
                                                    <?php else: ?>
                                                        <div
                                                            class="h-10 w-10 rounded-full bg-brand-gold text-slate-900 flex items-center justify-center font-bold text-sm border-2 border-white shadow-sm">
                                                            <?= getInitials($crew['full_name']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-semibold text-slate-900">
                                                            <?= htmlspecialchars($crew['full_name']) ?>
                                                        </div>
                                                        <div class="text-xs text-slate-500">
                                                            <?= htmlspecialchars($crew['rank_name'] ?? 'No Rank') ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                                    <?= htmlspecialchars($crew['employee_id'] ?? 'N/A') ?>
                                                </span>
                                            </td>
                                            <td class="py-4 px-6 text-sm text-slate-600">
                                                <div><?= htmlspecialchars($crew['phone'] ?? '-') ?></div>
                                                <div class="text-xs text-slate-400">
                                                    <?= htmlspecialchars($crew['email'] ?? '-') ?>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6 text-sm text-slate-600">
                                                <?= htmlspecialchars($crew['rank_name'] ?? 'Not Assigned') ?>
                                            </td>
                                            <td class="py-4 px-6"><?= getStatusBadge($crew['status'] ?? 'unknown') ?></td>
                                            <td class="py-4 px-6 text-right">
                                                <div
                                                    class="flex items-center justify-end space-x-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                                    <a href="<?= BASE_URL ?>crews/<?= $crew['id'] ?>"
                                                        class="p-1.5 rounded-md text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                                                        title="<?= __('common.view') ?>">
                                                        <span class="material-icons-round text-lg">visibility</span>
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
                    <?php if (!empty($crews)): ?>
                        <?php
                        $totalPages = ceil(($total ?? 0) / 20);
                        $currentPage = $page ?? 1;
                        $start = (($currentPage - 1) * 20) + 1;
                        $end = min($currentPage * 20, $total ?? 0);
                        ?>
                        <div class="bg-white px-6 py-4 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-sm text-slate-500">
                                <?= __('common.showing') ?> <span class="font-medium text-slate-900"><?= $start ?></span> -
                                <span class="font-medium text-slate-900"><?= $end ?></span> <?= __('common.of') ?>
                                <span class="font-medium text-slate-900"><?= $total ?? 0 ?></span> <?= __('common.entries') ?>
                            </span>
                            <div class="flex space-x-2">
                                <?php if ($currentPage > 1): ?>
                                    <a href="?page=<?= $currentPage - 1 ?><?= !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?><?= !empty($filters['status']) ? '&status=' . urlencode($filters['status']) : '' ?>"
                                        class="px-3 py-1 rounded-md border border-slate-200 text-slate-500 hover:bg-slate-50 text-sm">
                                        <?= __('common.previous') ?>
                                    </a>
                                <?php else: ?>
                                    <button disabled
                                        class="px-3 py-1 rounded-md border border-slate-200 text-slate-500 opacity-50 text-sm cursor-not-allowed">
                                        <?= __('common.previous') ?>
                                    </button>
                                <?php endif; ?>

                                <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                    <?php if ($i == $currentPage): ?>
                                        <button class="px-3 py-1 rounded-md bg-brand-blue text-white font-medium text-sm">
                                            <?= $i ?>
                                        </button>
                                    <?php else: ?>
                                        <a href="?page=<?= $i ?><?= !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?><?= !empty($filters['status']) ? '&status=' . urlencode($filters['status']) : '' ?>"
                                            class="px-3 py-1 rounded-md border border-slate-200 text-slate-500 hover:bg-slate-50 text-sm">
                                            <?= $i ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endfor; ?>

                                <?php if ($currentPage < $totalPages): ?>
                                    <a href="?page=<?= $currentPage + 1 ?><?= !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?><?= !empty($filters['status']) ? '&status=' . urlencode($filters['status']) : '' ?>"
                                        class="px-3 py-1 rounded-md border border-slate-200 text-slate-500 hover:bg-slate-50 text-sm">
                                        <?= __('common.next') ?>
                                    </a>
                                <?php else: ?>
                                    <button disabled
                                        class="px-3 py-1 rounded-md border border-slate-200 text-slate-500 opacity-50 text-sm cursor-not-allowed">
                                        <?= __('common.next') ?>
                                    </button>
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