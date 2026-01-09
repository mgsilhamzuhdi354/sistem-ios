<?php
/**
 * Reports Index View
 */
$currentPage = 'reports';
ob_start();
?>

<div class="page-header">
    <h1>Reports</h1>
    <p>Generate and download reports</p>
</div>

<div class="grid-2">
    <!-- Contract Reports -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 16px 20px; background: rgba(0,0,0,0.2); border-bottom: 1px solid var(--border-color);">
            <h3><i class="fas fa-file-contract" style="color: var(--accent-gold);"></i> Contract Reports</h3>
        </div>
        <div style="padding: 8px 0;">
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border-color);" class="report-item">
                <div><strong>Active Contracts</strong><br><small style="color: var(--text-muted);">List of all currently active contracts</small></div>
                <div style="display: flex; gap: 8px;">
                    <a href="<?= BASE_URL ?>reports/activeContracts" class="btn btn-secondary btn-sm"><i class="fas fa-eye"></i></a>
                    <a href="<?= BASE_URL ?>reports/export/active" class="btn btn-primary btn-sm"><i class="fas fa-download"></i></a>
                </div>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border-color);" class="report-item">
                <div><strong>Expiring Contracts</strong><br><small style="color: var(--text-muted);">Contracts expiring within 60 days</small></div>
                <div style="display: flex; gap: 8px;">
                    <a href="<?= BASE_URL ?>reports/expiringContracts" class="btn btn-secondary btn-sm"><i class="fas fa-eye"></i></a>
                    <a href="<?= BASE_URL ?>reports/export/expiring" class="btn btn-primary btn-sm"><i class="fas fa-download"></i></a>
                </div>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border-color);" class="report-item">
                <div><strong>Contracts by Vessel</strong><br><small style="color: var(--text-muted);">Contract breakdown per vessel</small></div>
                <div style="display: flex; gap: 8px;">
                    <a href="<?= BASE_URL ?>reports/contractsByVessel" class="btn btn-secondary btn-sm"><i class="fas fa-eye"></i></a>
                </div>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px 20px;" class="report-item">
                <div><strong>Contracts by Client</strong><br><small style="color: var(--text-muted);">Contract summary per client/principal</small></div>
                <div style="display: flex; gap: 8px;">
                    <a href="<?= BASE_URL ?>reports/contractsByClient" class="btn btn-secondary btn-sm"><i class="fas fa-eye"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Payroll Reports -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 16px 20px; background: rgba(0,0,0,0.2); border-bottom: 1px solid var(--border-color);">
            <h3><i class="fas fa-money-bill-wave" style="color: var(--accent-gold);"></i> Payroll Reports</h3>
        </div>
        <div style="padding: 8px 0;">
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border-color);" class="report-item">
                <div><strong>Monthly Payroll Summary</strong><br><small style="color: var(--text-muted);">Complete payroll breakdown per month</small></div>
                <div style="display: flex; gap: 8px;">
                    <a href="<?= BASE_URL ?>reports/payrollSummary" class="btn btn-secondary btn-sm"><i class="fas fa-eye"></i></a>
                </div>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border-color);" class="report-item">
                <div><strong>Tax Report (PPh 21)</strong><br><small style="color: var(--text-muted);">Tax deduction report for tax filing</small></div>
                <div style="display: flex; gap: 8px;">
                    <a href="<?= BASE_URL ?>reports/taxReport" class="btn btn-secondary btn-sm"><i class="fas fa-eye"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit Reports -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 16px 20px; background: rgba(0,0,0,0.2); border-bottom: 1px solid var(--border-color);">
            <h3><i class="fas fa-shield-alt" style="color: var(--accent-gold);"></i> Audit & Compliance</h3>
        </div>
        <div style="padding: 8px 0;">
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border-color);" class="report-item">
                <div><strong>Contract Change Log</strong><br><small style="color: var(--text-muted);">Audit trail of all contract changes</small></div>
                <div style="display: flex; gap: 8px;">
                    <a href="<?= BASE_URL ?>reports/auditLog" class="btn btn-secondary btn-sm"><i class="fas fa-eye"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.report-item:hover { background: var(--card-hover); }
</style>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>
