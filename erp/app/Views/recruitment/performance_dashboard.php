<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Kinerja Perekrut') ?> | IndoOcean ERP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body style="margin:0;padding:0;overflow-x:hidden;">
<?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

<style>
    * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
    .perf-page { margin-left: 256px; padding: 2rem 2.5rem; background: #f8fafc; min-height: 100vh; }
    
    .perf-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; }
    .perf-header h1 { font-size: 1.75rem; font-weight: 800; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 12px; }
    .perf-header h1 .material-icons { font-size: 2rem; color: #f59e0b; }
    
    .period-tabs { display: flex; gap: 0.5rem; background: white; padding: 4px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
    .period-tabs a { padding: 8px 18px; border-radius: 10px; font-size: 0.85rem; font-weight: 600; color: #64748b; text-decoration: none; transition: all 0.2s; }
    .period-tabs a:hover { color: #1e293b; background: #f1f5f9; }
    .period-tabs a.active { background: linear-gradient(135deg,#6366f1,#8b5cf6); color: white; box-shadow: 0 2px 8px rgba(99,102,241,0.3); }

    /* Stats Cards */
    .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 2rem; }
    .stat-card { background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04); position: relative; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
    .stat-card .stat-icon { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; }
    .stat-card .stat-value { font-size: 2rem; font-weight: 800; color: #1e293b; line-height: 1; }
    .stat-card .stat-label { font-size: 0.85rem; color: #64748b; margin-top: 4px; font-weight: 500; }
    .stat-card .stat-glow { position: absolute; width: 120px; height: 120px; border-radius: 50%; top: -30px; right: -30px; opacity: 0.1; }

    /* Leaderboard */
    .leaderboard-card { background: white; border-radius: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); overflow: hidden; }
    .leaderboard-header { padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; }
    .leaderboard-header h2 { margin: 0; font-size: 1.15rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px; }

    .lb-row { display: grid; grid-template-columns: 60px 1fr 120px 100px 100px; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid #f8fafc; transition: background 0.15s; }
    .lb-row:hover { background: #fafbff; }
    .lb-rank { font-size: 1.5rem; font-weight: 800; color: #94a3b8; text-align: center; }
    .lb-rank.gold { color: #f59e0b; }
    .lb-rank.silver { color: #94a3b8; }
    .lb-rank.bronze { color: #cd7f32; }
    .lb-name { display: flex; align-items: center; gap: 12px; }
    .lb-avatar { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 800; color: white; font-size: 1rem; flex-shrink: 0; }
    .lb-details { min-width: 0; }
    .lb-details .name { font-weight: 700; color: #1e293b; font-size: 0.95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .lb-details .sub { font-size: 0.8rem; color: #94a3b8; }
    .lb-points { font-size: 1.4rem; font-weight: 800; text-align: center; }
    .lb-points .unit { font-size: 0.7rem; color: #94a3b8; font-weight: 600; }
    .lb-actions, .lb-applicants { text-align: center; font-weight: 600; color: #64748b; font-size: 0.9rem; }
    .lb-head { background: #f8fafc; font-weight: 700; color: #64748b; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; padding: 0.75rem 1.5rem; }

    /* Activity Feed */
    .feed-card { background: white; border-radius: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
    .feed-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; }
    .feed-header h2 { margin: 0; font-size: 1.1rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px; }
    .feed-item { display: flex; align-items: center; gap: 12px; padding: 0.85rem 1.5rem; border-bottom: 1px solid #f8fafc; }
    .feed-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .feed-icon .material-icons { font-size: 18px; color: white; }
    .feed-info { flex: 1; min-width: 0; }
    .feed-info .feed-title { font-weight: 600; color: #1e293b; font-size: 0.88rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .feed-info .feed-sub { font-size: 0.78rem; color: #94a3b8; }
    .feed-pts { font-weight: 800; color: #22c55e; font-size: 0.95rem; white-space: nowrap; }

    .grid-2 { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-top: 1.5rem; }
    .grid-2-equal { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem; }

    /* Chart */
    .chart-card { background: white; border-radius: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); padding: 1.5rem; }
    .chart-card h3 { margin: 0 0 1rem; font-size: 1.05rem; font-weight: 700; color: #1e293b; }
    .chart-container { position: relative; height: 220px; width: 100%; }

    /* Config btn */
    .btn-settings { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 10px; border: 1px solid #e2e8f0; background: white; font-size: 0.85rem; font-weight: 600; color: #64748b; cursor: pointer; text-decoration: none; transition: all 0.2s; }
    .btn-settings:hover { border-color: #6366f1; color: #6366f1; }

    /* Empty state */
    .empty-state { text-align: center; padding: 3rem; color: #94a3b8; }
    .empty-state .material-icons { font-size: 4rem; opacity: 0.3; margin-bottom: 1rem; }
    .empty-state p { font-size: 1rem; font-weight: 500; }

    /* Trophy animation */
    @keyframes bounce { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
    .trophy-bounce { animation: bounce 1s ease-in-out 2; }

    @media (max-width: 1200px) { .stats-row { grid-template-columns: repeat(2, 1fr); } .grid-2, .grid-2-equal { grid-template-columns: 1fr; } }
    @media (max-width: 768px) { .perf-page { margin-left: 0; padding: 1rem; } .stats-row { grid-template-columns: 1fr; } .lb-row { grid-template-columns: 40px 1fr 80px; } .lb-actions, .lb-applicants { display: none; } }
</style>

<div class="perf-page">
    <!-- Header -->
    <div class="perf-header">
        <h1>
            <span class="material-icons trophy-bounce">emoji_events</span>
            Kinerja Perekrut
            <span style="font-size:0.85rem;font-weight:600;color:#64748b;margin-left:8px;"><?= htmlspecialchars($periodLabel) ?></span>
        </h1>
        <div style="display:flex;gap:0.75rem;align-items:center;">
            <div class="period-tabs">
                <a href="<?= BASE_URL ?>RecruiterPerformance?period=month" class="<?= $period === 'month' ? 'active' : '' ?>">Bulan Ini</a>
                <a href="<?= BASE_URL ?>RecruiterPerformance?period=quarter" class="<?= $period === 'quarter' ? 'active' : '' ?>">Kuartal</a>
                <a href="<?= BASE_URL ?>RecruiterPerformance?period=year" class="<?= $period === 'year' ? 'active' : '' ?>">Tahun</a>
                <a href="<?= BASE_URL ?>RecruiterPerformance?period=all" class="<?= $period === 'all' ? 'active' : '' ?>">Semua</a>
            </div>
            <a href="<?= BASE_URL ?>RecruiterPerformance/settings" class="btn-settings">
                <span class="material-icons" style="font-size:16px;">tune</span> Konfigurasi
            </a>
        </div>
    </div>

    <?php if (!empty($flash)): ?>
    <div style="padding:1rem;border-radius:12px;margin-bottom:1.5rem;background:<?= $flash['type']==='success'?'#dcfce7':'#fee2e2' ?>;color:<?= $flash['type']==='success'?'#166534':'#991b1b' ?>;font-weight:600;display:flex;align-items:center;gap:8px;">
        <span class="material-icons" style="font-size:18px;"><?= $flash['type']==='success'?'check_circle':'error' ?></span>
        <?= htmlspecialchars($flash['message']) ?>
    </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-glow" style="background:#f59e0b;"></div>
            <div class="stat-icon" style="background:linear-gradient(135deg,#fef3c7,#fde68a);"><span class="material-icons" style="color:#f59e0b;">emoji_events</span></div>
            <div class="stat-value"><?= number_format($totalPoints) ?></div>
            <div class="stat-label">Total Poin</div>
        </div>
        <div class="stat-card">
            <div class="stat-glow" style="background:#6366f1;"></div>
            <div class="stat-icon" style="background:linear-gradient(135deg,#e0e7ff,#c7d2fe);"><span class="material-icons" style="color:#6366f1;">groups</span></div>
            <div class="stat-value"><?= number_format($totalRecruiters) ?></div>
            <div class="stat-label">Perekrut Aktif</div>
        </div>
        <div class="stat-card">
            <div class="stat-glow" style="background:#10b981;"></div>
            <div class="stat-icon" style="background:linear-gradient(135deg,#d1fae5,#a7f3d0);"><span class="material-icons" style="color:#10b981;">person_add</span></div>
            <div class="stat-value"><?= number_format($totalApplicants) ?></div>
            <div class="stat-label">Pelamar Ditangani</div>
        </div>
        <div class="stat-card">
            <div class="stat-glow" style="background:#3b82f6;"></div>
            <div class="stat-icon" style="background:linear-gradient(135deg,#dbeafe,#bfdbfe);"><span class="material-icons" style="color:#3b82f6;">leaderboard</span></div>
            <div class="stat-value"><?= count($leaderboard) ?></div>
            <div class="stat-label">Total PIC</div>
        </div>
    </div>

    <!-- Leaderboard -->
    <div class="leaderboard-card">
        <div class="leaderboard-header">
            <h2><span class="material-icons" style="color:#f59e0b;">leaderboard</span> Leaderboard Perekrut</h2>
        </div>
        <?php if (!empty($leaderboard)): ?>
        <div class="lb-row lb-head">
            <div>#</div>
            <div>Perekrut</div>
            <div style="text-align:center;">Poin</div>
            <div style="text-align:center;">Aksi</div>
            <div style="text-align:center;">Pelamar</div>
        </div>
        <?php
            $rank = 1;
            $avatarColors = ['#6366f1','#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#ec4899'];
            foreach ($leaderboard as $i => $r):
                $rankClass = $rank === 1 ? 'gold' : ($rank === 2 ? 'silver' : ($rank === 3 ? 'bronze' : ''));
                $medal = $rank === 1 ? '🥇' : ($rank === 2 ? '🥈' : ($rank === 3 ? '🥉' : $rank));
                $color = $avatarColors[$i % count($avatarColors)];
                $initial = strtoupper(substr($r['recruiter_name'] ?? '?', 0, 1));
                $pointColor = $r['total_points'] > 50 ? '#22c55e' : ($r['total_points'] > 20 ? '#f59e0b' : '#64748b');
        ?>
        <a href="<?= BASE_URL ?>RecruiterPerformance/detail/<?= $r['recruiter_user_id'] ?>" style="text-decoration:none;display:contents;">
            <div class="lb-row">
                <div class="lb-rank <?= $rankClass ?>"><?= $medal ?></div>
                <div class="lb-name">
                    <div class="lb-avatar" style="background:linear-gradient(135deg,<?= $color ?>,<?= $color ?>cc);"><?= $initial ?></div>
                    <div class="lb-details">
                        <div class="name"><?= htmlspecialchars($r['recruiter_name'] ?? 'Unknown') ?></div>
                        <div class="sub"><?= $r['last_activity'] ? 'Terakhir: ' . date('d M', strtotime($r['last_activity'])) : 'Belum ada aktivitas' ?></div>
                    </div>
                </div>
                <div class="lb-points" style="color:<?= $pointColor ?>;">
                    <?= number_format($r['total_points']) ?>
                    <div class="unit">PTS</div>
                </div>
                <div class="lb-actions"><?= number_format($r['total_actions']) ?></div>
                <div class="lb-applicants"><?= number_format($r['total_applicants']) ?></div>
            </div>
        </a>
        <?php $rank++; endforeach; ?>
        <?php else: ?>
        <div class="empty-state">
            <span class="material-icons">emoji_events</span>
            <p>Belum ada data kinerja. Poin akan bertambah saat perekrut menangani pelamar.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Chart & Activity Feed -->
    <div class="grid-2">
        <!-- Monthly Trend Chart -->
        <div class="chart-card">
            <h3><span class="material-icons" style="font-size:20px;vertical-align:middle;color:#6366f1;">trending_up</span> Tren Poin Bulanan</h3>
            <div class="chart-container"><canvas id="trendChart"></canvas></div>
        </div>

        <!-- Recent Activity -->
        <div class="feed-card">
            <div class="feed-header">
                <h2><span class="material-icons" style="color:#3b82f6;font-size:20px;">history</span> Aktivitas Terbaru</h2>
            </div>
            <div style="max-height:380px;overflow-y:auto;">
                <?php if (!empty($recentActivity)): ?>
                    <?php foreach (array_slice($recentActivity, 0, 10) as $act): ?>
                    <div class="feed-item">
                        <div class="feed-icon" style="background:<?= htmlspecialchars($act['color'] ?? '#6366f1') ?>;">
                            <span class="material-icons"><?= htmlspecialchars($act['icon'] ?? 'star') ?></span>
                        </div>
                        <div class="feed-info">
                            <div class="feed-title"><?= htmlspecialchars($act['recruiter_name']) ?> — <?= htmlspecialchars($act['label'] ?? $act['action_type']) ?></div>
                            <div class="feed-sub"><?= $act['applicant_name'] ? htmlspecialchars($act['applicant_name']) . ' · ' : '' ?><?= date('d M H:i', strtotime($act['created_at'])) ?></div>
                        </div>
                        <div class="feed-pts">+<?= $act['points'] ?></div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state" style="padding:2rem;">
                        <span class="material-icons" style="font-size:2.5rem;">inbox</span>
                        <p style="font-size:0.9rem;">Belum ada aktivitas</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Points Config Overview -->
    <div class="chart-card" style="margin-top:1.5rem;">
        <h3><span class="material-icons" style="font-size:20px;vertical-align:middle;color:#f59e0b;">settings</span> Konfigurasi Poin</h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:1rem;margin-top:1rem;">
            <?php foreach ($configs as $cfg): ?>
            <div style="background:<?= $cfg['color'] ?>10;border:1px solid <?= $cfg['color'] ?>30;border-radius:14px;padding:1rem;text-align:center;transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
                <span class="material-icons" style="font-size:28px;color:<?= $cfg['color'] ?>;display:block;margin-bottom:6px;"><?= htmlspecialchars($cfg['icon']) ?></span>
                <div style="font-weight:800;font-size:1.5rem;color:<?= $cfg['color'] ?>;"><?= $cfg['points'] ?></div>
                <div style="font-size:0.78rem;font-weight:600;color:#64748b;margin-top:2px;"><?= htmlspecialchars($cfg['label']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
// Monthly Trend Chart
const trendData = <?= json_encode($monthlyTrend) ?>;
const trendCtx = document.getElementById('trendChart');

if (trendCtx && trendData.length > 0) {
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: trendData.map(d => {
                const [y, m] = d.month.split('-');
                return ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'][parseInt(m)-1] + ' ' + y.slice(2);
            }),
            datasets: [{
                label: 'Total Poin',
                data: trendData.map(d => d.total_points),
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99,102,241,0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 6,
                pointBackgroundColor: '#6366f1',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }, {
                label: 'Perekrut Aktif',
                data: trendData.map(d => d.active_recruiters),
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245,158,11,0.08)',
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                borderDash: [5, 3],
                pointRadius: 4,
                pointBackgroundColor: '#f59e0b',
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 15 } } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { weight: 600 } } },
                y1: { position: 'right', beginAtZero: true, grid: { display: false }, ticks: { font: { weight: 600 }, stepSize: 1 } },
                x: { grid: { display: false }, ticks: { font: { weight: 600 } } }
            }
        }
    });
} else if (trendCtx) {
    trendCtx.parentNode.innerHTML += '<div class="empty-state" style="padding:1rem;"><p style="font-size:0.85rem;">Belum ada data tren</p></div>';
}
</script>
</body>
</html>
