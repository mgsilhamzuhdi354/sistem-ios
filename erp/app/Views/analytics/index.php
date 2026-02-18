<?php
/**
 * Monitoring Center - Main Dashboard
 * Display visitor analytics and recruitment funnel
 */
$currentPage = 'analytics';
ob_start();
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-chart-line"></i> Monitoring Center</h1>
        <p>Track visitor analytics and recruitment metrics in real-time</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <input type="date" id="start_date" class="form-control" value="<?= $start_date ?>" style="width: auto;">
        <input type="date" id="end_date" class="form-control" value="<?= $end_date ?>" style="width: auto;">
        <button class="btn" style="background: var(--navy); color: white;" onclick="applyDateFilter()">
            <i class="fas fa-filter"></i> Apply Filter
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
    <!-- Total Visits -->
    <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="margin: 0; font-size: 32px; color: white;">
                    <?= number_format($visitor_stats['total_visits'] ?? 0) ?>
                </h2>
                <p style="margin: 8px 0 0 0; opacity: 0.9;">Total Page Views</p>
            </div>
            <i class="fas fa-eye" style="font-size: 48px; opacity: 0.3;"></i>
        </div>
    </div>

    <!-- Unique Visitors -->
    <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="margin: 0; font-size: 32px; color: white;">
                    <?= number_format($visitor_stats['unique_visitors'] ?? 0) ?>
                </h2>
                <p style="margin: 8px 0 0 0; opacity: 0.9;">Unique Visitors</p>
            </div>
            <i class="fas fa-users" style="font-size: 48px; opacity: 0.3;"></i>
        </div>
    </div>

    <!-- Applications -->
    <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="margin: 0; font-size: 32px; color: white;">
                    <?= number_format($funnel_data['funnel']['applications'] ?? 0) ?>
                </h2>
                <p style="margin: 8px 0 0 0; opacity: 0.9;">Applications</p>
            </div>
            <i class="fas fa-file-alt" style="font-size: 48px; opacity: 0.3;"></i>
        </div>
    </div>

    <!-- Hired -->
    <div class="card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="margin: 0; font-size: 32px; color: white;">
                    <?= number_format($funnel_data['funnel']['hired'] ?? 0) ?>
                </h2>
                <p style="margin: 8px 0 0 0; opacity: 0.9;">Hired</p>
            </div>
            <i class="fas fa-user-check" style="font-size: 48px; opacity: 0.3;"></i>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 30px;">
    <!-- Recruitment Funnel Chart -->
    <div class="card">
        <h3 style="margin-bottom: 20px;"><i class="fas fa-filter" style="color: var(--accent-gold);"></i> Recruitment
            Funnel</h3>
        <canvas id="funnelChart" height="100"></canvas>
    </div>

    <!-- Traffic Sources -->
    <div class="card">
        <h3 style="margin-bottom: 20px;"><i class="fas fa-chart-pie" style="color: var(--accent-gold);"></i> Traffic
            Sources</h3>
        <canvas id="sourcesChart"></canvas>
    </div>
</div>

<!-- Device Breakdown & Recent Visitors -->
<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
    <!-- Device Breakdown -->
    <div class="card">
        <h3 style="margin-bottom: 20px;"><i class="fas fa-mobile-alt" style="color: var(--accent-gold);"></i> Device
            Types</h3>
        <canvas id="devicesChart"></canvas>
    </div>

    <!-- Recent Visitors -->
    <div class="card">
        <h3 style="margin-bottom: 20px;"><i class="fas fa-clock" style="color: var(--success);"></i> Recent Visitors
        </h3>
        <div style="max-height: 400px; overflow-y: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Page</th>
                        <th>Location</th>
                        <th>Device</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recent_visitors)): ?>
                        <?php foreach (array_slice($recent_visitors, 0, 15) as $visitor): ?>
                            <tr>
                                <td>
                                    <?= date('H:i', strtotime($visitor['visited_at'])) ?>
                                </td>
                                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= htmlspecialchars($visitor['page_visited']) ?>
                                </td>
                                <td>
                                    <?php if ($visitor['city'] && $visitor['country']): ?>
                                        <?= htmlspecialchars($visitor['city']) ?>,
                                        <?= htmlspecialchars($visitor['country']) ?>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted);">Unknown</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge" style="background: var(--info);">
                                        <i
                                            class="fas fa-<?= $visitor['device_type'] === 'mobile' ? 'mobile' : ($visitor['device_type'] === 'tablet' ? 'tablet' : 'desktop') ?>"></i>
                                        <?= ucfirst($visitor['device_type']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                No recent visitors
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Funnel Chart
    const funnelCtx = document.getElementById('funnelChart').getContext('2d');
    new Chart(funnelCtx, {
        type: 'bar',
        data: {
            labels: ['Page Views', 'Applications', 'Interviews', 'Approved', 'Hired'],
            datasets: [{
                label: 'Count',
                data: [
                <?= $funnel_data['funnel']['page_views'] ?? 0 ?>,
                <?= $funnel_data['funnel']['applications'] ?? 0 ?>,
                <?= $funnel_data['funnel']['interviews'] ?? 0 ?>,
                <?= $funnel_data['funnel']['approved'] ?? 0 ?>,
                <?= $funnel_data['funnel']['hired'] ?? 0 ?>
            ],
                backgroundColor: [
                    'rgba(102, 126, 234, 0.8)',
                    'rgba(118, 75, 162, 0.8)',
                    'rgba(237, 100, 166, 0.8)',
                    'rgba(255, 154, 162, 0.8)',
                    'rgba(250, 208, 196, 0.8)'
                ],
                borderColor: [
                    'rgba(102, 126, 234, 1)',
                    'rgba(118, 75, 162, 1)',
                    'rgba(237, 100, 166, 1)',
                    'rgba(255, 154, 162, 1)',
                    'rgba(250, 208, 196, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.1)' },
                    ticks: { color: '#fff' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#fff' }
                }
            }
        }
    });

    // Traffic Sources Pie Chart
    const sourcesCtx = document.getElementById('sourcesChart').getContext('2d');
    const sourcesData = <?= json_encode($visitor_stats['traffic_sources'] ?? []) ?>;
    new Chart(sourcesCtx, {
        type: 'doughnut',
        data: {
            labels: sourcesData.map(s => s.source),
            datasets: [{
                data: sourcesData.map(s => s.count),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom', labels: { color: '#fff', padding: 15 } }
            }
        }
    });

    // Devices Pie Chart
    const devicesCtx = document.getElementById('devicesChart').getContext('2d');
    const devicesData = <?= json_encode($visitor_stats['devices'] ?? []) ?>;
    new Chart(devicesCtx, {
        type: 'pie',
        data: {
            labels: devicesData.map(d => d.device_type.charAt(0).toUpperCase() + d.device_type.slice(1)),
            datasets: [{
                data: devicesData.map(d => d.count),
                backgroundColor: [
                    'rgba(67, 233, 123, 0.8)',
                    'rgba(56, 249, 215, 0.8)',
                    'rgba(79, 172, 254, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { color: '#fff', padding: 15 } }
            }
        }
    });

    // Date Filter
    function applyDateFilter() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        window.location.href = `<?= BASE_URL ?>analytics?start_date=${startDate}&end_date=${endDate}`;
    }
</script>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>