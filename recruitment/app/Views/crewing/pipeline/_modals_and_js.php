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
    if (!confirm('Arsipkan aplikasi ini? Anda bisa mengembalikannya nanti dari tab Arsip.')) {
        return;
    }
    
    fetch('<?= url('/crewing/pipeline/archive') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `csrf_token=<?= csrf_token() ?>&application_id=${appId}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Aplikasi berhasil diarsipkan', 'success');
            // Remove card from view with animation
            const card = document.querySelector(`.app-card-modern[data-app-id="${appId}"]`);
            if (card) {
                card.style.transition = 'all 0.4s ease';
                card.style.transform = 'translateX(100%)';
                card.style.opacity = '0';
                setTimeout(() => {
                    card.remove();
                    // Update column counts
                    document.querySelectorAll('.status-column').forEach(col => {
                        const cards = col.querySelectorAll('.app-card-modern').length;
                        const countEl = col.querySelector('.status-count');
                        if (countEl) countEl.textContent = cards;
                    });
                }, 400);
            } else {
                // Fallback: reload page
                setTimeout(() => location.reload(), 500);
            }
        } else {
            showToast(data.message || 'Gagal mengarsipkan', 'error');
        }
    })
    .catch(err => showToast('Error: ' + err.message, 'error'));
}

// ===== LOAD ARCHIVED VIEW =====
function loadArchivedView() {
    // Update tab highlights
    document.querySelectorAll('.view-toggle-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    const archivedTab = document.getElementById('archivedTab');
    if (archivedTab) archivedTab.classList.add('active');
    
    // Hide candidate alerts and pending alerts when viewing archive
    const candidateAlert = document.getElementById('newCandidateAlert');
    if (candidateAlert) candidateAlert.style.display = 'none';
    const pendingAlert = document.querySelector('.pending-alert');
    if (pendingAlert) pendingAlert.style.display = 'none';
    
    // Get the pipeline scroll container
    const scrollContainer = document.querySelector('.pipeline-scroll-container');
    if (!scrollContainer) return;
    
    scrollContainer.innerHTML = '<div class="detail-loading"><i class="fas fa-spinner fa-spin"></i><span>Loading archived applications...</span></div>';
    
    fetch('<?= url('/crewing/pipeline/archived') ?>')
    .then(r => r.json())
    .then(res => {
        if (!res.success) {
            scrollContainer.innerHTML = '<p style="color:#ef4444;text-align:center;padding:40px;">Failed to load archived applications</p>';
            return;
        }
        
        const archived = res.data;
        if (archived.length === 0) {
            scrollContainer.innerHTML = `
                <div class="empty-state" style="width:100%;text-align:center;padding:80px 20px;">
                    <i class="fas fa-archive" style="font-size:4rem;color:#cbd5e1;margin-bottom:20px;"></i>
                    <h4 style="color:#64748b;font-weight:600;margin-bottom:10px;">Tidak Ada Arsip</h4>
                    <p style="color:#94a3b8;font-size:0.95rem;">Aplikasi yang diarsipkan akan muncul di sini</p>
                </div>
            `;
            return;
        }
        
        let html = '<div style="max-width:900px;margin:0 auto;padding:20px;">';
        archived.forEach(app => {
            const nameParts = (app.applicant_name || 'U').split(' ');
            let initials = nameParts[0][0].toUpperCase();
            if (nameParts[1]) initials += nameParts[1][0].toUpperCase();
            
            const avatarHtml = app.applicant_avatar 
                ? `<img src="<?= url('/') ?>${app.applicant_avatar}" style="width:50px;height:50px;border-radius:50%;object-fit:cover;border:3px solid white;box-shadow:0 3px 12px rgba(99,102,241,0.3);">`
                : `<div style="width:50px;height:50px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#4f46e5);color:white;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1rem;border:3px solid white;box-shadow:0 3px 12px rgba(99,102,241,0.3);">${initials}</div>`;
            
            html += `
                <div class="app-card-modern" data-app-id="${app.id}" style="max-width:600px;margin:0 auto 20px;background:linear-gradient(135deg,rgba(255,255,255,0.95),rgba(255,255,255,0.85));">
                    <div style="display:flex;align-items:center;gap:15px;margin-bottom:15px;">
                        ${avatarHtml}
                        <div style="flex:1;min-width:0;">
                            <h4 style="margin:0 0 4px;font-size:1.1rem;font-weight:700;color:#1e293b;">${app.applicant_name}</h4>
                            <div class="vacancy" style="font-size:0.9rem;color:#64748b;margin-bottom:4px;">
                                <i class="fas fa-briefcase"></i> ${app.vacancy_title || 'N/A'}
                            </div>
                            <span class="detail-status-badge" style="background:${app.status_color || '#6c757d'};padding:4px 10px;border-radius:16px;font-size:0.75rem;display:inline-block;">${app.status_name || '-'}</span>
                        </div>
                    </div>
                    <div class="email" style="font-size:0.85rem;color:#94a3b8;margin-bottom:12px;">
                        <i class="fas fa-envelope"></i> ${app.applicant_email || ''}
                    </div>
                    <div style="background:rgba(241,245,249,0.8);padding:10px 14px;border-radius:12px;margin-bottom:15px;font-size:0.85rem;color:#64748b;border-left:3px solid #94a3b8;">
                        <i class="fas fa-archive" style="margin-right:6px;"></i> Diarsipkan oleh <strong>${app.archived_by_name || 'Unknown'}</strong> 
                        pada ${new Date(app.archived_at).toLocaleDateString('id-ID')}
                    </div>
                    <div style="display:flex;gap:10px;">
                        <button class="btn-modern btn-view" onclick="showDetail(${app.id})">
                            <i class="fas fa-eye"></i> Detail
                        </button>
                        <button class="btn-modern" style="flex:1.5;background:linear-gradient(135deg,#10b981,#059669);color:white;box-shadow:0 4px 12px rgba(16,185,129,0.25);" 
                                onclick="restoreApplication(${app.id})">
                            <i class="fas fa-undo"></i> Kembalikan
                        </button>
                        <button class="btn-modern" style="background:white;color:#ef4444;border:2px solid #fca5a5;box-shadow:none;" 
                                onclick="permanentDeleteApplication(${app.id})" 
                                title="Hapus Permanen">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        
        scrollContainer.innerHTML = html;
    })
    .catch(err => {
        scrollContainer.innerHTML = '<p style="color:#ef4444;text-align:center;padding:40px;">Error: ' + err.message + '</p>';
    });
}

// ===== RESTORE APPLICATION =====
function restoreApplication(appId) {
    if (!confirm('Kembalikan aplikasi ini dari arsip?')) {
        return;
    }
    
    fetch('<?= url('/crewing/pipeline/restore') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `csrf_token=<?= csrf_token() ?>&application_id=${appId}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Aplikasi berhasil dikembalikan', 'success');
            // Reload archived view
            loadArchivedView();
        } else {
            showToast(data.message || 'Gagal mengembalikan', 'error');
        }
    })
    .catch(err => showToast('Error: ' + err.message, 'error'));
}

// ===== PERMANENT DELETE APPLICATION =====
function permanentDeleteApplication(appId) {
    if (!confirm('⚠️ PERHATIAN! Aplikasi akan dihapus PERMANEN dan tidak bisa dikembalikan. Lanjutkan?')) {
        return;
    }
    
    fetch('<?= url('/crewing/pipeline/delete-permanent') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `csrf_token=<?= csrf_token() ?>&application_id=${appId}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Aplikasi berhasil dihapus permanen', 'success');
            // Reload archived view
            loadArchivedView();
        } else {
            showToast(data.message || 'Gagal menghapus', 'error');
        }
    })
    .catch(err => showToast('Error: ' + err.message, 'error'));
}

</script>

<?php include __DIR__ . '/erp_modal.php'; ?>
