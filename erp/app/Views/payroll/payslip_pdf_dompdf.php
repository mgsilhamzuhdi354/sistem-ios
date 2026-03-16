<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Payslip - <?= htmlspecialchars($item['crew_name'] ?? '') ?></title>
    <style>
        @page { size: A4; margin: 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, 'Segoe UI', Helvetica, sans-serif; font-size: 11pt; color: #222; background: #fff; }
        
        .payslip-container {
            max-width: 750px;
            margin: 0 auto;
            padding: 10px;
        }

        /* Header using table layout for DOMPDF compatibility */
        .header-table {
            width: 100%;
            border: none;
            margin-bottom: 15px;
        }
        .header-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .logo-cell {
            width: 85px;
            padding-right: 15px;
        }
        .logo-cell img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 2px solid #1e3a5f;
        }
        .header-text h1 {
            font-size: 18pt;
            font-weight: 800;
            color: #1e3a5f;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .header-text p {
            font-size: 8pt;
            color: #555;
            margin: 2px 0 0 0;
            line-height: 1.5;
        }

        /* Title Bar */
        .title-bar {
            background: #1e3a5f;
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
            padding: 12px 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .info-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
        }
        .info-table td {
            border: none;
            padding: 3px 0;
            font-size: 10pt;
        }
        .info-label {
            width: 140px;
            font-weight: 700;
            color: #333;
        }
        .info-sep { width: 15px; text-align: center; color: #666; }
        .info-value { color: #333; }

        /* Salary Table */
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .salary-table th {
            background: #e8edf3;
            color: #1e3a5f;
            padding: 8px 10px;
            text-align: center;
            font-size: 10pt;
            font-weight: 700;
            border: 1px solid #ccc;
            text-transform: uppercase;
        }
        .salary-table td {
            padding: 5px 8px;
            border: 1px solid #ddd;
            font-size: 10pt;
            vertical-align: top;
        }
        .amount {
            text-align: right;
        }
        .label-col { width: 140px; }
        .sep-col { width: 15px; text-align: center; }
        .cur-col { width: 30px; }
        .amount-col { width: 100px; text-align: right; }
        .spacer-col { width: 15px; border-left: none !important; border-right: none !important; border-top: none !important; border-bottom: none !important; }
        
        /* Gross row */
        .gross-row td {
            font-weight: 800;
            background: #f0f4f8;
            border-top: 2px solid #1e3a5f;
        }

        /* Footer Section */
        .footer-section {
            border: 1px solid #ddd;
            padding: 12px 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .footer-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
        }
        .footer-table td {
            border: none;
            vertical-align: top;
            padding: 0;
        }
        .footer-left { width: 55%; }
        .footer-right { width: 45%; text-align: right; }
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

    // Original currency values (from contract)
    $origCurrency = strtoupper($item['original_currency'] ?? '');
    $origBasic = (float)($item['original_basic'] ?? 0);
    $origOvertime = (float)($item['original_overtime'] ?? 0);
    $kursRate = (float)($item['exchange_rate'] ?? 0);
    
    // Determine if dual-currency (non-IDR original)
    $isDualCurrency = !empty($origCurrency) && $origCurrency !== 'IDR' && $origCurrency !== 'Rp' && $kursRate > 0;
    
    // Currency symbol for the original currency
    if ($isDualCurrency) {
        $origCurSymbol = $origCurrency;
    } else {
        $origCurSymbol = 'IDR';
        if ($kursRate <= 0) $kursRate = 1;
    }
    
    // IDR values (converted)
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
    $adminBankFee = (float)($item['admin_bank_fee'] ?? 0);
    $reimbursement = (float)($item['reimbursement'] ?? 0);
    $loans = (float)($item['loans'] ?? 0);
    $totalDeductions = (float)($item['total_deductions'] ?? 0);
    $taxRate = (float)($item['tax_rate'] ?? 2.5);
    $taxAmount = (float)($item['tax_amount'] ?? 0);
    $netSalary = (float)($item['net_salary'] ?? 0);

    if ($isDualCurrency) {
        $actualySalary = $origBasic + $origOvertime;
    } else {
        $origBasic = $basicSalary > 0 ? $basicSalary : $origBasic;
        $origOvertime = 0;
        $actualySalary = $origBasic + $origOvertime;
    }
    
    $idrGross = $actualySalary * $kursRate;
    $totalGrossRp = $idrGross + $reimbursement - $loans;
    
    if (!$isDualCurrency && $grossSalary > 0) {
        $totalGrossRp = $grossSalary;
    }
    
    $totalDeductionsRp = $adminBankFee + $insurance + $otherDeductions + $taxAmount;

    if (!function_exists('fmtMoney')) {
        function fmtMoney($val) {
            if ($val == 0) return '-';
            return number_format(abs($val), 0, ',', '.');
        }
    }
?>

<div class="payslip-container">
    <!-- Header -->
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <img src="<?= defined('BASE_URL') ? BASE_URL . 'assets/images/logo.png' : 'assets/images/logo.png' ?>" alt="Logo">
            </td>
            <td>
                <div class="header-text">
                    <h1>PT. INDO OCEANCREW SERVICES</h1>
                    <p>Menara Cakrawala lt 15 no 1506</p>
                    <p>Jl. M.H. Thamrin No.9 2, RT.2/RW.1, Kb. Sirih, Kec. Menteng,</p>
                    <p>Kota Jakarta Pusat, Daerah Khusus Ibukota Jakarta, 10340</p>
                </div>
            </td>
        </tr>
    </table>

    <!-- Title Bar -->
    <div class="title-bar">PAYSLIP</div>

    <!-- Crew Info -->
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td class="info-label">NAME</td>
                <td class="info-sep">:</td>
                <td class="info-value"><?= htmlspecialchars(strtoupper($crewName)) ?></td>
            </tr>
            <tr>
                <td class="info-label">PERIODE</td>
                <td class="info-sep">:</td>
                <td class="info-value"><?= $periodText ?></td>
            </tr>
            <tr>
                <td class="info-label">SHIP</td>
                <td class="info-sep">:</td>
                <td class="info-value"><?= htmlspecialchars(strtoupper($vesselName)) ?></td>
            </tr>
            <tr>
                <td class="info-label">RANK</td>
                <td class="info-sep">:</td>
                <td class="info-value"><?= htmlspecialchars(strtoupper($rankName)) ?></td>
            </tr>
        </table>
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
            <!-- Row 1: Basic Salary | Admin Bank -->
            <tr>
                <td class="label-col">Basic Salary</td>
                <td class="sep-col">:</td>
                <td class="cur-col"><?= $origCurSymbol ?></td>
                <td class="amount-col amount"><?= fmtMoney($origBasic) ?></td>
                <td class="spacer-col"></td>
                <td class="label-col">Admin Bank</td>
                <td class="sep-col">:</td>
                <td class="cur-col">Rp</td>
                <td class="amount-col amount"><?= fmtMoney($adminBankFee) ?></td>
            </tr>
            <!-- Row 2: Advance Salary | Insurance -->
            <tr>
                <td>Advance Salary</td>
                <td class="sep-col">:</td>
                <td><?= $origCurSymbol ?></td>
                <td class="amount"><?= fmtMoney($origOvertime) ?></td>
                <td class="spacer-col"></td>
                <td>Insurance</td>
                <td class="sep-col">:</td>
                <td>Rp</td>
                <td class="amount"><?= fmtMoney($insurance) ?></td>
            </tr>
            <!-- Row 3: Actualy Salary | Other Deductions -->
            <tr>
                <td><strong>Actualy Salary</strong></td>
                <td class="sep-col">:</td>
                <td><strong><?= $origCurSymbol ?></strong></td>
                <td class="amount"><strong><?= fmtMoney($actualySalary) ?></strong></td>
                <td class="spacer-col"></td>
                <td>Other Deductions</td>
                <td class="sep-col">:</td>
                <td>Rp</td>
                <td class="amount"><?= fmtMoney($otherDeductions) ?></td>
            </tr>
            <!-- Row 4: Reimbursement | PPH 21 -->
            <tr>
                <td>Reimbursement</td>
                <td class="sep-col">:</td>
                <td>Rp</td>
                <td class="amount"><?= fmtMoney($reimbursement) ?></td>
                <td class="spacer-col"></td>
                <td>PPH 21 (<?= number_format($taxRate, 1, ',', '.') ?>%)</td>
                <td class="sep-col">:</td>
                <td>Rp</td>
                <td class="amount"><?= fmtMoney($taxAmount) ?></td>
            </tr>
            <!-- Row 5: Loans To IOS | (empty) -->
            <tr>
                <td>Loans To IOS</td>
                <td class="sep-col">:</td>
                <td>Rp</td>
                <td class="amount"><?= fmtMoney($loans) ?></td>
                <td class="spacer-col"></td>
                <td></td>
                <td class="sep-col"></td>
                <td></td>
                <td class="amount"></td>
            </tr>
            <!-- Row 6: Kurs | (empty) -->
            <tr>
                <td>Kurs</td>
                <td class="sep-col">:</td>
                <td>Rp</td>
                <td class="amount"><?= fmtMoney($kursRate) ?></td>
                <td class="spacer-col"></td>
                <td></td>
                <td class="sep-col"></td>
                <td></td>
                <td class="amount"></td>
            </tr>
            <!-- Row 7: IDR | (empty) -->
            <tr>
                <td>IDR</td>
                <td class="sep-col">:</td>
                <td>Rp</td>
                <td class="amount"><?= fmtMoney($idrGross) ?></td>
                <td class="spacer-col"></td>
                <td></td>
                <td class="sep-col"></td>
                <td></td>
                <td class="amount"></td>
            </tr>
            <!-- Gross Row -->
            <tr class="gross-row">
                <td><strong>Gross</strong></td>
                <td class="sep-col"></td>
                <td><strong>Rp</strong></td>
                <td class="amount"><strong><?= fmtMoney($totalGrossRp) ?></strong></td>
                <td class="spacer-col"></td>
                <td></td>
                <td class="sep-col"></td>
                <td><strong>Rp</strong></td>
                <td class="amount"><strong><?= fmtMoney($totalDeductionsRp) ?></strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Footer: Bank Info + Net Pay -->
    <div class="footer-section">
        <table class="footer-table">
            <tr>
                <td class="footer-left">
                    <p>Paid By Bank Transfer</p>
                    <p>Acc. Holder : <?= htmlspecialchars(strtoupper($crew['bank_holder'] ?? $crewName)) ?></p>
                    <p>Acc. No&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?= htmlspecialchars($crew['bank_account'] ?? '-') ?></p>
                    <p>Bank&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?= htmlspecialchars(strtoupper($crew['bank_name'] ?? '-')) ?></p>
                </td>
                <td class="footer-right">
                    <p class="net-pay-label">Net Take-Home Pay</p>
                    <p class="net-pay-amount">Rp &nbsp; <?= fmtMoney($netSalary) ?></p>
                </td>
            </tr>
        </table>
    </div>
</div>

</body>
</html>
