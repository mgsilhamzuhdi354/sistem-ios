<?php
/**
 * Payroll Summary Report View
 */
$currentPage = 'reports';
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1><i class="fas fa-money-bill-wave" style="color: var(--accent-gold);"></i> Payroll Summary - <?= $year ?? date('Y') ?></h1>
        <p>Monthly payroll overview for the year</p>
    </div>
    <a href="<?= BASE_URL ?>reports" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<!-- Year Filter -->
<div class="card" style="margin-bottom: 24px;">
    <form method="GET" action="<?= BASE_URL ?>reports/payroll" style="display: flex; gap: 16px; align-items: center;">
        <label>Year:</label>
        <select name="year" class="form-control" style="width: 120px;" onchange="this.form.submit()">
            <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                <option value="<?= $y ?>" <?= ($year ?? date('Y')) == $y ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
    </form>
</div>

<!-- Summary Stats -->
<?php 
$totalGross = array_sum(array_column($periods ?? [], 'total_gross'));
$totalNet = array_sum(array_column($periods ?? [], 'total_net'));
$totalTax = array_sum(array_column($periods ?? [], 'total_tax'));
?>
<div class="grid-3" style="margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-coins"></i></div>
        <div class="stat-info">
            <h3>$<?= number_format($totalGross, 0) ?></h3>
            <p>Total Gross (YTD)</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-percent"></i></div>
        <div class="stat-info">
            <h3>$<?= number_format($totalTax, 0) ?></h3>
            <p>Total Tax (YTD)</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fas fa-hand-holding-usd"></i></div>
        <div class="stat-info">
            <h3>$<?= number_format($totalNet, 0) ?></h3>
            <p>Total Net (YTD)</p>
        </div>
    </div>
</div>

<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Period</th>
                <th>Status</th>
                <th>Crew Count</th>
                <th style="text-align: right;">Gross</th>
                <th style="text-align: right;">Deductions</th>
                <th style="text-align: right;">Tax</th>
                <th style="text-align: right;">Net</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($periods)): ?>
                <tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 40px;">No payroll data for this year</td></tr>
            <?php else: ?>
                <?php 
                $months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                foreach ($periods as $p): 
                    $statusColor = $p['status'] === 'completed' ? 'success' : ($p['status'] === 'processing' ? 'warning' : 'secondary');
                ?>
                    <tr>
                        <td><strong><?= $months[$p['period_month']] ?> <?= $p['period_year'] ?></strong></td>
                        <td><span class="badge badge-<?= $statusColor ?>"><?= ucfirst($p['status']) ?></span></td>
                        <td><?= $p['total_crew'] ?? 0 ?></td>
                        <td style="text-align: right;">$<?= number_format($p['total_gross'] ?? 0, 2) ?></td>
                        <td style="text-align: right; color: var(--danger);">-$<?= number_format($p['total_deductions'] ?? 0, 2) ?></td>
                        <td style="text-align: right; color: var(--warning);">-$<?= number_format($p['total_tax'] ?? 0, 2) ?></td>
                        <td style="text-align: right; font-weight: 600; color: var(--accent-gold);">$<?= number_format($p['total_net'] ?? 0, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <?php if (!empty($periods)): ?>
        <tfoot style="background: rgba(0,0,0,0.2);">
            <tr>
                <td colspan="3"><strong>Year Total</strong></td>
                <td style="text-align: right;"><strong>$<?= number_format($totalGross, 2) ?></strong></td>
                <td style="text-align: right; color: var(--danger);"><strong>-$<?= number_format(array_sum(array_column($periods, 'total_deductions')), 2) ?></strong></td>
                <td style="text-align: right; color: var(--warning);"><strong>-$<?= number_format($totalTax, 2) ?></strong></td>
                <td style="text-align: right; color: var(--accent-gold);"><strong>$<?= number_format($totalNet, 2) ?></strong></td>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>
</div>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>
