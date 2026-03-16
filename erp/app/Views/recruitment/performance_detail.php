<?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>
    * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
    .perf-page { margin-left: 256px; padding: 2rem 2.5rem; background: #f8fafc; min-height: 100vh; }

    .back-link { display: inline-flex; align-items: center; gap: 6px; color: #6366f1; font-weight: 600; text-decoration: none; margin-bottom: 1.5rem; font-size: 0.9rem; }
    .back-link:hover { color: #4338ca; }

    .detail-header { display: flex; align-items: center; gap: 1.5rem; margin-bottom: 2rem; background: white; padding: 2rem; border-radius: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
    .detail-avatar { width: 72px; height: 72px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-weight: 900; color: white; font-size: 1.8rem; background: linear-gradient(135deg,#6366f1,#8b5cf6); flex-shrink: 0; }
    .detail-info h1 { margin: 0; font-size: 1.5rem; font-weight: 800; color: #1e293b; }
    .detail-info p { margin: 4px 0 0; color: #64748b; font-size: 0.9rem; }
    .detail-pts { margin-left: auto; text-align: center; }
    .detail-pts .big { font-size: 2.5rem; font-weight: 900; color: #6366f1; line-height: 1; }
    .detail-pts .label { font-size: 0.8rem; color: #94a3b8; font-weight: 600; margin-top: 4px; }

    .stats-mini { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
    .mini-card { background: white; border-radius: 14px; padding: 1.25rem; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.04); transition: transform 0.2s; }
    .mini-card:hover { transform: translateY(-2px); }
    .mini-card .m-icon { font-size: 28px; margin-bottom: 6px; }
    .mini-card .m-val { font-size: 1.4rem; font-weight: 800; color: #1e293b; }
    .mini-card .m-label { font-size: 0.75rem; color: #94a3b8; font-weight: 600; }

    .timeline { background: white; border-radius: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); padding: 1.5rem; }
    .timeline h2 { font-size: 1.15rem; font-weight: 700; color: #1e293b; margin: 0 0 1.25rem; display: flex; align-items: center; gap: 8px; }
    .tl-item { display: flex; gap: 1rem; padding: 0.85rem 0; border-bottom: 1px solid #f8fafc; align-items: flex-start; }
    .tl-item:last-child { border-bottom: none; }
    .tl-dot { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .tl-dot .material-icons { font-size: 18px; color: white; }
    .tl-content { flex: 1; }
    .tl-content .tl-title { font-weight: 600; color: #1e293b; font-size: 0.9rem; }
    .tl-content .tl-desc { font-size: 0.8rem; color: #94a3b8; margin-top: 2px; }
    .tl-meta { text-align: right; flex-shrink: 0; }
    .tl-meta .tl-pts { font-weight: 800; color: #22c55e; font-size: 1rem; }
    .tl-meta .tl-date { font-size: 0.75rem; color: #94a3b8; }

    .empty-state { text-align: center; padding: 3rem; color: #94a3b8; }
    .empty-state .material-icons { font-size: 3rem; opacity: 0.3; }

    @media (max-width: 768px) { .perf-page { margin-left: 0; padding: 1rem; } .detail-header { flex-wrap: wrap; } .detail-pts { margin-left: 0; width: 100%; margin-top: 1rem; } }
</style>

<div class="perf-page">
    <a href="<?= BASE_URL ?>RecruiterPerformance" class="back-link">
        <span class="material-icons" style="font-size:18px;">arrow_back</span> Kembali ke Leaderboard
    </a>

    <!-- Header -->
    <div class="detail-header">
        <div class="detail-avatar"><?= strtoupper(substr($recruiterName, 0, 1)) ?></div>
        <div class="detail-info">
            <h1><?= htmlspecialchars($recruiterName) ?></h1>
            <p>PIC Recruitment · ID #<?= $recruiterId ?></p>
        </div>
        <div class="detail-pts">
            <div class="big"><?= number_format($totalPoints) ?></div>
            <div class="label">TOTAL POIN</div>
        </div>
    </div>

    <!-- Action Type Stats -->
    <div class="stats-mini">
        <?php foreach ($configs as $key => $cfg): 
            $at = $actionTotals[$key] ?? ['total_points' => 0, 'count' => 0];
        ?>
        <div class="mini-card">
            <span class="material-icons m-icon" style="color:<?= htmlspecialchars($cfg['color']) ?>;"><?= htmlspecialchars($cfg['icon']) ?></span>
            <div class="m-val"><?= number_format($at['total_points']) ?></div>
            <div class="m-label"><?= htmlspecialchars($cfg['label']) ?> (<?= $at['count'] ?>x)</div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Timeline -->
    <div class="timeline">
        <h2><span class="material-icons" style="color:#6366f1;font-size:22px;">timeline</span> Histori Poin</h2>
        <?php if (!empty($history)): ?>
            <?php foreach ($history as $h): ?>
            <div class="tl-item">
                <div class="tl-dot" style="background:<?= htmlspecialchars($h['color'] ?? '#6366f1') ?>;">
                    <span class="material-icons"><?= htmlspecialchars($h['icon'] ?? 'star') ?></span>
                </div>
                <div class="tl-content">
                    <div class="tl-title"><?= htmlspecialchars($h['label'] ?? $h['action_type']) ?></div>
                    <div class="tl-desc">
                        <?= $h['applicant_name'] ? htmlspecialchars($h['applicant_name']) . ' — ' : '' ?>
                        <?= htmlspecialchars($h['description'] ?? '') ?>
                    </div>
                </div>
                <div class="tl-meta">
                    <div class="tl-pts">+<?= $h['points'] ?></div>
                    <div class="tl-date"><?= date('d M Y H:i', strtotime($h['created_at'])) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <span class="material-icons">inbox</span>
                <p>Belum ada histori poin untuk perekrut ini.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
