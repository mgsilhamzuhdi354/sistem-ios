<?php
/**
 * Modern Dashboard View - TailwindCSS
 * PT Indo Ocean - ERP System
 */
$currentPage = 'dashboard';

// Format number helper
function formatNumber($num)
{
    if ($num >= 1000000) {
        return '$' . number_format($num / 1000000, 1) . 'M';
    } elseif ($num >= 1000) {
        return '$' . number_format($num / 1000, 1) . 'K';
    }
    return '$' . number_format($num, 0);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>
        <?= $title ?? 'Dashboard' ?> - PT Indo Ocean
    </title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
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
    <style>
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        [x-cloak] { display: none !important; }
        @keyframes fadeInUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
        .animate-fade-in{animation:fadeInUp .4s ease-out forwards}
        .animate-d1{animation-delay:.05s}.animate-d2{animation-delay:.1s}.animate-d3{animation-delay:.15s}.animate-d4{animation-delay:.2s}
    </style>
</head>

<body class="bg-surface-subtle text-slate-600 antialiased">

    <div class="flex h-screen overflow-hidden">
        <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

        <main class="ml-64 flex-1 flex flex-col min-w-0 overflow-hidden bg-surface-subtle">
            <!-- Header -->
            <header class="h-16 flex items-center justify-between px-8 bg-white/80 backdrop-blur-xl border-b border-slate-200/60 z-10 sticky top-0 flex-shrink-0">
                <div class="flex items-center gap-4 flex-1">
                    <div class="relative w-full max-w-md group">
                        <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                        <input class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700 placeholder-slate-400 focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue transition-all"
                            placeholder="Cari kontrak, kru, kapal..." type="text" />
                    </div>
                </div>

                <!-- Contracts Dropdown Menu -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false"
                        class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 hover:border-brand-blue hover:text-brand-blue transition-all group">
                        <span class="material-icons-round text-lg">description</span>
                        <span>Contracts</span>
                        <?php $totalActive = $contractStats['active'] ?? 0; if ($totalActive > 0): ?>
                            <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-bold rounded-full"><?= $totalActive ?></span>
                        <?php endif; ?>
                        <span class="material-icons-round text-lg transition-transform" :class="open ? 'rotate-180' : ''">keyboard_arrow_down</span>
                    </button>

                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-2xl border border-slate-200 py-2 z-50"
                        style="display: none;">

                        <!-- All Contracts -->
                        <a href="<?= BASE_URL ?>contracts" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 transition-colors group">
                            <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                <span class="material-icons-round text-brand-blue text-lg">folder_open</span>
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-sm text-slate-800">Semua Kontrak</div>
                                <div class="text-xs text-slate-500"><?= $totalActive ?> aktif</div>
                            </div>
                            <?php if ($totalActive > 0): ?>
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-bold rounded-full"><?= $totalActive ?></span>
                            <?php endif; ?>
                        </a>

                        <div class="h-px bg-slate-100 my-1"></div>

                        <!-- New Contract -->
                        <a href="<?= BASE_URL ?>contracts/create" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 transition-colors group">
                            <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
                                <span class="material-icons-round text-green-600 text-lg">add_circle</span>
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-sm text-slate-800">Kontrak Baru</div>
                                <div class="text-xs text-slate-500">Buat kontrak baru</div>
                            </div>
                            <span class="material-icons-round text-slate-400 text-sm">arrow_forward</span>
                        </a>

                        <div class="h-px bg-slate-100 my-1"></div>

                        <!-- Expiring Soon -->
                        <?php $expiringCount = $contractStats['expiringSoon'] ?? 0; ?>
                        <button @click="$dispatch('open-expiring-modal')"
                            class="w-full flex items-center gap-3 px-4 py-3 hover:bg-slate-50 transition-colors group">
                            <div class="w-9 h-9 bg-orange-100 rounded-lg flex items-center justify-center group-hover:bg-orange-200 transition-colors">
                                <span class="material-icons-round text-orange-600 text-lg">schedule</span>
                            </div>
                            <div class="flex-1 text-left">
                                <div class="font-semibold text-sm text-slate-800">Segera Berakhir</div>
                                <div class="text-xs text-slate-500"><?= $expiringCount > 0 ? "$expiringCount kontrak" : "Tidak ada" ?></div>
                            </div>
                            <?php if ($expiringCount > 0): ?>
                                <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs font-bold rounded-full animate-pulse"><?= $expiringCount ?></span>
                            <?php endif; ?>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-3 ml-4">
                    <!-- Mode Toggle -->
                    <div class="flex items-center bg-slate-100 rounded-lg p-1">
                        <a href="<?= BASE_URL ?>dashboard/toggleMode?mode=classic"
                            class="px-3 py-1.5 text-xs font-bold text-slate-600 hover:bg-white rounded-md transition-all flex items-center gap-1">
                            <span class="material-icons-round text-sm">view_compact</span> Classic
                        </a>
                        <span class="px-3 py-1.5 text-xs font-bold text-brand-blue bg-white rounded-md shadow-sm flex items-center gap-1">
                            <span class="material-icons-round text-sm">auto_awesome</span> Modern
                        </span>
                    </div>

                    <div class="h-6 w-px bg-slate-200"></div>

                    <!-- Notification Bell with Real-time Polling -->
                    <div class="relative" x-data="notificationSystem()" x-init="init()">
                        <button @click="open = !open" @click.away="open = false" class="relative p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full transition-all">
                            <span class="material-icons-round">notifications</span>
                            <template x-if="unreadCount > 0">
                                <span class="absolute top-1 right-1 min-w-[18px] h-[18px] bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center ring-2 ring-white" x-text="unreadCount"></span>
                            </template>
                        </button>

                        <!-- Notification Dropdown -->
                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            class="absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-2xl border border-slate-200 z-50 overflow-hidden" style="display:none;">
                            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                                <h4 class="font-bold text-sm text-slate-800">Notifikasi</h4>
                                <span class="text-xs text-slate-400" x-text="notifications.length + ' notif'"></span>
                            </div>
                            <div class="max-h-80 overflow-y-auto divide-y divide-slate-50">
                                <template x-if="notifications.length === 0">
                                    <div class="px-4 py-8 text-center">
                                        <span class="material-icons-round text-4xl text-emerald-400 mb-2 block">check_circle</span>
                                        <p class="text-sm text-slate-400">Tidak ada notifikasi</p>
                                    </div>
                                </template>
                                <template x-for="notif in notifications" :key="notif.message">
                                    <a :href="notif.url || '#'" class="flex items-start gap-3 px-4 py-3 hover:bg-blue-50/50 transition-colors"
                                        :class="{'bg-red-50/30': notif.type === 'urgent', 'bg-amber-50/30': notif.type === 'warning'}">
                                        <div class="mt-0.5 flex-shrink-0">
                                            <span class="material-icons-round text-lg"
                                                :class="{'text-red-500': notif.type === 'urgent', 'text-amber-500': notif.type === 'warning', 'text-blue-500': notif.type === 'info'}"
                                                x-text="notif.icon"></span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold text-slate-700" x-text="notif.title"></p>
                                            <p class="text-xs text-slate-500 truncate" x-text="notif.message"></p>
                                            <p class="text-[10px] text-slate-400 mt-0.5" x-text="notif.time ? new Date(notif.time).toLocaleDateString('id-ID') : ''"></p>
                                        </div>
                                    </a>
                                </template>
                            </div>
                            <div class="px-4 py-2 border-t border-slate-100 bg-slate-50">
                                <a href="<?= BASE_URL ?>notifications" class="text-xs font-semibold text-brand-blue hover:underline">Lihat Semua →</a>
                            </div>
                        </div>
                    </div>

                    <a href="<?= BASE_URL ?>contracts/create"
                        class="bg-brand-gold hover:bg-yellow-400 text-slate-900 pl-4 pr-5 py-2 rounded-lg font-bold text-sm flex items-center gap-2 transition-all active:scale-95 shadow-lg shadow-yellow-500/20">
                        <span class="material-icons-round text-lg">add_circle</span>
                        Kontrak Baru
                    </a>
                </div>
            </header>

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto custom-scrollbar p-8 space-y-6">
                <!-- Page Header -->
                <div class="flex items-end justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800">Dashboard Overview</h2>
                        <p class="text-slate-500 mt-1">Selamat
                            <?= date('H') < 12 ? 'pagi' : (date('H') < 17 ? 'siang' : 'malam') ?>! Berikut ringkasan operasional maritim Anda.
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="p-2 bg-white border border-slate-200 text-slate-500 hover:text-brand-blue rounded-lg shadow-sm transition-colors" onclick="location.reload()">
                            <span class="material-icons-round">refresh</span>
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 opacity-0 animate-fade-in">
                    <!-- Active Contracts -->
                    <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-soft hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-slate-500 text-sm font-medium mb-1">Kontrak Aktif</p>
                                <h3 class="text-3xl font-bold text-slate-800"><?= $contractStats['active'] ?? 0 ?></h3>
                            </div>
                            <div class="p-3 bg-blue-50 text-brand-blue rounded-xl">
                                <span class="material-icons-round">description</span>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center gap-2">
                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md flex items-center gap-1">
                                <span class="material-icons-round text-[14px]">trending_up</span> Aktif
                            </span>
                            <span class="text-xs text-slate-400"><?= $contractStats['total'] ?? 0 ?> total</span>
                        </div>
                    </div>

                    <!-- Expiring Soon -->
                    <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-soft hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-slate-500 text-sm font-medium mb-1">Segera Berakhir</p>
                                <h3 class="text-3xl font-bold text-slate-800"><?= $contractStats['expiringSoon'] ?? 0 ?></h3>
                            </div>
                            <div class="p-3 bg-amber-50 text-amber-500 rounded-xl">
                                <span class="material-icons-round">history</span>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center gap-2">
                            <?php if (($contractStats['expiringSoon'] ?? 0) > 0): ?>
                                <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-1 rounded-md flex items-center gap-1">
                                    <span class="material-icons-round text-[14px]">priority_high</span> Urgent
                                </span>
                                <span class="text-xs text-slate-400">Perlu tindakan</span>
                            <?php else: ?>
                                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md">Aman</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Total Crew -->
                    <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-soft hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-slate-500 text-sm font-medium mb-1">Kru Aktif</p>
                                <h3 class="text-3xl font-bold text-slate-800"><?= $contractStats['onboard'] ?? 0 ?></h3>
                            </div>
                            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                                <span class="material-icons-round">group</span>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center gap-2">
                            <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-md">Onboard</span>
                            <span class="text-xs text-slate-400"><?= $vesselStats['total'] ?? 0 ?> kapal</span>
                        </div>
                    </div>

                    <!-- Monthly Payroll -->
                    <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-soft hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-slate-500 text-sm font-medium mb-1">Payroll Bulanan</p>
                                <h3 class="text-3xl font-bold text-slate-800"><?= formatNumber($monthlyPayroll ?? 0) ?></h3>
                            </div>
                            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                                <span class="material-icons-round">monetization_on</span>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded-md flex items-center gap-1">USD</span>
                            <span class="text-xs text-slate-400">Update hari ini</span>
                        </div>
                    </div>
                </div>

                <!-- Priority Contract Alerts -->
                <?php
                $expiring7 = 0;
                $expiring30 = 0;
                $expiring60 = 0;
                foreach ($expiringContracts ?? [] as $contract) {
                    $days = isset($contract['days_remaining']) ? (int) $contract['days_remaining'] : 999;
                    if ($days <= 7) $expiring7++;
                    elseif ($days <= 30) $expiring30++;
                    elseif ($days <= 60) $expiring60++;
                }
                ?>
                <div class="space-y-4 opacity-0 animate-fade-in animate-d1">
                    <h4 class="text-sm font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2 px-1">
                        <span class="material-icons-round text-brand-gold">notifications_active</span>
                        Alert Kontrak Prioritas
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <a href="<?= BASE_URL ?>contracts?filter=expiring7"
                            class="p-5 bg-red-50/50 border border-red-100 rounded-xl flex items-center justify-between group cursor-pointer hover:bg-red-50 transition-all hover:shadow-md">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center shadow-sm text-red-500 border border-red-100">
                                    <span class="material-icons-round text-2xl">priority_high</span>
                                </div>
                                <div>
                                    <h5 class="font-bold text-xl text-slate-800 leading-none"><?= $expiring7 ?></h5>
                                    <p class="text-xs text-red-600 font-medium mt-1">Kritis < 7 hari</p>
                                </div>
                            </div>
                            <span class="material-icons-round text-red-300 group-hover:text-red-500 transition-colors">chevron_right</span>
                        </a>
                        <a href="<?= BASE_URL ?>contracts?filter=expiring30"
                            class="p-5 bg-amber-50/50 border border-amber-100 rounded-xl flex items-center justify-between group cursor-pointer hover:bg-amber-50 transition-all hover:shadow-md">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center shadow-sm text-amber-500 border border-amber-100">
                                    <span class="material-icons-round text-2xl">timer</span>
                                </div>
                                <div>
                                    <h5 class="font-bold text-xl text-slate-800 leading-none"><?= $expiring30 ?></h5>
                                    <p class="text-xs text-amber-600 font-medium mt-1">Warning < 30 hari</p>
                                </div>
                            </div>
                            <span class="material-icons-round text-amber-300 group-hover:text-amber-500 transition-colors">chevron_right</span>
                        </a>
                        <a href="<?= BASE_URL ?>contracts?filter=expiring60"
                            class="p-5 bg-blue-50/50 border border-blue-100 rounded-xl flex items-center justify-between group cursor-pointer hover:bg-blue-50 transition-all hover:shadow-md">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center shadow-sm text-brand-blue border border-blue-100">
                                    <span class="material-icons-round text-2xl">info</span>
                                </div>
                                <div>
                                    <h5 class="font-bold text-xl text-slate-800 leading-none"><?= $expiring60 ?></h5>
                                    <p class="text-xs text-brand-blue font-medium mt-1">Info < 60 hari</p>
                                </div>
                            </div>
                            <span class="material-icons-round text-blue-300 group-hover:text-brand-blue transition-colors">chevron_right</span>
                        </a>
                    </div>

                    <!-- Profit per Vessel Section - Charts -->
                    <?php
                    $totalRevenue = 0; $totalCost = 0; $totalProfit = 0;
                    $vpNames = []; $vpRevenues = []; $vpCosts = []; $vpProfits = []; $vpMargins = [];
                    if (!empty($vesselsProfitData)) {
                        foreach ($vesselsProfitData as $vp) {
                            $totalRevenue += $vp['revenue_usd'];
                            $totalCost += $vp['cost_usd'];
                            $totalProfit += $vp['profit_usd'];
                            $vpNames[] = $vp['name'];
                            $vpRevenues[] = round($vp['revenue_usd'], 2);
                            $vpCosts[] = round($vp['cost_usd'], 2);
                            $vpProfits[] = round($vp['profit_usd'], 2);
                            $vpMargins[] = $vp['margin_percent'];
                        }
                    }
                    $totalMargin = $totalRevenue > 0 ? round(($totalProfit / $totalRevenue) * 100, 1) : 0;
                    ?>
                    <div class="bg-white rounded-xl border border-slate-100 shadow-soft overflow-hidden opacity-0 animate-fade-in animate-d2">
                        <div class="p-5 flex items-center justify-between border-b border-slate-100">
                            <div>
                                <h4 class="font-bold text-slate-800 flex items-center gap-2">
                                    <span class="material-icons-round text-emerald-500">sailing</span>
                                    Profit per Vessel
                                </h4>
                                <p class="text-xs text-slate-400">Revenue vs Cost dari kontrak aktif (dalam USD)</p>
                            </div>
                            <a href="<?= BASE_URL ?>reports/by-vessel" class="text-xs font-bold text-brand-blue flex items-center gap-1 hover:underline">
                                Detail <span class="material-icons-round text-xs">arrow_forward</span>
                            </a>
                        </div>

                        <!-- Summary Cards -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-5">
                            <div class="bg-blue-50 rounded-xl p-4 text-center">
                                <p class="text-[10px] uppercase tracking-wider text-blue-500 font-bold mb-1">Total Revenue</p>
                                <p class="text-xl font-bold text-blue-700"><?= formatNumber($totalRevenue) ?></p>
                            </div>
                            <div class="bg-rose-50 rounded-xl p-4 text-center">
                                <p class="text-[10px] uppercase tracking-wider text-rose-500 font-bold mb-1">Total Cost</p>
                                <p class="text-xl font-bold text-rose-700"><?= formatNumber($totalCost) ?></p>
                            </div>
                            <div class="<?= $totalProfit >= 0 ? 'bg-emerald-50' : 'bg-red-50' ?> rounded-xl p-4 text-center">
                                <p class="text-[10px] uppercase tracking-wider <?= $totalProfit >= 0 ? 'text-emerald-500' : 'text-red-500' ?> font-bold mb-1">Total Profit</p>
                                <p class="text-xl font-bold <?= $totalProfit >= 0 ? 'text-emerald-700' : 'text-red-700' ?>"><?= $totalProfit >= 0 ? '+' : '' ?><?= formatNumber($totalProfit) ?></p>
                            </div>
                            <div class="bg-amber-50 rounded-xl p-4 text-center">
                                <p class="text-[10px] uppercase tracking-wider text-amber-500 font-bold mb-1">Avg Margin</p>
                                <p class="text-xl font-bold text-amber-700"><?= $totalMargin ?>%</p>
                            </div>
                        </div>

                        <!-- Charts -->
                        <?php if (!empty($vesselsProfitData)): ?>
                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-0 border-t border-slate-100">
                            <!-- Revenue vs Cost Bar Chart -->
                            <div class="p-5 border-r border-slate-100">
                                <h5 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Revenue vs Cost per Kapal</h5>
                                <div class="relative" style="height: 280px;">
                                    <canvas id="vesselRevenueCostChart"></canvas>
                                </div>
                            </div>
                            <!-- Profit Bar Chart -->
                            <div class="p-5">
                                <h5 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Profit & Margin per Kapal</h5>
                                <div class="relative" style="height: 280px;">
                                    <canvas id="vesselProfitChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="p-8 text-center text-slate-400">
                            <span class="material-icons-round text-3xl mb-2 block">sailing</span>
                            Belum ada data profit kapal
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Client Management Section - Charts -->
                    <?php
                    $clientNames = []; $clientVessels = []; $clientCrews = []; $clientCosts = [];
                    $clientColors = ['#6366f1','#3b82f6','#10b981','#f59e0b','#8b5cf6','#ec4899','#14b8a6','#f97316'];
                    if (!empty($clientsData)) {
                        foreach ($clientsData as $ci => $client) {
                            $clientNames[] = $client['name'];
                            $clientVessels[] = $client['vessel_count'] ?? 0;
                            $clientCrews[] = $client['active_crew_count'] ?? 0;
                            $clientCosts[] = round($client['monthly_cost'] ?? 0, 2);
                        }
                    }
                    ?>
                    <div class="opacity-0 animate-fade-in animate-d3">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-bold text-slate-800 flex items-center gap-2">
                                <span class="material-icons-round text-indigo-500">business</span>
                                Client Management
                            </h4>
                            <a href="<?= BASE_URL ?>clients" class="text-xs font-bold text-brand-blue flex items-center gap-1 hover:underline">
                                Lihat Semua <span class="material-icons-round text-xs">arrow_forward</span>
                            </a>
                        </div>
                        <?php if (!empty($clientsData)): ?>
                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
                            <!-- Client Vessels & Crew Bar Chart -->
                            <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-soft">
                                <h5 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Kapal & Kru per Klien</h5>
                                <div class="relative" style="height: 280px;">
                                    <canvas id="clientOverviewChart"></canvas>
                                </div>
                            </div>
                            <!-- Client Cost Doughnut Chart -->
                            <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-soft">
                                <h5 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Monthly Cost per Klien (USD)</h5>
                                <div class="relative flex items-center justify-center" style="height: 280px;">
                                    <canvas id="clientCostChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="bg-white rounded-xl border border-slate-100 shadow-soft p-8 text-center text-slate-400">
                            <span class="material-icons-round text-3xl mb-2 block">business</span>
                            Belum ada data klien
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Charts Section -->
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 opacity-0 animate-fade-in animate-d3">
                        <!-- Contracts per Vessel Chart -->
                        <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-soft">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h4 class="font-bold text-slate-800">Kontrak per Kapal</h4>
                                    <p class="text-xs text-slate-400">Distribusi kontrak aktif</p>
                                </div>
                            </div>
                            <?php
                            $vesselContracts = $vesselStats['vessel_contracts'] ?? [];
                            $vesselCounts = array_column($vesselContracts, 'count');
                            $maxVesselCount = !empty($vesselCounts) ? max($vesselCounts) : 1;
                            $vesselColors = ['#1e40af', '#fbbf24', '#3b82f6', '#10b981', '#8b5cf6'];
                            ?>
                            <div class="h-56 flex items-end justify-between gap-4">
                                <?php foreach ($vesselContracts as $i => $vessel):
                                    $height = ($maxVesselCount > 0) ? ($vessel['count'] / $maxVesselCount) * 100 : 0;
                                    $color = $vesselColors[$i % 5];
                                ?>
                                <div class="flex-1 flex flex-col justify-end gap-2 h-full group">
                                    <div class="relative w-full h-full flex items-end">
                                        <div class="w-full rounded-md transition-all group-hover:opacity-90"
                                            style="height: <?= $height ?>%; background: <?= $color ?>;">
                                            <div class="absolute -top-6 left-1/2 -translate-x-1/2 text-[11px] font-bold text-slate-700"><?= $vessel['count'] ?></div>
                                        </div>
                                    </div>
                                    <span class="text-[10px] text-center font-medium text-slate-400 truncate"><?= $vessel['name'] ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Monthly Trends Chart -->
                        <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-soft">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h4 class="font-bold text-slate-800">Trend Bulanan</h4>
                                    <p class="text-xs text-slate-400">Volume kontrak per bulan</p>
                                </div>
                            </div>
                            <?php
                            $monthlyData = $contractStats['monthly'] ?? [];
                            $monthlyCounts = array_column($monthlyData, 'count');
                            $maxMonthCount = !empty($monthlyCounts) ? max($monthlyCounts) : 1;
                            $currentMonth = date('M');
                            ?>
                            <div class="h-56 flex items-end justify-between gap-4">
                                <?php foreach ($monthlyData as $data):
                                    $height = $maxMonthCount > 0 ? ($data['count'] / $maxMonthCount) * 100 : 0;
                                    $isCurrentMonth = ($data['month'] === $currentMonth);
                                ?>
                                <div class="flex-1 flex flex-col justify-end gap-2 h-full group">
                                    <div class="relative w-full h-full flex items-end">
                                        <div class="w-full rounded-md transition-all group-hover:opacity-90"
                                            style="height: <?= $height ?>%; background: <?= $isCurrentMonth ? 'linear-gradient(180deg, #fbbf24, #f59e0b)' : 'linear-gradient(180deg, #3b82f6, #1e40af)' ?>; box-shadow: <?= $isCurrentMonth ? '0 4px 12px rgba(251, 191, 36, 0.3)' : 'none' ?>;">
                                            <div class="absolute -top-6 left-1/2 -translate-x-1/2 text-[11px] font-bold text-slate-700"><?= $data['count'] ?></div>
                                        </div>
                                    </div>
                                    <span class="text-[10px] text-center font-medium <?= $isCurrentMonth ? 'text-slate-800 font-bold' : 'text-slate-400' ?>"><?= $data['month'] ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Contracts Table -->
                    <div class="bg-white rounded-xl border border-slate-100 shadow-soft overflow-hidden opacity-0 animate-fade-in animate-d4">
                        <div class="p-5 flex items-center justify-between border-b border-slate-100">
                            <div>
                                <h4 class="font-bold text-slate-800">Kontrak Terbaru</h4>
                                <p class="text-xs text-slate-400">Penugasan kru terbaru</p>
                            </div>
                            <a class="text-xs font-bold text-brand-blue flex items-center gap-1 hover:underline" href="<?= BASE_URL ?>contracts">
                                Lihat Semua <span class="material-icons-round text-xs">arrow_forward</span>
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] uppercase tracking-wider text-slate-500 font-bold">
                                        <th class="px-5 py-3">ID Kontrak</th>
                                        <th class="px-5 py-3">Kru</th>
                                        <th class="px-5 py-3">Posisi</th>
                                        <th class="px-5 py-3">Kapal</th>
                                        <th class="px-5 py-3">Durasi</th>
                                        <th class="px-5 py-3">Status</th>
                                        <th class="px-5 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php if (!empty($recentContracts)): ?>
                                        <?php foreach ($recentContracts as $contract): ?>
                                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                                <td class="px-5 py-3 text-sm font-semibold text-slate-700">
                                                    CTR-<?= date('Y', strtotime($contract['sign_on_date'] ?? 'now')) ?>-<?= str_pad($contract['id'], 4, '0', STR_PAD_LEFT) ?>
                                                </td>
                                                <td class="px-5 py-3">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-[10px] font-bold text-indigo-700 ring-2 ring-white shadow-sm">
                                                            <?= strtoupper(substr($contract['crew_name'] ?? 'N', 0, 2)) ?>
                                                        </div>
                                                        <span class="text-sm font-medium text-slate-700"><?= htmlspecialchars($contract['crew_name'] ?? 'N/A') ?></span>
                                                    </div>
                                                </td>
                                                <td class="px-5 py-3 text-sm text-slate-600"><?= htmlspecialchars($contract['rank_name'] ?? 'N/A') ?></td>
                                                <td class="px-5 py-3 text-sm text-slate-600"><?= htmlspecialchars($contract['vessel_name'] ?? 'N/A') ?></td>
                                                <td class="px-5 py-3 text-sm text-slate-500">
                                                    <div class="flex flex-col">
                                                        <span class="text-xs font-bold text-slate-700"><?= $contract['contract_duration'] ?? 'N/A' ?> Bln</span>
                                                        <span class="text-[10px]"><?= date('M Y', strtotime($contract['sign_on_date'] ?? 'now')) ?></span>
                                                    </div>
                                                </td>
                                                <td class="px-5 py-3">
                                                    <?php
                                                    $status = $contract['status'] ?? 'draft';
                                                    $statusClass = match ($status) {
                                                        'active', 'onboard' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                        'draft' => 'bg-slate-100 text-slate-600 border-slate-200',
                                                        'completed' => 'bg-blue-50 text-blue-600 border-blue-100',
                                                        default => 'bg-slate-100 text-slate-600 border-slate-200'
                                                    };
                                                    ?>
                                                    <span class="px-2.5 py-1 rounded-full <?= $statusClass ?> border text-[10px] font-bold uppercase tracking-tight"><?= ucfirst($status) ?></span>
                                                </td>
                                                <td class="px-5 py-3">
                                                    <div class="flex items-center justify-center gap-1 opacity-60 group-hover:opacity-100 transition-opacity">
                                                        <a href="<?= BASE_URL ?>contracts/view/<?= $contract['id'] ?>" class="p-1.5 text-slate-400 hover:text-brand-blue hover:bg-blue-50 rounded transition-all">
                                                            <span class="material-icons-round text-lg">visibility</span>
                                                        </a>
                                                        <a href="<?= BASE_URL ?>contracts/edit/<?= $contract['id'] ?>" class="p-1.5 text-slate-400 hover:text-brand-blue hover:bg-blue-50 rounded transition-all">
                                                            <span class="material-icons-round text-lg">edit</span>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="px-5 py-10 text-center text-slate-400">
                                                <span class="material-icons-round text-4xl mb-2 block">inbox</span>
                                                Belum ada kontrak terbaru
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        </main>
    </div>

    <!-- Expiring Contracts Modal -->
    <div x-data="{ showModal: false }" @open-expiring-modal.window="showModal = true" x-show="showModal" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">

        <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="showModal = false"
            class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

        <div class="flex min-h-screen items-center justify-center p-4">
            <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90" @click.away="showModal = false"
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden">

                <!-- Header -->
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-lg flex items-center justify-center">
                            <span class="material-icons-round text-white text-xl">schedule</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Kontrak Segera Berakhir</h3>
                            <p class="text-orange-100 text-sm">Kontrak berakhir dalam 90 hari</p>
                        </div>
                    </div>
                    <button @click="showModal = false" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/20 transition-colors">
                        <span class="material-icons-round text-white">close</span>
                    </button>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-3 gap-4 p-6 bg-slate-50 border-b border-slate-200">
                    <?php
                    $mc30 = 0; $mw60 = 0; $mu90 = 0;
                    foreach ($expiringContracts ?? [] as $contract) {
                        $daysLeft = isset($contract['days_remaining']) ? (int) $contract['days_remaining'] : 999;
                        if ($daysLeft <= 30) $mc30++;
                        elseif ($daysLeft <= 60) $mw60++;
                        else $mu90++;
                    }
                    ?>
                    <div class="bg-white rounded-xl p-4 border-l-4 border-red-500 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-3xl font-bold text-red-600"><?= $mc30 ?></div>
                                <div class="text-xs text-slate-500 mt-1">Kritis (≤30 hari)</div>
                            </div>
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                <span class="material-icons-round text-red-600">error</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-4 border-l-4 border-yellow-500 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-3xl font-bold text-yellow-600"><?= $mw60 ?></div>
                                <div class="text-xs text-slate-500 mt-1">Warning (31-60 hari)</div>
                            </div>
                            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <span class="material-icons-round text-yellow-600">warning</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-4 border-l-4 border-blue-500 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-3xl font-bold text-blue-600"><?= $mu90 ?></div>
                                <div class="text-xs text-slate-500 mt-1">Info (61-90 hari)</div>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <span class="material-icons-round text-blue-600">info</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contracts Table -->
                <div class="p-6 max-h-96 overflow-y-auto">
                    <?php if (!empty($expiringContracts)): ?>
                        <div class="space-y-3">
                            <?php foreach ($expiringContracts as $contract):
                                $daysLeft = isset($contract['days_remaining']) ? (int) $contract['days_remaining'] : 999;
                                $urgencyClass = $daysLeft <= 30 ? 'red' : ($daysLeft <= 60 ? 'yellow' : 'blue');
                                $urgencyBg = $daysLeft <= 30 ? 'bg-red-50' : ($daysLeft <= 60 ? 'bg-yellow-50' : 'bg-blue-50');
                                $urgencyBorder = $daysLeft <= 30 ? 'border-red-200' : ($daysLeft <= 60 ? 'border-yellow-200' : 'border-blue-200');
                                $contractId = 'CTR-' . date('Y', strtotime($contract['sign_on_date'] ?? $contract['sign_off_date'] ?? 'now')) . '-' . str_pad($contract['id'], 4, '0', STR_PAD_LEFT);
                            ?>
                            <div class="<?= $urgencyBg ?> border <?= $urgencyBorder ?> rounded-xl p-4 hover:shadow-md transition-all">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="font-bold text-slate-800"><?= htmlspecialchars($contractId) ?></span>
                                            <span class="px-2 py-1 bg-<?= $urgencyClass ?>-100 text-<?= $urgencyClass ?>-700 text-xs font-bold rounded-full">
                                                <?= $daysLeft ?> hari lagi
                                            </span>
                                        </div>
                                        <div class="grid grid-cols-3 gap-4 text-sm">
                                            <div>
                                                <div class="text-xs text-slate-500">Nama Kru</div>
                                                <div class="font-semibold text-slate-700"><?= htmlspecialchars($contract['crew_name'] ?? 'N/A') ?></div>
                                            </div>
                                            <div>
                                                <div class="text-xs text-slate-500">Kapal</div>
                                                <div class="font-semibold text-slate-700"><?= htmlspecialchars($contract['vessel_name'] ?? 'N/A') ?></div>
                                            </div>
                                            <div>
                                                <div class="text-xs text-slate-500">Tanggal Berakhir</div>
                                                <div class="font-semibold text-slate-700"><?= isset($contract['sign_off_date']) ? date('d M Y', strtotime($contract['sign_off_date'])) : '-' ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="<?= BASE_URL ?>contracts/view/<?= $contract['id'] ?>" class="ml-4 p-2 hover:bg-white rounded-lg transition-colors">
                                        <span class="material-icons-round text-slate-400">arrow_forward</span>
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12">
                            <span class="material-icons-round text-6xl text-green-500 mb-3 block">check_circle</span>
                            <div class="text-lg font-semibold text-slate-700">Tidak ada kontrak segera berakhir</div>
                            <div class="text-sm text-slate-500 mt-1">Semua kontrak masih berlaku lebih dari 90 hari</div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="bg-slate-50 px-6 py-4 flex items-center justify-between border-t border-slate-200">
                    <a href="<?= BASE_URL ?>contracts?filter=expiring" class="text-sm text-brand-blue hover:text-blue-700 font-semibold flex items-center gap-1">
                        <span>Lihat semua di halaman Kontrak</span>
                        <span class="material-icons-round text-sm">arrow_forward</span>
                    </a>
                    <button @click="showModal = false" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg font-semibold transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Charts JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartFont = { family: "'Inter', sans-serif", size: 11 };
        const gridColor = 'rgba(148, 163, 184, 0.12)';

        // ===== 1. Revenue vs Cost Bar Chart =====
        const rcCtx = document.getElementById('vesselRevenueCostChart');
        if (rcCtx) {
            new Chart(rcCtx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($vpNames) ?>,
                    datasets: [
                        {
                            label: 'Revenue',
                            data: <?= json_encode($vpRevenues) ?>,
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                            borderColor: '#3b82f6',
                            borderWidth: 1,
                            borderRadius: 6,
                            borderSkipped: false
                        },
                        {
                            label: 'Cost',
                            data: <?= json_encode($vpCosts) ?>,
                            backgroundColor: 'rgba(244, 63, 94, 0.7)',
                            borderColor: '#f43f5e',
                            borderWidth: 1,
                            borderRadius: 6,
                            borderSkipped: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { font: chartFont, usePointStyle: true, pointStyle: 'rectRounded', padding: 16 } },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleFont: { ...chartFont, weight: 'bold' },
                            bodyFont: chartFont,
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: { label: ctx => ctx.dataset.label + ': $' + ctx.raw.toLocaleString() }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: chartFont } },
                        y: { grid: { color: gridColor }, ticks: { font: chartFont, callback: v => '$' + (v >= 1000 ? (v/1000).toFixed(0) + 'K' : v) }, beginAtZero: true }
                    }
                }
            });
        }

        // ===== 2. Profit Bar Chart =====
        const profitCtx = document.getElementById('vesselProfitChart');
        if (profitCtx) {
            const profitData = <?= json_encode($vpProfits) ?>;
            const marginData = <?= json_encode($vpMargins) ?>;
            new Chart(profitCtx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($vpNames) ?>,
                    datasets: [
                        {
                            label: 'Profit (USD)',
                            data: profitData,
                            backgroundColor: profitData.map(v => v >= 0 ? 'rgba(16, 185, 129, 0.8)' : 'rgba(239, 68, 68, 0.8)'),
                            borderColor: profitData.map(v => v >= 0 ? '#10b981' : '#ef4444'),
                            borderWidth: 1,
                            borderRadius: 6,
                            borderSkipped: false,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Margin (%)',
                            data: marginData,
                            type: 'line',
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245, 158, 11, 0.15)',
                            borderWidth: 2.5,
                            pointBackgroundColor: '#f59e0b',
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            fill: true,
                            tension: 0.3,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { font: chartFont, usePointStyle: true, pointStyle: 'rectRounded', padding: 16 } },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleFont: { ...chartFont, weight: 'bold' },
                            bodyFont: chartFont,
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: ctx => {
                                    if (ctx.dataset.yAxisID === 'y1') return 'Margin: ' + ctx.raw + '%';
                                    return 'Profit: $' + ctx.raw.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: chartFont } },
                        y: { position: 'left', grid: { color: gridColor }, ticks: { font: chartFont, callback: v => '$' + (v >= 1000 ? (v/1000).toFixed(0) + 'K' : v) } },
                        y1: { position: 'right', grid: { drawOnChartArea: false }, ticks: { font: chartFont, callback: v => v + '%' }, min: -100, max: 100 }
                    }
                }
            });
        }

        // ===== 3. Client Overview Bar Chart =====
        const coCtx = document.getElementById('clientOverviewChart');
        if (coCtx) {
            new Chart(coCtx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($clientNames) ?>,
                    datasets: [
                        {
                            label: 'Kapal',
                            data: <?= json_encode($clientVessels) ?>,
                            backgroundColor: 'rgba(99, 102, 241, 0.8)',
                            borderColor: '#6366f1',
                            borderWidth: 1,
                            borderRadius: 6,
                            borderSkipped: false
                        },
                        {
                            label: 'Kru',
                            data: <?= json_encode($clientCrews) ?>,
                            backgroundColor: 'rgba(16, 185, 129, 0.8)',
                            borderColor: '#10b981',
                            borderWidth: 1,
                            borderRadius: 6,
                            borderSkipped: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { font: chartFont, usePointStyle: true, pointStyle: 'rectRounded', padding: 16 } },
                        tooltip: { backgroundColor: '#1e293b', titleFont: { ...chartFont, weight: 'bold' }, bodyFont: chartFont, padding: 12, cornerRadius: 8 }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: chartFont } },
                        y: { grid: { color: gridColor }, ticks: { font: chartFont, stepSize: 1 }, beginAtZero: true }
                    }
                }
            });
        }

        // ===== 4. Client Cost Doughnut Chart =====
        const ccCtx = document.getElementById('clientCostChart');
        if (ccCtx) {
            const clientColors = <?= json_encode($clientColors) ?>;
            new Chart(ccCtx, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode($clientNames) ?>,
                    datasets: [{
                        data: <?= json_encode($clientCosts) ?>,
                        backgroundColor: clientColors.slice(0, <?= count($clientNames) ?>),
                        borderColor: '#ffffff',
                        borderWidth: 3,
                        hoverOffset: 12
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '55%',
                    plugins: {
                        legend: { position: 'bottom', labels: { font: chartFont, usePointStyle: true, pointStyle: 'circle', padding: 16 } },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleFont: { ...chartFont, weight: 'bold' },
                            bodyFont: chartFont,
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: { label: ctx => ctx.label + ': $' + ctx.raw.toLocaleString() }
                        }
                    }
                }
            });
        }
    });
    </script>

    <!-- Notification System JavaScript -->
    <script>
    function notificationSystem() {
        return {
            open: false,
            notifications: [],
            unreadCount: 0,
            init() {
                this.fetchNotifications();
                // Poll every 30 seconds
                setInterval(() => this.fetchNotifications(), 30000);
            },
            async fetchNotifications() {
                try {
                    const res = await fetch('<?= BASE_URL ?>dashboard/notifications', {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.notifications = data.data || [];
                        this.unreadCount = data.counts?.total || 0;
                    }
                } catch (e) {
                    console.warn('Notification fetch failed:', e);
                }
            }
        };
    }
    </script>

</body>
</html>