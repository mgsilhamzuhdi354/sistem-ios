<!-- ============ CARD ACTION POPUP ============ -->
<div class="card-action-overlay" id="cardActionOverlay" onclick="if(event.target===this)closeCardActionPopup()">
    <div class="card-action-popup">
        <div class="card-action-popup-header">
            <div class="card-action-popup-avatar" id="cardActionAvatar"></div>
            <div class="card-action-popup-info">
                <h4 id="cardActionName">-</h4>
                <p id="cardActionVacancy">-</p>
            </div>
        </div>
        <div class="card-action-popup-body">
            <div class="action-label">Pilih Aksi</div>
            
            <button class="card-action-btn btn-action-view" onclick="closeCardActionPopup(); showDetail(window._cardActionAppId)">
                <div class="action-icon"><i class="fas fa-eye"></i></div>
                <div class="action-text">
                    <h5>Lihat Detail</h5>
                    <p>Lihat profil lengkap kandidat</p>
                </div>
            </button>
            
            <button class="card-action-btn btn-action-status" onclick="closeCardActionPopup(); openStatusModal(window._cardActionAppId, document.getElementById('cardActionName').textContent, window._cardActionStatusId)">
                <div class="action-icon"><i class="fas fa-exchange-alt"></i></div>
                <div class="action-text">
                    <h5>Ubah Status</h5>
                    <p>Pindahkan ke tahap berikutnya</p>
                </div>
            </button>
            
            <button class="card-action-btn btn-action-erp" id="cardActionErpBtn" style="display:none" onclick="closeCardActionPopup(); openErpModal(window._cardActionAppId, document.getElementById('cardActionName').textContent)">
                <div class="action-icon" style="background:#f3e8ff;color:#7c3aed"><i class="fas fa-paper-plane"></i></div>
                <div class="action-text">
                    <h5>Kirim ke ERP</h5>
                    <p>Sinkronkan data ke sistem ERP</p>
                </div>
            </button>
            
            <button class="card-action-btn btn-action-archive" onclick="closeCardActionPopup(); archiveApplication(window._cardActionAppId)">
                <div class="action-icon"><i class="fas fa-archive"></i></div>
                <div class="action-text">
                    <h5>Pindahkan ke Arsip</h5>
                    <p>Arsipkan kandidat ini</p>
                </div>
            </button>
        </div>
        <button class="card-action-close" onclick="closeCardActionPopup()">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>
</div>

<!-- ============ CONFIRM MODAL (replace native confirm) ============ -->
<div class="p-modal" id="confirmModal" style="z-index:99999">
    <div class="p-modal-box" style="max-width:420px">
        <div class="p-modal-header" id="confirmModalHeader" style="background:linear-gradient(135deg,#1e293b,#334155)">
            <h3 id="confirmModalTitle"><i class="fas fa-question-circle me-2"></i>Konfirmasi</h3>
        </div>
        <div class="p-modal-body">
            <p id="confirmModalMessage" style="font-size:0.95rem;color:#374151;margin:0;"></p>
        </div>
        <div class="p-modal-footer">
            <button class="btn-modal-cancel" onclick="closeModal('confirmModal');if(window._confirmReject)window._confirmReject();">Batal</button>
            <button id="confirmModalOkBtn" class="btn-modal-submit" onclick="closeModal('confirmModal');if(window._confirmResolve)window._confirmResolve();">Ya, Lanjutkan</button>
        </div>
    </div>
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

                ${d.sent_to_erp_at ? (function() {
                    const cl = d.erp_checklist || {};
                    const items = [
                        {key:'document_check', label:'Doc Check'},
                        {key:'owner_interview', label:'Interview'},
                        {key:'pengantar_mcu', label:'MCU'},
                        {key:'agreement_kontrak', label:'Kontrak'},
                        {key:'admin_charge', label:'Admin'},
                        {key:'ok_to_board', label:'OK Board'}
                    ];
                    let passed = 0, rejected = 0, total = items.length;
                    items.forEach(i => { const v = parseInt(cl[i.key]||0); if(v===1) passed++; if(v===2) rejected++; });
                    const pct = total > 0 ? Math.round((passed/total)*100) : 0;
                    const barColor = rejected > 0 ? '#ef4444' : (pct===100 ? '#22c55e' : '#3b82f6');
                    let checkHtml = items.map(i => {
                        const v = parseInt(cl[i.key]||0);
                        const bg = v===1 ? 'rgba(34,197,94,0.12)' : (v===2 ? 'rgba(239,68,68,0.12)' : 'rgba(148,163,184,0.08)');
                        const color = v===1 ? '#166534' : (v===2 ? '#991b1b' : '#64748b');
                        const icon = v===1 ? '✓' : (v===2 ? '✗' : '○');
                        return '<span style="display:inline-flex;align-items:center;gap:4px;padding:4px 8px;border-radius:6px;font-size:0.75rem;font-weight:600;background:'+bg+';color:'+color+'"><b>'+icon+'</b>'+i.label+'</span>';
                    }).join('');
                    let statusLine = '';
                    if (rejected > 0) statusLine = '<small style="color:#ef4444;font-weight:600;margin-top:6px;display:block"><i class="fas fa-times-circle"></i> Rejected</small>';
                    else if (pct===100) statusLine = '<small style="color:#22c55e;font-weight:600;margin-top:6px;display:block"><i class="fas fa-check-circle"></i> Siap Deploy</small>';
                    else statusLine = '<small style="color:#1e40af;margin-top:6px;display:block">ERP ID: '+(d.erp_crew_id||'')+ ' • Proses di ERP</small>';
                    return '<div class="detail-section">'+
                        '<div class="detail-section-title"><i class="fas fa-tasks me-1"></i>Admin Checklist <span style="float:right;font-weight:600">'+passed+'/'+total+'</span></div>'+
                        '<div style="height:6px;background:#e5e7eb;border-radius:3px;margin-bottom:10px;overflow:hidden"><div style="height:100%;width:'+pct+'%;background:'+barColor+';border-radius:3px;transition:width 0.3s"></div></div>'+
                        '<div style="display:flex;flex-wrap:wrap;gap:4px">'+checkHtml+'</div>'+
                        statusLine+'</div>';
                })() : (d.erp_crew_id ? '<div class="detail-section"><div class="detail-section-title"><i class="fas fa-tasks me-1"></i>Admin Checklist</div><p style="color:#9ca3af;font-size:0.85rem">Sent to ERP - ERP ID: '+d.erp_crew_id+'</p></div>' : '')}
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
            // Update the card in place â€” change button to "Pending"
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

// ===== ARCHIVE APPLICATION =====
function archiveApplication(appId) {
    showConfirm('Arsipkan aplikasi ini? Anda bisa mengembalikannya nanti dari tab Arsip.', 'Ya, Arsipkan', '#ef4444')
    .then(function() {
        fetch('<?= url('/crewing/pipeline/archive') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `csrf_token=<?= csrf_token() ?>&application_id=${appId}`
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Aplikasi berhasil diarsipkan', 'success');
                // Remove only this card from view
                const card = document.getElementById('appCard-' + appId)
                          || document.querySelector(`[data-app-id="${appId}"]`);
                if (card) {
                    card.style.transition = 'all 0.4s ease';
                    card.style.transform = 'translateX(100%)';
                    card.style.opacity = '0';
                    setTimeout(() => {
                        // Find parent column before removing card
                        const column = card.closest('.pl-col') || card.closest('td');
                        card.remove();
                        // Update count for this column only
                        if (column) {
                            const statusId = column.dataset.statusId;
                            const cards = column.querySelectorAll('.app-card').length;
                            const countEl = statusId
                                ? column.querySelector(`[data-count-for="${statusId}"]`)
                                : column.querySelector('.count');
                            if (countEl) countEl.textContent = cards;
                            // Show empty placeholder if no cards left in this column
                            const body = column.querySelector('.column-body');
                            if (body && cards === 0 && !body.querySelector('.empty-column')) {
                                body.innerHTML = '<div class="empty-column"><i class="fas fa-inbox"></i><small>Belum ada lamaran</small></div>';
                            }
                        }
                    }, 400);
                } else {
                    setTimeout(() => location.reload(), 500);
                }
            } else {
                showToast(data.message || 'Gagal mengarsipkan', 'error');
            }
        })
        .catch(err => showToast('Error: ' + err.message, 'error'));
    })
    .catch(function() {}); // user cancelled
}

// ===== LOAD ARCHIVED VIEW =====
function loadArchivedView() {
    document.querySelectorAll('.view-tab').forEach(tab => tab.classList.remove('active'));
    const archivedTab = document.getElementById('archivedTab');
    if (archivedTab) archivedTab.classList.add('active');

    // Hide alerts
    var ao = document.getElementById('alertOverlay');
    if (ao) ao.style.display = 'none';
    var po = document.getElementById('pendingOverlay');
    if (po) po.style.display = 'none';

    // Hide pipeline board scroll wrapper, show archive container
    var pipelineBoard = document.getElementById('pipelineBoard');
    var pipelineScroll = pipelineBoard ? pipelineBoard.parentElement : null;
    if (pipelineScroll) pipelineScroll.style.display = 'none';

    var container = document.getElementById('archiveContainer');
    if (!container) { console.error('archiveContainer not found'); return; }
    container.style.display = 'block';
    container.innerHTML = '<div style="text-align:center;padding:60px;color:#fff;"><i class="fas fa-spinner fa-spin" style="font-size:2rem;display:block;margin-bottom:12px;"></i>Memuat arsip...</div>';

    fetch('<?= url('/crewing/pipeline/archived') ?>')
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (!res.success) {
            container.innerHTML = '<p style="color:#fca5a5;text-align:center;padding:40px;">Gagal memuat arsip</p>';
            return;
        }
        var archived = res.data;
        if (!archived || archived.length === 0) {
            container.innerHTML = '<div style="text-align:center;padding:80px;color:#94a3b8;"><i class="fas fa-archive" style="font-size:4rem;margin-bottom:20px;display:block;opacity:0.4;"></i><h4 style="font-size:1.1rem;margin-bottom:8px;">Tidak Ada Arsip</h4><p style="font-size:0.9rem;">Aplikasi yang diarsipkan akan muncul di sini</p></div>';
            return;
        }
        var html = '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:16px;padding:4px 2px;">';
        for (var i = 0; i < archived.length; i++) {
            var app = archived[i];
            var nameParts = (app.applicant_name || 'U').split(' ');
            var initials = nameParts[0][0].toUpperCase() + (nameParts[1] ? nameParts[1][0].toUpperCase() : '');
            var avatarHtml = app.applicant_avatar
                ? '<img src="<?= url('/') ?>' + app.applicant_avatar + '" style="width:44px;height:44px;border-radius:12px;object-fit:cover;flex-shrink:0;">'
                : '<div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#6366f1,#4f46e5);color:white;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.95rem;flex-shrink:0;">' + initials + '</div>';
            var archivedDate = app.archived_at ? new Date(app.archived_at).toLocaleDateString('id-ID') : '-';
            html += '<div style="background:white;border-radius:14px;padding:16px;border:1px solid #e5e7eb;box-shadow:0 2px 8px rgba(0,0,0,0.1);">'
                + '<div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">'
                + avatarHtml
                + '<div style="flex:1;min-width:0;">'
                + '<div style="font-size:0.9rem;font-weight:700;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + (app.applicant_name || '-') + '</div>'
                + '<div style="font-size:0.75rem;color:#64748b;">' + (app.vacancy_title || 'N/A') + '</div>'
                + '<span style="background:' + (app.status_color || '#6c757d') + ';color:white;padding:2px 8px;border-radius:20px;font-size:0.7rem;font-weight:600;display:inline-block;margin-top:3px;">' + (app.status_name || '-') + '</span>'
                + '</div></div>'
                + '<div style="font-size:0.78rem;color:#94a3b8;margin-bottom:8px;"><i class="fas fa-envelope" style="margin-right:4px;"></i>' + (app.applicant_email || '') + '</div>'
                + '<div style="background:#f8fafc;padding:8px 10px;border-radius:8px;margin-bottom:10px;font-size:0.78rem;color:#64748b;border-left:3px solid #94a3b8;">'
                + '<i class="fas fa-archive" style="margin-right:4px;"></i>Oleh <strong>' + (app.archived_by_name || 'Unknown') + '</strong> &bull; ' + archivedDate
                + '</div>'
                + '<div style="display:flex;gap:8px;">'
                + '<button data-action="detail" data-id="' + app.id + '" style="padding:7px 12px;border:1px solid #e5e7eb;border-radius:8px;background:white;color:#374151;cursor:pointer;font-size:0.8rem;font-weight:600;display:flex;align-items:center;gap:4px;"><i class="fas fa-eye"></i> Detail</button>'
                + '<button data-action="restore" data-id="' + app.id + '" style="flex:1;padding:7px 0;border:none;border-radius:8px;background:linear-gradient(135deg,#10b981,#059669);color:white;cursor:pointer;font-size:0.8rem;font-weight:600;display:flex;align-items:center;justify-content:center;gap:4px;"><i class="fas fa-undo"></i> Kembalikan</button>'
                + '<button data-action="delete" data-id="' + app.id + '" style="padding:7px 10px;border:2px solid #fca5a5;border-radius:8px;background:white;color:#ef4444;cursor:pointer;font-size:0.8rem;" title="Hapus Permanen"><i class="fas fa-trash"></i></button>'
                + '</div></div>';
        }
        html += '</div>';
        container.innerHTML = html;
    })
    .catch(function(err) {
        container.innerHTML = '<p style="color:#fca5a5;text-align:center;padding:40px;">Error: ' + err.message + '</p>';
    });
}


// Event delegation for archive buttons (works with dynamically rendered content)
document.addEventListener('click', function(e) {
    const btn = e.target.closest('[data-action]');
    if (!btn) return;
    const action = btn.dataset.action;
    const id = parseInt(btn.dataset.id);
    if (!id) return;
    if (action === 'detail') { showDetail(id); }
    else if (action === 'restore') { restoreApplication(id); }
    else if (action === 'delete') { permanentDeleteApplication(id); }
});


// ===== CUSTOM CONFIRM HELPER =====
function showConfirm(message, okLabel, okColor) {
    return new Promise(function(resolve, reject) {
        window._confirmResolve = resolve;
        window._confirmReject = reject;
        document.getElementById('confirmModalMessage').textContent = message;
        var okBtn = document.getElementById('confirmModalOkBtn');
        okBtn.textContent = okLabel || 'Ya, Lanjutkan';
        okBtn.style.background = okColor || '#0f3460';
        openModal('confirmModal');
    });
}

// ===== RESTORE APPLICATION =====
function restoreApplication(appId) {
    showConfirm('Kembalikan aplikasi ini ke pipeline?', 'Ya, Kembalikan', '#059669')
    .then(function() {
        fetch('<?= url('/crewing/pipeline/restore') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'csrf_token=<?= csrf_token() ?>&application_id=' + appId
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                showToast(data.message || 'Aplikasi berhasil dikembalikan', 'success');
                loadArchivedView();
            } else {
                showToast(data.message || 'Gagal mengembalikan', 'error');
            }
        })
        .catch(function(err) { showToast('Error: ' + err.message, 'error'); });
    })
    .catch(function() {}); // user cancelled
}

// ===== PERMANENT DELETE APPLICATION =====
function permanentDeleteApplication(appId) {
    showConfirm('PERHATIAN! Aplikasi akan dihapus PERMANEN dan tidak bisa dikembalikan. Lanjutkan?', 'Hapus Permanen', '#dc2626')
    .then(function() {
        fetch('<?= url('/crewing/pipeline/delete-permanent') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `csrf_token=<?= csrf_token() ?>&application_id=${appId}`
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Aplikasi berhasil dihapus permanen', 'success');
                loadArchivedView();
            } else {
                showToast(data.message || 'Gagal menghapus', 'error');
            }
        })
        .catch(err => showToast('Error: ' + err.message, 'error'));
    })
    .catch(function() {}); // user cancelled
}

// ===== DISMISS NEW CANDIDATE ALERTS =====
function dismissAlert(appId) {
    const item = document.getElementById('alertItem-' + appId);
    if (item) {
        item.style.transition = 'all 0.3s ease';
        item.style.opacity = '0';
        item.style.transform = 'translateX(40px)';
        setTimeout(() => {
            item.remove();
            // Hide the whole overlay if no items left
            const box = document.getElementById('newCandidateAlert');
            if (box && box.querySelectorAll('[id^="alertItem-"]').length === 0) {
                var overlay = document.getElementById('alertOverlay');
                if (overlay) overlay.style.display = 'none';
            }
        }, 320);
    }
    fetch('<?= url('/crewing/pipeline/dismiss-alert') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `csrf_token=<?= csrf_token() ?>&application_id=${appId}`
    }).catch(err => console.warn('dismissAlert error:', err));
}

function dismissAllAlerts() {
    var overlay = document.getElementById('alertOverlay');
    if (overlay) {
        overlay.style.transition = 'opacity 0.3s ease';
        overlay.style.opacity = '0';
        setTimeout(() => { overlay.style.display = 'none'; }, 320);
    }
    fetch('<?= url('/crewing/pipeline/dismiss-alert') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `csrf_token=<?= csrf_token() ?>&application_id=0`
    }).catch(err => console.warn('dismissAllAlerts error:', err));
}

// ===== CARD ACTION POPUP =====
window._cardActionAppId = null;
window._cardActionStatusId = null;

function openCardActionPopup(appId, name, vacancy, avatar, statusId, showErp) {
    window._cardActionAppId = appId;
    window._cardActionStatusId = statusId;
    
    document.getElementById('cardActionName').textContent = name;
    document.getElementById('cardActionVacancy').innerHTML = '<i class="fas fa-briefcase" style="margin-right:4px;"></i>' + vacancy;
    
    // Show/hide ERP button
    var erpBtn = document.getElementById('cardActionErpBtn');
    if (erpBtn) erpBtn.style.display = showErp ? 'flex' : 'none';
    
    // Set avatar
    var avatarEl = document.getElementById('cardActionAvatar');
    if (avatar) {
        avatarEl.innerHTML = '<img src="<?= url('/') ?>' + avatar + '" alt="">';
    } else {
        var parts = name.split(' ');
        var initials = parts[0][0].toUpperCase() + (parts[1] ? parts[1][0].toUpperCase() : '');
        avatarEl.textContent = initials;
    }
    
    document.getElementById('cardActionOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeCardActionPopup() {
    var overlay = document.getElementById('cardActionOverlay');
    var popup = overlay.querySelector('.card-action-popup');
    popup.style.animation = 'none';
    popup.offsetHeight; // trigger reflow
    popup.style.animation = 'cardActionPopOut 0.25s ease forwards';
    overlay.style.animation = 'cardActionFadeOut 0.25s ease forwards';
    
    setTimeout(function() {
        overlay.classList.remove('show');
        overlay.style.animation = '';
        popup.style.animation = '';
        document.body.style.overflow = '';
    }, 250);
}

// Inject close animations
var cardActionStyle = document.createElement('style');
cardActionStyle.textContent = '@keyframes cardActionPopOut{from{transform:scale(1);opacity:1}to{transform:scale(0.85) translateY(20px);opacity:0}}@keyframes cardActionFadeOut{from{opacity:1}to{opacity:0}}';
document.head.appendChild(cardActionStyle);

// ===== FIX: Move overlays to document.body =====
// When .admin-main has transform:scale(), position:fixed children
// become relative to the transformed parent, not the viewport.
// Moving overlays to body fixes centering.
(function() {
    var overlays = document.querySelectorAll('.card-action-overlay, .p-modal, #alertOverlay, #pendingOverlay, #confirmModal');
    overlays.forEach(function(el) {
        document.body.appendChild(el);
    });
})();

</script>
