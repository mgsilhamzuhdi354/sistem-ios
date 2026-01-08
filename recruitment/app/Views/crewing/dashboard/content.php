<!-- Crewing Dashboard Modern -->
<div class="welcome-banner">
    <div class="welcome-content">
        <div class="welcome-icon">
            <i class="fas fa-anchor"></i>
        </div>
        <div class="welcome-text">
            <h1>Welcome back, <?= $_SESSION['user_name'] ?? 'Crewing' ?>! 👋</h1>
            <p>Here's your work overview for today</p>
        </div>
    </div>
    <div class="welcome-date">
        <i class="fas fa-calendar-alt"></i>
        <span><?= date('l, d F Y') ?></span>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-row-crewing">
    <div class="stat-card-crewing blue">
        <div class="stat-icon-wrap">
            <i class="fas fa-folder-open"></i>
        </div>
        <div class="stat-content">
            <h2><?= $stats['total_assigned'] ?? 0 ?></h2>
            <span>Active Assignments</span>
        </div>
    </div>
    
    <div class="stat-card-crewing orange">
        <div class="stat-icon-wrap">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <h2><?= $stats['pending_review'] ?? 0 ?></h2>
            <span>Pending Review</span>
        </div>
    </div>
    
    <div class="stat-card-crewing purple">
        <div class="stat-icon-wrap">
            <i class="fas fa-comments"></i>
        </div>
        <div class="stat-content">
            <h2><?= $stats['in_interview'] ?? 0 ?></h2>
            <span>In Interview</span>
        </div>
    </div>
    
    <div class="stat-card-crewing teal">
        <div class="stat-icon-wrap">
            <i class="fas fa-file-alt"></i>
        </div>
        <div class="stat-content">
            <h2><?= $stats['documents_pending'] ?? 0 ?></h2>
            <span>Docs Pending</span>
        </div>
    </div>
    
    <div class="stat-card-crewing green">
        <div class="stat-icon-wrap">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <h2><?= $stats['completed_month'] ?? 0 ?></h2>
            <span>Completed</span>
        </div>
    </div>
    
    <div class="stat-card-crewing red">
        <div class="stat-icon-wrap">
            <i class="fas fa-plus-circle"></i>
        </div>
        <div class="stat-content">
            <h2><?= $stats['new_today'] ?? 0 ?></h2>
            <span>New Today</span>
        </div>
    </div>
</div>

<!-- Quick Actions Bar -->
<div class="quick-actions-bar">
    <a href="<?= url('/crewing/applications?status=1') ?>" class="quick-action-item">
        <i class="fas fa-eye"></i>
        <span>Review New</span>
    </a>
    <a href="<?= url('/crewing/pipeline') ?>" class="quick-action-item">
        <i class="fas fa-stream"></i>
        <span>Pipeline</span>
    </a>
    <a href="<?= url('/crewing/team') ?>" class="quick-action-item">
        <i class="fas fa-users"></i>
        <span>Team</span>
    </a>
    <a href="<?= url('/crewing/applications') ?>" class="quick-action-item">
        <i class="fas fa-list"></i>
        <span>All Applications</span>
    </a>
</div>

<div class="dashboard-grid-modern">
    <!-- Pipeline Summary -->
    <div class="dashboard-card-modern">
        <div class="card-header-modern">
            <div class="header-left">
                <div class="header-icon blue"><i class="fas fa-stream"></i></div>
                <h3>My Pipeline</h3>
            </div>
            <a href="<?= url('/crewing/pipeline') ?>" class="view-all-link">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="card-body-modern">
            <div class="pipeline-visual">
                <?php foreach ($pipeline as $status): ?>
                <div class="pipeline-status-item">
                    <div class="status-color-bar" style="background: <?= $status['color'] ?>"></div>
                    <div class="status-details">
                        <span class="status-name"><?= $status['name'] ?></span>
                        <span class="status-count"><?= $status['count'] ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Recent Assignments -->
    <div class="dashboard-card-modern">
        <div class="card-header-modern">
            <div class="header-left">
                <div class="header-icon purple"><i class="fas fa-file-alt"></i></div>
                <h3>Recent Assignments</h3>
            </div>
            <a href="<?= url('/crewing/applications') ?>" class="view-all-link">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="card-body-modern">
            <?php if (empty($recentApplications)): ?>
            <div class="empty-state-modern">
                <i class="fas fa-inbox"></i>
                <p>No applications assigned yet</p>
            </div>
            <?php else: ?>
            <div class="assignments-list">
                <?php foreach ($recentApplications as $app): ?>
                <a href="<?= url('/crewing/applications/' . $app['id']) ?>" class="assignment-item">
                    <div class="assignment-avatar">
                        <?= strtoupper(substr($app['full_name'], 0, 2)) ?>
                    </div>
                    <div class="assignment-info">
                        <strong><?= htmlspecialchars($app['full_name']) ?></strong>
                        <span><?= htmlspecialchars($app['vacancy_title']) ?></span>
                        <small><i class="fas fa-calendar"></i> <?= date('M d', strtotime($app['assigned_at'])) ?></small>
                    </div>
                    <span class="assignment-status" style="background: <?= $app['status_color'] ?>">
                        <?= $app['status_name'] ?>
                    </span>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Pending Interviews -->
    <div class="dashboard-card-modern">
        <div class="card-header-modern">
            <div class="header-left">
                <div class="header-icon orange"><i class="fas fa-robot"></i></div>
                <h3>Pending Interviews</h3>
            </div>
        </div>
        <div class="card-body-modern">
            <?php if (empty($pendingInterviews)): ?>
            <div class="empty-state-modern success">
                <i class="fas fa-check-circle"></i>
                <p>No pending interviews</p>
            </div>
            <?php else: ?>
            <div class="interviews-list">
                <?php foreach ($pendingInterviews as $interview): ?>
                <div class="interview-item-modern <?= $interview['days_left'] <= 2 ? 'urgent' : '' ?>">
                    <div class="interview-main">
                        <strong><?= htmlspecialchars($interview['full_name']) ?></strong>
                        <span><?= htmlspecialchars($interview['vacancy_title']) ?></span>
                    </div>
                    <div class="interview-badges">
                        <span class="days-left <?= $interview['days_left'] <= 2 ? 'danger' : 'warning' ?>">
                            <i class="fas fa-hourglass-half"></i> <?= $interview['days_left'] ?> days
                        </span>
                        <span class="interview-status"><?= ucfirst($interview['status']) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (!empty($teamWorkload)): ?>
    <!-- Team Workload -->
    <div class="dashboard-card-modern team">
        <div class="card-header-modern">
            <div class="header-left">
                <div class="header-icon green"><i class="fas fa-users-cog"></i></div>
                <h3>Team Workload</h3>
            </div>
            <a href="<?= url('/crewing/team') ?>" class="view-all-link">Manage <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="card-body-modern">
            <div class="team-workload-list">
                <?php foreach ($teamWorkload as $crew): ?>
                <div class="team-member-row">
                    <div class="member-avatar-mini">
                        <?= strtoupper(substr($crew['full_name'], 0, 2)) ?>
                    </div>
                    <div class="member-details">
                        <strong><?= htmlspecialchars($crew['full_name']) ?></strong>
                        <?php 
                        $percentage = $crew['max_applications'] > 0 
                            ? min(100, ($crew['active_assignments'] / $crew['max_applications']) * 100) 
                            : 0;
                        $barClass = $percentage >= 80 ? 'danger' : ($percentage >= 50 ? 'warning' : 'success');
                        ?>
                        <div class="mini-progress">
                            <div class="mini-progress-fill <?= $barClass ?>" style="width: <?= $percentage ?>%"></div>
                        </div>
                    </div>
                    <span class="workload-count"><?= $crew['active_assignments'] ?>/<?= $crew['max_applications'] ?: 50 ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
/* Welcome Banner */
.welcome-banner {
    background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 50%, #3b82f6 100%);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
}
.welcome-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}
.welcome-icon {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.2);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
.welcome-text h1 {
    margin: 0;
    font-size: 1.5rem;
}
.welcome-text p {
    margin: 0.25rem 0 0;
    opacity: 0.8;
}
.welcome-date {
    background: rgba(255,255,255,0.15);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

/* Stats Row */
.stats-row-crewing {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.stat-card-crewing {
    background: white;
    border-radius: 16px;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    position: relative;
    overflow: hidden;
}
.stat-card-crewing::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
}
.stat-card-crewing.blue::before { background: linear-gradient(180deg, #3b82f6, #1d4ed8); }
.stat-card-crewing.orange::before { background: linear-gradient(180deg, #f59e0b, #d97706); }
.stat-card-crewing.purple::before { background: linear-gradient(180deg, #8b5cf6, #7c3aed); }
.stat-card-crewing.teal::before { background: linear-gradient(180deg, #14b8a6, #0d9488); }
.stat-card-crewing.green::before { background: linear-gradient(180deg, #22c55e, #16a34a); }
.stat-card-crewing.red::before { background: linear-gradient(180deg, #ef4444, #dc2626); }

.stat-icon-wrap {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    color: white;
}
.stat-card-crewing.blue .stat-icon-wrap { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.stat-card-crewing.orange .stat-icon-wrap { background: linear-gradient(135deg, #f59e0b, #d97706); }
.stat-card-crewing.purple .stat-icon-wrap { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.stat-card-crewing.teal .stat-icon-wrap { background: linear-gradient(135deg, #14b8a6, #0d9488); }
.stat-card-crewing.green .stat-icon-wrap { background: linear-gradient(135deg, #22c55e, #16a34a); }
.stat-card-crewing.red .stat-icon-wrap { background: linear-gradient(135deg, #ef4444, #dc2626); }

.stat-content h2 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
}
.stat-content span {
    font-size: 0.7rem;
    color: #6b7280;
    text-transform: uppercase;
}

/* Quick Actions Bar */
.quick-actions-bar {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}
.quick-action-item {
    flex: 1;
    background: white;
    border-radius: 12px;
    padding: 1rem;
    text-align: center;
    text-decoration: none;
    color: #374151;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: all 0.3s;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}
.quick-action-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    color: #3b82f6;
}
.quick-action-item i {
    font-size: 1.25rem;
    color: #3b82f6;
}
.quick-action-item span {
    font-size: 0.8rem;
    font-weight: 500;
}

/* Dashboard Grid */
.dashboard-grid-modern {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}
.dashboard-card-modern {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    overflow: hidden;
}
.card-header-modern {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.header-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.header-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}
.header-icon.blue { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.header-icon.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.header-icon.orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
.header-icon.green { background: linear-gradient(135deg, #22c55e, #16a34a); }
.card-header-modern h3 {
    margin: 0;
    font-size: 1rem;
    color: #1f2937;
}
.view-all-link {
    font-size: 0.8rem;
    color: #3b82f6;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}
.card-body-modern {
    padding: 1.5rem;
}

/* Pipeline Visual */
.pipeline-visual {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
.pipeline-status-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: #f8fafc;
    border-radius: 10px;
}
.status-color-bar {
    width: 4px;
    height: 30px;
    border-radius: 2px;
}
.status-details {
    flex: 1;
    display: flex;
    justify-content: space-between;
}
.status-name {
    font-size: 0.9rem;
    color: #374151;
}
.status-count {
    font-weight: 700;
    color: #1f2937;
}

/* Assignments List */
.assignments-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
.assignment-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    border-radius: 10px;
    text-decoration: none;
    transition: background 0.2s;
}
.assignment-item:hover {
    background: #f8fafc;
}
.assignment-avatar {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.8rem;
    font-weight: 600;
}
.assignment-info {
    flex: 1;
}
.assignment-info strong {
    display: block;
    color: #1f2937;
    font-size: 0.9rem;
}
.assignment-info span {
    display: block;
    color: #6b7280;
    font-size: 0.8rem;
}
.assignment-info small {
    color: #9ca3af;
    font-size: 0.75rem;
}
.assignment-status {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    color: white;
    font-size: 0.7rem;
    font-weight: 500;
}

/* Interviews List */
.interviews-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
.interview-item-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    border-radius: 10px;
    background: #f8fafc;
}
.interview-item-modern.urgent {
    background: #fef2f2;
    border-left: 3px solid #ef4444;
}
.interview-main strong {
    display: block;
    font-size: 0.9rem;
    color: #1f2937;
}
.interview-main span {
    font-size: 0.8rem;
    color: #6b7280;
}
.interview-badges {
    display: flex;
    gap: 0.5rem;
}
.days-left {
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 500;
}
.days-left.warning { background: #fef3c7; color: #d97706; }
.days-left.danger { background: #fee2e2; color: #dc2626; }
.interview-status {
    padding: 0.25rem 0.5rem;
    background: #e5e7eb;
    color: #374151;
    border-radius: 6px;
    font-size: 0.7rem;
}

/* Team Workload */
.team-workload-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
.team-member-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem;
}
.member-avatar-mini {
    width: 35px;
    height: 35px;
    background: linear-gradient(135deg, #22c55e, #16a34a);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.7rem;
    font-weight: 600;
}
.member-details {
    flex: 1;
}
.member-details strong {
    display: block;
    font-size: 0.85rem;
    color: #1f2937;
    margin-bottom: 0.25rem;
}
.mini-progress {
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    overflow: hidden;
}
.mini-progress-fill {
    height: 100%;
    border-radius: 3px;
}
.mini-progress-fill.success { background: linear-gradient(90deg, #22c55e, #16a34a); }
.mini-progress-fill.warning { background: linear-gradient(90deg, #f59e0b, #d97706); }
.mini-progress-fill.danger { background: linear-gradient(90deg, #ef4444, #dc2626); }
.workload-count {
    font-size: 0.8rem;
    color: #6b7280;
    font-weight: 500;
}

/* Empty State */
.empty-state-modern {
    text-align: center;
    padding: 2rem;
    color: #9ca3af;
}
.empty-state-modern i {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    opacity: 0.5;
}
.empty-state-modern.success i {
    color: #22c55e;
    opacity: 1;
}

@media (max-width: 1200px) {
    .stats-row-crewing {
        grid-template-columns: repeat(3, 1fr);
    }
}
@media (max-width: 768px) {
    .stats-row-crewing {
        grid-template-columns: repeat(2, 1fr);
    }
    .dashboard-grid-modern {
        grid-template-columns: 1fr;
    }
    .quick-actions-bar {
        flex-wrap: wrap;
    }
    .quick-action-item {
        flex: 0 0 calc(50% - 0.375rem);
    }
}
</style>
