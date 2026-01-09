<?php
/**
 * Expiring Contracts View (Feature 10)
 * Shows contracts expiring within selected days with urgency badges
 */
$currentPage = 'contracts';
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1><i class="fas fa-clock" style="color: var(--warning);"></i> Expiring Contracts</h1>
        <p>Contracts that will expire soon - take action before it's too late</p>
    </div>
    <a href="<?= BASE_URL ?>contracts" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Contracts
    </a>
</div>

<!-- Filter by Days -->
<div class="card" style="margin-bottom: 24px;">
    <form method="GET" action="<?= BASE_URL ?>contracts/expiring" style="display: flex; gap: 16px; align-items: center;">
        <label class="form-label" style="margin: 0;">Show contracts expiring within:</label>
        <select name="days" class="form-control" style="width: 150px;" onchange="this.form.submit()">
            <option value="7" <?= ($days ?? 60) == 7 ? 'selected' : '' ?>>7 days (Critical)</option>
            <option value="30" <?= ($days ?? 60) == 30 ? 'selected' : '' ?>>30 days</option>
            <option value="60" <?= ($days ?? 60) == 60 ? 'selected' : '' ?>>60 days</option>
            <option value="90" <?= ($days ?? 60) == 90 ? 'selected' : '' ?>>90 days</option>
        </select>
    </form>
</div>

<!-- Stats Cards -->
<div class="grid-3" style="margin-bottom: 24px;">
    <?php
    $critical = array_filter($contracts ?? [], fn($c) => ($c['days_remaining'] ?? 0) <= 7);
    $warning = array_filter($contracts ?? [], fn($c) => ($c['days_remaining'] ?? 0) > 7 && ($c['days_remaining'] ?? 0) <= 30);
    $info = array_filter($contracts ?? [], fn($c) => ($c['days_remaining'] ?? 0) > 30);
    ?>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-exclamation-circle"></i></div>
        <div class="stat-info">
            <h3><?= count($critical) ?></h3>
            <p>Critical (≤7 days)</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="stat-info">
            <h3><?= count($warning) ?></h3>
            <p>Warning (8-30 days)</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-info-circle"></i></div>
        <div class="stat-info">
            <h3><?= count($info) ?></h3>
            <p>Upcoming (31+ days)</p>
        </div>
    </div>
</div>

<!-- Contracts Table -->
<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Urgency</th>
                <th>Contract No</th>
                <th>Crew Name</th>
                <th>Rank</th>
                <th>Vessel</th>
                <th>Sign Off Date</th>
                <th>Days Left</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($contracts)): ?>
                <tr><td colspan="8" style="text-align: center; color: var(--text-muted); padding: 40px;">
                    <i class="fas fa-check-circle" style="font-size: 40px; margin-bottom: 10px; display: block; color: var(--success);"></i>
                    No contracts expiring within <?= $days ?? 60 ?> days - Great!
                </td></tr>
            <?php else: ?>
                <?php foreach ($contracts as $contract): ?>
                    <?php
                    $d = $contract['days_remaining'] ?? 0;
                    $urgencyClass = $d <= 7 ? 'danger' : ($d <= 30 ? 'warning' : 'info');
                    $urgencyText = $d <= 7 ? 'CRITICAL' : ($d <= 30 ? 'WARNING' : 'UPCOMING');
                    ?>
                    <tr>
                        <td><span class="badge badge-<?= $urgencyClass ?>"><?= $urgencyText ?></span></td>
                        <td><a href="<?= BASE_URL ?>contracts/<?= $contract['id'] ?>" style="color: var(--accent-gold); font-weight: 500;"><?= htmlspecialchars($contract['contract_no']) ?></a></td>
                        <td><strong><?= htmlspecialchars($contract['crew_name']) ?></strong></td>
                        <td><?= htmlspecialchars($contract['rank_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($contract['vessel_name'] ?? '-') ?></td>
                        <td><?= $contract['sign_off_date'] ? date('d M Y', strtotime($contract['sign_off_date'])) : '-' ?></td>
                        <td>
                            <span class="badge badge-<?= $urgencyClass ?>" style="font-size: 14px; padding: 6px 12px;">
                                <?= $d ?> days
                            </span>
                        </td>
                        <td style="white-space: nowrap;">
                            <a href="<?= BASE_URL ?>contracts/renew/<?= $contract['id'] ?>" class="btn btn-success btn-sm" title="Renew">
                                <i class="fas fa-redo"></i> Renew
                            </a>
                            <a href="<?= BASE_URL ?>contracts/<?= $contract['id'] ?>" class="btn-icon" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
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
