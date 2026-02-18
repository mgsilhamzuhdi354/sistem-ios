<?php
/**
 * Modern Reports Overview
 * Clean white design with modern sidebar
 */
$currentPage = 'reports';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Reports Center' ?> - IndoOcean ERP</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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
        <!-- Header -->
        <header class="h-14 bg-white border-b border-slate-200 flex items-center px-6 flex-shrink-0">
            <div>
                <h1 class="text-base font-bold text-slate-800 tracking-tight">Reports Center</h1>
                <p class="text-[11px] text-slate-400">Generate and download reports</p>
            </div>
        </header>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-6">
            <div class="flex items-center gap-2 mb-6">
                <span class="material-icons text-blue-600 text-2xl">assessment</span>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Available Reports</h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Contract Reports -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                        <div class="p-2 bg-blue-50 rounded-lg"><span class="material-icons text-blue-600">description</span></div>
                        <h3 class="text-sm font-bold text-slate-800">Contract Reports</h3>
                    </div>
                    <div class="divide-y divide-slate-100">
                        <div class="px-5 py-4 flex items-center justify-between hover:bg-blue-50/40 transition-colors">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Active Contracts</p>
                                <p class="text-xs text-slate-400 mt-0.5">List of all currently active contracts</p>
                            </div>
                            <div class="flex gap-2">
                                <a href="<?= BASE_URL ?>reports/activeContracts" class="p-2 rounded-lg bg-slate-100 hover:bg-blue-100 text-slate-500 hover:text-blue-600 transition-colors">
                                    <span class="material-icons text-lg">visibility</span>
                                </a>
                                <a href="<?= BASE_URL ?>reports/export/active" class="p-2 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 transition-colors">
                                    <span class="material-icons text-lg">download</span>
                                </a>
                            </div>
                        </div>
                        <div class="px-5 py-4 flex items-center justify-between hover:bg-blue-50/40 transition-colors">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Expiring Contracts</p>
                                <p class="text-xs text-slate-400 mt-0.5">Contracts expiring within 60 days</p>
                            </div>
                            <div class="flex gap-2">
                                <a href="<?= BASE_URL ?>reports/expiringContracts" class="p-2 rounded-lg bg-slate-100 hover:bg-blue-100 text-slate-500 hover:text-blue-600 transition-colors">
                                    <span class="material-icons text-lg">visibility</span>
                                </a>
                                <a href="<?= BASE_URL ?>reports/export/expiring" class="p-2 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 transition-colors">
                                    <span class="material-icons text-lg">download</span>
                                </a>
                            </div>
                        </div>
                        <div class="px-5 py-4 flex items-center justify-between hover:bg-blue-50/40 transition-colors">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Contracts by Vessel</p>
                                <p class="text-xs text-slate-400 mt-0.5">Contract breakdown per vessel</p>
                            </div>
                            <a href="<?= BASE_URL ?>reports/by-vessel" class="p-2 rounded-lg bg-slate-100 hover:bg-blue-100 text-slate-500 hover:text-blue-600 transition-colors">
                                <span class="material-icons text-lg">visibility</span>
                            </a>
                        </div>
                        <div class="px-5 py-4 flex items-center justify-between hover:bg-blue-50/40 transition-colors">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Contracts by Client</p>
                                <p class="text-xs text-slate-400 mt-0.5">Contract summary per client/principal</p>
                            </div>
                            <a href="<?= BASE_URL ?>reports/by-client" class="p-2 rounded-lg bg-slate-100 hover:bg-blue-100 text-slate-500 hover:text-blue-600 transition-colors">
                                <span class="material-icons text-lg">visibility</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Payroll Reports -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in animate-d1">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                        <div class="p-2 bg-emerald-50 rounded-lg"><span class="material-icons text-emerald-600">account_balance_wallet</span></div>
                        <h3 class="text-sm font-bold text-slate-800">Payroll Reports</h3>
                    </div>
                    <div class="divide-y divide-slate-100">
                        <div class="px-5 py-4 flex items-center justify-between hover:bg-blue-50/40 transition-colors">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Monthly Payroll Summary</p>
                                <p class="text-xs text-slate-400 mt-0.5">Complete payroll breakdown per month</p>
                            </div>
                            <a href="<?= BASE_URL ?>reports/payroll-summary" class="p-2 rounded-lg bg-slate-100 hover:bg-blue-100 text-slate-500 hover:text-blue-600 transition-colors">
                                <span class="material-icons text-lg">visibility</span>
                            </a>
                        </div>
                        <div class="px-5 py-4 flex items-center justify-between hover:bg-blue-50/40 transition-colors">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Tax Report (PPh 21)</p>
                                <p class="text-xs text-slate-400 mt-0.5">Tax deduction report for tax filing</p>
                            </div>
                            <a href="<?= BASE_URL ?>reports/taxReport" class="p-2 rounded-lg bg-slate-100 hover:bg-blue-100 text-slate-500 hover:text-blue-600 transition-colors">
                                <span class="material-icons text-lg">visibility</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Employee Reports -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in animate-d2">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                        <div class="p-2 bg-amber-50 rounded-lg"><span class="material-icons text-amber-600">people</span></div>
                        <h3 class="text-sm font-bold text-slate-800">Employee Reports</h3>
                        <span class="px-1.5 py-0.5 text-[9px] font-bold bg-emerald-100 text-emerald-600 rounded-full">HRIS</span>
                    </div>
                    <div class="divide-y divide-slate-100">
                        <div class="px-5 py-4 flex items-center justify-between hover:bg-blue-50/40 transition-colors">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Employee Data</p>
                                <p class="text-xs text-slate-400 mt-0.5">Employee report from HRIS integration</p>
                            </div>
                            <a href="<?= BASE_URL ?>reports/employees" class="p-2 rounded-lg bg-slate-100 hover:bg-blue-100 text-slate-500 hover:text-blue-600 transition-colors">
                                <span class="material-icons text-lg">visibility</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Audit Reports -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in animate-d3">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                        <div class="p-2 bg-red-50 rounded-lg"><span class="material-icons text-red-500">security</span></div>
                        <h3 class="text-sm font-bold text-slate-800">Audit & Compliance</h3>
                    </div>
                    <div class="divide-y divide-slate-100">
                        <div class="px-5 py-4 flex items-center justify-between hover:bg-blue-50/40 transition-colors">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Contract Change Log</p>
                                <p class="text-xs text-slate-400 mt-0.5">Audit trail of all contract changes</p>
                            </div>
                            <a href="<?= BASE_URL ?>reports/auditLog" class="p-2 rounded-lg bg-slate-100 hover:bg-blue-100 text-slate-500 hover:text-blue-600 transition-colors">
                                <span class="material-icons text-lg">visibility</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 text-center">
                <p class="text-xs text-slate-400">© <?= date('Y') ?> IndoOcean ERP System</p>
            </div>
        </div>
    </main>
</div>
</body>
</html>
