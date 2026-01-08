<?php
/**
 * Clients List View
 */
$currentPage = 'clients';
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1>Client / Principal Management</h1>
        <p>Manage ship owners and principals</p>
    </div>
    <a href="<?= BASE_URL ?>clients/create" class="btn btn-primary"><i class="fas fa-plus"></i> Add Client</a>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 24px;">
    <?php foreach ($clients as $client): ?>
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 20px; background: rgba(0,0,0,0.2); display: flex; justify-content: space-between; align-items: center;">
            <div style="width: 56px; height: 56px; border-radius: 12px; background: linear-gradient(135deg, var(--accent-gold), var(--accent-gold-light)); display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 700; color: var(--primary-dark);">
                <?= strtoupper(substr($client['short_name'] ?? $client['name'], 0, 2)) ?>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="<?= BASE_URL ?>clients/<?= $client['id'] ?>" class="btn-icon" title="View Detail"><i class="fas fa-eye"></i></a>
                <a href="<?= BASE_URL ?>clients/edit/<?= $client['id'] ?>" class="btn-icon" title="Edit"><i class="fas fa-edit"></i></a>
            </div>
        </div>
        <div style="padding: 20px;">
            <h3 style="margin-bottom: 8px;"><a href="<?= BASE_URL ?>clients/<?= $client['id'] ?>" style="color: inherit; text-decoration: none;"><?= htmlspecialchars($client['name']) ?></a></h3>
            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($client['country'] ?? 'Unknown') ?></p>
            <div style="display: flex; gap: 16px;">
                <div style="flex: 1; text-align: center; padding: 12px; background: rgba(0,0,0,0.2); border-radius: 8px;">
                    <span style="display: block; font-size: 20px; font-weight: 700; color: var(--accent-gold);"><?= $client['vessel_count'] ?? 0 ?></span>
                    <span style="font-size: 11px; color: var(--text-muted);">Vessels</span>
                </div>
                <div style="flex: 1; text-align: center; padding: 12px; background: rgba(0,0,0,0.2); border-radius: 8px;">
                    <span style="display: block; font-size: 20px; font-weight: 700; color: var(--accent-gold);"><?= $client['active_crew_count'] ?? 0 ?></span>
                    <span style="font-size: 11px; color: var(--text-muted);">Active Crew</span>
                </div>
                <div style="flex: 1; text-align: center; padding: 12px; background: rgba(0,0,0,0.2); border-radius: 8px;">
                    <span style="display: block; font-size: 20px; font-weight: 700; color: var(--accent-gold);">$<?= number_format(($client['monthly_cost'] ?? 0) / 1000, 1) ?>K</span>
                    <span style="font-size: 11px; color: var(--text-muted);">Monthly</span>
                </div>
            </div>
        </div>
        <div style="padding: 16px 20px; background: rgba(0,0,0,0.1); border-top: 1px solid var(--border-color);">
            <div style="display: flex; flex-direction: column; gap: 8px; font-size: 13px; color: var(--text-secondary);">
                <span><i class="fas fa-envelope" style="width: 16px; color: var(--text-muted);"></i> <?= htmlspecialchars($client['email'] ?? '-') ?></span>
                <span><i class="fas fa-phone" style="width: 16px; color: var(--text-muted);"></i> <?= htmlspecialchars($client['phone'] ?? '-') ?></span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>
