<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Payslip - <?= htmlspecialchars($item['crew_name'] ?? '') ?></title>
    <style>
        @page { size: A4; margin: 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11pt; color: #222; background: #fff; }
        
        .payslip-container {
            max-width: 750px;
            margin: 0 auto;
            padding: 30px;
        }

        /* Header */
        .header {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 20px;
        }
        .header-logo {
            width: 80px;
            height: 80px;
            flex-shrink: 0;
        }
        .header-logo svg {
            width: 80px;
            height: 80px;
        }
        .header-text h1 {
            font-size: 18pt;
            font-weight: 800;
            color: #1e3a5f;
            letter-spacing: 0.5px;
        }
        .header-text p {
            font-size: 8pt;
            color: #555;
            margin-top: 2px;
            line-height: 1.5;
        }

        /* Title Bar */
        .title-bar {
            background: linear-gradient(135deg, #1e3a5f, #2c5282);
            color: #fff;
            text-align: center;
            padding: 10px;
            font-size: 16pt;
            font-weight: 800;
            letter-spacing: 3px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        /* Info Section */
        .info-section {
            border: 1px solid #ddd;
            padding: 15px 20px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .info-row {
            display: flex;
            margin-bottom: 6px;
        }
        .info-label {
            width: 140px;
            font-weight: 700;
            color: #333;
            font-size: 10pt;
        }
        .info-sep { width: 15px; text-align: center; color: #666; }
        .info-value { flex: 1; font-size: 10pt; color: #333; }

        /* Salary Table */
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .salary-table th {
            background: #e8edf3;
            color: #1e3a5f;
            padding: 8px 12px;
            text-align: center;
            font-size: 10pt;
            font-weight: 700;
            border: 1px solid #ccc;
            text-transform: uppercase;
        }
        .salary-table td {
            padding: 6px 12px;
            border: 1px solid #ddd;
            font-size: 10pt;
            vertical-align: top;
        }
        .salary-table .amount {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }
        .salary-table .label-col { width: 160px; }
        .salary-table .sep-col { width: 15px; text-align: center; }
        .salary-table .cur-col { width: 30px; }
        .salary-table .amount-col { width: 120px; text-align: right; }
        .salary-table .spacer-col { width: 30px; border: none; }
        
        /* Gross row */
        .gross-row td {
            font-weight: 800;
            background: #f0f4f8;
            border-top: 2px solid #1e3a5f;
        }

        /* Footer Section */
        .footer-section {
            border: 1px solid #ddd;
            padding: 15px 20px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .footer-grid {
            display: flex;
            justify-content: space-between;
        }
        .footer-left { flex: 1; }
        .footer-right { flex: 1; text-align: right; }
        .footer-left p, .footer-right p {
            font-size: 10pt;
            margin-bottom: 4px;
        }
        .net-pay-label {
            font-weight: 800;
            font-size: 12pt;
            color: #1e3a5f;
        }
        .net-pay-amount {
            font-weight: 800;
            font-size: 14pt;
            color: #1e3a5f;
            margin-top: 4px;
        }

        /* Print-only styles */
        @media print {
            body { background: #fff; }
            .payslip-container { padding: 0; }
            .no-print { display: none !important; }
        }
        @media screen {
            body { background: #f3f4f6; padding: 20px; }
            .payslip-container {
                background: #fff;
                box-shadow: 0 4px 20px rgba(0,0,0,0.1);
                border-radius: 8px;
                padding: 40px;
            }
        }
    </style>
</head>
<body>
<?php
    $crewName = $item['crew_name'] ?? '-';
    $rankName = $item['rank_name'] ?? '-';
    $vesselName = $item['vessel_name'] ?? '-';
    $periodMonth = $period['period_month'] ?? date('n');
    $periodYear = $period['period_year'] ?? date('Y');
    $monthNames = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $periodText = ($payroll_day ?? 1) . '-' . date('t', mktime(0,0,0,$periodMonth,1,$periodYear)) . ' ' . strtoupper($monthNames[$periodMonth] ?? '') . ' ' . $periodYear;

    $basicSalary = (float)($item['basic_salary'] ?? 0);
    $overtime = (float)($item['overtime'] ?? 0);
    $leavePay = (float)($item['leave_pay'] ?? 0);
    $bonus = (float)($item['bonus'] ?? 0);
    $otherAllowance = (float)($item['other_allowance'] ?? 0);
    $grossSalary = (float)($item['gross_salary'] ?? 0);
    $insurance = (float)($item['insurance'] ?? 0);
    $medical = (float)($item['medical'] ?? 0);
    $advance = (float)($item['advance'] ?? 0);
    $otherDeductions = (float)($item['other_deductions'] ?? 0);
    $totalDeductions = (float)($item['total_deductions'] ?? 0);
    $taxAmount = (float)($item['tax_amount'] ?? 0);
    $netSalary = (float)($item['net_salary'] ?? 0);
    $currency = $item['currency_code'] ?? 'Rp';
    $curSymbol = ($currency === 'USD') ? 'USD' : (($currency === 'IDR') ? 'Rp' : $currency);

    // Format number with dots
    function fmtMoney($val) {
        if ($val == 0) return '-';
        return number_format($val, 0, ',', '.');
    }
?>

<div class="payslip-container">
    <!-- Header -->
    <div class="header">
        <div class="header-logo">
            <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="40" cy="40" r="38" fill="#1e3a5f" opacity="0.1"/>
                <path d="M20 50C25 35 35 25 50 20C45 30 42 40 50 55C40 55 30 55 20 50Z" fill="#1e3a5f" opacity="0.8"/>
                <path d="M25 45C30 33 38 28 48 25C44 33 43 40 48 52C40 52 32 50 25 45Z" fill="#2c5282"/>
            </svg>
        </div>
        <div class="header-text">
            <h1>PT. INDO OCEANCREW SERVICES</h1>
            <p>Menara Cakrawala lt 15 no 1506</p>
            <p>Jl. M.H. Thamrin No.9 2, RT.2/RW.1, Kb. Sirih, Kec. Menteng,</p>
            <p>Kota Jakarta Pusat, Daerah Khusus Ibukota Jakarta, 10340</p>
        </div>
    </div>

    <!-- Title Bar -->
    <div class="title-bar">PAYSLIP</div>

    <!-- Crew Info -->
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">NAME</span>
            <span class="info-sep">:</span>
            <span class="info-value"><?= htmlspecialchars(strtoupper($crewName)) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">PERIODE</span>
            <span class="info-sep">:</span>
            <span class="info-value"><?= $periodText ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">SHIP</span>
            <span class="info-sep">:</span>
            <span class="info-value"><?= htmlspecialchars(strtoupper($vesselName)) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">RANK</span>
            <span class="info-sep">:</span>
            <span class="info-value"><?= htmlspecialchars(strtoupper($rankName)) ?></span>
        </div>
    </div>

    <!-- Salary Table -->
    <table class="salary-table">
        <thead>
            <tr>
                <th colspan="4">INCOME</th>
                <th class="spacer-col"></th>
                <th colspan="4">DEDUCTION</th>
            </tr>
        </thead>
        <tbody>
            <!-- Row 1: Basic Salary | Insurance -->
            <tr>
                <td class="label-col">Basic Salary</td>
                <td class="sep-col">:</td>
                <td class="cur-col"><?= $curSymbol ?></td>
                <td class="amount-col amount"><?= fmtMoney($basicSalary) ?></td>
                <td class="spacer-col"></td>
                <td class="label-col">Insurance</td>
                <td class="sep-col">:</td>
                <td class="cur-col"><?= $curSymbol ?></td>
                <td class="amount-col amount"><?= fmtMoney($insurance) ?></td>
            </tr>
            <!-- Row 2: Overtime | Medical -->
            <tr>
                <td>Overtime</td>
                <td class="sep-col">:</td>
                <td><?= $curSymbol ?></td>
                <td class="amount"><?= fmtMoney($overtime) ?></td>
                <td class="spacer-col"></td>
                <td>Medical</td>
                <td class="sep-col">:</td>
                <td><?= $curSymbol ?></td>
                <td class="amount"><?= fmtMoney($medical) ?></td>
            </tr>
            <!-- Row 3: Leave Pay | Advance -->
            <tr>
                <td>Leave Pay</td>
                <td class="sep-col">:</td>
                <td><?= $curSymbol ?></td>
                <td class="amount"><?= fmtMoney($leavePay) ?></td>
                <td class="spacer-col"></td>
                <td>Advance</td>
                <td class="sep-col">:</td>
                <td><?= $curSymbol ?></td>
                <td class="amount"><?= fmtMoney($advance) ?></td>
            </tr>
            <!-- Row 4: Bonus | Other Deductions -->
            <tr>
                <td>Bonus</td>
                <td class="sep-col">:</td>
                <td><?= $curSymbol ?></td>
                <td class="amount"><?= fmtMoney($bonus) ?></td>
                <td class="spacer-col"></td>
                <td>Other Deductions</td>
                <td class="sep-col">:</td>
                <td><?= $curSymbol ?></td>
                <td class="amount"><?= fmtMoney($otherDeductions) ?></td>
            </tr>
            <!-- Row 5: Other Allowance | PPH Tax -->
            <tr>
                <td>Other Allowance</td>
                <td class="sep-col">:</td>
                <td><?= $curSymbol ?></td>
                <td class="amount"><?= fmtMoney($otherAllowance) ?></td>
                <td class="spacer-col"></td>
                <td>PPH 21 (<?= number_format($item['tax_rate'] ?? 2.5, 1) ?>%)</td>
                <td class="sep-col">:</td>
                <td><?= $curSymbol ?></td>
                <td class="amount"><?= fmtMoney($taxAmount) ?></td>
            </tr>
            <!-- Gross Row -->
            <tr class="gross-row">
                <td><strong>Gross</strong></td>
                <td class="sep-col"></td>
                <td><strong><?= $curSymbol ?></strong></td>
                <td class="amount"><strong><?= fmtMoney($grossSalary) ?></strong></td>
                <td class="spacer-col"></td>
                <td></td>
                <td class="sep-col"></td>
                <td><strong><?= $curSymbol ?></strong></td>
                <td class="amount"><strong><?= fmtMoney($totalDeductions + $taxAmount) ?></strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Footer: Bank Info + Net Pay -->
    <div class="footer-section">
        <div class="footer-grid">
            <div class="footer-left">
                <p>Paid By Bank Transfer</p>
                <p>Acc. Holder : <?= htmlspecialchars(strtoupper($crew['bank_account_name'] ?? $crewName)) ?></p>
                <p>Acc. No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?= htmlspecialchars($crew['bank_account_number'] ?? '-') ?></p>
                <p>Bank &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?= htmlspecialchars(strtoupper($crew['bank_name'] ?? '-')) ?></p>
            </div>
            <div class="footer-right">
                <p class="net-pay-label">Net Take-Home Pay</p>
                <p class="net-pay-amount"><?= $curSymbol ?> &nbsp; <?= fmtMoney($netSalary) ?></p>
            </div>
        </div>
    </div>

    <!-- Print Button (screen only) -->
    <div class="no-print" style="text-align:center; margin-top:20px;">
        <button onclick="window.print()" style="padding:10px 30px; background:#1e3a5f; color:#fff; border:none; border-radius:8px; font-weight:700; cursor:pointer; font-size:14px;">
            🖨️ Print / Save as PDF
        </button>
    </div>
</div>

</body>
</html>
