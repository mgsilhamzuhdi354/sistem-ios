<!-- Crewing Pipeline Content with Claim Request -->
<style>
.pipeline-header {
    background: linear-gradient(135deg, #0d9488, #14b8a6);
    color: white;
    padding: 25px 30px;
    border-radius: 16px;
    margin-bottom: 20px;
}
.pipeline-header h2 { margin: 0; font-weight: 600; }

.view-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}
.view-tab {
    padding: 12px 25px;
    background: white;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 500;
    color: #666;
    transition: all 0.3s;
}
.view-tab.active {
    background: linear-gradient(135deg, #0d9488, #14b8a6);
    color: white;
}
.view-tab:hover:not(.active) { background: #f0f0f0; }

.pending-requests-box {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    border-left: 4px solid #f59e0b;
}
.pending-requests-box h5 { margin: 0 0 10px; color: #92400e; }
.pending-item {
    background: white;
    padding: 10px 15px;
    border-radius: 8px;
    margin-top: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.pipeline-board { display: flex; gap: 15px; overflow-x: auto; padding: 10px 0; }
.pipeline-column { min-width: 300px; max-width: 320px; background: #f5f7fa; border-radius: 16px; flex-shrink: 0; }
.column-header { padding: 15px 20px; color: white; display: flex; justify-content: space-between; align-items: center; font-weight: 600; }
.column-header .count { background: rgba(255,255,255,0.3); padding: 4px 12px; border-radius: 15px; }
.column-body { padding: 15px; max-height: 500px; overflow-y: auto; }
.empty-column { text-align: center; padding: 40px 20px; color: #aaa; }
.empty-column i { font-size: 2rem; display: block; margin-bottom: 10px; }

.app-card {
    background: white;
    border-radius: 14px;
    padding: 20px;
    margin-bottom: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    border-left: 4px solid #14b8a6;
    transition: all 0.3s;
}
.app-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
.app-card h4 { margin: 0 0 5px; font-size: 1.1rem; font-weight: 600; color: #1e3a5f; }
.app-card .vacancy { color: #666; font-size: 0.85rem; margin-bottom: 15px; }

.btn-claim {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 10px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.btn-claim:hover { transform: scale(1.02); box-shadow: 0 5px 20px rgba(59,130,246,0.4); }
.btn-claim.pending {
    background: #fbbf24;
    color: #78350f;
    cursor: not-allowed;
}
.btn-claim.pending:hover { transform: none; box-shadow: none; }

/* Modal */
.claim-modal {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.7);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(5px);
}
.claim-modal.show { display: flex; animation: fadeIn 0.3s ease; }
.claim-modal-box {
    background: white;
    border-radius: 20px;
    max-width: 450px;
    width: 95%;
    overflow: hidden;
    animation: scaleIn 0.4s ease;
}
.claim-modal-header {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    padding: 20px 25px;
}
.claim-modal-header h3 { margin: 0; }
.claim-modal-body { padding: 25px; }
.claim-modal-footer { padding: 15px 25px; background: #f8f9fa; display: flex; gap: 12px; justify-content: flex-end; }

.btn-cancel { background: #e5e7eb; border: none; padding: 12px 25px; border-radius: 10px; cursor: pointer; }
.btn-submit { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; border: none; padding: 12px 25px; border-radius: 10px; cursor: pointer; }

@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes scaleIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
</style>

<div class="pipeline-header">
    <h2><i class="fas fa-columns me-2"></i>Pipeline Rekrutmen</h2>
    <small class="opacity-75">Ambil dan kelola lamaran pelamar</small>
</div>

<!-- View Tabs -->
<div class="view-tabs">
    <a href="<?= url('/crewing/pipeline?view=available') ?>" class="view-tab <?= $currentView == 'available' ? 'active' : '' ?>">
        <i class="fas fa-inbox me-2"></i>Tersedia
    </a>
    <a href="<?= url('/crewing/pipeline?view=my') ?>" class="view-tab <?= $currentView == 'my' ? 'active' : '' ?>">
        <i class="fas fa-user-check me-2"></i>Milik Saya
    </a>
</div>

<!-- Pending Requests Alert -->
<?php if (!empty($myPendingRequests)): ?>
<div class="pending-requests-box">
    <h5><i class="fas fa-hourglass-half me-2"></i>Request Pending Anda (<?= count($myPendingRequests) ?>)</h5>
    <small class="text-muted">Menunggu approval dari Master Admin</small>
    <?php foreach ($myPendingRequests as $req): ?>
    <div class="pending-item">
        <div>
            <strong><?= htmlspecialchars($req['applicant_name']) ?></strong>
            <small class="d-block text-muted"><?= htmlspecialchars($req['vacancy_title'] ?? 'N/A') ?></small>
        </div>
        <span style="background:#fef3c7;color:#92400e;padding:4px 10px;border-radius:6px;font-size:0.8rem;">
            <i class="fas fa-clock me-1"></i>Pending
        </span>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Pipeline Board -->
<div class="pipeline-board">
    <?php foreach ($statuses as $status): ?>
    <div class="pipeline-column">
        <div class="column-header" style="background: <?= $status['color'] ?? '#6c757d' ?>;">
            <span><?= htmlspecialchars($status['name']) ?></span>
            <span class="count"><?= count($pipeline[$status['id']] ?? []) ?></span>
        </div>
        <div class="column-body">
            <?php if (empty($pipeline[$status['id']])): ?>
            <div class="empty-column">
                <i class="fas fa-inbox"></i>
                <small>No applications</small>
            </div>
            <?php else: ?>
            <?php foreach ($pipeline[$status['id']] as $app): ?>
            <div class="app-card">
                <h4><?= htmlspecialchars($app['applicant_name']) ?></h4>
                <div class="vacancy"><i class="fas fa-briefcase me-1"></i><?= htmlspecialchars($app['vacancy_title'] ?? 'N/A') ?></div>
                
                <?php if ($currentView == 'available'): ?>
                    <?php if (!empty($app['my_pending_request'])): ?>
                    <button class="btn-claim pending" disabled>
                        <i class="fas fa-hourglass-half"></i>
                        Request Pending...
                    </button>
                    <?php else: ?>
                    <button class="btn-claim" onclick="openClaimModal(<?= $app['id'] ?>, '<?= htmlspecialchars($app['applicant_name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($app['vacancy_title'] ?? '', ENT_QUOTES) ?>')">
                        <i class="fas fa-hand-paper"></i>
                        Request Ambil
                    </button>
                    <?php endif; ?>
                <?php else: ?>
                    <div style="background:#dcfce7;color:#166534;padding:10px;border-radius:8px;text-align:center;">
                        <i class="fas fa-check-circle me-1"></i>Ditangani Anda
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Claim Request Modal -->
<div class="claim-modal" id="claimModal">
    <div class="claim-modal-box">
        <div class="claim-modal-header">
            <h3><i class="fas fa-hand-paper me-2"></i>Request Ambil Lamaran</h3>
        </div>
        <div class="claim-modal-body">
            <div style="background:#f0f4f8;padding:15px;border-radius:12px;margin-bottom:20px;">
                <strong id="claimAppName">-</strong>
                <small id="claimVacancy" class="d-block text-muted">-</small>
            </div>
            
            <p class="text-muted mb-3">Request ini akan dikirim ke Master Admin untuk di-approve. Jika disetujui, Anda akan menjadi handler untuk pelamar ini.</p>
            
            <div>
                <label class="form-label">Alasan (opsional)</label>
                <textarea id="claimReason" class="form-control" rows="2" placeholder="Mengapa Anda ingin mengambil lamaran ini?"></textarea>
            </div>
        </div>
        <div class="claim-modal-footer">
            <input type="hidden" id="claimAppId">
            <button class="btn-cancel" onclick="closeClaimModal()">Batal</button>
            <button class="btn-submit" onclick="submitClaimRequest()">
                <i class="fas fa-paper-plane me-2"></i>Kirim Request
            </button>
        </div>
    </div>
</div>

<!-- Success Popup -->
<div class="claim-modal" id="successPopup">
    <div class="claim-modal-box" style="max-width:400px;text-align:center;padding:40px;">
        <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#22c55e,#16a34a);color:white;display:flex;align-items:center;justify-content:center;font-size:2.5rem;margin:0 auto 20px;">
            <i class="fas fa-check"></i>
        </div>
        <h3 style="color:#1e3a5f;">Request Terkirim!</h3>
        <p class="text-muted">Menunggu approval dari Master Admin</p>
        <button class="btn-submit" onclick="closeSuccessPopup()">OK</button>
    </div>
</div>

<script>
function openClaimModal(appId, appName, vacancy) {
    document.getElementById('claimAppId').value = appId;
    document.getElementById('claimAppName').textContent = appName;
    document.getElementById('claimVacancy').textContent = vacancy || 'Posisi tidak tersedia';
    document.getElementById('claimReason').value = '';
    document.getElementById('claimModal').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeClaimModal() {
    document.getElementById('claimModal').classList.remove('show');
    document.body.style.overflow = '';
}

function closeSuccessPopup() {
    document.getElementById('successPopup').classList.remove('show');
    document.body.style.overflow = '';
    location.reload();
}

function submitClaimRequest() {
    const appId = document.getElementById('claimAppId').value;
    const reason = document.getElementById('claimReason').value;
    
    fetch('<?= url('/crewing/pipeline/request-claim') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `csrf_token=<?= csrf_token() ?>&application_id=${appId}&reason=${encodeURIComponent(reason)}`
    })
    .then(r => r.json())
    .then(data => {
        closeClaimModal();
        if (data.success) {
            document.getElementById('successPopup').classList.add('show');
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(err => {
        alert('Error: ' + err.message);
    });
}
</script>
