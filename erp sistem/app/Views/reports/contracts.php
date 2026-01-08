<?php
/**
 * Active Contracts Report View
 */
$currentPage = 'reports';
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1><i class="fas fa-file-contract" style="color: var(--accent-gold);"></i> Active Contracts Report</h1>
        <p>All currently active contracts</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="<?= BASE_URL ?>reports/export/active" class="btn btn-secondary">
            <i class="fas fa-download"></i> Export CSV
        </a>
        <a href="<?= BASE_URL ?>reports" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="stat-card" style="margin-bottom: 24px;">
    <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
    <div class="stat-info">
        <h3><?= count($contracts ?? []) ?></h3>
        <p>Total Active Contracts</p>
    </div>
</div>

<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Contract No</th>
                <th>Crew Name</th>
                <th>Rank</th>
                <th>Vessel</th>
                <th>Client</th>
                <th>Sign On</th>
                <th>Sign Off</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($contracts)): ?>
                <tr><td colspan="8" style="text-align: center; color: var(--text-muted); padding: 40px;">No active contracts found</td></tr>
            <?php else: ?>
                <?php foreach ($contracts as $c): ?>
                    <tr>
                        <td><a href="<?= BASE_URL ?>contracts/<?= $c['id'] ?>" style="color: var(--accent-gold);"><?= htmlspecialchars($c['contract_no']) ?></a></td>
                        <td><strong><?= htmlspecialchars($c['crew_name']) ?></strong></td>
                        <td><?= htmlspecialchars($c['rank_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($c['vessel_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($c['client_name'] ?? '-') ?></td>
                        <td><?= $c['sign_on_date'] ? date('d M Y', strtotime($c['sign_on_date'])) : '-' ?></td>
                        <td><?= $c['sign_off_date'] ? date('d M Y', strtotime($c['sign_off_date'])) : '-' ?></td>
                        <td><span class="badge badge-success"><?= ucfirst($c['status']) ?></span></td>
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
