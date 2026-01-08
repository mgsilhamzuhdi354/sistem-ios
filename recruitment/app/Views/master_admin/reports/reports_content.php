<!-- Master Admin Reports Content -->
<div class="page-header mb-4">
    <h1 class="mb-1"><i class="fas fa-chart-bar me-2"></i>Reports & Analytics</h1>
    <p class="text-muted mb-0">Comprehensive overview of recruitment performance</p>
</div>

<!-- Stats Overview -->
<div class="stats-grid mb-4">
    <div class="stat-card blue">
        <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
        <div class="stat-info">
            <h3><?= $stats['total_applications'] ?? 0 ?></h3>
            <p>Total Applications</p>
        </div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
        <div class="stat-info">
            <h3><?= $stats['this_month'] ?? 0 ?></h3>
            <p>This Month</p>
        </div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <h3><?= $stats['total_applicants'] ?? 0 ?></h3>
            <p>Registered Applicants</p>
        </div>
    </div>
    <div class="stat-card purple">
        <div class="stat-icon"><i class="fas fa-briefcase"></i></div>
        <div class="stat-info">
            <h3><?= $stats['active_vacancies'] ?? 0 ?></h3>
            <p>Active Vacancies</p>
        </div>
    </div>
    <div class="stat-card teal">
        <div class="stat-icon"><i class="fas fa-user-check"></i></div>
        <div class="stat-info">
            <h3><?= $stats['hired_this_month'] ?? 0 ?></h3>
            <p>Hired This Month</p>
        </div>
    </div>
    <div class="stat-card red">
        <div class="stat-icon"><i class="fas fa-clock"></i></div>
        <div class="stat-info">
            <h3><?= $stats['pending_review'] ?? 0 ?></h3>
            <p>Pending Review</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Status Distribution -->
    <div class="col-lg-6">
        <div class="report-card">
            <div class="report-header">
                <h5><i class="fas fa-chart-pie me-2"></i>Applications by Status</h5>
            </div>
            <div class="report-body">
                <?php foreach ($statusStats as $status): ?>
                <div class="status-bar-item">
                    <div class="status-info">
                        <span class="status-dot" style="background: <?= $status['color'] ?? '#6c757d' ?>"></span>
                        <span class="status-name"><?= $status['name'] ?></span>
                    </div>
                    <div class="status-value"><?= $status['count'] ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Department Distribution -->
    <div class="col-lg-6">
        <div class="report-card">
            <div class="report-header">
                <h5><i class="fas fa-building me-2"></i>Applications by Department</h5>
            </div>
            <div class="report-body">
                <?php if (empty($departmentStats)): ?>
                <div class="empty-mini">No department data available</div>
                <?php else: ?>
                <?php foreach ($departmentStats as $dept): ?>
                <div class="dept-bar-item">
                    <span class="dept-name"><?= $dept['name'] ?: 'Uncategorized' ?></span>
                    <div class="dept-progress">
                        <div class="dept-bar" style="width: <?= min(100, ($dept['count'] / max(1, $stats['total_applications'])) * 100) ?>%"></div>
                    </div>
                    <span class="dept-value"><?= $dept['count'] ?></span>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Top Crewing Performance -->
    <div class="col-lg-6">
        <div class="report-card">
            <div class="report-header">
                <h5><i class="fas fa-trophy me-2"></i>Top Crewing Performance</h5>
            </div>
            <div class="report-body">
                <?php if (empty($crewingPerformance)): ?>
                <div class="empty-mini">No crewing data available</div>
                <?php else: ?>
                <table class="mini-table">
                    <thead>
                        <tr>
                            <th>Crewing</th>
                            <th>Handled</th>
                            <th>Completed</th>
                            <th>Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($crewingPerformance as $idx => $crew): ?>
                        <tr>
                            <td>
                                <span class="rank-badge"><?= $idx + 1 ?></span>
                                <?= htmlspecialchars($crew['full_name']) ?>
                            </td>
                            <td><?= $crew['handled'] ?></td>
                            <td><?= $crew['completed'] ?></td>
                            <td>
                                <?php if ($crew['avg_rating']): ?>
                                <span class="rating"><i class="fas fa-star"></i> <?= number_format($crew['avg_rating'], 1) ?></span>
                                <?php else: ?>
                                <span class="no-rating">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Monthly Trends -->
    <div class="col-lg-6">
        <div class="report-card">
            <div class="report-header">
                <h5><i class="fas fa-chart-line me-2"></i>Monthly Trends</h5>
            </div>
            <div class="report-body">
                <?php if (empty($monthlyTrends)): ?>
                <div class="empty-mini">No trend data available</div>
                <?php else: ?>
                <div class="trend-bars">
                    <?php 
                    $maxCount = max(array_column($monthlyTrends, 'count'));
                    foreach ($monthlyTrends as $trend): 
                    $height = $maxCount > 0 ? ($trend['count'] / $maxCount) * 100 : 0;
                    ?>
                    <div class="trend-item">
                        <div class="trend-bar-wrapper">
                            <div class="trend-bar" style="height: <?= $height ?>%">
                                <span class="trend-value"><?= $trend['count'] ?></span>
                            </div>
                        </div>
                        <span class="trend-label"><?= date('M', strtotime($trend['month'] . '-01')) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
}
.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
}
.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
}
.stat-card.blue .stat-icon { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.stat-card.green .stat-icon { background: linear-gradient(135deg, #22c55e, #16a34a); }
.stat-card.orange .stat-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
.stat-card.purple .stat-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.stat-card.teal .stat-icon { background: linear-gradient(135deg, #14b8a6, #0d9488); }
.stat-card.red .stat-icon { background: linear-gradient(135deg, #ef4444, #dc2626); }
.stat-info h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
}
.stat-info p {
    margin: 0;
    font-size: 0.8rem;
    color: #6b7280;
}

/* Report Cards */
.report-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
    height: 100%;
}
.report-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f3f4f6;
}
.report-header h5 {
    margin: 0;
    font-size: 1rem;
    color: #374151;
}
.report-body {
    padding: 1.5rem;
}

/* Status Bars */
.status-bar-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f9fafb;
}
.status-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.status-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}
.status-value {
    font-weight: 600;
    color: #374151;
}

/* Department Bars */
.dept-bar-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}
.dept-name {
    width: 100px;
    font-size: 0.85rem;
    color: #6b7280;
}
.dept-progress {
    flex: 1;
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
}
.dept-bar {
    height: 100%;
    background: linear-gradient(90deg, #3b82f6, #1d4ed8);
    border-radius: 4px;
}
.dept-value {
    font-weight: 600;
    min-width: 30px;
    text-align: right;
}

/* Mini Table */
.mini-table {
    width: 100%;
    border-collapse: collapse;
}
.mini-table th {
    text-align: left;
    font-size: 0.75rem;
    color: #9ca3af;
    text-transform: uppercase;
    padding: 0.5rem;
    border-bottom: 1px solid #f3f4f6;
}
.mini-table td {
    padding: 0.75rem 0.5rem;
    border-bottom: 1px solid #f9fafb;
    font-size: 0.9rem;
}
.rank-badge {
    width: 20px;
    height: 20px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    margin-right: 0.5rem;
}
.rating {
    color: #f59e0b;
}
.no-rating {
    color: #d1d5db;
}

/* Trend Bars */
.trend-bars {
    display: flex;
    justify-content: space-around;
    align-items: flex-end;
    height: 150px;
}
.trend-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
}
.trend-bar-wrapper {
    height: 120px;
    display: flex;
    align-items: flex-end;
    margin-bottom: 0.5rem;
}
.trend-bar {
    width: 30px;
    background: linear-gradient(180deg, #3b82f6, #1d4ed8);
    border-radius: 4px 4px 0 0;
    min-height: 10px;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    position: relative;
}
.trend-value {
    position: absolute;
    top: -20px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #374151;
}
.trend-label {
    font-size: 0.7rem;
    color: #9ca3af;
}

.empty-mini {
    text-align: center;
    color: #9ca3af;
    padding: 2rem;
}
</style>
