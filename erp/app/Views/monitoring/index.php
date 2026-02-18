<?php
/**
 * Monitoring Dashboard View  
 */
?>
<div class="page-header">
    <h1>📊 Monitoring Dashboard</h1>
    <p>Central monitoring untuk semua sistem</p>
</div>

<div class="grid-4">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-eye"></i>
        </div>
        <div class="stat-info">
            <h3>
                <?= $stats['visitors_today'] ?? 0 ?>
            </h3>
            <p>Visitors Hari Ini</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon gold">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h3>
                <?= $stats['visitors_month'] ?? 0 ?>
            </h3>
            <p>Visitors Bulan Ini</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="stat-info">
            <h3>
                <?= $stats['recruitment_active'] ?? 0 ?>
            </h3>
            <p>Aplikasi Aktif</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon red">
            <i class="fas fa-bell"></i>
        </div>
        <div class="stat-info">
            <h3>
                <?= $stats['pending_approvals'] ?? 0 ?>
            </h3>
            <p>Pending Approval</p>
        </div>
    </div>
</div>

<div class="grid-2" style="margin-top: 20px;">
    <div class="card">
        <h3 style="margin-bottom: 12px;">Integration Status</h3>
        <div style="display: flex; gap: 16px; margin-top: 16px;">
            <div style="flex: 1;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                    <div
                        style="width: 12px; height: 12px; border-radius: 50%; background: <?= $stats['integration_status']['active'] == $stats['integration_status']['total'] ? 'var(--success)' : 'var(--warning)' ?>">
                    </div>
                    <span>
                        <?= $stats['integration_status']['active'] ?? 0 ?> /
                        <?= $stats['integration_status']['total'] ?? 0 ?> Connected
                    </span>
                </div>
                <a href="<?= BASE_URL ?>monitoring/integration" class="btn btn-sm btn-primary"
                    style="margin-top: 12px;">
                    <i class="fas fa-plug"></i> View Details
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <h3 style="margin-bottom: 12px;">Quick Links</h3>
        <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 16px;">
            <a href="<?= BASE_URL ?>monitoring/visitors" class="btn btn-secondary">
                <i class="fas fa-eye"></i> Visitor Tracking
            </a>
            <a href="<?= BASE_URL ?>monitoring/activity" class="btn btn-secondary">
                <i class="fas fa-list-alt"></i> Activity Logs
            </a>
        </div>
    </div>
</div>