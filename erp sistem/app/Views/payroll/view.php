<?php
/**
 * Payroll Detail View
 */
$currentPage = 'payroll';
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1><i class="fas fa-money-bill-wave" style="color: var(--accent-gold);"></i> Payroll - <?= date('F Y', strtotime($period['start_date'])) ?></h1>
        <p>Period: <?= date('d M Y', strtotime($period['start_date'])) ?> - <?= date('d M Y', strtotime($period['end_date'])) ?></p>
    </div>
    <div style="display: flex; gap: 10px;">
        <?php 
        $statusColors = ['draft' => 'secondary', 'processing' => 'warning', 'completed' => 'success', 'locked' => 'info'];
        ?>
        <span class="badge badge-<?= $statusColors[$period['status']] ?? 'secondary' ?>" style="font-size: 14px; padding: 8px 16px;">
            <?= ucfirst($period['status']) ?>
        </span>
        <a href="<?= BASE_URL ?>payroll/export/<?= $period['id'] ?>" class="btn btn-secondary">
            <i class="fas fa-download"></i> Export CSV
        </a>
        <a href="<?= BASE_URL ?>payroll" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<!-- Summary Stats -->
<div class="grid-4" style="margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <h3><?= $period['total_crew'] ?? 0 ?></h3>
            <p>Total Crew</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-coins"></i></div>
        <div class="stat-info">
            <h3>$<?= number_format($period['total_gross'] ?? 0, 0) ?></h3>
            <p>Total Gross</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-percent"></i></div>
        <div class="stat-info">
            <h3>$<?= number_format($period['total_tax'] ?? 0, 0) ?></h3>
            <p>Total Tax</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fas fa-hand-holding-usd"></i></div>
        <div class="stat-info">
            <h3>$<?= number_format($period['total_net'] ?? 0, 0) ?></h3>
            <p>Total Net</p>
        </div>
    </div>
</div>

<!-- Summary by Vessel -->
<?php if (!empty($summary)): ?>
<div class="card" style="margin-bottom: 24px;">
    <h3 style="margin-bottom: 16px;"><i class="fas fa-ship" style="color: var(--accent-gold);"></i> Summary by Vessel</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Vessel</th>
                <th style="text-align: center;">Crew</th>
                <th style="text-align: right;">Gross</th>
                <th style="text-align: right;">Deductions</th>
                <th style="text-align: right;">Tax</th>
                <th style="text-align: right;">Net</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($summary as $s): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($s['vessel_name'] ?? 'Unknown') ?></strong></td>
                    <td style="text-align: center;"><?= $s['crew_count'] ?></td>
                    <td style="text-align: right;">$<?= number_format($s['total_gross'], 2) ?></td>
                    <td style="text-align: right; color: var(--danger);">-$<?= number_format($s['total_deductions'], 2) ?></td>
                    <td style="text-align: right; color: var(--warning);">-$<?= number_format($s['total_tax'], 2) ?></td>
                    <td style="text-align: right; font-weight: 600; color: var(--accent-gold);">$<?= number_format($s['total_net'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- Detailed Items -->
<div class="table-card">
    <h3 style="margin-bottom: 16px;"><i class="fas fa-list" style="color: var(--accent-gold);"></i> Payroll Details</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Crew Name</th>
                <th>Rank</th>
                <th>Vessel</th>
                <th style="text-align: right;">Basic</th>
                <th style="text-align: right;">Gross</th>
                <th style="text-align: right;">Deductions</th>
                <th style="text-align: right;">Tax</th>
                <th style="text-align: right;">Net</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr><td colspan="9" style="text-align: center; color: var(--text-muted); padding: 40px;">No payroll items</td></tr>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($item['crew_name']) ?></strong></td>
                        <td><?= htmlspecialchars($item['rank_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($item['vessel_name'] ?? '-') ?></td>
                        <td style="text-align: right;">$<?= number_format($item['basic_salary'], 2) ?></td>
                        <td style="text-align: right;">$<?= number_format($item['gross_salary'], 2) ?></td>
                        <td style="text-align: right; color: var(--danger);">-$<?= number_format($item['total_deductions'], 2) ?></td>
                        <td style="text-align: right; color: var(--warning);">-$<?= number_format($item['tax_amount'], 2) ?></td>
                        <td style="text-align: right; font-weight: 600; color: var(--accent-gold);">$<?= number_format($item['net_salary'], 2) ?></td>
                        <td><span class="badge badge-<?= $item['status'] === 'paid' ? 'success' : 'secondary' ?>"><?= ucfirst($item['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>
