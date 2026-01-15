<style>
.requests-header {
    background: linear-gradient(135deg, #1e3a5f 0%, #0d1f33 100%);
    color: white;
    padding: 30px;
    border-radius: 16px;
    margin-bottom: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.requests-header h2 { margin: 0; font-weight: 600; }
.pending-badge {
    background: #dc2626;
    color: white;
    padding: 8px 20px;
    border-radius: 30px;
    font-size: 1.1rem;
    font-weight: 600;
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.section-title {
    margin: 30px 0 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e5e7eb;
}
.section-title i { margin-right: 10px; }

.request-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}
.request-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}
.request-card.claim { border-left: 5px solid #3b82f6; }
.request-card.status { border-left: 5px solid #f59e0b; }

.request-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}
.applicant-info h4 { margin: 0 0 5px; font-weight: 600; color: #1e3a5f; }
.applicant-info p { margin: 0; color: #666; }

.requester-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #e0f2fe, #bae6fd);
    padding: 8px 15px;
    border-radius: 20px;
    margin: 15px 0;
}
.requester-badge i { color: #0369a1; }
.requester-badge strong { color: #0c4a6e; }

.reason-box {
    background: #fffbeb;
    padding: 12px 16px;
    border-radius: 10px;
    margin-bottom: 15px;
    border-left: 3px solid #f59e0b;
}

.status-change-display {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
    margin-bottom: 15px;
}
.status-badge {
    padding: 8px 16px;
    border-radius: 20px;
    color: white;
    font-weight: 600;
}

.action-buttons {
    display: flex;
    gap: 12px;
}
.btn-approve {
    flex: 1;
    background: linear-gradient(135deg, #22c55e, #16a34a);
    border: none;
    color: white;
    padding: 12px 25px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}
.btn-approve:hover { transform: scale(1.02); box-shadow: 0 5px 20px rgba(34, 197, 94, 0.4); }
.btn-reject {
    flex: 1;
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    border: none;
    color: white;
    padding: 12px 25px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}
.btn-reject:hover { transform: scale(1.02); box-shadow: 0 5px 20px rgba(220, 38, 38, 0.4); }

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 16px;
}
.empty-state i { font-size: 4rem; color: #22c55e; margin-bottom: 20px; }

/* Popup */
.approval-popup {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.7);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(5px);
}
.approval-popup.show { display: flex; animation: fadeIn 0.3s ease; }
.popup-content {
    background: white;
    padding: 30px;
    border-radius: 20px;
    max-width: 450px;
    width: 90%;
    animation: scaleIn 0.4s ease;
}
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes scaleIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
</style>

<div class="requests-header">
    <div>
        <h2><i class="fas fa-clipboard-check me-3"></i>Approval Requests</h2>
        <p class="mb-0 opacity-75">Review dan approve permintaan dari staff</p>
    </div>
    <?php if ($pendingCount > 0): ?>
    <div class="pending-badge">
        <i class="fas fa-bell me-2"></i><?= $pendingCount ?> Pending
    </div>
    <?php endif; ?>
</div>

<?php if (empty($pendingClaims) && empty($pendingStatus)): ?>
<div class="empty-state">
    <i class="fas fa-check-circle"></i>
    <h3>Semua Beres!</h3>
    <p class="text-muted">Tidak ada request pending saat ini</p>
</div>
<?php endif; ?>

<!-- Job Claim Requests -->
<?php if (!empty($pendingClaims)): ?>
<h4 class="section-title"><i class="fas fa-hand-paper text-primary"></i>Request Ambil Lamaran (<?= count($pendingClaims) ?>)</h4>

<?php foreach ($pendingClaims as $req): ?>
<div class="request-card claim" id="claim-<?= $req['request_id'] ?>">
    <div class="request-header">
        <div class="applicant-info">
            <h4><?= htmlspecialchars($req['applicant_name']) ?></h4>
            <p><i class="fas fa-briefcase me-1"></i><?= htmlspecialchars($req['vacancy_title'] ?? 'N/A') ?></p>
        </div>
        <small class="text-muted">
            <i class="fas fa-clock me-1"></i><?= date('d M Y H:i', strtotime($req['created_at'])) ?>
        </small>
    </div>
    
    <div class="requester-badge">
        <i class="fas fa-user-circle"></i>
        <span>Diminta oleh: <strong><?= htmlspecialchars($req['requester_name']) ?></strong></span>
    </div>
    
    <?php if (!empty($req['reason'])): ?>
    <div class="reason-box">
        <small class="text-muted d-block mb-1"><i class="fas fa-comment me-1"></i>Alasan:</small>
        <?= htmlspecialchars($req['reason']) ?>
    </div>
    <?php endif; ?>
    
    <div class="action-buttons">
        <button class="btn-approve" onclick="processRequest('claim', <?= $req['request_id'] ?>, 'approve', '<?= htmlspecialchars($req['requester_name'], ENT_QUOTES) ?>')">
            <i class="fas fa-check me-2"></i>Approve
        </button>
        <button class="btn-reject" onclick="processRequest('claim', <?= $req['request_id'] ?>, 'reject', '<?= htmlspecialchars($req['requester_name'], ENT_QUOTES) ?>')">
            <i class="fas fa-times me-2"></i>Reject
        </button>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

<!-- Status Change Requests -->
<?php if (!empty($pendingStatus)): ?>
<h4 class="section-title"><i class="fas fa-exchange-alt text-warning"></i>Request Ubah Status (<?= count($pendingStatus) ?>)</h4>

<?php foreach ($pendingStatus as $req): ?>
<div class="request-card status" id="status-<?= $req['request_id'] ?>">
    <div class="request-header">
        <div class="applicant-info">
            <h4><?= htmlspecialchars($req['applicant_name']) ?></h4>
            <p><i class="fas fa-briefcase me-1"></i><?= htmlspecialchars($req['vacancy_title'] ?? 'N/A') ?></p>
        </div>
        <small class="text-muted">
            <i class="fas fa-clock me-1"></i><?= date('d M Y H:i', strtotime($req['created_at'])) ?>
        </small>
    </div>
    
    <div class="status-change-display">
        <span class="status-badge" style="background: <?= $req['from_status_color'] ?>">
            <?= htmlspecialchars($req['from_status_name']) ?>
        </span>
        <i class="fas fa-arrow-right text-muted"></i>
        <span class="status-badge" style="background: <?= $req['to_status_color'] ?>">
            <?= htmlspecialchars($req['to_status_name']) ?>
        </span>
    </div>
    
    <div class="requester-badge">
        <i class="fas fa-user-circle"></i>
        <span>Diminta oleh: <strong><?= htmlspecialchars($req['requester_name']) ?></strong></span>
    </div>
    
    <div class="action-buttons">
        <button class="btn-approve" onclick="processRequest('status', <?= $req['request_id'] ?>, 'approve', '')">
            <i class="fas fa-check me-2"></i>Approve
        </button>
        <button class="btn-reject" onclick="processRequest('status', <?= $req['request_id'] ?>, 'reject', '')">
            <i class="fas fa-times me-2"></i>Reject
        </button>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

<!-- Confirmation Popup -->
<div class="approval-popup" id="confirmPopup">
    <div class="popup-content">
        <div id="popupIcon" style="width:70px;height:70px;border-radius:50%;margin:0 auto 20px;display:flex;align-items:center;justify-content:center;font-size:2rem;"></div>
        <h4 id="popupTitle" class="text-center mb-3"></h4>
        <p id="popupMessage" class="text-center text-muted mb-4"></p>
        <div class="mb-3">
            <label class="form-label">Catatan (opsional)</label>
            <textarea id="popupNotes" class="form-control" rows="2" placeholder="Tambahkan catatan..."></textarea>
        </div>
        <input type="hidden" id="popupType">
        <input type="hidden" id="popupId">
        <input type="hidden" id="popupAction">
        <div class="d-flex gap-3">
            <button class="btn btn-secondary flex-fill" onclick="closePopup()">Batal</button>
            <button class="btn flex-fill" id="popupConfirmBtn" onclick="confirmAction()">Konfirmasi</button>
        </div>
    </div>
</div>

<script>
function processRequest(type, id, action, requesterName) {
    const isApprove = action === 'approve';
    const icon = document.getElementById('popupIcon');
    const title = document.getElementById('popupTitle');
    const message = document.getElementById('popupMessage');
    const confirmBtn = document.getElementById('popupConfirmBtn');
    
    document.getElementById('popupType').value = type;
    document.getElementById('popupId').value = id;
    document.getElementById('popupAction').value = action;
    document.getElementById('popupNotes').value = '';
    
    if (isApprove) {
        icon.style.background = 'linear-gradient(135deg, #22c55e, #16a34a)';
        icon.style.color = 'white';
        icon.innerHTML = '<i class="fas fa-check"></i>';
        title.textContent = 'Approve Request?';
        message.textContent = type === 'claim' ? `${requesterName} akan di-assign ke lamaran ini.` : 'Status akan diubah sesuai permintaan.';
        confirmBtn.style.background = 'linear-gradient(135deg, #22c55e, #16a34a)';
        confirmBtn.style.color = 'white';
        confirmBtn.style.border = 'none';
    } else {
        icon.style.background = 'linear-gradient(135deg, #dc2626, #b91c1c)';
        icon.style.color = 'white';
        icon.innerHTML = '<i class="fas fa-times"></i>';
        title.textContent = 'Reject Request?';
        message.textContent = 'Request ini akan ditolak.';
        confirmBtn.style.background = 'linear-gradient(135deg, #dc2626, #b91c1c)';
        confirmBtn.style.color = 'white';
        confirmBtn.style.border = 'none';
    }
    
    document.getElementById('confirmPopup').classList.add('show');
}

function closePopup() {
    document.getElementById('confirmPopup').classList.remove('show');
}

function confirmAction() {
    const type = document.getElementById('popupType').value;
    const id = document.getElementById('popupId').value;
    const action = document.getElementById('popupAction').value;
    const notes = document.getElementById('popupNotes').value;
    
    let url;
    if (type === 'claim') {
        url = action === 'approve' 
            ? '<?= url('/master-admin/requests/approve-claim/') ?>' + id
            : '<?= url('/master-admin/requests/reject-claim/') ?>' + id;
    } else {
        url = action === 'approve'
            ? '<?= url('/master-admin/requests/approve/') ?>' + id
            : '<?= url('/master-admin/requests/reject/') ?>' + id;
    }
    
    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'csrf_token=<?= csrf_token() ?>&notes=' + encodeURIComponent(notes)
    })
    .then(r => r.json())
    .then(data => {
        closePopup();
        if (data.success) {
            document.getElementById(type + '-' + id).style.display = 'none';
            alert('✅ ' + data.message);
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(err => {
        alert('Error: ' + err.message);
    });
}
</script>
