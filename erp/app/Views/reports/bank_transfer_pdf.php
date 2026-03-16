<?php
/**
 * Bank Transfer List - PDF View
 * List of bank transfers for payroll period
 */
$monthNames = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$reportTitle = 'BANK TRANSFER LIST';
$reportSubtitle = 'Payroll Period: ' . ($monthNames[$period['period_month'] ?? 1] ?? '') . ' ' . ($period['period_year'] ?? date('Y'));
$reportDate = date('d F Y');
$totalItems = count($items ?? []);
$totalTransfer = 0;
foreach ($items as $item) { $totalTransfer += (float)($item['net_salary'] ?? 0); }

include APPPATH . 'Views/partials/pdf_header.php';
?>

    <!-- Summary -->
    <div class="summary-grid">
        <div class="summary-card">
            <div class="label">Total Transfers</div>
            <div class="value text-blue"><?= $totalItems ?></div>
        </div>
        <div class="summary-card">
            <div class="label">Total Amount</div>
            <div class="value text-green" style="font-size:14pt">Rp <?= number_format($totalTransfer, 0, ',', '.') ?></div>
        </div>
    </div>

    <!-- Transfer Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th style="width:25px">No</th>
                <th>Crew Name</th>
                <th>Rank</th>
                <th>Bank Name</th>
                <th>Account Number</th>
                <th>Account Holder</th>
                <th class="text-right">Net Amount (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
            <tr><td colspan="7" style="text-align:center; padding:30px; color:#999;">No transfer data available</td></tr>
            <?php else: ?>
                <?php foreach ($items as $i => $item): ?>
                <tr>
                    <td class="text-center"><?= $i + 1 ?></td>
                    <td class="font-bold"><?= htmlspecialchars($item['crew_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($item['rank_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars(strtoupper($item['bank_name'] ?? '-')) ?></td>
                    <td class="font-bold" style="font-family:monospace; letter-spacing:1px"><?= htmlspecialchars($item['bank_account'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($item['bank_holder'] ?? $item['crew_name'] ?? '-') ?></td>
                    <td class="text-right font-bold" style="color:#16a34a">Rp <?= number_format($item['net_salary'] ?? 0, 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
                <!-- Total -->
                <tr style="background:#e8edf3 !important; border-top:2px solid #1e3a5f;">
                    <td colspan="6" class="font-bold text-right" style="color:#1e3a5f">TOTAL TRANSFER</td>
                    <td class="text-right font-bold" style="color:#16a34a; font-size:10pt">Rp <?= number_format($totalTransfer, 0, ',', '.') ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Approval Section -->
    <div style="margin-top:30px; display:flex; justify-content:space-between; padding:0 40px;">
        <div style="text-align:center; width:200px;">
            <p style="font-size:8pt; color:#666; margin-bottom:60px;">Prepared by:</p>
            <div style="border-top:1px solid #333; padding-top:5px;">
                <p style="font-size:8pt; font-weight:700;">Finance Department</p>
            </div>
        </div>
        <div style="text-align:center; width:200px;">
            <p style="font-size:8pt; color:#666; margin-bottom:60px;">Approved by:</p>
            <div style="border-top:1px solid #333; padding-top:5px;">
                <p style="font-size:8pt; font-weight:700;">Director</p>
            </div>
        </div>
    </div>

<?php include APPPATH . 'Views/partials/pdf_footer.php'; ?>
