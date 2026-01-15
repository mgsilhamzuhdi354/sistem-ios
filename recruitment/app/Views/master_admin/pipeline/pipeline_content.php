<!-- Modern Pipeline Content -->
<style>
/* Pipeline Header */
.pipeline-header {
    background: linear-gradient(135deg, #1e3a5f 0%, #0d1f33 100%);
    color: white;
    padding: 25px 30px;
    border-radius: 16px;
    margin-bottom: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.pipeline-header h2 { margin: 0; font-weight: 600; }
.pipeline-header .badge { background: #dc2626; padding: 8px 16px; border-radius: 20px; font-size: 0.9rem; }

/* Stats Row */
.stats-row { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
.stat-box { 
    background: white; 
    padding: 20px; 
    border-radius: 12px; 
    flex: 1; 
    min-width: 150px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 15px;
}
.stat-icon { 
    width: 50px; height: 50px; 
    border-radius: 12px; 
    display: flex; align-items: center; justify-content: center;
    font-size: 1.25rem;
}
.stat-icon.blue { background: #e3f2fd; color: #1976d2; }
.stat-icon.green { background: #e8f5e9; color: #388e3c; }
.stat-icon.red { background: #ffebee; color: #d32f2f; }
.stat-value { font-size: 1.75rem; font-weight: 700; color: #1a1a2e; }
.stat-label { font-size: 0.85rem; color: #666; }

/* Pipeline Board */
.pipeline-board { display: flex; gap: 15px; overflow-x: auto; padding: 10px 0; }
.pipeline-column { 
    min-width: 300px; 
    max-width: 320px; 
    background: #f5f7fa; 
    border-radius: 16px; 
    overflow: hidden;
    flex-shrink: 0;
}
.column-header { 
    padding: 15px 20px; 
    color: white; 
    display: flex; 
    justify-content: space-between; 
    align-items: center;
    font-weight: 600;
}
.column-header .count { 
    background: rgba(255,255,255,0.3); 
    padding: 4px 12px; 
    border-radius: 15px; 
    font-size: 0.85rem;
}
.column-body { padding: 15px; max-height: 550px; overflow-y: auto; }
.empty-column { text-align: center; padding: 40px 20px; color: #aaa; }
.empty-column i { font-size: 2rem; margin-bottom: 10px; display: block; }

/* Application Card */
.app-card {
    background: white;
    border-radius: 14px;
    padding: 20px;
    margin-bottom: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    border-left: 4px solid #3b82f6;
    transition: all 0.3s;
}
.app-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
.app-card.unassigned { border-left-color: #dc2626; background: linear-gradient(to right, #fef2f2, white); }

.app-card h4 { margin: 0 0 5px; font-size: 1.1rem; font-weight: 600; color: #1e3a5f; }
.app-card .vacancy { color: #666; font-size: 0.85rem; margin-bottom: 12px; }

.handler-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    border-radius: 20px;
    font-size: 0.85rem;
    margin: 10px 0;
}
.handler-badge.assigned { background: linear-gradient(135deg, #22c55e, #16a34a); color: white; }
.handler-badge.unassigned { background: #fee2e2; color: #dc2626; }

/* Action Buttons */
.card-actions { display: flex; gap: 8px; margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee; }
.action-btn {
    flex: 1;
    padding: 10px;
    border: none;
    border-radius: 10px;
    background: #f1f5f9;
    color: #475569;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.8rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
    text-decoration: none;
}
.action-btn:hover { background: #e2e8f0; transform: scale(1.02); }
.action-btn i { font-size: 1rem; }

/* Modal Styles */
.modern-modal {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.7);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(5px);
}
.modern-modal.show { display: flex; animation: fadeIn 0.3s ease; }
.modal-box {
    background: white;
    border-radius: 20px;
    max-width: 550px;
    width: 95%;
    overflow: hidden;
    animation: scaleIn 0.4s ease;
}
.modal-box-header {
    background: linear-gradient(135deg, #4a90d9, #63b3ed);
    color: white;
    padding: 20px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.modal-box-header h3 { margin: 0; font-size: 1.25rem; }
.modal-box-body { padding: 25px; }
.modal-box-footer { padding: 15px 25px; background: #f8f9fa; display: flex; gap: 12px; justify-content: flex-end; }

.btn-cancel { background: #e5e7eb; border: none; padding: 12px 25px; border-radius: 10px; cursor: pointer; }
.btn-submit { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; border: none; padding: 12px 25px; border-radius: 10px; cursor: pointer; }
.btn-submit:hover { transform: scale(1.02); }

.status-select-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin: 15px 0; }
.status-option {
    padding: 15px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s;
    text-align: center;
}
.status-option:hover { border-color: #3b82f6; background: #eff6ff; }
.status-option.selected { border-color: #3b82f6; background: #dbeafe; }
.status-option input { display: none; }

@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes scaleIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
</style>

<div class="pipeline-header">
    <div>
        <h2><i class="fas fa-columns me-3"></i>Pipeline Rekrutmen</h2>
        <small class="opacity-75">Kelola status lamaran pelamar</small>
    </div>
    <span class="badge"><?= $stats['total'] ?> Total Aplikasi</span>
</div>

<div class="stats-row">
    <div class="stat-box">
        <div class="stat-icon blue"><i class="fas fa-file-alt"></i></div>
        <div>
            <div class="stat-value"><?= $stats['total'] ?></div>
            <div class="stat-label">Total</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon green"><i class="fas fa-user-check"></i></div>
        <div>
            <div class="stat-value"><?= $stats['assigned'] ?></div>
            <div class="stat-label">Assigned</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon red"><i class="fas fa-user-times"></i></div>
        <div>
            <div class="stat-value"><?= $stats['unassigned'] ?></div>
            <div class="stat-label">Unassigned</div>
        </div>
    </div>
</div>

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
            <div class="app-card <?= !$app['crewing_id'] ? 'unassigned' : '' ?>">
                <h4><?= htmlspecialchars($app['applicant_name']) ?></h4>
                <div class="vacancy"><i class="fas fa-briefcase me-1"></i><?= htmlspecialchars($app['vacancy_title'] ?? 'N/A') ?></div>
                
                <div class="text-center">
                    <small class="text-muted d-block mb-2">HANDLER:</small>
                    <?php if ($app['crewing_name']): ?>
                    <span class="handler-badge assigned">
                        <i class="fas fa-user-circle"></i>
                        <?= htmlspecialchars($app['crewing_name']) ?>
                    </span>
                    <?php else: ?>
                    <span class="handler-badge unassigned">
                        <i class="fas fa-user-slash"></i> Unassigned
                    </span>
                    <?php endif; ?>
                </div>
                
                <div class="card-actions">
                    <button class="action-btn" onclick="openReassignModal(<?= $app['id'] ?>, '<?= htmlspecialchars($app['applicant_name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($app['vacancy_title'] ?? '', ENT_QUOTES) ?>', <?= $app['crewing_id'] ?: 0 ?>, '<?= htmlspecialchars($app['crewing_name'] ?? '', ENT_QUOTES) ?>')">
                        <i class="fas fa-exchange-alt"></i>
                        <span>Reassign</span>
                    </button>
                    <button class="action-btn" onclick="openStatusModal(<?= $app['id'] ?>, '<?= htmlspecialchars($app['applicant_name'], ENT_QUOTES) ?>', <?= $status['id'] ?>)">
                        <i class="fas fa-arrows-alt-v"></i>
                        <span>Status</span>
                    </button>
                    <button class="action-btn" onclick="openDetailModal(<?= $app['id'] ?>, '<?= htmlspecialchars($app['applicant_name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($app['vacancy_title'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($app['applicant_email'] ?? '', ENT_QUOTES) ?>', '-', '<?= date('d M Y', strtotime($app['created_at'] ?? 'now')) ?>', '<?= htmlspecialchars($status['name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($app['crewing_name'] ?? 'Belum ditugaskan', ENT_QUOTES) ?>')">
                        <i class="fas fa-eye"></i>
                        <span>Detail</span>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Change Status Modal -->
<div class="modern-modal" id="statusModal">
    <div class="modal-box">
        <div class="modal-box-header">
            <h3><i class="fas fa-arrows-alt-v me-2"></i>Pindah Status</h3>
            <button onclick="closeModal('statusModal')" style="background:none;border:none;color:white;font-size:1.5rem;cursor:pointer;">&times;</button>
        </div>
        <div class="modal-box-body">
            <p class="text-muted mb-3">Pindahkan <strong id="statusAppName">-</strong> ke status:</p>
            
            <div class="status-select-grid">
                <?php foreach ($statuses as $s): ?>
                <label class="status-option" onclick="selectStatus(<?= $s['id'] ?>)" id="statusOpt<?= $s['id'] ?>">
                    <input type="radio" name="new_status" value="<?= $s['id'] ?>">
                    <div style="color: <?= $s['color'] ?? '#666' ?>; font-weight: 600;">
                        <?= htmlspecialchars($s['name']) ?>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-3">
                <label class="form-label">Alasan (opsional)</label>
                <textarea id="statusReason" class="form-control" rows="2" placeholder="Masukkan alasan perubahan status..."></textarea>
            </div>
        </div>
        <div class="modal-box-footer">
            <input type="hidden" id="statusAppId">
            <input type="hidden" id="currentStatusId">
            <input type="hidden" id="selectedStatusId">
            <button class="btn-cancel" onclick="closeModal('statusModal')">Batal</button>
            <button class="btn-submit" onclick="submitStatusChange()">
                <i class="fas fa-paper-plane me-2"></i>Kirim Request
            </button>
        </div>
    </div>
</div>

<!-- View Detail Modal - Complete Profile -->
<div class="modern-modal" id="detailModal">
    <div class="modal-box" style="max-width: 650px;">
        <div class="modal-box-header" style="background: linear-gradient(135deg, #1e3a5f, #2d5a8f);">
            <h3><i class="fas fa-user me-2"></i>Detail Pelamar</h3>
            <button onclick="closeModal('detailModal')" style="background:none;border:none;color:white;font-size:1.5rem;cursor:pointer;">&times;</button>
        </div>
        <div class="modal-box-body" style="max-height: 70vh; overflow-y: auto;">
            <!-- Profile Header -->
            <div class="text-center mb-4" style="background: linear-gradient(135deg, #f0f4f8, #e2e8f0); padding: 30px; border-radius: 16px; margin: -25px -25px 20px -25px;">
                <div style="width:100px;height:100px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#1d4ed8);color:white;display:flex;align-items:center;justify-content:center;font-size:2.5rem;margin:0 auto 15px;box-shadow:0 8px 25px rgba(59,130,246,0.4);">
                    <i class="fas fa-user"></i>
                </div>
                <h3 id="detailName" style="margin:0 0 5px;color:#1e3a5f;font-weight:700;">-</h3>
                <p id="detailVacancy" style="color:#666;margin:0;font-size:1rem;"><i class="fas fa-briefcase me-1"></i>-</p>
            </div>
            
            <!-- Info Grid -->
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:15px;">
                <div style="background:#f8f9fa;padding:15px;border-radius:12px;">
                    <small style="color:#888;display:block;margin-bottom:5px;"><i class="fas fa-envelope me-1"></i>Email</small>
                    <strong id="detailEmail" style="color:#1e3a5f;">-</strong>
                </div>
                <div style="background:#f8f9fa;padding:15px;border-radius:12px;">
                    <small style="color:#888;display:block;margin-bottom:5px;"><i class="fas fa-phone me-1"></i>Telepon</small>
                    <strong id="detailPhone" style="color:#1e3a5f;">-</strong>
                </div>
                <div style="background:#f8f9fa;padding:15px;border-radius:12px;">
                    <small style="color:#888;display:block;margin-bottom:5px;"><i class="fas fa-hashtag me-1"></i>ID Lamaran</small>
                    <strong id="detailAppId" style="color:#1e3a5f;">-</strong>
                </div>
                <div style="background:#f8f9fa;padding:15px;border-radius:12px;">
                    <small style="color:#888;display:block;margin-bottom:5px;"><i class="fas fa-calendar me-1"></i>Tanggal Daftar</small>
                    <strong id="detailDate" style="color:#1e3a5f;">-</strong>
                </div>
            </div>
            
            <!-- Status & Handler -->
            <div style="margin-top:20px;display:flex;gap:15px;">
                <div style="flex:1;background:linear-gradient(135deg,#e0f2fe,#bae6fd);padding:15px;border-radius:12px;text-align:center;">
                    <small style="color:#0369a1;display:block;margin-bottom:5px;"><i class="fas fa-flag me-1"></i>Status</small>
                    <strong id="detailStatus" style="color:#0c4a6e;font-size:1.1rem;">-</strong>
                </div>
                <div style="flex:1;background:linear-gradient(135deg,#dcfce7,#bbf7d0);padding:15px;border-radius:12px;text-align:center;">
                    <small style="color:#166534;display:block;margin-bottom:5px;"><i class="fas fa-user-tie me-1"></i>Handler</small>
                    <strong id="detailHandler" style="color:#14532d;font-size:1.1rem;">-</strong>
                </div>
            </div>
        </div>
        <div class="modal-box-footer" style="justify-content:space-between;">
            <button class="btn-cancel" onclick="closeModal('detailModal')">Tutup</button>
            <a id="detailFullLink" href="#" class="btn-submit" style="text-decoration:none;display:inline-flex;align-items:center;">
                <i class="fas fa-external-link-alt me-2"></i>Lihat Profil Lengkap
            </a>
        </div>
    </div>
</div>

<!-- Reassign Modal - FROM → TO with Notes -->
<div class="modern-modal" id="reassignModal">
    <div class="modal-box" style="max-width: 600px;">
        <div class="modal-box-header" style="background: linear-gradient(135deg, #4a90d9, #63b3ed);">
            <h3><i class="fas fa-exchange-alt me-2"></i>Reassign Handler</h3>
            <span style="background:#22c55e;color:white;padding:5px 12px;border-radius:6px;font-size:0.8rem;font-weight:600;">Transfer</span>
            <button onclick="closeModal('reassignModal')" style="background:none;border:none;color:white;font-size:1.5rem;cursor:pointer;">&times;</button>
        </div>
        <div class="modal-box-body">
            <!-- Applicant Info -->
            <div style="background:#f0f4f8;padding:15px 20px;border-radius:12px;display:flex;align-items:center;gap:15px;margin-bottom:20px;">
                <div style="width:50px;height:50px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#1d4ed8);color:white;display:flex;align-items:center;justify-content:center;font-size:1.25rem;">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <strong id="reassignAppName" style="color:#1e3a5f;font-size:1.1rem;">-</strong>
                    <small id="reassignVacancy" class="d-block text-muted">-</small>
                </div>
            </div>
            
            <!-- FROM → TO Section -->
            <div style="display:flex;align-items:stretch;gap:15px;margin-bottom:20px;">
                <!-- FROM Box -->
                <div style="flex:1;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;">
                    <div style="background:linear-gradient(135deg,#94a3b8,#64748b);color:white;padding:10px 15px;font-size:0.85rem;font-weight:600;">
                        <i class="fas fa-sign-out-alt me-1"></i>DARI (Sekarang)
                    </div>
                    <div style="padding:15px;background:white;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div id="fromHandlerAvatar" style="width:40px;height:40px;border-radius:50%;background:#e2e8f0;display:flex;align-items:center;justify-content:center;color:#64748b;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <strong id="fromHandlerName" style="color:#334155;display:block;">Unassigned</strong>
                                <small id="fromHandlerRole" style="color:#94a3b8;">-</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Arrow -->
                <div style="display:flex;align-items:center;color:#4a90d9;font-size:1.5rem;">
                    <i class="fas fa-arrow-right"></i>
                </div>
                
                <!-- TO Box -->
                <div style="flex:1;border-radius:12px;overflow:hidden;border:2px solid #22c55e;">
                    <div style="background:linear-gradient(135deg,#22c55e,#16a34a);color:white;padding:10px 15px;font-size:0.85rem;font-weight:600;">
                        <i class="fas fa-sign-in-alt me-1"></i>KE (Handler Baru)
                    </div>
                    <div style="padding:15px;background:white;">
                        <select id="reassignTo" class="form-select" style="border:none;background:#f0fdf4;font-weight:500;color:#166534;padding:12px;">
                            <option value="">Pilih Handler Baru...</option>
                            <?php foreach ($crewingStaff as $crew): ?>
                            <option value="<?= $crew['id'] ?>"><?= htmlspecialchars($crew['full_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Optional Notes -->
            <div style="background:#fffbeb;padding:15px;border-radius:12px;border-left:4px solid #f59e0b;">
                <label style="font-size:0.85rem;font-weight:600;color:#92400e;display:block;margin-bottom:8px;">
                    <i class="fas fa-sticky-note me-1"></i>Catatan (Opsional)
                </label>
                <textarea id="reassignNotes" class="form-control" rows="2" placeholder="Masukkan alasan atau instruksi tambahan..." style="border:none;background:white;resize:none;"></textarea>
            </div>
        </div>
        <div class="modal-box-footer">
            <input type="hidden" id="reassignAppId">
            <input type="hidden" id="reassignFromId" value="0">
            <button class="btn-cancel" onclick="closeModal('reassignModal')">Batal</button>
            <button class="btn-submit" style="background:linear-gradient(135deg,#22c55e,#16a34a);" onclick="submitReassign()">
                <i class="fas fa-check me-2"></i>Konfirmasi Reassign
            </button>
        </div>
    </div>
</div>

<!-- Success Popup -->
<div class="modern-modal" id="successPopup">
    <div class="modal-box" style="max-width:400px;text-align:center;padding:40px;">
        <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#22c55e,#16a34a);color:white;display:flex;align-items:center;justify-content:center;font-size:2.5rem;margin:0 auto 20px;">
            <i class="fas fa-check"></i>
        </div>
        <h3 id="successTitle" style="color:#1e3a5f;">Berhasil!</h3>
        <p id="successMessage" class="text-muted">Request telah dikirim</p>
        <button class="btn-submit" onclick="closeModal('successPopup');location.reload();">OK</button>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.remove('show');
    document.body.style.overflow = '';
}

function openStatusModal(appId, appName, currentStatus) {
    document.getElementById('statusAppId').value = appId;
    document.getElementById('statusAppName').textContent = appName;
    document.getElementById('currentStatusId').value = currentStatus;
    document.getElementById('statusReason').value = '';
    document.querySelectorAll('.status-option').forEach(el => el.classList.remove('selected'));
    openModal('statusModal');
}

function selectStatus(statusId) {
    document.querySelectorAll('.status-option').forEach(el => el.classList.remove('selected'));
    document.getElementById('statusOpt' + statusId).classList.add('selected');
    document.getElementById('selectedStatusId').value = statusId;
}

function submitStatusChange() {
    const appId = document.getElementById('statusAppId').value;
    const fromStatus = document.getElementById('currentStatusId').value;
    const toStatus = document.getElementById('selectedStatusId').value;
    const reason = document.getElementById('statusReason').value;
    
    if (!toStatus) {
        alert('Pilih status tujuan!');
        return;
    }
    
    // For Master Admin - direct change (no request needed)
    fetch('<?= url('/master-admin/pipeline/update-status') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `csrf_token=<?= csrf_token() ?>&application_id=${appId}&status_id=${toStatus}`
    })
    .then(r => r.text())
    .then(() => {
        closeModal('statusModal');
        document.getElementById('successTitle').textContent = 'Status Diubah!';
        document.getElementById('successMessage').textContent = 'Status lamaran berhasil diubah';
        openModal('successPopup');
    });
}

function openDetailModal(appId, name, vacancy, email, phone, date, status, handler) {
    document.getElementById('detailName').textContent = name;
    document.getElementById('detailVacancy').innerHTML = '<i class="fas fa-briefcase me-1"></i>' + (vacancy || 'Posisi tidak tersedia');
    document.getElementById('detailEmail').textContent = email || '-';
    document.getElementById('detailPhone').textContent = phone || '-';
    document.getElementById('detailAppId').textContent = '#' + appId;
    document.getElementById('detailDate').textContent = date || '-';
    document.getElementById('detailStatus').textContent = status || 'Pending';
    document.getElementById('detailHandler').textContent = handler || 'Belum ditugaskan';
    document.getElementById('detailFullLink').href = '<?= url('/admin/applicants/') ?>' + appId;
    openModal('detailModal');
}

function openReassignModal(appId, appName, vacancy, currentHandlerId, currentHandlerName) {
    document.getElementById('reassignAppId').value = appId;
    document.getElementById('reassignAppName').textContent = appName;
    document.getElementById('reassignVacancy').textContent = vacancy || 'N/A';
    document.getElementById('reassignFromId').value = currentHandlerId || 0;
    document.getElementById('fromHandlerName').textContent = currentHandlerName || 'Unassigned';
    document.getElementById('fromHandlerRole').textContent = currentHandlerId ? 'Crewing Staff' : 'Belum ditugaskan';
    document.getElementById('reassignTo').value = '';
    document.getElementById('reassignNotes').value = '';
    
    // Update avatar style based on assignment
    const avatar = document.getElementById('fromHandlerAvatar');
    if (currentHandlerId) {
        avatar.style.background = 'linear-gradient(135deg,#22c55e,#16a34a)';
        avatar.style.color = 'white';
    } else {
        avatar.style.background = '#fee2e2';
        avatar.style.color = '#dc2626';
    }
    
    openModal('reassignModal');
}

function submitReassign() {
    const appId = document.getElementById('reassignAppId').value;
    const toCrewingId = document.getElementById('reassignTo').value;
    const fromCrewingId = document.getElementById('reassignFromId').value;
    const notes = document.getElementById('reassignNotes').value;
    
    if (!toCrewingId) {
        alert('Pilih handler baru!');
        return;
    }
    
    fetch('<?= url('/master-admin/pipeline/transfer') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `csrf_token=<?= csrf_token() ?>&application_id=${appId}&to_crewing_id=${toCrewingId}&from_crewing_id=${fromCrewingId}&reason=${encodeURIComponent(notes)}`
    })
    .then(r => r.json())
    .then(data => {
        closeModal('reassignModal');
        if (data.success) {
            document.getElementById('successTitle').textContent = 'Berhasil!';
            document.getElementById('successMessage').textContent = data.message;
            openModal('successPopup');
        } else {
            alert('Error: ' + data.message);
        }
    });
}
</script>
