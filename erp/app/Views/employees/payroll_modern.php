<?php
/**
 * Modern Payroll Dashboard View
 * PT Indo Ocean - ERP System
 * With Professional Print Layout
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
$totalBonus = 0;
$totalReimbursement = 0;
$totalBpjsJht = 0;
$totalBpjsKes = 0;
$totalPph21 = 0;

if (!empty($payrollData)) {
    $totalEmployees = count($payrollData);
    foreach ($payrollData as $row) {
        $gajiPokok = $row['gaji_pokok'] ?? 0;
        $tunjangan = $row['tunjangan'] ?? ($row['uang_transport'] ?? 0) + ($row['uang_makan'] ?? 0);
        $lembur = $row['lembur'] ?? $row['total_lembur'] ?? 0;
        $potongan = $row['potongan'] ?? 0;
        $bonus = ($row['bonus_pribadi'] ?? 0) + ($row['bonus_team'] ?? 0) + ($row['bonus_jackpot'] ?? 0);
        $reimbursement = $row['total_reimbursement'] ?? 0;
        $bpjsJht = $row['bpjs_jht_amount'] ?? 0;
        $bpjsKes = $row['bpjs_kes_amount'] ?? 0;
        $pph21 = $row['pph21_amount'] ?? 0;
        
        $totalGajiPokok += $gajiPokok;
        $totalTunjangan += $tunjangan;
        $totalLembur += $lembur;
        $totalPotongan += $potongan;
        $totalBonus += $bonus;
        $totalReimbursement += $reimbursement;
        $totalBpjsJht += $bpjsJht;
        $totalBpjsKes += $bpjsKes;
        $totalPph21 += $pph21;
        $totalGaji += ($row['grand_total'] ?? ($gajiPokok + $tunjangan + $lembur - $potongan));
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
<html lang="<?= getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('employees.payroll_title') ?> | IndoOcean ERP</title>

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

    <!-- Professional Print Styles -->
    <style>
        /* ===== PRINT-ONLY STYLES ===== */
        @media print {
            /* Reset */
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            
            /* Hide sidebar - it's an <aside> with inline styles */
            aside { display: none !important; }
            
            /* Hide all screen-only elements */
            .no-print, header, .glass-card, 
            .animate-float, .animate-float-delayed, 
            #erpSidebar, button, form, .filter-section,
            .stats-section, .back-button-section,
            .screen-only-table { display: none !important; }

            /* Hide animated backgrounds */
            [class*="absolute"][class*="blur"] { display: none !important; }
            .absolute { display: none !important; }

            html, body {
                font-family: 'Inter', Arial, sans-serif !important;
                font-size: 9pt !important;
                color: #000 !important;
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
                height: auto !important;
            }

            /* Main container reset — remove sidebar margin */
            .flex.h-screen { display: block !important; height: auto !important; overflow: visible !important; }
            [class*="ml-64"], .ml-64 { margin-left: 0 !important; }
            .flex-1 { flex: none !important; }
            main { padding: 0 !important; overflow: visible !important; height: auto !important; }
            .overflow-hidden, .overflow-y-auto { overflow: visible !important; }

            /* Show print header */
            .print-header { display: block !important; }
            .print-footer { display: block !important; }

            /* Table styling for print */
            .print-table-container {
                display: block !important;
                background: white !important;
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                overflow: visible !important;
            }

            .print-table {
                width: 100% !important;
                border-collapse: collapse !important;
                font-size: 7.5pt !important;
                page-break-inside: auto;
            }

            .print-table th {
                background-color: #1a365d !important;
                color: white !important;
                font-weight: 700 !important;
                padding: 6px 4px !important;
                border: 1px solid #1a365d !important;
                text-align: center !important;
                font-size: 7pt !important;
                white-space: nowrap !important;
            }

            .print-table td {
                padding: 4px 4px !important;
                border: 1px solid #cbd5e1 !important;
                font-size: 7.5pt !important;
                vertical-align: middle !important;
            }

            .print-table tbody tr:nth-child(even) {
                background-color: #f8fafc !important;
            }

            .print-table tfoot td {
                background-color: #e2e8f0 !important;
                font-weight: 700 !important;
                border: 1px solid #94a3b8 !important;
                padding: 6px 4px !important;
                font-size: 8pt !important;
            }

            .text-right-print { text-align: right !important; }
            .text-center-print { text-align: center !important; }
            .fw-bold-print { font-weight: 700 !important; }
            .text-red-print { color: #dc2626 !important; }
            .text-green-print { color: #16a34a !important; }

            /* Page settings */
            @page {
                size: A4 landscape;
                margin: 12mm 10mm 15mm 10mm;
            }

            /* Screen-only table hide */
            .screen-only-table { display: none !important; }
        }

        /* Hide print elements on screen */
        @media screen {
            .print-header { display: none !important; }
            .print-footer { display: none !important; }
            .print-table-container.print-only { display: none !important; }
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased overflow-hidden">

    <!-- ===== PRINT HEADER (Hidden on screen, shows on print) ===== -->
    <div class="print-header" style="display:none; margin-bottom: 20px;">
        <table style="width:100%; border:none; margin-bottom: 8px;">
            <tr>
                <td style="width:80px; vertical-align:middle; border:none; padding:0;">
                    <img src="<?= BASE_URL ?>assets/images/logo.png" alt="IndoOcean Logo" style="width:65px; height:65px;">
                </td>
                <td style="vertical-align:middle; border:none; padding:0 0 0 10px;">
                    <div style="font-size: 18pt; font-weight: 800; color: #1a365d; letter-spacing: 1px; line-height: 1.2;">
                        PT INDO OCEAN CREW SERVICE
                    </div>
                    <div style="font-size: 8pt; color: #475569; margin-top: 2px;">
                        Menara Cakrawala Lt. 5 Suite 503 • Jl. MH Thamrin No.9, Jakarta Pusat 10340
                    </div>
                    <div style="font-size: 8pt; color: #475569;">
                        Tel: (021) 3101-488 • Email: info@indooceancrew.com • www.indooceancrew.com
                    </div>
                </td>
                <td style="width:80px; vertical-align:middle; text-align:right; border:none; padding:0;">
                    <img src="<?= BASE_URL ?>assets/images/logo.png" alt="Company Logo" style="width:65px; height:65px;">
                </td>
            </tr>
        </table>
        
        <div style="border-top: 3px solid #1a365d; border-bottom: 1px solid #1a365d; padding: 4px 0; margin-bottom: 12px;"></div>
        
        <div style="text-align:center; margin-bottom: 12px;">
            <div style="font-size: 14pt; font-weight: 800; color: #1a365d; letter-spacing: 2px;">
                LAPORAN PENGGAJIAN KARYAWAN
            </div>
            <div style="font-size: 10pt; font-weight: 600; color: #475569; margin-top: 4px;">
                Periode: <?= $months[$month] ?> <?= $year ?>
            </div>
        </div>

        <table style="width: 100%; border:none; margin-bottom: 10px; font-size: 8pt;">
            <tr>
                <td style="border:none; padding:2px 0;">
                    <strong>Tanggal Cetak:</strong> <?= date('d/m/Y H:i') ?> WIB
                </td>
                <td style="border:none; padding:2px 0; text-align:right;">
                    <strong>Total Karyawan:</strong> <?= $totalEmployees ?> orang
                </td>
            </tr>
            <tr>
                <td style="border:none; padding:2px 0;">
                    <strong>Sumber Data:</strong> 
                    <?php 
                    $ds = $dataSource ?? 'unknown';
                    echo $ds === 'database' ? 'HRIS Live Database' : ($ds === 'api_live' ? 'HRIS API Live' : 'Data Lokal');
                    ?>
                </td>
                <td style="border:none; padding:2px 0; text-align:right;">
                    <strong>Status:</strong> <span style="color:#16a34a; font-weight:700;">FINAL</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- ===== PRINT TABLE (Hidden on screen, shown on print) ===== -->
    <?php if (!empty($payrollData)): ?>
    <div class="print-table-container print-only" style="display:none;">
        <table class="print-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width:25px;">No</th>
                    <th rowspan="2" style="width:80px;">No. Gaji</th>
                    <th rowspan="2" style="min-width:120px;">Nama Karyawan</th>
                    <th rowspan="2" style="min-width:80px;">Jabatan</th>
                    <th colspan="5" style="background-color:#166534 !important; border-color:#166534 !important;">PENDAPATAN</th>
                    <th colspan="5" style="background-color:#991b1b !important; border-color:#991b1b !important;">POTONGAN</th>
                    <th rowspan="2" style="background-color:#0c4a6e !important; border-color:#0c4a6e !important; min-width:80px;">GRAND TOTAL</th>
                </tr>
                <tr>
                    <th style="background-color:#15803d !important; border-color:#15803d !important;">Gaji Pokok</th>
                    <th style="background-color:#15803d !important; border-color:#15803d !important;">Tunjangan</th>
                    <th style="background-color:#15803d !important; border-color:#15803d !important;">Lembur</th>
                    <th style="background-color:#15803d !important; border-color:#15803d !important;">Bonus</th>
                    <th style="background-color:#15803d !important; border-color:#15803d !important;">Reimburse</th>
                    <th style="background-color:#b91c1c !important; border-color:#b91c1c !important;">Mangkir/Telat</th>
                    <th style="background-color:#b91c1c !important; border-color:#b91c1c !important;">Kasbon</th>
                    <th style="background-color:#b91c1c !important; border-color:#b91c1c !important;">BPJS</th>
                    <th style="background-color:#b91c1c !important; border-color:#b91c1c !important;">PPH 21</th>
                    <th style="background-color:#b91c1c !important; border-color:#b91c1c !important;">Lainnya</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                foreach ($payrollData as $row):
                    $gajiPokok = $row['gaji_pokok'] ?? 0;
                    $tunjangan = $row['tunjangan'] ?? ($row['uang_transport'] ?? 0) + ($row['uang_makan'] ?? 0);
                    $lembur = $row['lembur'] ?? $row['total_lembur'] ?? 0;
                    $bonus = ($row['bonus_pribadi'] ?? 0) + ($row['bonus_team'] ?? 0) + ($row['bonus_jackpot'] ?? 0);
                    $reimburse = $row['total_reimbursement'] ?? 0;
                    $mangkirTelat = ($row['total_mangkir'] ?? 0) + ($row['total_terlambat'] ?? 0);
                    $kasbon = $row['bayar_kasbon'] ?? 0;
                    $bpjs = ($row['bpjs_jht_amount'] ?? 0) + ($row['bpjs_kes_amount'] ?? 0);
                    $pph21 = $row['pph21_amount'] ?? 0;
                    $loss = $row['loss'] ?? 0;
                    $totalIzin = $row['total_izin'] ?? 0;
                    $potonganLain = $loss + $totalIzin;
                    $grandTotal = $row['grand_total'] ?? ($gajiPokok + $tunjangan + $lembur + $bonus + $reimburse - $mangkirTelat - $kasbon - $bpjs - $pph21 - $potonganLain);
                ?>
                    <tr>
                        <td class="text-center-print"><?= $no++ ?></td>
                        <td style="font-size:7pt;"><?= htmlspecialchars($row['nik'] ?? $row['no_gaji'] ?? '-') ?></td>
                        <td class="fw-bold-print"><?= htmlspecialchars($row['nama'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['jabatan'] ?? '-') ?></td>
                        <td class="text-right-print"><?= number_format($gajiPokok, 0, ',', '.') ?></td>
                        <td class="text-right-print"><?= number_format($tunjangan, 0, ',', '.') ?></td>
                        <td class="text-right-print"><?= number_format($lembur, 0, ',', '.') ?></td>
                        <td class="text-right-print"><?= number_format($bonus, 0, ',', '.') ?></td>
                        <td class="text-right-print"><?= number_format($reimburse, 0, ',', '.') ?></td>
                        <td class="text-right-print text-red-print"><?= $mangkirTelat > 0 ? number_format($mangkirTelat, 0, ',', '.') : '-' ?></td>
                        <td class="text-right-print text-red-print"><?= $kasbon > 0 ? number_format($kasbon, 0, ',', '.') : '-' ?></td>
                        <td class="text-right-print text-red-print"><?= $bpjs > 0 ? number_format($bpjs, 0, ',', '.') : '-' ?></td>
                        <td class="text-right-print text-red-print"><?= $pph21 > 0 ? number_format($pph21, 0, ',', '.') : '-' ?></td>
                        <td class="text-right-print text-red-print"><?= $potonganLain > 0 ? number_format($potonganLain, 0, ',', '.') : '-' ?></td>
                        <td class="text-right-print fw-bold-print text-green-print"><?= number_format($grandTotal, 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right-print" style="font-size:8pt;">TOTAL</td>
                    <td class="text-right-print"><?= number_format($totalGajiPokok, 0, ',', '.') ?></td>
                    <td class="text-right-print"><?= number_format($totalTunjangan, 0, ',', '.') ?></td>
                    <td class="text-right-print"><?= number_format($totalLembur, 0, ',', '.') ?></td>
                    <td class="text-right-print"><?= number_format($totalBonus, 0, ',', '.') ?></td>
                    <td class="text-right-print"><?= number_format($totalReimbursement, 0, ',', '.') ?></td>
                    <td class="text-right-print text-red-print"><?= number_format(array_sum(array_map(function($r){ return ($r['total_mangkir']??0)+($r['total_terlambat']??0); }, $payrollData)), 0, ',', '.') ?></td>
                    <td class="text-right-print text-red-print"><?= number_format(array_sum(array_map(function($r){ return $r['bayar_kasbon']??0; }, $payrollData)), 0, ',', '.') ?></td>
                    <td class="text-right-print text-red-print"><?= number_format($totalBpjsJht + $totalBpjsKes, 0, ',', '.') ?></td>
                    <td class="text-right-print text-red-print"><?= number_format($totalPph21, 0, ',', '.') ?></td>
                    <td class="text-right-print text-red-print"><?= number_format(array_sum(array_map(function($r){ return ($r['loss']??0)+($r['total_izin']??0); }, $payrollData)), 0, ',', '.') ?></td>
                    <td class="text-right-print fw-bold-print text-green-print" style="font-size:9pt !important;"><?= number_format($totalGaji, 0, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php endif; ?>

    <!-- ===== PRINT FOOTER (signatures) ===== -->
    <div class="print-footer" style="display:none; margin-top: 30px; page-break-inside: avoid;">
        <table style="width:100%; border:none;">
            <tr>
                <td style="width:33%; text-align:center; border:none; padding-top:20px; vertical-align:top;">
                    <div style="font-size:8pt; color:#475569;">Dibuat oleh,</div>
                    <div style="height:60px;"></div>
                    <div style="border-top:1px solid #000; display:inline-block; padding-top:4px; min-width:150px;">
                        <div style="font-size:8pt; font-weight:700;">HRD / Finance</div>
                    </div>
                </td>
                <td style="width:33%; text-align:center; border:none; padding-top:20px; vertical-align:top;">
                    <div style="font-size:8pt; color:#475569;">Diperiksa oleh,</div>
                    <div style="height:60px;"></div>
                    <div style="border-top:1px solid #000; display:inline-block; padding-top:4px; min-width:150px;">
                        <div style="font-size:8pt; font-weight:700;">Manager Operasional</div>
                    </div>
                </td>
                <td style="width:33%; text-align:center; border:none; padding-top:20px; vertical-align:top;">
                    <div style="font-size:8pt; color:#475569;">Disetujui oleh,</div>
                    <div style="height:60px;"></div>
                    <div style="border-top:1px solid #000; display:inline-block; padding-top:4px; min-width:150px;">
                        <div style="font-size:8pt; font-weight:700;">Direktur</div>
                    </div>
                </td>
            </tr>
        </table>
        <div style="text-align:center; margin-top:15px; font-size:7pt; color:#94a3b8;">
            Dokumen ini dicetak secara otomatis dari IndoOcean ERP System • <?= date('d/m/Y H:i:s') ?> WIB
        </div>
    </div>

    <!-- ===== SCREEN LAYOUT ===== -->
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
            <header class="h-20 bg-white/70 backdrop-blur-md border-b border-slate-200/60 flex items-center justify-between px-8 z-20 sticky top-0 no-print">
                <nav aria-label="Breadcrumb" class="flex">
                    <ol class="flex items-center space-x-2">
                        <li><a class="text-slate-400 hover:text-primary transition-colors" href="<?= BASE_URL ?>"><span class="material-symbols-outlined text-[20px]">home</span></a></li>
                        <li><span class="text-slate-300">/</span></li>
                        <li><a class="text-sm font-medium text-slate-500 hover:text-slate-800 transition-colors" href="<?= BASE_URL ?>employees"><?= __('employees.title') ?></a></li>
                        <li><span class="text-slate-300">/</span></li>
                        <li><span aria-current="page" class="text-sm font-semibold text-slate-800 bg-slate-100 px-2 py-0.5 rounded-md"><?= __('employees.payroll_title') ?></span></li>
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
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10 animate-slide-up no-print">
                    <div>
                        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                            <?= __('employees.payroll_title') ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                <?= __('common.active_period') ?>
                            </span>
                        </h1>
                        <p class="mt-2 text-sm text-slate-500 font-medium flex items-center gap-2">
                            Data payroll karyawan dari sistem HRIS Absensi.
                            <?php 
                            $dataSource = $dataSource ?? 'unknown';
                            if ($dataSource === 'database'): ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 border border-green-200">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1"></span>Live Database
                                </span>
                            <?php elseif ($dataSource === 'api_live'): ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-1"></span>API Live
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full mr-1"></span>Demo Data
                                </span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="flex items-center gap-3 back-button-section">
                        <a href="<?= BASE_URL ?>employees" class="inline-flex items-center px-5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 shadow-sm hover:bg-slate-50 hover:border-slate-300 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-300 transform hover:-translate-y-0.5">
                            <span class="material-symbols-outlined mr-2 text-sm">arrow_back</span>
                            Back
                        </a>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10 stats-section no-print">
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
                            <p class="text-sm font-medium text-slate-500 mb-1"><?= __('employees.total_employees') ?></p>
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
                            <p class="text-sm font-medium text-slate-500 mb-1"><?= __('employees.total_gross') ?></p>
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
                            <p class="text-sm font-medium text-slate-500 mb-1"><?= __('employees.total_deductions') ?></p>
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
                            <p class="text-sm font-medium text-slate-500 mb-1"><?= __('employees.net_payroll') ?></p>
                            <p class="text-3xl font-bold text-green-600 tracking-tight">Rp <?= number_format($totalGaji, 0, ',', '.') ?></p>
                            <div class="mt-2 flex items-center text-xs text-green-600 font-semibold bg-green-50 w-fit px-2 py-1 rounded-md">
                                <span class="material-symbols-outlined text-[14px] mr-1">check_circle</span> Disbursed
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl border border-slate-200 p-6 mb-8 shadow-sm animate-slide-up delay-100 filter-section no-print">
                    <form method="GET" action="<?= BASE_URL ?>employees/payroll" class="flex flex-col md:flex-row md:items-end gap-5">
                        <div class="w-full md:w-1/4 group">
                            <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide group-focus-within:text-primary transition-colors"><?= __('common.month') ?></label>
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
                            <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide group-focus-within:text-primary transition-colors"><?= __('common.year') ?></label>
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
                            <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide group-focus-within:text-primary transition-colors"><?= __('employees.search_employee') ?></label>
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
                    <div class="bg-white rounded-2xl border border-red-200 p-12 text-center no-print">
                        <div class="inline-block p-4 bg-red-50 rounded-full mb-4">
                            <span class="material-symbols-outlined text-5xl text-red-500">error</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Gagal Memuat Data</h3>
                        <p class="text-slate-500">Tidak dapat terhubung ke sistem HRIS untuk mengambil data payroll.</p>
                    </div>
                <?php elseif (empty($payrollData)): ?>
                    <!-- Empty State -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center no-print">
                        <div class="inline-block p-4 bg-slate-50 rounded-full mb-4">
                            <span class="material-symbols-outlined text-5xl text-slate-400">payments</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Tidak Ada Data Payroll</h3>
                        <p class="text-slate-500">Belum ada data payroll untuk periode <?= $months[$month] ?> <?= $year ?></p>
                    </div>
                <?php else: ?>
                    <!-- Table with Data -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden animate-slide-up delay-200 screen-only-table">
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
                                        $tunjangan = $row['tunjangan'] ?? ($row['uang_transport'] ?? 0) + ($row['uang_makan'] ?? 0);
                                        $lembur = $row['lembur'] ?? $row['total_lembur'] ?? 0;
                                        $potongan = $row['potongan'] ?? 0;
                                        $total = $row['grand_total'] ?? ($gajiPokok + $tunjangan + $lembur - $potongan);
                                        
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
                                        <td class="px-8 py-6 text-sm font-bold text-slate-700 text-right uppercase tracking-wider" colspan="3"><?= __('common.total_summary') ?></td>
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

                <div class="mt-12 text-center pb-4 no-print">
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
