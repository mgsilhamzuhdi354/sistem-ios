<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('contracts.expiring_title') ?> | IndoOcean ERP</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-blue': '#1e40af',
                        'brand-gold': '#f59e0b',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
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
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Modern Sidebar (Compact) -->
        <?php
        $currentPage = 'contracts-expiring';
        include APPPATH . 'Views/partials/modern_sidebar.php';
        ?>

        <!-- Main Content -->
        <main class="ml-64 flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">
            <!-- Header -->
            <header
                class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between z-10 flex-shrink-0">
                <div class="flex items-center gap-2">
                    <span class="text-slate-400 font-medium text-sm"><?= __('sidebar.contracts') ?></span>
                    <span class="material-icons-outlined text-slate-400 text-sm">chevron_right</span>
                    <span class="text-slate-800 font-semibold text-sm"><?= __('contracts.expiring_title') ?></span>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Language Toggle -->
                    <div class="flex bg-slate-100 p-1 rounded-lg">
                        <button class="px-3 py-1 text-xs font-bold text-slate-500">ID</button>
                        <button
                            class="px-3 py-1 text-xs font-bold bg-white shadow-sm rounded-md text-orange-600">EN</button>
                    </div>

                    <!-- Notifications -->
                    <button class="p-2 text-slate-400 hover:bg-slate-50 rounded-full relative">
                        <span class="material-icons-outlined">notifications</span>
                        <span
                            class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                    </button>
                </div>
            </header>

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto p-8">
                <div class="max-w-7xl mx-auto">
                    <!-- Page Header -->
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                                <span class="material-icons-outlined text-orange-500 text-3xl">schedule</span>
                                <?= __('contracts.expiring_title') ?>
                            </h2>
                            <p class="text-slate-500 mt-1"><?= __('contracts.expiring_subtitle') ?></p>
                        </div>

                        <a href="<?= BASE_URL ?>contracts"
                            class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-all shadow-sm">
                            <span class="material-icons-outlined text-sm">arrow_back</span>
                            <?= __('contracts.back_to_contracts') ?>
                        </a>
                    </div>

                    <!-- Filter Section -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 mb-8 shadow-sm">
                        <div class="flex items-center gap-4">
                            <label for="expiry-filter" class="text-sm font-medium text-slate-600">
                                <?= __('contracts.show_expiring_within') ?>:
                            </label>
                            <select id="expiry-filter"
                                class="bg-slate-50 border-slate-200 rounded-xl text-sm font-semibold px-4 py-2 min-w-[150px] focus:ring-blue-500 focus:border-blue-500 transition-all">
                                <option value="30">30 <?= __('contracts.days_label') ?></option>
                                <option value="60" selected>60 <?= __('contracts.days_label') ?></option>
                                <option value="90">90 <?= __('contracts.days_label') ?></option>
                            </select>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <!-- Critical -->
                        <div
                            class="bg-red-50 border border-red-100 p-6 rounded-2xl flex items-center gap-5 transition-transform hover:scale-[1.02]">
                            <div class="w-14 h-14 bg-red-500/10 rounded-xl flex items-center justify-center">
                                <span class="material-icons-outlined text-red-600 text-3xl">error_outline</span>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-slate-800">
                                    <?= isset($critical_count) ? $critical_count : 0 ?>
                                </div>
                                <div class="text-sm font-medium text-red-600/80"><?= __('contracts.critical_days') ?></div>
                            </div>
                        </div>

                        <!-- Warning -->
                        <div
                            class="bg-amber-50 border border-amber-100 p-6 rounded-2xl flex items-center gap-5 transition-transform hover:scale-[1.02]">
                            <div class="w-14 h-14 bg-amber-500/10 rounded-xl flex items-center justify-center">
                                <span class="material-icons-outlined text-amber-600 text-3xl">warning_amber</span>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-slate-800">
                                    <?= isset($warning_count) ? $warning_count : 0 ?>
                                </div>
                                <div class="text-sm font-medium text-amber-600/80"><?= __('contracts.warning_days') ?></div>
                            </div>
                        </div>

                        <!-- Upcoming -->
                        <div
                            class="bg-blue-50 border border-blue-100 p-6 rounded-2xl flex items-center gap-5 transition-transform hover:scale-[1.02]">
                            <div class="w-14 h-14 bg-blue-500/10 rounded-xl flex items-center justify-center">
                                <span class="material-icons-outlined text-blue-600 text-3xl">info</span>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-slate-800">
                                    <?= isset($upcoming_count) ? $upcoming_count : 0 ?>
                                </div>
                                <div class="text-sm font-medium text-blue-600/80"><?= __('contracts.upcoming_days') ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Contracts Table -->
                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                        <?php if (empty($contracts)): ?>
                            <!-- Empty State -->
                            <div class="flex flex-col items-center justify-center py-32 px-4">
                                <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mb-6">
                                    <span class="material-icons-outlined text-emerald-500 text-5xl">check_circle</span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-800"><?= __('contracts.no_expiring') ?>
                                </h3>
                                <p class="text-slate-500 text-sm mt-1"><?= __('contracts.all_good') ?></p>
                            </div>
                        <?php else: ?>
                            <!-- Table with Data -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="bg-slate-50 border-b border-slate-200">
                                            <th
                                                class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                                Urgency</th>
                                            <th
                                                class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                                Contract No</th>
                                            <th
                                                class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                                Crew Name</th>
                                            <th
                                                class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                                Rank</th>
                                            <th
                                                class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                                Vessel</th>
                                            <th
                                                class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                                Sign Off Date</th>
                                            <th
                                                class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider text-center">
                                                <?= __('contracts.days_left') ?></th>
                                            <th
                                                class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider text-right">
                                                Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <?php foreach ($contracts as $contract): ?>
                                            <?php
                                            $daysLeft = $contract['days_remaining'] ?? 0;
                                            $urgencyClass = 'bg-blue-100 text-blue-700';
                                            $urgencyLabel = 'Upcoming';
                                            $urgencyIcon = 'info';

                                            if ($daysLeft <= 7) {
                                                $urgencyClass = 'bg-red-100 text-red-700';
                                                $urgencyLabel = 'Critical';
                                                $urgencyIcon = 'error_outline';
                                            } elseif ($daysLeft <= 30) {
                                                $urgencyClass = 'bg-amber-100 text-amber-700';
                                                $urgencyLabel = 'Warning';
                                                $urgencyIcon = 'warning_amber';
                                            }
                                            ?>
                                            <tr class="hover:bg-slate-50 transition-colors">
                                                <td class="px-6 py-4">
                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold <?= $urgencyClass ?>">
                                                        <span class="material-icons-outlined text-sm">
                                                            <?= $urgencyIcon ?>
                                                        </span>
                                                        <?= $urgencyLabel ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="font-mono text-sm font-semibold text-slate-700">
                                                        <?= htmlspecialchars($contract['contract_no'] ?? '') ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="text-sm font-medium text-slate-800">
                                                        <?= htmlspecialchars($contract['crew_name']) ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="text-sm text-slate-600">
                                                        <?= htmlspecialchars($contract['rank_name'] ?? '-') ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="text-sm text-slate-600">
                                                        <?= htmlspecialchars($contract['vessel_name'] ?? '-') ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="text-sm text-slate-600">
                                                        <?= date('d M Y', strtotime($contract['sign_off_date'])) ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    <span
                                                        class="inline-flex items-center justify-center w-12 h-12 rounded-xl font-bold text-sm <?= $urgencyClass ?>">
                                                        <?= $daysLeft ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    <div class="flex items-center justify-end gap-2">
                                                        <a href="<?= BASE_URL ?>contracts/view/<?= $contract['id'] ?>"
                                                            class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                                            title="View Details">
                                                            <span class="material-icons-outlined text-lg">visibility</span>
                                                        </a>
                                                        <a href="<?= BASE_URL ?>contracts/renew/<?= $contract['id'] ?>"
                                                            class="p-2 text-slate-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-all"
                                                            title="Renew Contract">
                                                            <span class="material-icons-outlined text-lg">autorenew</span>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Filter functionality
        document.getElementById('expiry-filter')?.addEventListener('change', function () {
            const days = this.value;
            window.location.href = '<?= BASE_URL ?>contracts/expiring/' + days;
        });
    </script>
</body>

</html>