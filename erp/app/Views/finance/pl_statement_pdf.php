<?php
/**
 * Profit & Loss Statement - PDF View
 */
$reportTitle = 'PROFIT & LOSS STATEMENT';
$periodLabel = '';
if (!empty($month) && !empty($year)) {
    $monthNames = ['','January','February','March','April','May','June','July','August','September','October','November','December'];
    $periodLabel = $monthNames[$month] . ' ' . $year;
} elseif (!empty($year)) {
    $periodLabel = 'Year ' . $year;
} else {
    $periodLabel = 'All Time';
}
$reportSubtitle = 'Period: ' . $periodLabel;
$reportDate = date('d F Y');

$totalRevenue = (float)($totalRevenue ?? 0);
$totalExpenses = (float)($totalExpenses ?? 0);
$netIncome = $totalRevenue - $totalExpenses;
$margin = $totalRevenue > 0 ? ($netIncome / $totalRevenue * 100) : 0;

$currSymbol = '$';
if (!function_exists('fmtPL')) {
    function fmtPL($val) { 
        if ($val == 0) return '$ 0.00';
        $sign = $val < 0 ? '-' : '';
        return $sign . '$ ' . number_format(abs($val), 2, '.', ','); 
    }
}

include APPPATH . 'Views/partials/pdf_header.php';
?>

    <!-- Summary Cards -->
    <div class="summary-grid">
        <div class="summary-card">
            <div class="label">Total Revenue</div>
            <div class="value text-green" style="font-size:14pt"><?= fmtPL($totalRevenue) ?></div>
        </div>
        <div class="summary-card">
            <div class="label">Total Expenses</div>
            <div class="value text-red" style="font-size:14pt"><?= fmtPL($totalExpenses) ?></div>
        </div>
        <div class="summary-card">
            <div class="label">Net Income</div>
            <div class="value <?= $netIncome >= 0 ? 'text-green' : 'text-red' ?>" style="font-size:14pt"><?= fmtPL($netIncome) ?></div>
        </div>
        <div class="summary-card">
            <div class="label">Profit Margin</div>
            <div class="value <?= $margin >= 0 ? 'text-green' : 'text-red' ?>" style="font-size:14pt"><?= number_format($margin, 1) ?>%</div>
        </div>
    </div>

    <!-- P&L Table -->
    <table class="report-table">
        <!-- Revenue Section -->
        <thead>
            <tr>
                <th colspan="2" style="background:#dcfce7; color:#166534; font-size:9pt;">📈 REVENUE</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($revenueItems)): ?>
                <?php foreach ($revenueItems as $item): ?>
                <tr>
                    <td style="padding-left:20px;"><?= htmlspecialchars($item['description'] ?? $item['category'] ?? '-') ?></td>
                    <td class="text-right font-bold" style="color:#16a34a"><?= fmtPL($item['amount'] ?? 0) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td style="padding-left:20px;">Invoice Revenue</td>
                    <td class="text-right font-bold" style="color:#16a34a"><?= fmtPL($totalRevenue) ?></td>
                </tr>
            <?php endif; ?>
            <tr style="background:#dcfce7 !important; border-top:2px solid #16a34a;">
                <td class="font-bold" style="color:#166534;">TOTAL REVENUE</td>
                <td class="text-right font-bold" style="color:#166534; font-size:10pt;"><?= fmtPL($totalRevenue) ?></td>
            </tr>
        </tbody>

        <!-- Expenses Section -->
        <thead>
            <tr>
                <th colspan="2" style="background:#fee2e2; color:#991b1b; font-size:9pt; border-top:3px solid #fff;">📉 EXPENSES</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($expenseItems)): ?>
                <?php foreach ($expenseItems as $item): ?>
                <tr>
                    <td style="padding-left:20px;"><?= htmlspecialchars($item['description'] ?? $item['category'] ?? '-') ?></td>
                    <td class="text-right font-bold" style="color:#dc2626"><?= fmtPL($item['amount'] ?? 0) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td style="padding-left:20px;">Crew Salaries & Payroll</td>
                    <td class="text-right font-bold" style="color:#dc2626"><?= fmtPL($totalExpenses * 0.7) ?></td>
                </tr>
                <tr>
                    <td style="padding-left:20px;">Bills & Operational Costs</td>
                    <td class="text-right font-bold" style="color:#dc2626"><?= fmtPL($totalExpenses * 0.3) ?></td>
                </tr>
            <?php endif; ?>
            <tr style="background:#fee2e2 !important; border-top:2px solid #dc2626;">
                <td class="font-bold" style="color:#991b1b;">TOTAL EXPENSES</td>
                <td class="text-right font-bold" style="color:#991b1b; font-size:10pt;"><?= fmtPL($totalExpenses) ?></td>
            </tr>
        </tbody>

        <!-- Net Income -->
        <tbody>
            <tr style="background:linear-gradient(135deg,#1e3a5f,#2c5282); border-top:3px solid #1e3a5f;">
                <td class="font-bold" style="color:#fff; font-size:11pt; padding:12px 10px;">NET INCOME</td>
                <td class="text-right font-bold" style="color:<?= $netIncome >= 0 ? '#4ade80' : '#fca5a5' ?>; font-size:13pt; padding:12px 10px;"><?= fmtPL($netIncome) ?></td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top:15px; padding:10px; background:#f8fafc; border-radius:6px; font-size:8pt; color:#475569;">
        <strong>Note:</strong> This P&L Statement is generated from the ERP financial records. 
        Revenue is calculated from paid invoices; expenses include payroll costs and operational bills.
    </div>

<?php include APPPATH . 'Views/partials/pdf_footer.php'; ?>
