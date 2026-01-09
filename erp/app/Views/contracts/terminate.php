<?php
/**
 * Contract Termination View (Feature 12)
 * Terminate contract with reason and final calculations
 */
$currentPage = 'contracts';
ob_start();
?>

<div class="page-header">
    <h1><i class="fas fa-times-circle" style="color: var(--danger);"></i> Terminate Contract</h1>
    <p>End contract <?= htmlspecialchars($contract['contract_no'] ?? '') ?> early</p>
</div>

<div class="grid-2">
    <!-- Contract Summary -->
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 16px;"><i class="fas fa-file-contract" style="color: var(--accent-gold);"></i> Contract Details</h3>
        <table style="width: 100%;">
            <tr><td style="padding: 8px 0; color: var(--text-muted);">Contract No</td><td style="padding: 8px 0;"><strong><?= htmlspecialchars($contract['contract_no'] ?? '-') ?></strong></td></tr>
            <tr><td style="padding: 8px 0; color: var(--text-muted);">Crew Name</td><td style="padding: 8px 0;"><?= htmlspecialchars($contract['crew_name'] ?? '-') ?></td></tr>
            <tr><td style="padding: 8px 0; color: var(--text-muted);">Sign On Date</td><td style="padding: 8px 0;"><?= isset($contract['sign_on_date']) ? date('d M Y', strtotime($contract['sign_on_date'])) : '-' ?></td></tr>
            <tr><td style="padding: 8px 0; color: var(--text-muted);">Expected Sign Off</td><td style="padding: 8px 0;"><?= isset($contract['sign_off_date']) ? date('d M Y', strtotime($contract['sign_off_date'])) : '-' ?></td></tr>
            <tr><td style="padding: 8px 0; color: var(--text-muted);">Status</td><td style="padding: 8px 0;"><span class="badge badge-success"><?= ucfirst($contract['status'] ?? 'active') ?></span></td></tr>
        </table>
    </div>

    <!-- Termination Form -->
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 16px;"><i class="fas fa-exclamation-triangle" style="color: var(--warning);"></i> Termination Details</h3>
        
        <div class="alert alert-warning" style="margin-bottom: 20px;">
            <i class="fas fa-exclamation-triangle"></i>
            <span><strong>Warning:</strong> This action cannot be undone. The contract will be marked as terminated.</span>
        </div>
        
        <form method="POST" action="<?= BASE_URL ?>contracts/terminate/<?= $contract['id'] ?>">
            <div class="form-group">
                <label class="form-label">Actual Sign Off Date</label>
                <input type="date" name="actual_sign_off_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Termination Reason <span style="color: var(--danger);">*</span></label>
                <select id="reason_select" class="form-control" onchange="setReason()" style="margin-bottom: 10px;">
                    <option value="">Select reason...</option>
                    <option value="Mutual Agreement">Mutual Agreement</option>
                    <option value="Contract Violation">Contract Violation</option>
                    <option value="Medical Reasons">Medical Reasons</option>
                    <option value="Personal Request">Personal Request (Crew)</option>
                    <option value="Performance Issues">Performance Issues</option>
                    <option value="Vessel Sold/Laid Up">Vessel Sold / Laid Up</option>
                    <option value="Other">Other (specify below)</option>
                </select>
                <textarea name="termination_reason" id="termination_reason" class="form-control" rows="4" 
                          placeholder="Provide detailed reason for termination..." required></textarea>
            </div>
            
            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 20px;">
                <a href="<?= BASE_URL ?>contracts/<?= $contract['id'] ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to terminate this contract?')">
                    <i class="fas fa-times"></i> Confirm Termination
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function setReason() {
    const select = document.getElementById('reason_select');
    const textarea = document.getElementById('termination_reason');
    if (select.value && select.value !== 'Other') {
        textarea.value = select.value + ': ';
        textarea.focus();
    }
}
</script>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>
