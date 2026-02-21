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

    <!-- Alpine.js (required for sidebar collapsible menus) -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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

        /* Book-flip modal animation */
        @keyframes bookOpen {
            0% { transform: perspective(1200px) rotateY(-90deg) scale(0.8); opacity: 0; }
            40% { transform: perspective(1200px) rotateY(10deg) scale(1.02); opacity: 1; }
            70% { transform: perspective(1200px) rotateY(-5deg) scale(1); }
            100% { transform: perspective(1200px) rotateY(0deg) scale(1); opacity: 1; }
        }
        @keyframes bookClose {
            0% { transform: perspective(1200px) rotateY(0deg) scale(1); opacity: 1; }
            100% { transform: perspective(1200px) rotateY(-90deg) scale(0.8); opacity: 0; }
        }
        .book-open-enter {
            animation: bookOpen 0.5s cubic-bezier(0.22, 1, 0.36, 1) forwards;
            transform-origin: left center;
        }
        .book-open-leave {
            animation: bookClose 0.3s cubic-bezier(0.55, 0, 1, 0.45) forwards;
            transform-origin: left center;
        }
        @keyframes fadeInBackdrop {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .backdrop-fade-in {
            animation: fadeInBackdrop 0.3s ease forwards;
        }
    </style>
</head>

<body class="bg-background-light font-display text-slate-600 antialiased" x-data="{ editClientModal: false }">

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
                            <button @click="editClientModal = true"
                                class="flex-1 md:flex-none justify-center flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-lg text-sm font-medium transition-all shadow-sm">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                                Edit
                            </button>
                            <button
                                class="flex-1 md:flex-none justify-center flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-lg text-sm font-medium transition-all shadow-sm">
                                <span class="material-symbols-outlined text-[20px]">mail</span>
                                Message
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

                        <!-- Biaya Bulanan (Data Real dari Gaji Kru) -->
                        <div
                            class="xl:col-span-1 bg-white rounded-xl border border-slate-100 shadow-sm p-6 flex flex-col h-full">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="font-bold text-navy text-lg">Biaya Bulanan</h3>
                                <span class="text-[10px] font-medium text-slate-400 bg-slate-50 px-2 py-1 rounded">Data Real</span>
                            </div>

                            <div class="flex-1 flex flex-col justify-center">
                                <div class="mb-2">
                                    <p class="text-sm text-slate-500 mb-1">Total Pengeluaran Gaji Kru</p>
                                    <?php
                                    $totalCostUSD = $monthlyCost['total_usd'] ?? 0;
                                    ?>
                                    <p class="text-2xl font-bold text-navy tracking-tight">$<?= number_format($totalCostUSD, 2) ?></p>
                                    <p class="text-xs font-medium text-slate-400 mt-1">Total gaji bulanan semua kru aktif</p>
                                </div>

                                <!-- Breakdown per Currency (data real) -->
                                <div class="mt-6 space-y-3">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Rincian Per Mata Uang</p>
                                    <?php
                                    $byCurrency = $monthlyCost['by_currency'] ?? [];
                                    $symbols = $monthlyCost['symbols'] ?? [];
                                    if (!empty($byCurrency)):
                                        $maxAmount = max($byCurrency);
                                        foreach ($byCurrency as $currCode => $amount):
                                            $pct = $maxAmount > 0 ? round(($amount / $maxAmount) * 100) : 0;
                                            $sym = $symbols[$currCode] ?? $currCode;
                                    ?>
                                        <div>
                                            <div class="flex justify-between text-xs font-medium mb-1.5">
                                                <span class="text-slate-600"><?= $currCode ?></span>
                                                <span class="text-navy font-semibold"><?= $sym ?> <?= number_format($amount, 0, ',', '.') ?></span>
                                            </div>
                                            <div class="w-full bg-slate-100 rounded-full h-2">
                                                <div class="bg-primary h-2 rounded-full transition-all" style="width: <?= $pct ?>%"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-sm text-slate-400 italic">Belum ada data gaji</p>
                                    <?php endif; ?>
                                </div>

                                <!-- USD Equivalent -->
                                <div class="mt-4 pt-4 border-t border-slate-100">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-slate-400">Setara USD</span>
                                        <span class="text-sm font-bold text-primary">$ <?= number_format($totalCostUSD, 2) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fleet & Profit Section (Tabbed) -->
                        <div class="xl:col-span-2 flex flex-col gap-4" x-data="{ activeTab: 'fleet', vesselModal: false, selectedVessel: null, vesselDetailModal: false }">
                            <!-- Tab Headers -->
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <div class="flex items-center gap-1 p-1 bg-slate-100 rounded-lg">
                                    <button @click="activeTab = 'fleet'"
                                        :class="activeTab === 'fleet' ? 'bg-white text-navy shadow-sm font-semibold' : 'text-slate-500 hover:text-navy'"
                                        class="px-4 py-2 rounded-md text-sm transition-all">
                                        <span class="flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-[16px]">directions_boat</span>
                                            Manajemen Kapal
                                        </span>
                                    </button>
                                    <button @click="activeTab = 'profit'"
                                        :class="activeTab === 'profit' ? 'bg-white text-navy shadow-sm font-semibold' : 'text-slate-500 hover:text-navy'"
                                        class="px-4 py-2 rounded-md text-sm transition-all">
                                        <span class="flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-[16px]">analytics</span>
                                            Profit Per Kapal
                                        </span>
                                    </button>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="<?= BASE_URL ?>vessels/create?client_id=<?= $client['id'] ?>"
                                        class="flex items-center gap-1.5 bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm hover:shadow-md transition-all active:scale-95">
                                        <span class="material-symbols-outlined text-[16px]">add</span>
                                        Tambah Kapal
                                    </a>
                                    <a href="<?= BASE_URL ?>vessels"
                                        class="text-sm font-medium text-primary hover:text-blue-700 flex items-center">
                                        Lihat Semua <span class="material-symbols-outlined text-sm ml-1">arrow_forward</span>
                                    </a>
                                </div>
                            </div>

                            <!-- Tab 1: Manajemen Kapal (Daftar Kapal Lengkap) -->
                            <div x-show="activeTab === 'fleet'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <?php if (!empty($vessels)): ?>
                                        <?php foreach ($vessels as $vIdx => $vessel): ?>
                                            <div @click="selectedVessel = <?= $vIdx ?>; vesselModal = true" class="group bg-white rounded-xl border border-slate-100 shadow-sm p-4 hover:border-primary/30 hover:shadow-md transition-all cursor-pointer">
                                                <!-- Foto Kapal -->
                                                <div class="h-32 rounded-lg bg-gradient-to-br from-slate-200 to-slate-300 w-full mb-4 relative overflow-hidden">
                                                    <?php if (!empty($vessel['image_url'])): ?>
                                                        <?php
                                                        $imageUrl = $vessel['image_url'];
                                                        if (!preg_match('/^https?:\/\//', $imageUrl)) {
                                                            $imageUrl = BASE_URL . $imageUrl;
                                                        }
                                                        ?>
                                                        <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($vessel['name']) ?>"
                                                            class="w-full h-full object-cover"
                                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <div class="hidden w-full h-full items-center justify-center">
                                                            <span class="material-symbols-outlined text-5xl text-slate-400">directions_boat</span>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="w-full h-full flex items-center justify-center">
                                                            <span class="material-symbols-outlined text-5xl text-slate-400">directions_boat</span>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="absolute top-2 right-2 bg-white/90 backdrop-blur px-2 py-1 rounded text-xs font-bold text-navy shadow-sm">
                                                        <?= ucfirst($vessel['status'] ?? 'Active') ?>
                                                    </div>
                                                </div>

                                                <div class="flex justify-between items-start">
                                                    <div>
                                                        <h4 class="font-bold text-navy group-hover:text-primary transition-colors">
                                                            <?= htmlspecialchars($vessel['name'] ?? 'N/A') ?>
                                                        </h4>
                                                        <p class="text-xs text-slate-500 mt-1">
                                                            <?= htmlspecialchars($vessel['vessel_type_name'] ?? '') ?> • IMO: <?= htmlspecialchars($vessel['imo_number'] ?? 'N/A') ?>
                                                        </p>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <a href="<?= BASE_URL ?>vessels/edit/<?= $vessel['id'] ?>" @click.stop
                                                            class="flex items-center gap-1 bg-slate-50 hover:bg-primary hover:text-white px-2 py-1 rounded-md text-slate-500 text-xs font-medium border border-slate-100 transition-all">
                                                            <span class="material-symbols-outlined text-[14px]">edit</span>
                                                        </a>
                                                        <div class="flex items-center gap-1 bg-slate-50 px-2 py-1 rounded-md text-slate-600 text-xs font-medium border border-slate-100">
                                                            <span class="material-symbols-outlined text-sm">group</span>
                                                            <?= $vessel['crew_count'] ?? 0 ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="col-span-2 bg-slate-50 rounded-xl p-8 text-center">
                                            <span class="material-symbols-outlined text-5xl text-slate-300">directions_boat</span>
                                            <p class="text-slate-400 mt-2">No vessels assigned to this client</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Vessel Detail Modal (Book-Flip Animation) -->
                            <template x-if="vesselModal">
                                <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4" @click.self="vesselModal = false" @keydown.escape.window="vesselModal = false">
                                    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm backdrop-fade-in" @click="vesselModal = false"></div>
                                    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto book-open-enter z-10">
                                        <?php foreach ($vessels as $mIdx => $mv): ?>
                                        <div x-show="selectedVessel === <?= $mIdx ?>">
                                            <!-- Foto Kapal -->
                                            <div class="h-44 bg-gradient-to-br from-slate-200 to-slate-300 relative overflow-hidden">
                                                <?php if (!empty($mv['image_url'])): ?>
                                                    <?php
                                                    $mImgUrl = $mv['image_url'];
                                                    if (!preg_match('/^https?:\/\//', $mImgUrl)) {
                                                        $mImgUrl = BASE_URL . $mImgUrl;
                                                    }
                                                    ?>
                                                    <img src="<?= $mImgUrl ?>" alt="<?= htmlspecialchars($mv['name']) ?>" class="w-full h-full object-cover">
                                                <?php else: ?>
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <span class="material-symbols-outlined text-6xl text-slate-400">directions_boat</span>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="absolute top-3 right-3 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-navy shadow">
                                                    <?= ucfirst($mv['status'] ?? 'Active') ?>
                                                </div>
                                                <button @click="vesselModal = false" class="absolute top-3 left-3 bg-black/30 hover:bg-black/50 text-white rounded-full p-1.5 transition-all">
                                                    <span class="material-symbols-outlined text-lg">close</span>
                                                </button>
                                            </div>

                                            <!-- Info Kapal + Tombol -->
                                            <div class="p-5 border-b border-slate-100">
                                                <div class="flex items-start justify-between mb-3">
                                                    <div>
                                                        <h3 class="text-lg font-bold text-navy"><?= htmlspecialchars($mv['name']) ?></h3>
                                                        <p class="text-sm text-slate-500"><?= htmlspecialchars($mv['vessel_type_name'] ?? '-') ?> • IMO: <?= htmlspecialchars($mv['imo_number'] ?? 'N/A') ?></p>
                                                    </div>
                                                    <div class="flex gap-2">
                                                        <button onclick="alert('Edit Kapal: <?= htmlspecialchars($mv['name']) ?>\nFitur edit kapal dalam pengembangan.\nSilakan gunakan halaman Daftar Kapal untuk mengedit.')"
                                                            class="flex items-center gap-1.5 bg-primary hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-xs font-bold transition-all active:scale-95 shadow-sm">
                                                            <span class="material-symbols-outlined text-[16px]">edit</span>
                                                            Edit
                                                        </button>
                                                        <button @click="vesselDetailModal = true; vesselModal = false"
                                                            class="flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-navy px-3 py-2 rounded-lg text-xs font-bold transition-all active:scale-95">
                                                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                                                            Lihat Detail
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-4 gap-2">
                                                    <div class="bg-slate-50 rounded-lg p-2 text-center">
                                                        <p class="text-[9px] font-bold text-slate-400 uppercase">Kru</p>
                                                        <p class="text-sm font-bold text-navy"><?= $mv['crew_count'] ?? 0 ?></p>
                                                    </div>
                                                    <div class="bg-slate-50 rounded-lg p-2 text-center">
                                                        <p class="text-[9px] font-bold text-slate-400 uppercase">IMO</p>
                                                        <p class="text-sm font-bold text-navy"><?= htmlspecialchars($mv['imo_number'] ?? 'N/A') ?></p>
                                                    </div>
                                                    <div class="bg-slate-50 rounded-lg p-2 text-center">
                                                        <p class="text-[9px] font-bold text-slate-400 uppercase">Bendera</p>
                                                        <p class="text-sm font-bold text-navy"><?= $mv['flag_emoji'] ?? '' ?></p>
                                                    </div>
                                                    <div class="bg-slate-50 rounded-lg p-2 text-center">
                                                        <p class="text-[9px] font-bold text-slate-400 uppercase">Status</p>
                                                        <p class="text-sm font-bold <?= ($mv['status'] ?? 'active') === 'active' ? 'text-emerald-600' : 'text-amber-600' ?>"><?= ucfirst($mv['status'] ?? 'Active') ?></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Tabel Anggota Kru Kapal Ini -->
                                            <div class="p-5">
                                                <h4 class="text-sm font-bold text-navy mb-3 flex items-center gap-1.5">
                                                    <span class="material-symbols-outlined text-[18px] text-primary">groups</span>
                                                    Anggota Kru — <?= htmlspecialchars($mv['name']) ?>
                                                </h4>
                                                <div class="overflow-x-auto rounded-lg border border-slate-100">
                                                    <table class="w-full text-left border-collapse">
                                                        <thead>
                                                            <tr class="bg-slate-50/80 border-b border-slate-100">
                                                                <th class="px-3 py-2.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Anggota Kru</th>
                                                                <th class="px-3 py-2.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jabatan</th>
                                                                <th class="px-3 py-2.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Gaji Bulanan</th>
                                                                <th class="px-3 py-2.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-right">Est. Profit</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-slate-50">
                                                            <?php
                                                            $vesselCrewFound = false;
                                                            foreach ($contracts as $mc):
                                                                if (($mc['vessel_id'] ?? null) == $mv['id'] && in_array($mc['status'], ['active', 'onboard'])):
                                                                    $vesselCrewFound = true;
                                                            ?>
                                                                <tr class="hover:bg-blue-50/30 transition-colors">
                                                                    <td class="px-3 py-2.5">
                                                                        <div class="flex items-center gap-2">
                                                                            <?php
                                                                            $mci = '';
                                                                            $mcParts = explode(' ', $mc['crew_name'] ?? 'N/A');
                                                                            foreach ($mcParts as $mcp) { if (!empty($mcp)) $mci .= strtoupper(substr($mcp, 0, 1)); }
                                                                            $mci = substr($mci, 0, 2);
                                                                            ?>
                                                                            <div class="size-7 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-[10px] font-bold"><?= $mci ?></div>
                                                                            <span class="text-xs font-semibold text-navy"><?= htmlspecialchars($mc['crew_name'] ?? 'N/A') ?></span>
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-3 py-2.5">
                                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-50 text-blue-700"><?= htmlspecialchars($mc['rank'] ?? $mc['rank_name'] ?? 'N/A') ?></span>
                                                                    </td>
                                                                    <td class="px-3 py-2.5">
                                                                        <span class="text-xs font-medium text-navy">$<?= number_format($mc['salary_usd'] ?? 0, 0) ?></span>
                                                                    </td>
                                                                    <td class="px-3 py-2.5 text-right">
                                                                        <?php if (($mc['profit_usd'] ?? 0) > 0): ?>
                                                                            <span class="text-xs font-bold text-emerald-600">+$<?= number_format($mc['profit_usd'] ?? 0, 0) ?></span>
                                                                        <?php elseif (($mc['profit_usd'] ?? 0) < 0): ?>
                                                                            <span class="text-xs font-bold text-rose-600">-$<?= number_format(abs($mc['profit_usd'] ?? 0), 0) ?></span>
                                                                        <?php else: ?>
                                                                            <span class="text-xs text-slate-400">-</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                </tr>
                                                            <?php
                                                                endif;
                                                            endforeach;
                                                            if (!$vesselCrewFound):
                                                            ?>
                                                                <tr>
                                                                    <td colspan="4" class="px-3 py-6 text-center">
                                                                        <span class="material-symbols-outlined text-3xl text-slate-200">groups</span>
                                                                        <p class="text-xs text-slate-400 mt-1">Belum ada kru aktif di kapal ini</p>
                                                                    </td>
                                                                </tr>
                                                            <?php endif; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </template>

                            <!-- Vessel Detail Comprehensive Modal (Book-Flip Animation) -->
                            <template x-if="vesselDetailModal">
                                <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4" @click.self="vesselDetailModal = false" @keydown.escape.window="vesselDetailModal = false">
                                    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm backdrop-fade-in" @click="vesselDetailModal = false"></div>
                                    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto book-open-enter z-10">
                                        <?php foreach ($vessels as $dIdx => $dv): ?>
                                        <div x-show="selectedVessel === <?= $dIdx ?>">
                                            <!-- Header -->
                                            <div class="bg-gradient-to-r from-navy to-navy-light p-5 rounded-t-2xl relative">
                                                <button @click="vesselDetailModal = false" class="absolute top-4 right-4 bg-white/20 hover:bg-white/30 text-white rounded-full p-1.5 transition-all">
                                                    <span class="material-symbols-outlined text-lg">close</span>
                                                </button>
                                                <div class="flex items-center gap-4">
                                                    <div class="size-14 rounded-xl bg-white/10 flex items-center justify-center">
                                                        <span class="material-symbols-outlined text-3xl text-white">directions_boat</span>
                                                    </div>
                                                    <div>
                                                        <h3 class="text-xl font-bold text-white"><?= htmlspecialchars($dv['name']) ?></h3>
                                                        <p class="text-sm text-blue-200">IMO: <?= htmlspecialchars($dv['imo_number'] ?? 'N/A') ?> | <?= htmlspecialchars($dv['vessel_type_name'] ?? 'Unknown Type') ?></p>
                                                    </div>
                                                    <div class="ml-auto">
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold <?= ($dv['status'] ?? 'active') === 'active' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300' ?>">
                                                            <?= ucfirst($dv['status'] ?? 'Active') ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Vessel Information Section -->
                                            <div class="p-5 border-b border-slate-100">
                                                <h4 class="text-sm font-bold text-navy mb-3 flex items-center gap-1.5">
                                                    <span class="material-symbols-outlined text-[18px] text-primary">info</span>
                                                    Informasi Kapal
                                                </h4>
                                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                                    <div class="bg-slate-50 rounded-lg p-3">
                                                        <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Nama Kapal</p>
                                                        <p class="text-sm font-semibold text-navy"><?= htmlspecialchars($dv['name']) ?></p>
                                                    </div>
                                                    <div class="bg-slate-50 rounded-lg p-3">
                                                        <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Nomor IMO</p>
                                                        <p class="text-sm font-semibold text-navy"><?= htmlspecialchars($dv['imo_number'] ?? 'N/A') ?></p>
                                                    </div>
                                                    <div class="bg-slate-50 rounded-lg p-3">
                                                        <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Tipe Kapal</p>
                                                        <p class="text-sm font-semibold text-navy"><?= htmlspecialchars($dv['vessel_type_name'] ?? '-') ?></p>
                                                    </div>
                                                    <div class="bg-slate-50 rounded-lg p-3">
                                                        <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Bendera</p>
                                                        <p class="text-sm font-semibold text-navy"><?= ($dv['flag_emoji'] ?? '') ?> <?= htmlspecialchars($dv['flag_state'] ?? '-') ?></p>
                                                    </div>
                                                    <div class="bg-slate-50 rounded-lg p-3">
                                                        <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Klien</p>
                                                        <p class="text-sm font-semibold text-navy"><?= htmlspecialchars($client['name'] ?? '-') ?></p>
                                                    </div>
                                                    <div class="bg-slate-50 rounded-lg p-3">
                                                        <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Tahun Dibangun</p>
                                                        <p class="text-sm font-semibold text-navy"><?= $dv['year_built'] ?? '-' ?></p>
                                                    </div>
                                                    <div class="bg-slate-50 rounded-lg p-3">
                                                        <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Gross Tonnage</p>
                                                        <p class="text-sm font-semibold text-navy"><?= !empty($dv['gross_tonnage']) ? number_format($dv['gross_tonnage'], 0, ',', '.') . ' GT' : '-' ?></p>
                                                    </div>
                                                    <div class="bg-slate-50 rounded-lg p-3">
                                                        <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Kapasitas Kru</p>
                                                        <p class="text-sm font-semibold text-navy"><?= $dv['crew_capacity'] ?? '-' ?></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Cost Summary Section -->
                                            <?php
                                            $dvCostIDR = 0; $dvCostUSD = 0; $dvActiveCrew = 0;
                                            foreach ($contracts as $dc) {
                                                if (($dc['vessel_id'] ?? null) == $dv['id'] && in_array($dc['status'], ['active', 'onboard'])) {
                                                    $dvActiveCrew++;
                                                    $dcCurr = $dc['currency_code'] ?? 'USD';
                                                    $dcAmt = $dc['total_monthly'] ?? 0;
                                                    if ($dcCurr === 'IDR') {
                                                        $dvCostIDR += $dcAmt;
                                                    } else {
                                                        $dvCostUSD += ($dc['salary_usd'] ?? 0);
                                                    }
                                                }
                                            }
                                            ?>
                                            <div class="p-5 border-b border-slate-100">
                                                <h4 class="text-sm font-bold text-navy mb-3 flex items-center gap-1.5">
                                                    <span class="material-symbols-outlined text-[18px] text-primary">payments</span>
                                                    Ringkasan Biaya
                                                </h4>
                                                <div class="grid grid-cols-3 gap-3">
                                                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 text-center">
                                                        <p class="text-lg font-bold text-navy">Rp <?= number_format($dvCostIDR, 0, ',', '.') ?></p>
                                                        <p class="text-[10px] font-bold text-blue-500 uppercase mt-1">Biaya Bulanan (IDR)</p>
                                                    </div>
                                                    <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl p-4 text-center">
                                                        <p class="text-lg font-bold text-navy">$<?= number_format($dvCostUSD, 0) ?></p>
                                                        <p class="text-[10px] font-bold text-emerald-500 uppercase mt-1">Biaya Bulanan (USD)</p>
                                                    </div>
                                                    <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl p-4 text-center">
                                                        <p class="text-lg font-bold text-navy"><?= $dvActiveCrew ?></p>
                                                        <p class="text-[10px] font-bold text-amber-500 uppercase mt-1">Kru Aktif</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Crew List Section -->
                                            <div class="p-5">
                                                <h4 class="text-sm font-bold text-navy mb-3 flex items-center gap-1.5">
                                                    <span class="material-symbols-outlined text-[18px] text-primary">groups</span>
                                                    Daftar Kru
                                                </h4>
                                                <div class="overflow-x-auto rounded-lg border border-slate-100">
                                                    <table class="w-full text-left border-collapse">
                                                        <thead>
                                                            <tr class="bg-slate-50/80 border-b border-slate-100">
                                                                <th class="px-3 py-2.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jabatan</th>
                                                                <th class="px-3 py-2.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama</th>
                                                                <th class="px-3 py-2.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">No. Kontrak</th>
                                                                <th class="px-3 py-2.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sign Off</th>
                                                                <th class="px-3 py-2.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-right">Sisa Hari</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-slate-50">
                                                            <?php
                                                            $dvCrewFound = false;
                                                            foreach ($contracts as $dc):
                                                                if (($dc['vessel_id'] ?? null) == $dv['id'] && in_array($dc['status'], ['active', 'onboard'])):
                                                                    $dvCrewFound = true;
                                                                    $daysRemaining = $dc['days_remaining'] ?? null;
                                                                    $contractNo = !empty($dc['contract_number']) ? $dc['contract_number'] : 'CTR-' . date('Y', strtotime($dc['sign_on_date'] ?? 'now')) . '-' . str_pad($dc['id'] ?? '0', 4, '0', STR_PAD_LEFT);
                                                                    $signOff = !empty($dc['sign_off_date']) ? date('d M Y', strtotime($dc['sign_off_date'])) : '-';
                                                            ?>
                                                                <tr class="hover:bg-blue-50/30 transition-colors">
                                                                    <td class="px-3 py-2.5">
                                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-50 text-blue-700"><?= htmlspecialchars($dc['rank'] ?? $dc['rank_name'] ?? 'N/A') ?></span>
                                                                    </td>
                                                                    <td class="px-3 py-2.5">
                                                                        <span class="text-xs font-semibold text-navy"><?= htmlspecialchars($dc['crew_name'] ?? 'N/A') ?></span>
                                                                    </td>
                                                                    <td class="px-3 py-2.5">
                                                                        <span class="text-xs text-slate-600 font-mono"><?= htmlspecialchars($contractNo) ?></span>
                                                                    </td>
                                                                    <td class="px-3 py-2.5">
                                                                        <span class="text-xs text-slate-600"><?= $signOff ?></span>
                                                                    </td>
                                                                    <td class="px-3 py-2.5 text-right">
                                                                        <?php if ($daysRemaining !== null && $daysRemaining >= 0): ?>
                                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold <?= $daysRemaining <= 30 ? 'bg-rose-50 text-rose-600' : ($daysRemaining <= 90 ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600') ?>">
                                                                                <?= $daysRemaining ?> hari
                                                                            </span>
                                                                        <?php elseif ($daysRemaining !== null): ?>
                                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600">Expired</span>
                                                                        <?php else: ?>
                                                                            <span class="text-xs text-slate-400">-</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                </tr>
                                                            <?php
                                                                endif;
                                                            endforeach;
                                                            if (!$dvCrewFound):
                                                            ?>
                                                                <tr>
                                                                    <td colspan="5" class="px-3 py-6 text-center">
                                                                        <span class="material-symbols-outlined text-3xl text-slate-200">groups</span>
                                                                        <p class="text-xs text-slate-400 mt-1">Belum ada kru aktif di kapal ini</p>
                                                                    </td>
                                                                </tr>
                                                            <?php endif; ?>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <!-- Tombol Kembali -->
                                                <div class="flex justify-center mt-4">
                                                    <button @click="vesselDetailModal = false; vesselModal = true"
                                                        class="flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-navy px-5 py-2 rounded-lg text-sm font-bold transition-all active:scale-95">
                                                        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                                                        Kembali
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </template>

                            <!-- Tab 2: Profit Per Vessel -->
                            <div x-show="activeTab === 'profit'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                                <?php
                                // Calculate totals from vesselProfitData
                                $vpTotalRevenue = 0;
                                $vpTotalCost = 0;
                                $vpProfitableCount = 0;
                                foreach ($vesselProfitData as $vp) {
                                    $vpTotalRevenue += $vp['revenue_usd'];
                                    $vpTotalCost += $vp['cost_usd'];
                                    if ($vp['is_profitable']) $vpProfitableCount++;
                                }
                                $vpTotalProfit = $vpTotalRevenue - $vpTotalCost;
                                $vpAvgMargin = $vpTotalRevenue > 0 ? ($vpTotalProfit / $vpTotalRevenue) * 100 : 0;
                                ?>

                                <!-- Profit KPI Mini Cards -->
                                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                                    <div class="bg-white rounded-xl border border-slate-100 p-4 shadow-sm">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Revenue</p>
                                        <p class="text-lg font-bold text-navy">$<?= number_format($vpTotalRevenue, 0) ?></p>
                                    </div>
                                    <div class="bg-white rounded-xl border border-slate-100 p-4 shadow-sm">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cost</p>
                                        <p class="text-lg font-bold text-navy">$<?= number_format($vpTotalCost, 0) ?></p>
                                    </div>
                                    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-4 shadow-lg shadow-blue-200/50">
                                        <p class="text-[10px] font-bold text-blue-200 uppercase tracking-wider mb-1">Net Profit</p>
                                        <p class="text-lg font-bold text-white">$<?= number_format($vpTotalProfit, 0) ?></p>
                                    </div>
                                    <div class="bg-white rounded-xl border border-slate-100 p-4 shadow-sm">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Avg Margin</p>
                                        <p class="text-lg font-bold <?= $vpAvgMargin > 0 ? 'text-emerald-600' : 'text-rose-600' ?>"><?= number_format($vpAvgMargin, 1) ?>%</p>
                                    </div>
                                </div>

                                <!-- Profit Table -->
                                <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-left border-collapse">
                                            <thead>
                                                <tr class="bg-slate-50/80 border-b border-slate-100">
                                                    <th class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Vessel</th>
                                                    <th class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-right">Revenue</th>
                                                    <th class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-right">Cost</th>
                                                    <th class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-right">Profit</th>
                                                    <th class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-50">
                                                <?php if (!empty($vesselProfitData)): ?>
                                                    <?php foreach ($vesselProfitData as $vp): ?>
                                                        <tr class="hover:bg-blue-50/30 transition-colors group">
                                                            <td class="px-4 py-3">
                                                                <div class="flex items-center gap-2.5">
                                                                    <div class="<?= $vp['is_profitable'] ? 'bg-blue-100 text-blue-600' : 'bg-rose-100 text-rose-600' ?> rounded-lg p-1.5">
                                                                        <span class="material-symbols-outlined text-[16px]">
                                                                            <?= $vp['is_profitable'] ? 'directions_boat' : 'warning' ?>
                                                                        </span>
                                                                    </div>
                                                                    <div>
                                                                        <p class="font-semibold text-navy text-sm"><?= htmlspecialchars($vp['name']) ?></p>
                                                                        <p class="text-[10px] text-slate-400 font-medium"><?= htmlspecialchars($vp['vessel_type']) ?> • <?= $vp['crew_count'] ?> Crew</p>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="px-4 py-3 text-right text-sm font-medium text-slate-600 tabular-nums">$<?= number_format($vp['revenue_usd'], 0) ?></td>
                                                            <td class="px-4 py-3 text-right text-sm font-medium text-slate-600 tabular-nums">$<?= number_format($vp['cost_usd'], 0) ?></td>
                                                            <td class="px-4 py-3 text-right text-sm font-bold tabular-nums <?= $vp['is_profitable'] ? 'text-navy' : 'text-rose-600' ?>">
                                                                <?= $vp['is_profitable'] ? '$' : '-$' ?><?= number_format(abs($vp['profit_usd']), 0) ?>
                                                            </td>
                                                            <td class="px-4 py-3 text-center">
                                                                <?php if ($vp['is_profitable']): ?>
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200/50">
                                                                        Profit <?= number_format($vp['margin_percent'], 0) ?>%
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-200/50">
                                                                        Loss <?= number_format(abs($vp['margin_percent']), 0) ?>%
                                                                    </span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="px-4 py-8 text-center">
                                                            <span class="material-symbols-outlined text-4xl text-slate-200">analytics</span>
                                                            <p class="text-slate-400 mt-2 text-sm">No profit data available</p>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
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

                    <!-- Anggota Kru Table -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden" x-data="{ crewFilter: 'active' }">
                        <div
                            class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <h3 class="font-bold text-navy text-lg">Anggota Kru</h3>
                            <div class="flex p-1 bg-slate-50 rounded-lg border border-slate-200">
                                <button @click="crewFilter = 'active'"
                                    :class="crewFilter === 'active' ? 'text-primary bg-white shadow-sm' : 'text-slate-500 hover:text-navy'"
                                    class="px-4 py-1.5 text-sm font-medium rounded-md transition-all">
                                    Aktif <span class="text-[10px] ml-1 opacity-60">(<?= $stats['active_crew'] ?>)</span>
                                </button>
                                <button @click="crewFilter = 'inactive'"
                                    :class="crewFilter === 'inactive' ? 'text-primary bg-white shadow-sm' : 'text-slate-500 hover:text-navy'"
                                    class="px-4 py-1.5 text-sm font-medium rounded-md transition-all">
                                    Non-Aktif <span class="text-[10px] ml-1 opacity-60">(<?= $stats['inactive_crew'] ?>)</span>
                                </button>
                                <button @click="crewFilter = 'all'"
                                    :class="crewFilter === 'all' ? 'text-primary bg-white shadow-sm' : 'text-slate-500 hover:text-navy'"
                                    class="px-4 py-1.5 text-sm font-medium rounded-md transition-all">
                                    Semua <span class="text-[10px] ml-1 opacity-60">(<?= $stats['active_crew'] + $stats['inactive_crew'] ?>)</span>
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50/50 border-b border-slate-100">
                                        <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Anggota Kru</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Jabatan</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Kapal</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Gaji Bulanan</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider text-right">Est. Profit</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    <?php if (!empty($contracts)): ?>
                                        <?php foreach ($contracts as $contract): ?>
                                            <?php
                                            $isActive = in_array($contract['status'], ['active', 'onboard']);
                                            $statusTag = $isActive ? 'active' : 'inactive';
                                            ?>
                                            <tr class="hover:bg-slate-50/80 transition-colors group"
                                                x-show="crewFilter === 'all' || crewFilter === '<?= $statusTag ?>'" x-transition>
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
                                                        <div class="size-10 rounded-full bg-gradient-to-br <?= $isActive ? 'from-blue-400 to-blue-600' : 'from-slate-300 to-slate-400' ?> flex items-center justify-center text-white text-sm font-bold">
                                                            <?= $crewInitials ?>
                                                        </div>
                                                        <div>
                                                            <p class="text-sm font-semibold text-navy">
                                                                <?= htmlspecialchars($contract['crew_name'] ?? 'N/A') ?>
                                                            </p>
                                                            <div class="flex items-center gap-1.5">
                                                                <p class="text-xs text-slate-400">ID: CR-<?= str_pad($contract['crew_id'] ?? '0', 4, '0', STR_PAD_LEFT) ?></p>
                                                                <?php if (!$isActive): ?>
                                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-500"><?= ucfirst($contract['status'] ?? 'inactive') ?></span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $isActive ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'bg-slate-50 text-slate-500 border border-slate-100' ?>">
                                                        <?= htmlspecialchars($contract['rank'] ?? 'N/A') ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center gap-2">
                                                        <div class="size-2 <?= $isActive ? 'bg-emerald-500' : 'bg-slate-300' ?> rounded-full"></div>
                                                        <span class="text-sm text-slate-600 font-medium">
                                                            <?= htmlspecialchars($contract['vessel_name'] ?? 'Belum Ditugaskan') ?>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="flex flex-col">
                                                        <?php $salaryIDR = ($contract['salary_usd'] ?? 0) * 15300; ?>
                                                        <span class="text-sm font-medium text-navy">IDR <?= number_format($salaryIDR, 0, ',', '.') ?></span>
                                                        <span class="text-xs text-slate-400">≈ $<?= number_format($contract['salary_usd'] ?? 0, 0) ?> USD</span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    <?php if ($isActive && ($contract['profit_usd'] ?? 0) > 0): ?>
                                                        <span class="text-sm font-bold text-emerald-600">+$<?= number_format($contract['profit_usd'] ?? 0, 0) ?></span>
                                                    <?php elseif ($isActive && ($contract['profit_usd'] ?? 0) < 0): ?>
                                                        <span class="text-sm font-bold text-rose-600">-$<?= number_format(abs($contract['profit_usd'] ?? 0), 0) ?></span>
                                                    <?php else: ?>
                                                        <span class="text-sm text-slate-400">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    <button class="text-slate-300 hover:text-primary transition-colors">
                                                        <span class="material-symbols-outlined">more_vert</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="px-6 py-12 text-center">
                                                <span class="material-symbols-outlined text-5xl text-slate-200">groups</span>
                                                <p class="text-slate-400 mt-2">Belum ada anggota kru untuk klien ini</p>
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

    <!-- Edit Client Modal (Book-Flip Animation) -->
    <template x-if="editClientModal">
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
             @keydown.escape.window="editClientModal = false">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm backdrop-fade-in" @click="editClientModal = false"></div>

            <!-- Modal Content -->
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto book-open-enter z-10">
                <!-- Modal Header -->
                <div class="sticky top-0 z-20 bg-gradient-to-r from-slate-800 to-slate-900 rounded-t-2xl px-6 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white/10 rounded-lg">
                            <span class="material-symbols-outlined text-amber-400 text-2xl">edit_note</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Edit Client</h3>
                            <p class="text-slate-400 text-xs">Perbarui informasi klien <?= htmlspecialchars($client['name'] ?? '') ?></p>
                        </div>
                    </div>
                    <button @click="editClientModal = false"
                            class="p-2 hover:bg-white/10 rounded-full transition-colors text-slate-400 hover:text-white">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <!-- Form -->
                <form action="<?= BASE_URL ?>clients/update/<?= $client['id'] ?>" method="POST">
                    <div class="p-6 space-y-6">
                        <!-- Section 1: Company Information -->
                        <div class="bg-slate-50/50 rounded-xl border border-slate-100 overflow-hidden">
                            <div class="px-5 py-3 border-b border-slate-100 flex items-center gap-2">
                                <span class="material-symbols-outlined text-blue-600 text-lg">business</span>
                                <h4 class="font-bold text-slate-800 text-sm">Informasi Perusahaan</h4>
                            </div>
                            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Company Name -->
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-semibold text-slate-500">Nama Perusahaan <span class="text-red-400">*</span></label>
                                    <input type="text" name="name" required
                                           value="<?= htmlspecialchars($client['name'] ?? '') ?>"
                                           class="w-full rounded-lg bg-white border border-slate-200 px-3.5 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all placeholder-slate-400"
                                           placeholder="Nama perusahaan">
                                </div>
                                <!-- Short Name -->
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-semibold text-slate-500">Nama Singkat / Kode</label>
                                    <input type="text" name="short_name"
                                           value="<?= htmlspecialchars($client['short_name'] ?? '') ?>"
                                           class="w-full rounded-lg bg-white border border-slate-200 px-3.5 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all placeholder-slate-400"
                                           placeholder="Kode singkat">
                                </div>
                                <!-- Country -->
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-semibold text-slate-500">Negara</label>
                                    <input type="text" name="country"
                                           value="<?= htmlspecialchars($client['country'] ?? '') ?>"
                                           class="w-full rounded-lg bg-white border border-slate-200 px-3.5 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all placeholder-slate-400"
                                           placeholder="Negara">
                                </div>
                                <!-- City -->
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-semibold text-slate-500">Kota</label>
                                    <input type="text" name="city"
                                           value="<?= htmlspecialchars($client['city'] ?? '') ?>"
                                           class="w-full rounded-lg bg-white border border-slate-200 px-3.5 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all placeholder-slate-400"
                                           placeholder="Kota">
                                </div>
                                <!-- Address -->
                                <div class="md:col-span-2 space-y-1.5">
                                    <label class="block text-xs font-semibold text-slate-500">Alamat</label>
                                    <textarea name="address" rows="2"
                                              class="w-full rounded-lg bg-white border border-slate-200 px-3.5 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all placeholder-slate-400 resize-none"
                                              placeholder="Alamat lengkap"><?= htmlspecialchars($client['address'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Contact Details -->
                        <div class="bg-slate-50/50 rounded-xl border border-slate-100 overflow-hidden">
                            <div class="px-5 py-3 border-b border-slate-100 flex items-center gap-2">
                                <span class="material-symbols-outlined text-green-600 text-lg">call</span>
                                <h4 class="font-bold text-slate-800 text-sm">Detail Kontak</h4>
                            </div>
                            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Email -->
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-semibold text-slate-500">Email</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                            <span class="material-symbols-outlined text-[18px]">mail</span>
                                        </span>
                                        <input type="email" name="email"
                                               value="<?= htmlspecialchars($client['email'] ?? '') ?>"
                                               class="w-full rounded-lg bg-white border border-slate-200 pl-10 pr-3.5 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all placeholder-slate-400"
                                               placeholder="email@perusahaan.com">
                                    </div>
                                </div>
                                <!-- Phone -->
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-semibold text-slate-500">Telepon</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                            <span class="material-symbols-outlined text-[18px]">phone</span>
                                        </span>
                                        <input type="text" name="phone"
                                               value="<?= htmlspecialchars($client['phone'] ?? '') ?>"
                                               class="w-full rounded-lg bg-white border border-slate-200 pl-10 pr-3.5 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all placeholder-slate-400"
                                               placeholder="+62 xxx xxxx xxxx">
                                    </div>
                                </div>
                                <!-- Website -->
                                <div class="md:col-span-2 space-y-1.5">
                                    <label class="block text-xs font-semibold text-slate-500">Website</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                            <span class="material-symbols-outlined text-[18px]">language</span>
                                        </span>
                                        <input type="url" name="website"
                                               value="<?= htmlspecialchars($client['website'] ?? '') ?>"
                                               class="w-full rounded-lg bg-white border border-slate-200 pl-10 pr-3.5 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all placeholder-slate-400"
                                               placeholder="https://">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Contact Person -->
                        <div class="bg-slate-50/50 rounded-xl border border-slate-100 overflow-hidden">
                            <div class="px-5 py-3 border-b border-slate-100 flex items-center gap-2">
                                <span class="material-symbols-outlined text-purple-600 text-lg">person</span>
                                <h4 class="font-bold text-slate-800 text-sm">Kontak Person</h4>
                            </div>
                            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Contact Name -->
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-semibold text-slate-500">Nama</label>
                                    <input type="text" name="contact_person"
                                           value="<?= htmlspecialchars($client['contact_person'] ?? '') ?>"
                                           class="w-full rounded-lg bg-white border border-slate-200 px-3.5 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all placeholder-slate-400"
                                           placeholder="Nama kontak person">
                                </div>
                                <!-- Contact Email -->
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-semibold text-slate-500">Email</label>
                                    <input type="email" name="contact_email"
                                           value="<?= htmlspecialchars($client['contact_email'] ?? '') ?>"
                                           class="w-full rounded-lg bg-white border border-slate-200 px-3.5 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all placeholder-slate-400"
                                           placeholder="email@kontak.com">
                                </div>
                                <!-- Contact Phone -->
                                <div class="md:col-span-2 space-y-1.5">
                                    <label class="block text-xs font-semibold text-slate-500">Telepon</label>
                                    <input type="text" name="contact_phone"
                                           value="<?= htmlspecialchars($client['contact_phone'] ?? '') ?>"
                                           class="w-full rounded-lg bg-white border border-slate-200 px-3.5 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all placeholder-slate-400"
                                           placeholder="+62 xxx xxxx xxxx">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="sticky bottom-0 bg-white border-t border-slate-100 px-6 py-4 flex items-center justify-end gap-3 rounded-b-2xl">
                        <button type="button" @click="editClientModal = false"
                                class="px-5 py-2.5 rounded-lg border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50 transition-all">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white text-sm font-bold shadow-lg shadow-amber-200/50 hover:shadow-amber-300/50 transition-all flex items-center gap-2 active:scale-95">
                            <span class="material-symbols-outlined text-[18px]">save</span>
                            Update Client
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

</body>

</html>