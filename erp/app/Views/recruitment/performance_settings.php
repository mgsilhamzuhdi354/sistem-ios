<?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>
    * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
    .perf-page { margin-left: 256px; padding: 2rem 2.5rem; background: #f8fafc; min-height: 100vh; }

    .back-link { display: inline-flex; align-items: center; gap: 6px; color: #6366f1; font-weight: 600; text-decoration: none; margin-bottom: 1.5rem; font-size: 0.9rem; }
    .back-link:hover { color: #4338ca; }

    .settings-header { margin-bottom: 2rem; }
    .settings-header h1 { font-size: 1.5rem; font-weight: 800; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 10px; }

    .config-card { background: white; border-radius: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); overflow: hidden; }
    .config-header { padding: 1.5rem; border-bottom: 1px solid #f1f5f9; }
    .config-header h2 { margin: 0; font-size: 1.1rem; font-weight: 700; color: #1e293b; }
    .config-header p { margin: 4px 0 0; color: #64748b; font-size: 0.85rem; }

    .config-row { display: grid; grid-template-columns: 48px 1fr 200px 120px; align-items: center; gap: 1rem; padding: 1rem 1.5rem; border-bottom: 1px solid #f8fafc; transition: background 0.15s; }
    .config-row:hover { background: #fafbff; }
    .config-icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
    .config-icon .material-icons { font-size: 20px; color: white; }
    .config-info .cfg-label { font-weight: 700; color: #1e293b; font-size: 0.95rem; }
    .config-info .cfg-desc { font-size: 0.8rem; color: #94a3b8; margin-top: 2px; }
    .config-action-type { font-family: 'Courier New', monospace; font-size: 0.8rem; color: #6366f1; background: #eef2ff; padding: 4px 10px; border-radius: 8px; font-weight: 600; }
    .config-input { display: flex; align-items: center; gap: 6px; }
    .config-input input { width: 80px; padding: 10px 12px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 1.1rem; font-weight: 700; text-align: center; transition: all 0.2s; font-family: inherit; }
    .config-input input:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
    .config-input span { color: #94a3b8; font-size: 0.85rem; font-weight: 600; }

    .btn-save { display: inline-flex; align-items: center; gap: 8px; padding: 12px 28px; border-radius: 14px; border: none; background: linear-gradient(135deg,#6366f1,#8b5cf6); color: white; font-size: 0.95rem; font-weight: 700; cursor: pointer; transition: all 0.2s; font-family: inherit; margin-top: 1.5rem; }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(99,102,241,0.3); }

    .flash-msg { padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-weight: 600; display: flex; align-items: center; gap: 8px; }
    .flash-msg.success { background: #dcfce7; color: #166534; }

    @media (max-width: 768px) { .perf-page { margin-left: 0; padding: 1rem; } .config-row { grid-template-columns: 1fr; } }
</style>

<div class="perf-page">
    <a href="<?= BASE_URL ?>RecruiterPerformance" class="back-link">
        <span class="material-icons" style="font-size:18px;">arrow_back</span> Kembali ke Dashboard
    </a>

    <div class="settings-header">
        <h1><span class="material-icons" style="color:#f59e0b;">tune</span> Konfigurasi Poin</h1>
    </div>

    <?php if (!empty($flash) && $flash['type'] === 'success'): ?>
    <div class="flash-msg success">
        <span class="material-icons" style="font-size:18px;">check_circle</span>
        <?= htmlspecialchars($flash['message']) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>RecruiterPerformance/settings">
        <div class="config-card">
            <div class="config-header">
                <h2>Poin Per Aksi Rekrutmen</h2>
                <p>Tentukan berapa poin yang diberikan untuk setiap aksi yang dilakukan perekrut</p>
            </div>
            <?php foreach ($configs as $i => $cfg): ?>
            <div class="config-row">
                <div class="config-icon" style="background:<?= htmlspecialchars($cfg['color']) ?>;">
                    <span class="material-icons"><?= htmlspecialchars($cfg['icon']) ?></span>
                </div>
                <div class="config-info">
                    <div class="cfg-label"><?= htmlspecialchars($cfg['label']) ?></div>
                    <div class="cfg-desc"><?= htmlspecialchars($cfg['description']) ?></div>
                    <div style="margin-top:4px;"><span class="config-action-type"><?= htmlspecialchars($cfg['action_type']) ?></span></div>
                </div>
                <div></div>
                <div class="config-input">
                    <input type="hidden" name="action_type[]" value="<?= htmlspecialchars($cfg['action_type']) ?>">
                    <input type="number" name="points[]" value="<?= $cfg['points'] ?>" min="0" max="100">
                    <span>PTS</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="btn-save">
            <span class="material-icons" style="font-size:18px;">save</span> Simpan Konfigurasi
        </button>
    </form>
</div>
