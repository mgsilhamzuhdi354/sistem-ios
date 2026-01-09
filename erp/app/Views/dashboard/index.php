<?php
/**
 * Dashboard View - Full Design with Charts
 */
$currentPage = 'dashboard';
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
    <div>
        <h1>Dashboard</h1>
        <p>Welcome back! Here's your contract overview.</p>
    </div>
    <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
        <!-- Period Filter -->
        <select id="periodFilter" class="form-control" style="width: auto; padding: 8px 16px;" onchange="refreshDashboard()">
            <option value="today">Today</option>
            <option value="week">This Week</option>
            <option value="month" selected>This Month</option>
            <option value="quarter">This Quarter</option>
            <option value="year">This Year</option>
        </select>
        <!-- Auto Refresh Indicator -->
        <span id="refreshIndicator" style="font-size: 12px; color: var(--text-muted); display: flex; align-items: center; gap: 4px;">
            <i class="fas fa-sync-alt"></i> <span id="lastUpdate">Just now</span>
        </span>
        <a href="<?= BASE_URL ?>contracts/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> <span class="hide-mobile">New Contract</span>
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-file-contract"></i></div>
        <div class="stat-info">
            <span class="stat-value"><?= $contractStats['active'] ?? 0 ?></span>
            <span class="stat-label">Active Contracts</span>
        </div>
        <?php if (isset($contractStats['active_trend'])): ?>
        <div class="stat-trend <?= $contractStats['active_trend'] >= 0 ? 'up' : 'down' ?>">
            <i class="fas fa-arrow-<?= $contractStats['active_trend'] >= 0 ? 'up' : 'down' ?>"></i> 
            <?= abs($contractStats['active_trend']) ?>%
        </div>
        <?php endif; ?>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
        <div class="stat-info">
            <span class="stat-value"><?= $contractStats['expiring_30'] ?? 0 ?></span>
            <span class="stat-label">Expiring Soon</span>
        </div>
        <?php if (($contractStats['expiring_30'] ?? 0) > 0): ?>
        <div class="stat-trend warning"><i class="fas fa-exclamation"></i> Alert</div>
        <?php endif; ?>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <span class="stat-value"><?= $contractStats['total_crew'] ?? 0 ?></span>
            <span class="stat-label">Total Crew</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fas fa-dollar-sign"></i></div>
        <div class="stat-info">
            <span class="stat-value">$<?= number_format(($monthlyPayroll ?? 0) / 1000) ?>K</span>
            <span class="stat-label">Monthly Payroll</span>
        </div>
    </div>
</div>

<!-- Alerts Section -->
<div class="alerts-section" style="margin-bottom: 24px;">
    <h3 style="margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-bell" style="color: var(--warning);"></i> Contract Alerts
    </h3>
    <div class="alerts-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
        <div class="alert-card danger" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 16px;">
            <div class="alert-icon" style="width: 48px; height: 48px; background: rgba(239, 68, 68, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-exclamation-triangle" style="color: var(--danger); font-size: 20px;"></i>
            </div>
            <div class="alert-content" style="flex: 1;">
                <span class="alert-count" style="font-size: 28px; font-weight: 700; display: block;"><?= $contractStats['expiring_7'] ?? 0 ?></span>
                <span class="alert-text" style="color: var(--text-muted); font-size: 13px;">Contracts expiring in 7 days</span>
            </div>
            <a href="<?= BASE_URL ?>contracts/expiring?days=7" class="alert-action" style="color: var(--danger); text-decoration: none; font-weight: 500; font-size: 14px;">View <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="alert-card warning" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 16px;">
            <div class="alert-icon" style="width: 48px; height: 48px; background: rgba(245, 158, 11, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-clock" style="color: var(--warning); font-size: 20px;"></i>
            </div>
            <div class="alert-content" style="flex: 1;">
                <span class="alert-count" style="font-size: 28px; font-weight: 700; display: block;"><?= $contractStats['expiring_30'] ?? 0 ?></span>
                <span class="alert-text" style="color: var(--text-muted); font-size: 13px;">Contracts expiring in 30 days</span>
            </div>
            <a href="<?= BASE_URL ?>contracts/expiring?days=30" class="alert-action" style="color: var(--warning); text-decoration: none; font-weight: 500; font-size: 14px;">View <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="alert-card info" style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 16px;">
            <div class="alert-icon" style="width: 48px; height: 48px; background: rgba(59, 130, 246, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-info-circle" style="color: var(--info); font-size: 20px;"></i>
            </div>
            <div class="alert-content" style="flex: 1;">
                <span class="alert-count" style="font-size: 28px; font-weight: 700; display: block;"><?= $contractStats['expiring_60'] ?? 0 ?></span>
                <span class="alert-text" style="color: var(--text-muted); font-size: 13px;">Contracts expiring in 60 days</span>
            </div>
            <a href="<?= BASE_URL ?>contracts/expiring?days=60" class="alert-action" style="color: var(--info); text-decoration: none; font-weight: 500; font-size: 14px;">View <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="charts-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 24px;">
    <!-- Contracts by Vessel Chart -->
    <div class="chart-card card">
        <div class="chart-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3>Contracts by Vessel</h3>
            <select class="form-control" style="width: auto; padding: 6px 12px;">
                <option>This Month</option>
                <option>Last 3 Months</option>
                <option>This Year</option>
            </select>
        </div>
        <div class="chart-body" style="display: flex; align-items: center; gap: 30px;">
            <!-- Donut Chart -->
            <div class="pie-chart-container" style="position: relative; width: 150px; height: 150px;">
                <svg viewBox="0 0 100 100" style="transform: rotate(-90deg); width: 100%; height: 100%;">
                    <?php
                    $vesselContracts = $vesselStats['vessel_contracts'] ?? [
                        ['name' => 'MV Pacific Star', 'count' => 35],
                        ['name' => 'MV Ocean Glory', 'count' => 25],
                        ['name' => 'MT Borneo', 'count' => 20],
                        ['name' => 'Others', 'count' => 20]
                    ];
                    $colors = ['#3B82F6', '#10B981', '#F59E0B', '#8B5CF6'];
                    $total = array_sum(array_column($vesselContracts, 'count'));
                    $offset = 0;
                    foreach ($vesselContracts as $i => $vessel):
                        $percentage = $total > 0 ? ($vessel['count'] / $total) * 100 : 0;
                        $dasharray = $percentage * 2.51327;
                    ?>
                    <circle cx="50" cy="50" r="40" fill="none" stroke="<?= $colors[$i % 4] ?>" 
                            stroke-width="20" stroke-dasharray="<?= $dasharray ?> 251.327" 
                            stroke-dashoffset="<?= -$offset * 2.51327 ?>" />
                    <?php 
                        $offset += $percentage;
                    endforeach; 
                    ?>
                </svg>
            </div>
            <!-- Legend -->
            <div class="chart-legend" style="display: flex; flex-direction: column; gap: 10px;">
                <?php foreach ($vesselContracts as $i => $vessel): ?>
                <div class="legend-item" style="display: flex; align-items: center; gap: 8px; font-size: 13px;">
                    <span class="dot" style="width: 10px; height: 10px; border-radius: 50%; background: <?= $colors[$i % 4] ?>;"></span>
                    <?= htmlspecialchars($vessel['name']) ?> (<?= $vessel['count'] ?>)
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Monthly Contracts Chart -->
    <div class="chart-card card">
        <div class="chart-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3>Monthly Contracts</h3>
            <select class="form-control" style="width: auto; padding: 6px 12px;">
                <option><?= date('Y') ?></option>
                <option><?= date('Y') - 1 ?></option>
            </select>
        </div>
        <div class="chart-body">
            <?php
            $monthlyData = $contractStats['monthly'] ?? [
                ['month' => 'Jan', 'count' => 24],
                ['month' => 'Feb', 'count' => 30],
                ['month' => 'Mar', 'count' => 22],
                ['month' => 'Apr', 'count' => 32],
                ['month' => 'May', 'count' => 26],
                ['month' => 'Jun', 'count' => 36]
            ];
            $maxCount = max(array_column($monthlyData, 'count')) ?: 1;
            ?>
            <div class="bar-chart" style="display: flex; align-items: flex-end; gap: 12px; height: 150px;">
                <?php foreach ($monthlyData as $data): 
                    $height = ($data['count'] / $maxCount) * 100;
                    $isCurrentMonth = ($data['month'] === date('M'));
                ?>
                <div class="bar-group" style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 8px;">
                    <div class="bar <?= $isCurrentMonth ? 'active' : '' ?>" 
                         style="width: 100%; height: <?= $height ?>%; 
                                background: <?= $isCurrentMonth ? 'linear-gradient(180deg, var(--accent-gold), var(--accent-gold-light))' : 'linear-gradient(180deg, var(--primary-blue), var(--info))' ?>; 
                                border-radius: 6px 6px 0 0; 
                                display: flex; align-items: flex-start; justify-content: center; padding-top: 8px;
                                min-height: 30px;">
                        <span style="font-size: 12px; font-weight: 600;"><?= $data['count'] ?></span>
                    </div>
                    <span class="bar-label" style="font-size: 12px; color: var(--text-muted);"><?= $data['month'] ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Contracts Table -->
<div class="table-card">
    <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3>Recent Contracts</h3>
        <a href="<?= BASE_URL ?>contracts" class="btn-link" style="color: var(--accent-gold); text-decoration: none; font-weight: 500;">
            View All <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Contract No</th>
                <th>Crew Name</th>
                <th>Rank</th>
                <th>Vessel</th>
                <th>Sign On</th>
                <th>Sign Off</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($recentContracts)): ?>
                <tr><td colspan="8" style="text-align: center; color: var(--text-muted);">No contracts found</td></tr>
            <?php else: ?>
                <?php foreach ($recentContracts as $contract): ?>
                    <?php
                    $statusClass = [
                        'draft' => 'secondary',
                        'pending_approval' => 'warning',
                        'active' => 'success',
                        'onboard' => 'info',
                        'completed' => 'info',
                        'terminated' => 'danger',
                        'expiring' => 'warning',
                    ][$contract['status']] ?? 'secondary';
                    
                    $initials = implode('', array_map(fn($n) => strtoupper($n[0] ?? ''), explode(' ', $contract['crew_name'])));
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($contract['contract_no']) ?></strong></td>
                        <td>
                            <div class="crew-info" style="display: flex; align-items: center; gap: 10px;">
                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($contract['crew_name']) ?>&background=0A2463&color=fff&size=32" alt="" style="width: 32px; height: 32px; border-radius: 50%;">
                                <span><?= htmlspecialchars($contract['crew_name']) ?></span>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($contract['rank_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($contract['vessel_name'] ?? '-') ?></td>
                        <td><?= $contract['sign_on_date'] ? date('d M Y', strtotime($contract['sign_on_date'])) : '-' ?></td>
                        <td><?= $contract['sign_off_date'] ? date('d M Y', strtotime($contract['sign_off_date'])) : '-' ?></td>
                        <td><span class="badge badge-<?= $statusClass ?>"><?= ucfirst(str_replace('_', ' ', $contract['status'])) ?></span></td>
                        <td>
                            <div class="action-buttons" style="display: flex; gap: 4px;">
                                <a href="<?= BASE_URL ?>contracts/<?= $contract['id'] ?>" class="btn-icon" title="View"><i class="fas fa-eye"></i></a>
                                <a href="<?= BASE_URL ?>contracts/edit/<?= $contract['id'] ?>" class="btn-icon" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="<?= BASE_URL ?>contracts/renew/<?= $contract['id'] ?>" class="btn-icon" title="Renew"><i class="fas fa-redo"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
/* Additional Dashboard Styles */
.stats-grid .stat-card {
    display: flex;
    align-items: center;
    gap: 16px;
    position: relative;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    display: block;
}

.stat-label {
    font-size: 13px;
    color: var(--text-muted);
}

.stat-trend {
    position: absolute;
    top: 16px;
    right: 16px;
    font-size: 12px;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 4px;
}

.stat-trend.up {
    background: rgba(16, 185, 129, 0.2);
    color: var(--success);
}

.stat-trend.down {
    background: rgba(239, 68, 68, 0.2);
    color: var(--danger);
}

.stat-trend.warning {
    background: rgba(245, 158, 11, 0.2);
    color: var(--warning);
}

.stat-icon.orange {
    background: rgba(245, 158, 11, 0.2);
    color: var(--warning);
}

@media (max-width: 1200px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }
    .alerts-grid {
        grid-template-columns: 1fr !important;
    }
    .charts-grid {
        grid-template-columns: 1fr !important;
    }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>
