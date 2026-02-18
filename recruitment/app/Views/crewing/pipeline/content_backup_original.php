<!-- Crewing Pipeline Content - Enhanced with Detail Modal & Fixed Assign -->
<style>
    .pipeline-header {
        background: linear-gradient(135deg, #0d9488, #14b8a6);
        color: white;
        padding: 25px 30px;
        border-radius: 16px;
        margin-bottom: 20px;
    }
    .pipeline-header h2 { margin: 0; font-weight: 600; }

    .view-tabs { display: flex; gap: 10px; margin-bottom: 20px; }
    .view-tab {
        padding: 12px 25px; background: white; border: none; border-radius: 10px;
        cursor: pointer; font-weight: 500; color: #666; transition: all 0.3s;
        text-decoration: none; font-size: 0.9rem;
    }
    .view-tab.active { background: linear-gradient(135deg, #0d9488, #14b8a6); color: white; }
    .view-tab:hover:not(.active) { background: #f0f0f0; }

    .pending-requests-box {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border-radius: 12px; padding: 20px; margin-bottom: 20px; border-left: 4px solid #f59e0b;
    }
    .pending-requests-box h5 { margin: 0 0 10px; color: #92400e; }
    .pending-item {
        background: white; padding: 10px 15px; border-radius: 8px; margin-top: 10px;
        display: flex; justify-content: space-between; align-items: center;
    }

    .pipeline-board { display: flex; gap: 15px; overflow-x: auto; padding: 10px 0; }
    .pipeline-column { min-width: 300px; max-width: 320px; background: #f5f7fa; border-radius: 16px; flex-shrink: 0; }
    .column-header {
        padding: 15px 20px; color: white; display: flex; justify-content: space-between;
        align-items: center; font-weight: 600; border-radius: 16px 16px 0 0;
    }
    .column-header .count { background: rgba(255,255,255,0.3); padding: 4px 12px; border-radius: 15px; font-size: 0.85rem; }
    .column-body { padding: 15px; max-height: 500px; overflow-y: auto; }
    .empty-column { text-align: center; padding: 40px 20px; color: #aaa; }
    .empty-column i { font-size: 2rem; display: block; margin-bottom: 10px; }

    /* App Card - Clickable */
    .app-card {
        background: white; border-radius: 14px; padding: 18px; margin-bottom: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06); border-left: 4px solid #14b8a6;
        transition: all 0.3s; cursor: pointer;
    }
    .app-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
    .app-card h4 { margin: 0 0 4px; font-size: 1rem; font-weight: 600; color: #1e3a5f; }
    .app-card .vacancy { color: #666; font-size: 0.82rem; margin-bottom: 8px; }
    .app-card .card-meta { display: flex; align-items: center; gap: 8px; font-size: 0.78rem; color: #9ca3af; margin-bottom: 12px; }
    .app-card .card-actions { display: flex; gap: 8px; }

    /* Buttons */
    .btn-claim {
        flex: 1; padding: 10px 14px; border: none; border-radius: 10px;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white;
        font-weight: 600; cursor: pointer; transition: all 0.3s;
        display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 0.85rem;
    }
    .btn-claim:hover { transform: scale(1.02); box-shadow: 0 5px 20px rgba(59,130,246,0.4); }
    .btn-claim.pending { background: #fbbf24; color: #78350f; cursor: not-allowed; }
    .btn-claim.pending:hover { transform: none; box-shadow: none; }
    .btn-claim:disabled { opacity: 0.7; cursor: wait; }
    .btn-detail-sm {
        padding: 10px 14px; border: 2px solid #e5e7eb; border-radius: 10px;
        background: white; color: #374151; cursor: pointer; transition: all 0.3s; font-size: 0.85rem;
    }
    .btn-detail-sm:hover { border-color: #3b82f6; color: #3b82f6; background: #eff6ff; }
    .btn-status-change {
        flex: 1; padding: 10px 14px; border: none; border-radius: 10px;
        background: linear-gradient(135deg, #4f46e5, #4338ca); color: white;
        font-weight: 600; cursor: pointer; transition: all 0.3s;
        display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 0.85rem;
    }
    .btn-status-change:hover { transform: scale(1.02); box-shadow: 0 5px 20px rgba(79,70,229,0.4); }
    .assigned-badge {
        background: #dcfce7; color: #166534; padding: 6px 10px; border-radius: 8px;
        text-align: center; margin-bottom: 8px; font-size: 0.82rem; font-weight: 500;
    }

    /* Generic Modal */
    .p-modal {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.65); display: none; align-items: center; justify-content: center;
        z-index: 9999; backdrop-filter: blur(5px);
    }
    .p-modal.show { display: flex; animation: pfadeIn 0.3s ease; }
    .p-modal-box {
        background: white; border-radius: 20px; max-width: 480px; width: 95%;
        overflow: hidden; animation: pscaleIn 0.4s ease; max-height: 90vh; display: flex; flex-direction: column;
    }
    .p-modal-header { padding: 20px 25px; color: white; }
    .p-modal-header h3 { margin: 0; font-size: 1.1rem; }
    .p-modal-body { padding: 25px; overflow-y: auto; flex: 1; }
    .p-modal-footer {
        padding: 15px 25px; background: #f8f9fa; display: flex; gap: 10px; justify-content: flex-end;
        border-top: 1px solid #f3f4f6;
    }
    .btn-modal-cancel { background: #e5e7eb; border: none; padding: 10px 22px; border-radius: 10px; cursor: pointer; font-weight: 500; }
    .btn-modal-submit {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white;
        border: none; padding: 10px 22px; border-radius: 10px; cursor: pointer; font-weight: 600;
    }
    .btn-modal-submit:disabled { opacity: 0.7; cursor: wait; }
    @keyframes pfadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes pscaleIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }

    /* Detail Modal - Wide */
    #detailModal .p-modal-box { max-width: 600px; }
    .detail-loading { text-align: center; padding: 40px; color: #9ca3af; }
    .detail-loading i { font-size: 2rem; margin-bottom: 10px; display: block; }
    .detail-profile { display: flex; align-items: center; gap: 18px; margin-bottom: 20px; }
    .detail-avatar {
        width: 70px; height: 70px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; font-weight: 700;
        flex-shrink: 0;
    }
    .detail-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
    .detail-name { font-size: 1.2rem; font-weight: 700; color: #1e3a5f; margin: 0 0 4px; }
    .detail-vacancy { color: #6b7280; font-size: 0.88rem; }
    .detail-status-badge {
        display: inline-block; padding: 4px 12px; border-radius: 20px; color: white;
        font-size: 0.78rem; font-weight: 600; margin-top: 6px;
    }
    .detail-section { margin-bottom: 16px; }
    .detail-section-title {
        font-size: 0.82rem; font-weight: 700; color: #3b82f6; text-transform: uppercase;
        letter-spacing: 0.5px; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 2px solid #eff6ff;
    }
    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .detail-item label { display: block; font-size: 0.75rem; color: #9ca3af; margin-bottom: 2px; }
    .detail-item span { font-size: 0.88rem; color: #1f2937; font-weight: 500; }
    .detail-docs { display: flex; flex-wrap: wrap; gap: 6px; }
    .detail-doc-badge {
        background: #eff6ff; color: #1d4ed8; padding: 4px 10px; border-radius: 6px;
        font-size: 0.78rem; font-weight: 500;
    }

    /* Toast */
    /* Toast - appended to body via JS */

    @media (max-width: 768px) {
        .pipeline-board { flex-direction: column; }
        .pipeline-column { min-width: 100%; max-width: 100%; }
        .detail-grid { grid-template-columns: 1fr; }
        #detailModal .p-modal-box { max-width: 95%; }
    }
</style>

<!-- Toast is injected into body via JS -->

<div class="pipeline-header">
    <h2><i class="fas fa-columns me-2"></i>Pipeline Rekrutmen</h2>
    <small class="opacity-75">Klik kartu untuk lihat detail, ambil dan kelola lamaran pelamar</small>
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
        <div class="pipeline-column" data-status-id="<?= $status['id'] ?>">
            <div class="column-header" style="background: <?= $status['color'] ?? '#6c757d' ?>;">
                <span><?= htmlspecialchars($status['name']) ?></span>
                <span class="count" data-count-for="<?= $status['id'] ?>"><?= count($pipeline[$status['id']] ?? []) ?></span>
            </div>
            <div class="column-body" id="columnBody-<?= $status['id'] ?>">
                <?php if (empty($pipeline[$status['id']])): ?>
                    <div class="empty-column">
                        <i class="fas fa-inbox"></i>
                        <small>No applications</small>
                    </div>
                <?php else: ?>
                    <?php foreach ($pipeline[$status['id']] as $app): ?>
                        <div class="app-card" id="appCard-<?= $app['id'] ?>" data-app-id="<?= $app['id'] ?>">
                            <div onclick="showDetail(<?= $app['id'] ?>)">
                                <h4><?= htmlspecialchars($app['applicant_name']) ?></h4>
                                <div class="vacancy"><i class="fas fa-briefcase me-1"></i><?= htmlspecialchars($app['vacancy_title'] ?? 'N/A') ?></div>
                                <div class="card-meta">
                                    <span><i class="fas fa-envelope me-1"></i><?= htmlspecialchars($app['applicant_email'] ?? '') ?></span>
                                </div>
                                <?php if (!empty($app['medical_email_sent_at'])): ?>
                                <div style="margin-bottom:8px;">
                                    <span style="display:inline-flex;align-items:center;gap:5px;background:#dcfce7;color:#166534;padding:4px 10px;border-radius:8px;font-size:0.78rem;font-weight:600;">
                                        <i class="fas fa-heartbeat"></i> MCU Email Terkirim
                                        <span style="font-weight:400;opacity:0.7;"><?= date('d/m/Y', strtotime($app['medical_email_sent_at'])) ?></span>
                                    </span>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($app['sent_to_erp_at'])): ?>
                                <div style="margin-bottom:8px;">
                                    <span style="display:inline-flex;align-items:center;gap:5px;background:#dcfce7;color:#166534;padding:4px 10px;border-radius:8px;font-size:0.78rem;font-weight:600;">
                                        <i class="fas fa-check-circle"></i> Sent to ERP
                                        <span style="font-weight:400;opacity:0.7;"><?= date('d/m/Y', strtotime($app['sent_to_erp_at'])) ?> | ID: <?= $app['erp_crew_id'] ?></span>
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="card-actions">
                                <?php if ($currentView == 'available'): ?>
                                    <?php if (!empty($app['my_pending_request'])): ?>
                                        <button class="btn-claim pending" disabled style="flex:1;">
                                            <i class="fas fa-hourglass-half"></i> Request Pending...
                                        </button>
                                    <?php else: ?>
                                        <button class="btn-detail-sm" onclick="event.stopPropagation(); showDetail(<?= $app['id'] ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-claim" id="claimBtn-<?= $app['id'] ?>"
                                            onclick="event.stopPropagation(); openClaimModal(<?= $app['id'] ?>, '<?= htmlspecialchars($app['applicant_name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($app['vacancy_title'] ?? '', ENT_QUOTES) ?>')">
                                            <i class="fas fa-hand-paper"></i> Request Ambil
                                        </button>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div style="width:100%;">
                                        <div class="assigned-badge"><i class="fas fa-check-circle me-1"></i>Ditangani Anda</div>
                                        <div style="display:flex;gap:8px;">
                                            <button class="btn-detail-sm" onclick="event.stopPropagation(); showDetail(<?= $app['id'] ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn-status-change" style="flex:1;"
                                                onclick="event.stopPropagation(); openStatusModal(<?= $app['id'] ?>, '<?= htmlspecialchars($app['applicant_name'], ENT_QUOTES) ?>', <?= $app['status_id'] ?>)">
                                                <i class="fas fa-exchange-alt"></i> Ubah Status
                                            </button>
                                            <?php if ($app['status_id'] == 6 && empty($app['sent_to_erp_at'])): ?>
                                            <button class="btn-erp" style="flex:1;"
                                                onclick="event.stopPropagation(); openErpModal(<?= $app['id'] ?>, '<?= htmlspecialchars($app['applicant_name'], ENT_QUOTES) ?>')">
                                                <i class="fas fa-paper-plane"></i> Send to ERP
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- ============ DETAIL MODAL ============ -->
<div class="p-modal" id="detailModal">
    <div class="p-modal-box">
        <div class="p-modal-header" style="background:linear-gradient(135deg, #1e3a5f, #3b82f6);">
            <h3><i class="fas fa-id-card me-2"></i>Detail Pelamar</h3>
        </div>
        <div class="p-modal-body" id="detailBody">
            <div class="detail-loading">
                <i class="fas fa-spinner fa-spin"></i>
                <span>Loading...</span>
            </div>
        </div>
        <div class="p-modal-footer">
            <button class="btn-modal-cancel" onclick="closeModal('detailModal')">Tutup</button>
        </div>
    </div>
</div>

<!-- ============ CLAIM MODAL ============ -->
<div class="p-modal" id="claimModal">
    <div class="p-modal-box">
        <div class="p-modal-header" style="background:linear-gradient(135deg, #3b82f6, #1d4ed8);">
            <h3><i class="fas fa-hand-paper me-2"></i>Request Ambil Lamaran</h3>
        </div>
        <div class="p-modal-body">
            <div style="background:#f0f4f8;padding:15px;border-radius:12px;margin-bottom:20px;">
                <strong id="claimAppName">-</strong>
                <small id="claimVacancy" class="d-block text-muted">-</small>
            </div>
            <p class="text-muted" style="font-size:0.88rem;margin-bottom:15px;">
                Request ini akan dikirim ke Master Admin untuk di-approve. Jika disetujui, Anda akan menjadi handler pelamar ini.
            </p>
            <div>
                <label style="font-weight:600;font-size:0.85rem;margin-bottom:6px;display:block;">Alasan (opsional)</label>
                <textarea id="claimReason" style="width:100%;padding:10px;border-radius:10px;border:2px solid #e5e7eb;font-family:inherit;resize:vertical;" rows="2"
                    placeholder="Mengapa Anda ingin mengambil lamaran ini?"></textarea>
            </div>
        </div>
        <div class="p-modal-footer">
            <input type="hidden" id="claimAppId">
            <button class="btn-modal-cancel" onclick="closeModal('claimModal')">Batal</button>
            <button class="btn-modal-submit" id="claimSubmitBtn" onclick="submitClaimRequest()">
                <i class="fas fa-paper-plane me-1"></i>Kirim Request
            </button>
        </div>
    </div>
</div>

<!-- ============ STATUS CHANGE MODAL ============ -->
<div class="p-modal" id="statusModal">
    <div class="p-modal-box">
        <div class="p-modal-header" style="background:linear-gradient(135deg, #4f46e5, #4338ca);">
            <h3><i class="fas fa-exchange-alt me-2"></i>Request Ganti Status</h3>
        </div>
        <div class="p-modal-body">
            <div style="background:#f0f4f8;padding:15px;border-radius:12px;margin-bottom:20px;">
                <strong id="statusAppName">-</strong>
            </div>
            <p class="text-muted" style="font-size:0.88rem;margin-bottom:15px;">Pilih status baru. Request akan dikirim ke Master Admin.</p>
            <div>
                <label style="font-weight:600;font-size:0.85rem;margin-bottom:6px;display:block;">Status Baru</label>
                <select id="statusSelect" style="width:100%;padding:10px;border-radius:10px;border:2px solid #e5e7eb;font-family:inherit;">
                    <?php foreach ($statuses as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="p-modal-footer">
            <input type="hidden" id="statusAppId">
            <button class="btn-modal-cancel" onclick="closeModal('statusModal')">Batal</button>
            <button class="btn-modal-submit" id="statusSubmitBtn" style="background:linear-gradient(135deg, #4f46e5, #4338ca);" onclick="submitStatusRequest()">
                <i class="fas fa-paper-plane me-1"></i>Kirim Request
            </button>
        </div>
    </div>
</div>

<script>
// ===== TOAST SYSTEM =====
(function() {
    // Create toast container on body level to avoid overflow issues
    var tc = document.createElement('div');
    tc.id = 'pipelineToastContainer';
    tc.style.cssText = 'position:fixed;top:20px;right:20px;z-index:999999;display:flex;flex-direction:column;gap:10px;pointer-events:none;';
    document.body.appendChild(tc);
})();
function showToast(message, type) {
    type = type || 'success';
    var container = document.getElementById('pipelineToastContainer');
    if (!container) { alert(message); return; }
    var toast = document.createElement('div');
    var colors = { success: '#16a34a', error: '#dc2626', warning: '#d97706' };
    var icons = { success: 'check-circle', error: 'times-circle', warning: 'exclamation-triangle' };
    toast.style.cssText = 'padding:16px 22px;border-radius:12px;color:white;font-weight:600;font-size:0.95rem;' +
        'box-shadow:0 10px 30px rgba(0,0,0,0.25);display:flex;align-items:center;gap:10px;min-width:300px;' +
        'pointer-events:auto;background:' + (colors[type] || '#3b82f6') + ';' +
        'border-left:5px solid rgba(255,255,255,0.4);animation:pToastIn 0.4s ease;';
    toast.innerHTML = '<i class="fas fa-' + (icons[type] || 'info-circle') + '" style="font-size:1.2rem;"></i><span>' + message + '</span>';
    container.appendChild(toast);
    setTimeout(function() {
        toast.style.transition = 'all 0.4s ease';
        toast.style.transform = 'translateX(120%)';
        toast.style.opacity = '0';
        setTimeout(function() { toast.remove(); }, 400);
    }, 4000);
}
// Inject toast animation
var toastStyle = document.createElement('style');
toastStyle.textContent = '@keyframes pToastIn{from{transform:translateX(120%);opacity:0}to{transform:translateX(0);opacity:1}}';
document.head.appendChild(toastStyle);

// ===== MODAL HELPERS =====
function openModal(id) {
    document.getElementById(id).classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.remove('show');
    document.body.style.overflow = '';
}
// Close on backdrop click
document.querySelectorAll('.p-modal').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) closeModal(m.id); });
});

// ===== DETAIL MODAL =====
function showDetail(appId) {
    const body = document.getElementById('detailBody');
    body.innerHTML = '<div class="detail-loading"><i class="fas fa-spinner fa-spin"></i><span>Loading...</span></div>';
    openModal('detailModal');

    fetch(`<?= url('/crewing/pipeline/detail') ?>?application_id=${appId}`)
        .then(r => r.json())
        .then(res => {
            if (!res.success) { body.innerHTML = `<p style="color:#ef4444;">${res.message}</p>`; return; }
            const d = res.data;
            const initials = (d.full_name || 'U').split(' ').map(n=>n[0]).join('').substring(0,2).toUpperCase();
            const avatarHtml = d.avatar
                ? `<img src="<?= url('/') ?>${d.avatar}" alt="">`
                : initials;
            const age = d.date_of_birth ? Math.floor((Date.now() - new Date(d.date_of_birth).getTime()) / 31557600000) + ' thn' : '-';

            let docsHtml = '<span style="color:#9ca3af;font-size:0.82rem;">Belum ada dokumen</span>';
            if (d.documents && d.documents.length > 0) {
                docsHtml = d.documents.map(doc =>
                    `<span class="detail-doc-badge"><i class="fas fa-file-alt me-1"></i>${doc.type_name}${doc.document_number ? ' (#'+doc.document_number+')' : ''}</span>`
                ).join('');
            }

            body.innerHTML = `
                <div class="detail-profile">
                    <div class="detail-avatar">${avatarHtml}</div>
                    <div>
                        <div class="detail-name">${d.full_name || '-'}</div>
                        <div class="detail-vacancy"><i class="fas fa-briefcase me-1"></i>${d.vacancy_title || 'N/A'}</div>
                        <span class="detail-status-badge" style="background:${d.status_color || '#6c757d'}">${d.status_name || '-'}</span>
                    </div>
                </div>

                <div class="detail-section">
                    <div class="detail-section-title"><i class="fas fa-user me-1"></i>Informasi Pribadi</div>
                    <div class="detail-grid">
                        <div class="detail-item"><label>Email</label><span>${d.email || '-'}</span></div>
                        <div class="detail-item"><label>Telepon</label><span>${d.phone || '-'}</span></div>
                        <div class="detail-item"><label>Jenis Kelamin</label><span>${d.gender || '-'}</span></div>
                        <div class="detail-item"><label>Usia</label><span>${age}</span></div>
                        <div class="detail-item"><label>Tempat Lahir</label><span>${d.place_of_birth || '-'}</span></div>
                        <div class="detail-item"><label>Kewarganegaraan</label><span>${d.nationality || '-'}</span></div>
                        <div class="detail-item"><label>Gol. Darah</label><span>${d.blood_type || '-'}</span></div>
                        <div class="detail-item"><label>Alamat</label><span>${d.address || '-'}${d.city ? ', '+d.city : ''}</span></div>
                    </div>
                </div>

                <div class="detail-section">
                    <div class="detail-section-title"><i class="fas fa-ruler-vertical me-1"></i>Data Fisik</div>
                    <div class="detail-grid">
                        <div class="detail-item"><label>Tinggi</label><span>${d.height_cm ? d.height_cm+' cm' : '-'}</span></div>
                        <div class="detail-item"><label>Berat</label><span>${d.weight_kg ? d.weight_kg+' kg' : '-'}</span></div>
                    </div>
                </div>

                <div class="detail-section">
                    <div class="detail-section-title"><i class="fas fa-ship me-1"></i>Pengalaman Laut</div>
                    <div class="detail-grid">
                        <div class="detail-item"><label>Total (bulan)</label><span>${d.total_sea_service_months || '-'}</span></div>
                        <div class="detail-item"><label>Rank Terakhir</label><span>${d.last_rank || '-'}</span></div>
                        <div class="detail-item"><label>Kapal Terakhir</label><span>${d.last_vessel_name || '-'}</span></div>
                        <div class="detail-item"><label>Tipe Kapal</label><span>${d.last_vessel_type || '-'}</span></div>
                    </div>
                </div>

                <div class="detail-section">
                    <div class="detail-section-title"><i class="fas fa-id-card me-1"></i>Dokumen (${d.documents ? d.documents.length : 0})</div>
                    <div class="detail-docs">${docsHtml}</div>
                </div>

                <div class="detail-section">
                    <div class="detail-section-title"><i class="fas fa-phone-alt me-1"></i>Kontak Darurat</div>
                    <div class="detail-grid">
                        <div class="detail-item"><label>Nama</label><span>${d.emergency_name || '-'}</span></div>
                        <div class="detail-item"><label>Telepon</label><span>${d.emergency_phone || '-'}</span></div>
                        <div class="detail-item"><label>Hubungan</label><span>${d.emergency_relation || '-'}</span></div>
                    </div>
                </div>

                ${d.crewing_name ? `
                <div class="detail-section">
                    <div class="detail-section-title"><i class="fas fa-user-tie me-1"></i>Assignment</div>
                    <div class="detail-grid">
                        <div class="detail-item"><label>Handler</label><span>${d.crewing_name}</span></div>
                        <div class="detail-item"><label>Ditugaskan</label><span>${d.assigned_at ? new Date(d.assigned_at).toLocaleDateString('id-ID') : '-'}</span></div>
                    </div>
                </div>` : ''}
            `;
        })
        .catch(err => {
            body.innerHTML = `<p style="color:#ef4444;">Gagal memuat data: ${err.message}</p>`;
        });
}

// ===== CLAIM (ASSIGN) REQUEST =====
function openClaimModal(appId, appName, vacancy) {
    document.getElementById('claimAppId').value = appId;
    document.getElementById('claimAppName').textContent = appName;
    document.getElementById('claimVacancy').textContent = vacancy || 'Posisi tidak tersedia';
    document.getElementById('claimReason').value = '';
    document.getElementById('claimSubmitBtn').disabled = false;
    document.getElementById('claimSubmitBtn').innerHTML = '<i class="fas fa-paper-plane me-1"></i>Kirim Request';
    openModal('claimModal');
}

function submitClaimRequest() {
    const appId = document.getElementById('claimAppId').value;
    const reason = document.getElementById('claimReason').value;
    const btn = document.getElementById('claimSubmitBtn');

    // Loading state
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Mengirim...';

    fetch('<?= url('/crewing/pipeline/request-claim') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `csrf_token=<?= csrf_token() ?>&application_id=${appId}&reason=${encodeURIComponent(reason)}`
    })
    .then(r => r.json())
    .then(data => {
        closeModal('claimModal');
        if (data.success) {
            showToast('Request berhasil dikirim! Menunggu approval Master Admin.', 'success');
            // Update the card in place — change button to "Pending"
            const card = document.getElementById('appCard-' + appId);
            if (card) {
                const actionsDiv = card.querySelector('.card-actions');
                if (actionsDiv) {
                    actionsDiv.innerHTML = `
                        <button class="btn-claim pending" disabled style="flex:1;">
                            <i class="fas fa-hourglass-half"></i> Request Pending...
                        </button>
                    `;
                }
            }
        } else {
            showToast(data.message || 'Gagal mengirim request', 'error');
        }
    })
    .catch(err => {
        closeModal('claimModal');
        showToast('Error: ' + err.message, 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Kirim Request';
    });
}

// ===== STATUS CHANGE REQUEST =====
function openStatusModal(appId, appName, currentStatusId) {
    document.getElementById('statusAppId').value = appId;
    document.getElementById('statusAppName').textContent = appName;
    document.getElementById('statusSubmitBtn').disabled = false;
    document.getElementById('statusSubmitBtn').innerHTML = '<i class="fas fa-paper-plane me-1"></i>Kirim Request';

    const select = document.getElementById('statusSelect');
    for (let i = 0; i < select.options.length; i++) {
        select.options[i].disabled = (select.options[i].value == currentStatusId);
        if (select.options[i].value != currentStatusId && !select.value) {
            select.value = select.options[i].value;
        }
    }
    // Set to first non-disabled option
    for (let i = 0; i < select.options.length; i++) {
        if (!select.options[i].disabled) { select.value = select.options[i].value; break; }
    }

    openModal('statusModal');
}

function submitStatusRequest() {
    const appId = document.getElementById('statusAppId').value;
    const statusId = document.getElementById('statusSelect').value;
    const btn = document.getElementById('statusSubmitBtn');

    // Loading state
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Mengirim...';

    fetch('<?= url('/crewing/pipeline/request-status-change') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `csrf_token=<?= csrf_token() ?>&application_id=${appId}&status_id=${statusId}`
    })
    .then(r => r.json())
    .then(data => {
        closeModal('statusModal');
        if (data.success) {
            showToast('Permintaan pindah status berhasil dikirim!', 'success');
        } else {
            showToast(data.message || 'Gagal mengirim request', 'error');
        }
    })
    .catch(err => {
        closeModal('statusModal');
        showToast('Error: ' + err.message, 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Kirim Request';
    });
}
</script>

<?php include __DIR__ . '/erp_modal.php'; ?>
