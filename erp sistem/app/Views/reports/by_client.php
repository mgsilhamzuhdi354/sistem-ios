<?php
/**
 * Contracts by Client Report View
 */
$currentPage = 'reports';
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1><i class="fas fa-building" style="color: var(--accent-gold);"></i> Contracts by Client</h1>
        <p>Crew contracts grouped by client/principal</p>
    </div>
    <a href="<?= BASE_URL ?>reports" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<?php if (empty($clients)): ?>
    <div class="card" style="text-align: center; padding: 40px; color: var(--text-muted);">
        <i class="fas fa-building" style="font-size: 40px; margin-bottom: 10px; opacity: 0.5;"></i>
        <p>No clients found</p>
    </div>
<?php else: ?>
    <?php foreach ($clients as $client): ?>
        <div class="card" style="margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <div>
                    <h3><i class="fas fa-building" style="color: var(--info);"></i> <?= htmlspecialchars($client['name']) ?></h3>
                    <small style="color: var(--text-muted);"><?= htmlspecialchars($client['country'] ?? '') ?></small>
                </div>
                <div>
                    <span class="badge badge-info"><?= $client['active_contracts'] ?? 0 ?> Active</span>
                    <span class="badge badge-secondary"><?= $client['vessel_count'] ?? 0 ?> Vessels</span>
                </div>
            </div>
            
            <?php if (empty($client['contracts'])): ?>
                <p style="color: var(--text-muted);">No active contracts</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Vessel</th>
                            <th>Crew Name</th>
                            <th>Rank</th>
                            <th>Contract No</th>
                            <th>Sign Off</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($client['contracts'] as $c): ?>
                            <tr>
                                <td><?= htmlspecialchars($c['vessel_name'] ?? '-') ?></td>
                                <td><strong><?= htmlspecialchars($c['crew_name']) ?></strong></td>
                                <td><?= htmlspecialchars($c['rank_name'] ?? '-') ?></td>
                                <td><a href="<?= BASE_URL ?>contracts/<?= $c['id'] ?>" style="color: var(--accent-gold);"><?= htmlspecialchars($c['contract_no']) ?></a></td>
                                <td><?= $c['sign_off_date'] ? date('d M Y', strtotime($c['sign_off_date'])) : '-' ?></td>
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
