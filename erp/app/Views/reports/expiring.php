<?php
/**
 * Expiring Contracts Report View
 */
$currentPage = 'reports';
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1><i class="fas fa-clock" style="color: var(--warning);"></i> Expiring Contracts Report</h1>
        <p>Contracts expiring within <?= $days ?? 60 ?> days</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="<?= BASE_URL ?>reports/export/expiring" class="btn btn-secondary">
            <i class="fas fa-download"></i> Export CSV
        </a>
        <a href="<?= BASE_URL ?>reports" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<!-- Filter -->
<div class="card" style="margin-bottom: 24px;">
    <form method="GET" action="<?= BASE_URL ?>reports/expiring" style="display: flex; gap: 16px; align-items: center;">
        <label>Show contracts expiring within:</label>
        <select name="days" class="form-control" style="width: 150px;" onchange="this.form.submit()">
            <option value="7" <?= ($days ?? 60) == 7 ? 'selected' : '' ?>>7 days</option>
            <option value="30" <?= ($days ?? 60) == 30 ? 'selected' : '' ?>>30 days</option>
            <option value="60" <?= ($days ?? 60) == 60 ? 'selected' : '' ?>>60 days</option>
            <option value="90" <?= ($days ?? 60) == 90 ? 'selected' : '' ?>>90 days</option>
        </select>
    </form>
</div>

<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Days Left</th>
                <th>Contract No</th>
                <th>Crew Name</th>
                <th>Rank</th>
                <th>Vessel</th>
                <th>Sign Off Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($contracts)): ?>
                <tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px;">No expiring contracts</td></tr>
            <?php else: ?>
                <?php foreach ($contracts as $c): ?>
                    <?php
                    $d = $c['days_remaining'] ?? 0;
                    $urgencyClass = $d <= 7 ? 'danger' : ($d <= 30 ? 'warning' : 'info');
                    ?>
                    <tr>
                        <td><span class="badge badge-<?= $urgencyClass ?>"><?= $d ?> days</span></td>
                        <td><a href="<?= BASE_URL ?>contracts/<?= $c['id'] ?>" style="color: var(--accent-gold);"><?= htmlspecialchars($c['contract_no']) ?></a></td>
                        <td><strong><?= htmlspecialchars($c['crew_name']) ?></strong></td>
                        <td><?= htmlspecialchars($c['rank_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($c['vessel_name'] ?? '-') ?></td>
                        <td><?= $c['sign_off_date'] ? date('d M Y', strtotime($c['sign_off_date'])) : '-' ?></td>
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
