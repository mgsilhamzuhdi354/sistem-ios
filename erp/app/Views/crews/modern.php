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
    'pending_approval' => 0,
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
        'pending_approval' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200">PENDING APPROVAL</span>',
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

                <!-- Ringkasan Biaya (Cost Summary) -->
                <?php $cost = $costSummary ?? ['idr' => 0, 'usd' => 0, 'other' => 0, 'other_currency' => '', 'active_crew' => 0]; ?>
                <div class="mt-6 mb-6">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="material-icons-round text-slate-400">payments</span>
                        <h3 class="font-bold text-slate-700">Ringkasan Biaya</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- IDR -->
                        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl p-5 border border-emerald-200/50 hover:shadow-lg transition-shadow">
                            <p class="text-2xl font-bold text-slate-800">Rp <?= number_format($cost['idr'], 0, ',', '.') ?></p>
                            <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider mt-1">Biaya Bulanan (IDR)</p>
                        </div>
                        <!-- USD -->
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 border border-blue-200/50 hover:shadow-lg transition-shadow">
                            <p class="text-2xl font-bold text-slate-800">$<?= number_format($cost['usd'], 0, ',', '.') ?></p>
                            <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider mt-1">Biaya Bulanan (USD)</p>
                        </div>
                        <!-- Active Crew / Other Currency -->
                        <?php if ($cost['other'] > 0): ?>
                        <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl p-5 border border-amber-200/50 hover:shadow-lg transition-shadow">
                            <p class="text-2xl font-bold text-slate-800"><?= $cost['other_currency'] ?: 'MYR' ?> <?= number_format($cost['other'], 0, ',', '.') ?></p>
                            <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider mt-1">Biaya Bulanan (<?= $cost['other_currency'] ?: 'Lainnya' ?>)</p>
                        </div>
                        <?php else: ?>
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5 border border-purple-200/50 hover:shadow-lg transition-shadow">
                            <p class="text-2xl font-bold text-slate-800"><?= $cost['active_crew'] ?></p>
                            <p class="text-xs font-semibold text-purple-600 uppercase tracking-wider mt-1">Kru Aktif</p>
                        </div>
                        <?php endif; ?>
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
                                <option value="pending_approval" <?= ($filters['status'] ?? '') === 'pending_approval' ? 'selected' : '' ?>>Pending Approval</option>
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
                                    <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                        Kontrak</th>
                                    <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                        Dokumen</th>
                                    <th
                                        class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-slate-500 text-right">
                                        <?= __('common.actions') ?></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php if (empty($crews)): ?>
                                    <tr>
                                        <td colspan="9" class="py-12 px-6 text-center text-slate-500">
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
                                                        <div class="text-sm font-semibold text-slate-900 flex items-center gap-1.5">
                                                            <?= htmlspecialchars($crew['full_name']) ?>
                                                            <?php if (($crew['source'] ?? '') === 'recruitment'): ?>
                                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-100 text-indigo-700 border border-indigo-200">REC</span>
                                                            <?php endif; ?>
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
                                            <td class="py-4 px-6">
                                                <?php
                                                $contractEnd = $crew['contract_end_date'] ?? null;
                                                $contractVessel = $crew['contract_vessel'] ?? null;
                                                if ($contractEnd) {
                                                    $endDate = new DateTime($contractEnd);
                                                    $today = new DateTime();
                                                    $diff = $today->diff($endDate);
                                                    $daysLeft = $endDate > $today ? (int)$diff->format('%a') : -(int)$diff->format('%a');
                                                    
                                                    if ($daysLeft < 0) {
                                                        // Expired
                                                        echo '<div class="flex flex-col">';
                                                        echo '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 border border-red-200"><span class="material-icons-round" style="font-size:11px">event_busy</span>Berakhir</span>';
                                                        echo '<span class="text-[10px] text-red-500 mt-0.5">' . date('d M Y', strtotime($contractEnd)) . '</span>';
                                                        echo '</div>';
                                                    } elseif ($daysLeft <= 30) {
                                                        // < 30 days
                                                        echo '<div class="flex flex-col">';
                                                        echo '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 border border-red-200"><span class="material-icons-round" style="font-size:11px">warning</span>' . $daysLeft . ' hari lagi</span>';
                                                        echo '<span class="text-[10px] text-slate-400 mt-0.5">' . date('d M Y', strtotime($contractEnd)) . '</span>';
                                                        if ($contractVessel) echo '<span class="text-[10px] text-slate-400">🚢 ' . htmlspecialchars($contractVessel) . '</span>';
                                                        echo '</div>';
                                                    } elseif ($daysLeft <= 60) {
                                                        // 30-60 days
                                                        echo '<div class="flex flex-col">';
                                                        echo '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 border border-amber-200"><span class="material-icons-round" style="font-size:11px">schedule</span>' . $daysLeft . ' hari lagi</span>';
                                                        echo '<span class="text-[10px] text-slate-400 mt-0.5">' . date('d M Y', strtotime($contractEnd)) . '</span>';
                                                        if ($contractVessel) echo '<span class="text-[10px] text-slate-400">🚢 ' . htmlspecialchars($contractVessel) . '</span>';
                                                        echo '</div>';
                                                    } else {
                                                        // > 60 days
                                                        echo '<div class="flex flex-col">';
                                                        echo '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200"><span class="material-icons-round" style="font-size:11px">check_circle</span>' . $daysLeft . ' hari lagi</span>';
                                                        echo '<span class="text-[10px] text-slate-400 mt-0.5">' . date('d M Y', strtotime($contractEnd)) . '</span>';
                                                        if ($contractVessel) echo '<span class="text-[10px] text-slate-400">🚢 ' . htmlspecialchars($contractVessel) . '</span>';
                                                        echo '</div>';
                                                    }
                                                } else {
                                                    echo '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-400 border border-slate-200"><span class="material-icons-round" style="font-size:11px">remove_circle_outline</span>Belum ada</span>';
                                                }
                                                ?>
                                            </td>
                                            <td class="py-4 px-6">
                                                <?php
                                                $docCount = (int)($crew['doc_count'] ?? 0);
                                                $expiredCount = (int)($crew['expired_doc_count'] ?? 0);
                                                if ($docCount === 0) {
                                                    echo '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-400 border border-slate-200"><span class="material-icons-round" style="font-size:12px">folder_off</span>0</span>';
                                                } elseif ($expiredCount > 0) {
                                                    echo '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 border border-red-200"><span class="material-icons-round" style="font-size:12px">warning</span>' . $docCount . ' <span class="text-red-500">(' . $expiredCount . ' exp)</span></span>';
                                                } else {
                                                    echo '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200"><span class="material-icons-round" style="font-size:12px">verified</span>' . $docCount . '</span>';
                                                }
                                                ?>
                                            </td>
                                            <td class="py-4 px-6 text-right">
                                                <div
                                                    class="flex items-center justify-end space-x-1 opacity-80 group-hover:opacity-100 transition-opacity">
                                                    <a href="<?= BASE_URL ?>crews/<?= $crew['id'] ?>"
                                                        class="p-1.5 rounded-md text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                                                        title="<?= __('common.view') ?>">
                                                        <span class="material-icons-round text-lg">visibility</span>
                                                    </a>
                                                    <a href="<?= BASE_URL ?>crews/edit/<?= $crew['id'] ?>"
                                                        class="p-1.5 rounded-md text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition-colors"
                                                        title="Edit">
                                                        <span class="material-icons-round text-lg">edit</span>
                                                    </a>
                                                    <button onclick="confirmDelete(<?= $crew['id'] ?>, '<?= htmlspecialchars(addslashes($crew['full_name']), ENT_QUOTES) ?>')"
                                                        class="p-1.5 rounded-md text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                                        title="Hapus">
                                                        <span class="material-icons-round text-lg">delete</span>
                                                    </button>
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


<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden" style="animation: slideUp 0.3s ease-out">
            <div class="px-6 py-4 bg-gradient-to-r from-red-500 to-red-600 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="material-icons-round">warning</span>
                    <h3 class="text-lg font-bold">Hapus Data Kru</h3>
                </div>
                <button onclick="closeDeleteModal()" class="p-1 hover:bg-white/20 rounded-lg transition-colors">
                    <span class="material-icons-round">close</span>
                </button>
            </div>
            <div class="p-6">
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5">
                    <p class="text-sm text-red-700">
                        <span class="material-icons-round text-[16px] align-middle mr-1">error</span>
                        Anda akan menghapus <strong id="deleteCrewName"></strong> beserta <strong>semua data terkait</strong>. Tindakan ini tidak bisa dibatalkan!
                    </p>
                </div>
                <div class="mb-5">
                    <p class="text-sm font-semibold text-slate-700 mb-3">Data yang akan dihapus:</p>
                    <div id="deleteDataList" class="space-y-2"></div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Ketik <span class="text-red-600 font-mono bg-red-50 px-1.5 py-0.5 rounded">HAPUS</span> untuk konfirmasi</label>
                    <input type="text" id="deleteConfirmInput"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-400 transition-colors"
                           placeholder="Ketik HAPUS..." autocomplete="off">
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 flex justify-end gap-3 bg-slate-50">
                <input type="hidden" id="deleteCrewId">
                <button onclick="closeDeleteModal()" class="px-4 py-2.5 border border-slate-200 text-slate-600 rounded-xl text-sm font-medium hover:bg-slate-100 transition-colors">Batal</button>
                <button onclick="submitDelete()" id="deleteSubmitBtn" disabled
                        class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-bold shadow-sm transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="material-icons-round text-lg">delete_forever</span>
                    Hapus Permanen
                </button>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes slideUp { from { opacity: 0; transform: translateY(30px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
</style>

<script>
const BASE_URL = '<?= BASE_URL ?>';

function confirmDelete(crewId, crewName) {
    document.getElementById('deleteCrewId').value = crewId;
    document.getElementById('deleteCrewName').textContent = crewName;
    document.getElementById('deleteConfirmInput').value = '';
    document.getElementById('deleteSubmitBtn').disabled = true;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    document.getElementById('deleteDataList').innerHTML = '<div class="flex items-center justify-center py-4"><div class="animate-spin w-6 h-6 rounded-full" style="border:3px solid #e2e8f0;border-top-color:#475569"></div></div>';

    fetch(BASE_URL + 'crews/deleteInfo/' + crewId, { credentials: 'same-origin' })
        .then(r => r.json())
        .then(data => {
            const items = [
                { icon: 'description', label: 'Kontrak', count: data.contracts ?? 0, color: '#3b82f6' },
                { icon: 'payments', label: 'Data Payroll', count: data.payroll_items ?? 0, color: '#22c55e' },
                { icon: 'psychology', label: 'Skill & Sertifikasi', count: data.skills ?? 0, color: '#a855f7' },
                { icon: 'folder', label: 'Dokumen', count: data.documents ?? 0, color: '#f59e0b' },
                { icon: 'work_history', label: 'Pengalaman Kerja', count: data.experiences ?? 0, color: '#14b8a6' },
            ];
            document.getElementById('deleteDataList').innerHTML = items.map(item =>
                `<div class="flex items-center justify-between px-3 py-2 bg-slate-50 rounded-lg border border-slate-100">
                    <div class="flex items-center gap-2 text-sm text-slate-600">
                        <span class="material-icons-round" style="font-size:18px;color:${item.color}">${item.icon}</span>
                        ${item.label}
                    </div>
                    <span class="text-sm font-bold ${item.count > 0 ? 'text-red-600' : 'text-slate-400'}">${item.count} data</span>
                </div>`
            ).join('');
        })
        .catch(() => {
            document.getElementById('deleteDataList').innerHTML = '<p class="text-sm text-slate-500 text-center py-2">Tidak dapat memuat info data terkait</p>';
        });
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.getElementById('deleteConfirmInput').addEventListener('input', function() {
    document.getElementById('deleteSubmitBtn').disabled = this.value.trim() !== 'HAPUS';
});

function submitDelete() {
    if (document.getElementById('deleteConfirmInput').value.trim() !== 'HAPUS') return;
    const crewId = document.getElementById('deleteCrewId').value;
    const btn = document.getElementById('deleteSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="animate-spin material-icons-round text-lg">progress_activity</span> Menghapus...';

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = BASE_URL + 'crews/delete/' + crewId;
    document.body.appendChild(form);
    form.submit();
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDeleteModal(); });
</script>


</body>

</html>