<?php
/**
 * Contract Renewal View (Feature 11)
 * One-click renewal with auto copy of salary, tax, allowances
 */
$currentPage = 'contracts';
ob_start();
?>

<div class="page-header">
    <h1><i class="fas fa-redo" style="color: var(--success);"></i> Renew Contract</h1>
    <p>Create a new contract based on <?= htmlspecialchars($contract['contract_no']) ?></p>
</div>

<div class="grid-2">
    <!-- Previous Contract Summary -->
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 16px;"><i class="fas fa-file-contract" style="color: var(--accent-gold);"></i> Previous Contract</h3>
        <table style="width: 100%;">
            <tr><td style="padding: 8px 0; color: var(--text-muted);">Contract No</td><td style="padding: 8px 0;"><strong><?= htmlspecialchars($contract['contract_no']) ?></strong></td></tr>
            <tr><td style="padding: 8px 0; color: var(--text-muted);">Crew Name</td><td style="padding: 8px 0;"><?= htmlspecialchars($contract['crew_name']) ?></td></tr>
            <tr><td style="padding: 8px 0; color: var(--text-muted);">Rank</td><td style="padding: 8px 0;"><?= htmlspecialchars($contract['rank_name'] ?? '-') ?></td></tr>
            <tr><td style="padding: 8px 0; color: var(--text-muted);">Vessel</td><td style="padding: 8px 0;"><?= htmlspecialchars($contract['vessel_name'] ?? '-') ?></td></tr>
            <tr><td style="padding: 8px 0; color: var(--text-muted);">Sign Off Date</td><td style="padding: 8px 0;"><?= $contract['sign_off_date'] ? date('d M Y', strtotime($contract['sign_off_date'])) : '-' ?></td></tr>
            <tr style="border-top: 1px solid var(--border-color);">
                <td style="padding: 12px 0; color: var(--text-muted);">Monthly Salary</td>
                <td style="padding: 12px 0; font-size: 18px; color: var(--accent-gold); font-weight: 700;">
                    $<?= number_format($contract['total_monthly'] ?? 0, 2) ?>
                </td>
            </tr>
        </table>
    </div>

    <!-- New Contract Form -->
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 16px;"><i class="fas fa-plus-circle" style="color: var(--success);"></i> New Contract Details</h3>
        <form method="POST" action="<?= BASE_URL ?>contracts/renew/<?= $contract['id'] ?>">
            <div class="form-group">
                <label class="form-label">New Contract Number</label>
                <input type="text" name="contract_no" class="form-control" value="<?= htmlspecialchars($newContractNo ?? '') ?>" readonly>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Vessel</label>
                    <select name="vessel_id" class="form-control">
                        <?php foreach ($vessels as $v): ?>
                            <option value="<?= $v['id'] ?>" <?= ($contract['vessel_id'] ?? '') == $v['id'] ? 'selected' : '' ?>><?= htmlspecialchars($v['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Client</label>
                    <select name="client_id" class="form-control">
                        <?php foreach ($clients as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($contract['client_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Rank</label>
                <select name="rank_id" class="form-control">
                    <?php foreach ($ranks as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= ($contract['rank_id'] ?? '') == $r['id'] ? 'selected' : '' ?>><?= htmlspecialchars($r['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">New Sign On Date</label>
                    <input type="date" name="sign_on_date" class="form-control" value="<?= date('Y-m-d', strtotime($contract['sign_off_date'] ?? 'now')) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Duration (Months)</label>
                    <input type="number" name="duration_months" class="form-control" value="<?= $contract['duration_months'] ?? 9 ?>" min="1" max="36" required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">New Sign Off Date</label>
                <input type="date" name="sign_off_date" class="form-control" id="sign_off_date">
            </div>
            
            <div class="alert alert-info" style="margin-top: 16px;">
                <i class="fas fa-info-circle"></i>
                <span>Salary, tax settings, and allowances will be automatically copied from the previous contract.</span>
            </div>
            
            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 20px;">
                <a href="<?= BASE_URL ?>contracts/<?= $contract['id'] ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-success"><i class="fas fa-redo"></i> Create Renewal Contract</button>
            </div>
        </form>
    </div>
</div>

<script>
// Auto calculate sign-off date
document.querySelector('input[name="sign_on_date"]').addEventListener('change', calculateSignOff);
document.querySelector('input[name="duration_months"]').addEventListener('change', calculateSignOff);

function calculateSignOff() {
    const signOn = document.querySelector('input[name="sign_on_date"]').value;
    const months = parseInt(document.querySelector('input[name="duration_months"]').value) || 9;
    if (signOn) {
        const date = new Date(signOn);
        date.setMonth(date.getMonth() + months);
        document.getElementById('sign_off_date').value = date.toISOString().split('T')[0];
    }
}
calculateSignOff();
</script>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>
