<!-- Crewing PIC Dashboard Content -->
<div class="page-header">
    <h1>Dashboard</h1>
    <p class="text-muted"><?= date('l, d M Y') ?></p>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h3><?= $teamStats['total_crewing'] ?? 0 ?></h3>
            <p>Total Crewing</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);">
            <i class="fas fa-circle"></i>
        </div>
        <div class="stat-info">
            <h3><?= $teamStats['online_crewing'] ?? 0 ?></h3>
            <p>Online Now</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
            <i class="fas fa-clipboard-check"></i>
        </div>
        <div class="stat-info">
            <h3><?= count($pendingRequests) ?></h3>
            <p>Pending Requests</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
            <i class="fas fa-tasks"></i>
        </div>
        <div class="stat-info">
            <h3><?= $teamStats['active_assignments'] ?? 0 ?></h3>
            <p>Active Assignments</p>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Pending Requests -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-clock me-2 text-warning"></i>Pending Requests</h5>
                <a href="<?= url('/crewing-pic/requests') ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <?php if (empty($pendingRequests)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-check-circle fs-1"></i>
                        <p class="mt-2">No pending requests</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach (array_slice($pendingRequests, 0, 5) as $req): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong><?= htmlspecialchars($req['crewing_name']) ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <?= $req['from_status_name'] ?> → <?= $req['to_status_name'] ?>
                                    </small>
                                </div>
                                <div>
                                    <form action="<?= url('/crewing-pic/requests/approve/' . $req['id']) ?>" method="post" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Online Crewing -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-circle text-success me-2"></i>Online Crewing Staff</h5>
            </div>
            <div class="card-body">
                <?php if (empty($onlineCrewingStaff)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-user-slash fs-1"></i>
                        <p class="mt-2">No crewing online</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach (array_slice($onlineCrewingStaff, 0, 5) as $staff): ?>
                        <div class="list-group-item d-flex align-items-center">
                            <span class="online-indicator me-2"></span>
                            <div>
                                <strong><?= htmlspecialchars($staff['full_name']) ?></strong>
                                <br>
                                <small class="text-muted"><?= $staff['rank'] ?? 'Crewing' ?> • <?= $staff['company'] ?? '-' ?></small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Pipeline Overview -->
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-stream me-2"></i>Recruitment Pipeline</h5>
    </div>
    <div class="card-body">
        <div class="pipeline-status-grid">
            <?php 
            $colors = ['#3b82f6', '#f59e0b', '#8b5cf6', '#06b6d4', '#22c55e', '#10b981', '#ef4444'];
            foreach ($pipelineStats as $i => $status): 
            ?>
            <div class="pipeline-status-card" style="background: <?= $colors[$i % count($colors)] ?>;">
                <div class="status-count"><?= $status['count'] ?></div>
                <div class="status-name"><?= htmlspecialchars($status['name']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.online-indicator {
    width: 10px;
    height: 10px;
    background: #22c55e;
    border-radius: 50%;
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
.pipeline-status-grid {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}
.pipeline-status-card {
    flex: 1;
    min-width: 100px;
    padding: 1rem;
    border-radius: 8px;
    color: white;
    text-align: center;
}
.status-count {
    font-size: 1.5rem;
    font-weight: 700;
}
.status-name {
    font-size: 0.75rem;
    opacity: 0.9;
}
</style>
