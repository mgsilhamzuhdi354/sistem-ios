<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($vessel['name']) ?> | IndoOcean ERP</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#1e3a8a",
                        secondary: "#d4af37",
                        navy: "#0f172a",
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    
    <style>
        body { font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Modern Sidebar -->
        <?php 
        $currentPage = 'vessels';
        include APPPATH . 'Views/partials/modern_sidebar.php'; 
        ?>

        <!-- Main Content -->
        <main class="ml-64 flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">
            <!-- Header -->
            <header class="h-16 flex items-center justify-between px-8 z-10 bg-white/80 backdrop-blur-sm border-b border-slate-200/50 flex-shrink-0">
                <div class="flex items-center text-slate-500 text-sm">
                    <a href="<?= BASE_URL ?>dashboard" class="hover:text-primary transition-colors">
                        <span class="material-icons-round text-lg mr-1 text-slate-400">home</span>
                    </a>
                    <span class="mx-2 text-slate-300">/</span>
                    <a href="<?= BASE_URL ?>vessels" class="hover:text-primary transition-colors">Vessels</a>
                    <span class="mx-2 text-slate-300">/</span>
                    <span class="text-slate-800 font-medium"><?= htmlspecialchars($vessel['name']) ?></span>
                </div>
                <div class="flex items-center gap-3">
                    <a href="<?= BASE_URL ?>vessels/edit/<?= $vessel['id'] ?>" 
                       class="flex items-center gap-2 px-4 py-2 bg-primary hover:bg-blue-900 text-white rounded-xl text-sm font-semibold transition-all shadow-sm">
                        <span class="material-icons-round text-lg">edit</span>
                        Edit Vessel
                    </a>
                </div>
            </header>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto px-8 py-6 custom-scrollbar">
                <div class="max-w-6xl mx-auto space-y-6">

                    <!-- Vessel Header Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="flex flex-col md:flex-row">
                            <!-- Vessel Image -->
                            <div class="w-full md:w-72 h-48 md:h-auto bg-gradient-to-br from-slate-200 to-slate-300 relative flex-shrink-0">
                                <?php if (!empty($vessel['image_url'])): ?>
                                    <?php
                                    $imgUrl = $vessel['image_url'];
                                    if (!preg_match('/^https?:\/\//', $imgUrl)) {
                                        $imgUrl = BASE_URL . $imgUrl;
                                    }
                                    ?>
                                    <img src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($vessel['name']) ?>" 
                                         class="w-full h-full object-cover"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="hidden w-full h-full items-center justify-center">
                                        <span class="material-icons-round text-6xl text-slate-400">directions_boat</span>
                                    </div>
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center min-h-[200px]">
                                        <span class="material-icons-round text-6xl text-slate-400">directions_boat</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Vessel Info -->
                            <div class="flex-1 p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <div class="flex items-center gap-3 mb-1">
                                            <h1 class="text-2xl font-bold text-navy"><?= htmlspecialchars($vessel['name']) ?></h1>
                                            <?php 
                                            $statusColors = [
                                                'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'maintenance' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'laid_up' => 'bg-slate-100 text-slate-600 border-slate-200',
                                                'inactive' => 'bg-red-50 text-red-700 border-red-200',
                                            ];
                                            $sc = $statusColors[$vessel['status'] ?? 'active'] ?? $statusColors['active'];
                                            ?>
                                            <span class="px-3 py-1 rounded-full text-xs font-bold border <?= $sc ?>">
                                                <?= ucfirst(str_replace('_', ' ', $vessel['status'] ?? 'Active')) ?>
                                            </span>
                                        </div>
                                        <p class="text-sm text-slate-500">
                                            IMO: <?= htmlspecialchars($vessel['imo_number'] ?? 'N/A') ?> • <?= htmlspecialchars($vessel['type_name'] ?? $vessel['vessel_type_name'] ?? 'Unknown Type') ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4">
                                    <div class="bg-slate-50 rounded-xl p-3">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Vessel Type</p>
                                        <p class="text-sm font-semibold text-navy"><?= htmlspecialchars($vessel['type_name'] ?? $vessel['vessel_type_name'] ?? '-') ?></p>
                                    </div>
                                    <div class="bg-slate-50 rounded-xl p-3">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Flag State</p>
                                        <p class="text-sm font-semibold text-navy"><?= ($vessel['flag_emoji'] ?? '') . ' ' . htmlspecialchars($vessel['flag_name'] ?? $vessel['flag_state_name'] ?? '-') ?></p>
                                    </div>
                                    <div class="bg-slate-50 rounded-xl p-3">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Client</p>
                                        <p class="text-sm font-semibold text-navy"><?= htmlspecialchars($vessel['client_name'] ?? '-') ?></p>
                                    </div>
                                    <div class="bg-slate-50 rounded-xl p-3">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Year Built</p>
                                        <p class="text-sm font-semibold text-navy"><?= $vessel['year_built'] ?? '-' ?></p>
                                    </div>
                                    <div class="bg-slate-50 rounded-xl p-3">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Gross Tonnage</p>
                                        <p class="text-sm font-semibold text-navy"><?= !empty($vessel['gross_tonnage']) ? number_format($vessel['gross_tonnage']) . ' GT' : '-' ?></p>
                                    </div>
                                    <div class="bg-slate-50 rounded-xl p-3">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">DWT</p>
                                        <p class="text-sm font-semibold text-navy"><?= !empty($vessel['dwt']) ? number_format($vessel['dwt']) . ' T' : '-' ?></p>
                                    </div>
                                    <div class="bg-slate-50 rounded-xl p-3">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Call Sign</p>
                                        <p class="text-sm font-semibold text-navy"><?= htmlspecialchars($vessel['call_sign'] ?? '-') ?></p>
                                    </div>
                                    <div class="bg-slate-50 rounded-xl p-3">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Crew Capacity</p>
                                        <p class="text-sm font-semibold text-navy"><?= ($vessel['crew_capacity'] ?? 25) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Row -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Monthly Cost -->
                        <?php 
                        $costData = $totalCost ?? [];
                        $byCurrency = $costData['by_currency'] ?? [];
                        $symbols = $costData['symbols'] ?? [];
                        $totalUsd = $costData['total_usd'] ?? 0;
                        ?>
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="p-2.5 rounded-xl bg-amber-50 text-amber-600">
                                    <span class="material-icons-round">payments</span>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-medium">Monthly Crew Cost</p>
                                    <p class="text-xl font-bold text-navy">$<?= number_format($totalUsd, 2) ?></p>
                                </div>
                            </div>
                            <?php if (!empty($byCurrency)): ?>
                                <div class="space-y-1 mt-3 pt-3 border-t border-slate-100">
                                    <?php foreach ($byCurrency as $curr => $amt): ?>
                                        <div class="flex justify-between text-xs">
                                            <span class="text-slate-500"><?= $curr ?></span>
                                            <span class="font-semibold text-slate-700"><?= ($symbols[$curr] ?? '') . number_format($amt, 0) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Active Crew -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="p-2.5 rounded-xl bg-blue-50 text-blue-600">
                                    <span class="material-icons-round">groups</span>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-medium">Active Crew Members</p>
                                    <p class="text-xl font-bold text-navy"><?= count($crewList ?? []) ?></p>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-slate-100">
                                <div class="flex justify-between text-xs">
                                    <span class="text-slate-500">Capacity</span>
                                    <span class="font-semibold text-slate-700"><?= $vessel['crew_capacity'] ?? 25 ?></span>
                                </div>
                                <?php 
                                $crewCount = count($crewList ?? []);
                                $capacity = $vessel['crew_capacity'] ?? 25;
                                $pct = $capacity > 0 ? round(($crewCount / $capacity) * 100) : 0;
                                ?>
                                <div class="mt-2 w-full bg-slate-100 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full transition-all" style="width: <?= min($pct, 100) ?>%"></div>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1"><?= $pct ?>% filled</p>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-2.5 rounded-xl bg-emerald-50 text-emerald-600">
                                    <span class="material-icons-round">bolt</span>
                                </div>
                                <p class="text-xs text-slate-400 font-medium">Quick Actions</p>
                            </div>
                            <div class="space-y-2">
                                <a href="<?= BASE_URL ?>contracts/create?vessel_id=<?= $vessel['id'] ?>" 
                                   class="flex items-center gap-2 w-full px-3 py-2.5 rounded-xl bg-primary/5 text-primary hover:bg-primary/10 text-sm font-medium transition-colors">
                                    <span class="material-icons-round text-lg">person_add</span>
                                    Assign Crew
                                </a>
                                <a href="<?= BASE_URL ?>vessels/edit/<?= $vessel['id'] ?>" 
                                   class="flex items-center gap-2 w-full px-3 py-2.5 rounded-xl bg-amber-50 text-amber-700 hover:bg-amber-100 text-sm font-medium transition-colors">
                                    <span class="material-icons-round text-lg">edit</span>
                                    Edit Vessel
                                </a>
                                <a href="<?= BASE_URL ?>vessels" 
                                   class="flex items-center gap-2 w-full px-3 py-2.5 rounded-xl bg-slate-50 text-slate-600 hover:bg-slate-100 text-sm font-medium transition-colors">
                                    <span class="material-icons-round text-lg">arrow_back</span>
                                    Back to List
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Crew List -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-blue-50 rounded-lg text-primary">
                                    <span class="material-icons-round text-xl">groups</span>
                                </div>
                                <h2 class="text-lg font-bold text-slate-800">Crew List</h2>
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded-full text-xs font-bold"><?= count($crewList ?? []) ?></span>
                            </div>
                            <a href="<?= BASE_URL ?>contracts/create?vessel_id=<?= $vessel['id'] ?>" 
                               class="flex items-center gap-1.5 px-4 py-2 bg-primary hover:bg-blue-900 text-white rounded-xl text-sm font-semibold transition-all shadow-sm">
                                <span class="material-icons-round text-lg">add</span>
                                Assign Crew
                            </a>
                        </div>

                        <?php if (empty($crewList)): ?>
                            <div class="px-6 py-16 text-center">
                                <span class="material-icons-round text-6xl text-slate-200">groups</span>
                                <p class="text-slate-400 mt-3 text-sm">No crew assigned to this vessel</p>
                                <a href="<?= BASE_URL ?>contracts/create?vessel_id=<?= $vessel['id'] ?>" 
                                   class="inline-flex items-center gap-1.5 mt-4 px-5 py-2.5 bg-primary text-white rounded-xl text-sm font-semibold hover:bg-blue-900 transition-all">
                                    <span class="material-icons-round text-lg">person_add</span>
                                    Assign First Crew Member
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="bg-slate-50/80 border-b border-slate-100">
                                            <th class="px-5 py-3 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">Rank</th>
                                            <th class="px-5 py-3 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">Name</th>
                                            <th class="px-5 py-3 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">Contract No</th>
                                            <th class="px-5 py-3 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sign Off Date</th>
                                            <th class="px-5 py-3 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">Days Remaining</th>
                                            <th class="px-5 py-3 text-right text-[11px] font-bold text-slate-400 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50">
                                        <?php foreach ($crewList as $crew): ?>
                                            <?php 
                                            $d = $crew['days_remaining'] ?? null;
                                            $dClass = $d !== null 
                                                ? ($d <= 7 ? 'bg-red-50 text-red-600' : ($d <= 30 ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600'))
                                                : 'bg-slate-50 text-slate-400';
                                            ?>
                                            <tr class="hover:bg-blue-50/30 transition-colors">
                                                <td class="px-5 py-3.5">
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-primary/5 text-primary text-xs font-semibold">
                                                        <?= htmlspecialchars($crew['rank_name'] ?? '-') ?>
                                                    </span>
                                                </td>
                                                <td class="px-5 py-3.5">
                                                    <span class="text-sm font-semibold text-navy"><?= htmlspecialchars($crew['crew_name']) ?></span>
                                                </td>
                                                <td class="px-5 py-3.5">
                                                    <a href="<?= BASE_URL ?>contracts/<?= $crew['id'] ?>" class="text-sm text-primary hover:text-blue-900 font-medium transition-colors">
                                                        <?= htmlspecialchars($crew['contract_no'] ?? '-') ?>
                                                    </a>
                                                </td>
                                                <td class="px-5 py-3.5">
                                                    <span class="text-sm text-slate-600"><?= !empty($crew['sign_off_date']) ? date('d M Y', strtotime($crew['sign_off_date'])) : '-' ?></span>
                                                </td>
                                                <td class="px-5 py-3.5">
                                                    <?php if ($d !== null): ?>
                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold <?= $dClass ?>">
                                                            <?= $d >= 0 ? $d . ' days' : 'Expired' ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-sm text-slate-400">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="px-5 py-3.5 text-right">
                                                    <a href="<?= BASE_URL ?>contracts/<?= $crew['id'] ?>" 
                                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-medium transition-colors">
                                                        <span class="material-icons-round text-sm">visibility</span>
                                                        View
                                                    </a>
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
</body>
</html>
