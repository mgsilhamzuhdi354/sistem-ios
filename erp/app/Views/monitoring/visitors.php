<?php
/**
 * Monitoring Visitors View
 */
?>
<div class="page-header">
    <h1>Company Profile Visitors</h1>
    <p>Track pengunjung website company profile</p>
</div>

<div class="card" style="margin-bottom: 20px;">
    <div style="display: flex; gap: 12px; align-items: center;">
        <div style="flex: 1;">
            <strong>Period:</strong>
            <div style="display: inline-flex; gap: 8px; margin-left: 12px;">
                <a href="?period=today" 
                   class="btn btn-sm <?= $period === 'today' ? 'btn-primary' : 'btn-secondary' ?>">
                    Today
                </a>
                <a href="?period=week" 
                   class="btn btn-sm <?= $period === 'week' ? 'btn-primary' : 'btn-secondary' ?>">
                    This Week
                </a>
                <a href="?period=month" 
                   class="btn btn-sm <?= $period === 'month' ? 'btn-primary' : 'btn-secondary' ?>">
                    This Month
                </a>
            </div>
        </div>
    </div>
</div>

<div class="grid-3" style="margin-bottom: 20px;">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h3><?= count($visitors) ?></h3>
            <p>Total Visitors</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon gold">
            <i class="fas fa-globe"></i>
        </div>
        <div class="stat-info">
            <h3><?= count($stats['top_countries'] ?? []) ?></h3>
            <p>Countries</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-file-alt"></i>
        </div>
        <div class="stat-info">
            <h3><?= count($stats['top_pages'] ?? []) ?></h3>
            <p>Unique Pages</p>
        </div>
    </div>
</div>

<div class="grid-2" style="margin-bottom: 20px;">
    <div class="card">
        <h3 style="margin-bottom: 12px;">Top Pages</h3>
        <?php if (empty($stats['top_pages'])): ?>
            <p style="color: var(--text-muted);">No data available</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Page</th>
                        <th>Visits</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats['top_pages'] as $page): ?>
                    <tr>
                        <td><?= htmlspecialchars($page['page_url']) ?></td>
                        <td><strong><?= $page['count'] ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3 style="margin-bottom: 12px;">Top Countries</h3>
        <?php if (empty($stats['top_countries'])): ?>
            <p style="color: var(--text-muted);">No data available</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Country</th>
                        <th>Visits</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats['top_countries'] as $country): ?>
                    <tr>
                        <td><?= htmlspecialchars($country['country']) ?></td>
                        <td><strong><?= $country['count'] ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <h3 style="margin-bottom: 16px;">Recent Visitors</h3>
    <?php if (empty($visitors)): ?>
        <div class="alert alert-warning">
            <i class="fas fa-info-circle"></i>
            Belum ada visitor data. 
            <a href="<?= BASE_URL ?>monitoring/integration" style="color: var(--accent-gold);">
                Check Integration Status →
            </a>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>IP Address</th>
                    <th>Country</th>
                    <th>Page</th>
                    <th>Browser</th>
                    <th>Device</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($visitors, 0, 50) as $visitor): ?>
                <tr>
                    <td><?= date('d/m/Y H:i', strtotime($visitor['visited_at'])) ?></td>
                    <td><code><?= htmlspecialchars($visitor['ip_address']) ?></code></td>
                    <td><?= htmlspecialchars($visitor['country'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($visitor['page_url']) ?></td>
                    <td><?= htmlspecialchars($visitor['browser'] ?? '-') ?></td>
                    <td>
                        <span class="badge badge-secondary">
                            <?= htmlspecialchars($visitor['device_type'] ?? 'Unknown') ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
