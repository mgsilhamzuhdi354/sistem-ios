<?php
/**
 * Tax Report (PPh 21) View
 */
$currentPage = 'reports';
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1><i class="fas fa-percent" style="color: var(--accent-gold);"></i> Tax Report (PPh 21)</h1>
        <p><?= date('F Y', strtotime("$year-$month-01")) ?></p>
    </div>
    <a href="<?= BASE_URL ?>reports" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<!-- Period Filter -->
<div class="card" style="margin-bottom: 24px;">
    <form method="GET" action="<?= BASE_URL ?>reports/tax" style="display: flex; gap: 16px; align-items: center;">
        <label>Period:</label>
        <select name="month" class="form-control" style="width: 120px;">
            <?php 
            $months = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?= $m ?>" <?= ($month ?? date('n')) == $m ? 'selected' : '' ?>><?= $months[$m] ?></option>
            <?php endfor; ?>
        </select>
        <select name="year" class="form-control" style="width: 100px;">
            <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
                <option value="<?= $y ?>" <?= ($year ?? date('Y')) == $y ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
        <button type="submit" class="btn btn-secondary">View</button>
    </form>
</div>

<!-- Total Tax -->
<div class="stat-card" style="margin-bottom: 24px;">
    <div class="stat-icon red"><i class="fas fa-percent"></i></div>
    <div class="stat-info">
        <h3>$<?= number_format($totalTax ?? 0, 2) ?></h3>
        <p>Total PPh 21 for this period</p>
    </div>
</div>

<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Crew Name</th>
                <th>Rank</th>
                <th>Vessel</th>
                <th>Tax Type</th>
                <th style="text-align: right;">Gross Salary</th>
                <th style="text-align: right;">Tax Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px;">No payroll data for this period</td></tr>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($item['crew_name'] ?? '-') ?></strong></td>
                        <td><?= htmlspecialchars($item['rank_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($item['vessel_name'] ?? '-') ?></td>
                        <td>
                            <?php 
                            $taxLabel = [
                                'pph21' => 'PPh 21 (NPWP)',
                                'pph21_non_npwp' => 'PPh 21 (Non-NPWP)',
                                'exempt' => 'Exempt',
                                'foreign' => 'Foreign'
                            ];
                            echo $taxLabel[$item['tax_type']] ?? $item['tax_type']; 
                            ?>
                        </td>
                        <td style="text-align: right;">$<?= number_format($item['gross_salary'] ?? 0, 2) ?></td>
                        <td style="text-align: right; color: var(--danger); font-weight: 600;">$<?= number_format($item['tax_amount'] ?? 0, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <?php if (!empty($items)): ?>
        <tfoot style="background: rgba(0,0,0,0.2);">
            <tr>
                <td colspan="4"><strong>Total</strong></td>
                <td style="text-align: right;"><strong>$<?= number_format(array_sum(array_column($items, 'gross_salary')), 2) ?></strong></td>
                <td style="text-align: right; color: var(--danger);"><strong>$<?= number_format($totalTax ?? 0, 2) ?></strong></td>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>
</div>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>
