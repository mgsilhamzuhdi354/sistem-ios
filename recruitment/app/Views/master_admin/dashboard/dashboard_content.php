<!-- Master Admin Dashboard Content -->
<div class="welcome-section mb-4">
    <div class="welcome-card">
        <div class="welcome-content">
            <h1>Welcome back, <?= $_SESSION['user_name'] ?? 'Master Admin' ?>! 👋</h1>
            <p>Here's your system overview for today.</p>
            <div class="welcome-date">
                <i class="fas fa-calendar-alt"></i>
                <span><?= date('l, d F Y') ?></span>
            </div>
        </div>
        <div class="welcome-illustration">
            <i class="fas fa-chart-line"></i>
        </div>
    </div>
</div>

<!-- Quick Stats Row -->
<div class="stats-row mb-4">
    <div class="quick-stat blue">
        <div class="stat-icon-wrapper">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-details">
            <h2><?= ($stats['total_master_admins'] ?? 0) + ($stats['total_admins'] ?? 0) + ($stats['total_leaders'] ?? 0) + ($stats['total_crewing'] ?? 0) + ($stats['total_applicants'] ?? 0) ?></h2>
            <span>Total Users</span>
        </div>
        <div class="stat-trend up">
            <i class="fas fa-arrow-up"></i> 12%
        </div>
    </div>
    
    <div class="quick-stat green">
        <div class="stat-icon-wrapper">
            <i class="fas fa-briefcase"></i>
        </div>
        <div class="stat-details">
            <h2><?= $stats['active_vacancies'] ?? 0 ?></h2>
            <span>Active Vacancies</span>
        </div>
        <a href="<?= url('/master-admin/vacancies') ?>" class="stat-link">
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    
    <div class="quick-stat purple">
        <div class="stat-icon-wrapper">
            <i class="fas fa-file-alt"></i>
        </div>
        <div class="stat-details">
            <h2><?= $stats['total_applications'] ?? 0 ?></h2>
            <span>Applications</span>
        </div>
        <a href="<?= url('/master-admin/pipeline') ?>" class="stat-link">
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    
    <div class="quick-stat orange">
        <div class="stat-icon-wrapper">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="stat-details">
            <h2><?= $stats['total_hired'] ?? 0 ?></h2>
            <span>Total Hired</span>
        </div>
        <div class="stat-badge">
            <i class="fas fa-star"></i>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions mb-4">
    <a href="<?= url('/master-admin/users/create') ?>" class="action-card">
        <i class="fas fa-user-plus"></i>
        <span>Add User</span>
    </a>
    <a href="<?= url('/admin/vacancies/create') ?>" class="action-card">
        <i class="fas fa-plus-circle"></i>
        <span>New Vacancy</span>
    </a>
    <a href="<?= url('/master-admin/pipeline') ?>" class="action-card">
        <i class="fas fa-stream"></i>
        <span>Pipeline</span>
    </a>
    <a href="<?= url('/master-admin/reports') ?>" class="action-card">
        <i class="fas fa-chart-bar"></i>
        <span>Reports</span>
    </a>
    <a href="<?= url('/master-admin/settings') ?>" class="action-card">
        <i class="fas fa-cog"></i>
        <span>Settings</span>
    </a>
</div>

<div class="row g-4">
    <!-- Users by Role -->
    <div class="col-lg-6">
        <div class="dashboard-card">
            <div class="card-header-styled">
                <h5><i class="fas fa-users-cog me-2"></i>Users by Role</h5>
            </div>
            <div class="card-body-styled">
                <div class="role-grid">
                    <div class="role-item red">
                        <span class="role-count"><?= $stats['total_master_admins'] ?? 0 ?></span>
                        <span class="role-name">Master Admin</span>
                        <i class="fas fa-crown"></i>
                    </div>
                    <div class="role-item blue">
                        <span class="role-count"><?= $stats['total_admins'] ?? 0 ?></span>
                        <span class="role-name">Admin</span>
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="role-item orange">
                        <span class="role-count"><?= $stats['total_leaders'] ?? 0 ?></span>
                        <span class="role-name">Leader</span>
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="role-item yellow">
                        <span class="role-count"><?= $stats['total_crewing_pic'] ?? 0 ?></span>
                        <span class="role-name">Crewing PIC</span>
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="role-item green">
                        <span class="role-count"><?= $stats['total_crewing'] ?? 0 ?></span>
                        <span class="role-name">Crewing</span>
                        <i class="fas fa-anchor"></i>
                    </div>
                    <div class="role-item gray">
                        <span class="role-count"><?= $stats['total_applicants'] ?? 0 ?></span>
                        <span class="role-name">Applicants</span>
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Team Overview -->
    <div class="col-lg-6">
        <div class="dashboard-card">
            <div class="card-header-styled">
                <h5><i class="fas fa-shield-alt me-2"></i>Team Members</h5>
                <a href="<?= url('/master-admin/users') ?>" class="btn-header">View All</a>
            </div>
            <div class="card-body-styled">
                <div class="team-list">
                    <?php foreach (array_slice($leaders, 0, 4) as $leader): ?>
                    <div class="team-member">
                        <div class="member-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="member-info">
                            <strong><?= htmlspecialchars($leader['full_name']) ?></strong>
                            <small><?= $leader['email'] ?></small>
                        </div>
                        <span class="member-badge leader">Leader</span>
                    </div>
                    <?php endforeach; ?>
                    <?php foreach (array_slice($admins, 0, 2) as $admin): ?>
                    <div class="team-member">
                        <div class="member-avatar admin">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="member-info">
                            <strong><?= htmlspecialchars($admin['full_name']) ?></strong>
                            <small><?= $admin['email'] ?></small>
                        </div>
                        <span class="member-badge admin">Admin</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Crewing Performance -->
<div class="dashboard-card mt-4">
    <div class="card-header-styled">
        <h5><i class="fas fa-trophy me-2"></i>Top Crewing Performance</h5>
    </div>
    <div class="card-body-styled">
        <?php if (empty($crewingPerformance)): ?>
        <div class="empty-state-mini">
            <i class="fas fa-chart-line"></i>
            <p>No performance data yet</p>
        </div>
        <?php else: ?>
        <div class="performance-table">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Crewing Staff</th>
                        <th>Completed</th>
                        <th>Hired</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $maxCompleted = max(array_column($crewingPerformance, 'completed')) ?: 1;
                    foreach ($crewingPerformance as $i => $perf): 
                    $percentage = ($perf['completed'] / $maxCompleted) * 100;
                    ?>
                    <tr>
                        <td>
                            <span class="rank-medal rank-<?= $i + 1 ?>">
                                <?php if ($i == 0): ?>🥇
                                <?php elseif ($i == 1): ?>🥈
                                <?php elseif ($i == 2): ?>🥉
                                <?php else: echo $i + 1; endif; ?>
                            </span>
                        </td>
                        <td><strong><?= htmlspecialchars($perf['full_name']) ?></strong></td>
                        <td><span class="count-badge green"><?= $perf['completed'] ?></span></td>
                        <td><span class="count-badge blue"><?= $perf['hired'] ?></span></td>
                        <td>
                            <div class="perf-bar">
                                <div class="perf-fill" style="width: <?= $percentage ?>%"></div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Welcome Section */
.welcome-card {
    background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 50%, #3b82f6 100%);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    overflow: hidden;
    position: relative;
}
.welcome-content h1 {
    font-size: 1.75rem;
    margin-bottom: 0.5rem;
}
.welcome-content p {
    opacity: 0.8;
    margin-bottom: 1rem;
}
.welcome-date {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255,255,255,0.15);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
}
.welcome-illustration {
    font-size: 6rem;
    opacity: 0.2;
}

/* Quick Stats */
.stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}
.quick-stat {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    position: relative;
    overflow: hidden;
}
.quick-stat::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
}
.quick-stat.blue::before { background: linear-gradient(180deg, #3b82f6, #1d4ed8); }
.quick-stat.green::before { background: linear-gradient(180deg, #22c55e, #16a34a); }
.quick-stat.purple::before { background: linear-gradient(180deg, #8b5cf6, #7c3aed); }
.quick-stat.orange::before { background: linear-gradient(180deg, #f59e0b, #d97706); }

.stat-icon-wrapper {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
}
.quick-stat.blue .stat-icon-wrapper { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.quick-stat.green .stat-icon-wrapper { background: linear-gradient(135deg, #22c55e, #16a34a); }
.quick-stat.purple .stat-icon-wrapper { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.quick-stat.orange .stat-icon-wrapper { background: linear-gradient(135deg, #f59e0b, #d97706); }

.stat-details h2 {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0;
    color: #1f2937;
}
.stat-details span {
    font-size: 0.8rem;
    color: #6b7280;
}
.stat-link {
    margin-left: auto;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f3f4f6;
    border-radius: 8px;
    color: #6b7280;
    transition: all 0.3s;
}
.stat-link:hover {
    background: #1e3a5f;
    color: white;
}
.stat-trend {
    margin-left: auto;
    font-size: 0.8rem;
    font-weight: 600;
    color: #22c55e;
}
.stat-badge {
    margin-left: auto;
    color: #f59e0b;
}

/* Quick Actions */
.quick-actions {
    display: flex;
    gap: 0.75rem;
}
.action-card {
    flex: 1;
    background: white;
    border-radius: 12px;
    padding: 1rem;
    text-align: center;
    text-decoration: none;
    color: #374151;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: all 0.3s;
}
.action-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    color: #1e3a5f;
}
.action-card i {
    font-size: 1.5rem;
    color: #3b82f6;
    display: block;
    margin-bottom: 0.5rem;
}
.action-card span {
    font-size: 0.8rem;
    font-weight: 500;
}

/* Dashboard Card */
.dashboard-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    overflow: hidden;
}
.card-header-styled {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.card-header-styled h5 {
    margin: 0;
    color: #1f2937;
    font-size: 1rem;
}
.btn-header {
    font-size: 0.8rem;
    color: #3b82f6;
    text-decoration: none;
}
.card-body-styled {
    padding: 1.5rem;
}

/* Role Grid */
.role-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}
.role-item {
    padding: 1rem;
    border-radius: 12px;
    text-align: center;
    position: relative;
}
.role-item.red { background: #fef2f2; }
.role-item.blue { background: #eff6ff; }
.role-item.orange { background: #fff7ed; }
.role-item.yellow { background: #fefce8; }
.role-item.green { background: #f0fdf4; }
.role-item.gray { background: #f9fafb; }
.role-count {
    display: block;
    font-size: 1.75rem;
    font-weight: 700;
}
.role-item.red .role-count { color: #dc2626; }
.role-item.blue .role-count { color: #2563eb; }
.role-item.orange .role-count { color: #ea580c; }
.role-item.yellow .role-count { color: #ca8a04; }
.role-item.green .role-count { color: #16a34a; }
.role-item.gray .role-count { color: #4b5563; }
.role-name {
    font-size: 0.75rem;
    color: #6b7280;
}
.role-item i {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    opacity: 0.3;
}

/* Team List */
.team-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
.team-member {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    border-radius: 10px;
    transition: background 0.2s;
}
.team-member:hover {
    background: #f9fafb;
}
.member-avatar {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}
.member-avatar.admin {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
}
.member-info {
    flex: 1;
}
.member-info strong {
    display: block;
    font-size: 0.9rem;
}
.member-info small {
    color: #9ca3af;
}
.member-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
}
.member-badge.leader {
    background: #fff7ed;
    color: #ea580c;
}
.member-badge.admin {
    background: #eff6ff;
    color: #2563eb;
}

/* Performance Table */
.performance-table table {
    width: 100%;
    border-collapse: collapse;
}
.performance-table th {
    text-align: left;
    padding: 0.75rem;
    font-size: 0.75rem;
    color: #9ca3af;
    text-transform: uppercase;
    border-bottom: 1px solid #f3f4f6;
}
.performance-table td {
    padding: 0.75rem;
    border-bottom: 1px solid #f9fafb;
}
.count-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
}
.count-badge.green { background: #dcfce7; color: #166534; }
.count-badge.blue { background: #dbeafe; color: #1e40af; }
.perf-bar {
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
}
.perf-fill {
    height: 100%;
    background: linear-gradient(90deg, #22c55e, #16a34a);
    border-radius: 4px;
}

.empty-state-mini {
    text-align: center;
    padding: 2rem;
    color: #9ca3af;
}
.empty-state-mini i {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}

@media (max-width: 992px) {
    .stats-row { grid-template-columns: repeat(2, 1fr); }
    .quick-actions { flex-wrap: wrap; }
    .action-card { flex: 0 0 calc(33% - 0.5rem); }
    .role-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>
