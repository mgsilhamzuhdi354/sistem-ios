<?php
/**
 * Payroll History View
 */
$currentPage = 'payroll-history';
$months = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
ob_start();
?>

<div class="page-header">
    <div>
        <h1 data-translate="payroll_history">Payroll History</h1>
        <p data-translate="payroll_history_subtitle">View all processed payroll periods</p>
    </div>
    <a href="<?= BASE_URL ?>payroll" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> <span
            data-translate="back_to_payroll">Back to Payroll</span></a>
</div>

<!-- Periods List -->
<div class="table-card">
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th data-translate="period">Period</th>
                    <th style="text-align: right;" data-translate="total_crew">Total Crew</th>
                    <th style="text-align: right;" data-translate="total_amount">Total Amount</th>
                    <th data-translate="th_status">Status</th>
                    <th style="text-align: right;" data-translate="th_actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($periods)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 40px;">
                            <i class="fas fa-folder-open"
                                style="font-size: 40px; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                            <span data-translate="no_payroll_history">No payroll history found</span>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($periods as $period):
                        $monthName = $months[$period['period_month']] ?? '';
                        $year = $period['period_year'];
                        $statusClass = $period['status'] === 'completed' ? 'success' : ($period['status'] === 'processing' ? 'warning' : 'secondary');
                        ?>
                        <tr>
                            <td>
                                <strong>
                                    <?= $monthName ?>
                                    <?= $year ?>
                                </strong>
                                <br>
                                <span style="font-size: 11px; color: var(--text-muted);">
                                    <?= date('M d, Y', strtotime($period['start_date'])) ?> -
                                    <?= date('M d, Y', strtotime($period['end_date'])) ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <?= $period['total_items'] ?>
                            </td>
                            <td style="text-align: right; font-weight: 600; color: var(--success);">
                                $
                                <?= number_format($period['total_amount'], 2) ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= $statusClass ?>">
                                    <?= ucfirst($period['status']) ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <a href="<?= BASE_URL ?>payroll/show/<?= $period['id'] ?>" class="btn-icon"
                                    title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= BASE_URL ?>payroll/export/<?= $period['id'] ?>" class="btn-icon"
                                    title="Export CSV">
                                    <i class="fas fa-download"></i>
                                </a>
                                <?php if ($period['status'] !== 'completed'): ?>
                                    <form method="POST" action="<?= BASE_URL ?>payroll/complete" style="display: inline;"
                                        onsubmit="return confirm('Mark this payroll as completed?')">
                                        <input type="hidden" name="period_id" value="<?= $period['id'] ?>">
                                        <button type="submit" class="btn-icon" title="Mark as Completed"
                                            style="color: var(--success); border: none; background: none; cursor: pointer;">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
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