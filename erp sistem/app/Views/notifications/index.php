<?php
/**
 * Notifications View
 */
$currentPage = 'notifications';
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1><i class="fas fa-bell"></i> Notifications</h1>
        <p>System alerts and notifications</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="<?= BASE_URL ?>notifications/generate" class="btn btn-secondary">
            <i class="fas fa-sync"></i> Generate Alerts
        </a>
        <a href="<?= BASE_URL ?>notifications/mark-all-read" class="btn btn-primary">
            <i class="fas fa-check-double"></i> Mark All Read
        </a>
    </div>
</div>

<div class="card">
    <?php if (empty($notifications)): ?>
        <div style="text-align: center; padding: 60px; color: var(--text-muted);">
            <i class="fas fa-bell-slash" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
            <p>No notifications</p>
        </div>
    <?php else: ?>
        <div class="notification-list">
            <?php foreach ($notifications as $notif): 
                $iconClass = [
                    'info' => 'fa-info-circle',
                    'warning' => 'fa-exclamation-triangle',
                    'danger' => 'fa-exclamation-circle',
                    'success' => 'fa-check-circle',
                ][$notif['type']] ?? 'fa-bell';
                
                $colorClass = [
                    'info' => 'var(--info)',
                    'warning' => 'var(--warning)', 
                    'danger' => 'var(--danger)',
                    'success' => 'var(--success)',
                ][$notif['type']] ?? 'var(--text-muted)';
            ?>
            <div class="notification-item <?= $notif['is_read'] ? 'read' : 'unread' ?>" 
                 style="display: flex; align-items: flex-start; gap: 16px; padding: 16px; border-bottom: 1px solid var(--border-color); <?= !$notif['is_read'] ? 'background: rgba(212, 175, 55, 0.05);' : '' ?>">
                <div class="notification-icon" style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center;">
                    <i class="fas <?= $iconClass ?>" style="color: <?= $colorClass ?>; font-size: 18px;"></i>
                </div>
                <div class="notification-content" style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 4px;">
                            <?= htmlspecialchars($notif['title']) ?>
                            <?php if (!$notif['is_read']): ?>
                                <span class="badge badge-warning" style="font-size: 10px; margin-left: 8px;">NEW</span>
                            <?php endif; ?>
                        </h4>
                        <span style="font-size: 12px; color: var(--text-muted);">
                            <?= date('d M Y H:i', strtotime($notif['created_at'])) ?>
                        </span>
                    </div>
                    <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px;">
                        <?= htmlspecialchars($notif['message']) ?>
                    </p>
                    <div style="display: flex; gap: 12px;">
                        <?php if ($notif['link']): ?>
                            <a href="<?= BASE_URL . $notif['link'] ?>" class="btn btn-sm btn-secondary">
                                <i class="fas fa-eye"></i> View
                            </a>
                        <?php endif; ?>
                        <?php if (!$notif['is_read']): ?>
                            <a href="<?= BASE_URL ?>notifications/mark-read/<?= $notif['id'] ?>" class="btn btn-sm btn-secondary">
                                <i class="fas fa-check"></i> Mark Read
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>
