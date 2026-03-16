<?php
/**
 * Modern Monitoring Dashboard View
 * PT Indo Ocean - ERP System
 */
$currentPage = 'monitoring';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?= $title ?? 'Monitoring Dashboard' ?> - PT Indo Ocean</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { blue: "#1e40af", gold: "#fbbf24", dark: "#0f172a" },
                        surface: { DEFAULT: "#ffffff", subtle: "#f8fafc" }
                    },
                    fontFamily: { sans: ["Inter", "sans-serif"] },
                    boxShadow: {
                        'soft': '0 2px 15px -3px rgba(0,0,0,0.07), 0 10px 20px -2px rgba(0,0,0,0.04)',
                        'card': '0 0 0 1px rgba(0,0,0,0.03), 0 2px 8px rgba(0,0,0,0.04)',
                    }
                }
            }
        };
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        [x-cloak] { display: none !important; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeInUp .4s ease-out forwards; }
        .animate-d1 { animation-delay: .05s; }
        .animate-d2 { animation-delay: .1s; }
        .animate-d3 { animation-delay: .15s; }
        .animate-d4 { animation-delay: .2s; }
        @keyframes pulse-dot { 0%, 100% { opacity: 1; } 50% { opacity: .4; } }
        .pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }
    </style>
</head>
<body class="bg-surface-subtle text-slate-600 antialiased">
    <div class="flex h-screen overflow-hidden">
        <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

        <main class="ml-64 flex-1 flex flex-col min-w-0 overflow-hidden bg-surface-subtle">
            <!-- Header -->
            <header class="h-16 flex items-center justify-between px-8 bg-white/80 backdrop-blur-xl border-b border-slate-200/60 z-10 sticky top-0 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 rounded-lg">
                        <span class="material-icons-round text-indigo-600">monitoring</span>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-slate-800">Monitoring Dashboard</h1>
                        <p class="text-xs text-slate-400">Central monitoring untuk semua sistem</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-400">Last updated: <?= date('H:i:s') ?></span>
                    <button class="p-2 bg-white border border-slate-200 text-slate-500 hover:text-brand-blue rounded-lg shadow-sm transition-colors" onclick="location.reload()">
                        <span class="material-icons-round">refresh</span>
                    </button>
                </div>
            </header>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto custom-scrollbar p-8 space-y-6">

                <!-- KPI Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 opacity-0 animate-fade-in">
                    <!-- Visitors Today -->
                    <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-soft hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-slate-500 text-sm font-medium mb-1">Visitors Hari Ini</p>
                                <h3 class="text-3xl font-bold text-slate-800"><?= $stats['visitors_today'] ?? 0 ?></h3>
                            </div>
                            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                                <span class="material-icons-round">visibility</span>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center gap-2">
                            <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-md flex items-center gap-1">
                                <span class="material-icons-round text-[14px]">language</span> Company Profile
                            </span>
                        </div>
                    </div>

                    <!-- Visitors Month -->
                    <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-soft hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-slate-500 text-sm font-medium mb-1">Visitors Bulan Ini</p>
                                <h3 class="text-3xl font-bold text-slate-800"><?= $stats['visitors_month'] ?? 0 ?></h3>
                            </div>
                            <div class="p-3 bg-amber-50 text-amber-500 rounded-xl">
                                <span class="material-icons-round">groups</span>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center gap-2">
                            <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-md">30 Hari Terakhir</span>
                        </div>
                    </div>

                    <!-- Active Applications -->
                    <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-soft hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-slate-500 text-sm font-medium mb-1">Aplikasi Aktif</p>
                                <h3 class="text-3xl font-bold text-slate-800"><?= $stats['recruitment_active'] ?? 0 ?></h3>
                            </div>
                            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                                <span class="material-icons-round">person_search</span>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center gap-2">
                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md flex items-center gap-1">
                                <span class="material-icons-round text-[14px]">trending_up</span> Recruitment
                            </span>
                        </div>
                    </div>

                    <!-- Pending Approval -->
                    <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-soft hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-slate-500 text-sm font-medium mb-1">Pending Approval</p>
                                <h3 class="text-3xl font-bold text-slate-800"><?= $stats['pending_approvals'] ?? 0 ?></h3>
                            </div>
                            <div class="p-3 bg-red-50 text-red-500 rounded-xl">
                                <span class="material-icons-round">pending_actions</span>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center gap-2">
                            <?php if (($stats['pending_approvals'] ?? 0) > 0): ?>
                                <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-1 rounded-md flex items-center gap-1">
                                    <span class="material-icons-round text-[14px]">priority_high</span> Perlu Ditinjau
                                </span>
                            <?php else: ?>
                                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md">Semua Selesai</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Integration Status & Quick Links -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 opacity-0 animate-fade-in animate-d1">
                    <!-- Integration Status Card -->
                    <div class="bg-white rounded-xl border border-slate-100 shadow-soft overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="material-icons-round text-indigo-500">hub</span>
                                <h4 class="font-bold text-slate-800">Integration Status</h4>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-2.5 h-2.5 rounded-full <?= ($stats['integration_status']['active'] ?? 0) == ($stats['integration_status']['total'] ?? 0) ? 'bg-emerald-500 pulse-dot' : 'bg-amber-500 pulse-dot' ?>"></div>
                                <span class="text-sm font-bold <?= ($stats['integration_status']['active'] ?? 0) == ($stats['integration_status']['total'] ?? 0) ? 'text-emerald-600' : 'text-amber-600' ?>">
                                    <?= $stats['integration_status']['active'] ?? 0 ?> / <?= $stats['integration_status']['total'] ?? 0 ?> Connected
                                </span>
                            </div>
                        </div>
                        <div class="p-6 space-y-4">
                            <!-- HRIS -->
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                                        <span class="material-icons-round text-indigo-600">badge</span>
                                    </div>
                                    <div>
                                        <h5 class="font-semibold text-slate-800">HRIS Absensi</h5>
                                        <p class="text-xs text-slate-500">Attendance & Employee Management</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1.5 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full flex items-center gap-1.5">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500 pulse-dot"></div> Connected
                                </span>
                            </div>

                            <!-- Recruitment -->
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center">
                                        <span class="material-icons-round text-pink-600">group_add</span>
                                    </div>
                                    <div>
                                        <h5 class="font-semibold text-slate-800">Recruitment System</h5>
                                        <p class="text-xs text-slate-500">Pipeline & Candidate Management</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1.5 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full flex items-center gap-1.5">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500 pulse-dot"></div> Connected
                                </span>
                            </div>

                            <!-- Company Profile -->
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                        <span class="material-icons-round text-blue-600">language</span>
                                    </div>
                                    <div>
                                        <h5 class="font-semibold text-slate-800">Company Profile</h5>
                                        <p class="text-xs text-slate-500">Visitor Tracking & Analytics</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1.5 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full flex items-center gap-1.5">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500 pulse-dot"></div> Active
                                </span>
                            </div>

                            <!-- Finance -->
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                                        <span class="material-icons-round text-amber-600">account_balance</span>
                                    </div>
                                    <div>
                                        <h5 class="font-semibold text-slate-800">Finance & Payroll</h5>
                                        <p class="text-xs text-slate-500">Payroll Processing & Reports</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1.5 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full flex items-center gap-1.5">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500 pulse-dot"></div> Active
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Links & System Health -->
                    <div class="space-y-6">
                        <!-- Quick Links -->
                        <div class="bg-white rounded-xl border border-slate-100 shadow-soft overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                                <span class="material-icons-round text-amber-500">bolt</span>
                                <h4 class="font-bold text-slate-800">Quick Links</h4>
                            </div>
                            <div class="p-4 grid grid-cols-2 gap-3">
                                <a href="<?= BASE_URL ?>monitoring/visitors" class="flex items-center gap-3 p-4 bg-blue-50 hover:bg-blue-100 rounded-xl transition-all group">
                                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm">
                                        <span class="material-icons-round text-blue-600">visibility</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm text-slate-800">Visitor Tracking</p>
                                        <p class="text-xs text-slate-500">Lihat detail visitor</p>
                                    </div>
                                </a>
                                <a href="<?= BASE_URL ?>monitoring/activity" class="flex items-center gap-3 p-4 bg-purple-50 hover:bg-purple-100 rounded-xl transition-all group">
                                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm">
                                        <span class="material-icons-round text-purple-600">history</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm text-slate-800">Activity Logs</p>
                                        <p class="text-xs text-slate-500">Riwayat aktivitas</p>
                                    </div>
                                </a>
                                <a href="<?= BASE_URL ?>monitoring/integration" class="flex items-center gap-3 p-4 bg-emerald-50 hover:bg-emerald-100 rounded-xl transition-all group">
                                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm">
                                        <span class="material-icons-round text-emerald-600">hub</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm text-slate-800">Integration</p>
                                        <p class="text-xs text-slate-500">Status semua sistem</p>
                                    </div>
                                </a>
                                <a href="<?= BASE_URL ?>recruitment/pipeline" class="flex items-center gap-3 p-4 bg-pink-50 hover:bg-pink-100 rounded-xl transition-all group">
                                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm">
                                        <span class="material-icons-round text-pink-600">group_add</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm text-slate-800">Recruitment</p>
                                        <p class="text-xs text-slate-500">Pipeline kandidat</p>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- System Health -->
                        <div class="bg-white rounded-xl border border-slate-100 shadow-soft overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="material-icons-round text-emerald-500">health_and_safety</span>
                                    <h4 class="font-bold text-slate-800">System Health</h4>
                                </div>
                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">All Systems OK</span>
                            </div>
                            <div class="p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-slate-600">Database</span>
                                    <div class="flex items-center gap-2">
                                        <div class="w-24 h-2 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="w-[15%] h-full bg-emerald-500 rounded-full"></div>
                                        </div>
                                        <span class="text-xs font-bold text-emerald-600">OK</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-slate-600">Web Server</span>
                                    <div class="flex items-center gap-2">
                                        <div class="w-24 h-2 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="w-[22%] h-full bg-emerald-500 rounded-full"></div>
                                        </div>
                                        <span class="text-xs font-bold text-emerald-600">OK</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-slate-600">WhatsApp API</span>
                                    <div class="flex items-center gap-2">
                                        <div class="w-24 h-2 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="w-[8%] h-full bg-emerald-500 rounded-full"></div>
                                        </div>
                                        <span class="text-xs font-bold text-emerald-600">OK</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-slate-600">Email SMTP</span>
                                    <div class="flex items-center gap-2">
                                        <div class="w-24 h-2 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="w-[5%] h-full bg-emerald-500 rounded-full"></div>
                                        </div>
                                        <span class="text-xs font-bold text-emerald-600">OK</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center text-xs text-slate-400 py-4">
                    &copy; <?= date('Y') ?> IndoOcean ERP System. All rights reserved.
                </div>
            </div>
        </main>
    </div>
</body>
</html>
