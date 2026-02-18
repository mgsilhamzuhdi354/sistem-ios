<?php
/**
 * Payroll View
 */
$currentPage = 'payroll';
$months = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 data-translate="payroll_management">Payroll Management</h1>
        <p data-translate="payroll_subtitle">Process crew salaries and taxes</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="<?= BASE_URL ?>payroll/export/<?= $period['id'] ?? 0 ?>" class="btn btn-secondary"><i
                class="fas fa-download"></i> <span data-translate="btn_export_csv">Export CSV</span></a>
        <form method="POST" action="<?= BASE_URL ?>payroll/process" style="display: inline;">
            <input type="hidden" name="month" value="<?= $month ?>">
            <input type="hidden" name="year" value="<?= $year ?>">
            <button type="submit" class="btn btn-primary"><i class="fas fa-play"></i> <span
                    data-translate="run_payroll">Run Payroll</span></button>
        </form>
    </div>
</div>

<!-- Period Selector -->
<div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
    <a href="?month=<?= $month == 1 ? 12 : $month - 1 ?>&year=<?= $month == 1 ? $year - 1 : $year ?>"
        class="btn-icon"><i class="fas fa-chevron-left"></i></a>
    <div style="text-align: center;">
        <span style="display: block; font-size: 12px; color: var(--text-muted);">Payroll Period</span>
        <span style="display: block; font-size: 20px; font-weight: 700;"><?= $months[$month] ?> <?= $year ?></span>
    </div>
    <a href="?month=<?= $month == 12 ? 1 : $month + 1 ?>&year=<?= $month == 12 ? $year + 1 : $year ?>"
        class="btn-icon"><i class="fas fa-chevron-right"></i></a>
    <span
        class="badge badge-<?= $period['status'] === 'completed' ? 'success' : ($period['status'] === 'processing' ? 'warning' : 'secondary') ?>"
        style="margin-left: 16px;">
        <?= ucfirst($period['status'] ?? 'draft') ?>
    </span>
</div>

<!-- Summary Cards -->
<div class="grid-4" style="margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <h3><?= $period['total_crew'] ?? 0 ?></h3>
            <p data-translate="total_crew">Total Crew</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fas fa-money-bill-wave"></i></div>
        <div class="stat-info">
            <h3>$<?= number_format($period['total_gross'] ?? 0, 2) ?></h3>
            <p data-translate="gross_salary">Gross Salary</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-percentage"></i></div>
        <div class="stat-info">
            <h3>$<?= number_format($period['total_tax'] ?? 0, 2) ?></h3>
            <p data-translate="total_tax">Total Tax (5%)</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info">
            <h3>$<?= number_format($period['total_net'] ?? 0, 2) ?></h3>
            <p data-translate="net_payable">Net Payable</p>
        </div>
    </div>
</div>

<!-- Payroll Table -->
<div class="table-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3>Payroll Details</h3>
        <span style="font-size: 12px; color: var(--text-muted);">* Semua nilai dalam USD dengan 2 desimal untuk
            akurasi</span>
    </div>
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th data-translate="crew_name">Crew Name</th>
                    <th data-translate="th_rank">Rank</th>
                    <th data-translate="th_vessel">Vessel</th>
                    <th style="text-align: right;" data-translate="original">Original</th>
                    <th style="text-align: right;" data-translate="basic">Basic</th>
                    <th style="text-align: right;" data-translate="allowances">Allowances</th>
                    <th style="text-align: right;" data-translate="gross">Gross</th>
                    <th style="text-align: right;" data-translate="deductions">Deductions</th>
                    <th style="text-align: right;" data-translate="tax">Tax (5%)</th>
                    <th style="text-align: right;" data-translate="net">Net</th>
                    <th data-translate="th_status">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="11" style="text-align: center; color: var(--text-muted); padding: 40px;">
                            <i class="fas fa-calculator"
                                style="font-size: 40px; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                            <span data-translate="no_payroll_data">No payroll data. Click "Run Payroll" to generate.</span>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $item):
                        $originalCurrency = $item['original_currency'] ?? 'USD';
                        $originalGross = $item['original_gross'] ?? $item['gross_salary'];
                        $exchangeRate = $item['exchange_rate'] ?? 1;

                        // Accurate calculation values
                        $basicSalary = (float) $item['basic_salary'];
                        $overtime = (float) $item['overtime'];
                        $leavePay = (float) $item['leave_pay'];
                        $bonus = (float) $item['bonus'];
                        $otherAllowance = (float) $item['other_allowance'];
                        $grossSalary = (float) $item['gross_salary'];
                        $totalDeductions = (float) $item['total_deductions'];
                        $taxAmount = (float) $item['tax_amount'];
                        $netSalary = (float) $item['net_salary'];

                        // Total allowances
                        $totalAllowances = $overtime + $leavePay + $bonus + $otherAllowance;

                        // Format original currency display
                        $currencySymbols = ['USD' => '$', 'IDR' => 'Rp', 'SGD' => 'S$', 'EUR' => '€'];
                        $symbol = $currencySymbols[$originalCurrency] ?? $originalCurrency . ' ';
                        ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($item['crew_name']) ?></strong></td>
                            <td><?= htmlspecialchars($item['rank_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($item['vessel_name'] ?? '-') ?></td>
                            <td style="text-align: right;">
                                <?php if ($originalCurrency !== 'USD'): ?>
                                    <span style="font-size: 11px;"><?= $symbol ?><?= number_format($originalGross, 0) ?></span>
                                    <br><span
                                        style="font-size: 9px; color: var(--warning);">×<?= number_format($exchangeRate, 6) ?></span>
                                <?php else: ?>
                                    <span style="color: var(--text-muted);">—</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;">$<?= number_format($basicSalary, 2) ?></td>
                            <td style="text-align: right;">
                                <?php if ($totalAllowances > 0): ?>
                                    $<?= number_format($totalAllowances, 2) ?>
                                <?php else: ?>
                                    <span style="color: var(--text-muted);">$0.00</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right; font-weight: 600;">$<?= number_format($grossSalary, 2) ?></td>
                            <td style="text-align: right; color: var(--danger);">
                                <?php if ($totalDeductions > 0): ?>
                                    -$<?= number_format($totalDeductions, 2) ?>
                                <?php else: ?>
                                    <span style="color: var(--text-muted);">$0.00</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right; color: var(--danger);">-$<?= number_format($taxAmount, 2) ?></td>
                            <td style="text-align: right; color: var(--success); font-weight: 700; font-size: 14px;">
                                $<?= number_format($netSalary, 2) ?></td>
                            <td><span
                                    class="badge badge-<?= $item['status'] === 'paid' ? 'success' : 'warning' ?>"><?= ucfirst($item['status']) ?></span>
                            </td>
                        </tr>
                        <!-- Calculation breakdown row -->
                        <tr style="background: rgba(0,0,0,0.2); font-size: 10px; color: var(--text-muted);">
                            <td colspan="4" style="text-align: right; padding: 4px 12px;">Rumus:</td>
                            <td colspan="7" style="padding: 4px 12px;">
                                Gross ($<?= number_format($grossSalary, 2) ?>) - Deductions
                                ($<?= number_format($totalDeductions, 2) ?>) - Tax ($<?= number_format($taxAmount, 2) ?>) =
                                <strong style="color: var(--success);">Net $<?= number_format($netSalary, 2) ?></strong>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>