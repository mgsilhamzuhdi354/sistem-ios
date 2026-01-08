<div class="page-header">
    <div>
        <h1><i class="fas fa-signal"></i> User Activity Monitor</h1>
        <p class="subtitle">Track online/offline status of all users in real-time</p>
    </div>
    <div class="header-actions">
        <button onclick="location.reload()" class="btn btn-outline">
            <i class="fas fa-sync"></i> Refresh
        </button>
    </div>
</div>

<!-- Stats Overview -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #28a745;">
            <i class="fas fa-circle"></i>
        </div>
        <div class="stat-info">
            <h3><?= $stats['online'] ?></h3>
            <p>Online Now</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #6c757d;">
            <i class="fas fa-circle"></i>
        </div>
        <div class="stat-info">
            <h3><?= $stats['offline'] ?></h3>
            <p>Offline</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #0A2463;">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h3><?= $stats['total'] ?></h3>
            <p>Total Active Users</p>
        </div>
    </div>
</div>

<!-- Users by Role -->
<?php foreach ($usersByRole as $roleName => $users): ?>
<div class="card mb-3">
    <div class="card-header">
        <h3>
            <i class="fas fa-user-tag"></i> 
            <?= htmlspecialchars($roleName) ?>
            <span class="badge"><?= count($users) ?></span>
        </h3>
    </div>
    <div class="card-body">
        <div class="users-grid">
            <?php foreach ($users as $user): ?>
            <div class="user-card <?= $user['is_online'] ? 'online' : 'offline' ?>">
                <div class="user-avatar">
                    <img src="<?= asset('images/avatar-default.svg') ?>" alt="">
                    <span class="status-indicator <?= $user['is_online'] ? 'online' : 'offline' ?>"></span>
                </div>
                <div class="user-info">
                    <h4><?= htmlspecialchars($user['full_name']) ?></h4>
                    <p class="email"><?= htmlspecialchars($user['email']) ?></p>
                    <div class="activity-info">
                        <?php if ($user['is_online']): ?>
                            <span class="status-badge online">
                                <i class="fas fa-circle"></i> Online
                            </span>
                        <?php else: ?>
                            <span class="status-badge offline">
                                <i class="fas fa-circle"></i> Offline
                            </span>
                        <?php endif; ?>
                        <span class="last-seen">
                            <?php if ($user['last_activity']): ?>
                                <?= BaseController::getLastSeenText($user['last_activity']) ?>
                            <?php else: ?>
                                Never
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php if (empty($usersByRole)): ?>
<div class="empty-state">
    <i class="fas fa-users-slash"></i>
    <h3>No Active Users</h3>
    <p>There are no active users in the system.</p>
</div>
<?php endif; ?>

<style>
.page-header .subtitle {
    color: #6c757d;
    font-size: 14px;
    margin: 5px 0 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}

.stat-info h3 {
    font-size: 32px;
    font-weight: 700;
    color: #1a1a2e;
    margin: 0;
}

.stat-info p {
    color: #6c757d;
    margin: 0;
    font-size: 14px;
}

.mb-3 { margin-bottom: 20px; }

.card-header .badge {
    background: #0A2463;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    margin-left: 10px;
}

.users-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 15px;
}

.user-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    display: flex;
    align-items: center;
    gap: 15px;
    border-left: 4px solid #6c757d;
    transition: all 0.3s;
}

.user-card.online {
    border-left-color: #28a745;
    background: #f8fff9;
}

.user-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.user-avatar {
    position: relative;
    flex-shrink: 0;
}

.user-avatar img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.status-indicator {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 2px solid white;
}

.status-indicator.online {
    background: #28a745;
}

.status-indicator.offline {
    background: #6c757d;
}

.user-info {
    flex: 1;
    min-width: 0;
}

.user-info h4 {
    font-size: 15px;
    font-weight: 600;
    color: #1a1a2e;
    margin: 0 0 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-info .email {
    font-size: 12px;
    color: #6c757d;
    margin: 0 0 8px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.activity-info {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 500;
}

.status-badge.online {
    background: #d4edda;
    color: #155724;
}

.status-badge.offline {
    background: #e9ecef;
    color: #495057;
}

.status-badge i {
    font-size: 6px;
}

.last-seen {
    font-size: 11px;
    color: #6c757d;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .users-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Auto-refresh every 60 seconds
setTimeout(() => location.reload(), 60000);
</script>
