<?php
/**
 * Payroll Summary Report - PDF View
 */
$monthNames = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$reportTitle = 'PAYROLL SUMMARY REPORT';
$reportSubtitle = 'Period: ' . ($monthNames[$period['period_month'] ?? 1] ?? '') . ' ' . ($period['period_year'] ?? date('Y'));
$reportDate = date('d F Y');
$totalItems = count($items ?? []);

// Calculate totals
$totalGross = 0; $totalTax = 0; $totalDeductions = 0; $totalNet = 0;
foreach ($items as $item) {
    $totalGross += (float)($item['gross_salary'] ?? 0);
    $totalTax += (float)($item['tax_amount'] ?? 0);
    $totalDeductions += (float)($item['total_deductions'] ?? 0);
    $totalNet += (float)($item['net_salary'] ?? 0);
}

if (!function_exists('fmtRp')) {
    function fmtRp($val) { return $val == 0 ? '-' : 'Rp ' . number_format(abs($val), 0, ',', '.'); }
}
if (!function_exists('fmtUsd')) {
    function fmtUsd($val) { return $val == 0 ? '-' : '$ ' . number_format(abs($val), 2, '.', ','); }
}

include APPPATH . 'Views/partials/pdf_header.php';
?>

    <!-- Summary Cards -->
    <div class="summary-grid">
        <div class="summary-card">
            <div class="label">Total Crew</div>
            <div class="value text-blue"><?= $totalItems ?></div>
        </div>
        <div class="summary-card">
            <div class="label">Total Gross</div>
            <div class="value" style="font-size:12pt"><?= fmtUsd($totalGross) ?></div>
        </div>
        <div class="summary-card">
            <div class="label">Total Tax</div>
            <div class="value text-red" style="font-size:12pt"><?= fmtUsd($totalTax) ?></div>
        </div>
        <div class="summary-card">
            <div class="label">Total Net Pay</div>
            <div class="value text-green" style="font-size:12pt"><?= fmtUsd($totalNet) ?></div>
        </div>
    </div>

    <!-- Summary by Vessel -->
    <?php if (!empty($summary)): ?>
    <h3 style="font-size:10pt; font-weight:700; color:#1e3a5f; margin:15px 0 8px; border-bottom:2px solid #e2e8f0; padding-bottom:5px;">
        📊 Summary by Vessel
    </h3>
    <table class="report-table" style="margin-bottom:20px">
        <thead>
            <tr>
                <th>Vessel</th>
                <th class="text-center">Crew Count</th>
                <th class="text-right">Total Gross</th>
                <th class="text-right">Total Tax</th>
                <th class="text-right">Total Net</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($summary as $s): ?>
            <tr>
                <td class="font-bold"><?= htmlspecialchars($s['vessel_name'] ?? 'Unassigned') ?></td>
                <td class="text-center"><?= $s['crew_count'] ?? 0 ?></td>
                <td class="text-right"><?= fmtUsd($s['total_gross'] ?? 0) ?></td>
                <td class="text-right" style="color:#dc2626"><?= fmtUsd($s['total_tax'] ?? 0) ?></td>
                <td class="text-right font-bold" style="color:#16a34a"><?= fmtUsd($s['total_net'] ?? 0) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <!-- Detail Table -->
    <h3 style="font-size:10pt; font-weight:700; color:#1e3a5f; margin:15px 0 8px; border-bottom:2px solid #e2e8f0; padding-bottom:5px;">
        📋 Payroll Detail
    </h3>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width:25px">No</th>
                <th>Crew Name</th>
                <th>Rank</th>
                <th>Vessel</th>
                <th class="text-right">Gross Salary</th>
                <th class="text-right">Tax</th>
                <th class="text-right">Net Salary</th>
                <th class="text-center">Currency</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $i => $item): ?>
            <tr>
                <td class="text-center"><?= $i + 1 ?></td>
                <td class="font-bold"><?= htmlspecialchars($item['crew_name'] ?? '-') ?></td>
                <td><?= htmlspecialchars($item['rank_name'] ?? '-') ?></td>
                <td><?= htmlspecialchars($item['vessel_name'] ?? '-') ?></td>
                <td class="text-right"><?= number_format($item['gross_salary'] ?? 0, 2) ?></td>
                <td class="text-right" style="color:#dc2626"><?= number_format($item['tax_amount'] ?? 0, 2) ?></td>
                <td class="text-right font-bold" style="color:#16a34a"><?= number_format($item['net_salary'] ?? 0, 2) ?></td>
                <td class="text-center"><span class="badge badge-blue"><?= $item['currency_code'] ?? 'USD' ?></span></td>
            </tr>
            <?php endforeach; ?>
            <!-- Total Row -->
            <tr style="background:#e8edf3 !important; border-top:2px solid #1e3a5f;">
                <td colspan="4" class="font-bold text-right" style="color:#1e3a5f">TOTAL</td>
                <td class="text-right font-bold"><?= number_format($totalGross, 2) ?></td>
                <td class="text-right font-bold" style="color:#dc2626"><?= number_format($totalTax, 2) ?></td>
                <td class="text-right font-bold" style="color:#16a34a"><?= number_format($totalNet, 2) ?></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top:15px; padding:10px; background:#f0f4f8; border-radius:6px; font-size:8pt; color:#475569;">
        <strong>Notes:</strong> This payroll summary includes all crew with active contracts during the period. 
        Amounts are shown in the contract's original currency.
    </div>

<?php include APPPATH . 'Views/partials/pdf_footer.php'; ?>
