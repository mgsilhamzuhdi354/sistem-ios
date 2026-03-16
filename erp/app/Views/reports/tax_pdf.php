<?php
/**
 * Tax Report (PPh 21) - PDF View
 */
$monthNames = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$reportTitle = 'TAX REPORT — PPh 21';
$reportSubtitle = ($monthNames[$month ?? 1] ?? '') . ' ' . ($year ?? date('Y'));
$reportDate = date('d F Y');
$totalItems = count($items ?? []);
$totalTax = $totalTax ?? 0;

include APPPATH . 'Views/partials/pdf_header.php';
?>

    <!-- Summary -->
    <div class="summary-grid">
        <div class="summary-card">
            <div class="label">Total Crew</div>
            <div class="value text-blue"><?= $totalItems ?></div>
        </div>
        <div class="summary-card">
            <div class="label">Total PPh 21</div>
            <div class="value text-red" style="font-size:14pt">$ <?= number_format($totalTax, 2) ?></div>
        </div>
        <div class="summary-card">
            <div class="label">Period</div>
            <div class="value" style="font-size:11pt"><?= ($monthNames[$month ?? 1] ?? '') . ' ' . ($year ?? '') ?></div>
        </div>
    </div>

    <!-- Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th style="width:25px">No</th>
                <th>Crew Name</th>
                <th>Rank</th>
                <th>Vessel</th>
                <th class="text-right">Gross Salary</th>
                <th class="text-center">Tax Rate</th>
                <th class="text-right">Tax Amount (PPh 21)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
            <tr><td colspan="7" style="text-align:center; padding:30px; color:#999;">No tax data for this period</td></tr>
            <?php else: ?>
                <?php foreach ($items as $i => $item): ?>
                <tr>
                    <td class="text-center"><?= $i + 1 ?></td>
                    <td class="font-bold"><?= htmlspecialchars($item['crew_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($item['rank_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($item['vessel_name'] ?? '-') ?></td>
                    <td class="text-right"><?= number_format($item['gross_salary'] ?? 0, 2) ?></td>
                    <td class="text-center"><?= number_format($item['tax_rate'] ?? 2.5, 1) ?>%</td>
                    <td class="text-right font-bold" style="color:#dc2626"><?= number_format($item['tax_amount'] ?? 0, 2) ?></td>
                </tr>
                <?php endforeach; ?>
                <!-- Total -->
                <tr style="background:#e8edf3 !important; border-top:2px solid #1e3a5f;">
                    <td colspan="6" class="font-bold text-right" style="color:#1e3a5f">TOTAL PPh 21</td>
                    <td class="text-right font-bold" style="color:#dc2626; font-size:10pt">$ <?= number_format($totalTax, 2) ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top:15px; padding:12px; background:#fef2f2; border-left:4px solid #dc2626; border-radius:4px; font-size:8pt; color:#991b1b;">
        <strong>⚠️ Disclaimer:</strong> This is an internal tax calculation report. 
        Actual tax filing must comply with Indonesian Directorate General of Taxation (DJP) regulations.
        Please consult with your tax advisor for official tax reporting.
    </div>

<?php include APPPATH . 'Views/partials/pdf_footer.php'; ?>
