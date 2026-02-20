<?php
/**
 * Modern Payroll Detail View
 * Clean white design with modern sidebar
 */
$currentPage = 'crew-payroll';

// Compute totals from items
$totalBasic = 0; $totalGross = 0; $totalDeductions = 0; $totalTax = 0; $totalNet = 0;
if (!empty($items)) {
    foreach ($items as $item) {
        $totalBasic += $item['basic_salary'] ?? 0;
        $totalGross += $item['gross_salary'] ?? 0;
        $totalDeductions += $item['total_deductions'] ?? 0;
        $totalTax += $item['tax_amount'] ?? 0;
        $totalNet += $item['net_salary'] ?? 0;
    }
}

$statusMap = [
    'draft' => ['bg-slate-100 text-slate-600', 'edit_note'],
    'processing' => ['bg-amber-100 text-amber-700', 'sync'],
    'completed' => ['bg-emerald-100 text-emerald-700', 'check_circle'],
    'locked' => ['bg-blue-100 text-blue-700', 'lock'],
];
$statusInfo = $statusMap[$period['status'] ?? 'draft'] ?? $statusMap['draft'];
?>
<!DOCTYPE html>
<html lang="<?= session()->get('lang') ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Payroll Detail' ?> - IndoOcean ERP</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                }
            }
        };
    </script>
    <style>
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background-color: #e2e8f0; border-radius: 20px; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out forwards; }
        .animate-d1 { animation-delay: 0.05s; }
        .animate-d2 { animation-delay: 0.1s; }
        .animate-d3 { animation-delay: 0.15s; }
        .animate-d4 { animation-delay: 0.2s; }
        .animate-d5 { animation-delay: 0.25s; }
        tr { transition: background-color 0.15s ease; }
        @media print {
            aside, header { display: none !important; }
            main { margin-left: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans">
<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden ml-64">
        <!-- Header Bar -->
        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-6 flex-shrink-0 z-10">
            <div class="flex items-center gap-3">
                <a href="<?= BASE_URL ?>payroll" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-icons text-lg">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-base font-bold text-slate-800 tracking-tight"><?= __('payroll.detail_title') ?></h1>
                    <p class="text-[11px] text-slate-400">
                        <?= date('d M Y', strtotime($period['start_date'])) ?> — <?= date('d M Y', strtotime($period['end_date'])) ?>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <!-- Status Badge -->
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold <?= $statusInfo[0] ?>">
                    <span class="material-icons text-sm"><?= $statusInfo[1] ?></span>
                    <?= ucfirst($period['status'] ?? 'Draft') ?>
                </span>

                <!-- Export Button -->
                <a href="<?= BASE_URL ?>payroll/export/<?= $period['id'] ?>"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition-all shadow-sm">
                    <span class="material-icons text-sm">download</span>
                    Export CSV
                </a>

                <?php if (($period['status'] ?? '') === 'completed'): ?>
                <!-- Send Emails Button -->
                <a href="<?= BASE_URL ?>payroll/send-emails?period_id=<?= $period['id'] ?>"
                   onclick="return confirm('Kirim slip gaji ke semua crew? Proses ini akan memakan waktu.')"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-all shadow-sm">
                    <span class="material-icons text-sm">email</span>
                    <?= __('payroll.send_email') ?>
                </a>
                <?php endif; ?>
            </div>
        </header>

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto p-6">
            <!-- Flash Messages -->
            <?php if (!empty($flash)): ?>
                <?php foreach ($flash as $type => $msg): ?>
                    <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium 
                        <?= $type === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' ?>
                        <?= $type === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : '' ?>
                        <?= $type === 'warning' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' ?>">
                        <?= $msg ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Page Title -->
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-6">
                <div>
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="material-icons text-blue-600 text-2xl">account_balance_wallet</span>
                        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">
                            Payroll <?= date('F Y', strtotime($period['start_date'])) ?>
                        </h2>
                    </div>
                    <p class="text-slate-500 text-sm">Detail pembayaran gaji crew untuk periode ini.</p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                <!-- Total Crew -->
                <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm opacity-0 animate-fade-in">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-[11px] font-medium text-slate-500 uppercase tracking-wide"><?= __('payroll.total_crew') ?></p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1"><?= count($items) ?></h3>
                        </div>
                        <div class="p-2 bg-blue-50 rounded-lg">
                            <span class="material-icons text-blue-600">people</span>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center text-xs text-slate-400">
                        <span class="material-icons text-sm mr-0.5">badge</span>
                        <span><?= __('payroll.active_payroll_items') ?></span>
                    </div>
                </div>

                <!-- Total Gross -->
                <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm opacity-0 animate-fade-in animate-d1">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-[11px] font-medium text-slate-500 uppercase tracking-wide"><?= __('payroll.total_gross') ?></p>
                            <h3 class="text-xl font-bold text-slate-800 mt-1">$<?= number_format($totalGross, 0) ?></h3>
                        </div>
                        <div class="p-2 bg-emerald-50 rounded-lg">
                            <span class="material-icons text-emerald-600">payments</span>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center text-xs text-emerald-500">
                        <span class="material-icons text-sm mr-0.5">trending_up</span>
                        <span>Gaji kotor keseluruhan</span>
                    </div>
                </div>

                <!-- Total Deductions -->
                <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm opacity-0 animate-fade-in animate-d2">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-[11px] font-medium text-slate-500 uppercase tracking-wide">Deductions</p>
                            <h3 class="text-xl font-bold text-red-600 mt-1">-$<?= number_format($totalDeductions, 0) ?></h3>
                        </div>
                        <div class="p-2 bg-red-50 rounded-lg">
                            <span class="material-icons text-red-500">remove_circle</span>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center text-xs text-slate-400">
                        <span class="material-icons text-sm mr-0.5">info</span>
                        <span>Potongan gaji</span>
                    </div>
                </div>

                <!-- Total Tax -->
                <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm opacity-0 animate-fade-in animate-d3">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-[11px] font-medium text-slate-500 uppercase tracking-wide">Tax</p>
                            <h3 class="text-xl font-bold text-amber-600 mt-1">-$<?= number_format($totalTax, 0) ?></h3>
                        </div>
                        <div class="p-2 bg-amber-50 rounded-lg">
                            <span class="material-icons text-amber-600">receipt</span>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center text-xs text-slate-400">
                        <span class="material-icons text-sm mr-0.5">percent</span>
                        <span>Pajak penghasilan</span>
                    </div>
                </div>

                <!-- Total Net -->
                <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm opacity-0 animate-fade-in animate-d4">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-[11px] font-medium text-slate-500 uppercase tracking-wide"><?= __('payroll.net_salary') ?></p>
                            <h3 class="text-xl font-bold text-blue-700 mt-1">$<?= number_format($totalNet, 0) ?></h3>
                        </div>
                        <div class="p-2 bg-blue-50 rounded-lg">
                            <span class="material-icons text-blue-600">account_balance</span>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center text-xs text-blue-500">
                        <span class="material-icons text-sm mr-0.5">paid</span>
                        <span>Total dibayarkan</span>
                    </div>
                </div>
            </div>

            <!-- Summary by Vessel -->
            <?php if (!empty($summary)): ?>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-6 opacity-0 animate-fade-in animate-d4">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-icons text-blue-600 text-lg">directions_boat</span>
                        <h3 class="text-sm font-bold text-slate-800"><?= __('payroll.summary_by_vessel') ?></h3>
                    </div>
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">
                        <?= count($summary) ?> vessels
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Vessel</th>
                                <th class="px-5 py-2.5 text-center text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Crew</th>
                                <th class="px-5 py-2.5 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Gross</th>
                                <th class="px-5 py-2.5 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Deductions</th>
                                <th class="px-5 py-2.5 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Tax</th>
                                <th class="px-5 py-2.5 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Net</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($summary as $s): ?>
                            <tr class="hover:bg-blue-50/40 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                                            <span class="material-icons text-white text-sm">directions_boat</span>
                                        </div>
                                        <span class="text-sm font-semibold text-slate-800"><?= htmlspecialchars($s['vessel_name'] ?? 'Unknown') ?></span>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-100 text-xs font-bold text-slate-600">
                                        <?= $s['crew_count'] ?>
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right text-sm font-medium text-slate-700">$<?= number_format($s['total_gross'], 2) ?></td>
                                <td class="px-5 py-3 text-right text-sm font-medium text-red-500">-$<?= number_format($s['total_deductions'], 2) ?></td>
                                <td class="px-5 py-3 text-right text-sm font-medium text-amber-600">-$<?= number_format($s['total_tax'], 2) ?></td>
                                <td class="px-5 py-3 text-right text-sm font-bold text-blue-700">$<?= number_format($s['total_net'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50 border-t border-slate-200">
                                <td class="px-5 py-3 text-sm font-bold text-slate-700">Total</td>
                                <td class="px-5 py-3 text-center text-sm font-bold text-slate-700"><?= count($items) ?></td>
                                <td class="px-5 py-3 text-right text-sm font-bold text-slate-700">$<?= number_format($totalGross, 2) ?></td>
                                <td class="px-5 py-3 text-right text-sm font-bold text-red-600">-$<?= number_format($totalDeductions, 2) ?></td>
                                <td class="px-5 py-3 text-right text-sm font-bold text-amber-600">-$<?= number_format($totalTax, 2) ?></td>
                                <td class="px-5 py-3 text-right text-sm font-bold text-blue-700">$<?= number_format($totalNet, 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Payroll Detail Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in animate-d5" x-data="payrollTable()">
                <!-- Search & Filter -->
                <div class="px-5 py-3 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-3">
                    <div class="flex items-center gap-2">
                        <span class="material-icons text-blue-600 text-lg">list_alt</span>
                        <h3 class="text-sm font-bold text-slate-800"><?= __('payroll.detail_title') ?></h3>
                    </div>
                    <div class="flex gap-2">
                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="material-icons text-slate-400 text-lg">search</span>
                            </div>
                            <input type="text" x-model="search" @input="filterRows()"
                                   class="block w-full pl-9 pr-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-colors"
                                   placeholder="Cari nama crew...">
                        </div>
                        <select x-model="vesselFilter" @change="filterRows()"
                                class="px-3 py-2 border border-slate-200 rounded-lg text-xs font-medium text-slate-600 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                            <option value="">Semua Vessel</option>
                            <?php 
                            $vesselNames = array_unique(array_column($items, 'vessel_name'));
                            sort($vesselNames);
                            foreach ($vesselNames as $vn): ?>
                                <option value="<?= htmlspecialchars($vn) ?>"><?= htmlspecialchars($vn) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider w-8">#</th>
                                <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Crew Name</th>
                                <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Rank</th>
                                <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Vessel</th>
                                <th class="px-5 py-2.5 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Basic</th>
                                <th class="px-5 py-2.5 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Gross</th>
                                <th class="px-5 py-2.5 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Deductions</th>
                                <th class="px-5 py-2.5 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Tax</th>
                                <th class="px-5 py-2.5 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Net Salary</th>
                                <th class="px-5 py-2.5 text-center text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="10" class="py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="bg-slate-100 p-5 rounded-full mb-4">
                                            <span class="material-icons text-4xl text-slate-300">receipt_long</span>
                                        </div>
                                        <h3 class="text-base font-semibold text-slate-700 mb-1">Belum ada data payroll</h3>
                                        <p class="text-slate-400 text-sm">Silakan proses payroll terlebih dahulu untuk periode ini.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($items as $idx => $item): ?>
                                <tr class="hover:bg-blue-50/40 transition-colors group payroll-row"
                                    data-name="<?= strtolower(htmlspecialchars($item['crew_name'] ?? '')) ?>"
                                    data-vessel="<?= htmlspecialchars($item['vessel_name'] ?? '') ?>">
                                    <td class="px-5 py-3 text-xs text-slate-400 font-medium"><?= $idx + 1 ?></td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                                <?= strtoupper(substr($item['crew_name'] ?? 'C', 0, 1)) ?>
                                            </div>
                                            <span class="text-sm font-semibold text-slate-800"><?= htmlspecialchars($item['crew_name'] ?? '-') ?></span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 text-sm text-slate-600"><?= htmlspecialchars($item['rank_name'] ?? '-') ?></td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center gap-1 text-sm text-slate-600">
                                            <span class="material-icons text-[14px] text-slate-400">directions_boat</span>
                                            <?= htmlspecialchars($item['vessel_name'] ?? '-') ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-right text-sm text-slate-600 tabular-nums">$<?= number_format($item['basic_salary'], 2) ?></td>
                                    <td class="px-5 py-3 text-right text-sm font-medium text-slate-700 tabular-nums">$<?= number_format($item['gross_salary'], 2) ?></td>
                                    <td class="px-5 py-3 text-right text-sm font-medium text-red-500 tabular-nums">-$<?= number_format($item['total_deductions'], 2) ?></td>
                                    <td class="px-5 py-3 text-right text-sm font-medium text-amber-600 tabular-nums">-$<?= number_format($item['tax_amount'], 2) ?></td>
                                    <td class="px-5 py-3 text-right text-sm font-bold text-blue-700 tabular-nums">$<?= number_format($item['net_salary'], 2) ?></td>
                                    <td class="px-5 py-3 text-center">
                                        <?php
                                        $itemStatus = $item['status'] ?? 'pending';
                                        $itemStatusClasses = [
                                            'paid' => 'bg-emerald-100 text-emerald-700',
                                            'pending' => 'bg-orange-100 text-orange-700',
                                            'processing' => 'bg-blue-100 text-blue-700',
                                        ];
                                        ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold <?= $itemStatusClasses[$itemStatus] ?? 'bg-slate-100 text-slate-600' ?>">
                                            <?= ucfirst($itemStatus) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer -->
                <?php if (!empty($items)): ?>
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50 flex justify-between items-center">
                    <p class="text-xs text-slate-400">
                        Menampilkan <span class="font-semibold text-slate-600" x-text="visibleCount"><?= count($items) ?></span>
                        dari <span class="font-semibold text-slate-600"><?= count($items) ?></span> crew
                    </p>
                    <p class="text-xs text-slate-400">
                        <span class="material-icons text-[12px] align-middle">schedule</span>
                        Period: <?= date('M Y', strtotime($period['start_date'])) ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Footer -->
            <div class="mt-6 text-center">
                <p class="text-xs text-slate-400">© <?= date('Y') ?> IndoOcean ERP System. All rights reserved.</p>
            </div>
        </div>
    </main>
</div>

<script>
function payrollTable() {
    return {
        search: '',
        vesselFilter: '',
        visibleCount: <?= count($items) ?>,

        filterRows() {
            const rows = document.querySelectorAll('.payroll-row');
            let count = 0;
            const searchLower = this.search.toLowerCase();

            rows.forEach(row => {
                const name = row.dataset.name || '';
                const vessel = row.dataset.vessel || '';

                const matchSearch = !searchLower || name.includes(searchLower);
                const matchVessel = !this.vesselFilter || vessel === this.vesselFilter;

                if (matchSearch && matchVessel) {
                    row.style.display = '';
                    count++;
                } else {
                    row.style.display = 'none';
                }
            });

            this.visibleCount = count;
        }
    };
}
</script>
</body>
</html>
