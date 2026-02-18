<?php
/**
 * Contracts by Vessel Report View
 */
$currentPage = 'reports';
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1><i class="fas fa-ship" style="color: var(--accent-gold);"></i> Contracts by Vessel</h1>
        <p>Crew assignments and costs per vessel</p>
    </div>
    <a href="<?= BASE_URL ?>reports" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<?php if (empty($vessels)): ?>
    <div class="card" style="text-align: center; padding: 40px; color: var(--text-muted);">
        <i class="fas fa-ship" style="font-size: 40px; margin-bottom: 10px; opacity: 0.5;"></i>
        <p>No vessels found</p>
    </div>
<?php else: ?>
    <?php foreach ($vessels as $vessel): ?>
        <div class="card" style="margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3><i class="fas fa-ship" style="color: var(--info);"></i> <?= htmlspecialchars($vessel['name']) ?></h3>
                <div style="text-align: right;">
                    <span class="badge badge-info"><?= count($vessel['crew_list'] ?? []) ?> Crew</span>
                    <span style="font-size: 18px; color: var(--accent-gold); font-weight: 700; margin-left: 16px;">
                        $<?= number_format($vessel['monthly_cost']['total_usd'] ?? 0, 2) ?>/mo
                    </span>
                </div>
            </div>

            <?php if (empty($vessel['crew_list'])): ?>
                <p style="color: var(--text-muted);">No active crew assigned</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Crew Name</th>
                            <th>Contract No</th>
                            <th>Sign Off</th>
                            <th>Days Left</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vessel['crew_list'] as $crew): ?>
                            <?php
                            $d = $crew['days_remaining'] ?? null;
                            $dClass = $d !== null ? ($d <= 7 ? 'danger' : ($d <= 30 ? 'warning' : 'success')) : 'secondary';
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($crew['rank_name'] ?? '-') ?></td>
                                <td><strong><?= htmlspecialchars($crew['crew_name']) ?></strong></td>
                                <td><a href="<?= BASE_URL ?>contracts/<?= $crew['id'] ?>"
                                        style="color: var(--accent-gold);"><?= htmlspecialchars($crew['contract_no']) ?></a></td>
                                <td><?= $crew['sign_off_date'] ? date('d M Y', strtotime($crew['sign_off_date'])) : '-' ?></td>
                                <td><?= $d !== null ? '<span class="badge badge-' . $dClass . '">' . $d . ' days</span>' : '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>