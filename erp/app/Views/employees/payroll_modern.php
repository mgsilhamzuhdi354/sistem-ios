<?php
/**
 * Modern Payroll Dashboard View
 * PT Indo Ocean - ERP System
 */
$currentPage = $currentPage ?? 'payroll';
$months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$monthsEn = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

// Ensure month and year are integers
$month = isset($month) ? (int) $month : (int) date('m');
$year = isset($year) ? (int) $year : (int) date('Y');

// Validate month range
if ($month < 1 || $month > 12) {
    $month = (int) date('m');
}

// Calculate totals
$totalEmployees = 0;
$totalGajiPokok = 0;
$totalTunjangan = 0;
$totalLembur = 0;
$totalPotongan = 0;
$totalGaji = 0;

if (!empty($payrollData)) {
    $totalEmployees = count($payrollData);
    foreach ($payrollData as $row) {
        $gajiPokok = $row['gaji_pokok'] ?? 0;
        $tunjangan = $row['tunjangan'] ?? 0;
        $lembur = $row['lembur'] ?? 0;
        $potongan = $row['potongan'] ?? 0;
        
        $totalGajiPokok += $gajiPokok;
        $totalTunjangan += $tunjangan;
        $totalLembur += $lembur;
        $totalPotongan += $potongan;
        $totalGaji += ($gajiPokok + $tunjangan + $lembur - $potongan);
    }
}

// Avatar colors
$avatarColors = [
    'bg-slate-200 text-slate-600',
    'bg-indigo-100 text-indigo-600',
    'bg-pink-100 text-pink-600',
    'bg-blue-100 text-blue-600',
    'bg-purple-100 text-purple-600',
    'bg-orange-100 text-orange-600',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Karyawan | IndoOcean ERP</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#F59E0B",
                        secondary: "#1E293B",
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'float-delayed': 'float 6s ease-in-out 3s infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'slide-up': 'slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                    }
                },
            },
        };
    </script>

    <style type="text/tailwindcss">
        @layer utilities {
            .glass-card {
                @apply bg-white/80 backdrop-blur-md border border-white/40 shadow-[0_8px_30px_rgb(0,0,0,0.04)];
            }
            .glass-card:hover {
                @apply bg-white/90 shadow-[0_8px_30px_rgb(245,158,11,0.1)] border-primary/20;
            }
            .table-row-animate {
                @apply opacity-0 animate-slide-up;
            }
            .delay-100 { animation-delay: 100ms; }
            .delay-200 { animation-delay: 200ms; }
            .delay-300 { animation-delay: 300ms; }
            .delay-400 { animation-delay: 400ms; }
            .delay-500 { animation-delay: 500ms; }
            .delay-600 { animation-delay: 600ms; }
            .delay-700 { animation-delay: 700ms; }
            .delay-800 { animation-delay: 800ms; }
        }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased overflow-hidden">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="ml-64 flex-1 flex flex-col h-screen overflow-hidden relative bg-slate-50">
            <!-- Animated Background -->
            <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-b from-white to-slate-50 -z-10"></div>
            <div class="absolute top-[-10%] right-[-5%] w-96 h-96 bg-primary/5 rounded-full blur-3xl animate-float"></div>
            <div class="absolute top-[20%] left-[10%] w-64 h-64 bg-blue-400/5 rounded-full blur-3xl animate-float-delayed"></div>

            <!-- Header -->
            <header class="h-20 bg-white/70 backdrop-blur-md border-b border-slate-200/60 flex items-center justify-between px-8 z-20 sticky top-0">
                <nav aria-label="Breadcrumb" class="flex">
                    <ol class="flex items-center space-x-2">
                        <li><a class="text-slate-400 hover:text-primary transition-colors" href="<?= BASE_URL ?>"><span class="material-symbols-outlined text-[20px]">home</span></a></li>
                        <li><span class="text-slate-300">/</span></li>
                        <li><a class="text-sm font-medium text-slate-500 hover:text-slate-800 transition-colors" href="<?= BASE_URL ?>employees">Employees</a></li>
                        <li><span class="text-slate-300">/</span></li>
                        <li><span aria-current="page" class="text-sm font-semibold text-slate-800 bg-slate-100 px-2 py-0.5 rounded-md">Payroll Karyawan</span></li>
                    </ol>
                </nav>
                <div class="flex items-center space-x-6">
                    <button class="relative p-2 text-slate-400 hover:text-primary transition-colors group">
                        <span class="material-symbols-outlined group-hover:animate-pulse">notifications</span>
                    </button>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-8 relative scroll-smooth">
                <!-- Page Header -->
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10 animate-slide-up">
                    <div>
                        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                            Payroll Karyawan
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                Active Period
                            </span>
                        </h1>
                        <p class="mt-2 text-sm text-slate-500 font-medium">Data payroll karyawan dari sistem HRIS Absensi.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="<?= BASE_URL ?>employees" class="inline-flex items-center px-5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 shadow-sm hover:bg-slate-50 hover:border-slate-300 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-300 transform hover:-translate-y-0.5">
                            <span class="material-symbols-outlined mr-2 text-sm">arrow_back</span>
                            Back
                        </a>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    <div class="glass-card p-6 rounded-2xl animate-float delay-100">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl shadow-sm ring-1 ring-blue-100">
                                <span class="material-symbols-outlined animate-pulse-slow">group</span>
                            </div>
                            <span class="text-xs font-bold px-2 py-1 bg-green-50 text-green-600 rounded-lg flex items-center">
                                <span class="material-symbols-outlined text-[14px] mr-1">trending_up</span> 0%
                            </span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500 mb-1">Total Employees</p>
                            <p class="text-3xl font-bold text-slate-800 tracking-tight"><?= $totalEmployees ?></p>
                        </div>
                    </div>
                    <div class="glass-card p-6 rounded-2xl animate-float delay-300">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl shadow-sm ring-1 ring-indigo-100">
                                <span class="material-symbols-outlined animate-pulse-slow">account_balance_wallet</span>
                            </div>
                            <span class="text-xs font-medium text-slate-400"><?= $monthsEn[$month] ?> <?= $year ?></span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500 mb-1">Total Gross Salary</p>
                            <p class="text-3xl font-bold text-slate-800 tracking-tight">Rp <?= number_format($totalGajiPokok, 0, ',', '.') ?></p>
                        </div>
                    </div>
                    <div class="glass-card p-6 rounded-2xl animate-float delay-500">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-red-50 text-red-600 rounded-xl shadow-sm ring-1 ring-red-100">
                                <span class="material-symbols-outlined animate-pulse-slow">remove_circle_outline</span>
                            </div>
                            <span class="text-xs font-medium text-slate-400">Inc. PPH21</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500 mb-1">Total Deductions</p>
                            <p class="text-3xl font-bold text-red-500 tracking-tight">Rp <?= number_format($totalPotongan, 0, ',', '.') ?></p>
                        </div>
                    </div>
                    <div class="glass-card p-6 rounded-2xl animate-float delay-700 ring-2 ring-green-500/20 relative overflow-hidden">
                        <div class="absolute -right-10 -top-10 w-32 h-32 bg-green-400/10 rounded-full blur-2xl"></div>
                        <div class="flex items-center justify-between mb-4 relative z-10">
                            <div class="p-3 bg-green-50 text-green-600 rounded-xl shadow-sm ring-1 ring-green-100">
                                <span class="material-symbols-outlined animate-pulse-slow">payments</span>
                            </div>
                            <span class="inline-flex relative h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                            </span>
                        </div>
                        <div class="relative z-10">
                            <p class="text-sm font-medium text-slate-500 mb-1">Net Payroll</p>
                            <p class="text-3xl font-bold text-green-600 tracking-tight">Rp <?= number_format($totalGaji, 0, ',', '.') ?></p>
                            <div class="mt-2 flex items-center text-xs text-green-600 font-semibold bg-green-50 w-fit px-2 py-1 rounded-md">
                                <span class="material-symbols-outlined text-[14px] mr-1">check_circle</span> Disbursed
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl border border-slate-200 p-6 mb-8 shadow-sm animate-slide-up delay-100">
                    <form method="GET" action="<?= BASE_URL ?>employees/payroll" class="flex flex-col md:flex-row md:items-end gap-5">
                        <div class="w-full md:w-1/4 group">
                            <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide group-focus-within:text-primary transition-colors">Month</label>
                            <div class="relative">
                                <select name="bulan" class="w-full appearance-none pl-4 pr-10 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-primary focus:border-transparent focus:bg-white outline-none transition-all cursor-pointer hover:border-primary/50">
                                    <?php for ($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?= $m ?>" <?= ($month == $m) ? 'selected' : '' ?>><?= $monthsEn[$m] ?></option>
                                    <?php endfor; ?>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                                    <span class="material-symbols-outlined text-sm">expand_more</span>
                                </div>
                            </div>
                        </div>
                        <div class="w-full md:w-1/4 group">
                            <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide group-focus-within:text-primary transition-colors">Year</label>
                            <div class="relative">
                                <select name="tahun" class="w-full appearance-none pl-4 pr-10 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-primary focus:border-transparent focus:bg-white outline-none transition-all cursor-pointer hover:border-primary/50">
                                    <?php for ($y = date('Y'); $y >= date('Y') - 2; $y--): ?>
                                        <option value="<?= $y ?>" <?= ($year == $y) ? 'selected' : '' ?>><?= $y ?></option>
                                    <?php endfor; ?>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                                    <span class="material-symbols-outlined text-sm">expand_more</span>
                                </div>
                            </div>
                        </div>
                        <div class="w-full md:flex-1 group">
                            <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide group-focus-within:text-primary transition-colors">Search Employee</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="material-symbols-outlined text-slate-400 text-[20px] group-focus-within:text-primary transition-colors">search</span>
                                </div>
                                <input id="searchInput" onkeyup="filterTable()" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 placeholder-slate-400 focus:ring-2 focus:ring-primary focus:border-transparent focus:bg-white outline-none transition-all hover:border-primary/50"
                                    placeholder="Search by name or NIK..." type="text">
                            </div>
                        </div>
                        <div class="w-full md:w-auto">
                            <button type="submit" class="w-full md:w-auto flex items-center justify-center px-8 py-3 bg-slate-800 text-white font-bold text-sm rounded-xl shadow-lg hover:shadow-xl hover:bg-slate-900 transition-all transform hover:-translate-y-0.5 focus:ring-2 focus:ring-offset-2 focus:ring-slate-800">
                                <span class="material-symbols-outlined text-sm mr-2">filter_list</span>
                                Filter
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Payroll Table -->
                <?php if (!$success): ?>
                    <!-- Error State -->
                    <div class="bg-white rounded-2xl border border-red-200 p-12 text-center">
                        <div class="inline-block p-4 bg-red-50 rounded-full mb-4">
                            <span class="material-symbols-outlined text-5xl text-red-500">error</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Gagal Memuat Data</h3>
                        <p class="text-slate-500">Tidak dapat terhubung ke sistem HRIS untuk mengambil data payroll.</p>
                    </div>
                <?php elseif (empty($payrollData)): ?>
                    <!-- Empty State -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
                        <div class="inline-block p-4 bg-slate-50 rounded-full mb-4">
                            <span class="material-symbols-outlined text-5xl text-slate-400">payments</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Tidak Ada Data Payroll</h3>
                        <p class="text-slate-500">Belum ada data payroll untuk periode <?= $months[$month] ?> <?= $year ?></p>
                    </div>
                <?php else: ?>
                    <!-- Table with Data -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden animate-slide-up delay-200">
                        <div class="px-8 py-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4 bg-gradient-to-r from-white to-slate-50">
                            <div class="flex items-center gap-3">
                                <div class="p-2.5 bg-gradient-to-br from-primary/20 to-yellow-100 rounded-xl text-primary shadow-sm">
                                    <span class="material-symbols-outlined">table_view</span>
                                </div>
                                <h2 class="text-lg font-bold text-slate-800">Daftar Payroll - <?= $months[$month] ?> <?= $year ?></h2>
                            </div>
                            <div class="flex items-center space-x-3">
                                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold bg-white hover:bg-slate-50 hover:text-primary hover:border-primary/30 transition-all shadow-sm">
                                    <span class="material-symbols-outlined text-sm mr-2">print</span> Print
                                </button>
                                <button onclick="exportData()" class="inline-flex items-center px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold bg-white hover:bg-slate-50 hover:text-primary hover:border-primary/30 transition-all shadow-sm">
                                    <span class="material-symbols-outlined text-sm mr-2">download</span> Export
                                </button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full whitespace-nowrap" id="payrollTable">
                                <thead>
                                    <tr class="bg-slate-50/80 border-b border-slate-100">
                                        <th class="px-8 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">No</th>
                                        <th class="px-8 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">NIK & Name</th>
                                        <th class="px-8 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Position</th>
                                        <th class="px-8 py-5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Basic Salary</th>
                                        <th class="px-8 py-5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Allowance</th>
                                        <th class="px-8 py-5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Overtime</th>
                                        <th class="px-8 py-5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Deductions</th>
                                        <th class="px-8 py-5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    <?php 
                                    $no = 1;
                                    foreach ($payrollData as $i => $row):
                                        $gajiPokok = $row['gaji_pokok'] ?? 0;
                                        $tunjangan = $row['tunjangan'] ?? 0;
                                        $lembur = $row['lembur'] ?? 0;
                                        $potongan = $row['potongan'] ?? 0;
                                        $total = $gajiPokok + $tunjangan + $lembur - $potongan;
                                        
                                        $nama = $row['nama'] ?? '-';
                                        $initials = strtoupper(substr($nama, 0, 1));
                                        if (strpos($nama, ' ') !== false) {
                                            $parts = explode(' ', $nama);
                                            $initials = strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
                                        }
                                        
                                        $colorClass = $avatarColors[$i % count($avatarColors)];
                                        $delayClass = 'delay-' . (($i % 8 + 1) * 100);
                                    ?>
                                        <tr class="hover:bg-blue-50/30 transition-colors group table-row-animate <?= $delayClass ?>">
                                            <td class="px-8 py-5 text-sm font-medium text-slate-400"><?= $no++ ?></td>
                                            <td class="px-8 py-5">
                                                <div class="flex items-center">
                                                    <div class="h-10 w-10 rounded-full <?= $colorClass ?> flex items-center justify-center text-xs font-bold mr-4 shadow-sm group-hover:scale-110 transition-transform">
                                                        <?= $initials ?>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-bold text-slate-900 group-hover:text-primary transition-colors"><?= htmlspecialchars($nama) ?></div>
                                                        <div class="text-xs font-medium text-slate-400"><?= htmlspecialchars($row['nik'] ?? '-') ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-5 text-sm font-medium text-slate-600"><?= htmlspecialchars($row['jabatan'] ?? '-') ?></td>
                                            <td class="px-8 py-5 text-sm text-slate-600 text-right font-mono tracking-tight"><?= number_format($gajiPokok, 0, ',', '.') ?></td>
                                            <td class="px-8 py-5 text-sm text-slate-600 text-right font-mono tracking-tight"><?= number_format($tunjangan, 0, ',', '.') ?></td>
                                            <td class="px-8 py-5 text-sm text-slate-600 text-right font-mono tracking-tight"><?= number_format($lembur, 0, ',', '.') ?></td>
                                            <td class="px-8 py-5 text-sm text-red-500 text-right font-mono font-medium tracking-tight">-<?= number_format($potongan, 0, ',', '.') ?></td>
                                            <td class="px-8 py-5 text-sm text-green-600 text-right font-bold font-mono tracking-tight bg-green-50/50 rounded-lg"><?= number_format($total, 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="bg-slate-50/90 border-t border-slate-200 backdrop-blur-sm">
                                    <tr>
                                        <td class="px-8 py-6 text-sm font-bold text-slate-700 text-right uppercase tracking-wider" colspan="3">Total Summary</td>
                                        <td class="px-8 py-6 text-sm text-slate-900 text-right font-mono font-bold"><?= number_format($totalGajiPokok, 0, ',', '.') ?></td>
                                        <td class="px-8 py-6 text-sm text-slate-900 text-right font-mono font-bold"><?= number_format($totalTunjangan, 0, ',', '.') ?></td>
                                        <td class="px-8 py-6 text-sm text-slate-900 text-right font-mono font-bold"><?= number_format($totalLembur, 0, ',', '.') ?></td>
                                        <td class="px-8 py-6 text-sm text-red-600 text-right font-mono font-bold"><?= number_format($totalPotongan, 0, ',', '.') ?></td>
                                        <td class="px-8 py-6 text-base text-green-600 text-right font-mono font-extrabold bg-green-50/80"><?= number_format($totalGaji, 0, ',', '.') ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mt-12 text-center pb-4">
                    <p class="text-xs font-medium text-slate-400">© <?= date('Y') ?> IndoOcean ERP. All rights reserved.</p>
                </div>
            </main>
        </div>
    </div>

    <script>
        function exportData() {
            alert('Export functionality will be available soon');
        }

        function filterTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('payrollTable');
            if (!table) return;
            
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            for (let i = 0; i < rows.length; i++) {
                const nameCell = rows[i].getElementsByTagName('td')[1];
                if (nameCell) {
                    const txtValue = nameCell.textContent || nameCell.innerText;
                    rows[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? '' : 'none';
                }
            }
        }
    </script>
</body>
</html>
