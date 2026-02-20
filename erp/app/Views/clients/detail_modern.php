<?php
/**
 * Modern Client Detail View
 * Displays comprehensive client analytics, fleet overview, and crew management
 */
?>
<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $title ?? 'Client Detail' ?> - PT Indo Ocean ERP
    </title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1"
        rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary": "#137fec",
                        "background-light": "#f6f7f8",
                        "navy": "#0f172a",
                        "navy-light": "#1e293b",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                },
            },
        }
    </script>

    <style>
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .material-symbols-filled {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>

<body class="bg-background-light font-display text-slate-600 antialiased">

    <div class="flex h-screen w-full overflow-hidden">
        <!-- Include Modern Sidebar -->
        <?php require_once APPPATH . 'Views/partials/modern_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="ml-64 flex-1 flex flex-col h-full overflow-hidden relative">
            <!-- Mobile Header -->
            <div
                class="md:hidden h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 shrink-0">
                <div class="flex items-center gap-2 text-primary font-bold">
                    <span class="material-symbols-outlined">anchor</span>
                    Maritime ERP
                </div>
                <button class="p-2 text-slate-500">
                    <span class="material-symbols-outlined">menu</span>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-4 md:p-8 scroll-smooth">
                <div class="max-w-[1280px] mx-auto space-y-6">

                    <!-- Client Header -->
                    <div
                        class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div class="flex items-center gap-4 md:gap-6">
                            <!-- Back Button -->
                            <a href="<?= BASE_URL ?>clients"
                                class="flex items-center justify-center p-2 -ml-2 rounded-full text-slate-400 hover:text-navy hover:bg-slate-50 transition-colors"
                                title="Back to Client List">
                                <span class="material-symbols-outlined">arrow_back</span>
                            </a>

                            <!-- Client Avatar -->
                            <div class="relative group cursor-pointer">
                                <?php
                                $initials = '';
                                $nameParts = explode(' ', $client['name'] ?? 'Client');
                                foreach ($nameParts as $part) {
                                    if (!empty($part)) {
                                        $initials .= strtoupper(substr($part, 0, 1));
                                    }
                                }
                                $initials = substr($initials, 0, 2);
                                ?>
                                <div
                                    class="size-20 md:size-24 rounded-full bg-gradient-to-br from-amber-200 via-amber-400 to-amber-600 flex items-center justify-center text-white text-2xl md:text-3xl font-bold shadow-lg shadow-amber-200/50">
                                    <?= $initials ?>
                                </div>
                                <div
                                    class="absolute bottom-0 right-0 size-6 bg-green-500 border-4 border-white rounded-full">
                                </div>
                            </div>

                            <!-- Client Info -->
                            <div class="space-y-1">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <h2 class="text-2xl md:text-3xl font-bold text-navy">
                                        <?= htmlspecialchars($client['name'] ?? 'N/A') ?>
                                    </h2>
                                    <span
                                        class="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-600 text-xs font-semibold border border-blue-100">Premium
                                        Client</span>
                                </div>
                                <div class="flex items-center gap-4 text-slate-500 text-sm">
                                    <div class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[18px]">location_on</span>
                                        <?= htmlspecialchars($client['city'] ?? $client['country'] ?? 'N/A') ?>
                                    </div>
                                    <div class="hidden md:block w-1 h-1 bg-slate-300 rounded-full"></div>
                                    <div class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[18px]">badge</span>
                                        Client ID: #
                                        <?= $client['id'] ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-3 w-full md:w-auto">
                            <a href="<?= BASE_URL ?>clients/edit/<?= $client['id'] ?>"
                                class="flex-1 md:flex-none justify-center flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-lg text-sm font-medium transition-all shadow-sm">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                                Edit
                            </a>
                            <button
                                class="flex-1 md:flex-none justify-center flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-lg text-sm font-medium transition-all shadow-sm">
                                <span class="material-symbols-outlined text-[20px]">mail</span>
                                Message
                            </button>
                            <button
                                class="flex-1 md:flex-none justify-center flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-blue-600 text-white rounded-lg text-sm font-medium transition-all shadow-md shadow-blue-200">
                                <span class="material-symbols-outlined text-[20px]">add</span>
                                New Order
                            </button>
                        </div>
                    </div>

                    <!-- KPI Stats Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                        <!-- Total Vessels -->
                        <div
                            class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow group">
                            <div class="flex justify-between items-start mb-4">
                                <div
                                    class="p-2 bg-blue-50 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                    <span class="material-symbols-outlined">directions_boat</span>
                                </div>
                                <span
                                    class="flex items-center gap-1 text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full border border-emerald-100">
                                    <span class="material-symbols-outlined text-[14px]">trending_up</span> +2%
                                </span>
                            </div>
                            <p class="text-sm font-medium text-slate-500"><?= __('clients.total_vessels') ?></p>
                            <p class="text-2xl font-bold text-navy mt-1">
                                <?= count($vessels ?? []) ?>
                            </p>
                        </div>

                        <!-- Active Crew -->
                        <div
                            class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow group">
                            <div class="flex justify-between items-start mb-4">
                                <div
                                    class="p-2 bg-indigo-50 text-indigo-600 rounded-lg group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                    <span class="material-symbols-outlined">groups</span>
                                </div>
                                <span
                                    class="flex items-center gap-1 text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full border border-emerald-100">
                                    <span class="material-symbols-outlined text-[14px]">trending_up</span> +5%
                                </span>
                            </div>
                            <p class="text-sm font-medium text-slate-500"><?= __('clients.active_crew') ?></p>
                            <p class="text-2xl font-bold text-navy mt-1">
                                <?= $stats['active_crew'] ?? 0 ?>
                            </p>
                        </div>

                        <!-- Total Revenue -->
                        <div
                            class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow group">
                            <div class="flex justify-between items-start mb-4">
                                <div
                                    class="p-2 bg-amber-50 text-amber-600 rounded-lg group-hover:bg-amber-600 group-hover:text-white transition-colors">
                                    <span class="material-symbols-outlined">account_balance_wallet</span>
                                </div>
                                <span
                                    class="flex items-center gap-1 text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full border border-emerald-100">
                                    <span class="material-symbols-outlined text-[14px]">trending_up</span> +12%
                                </span>
                            </div>
                            <p class="text-sm font-medium text-slate-500"><?= __('clients.total_revenue') ?></p>
                            <p class="text-2xl font-bold text-navy mt-1">$
                                <?= number_format($profit['monthly_client_rate_usd'] ?? 0, 0) ?>
                            </p>
                        </div>

                        <!-- Total Profit -->
                        <div
                            class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow group">
                            <div class="flex justify-between items-start mb-4">
                                <div
                                    class="p-2 bg-emerald-50 text-emerald-600 rounded-lg group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                    <span class="material-symbols-outlined">monetization_on</span>
                                </div>
                                <span
                                    class="flex items-center gap-1 text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full border border-emerald-100">
                                    <span class="material-symbols-outlined text-[14px]">trending_up</span> +8%
                                </span>
                            </div>
                            <p class="text-sm font-medium text-slate-500"><?= __('clients.total_profit') ?></p>
                            <p class="text-2xl font-bold text-navy mt-1">$
                                <?= number_format($profit['monthly_profit_usd'] ?? 0, 0) ?>
                            </p>
                        </div>
                    </div>

                    <!-- Main Content Grid -->
                    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">

                        <!-- Monthly Cost Breakdown -->
                        <div
                            class="xl:col-span-1 bg-white rounded-xl border border-slate-100 shadow-sm p-6 flex flex-col h-full">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="font-bold text-navy text-lg"><?= __('clients.monthly_cost') ?></h3>
                                <button class="text-slate-400 hover:text-primary">
                                    <span class="material-symbols-outlined">more_horiz</span>
                                </button>
                            </div>

                            <div class="flex-1 flex flex-col justify-center">
                                <div class="mb-2">
                                    <p class="text-sm text-slate-500 mb-1"><?= __('clients.total_expenses') ?></p>
                                    <?php
                                    // Get total from monthlyCost breakdown
                                    $totalCostUSD = $monthlyCost['total_usd'] ?? 0;
                                    $totalCostIDR = $totalCostUSD * 15300; // Approximate IDR conversion
                                    ?>
                                    <p class="text-2xl font-bold text-navy tracking-tight">IDR
                                        <?= number_format($totalCostIDR, 0, ',', '.') ?>
                                    </p>
                                    <p class="text-sm font-medium text-slate-400">≈ $
                                        <?= number_format($totalCostUSD, 2) ?> USD
                                    </p>
                                </div>

                                <div class="mt-8 space-y-4">
                                    <?php
                                    // Calculate breakdown percentages based on actual crew salary data
                                    // For now, we'll show salary as the main component
                                    $salaryPercent = 100; // All cost is currently salary-based
                                    
                                    // Calculate estimated operational costs (rough estimate: 20% of salary)
                                    $operationsEstimate = $totalCostUSD * 0.20;
                                    $maintenanceEstimate = $totalCostUSD * 0.15;
                                    $wagesActual = $totalCostUSD;

                                    $totalEstimate = $operationsEstimate + $maintenanceEstimate + $wagesActual;

                                    $opsPercent = $totalEstimate > 0 ? round(($operationsEstimate / $totalEstimate) * 100) : 0;
                                    $maintPercent = $totalEstimate > 0 ? round(($maintenanceEstimate / $totalEstimate) * 100) : 0;
                                    $wagesPercent = $totalEstimate > 0 ? round(($wagesActual / $totalEstimate) * 100) : 0;
                                    ?>

                                    <div>
                                        <div class="flex justify-between text-xs font-medium mb-1.5">
                                            <span class="text-slate-600">Operations</span>
                                            <span class="text-primary">
                                                <?= $opsPercent ?>%
                                            </span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-2">
                                            <div class="bg-primary h-2 rounded-full" style="width: <?= $opsPercent ?>%">
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="flex justify-between text-xs font-medium mb-1.5">
                                            <span class="text-slate-600">Maintenance</span>
                                            <span class="text-amber-500">
                                                <?= $maintPercent ?>%
                                            </span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-2">
                                            <div class="bg-amber-500 h-2 rounded-full"
                                                style="width: <?= $maintPercent ?>%"></div>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="flex justify-between text-xs font-medium mb-1.5">
                                            <span class="text-slate-600">Crew Wages</span>
                                            <span class="text-emerald-500">
                                                <?= $wagesPercent ?>%
                                            </span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-2">
                                            <div class="bg-emerald-500 h-2 rounded-full"
                                                style="width: <?= $wagesPercent ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fleet Overview -->
                        <div class="xl:col-span-2 flex flex-col gap-4">
                            <div class="flex items-center justify-between">
                                <h3 class="font-bold text-navy text-lg"><?= __('vessels.title') ?></h3>
                                <a href="<?= BASE_URL ?>vessels"
                                    class="text-sm font-medium text-primary hover:text-blue-700 flex items-center">
                                    <?= __('common.view_all') ?> <span class="material-symbols-outlined text-sm ml-1">arrow_forward</span>
                                </a>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 h-full">
                                <?php if (!empty($vessels)): ?>
                                    <?php foreach (array_slice($vessels, 0, 2) as $vessel): ?>
                                        <div
                                            class="group bg-white rounded-xl border border-slate-100 shadow-sm p-4 hover:border-primary/30 transition-all cursor-pointer">
                                            <!-- Vessel Image -->
                                            <div
                                                class="h-32 rounded-lg bg-gradient-to-br from-slate-200 to-slate-300 w-full mb-4 relative overflow-hidden">
                                                <?php if (!empty($vessel['image_url'])): ?>
                                                    <?php
                                                    // Check if URL is already absolute (starts with http:// or https://)
                                                    $imageUrl = $vessel['image_url'];
                                                    if (!preg_match('/^https?:\/\//', $imageUrl)) {
                                                        // Relative path, prepend BASE_URL
                                                        $imageUrl = BASE_URL . $imageUrl;
                                                    }
                                                    ?>
                                                    <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($vessel['name']) ?>"
                                                        class="w-full h-full object-cover"
                                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div class="hidden w-full h-full items-center justify-center">
                                                        <span
                                                            class="material-symbols-outlined text-6xl text-slate-400">directions_boat</span>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <span
                                                            class="material-symbols-outlined text-6xl text-slate-400">directions_boat</span>
                                                    </div>
                                                <?php endif; ?>
                                                <div
                                                    class="absolute top-2 right-2 bg-white/90 backdrop-blur px-2 py-1 rounded text-xs font-bold text-navy shadow-sm">
                                                    <?= ucfirst($vessel['status'] ?? 'Active') ?>
                                                </div>
                                            </div>

                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <h4 class="font-bold text-navy group-hover:text-primary transition-colors">
                                                        <?= htmlspecialchars($vessel['name'] ?? 'N/A') ?>
                                                    </h4>
                                                    <p class="text-xs text-slate-500 mt-1">IMO:
                                                        <?= htmlspecialchars($vessel['imo_number'] ?? 'N/A') ?>
                                                    </p>
                                                </div>
                                                <div
                                                    class="flex items-center gap-1 bg-slate-50 px-2 py-1 rounded-md text-slate-600 text-xs font-medium border border-slate-100">
                                                    <span class="material-symbols-outlined text-sm">group</span>
                                                    <?= $vessel['crew_count'] ?? 0 ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-span-2 bg-slate-50 rounded-xl p-8 text-center">
                                        <span
                                            class="material-symbols-outlined text-5xl text-slate-300">directions_boat</span>
                                        <p class="text-slate-400 mt-2">No vessels assigned to this client</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Company Info -->
                        <div class="xl:col-span-1 bg-white rounded-xl border border-slate-100 shadow-sm p-6 h-full">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="font-bold text-navy text-lg"><?= __('clients.company_info') ?></h3>
                                <a href="<?= BASE_URL ?>clients/edit/<?= $client['id'] ?>"
                                    class="text-primary hover:bg-primary/5 p-1 rounded-md transition-colors">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </a>
                            </div>

                            <div class="space-y-5">
                                <div class="flex gap-3">
                                    <div
                                        class="size-8 rounded-lg bg-slate-50 flex items-center justify-center shrink-0 text-slate-500">
                                        <span class="material-symbols-outlined text-lg">mail</span>
                                    </div>
                                    <div class="overflow-hidden">
                                        <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">Email
                                            Address</p>
                                        <p class="text-sm font-medium text-navy truncate">
                                            <?= htmlspecialchars($client['email'] ?? 'N/A') ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex gap-3">
                                    <div
                                        class="size-8 rounded-lg bg-slate-50 flex items-center justify-center shrink-0 text-slate-500">
                                        <span class="material-symbols-outlined text-lg">call</span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">Phone
                                            Number</p>
                                        <p class="text-sm font-medium text-navy">
                                            <?= htmlspecialchars($client['phone'] ?? 'N/A') ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex gap-3">
                                    <div
                                        class="size-8 rounded-lg bg-slate-50 flex items-center justify-center shrink-0 text-slate-500">
                                        <span class="material-symbols-outlined text-lg">receipt_long</span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">Tax ID
                                            (NPWP)</p>
                                        <p class="text-sm font-medium text-navy">
                                            <?= htmlspecialchars($client['tax_id'] ?? 'N/A') ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex gap-3">
                                    <div
                                        class="size-8 rounded-lg bg-slate-50 flex items-center justify-center shrink-0 text-slate-500">
                                        <span class="material-symbols-outlined text-lg">domain</span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">Official
                                            Address</p>
                                        <p class="text-sm font-medium text-navy leading-relaxed">
                                            <?= htmlspecialchars($client['address'] ?? 'N/A') ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Crew Members Table -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                        <div
                            class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <h3 class="font-bold text-navy text-lg"><?= __('clients.crew_members') ?></h3>
                            <div class="flex p-1 bg-slate-50 rounded-lg border border-slate-200">
                                <button
                                    class="px-4 py-1.5 text-sm font-medium text-primary bg-white rounded-md shadow-sm transition-all">Active</button>
                                <button
                                    class="px-4 py-1.5 text-sm font-medium text-slate-500 hover:text-navy transition-all">Non-Active</button>
                                <button
                                    class="px-4 py-1.5 text-sm font-medium text-slate-500 hover:text-navy transition-all">All</button>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50/50 border-b border-slate-100">
                                        <th
                                            class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                            Crew Member</th>
                                        <th
                                            class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                            Rank</th>
                                        <th
                                            class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                            Assigned Vessel</th>
                                        <th
                                            class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                            Monthly Salary</th>
                                        <th
                                            class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider text-right">
                                            Est. Profit</th>
                                        <th
                                            class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    <?php if (!empty($contracts)): ?>
                                        <?php foreach ($contracts as $contract): ?>
                                            <?php if (in_array($contract['status'], ['active', 'onboard'])): ?>
                                                <tr class="hover:bg-slate-50/80 transition-colors group">
                                                    <td class="px-6 py-4">
                                                        <div class="flex items-center gap-3">
                                                            <?php
                                                            $crewInitials = '';
                                                            $crewNameParts = explode(' ', $contract['crew_name'] ?? 'N/A');
                                                            foreach ($crewNameParts as $part) {
                                                                if (!empty($part)) {
                                                                    $crewInitials .= strtoupper(substr($part, 0, 1));
                                                                }
                                                            }
                                                            $crewInitials = substr($crewInitials, 0, 2);
                                                            ?>
                                                            <div
                                                                class="size-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-sm font-bold">
                                                                <?= $crewInitials ?>
                                                            </div>
                                                            <div>
                                                                <p class="text-sm font-semibold text-navy">
                                                                    <?= htmlspecialchars($contract['crew_name'] ?? 'N/A') ?>
                                                                </p>
                                                                <p class="text-xs text-slate-400">ID: CR-
                                                                    <?= str_pad($contract['crew_id'] ?? '0', 4, '0', STR_PAD_LEFT) ?>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                                            <?= htmlspecialchars($contract['rank'] ?? 'N/A') ?>
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <div class="flex items-center gap-2">
                                                            <div class="size-2 bg-emerald-500 rounded-full"></div>
                                                            <span class="text-sm text-slate-600 font-medium">
                                                                <?= htmlspecialchars($contract['vessel_name'] ?? 'Unassigned') ?>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <div class="flex flex-col">
                                                            <?php
                                                            $salaryIDR = ($contract['salary_usd'] ?? 0) * 15300;
                                                            ?>
                                                            <span class="text-sm font-medium text-navy">IDR
                                                                <?= number_format($salaryIDR, 0, ',', '.') ?>
                                                            </span>
                                                            <span class="text-xs text-slate-400">≈ $
                                                                <?= number_format($contract['salary_usd'] ?? 0, 0) ?> USD
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 text-right">
                                                        <span class="text-sm font-bold text-emerald-600">+$
                                                            <?= number_format($contract['profit_usd'] ?? 0, 0) ?>
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 text-right">
                                                        <button class="text-slate-300 hover:text-primary transition-colors">
                                                            <span class="material-symbols-outlined">more_vert</span>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="px-6 py-12 text-center">
                                                <span
                                                    class="material-symbols-outlined text-5xl text-slate-200">groups</span>
                                                <p class="text-slate-400 mt-2">No crew members assigned to this client</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if (!empty($contracts)): ?>
                            <div class="px-6 py-4 border-t border-slate-50 flex items-center justify-between">
                                <p class="text-xs text-slate-400">Showing
                                    <?= count($contracts) ?> crew members
                                </p>
                                <div class="flex gap-2">
                                    <button class="p-1 text-slate-400 hover:text-navy hover:bg-slate-50 rounded">
                                        <span class="material-symbols-outlined text-lg">chevron_left</span>
                                    </button>
                                    <button class="p-1 text-slate-400 hover:text-navy hover:bg-slate-50 rounded">
                                        <span class="material-symbols-outlined text-lg">chevron_right</span>
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </main>
    </div>

</body>

</html>