<!-- Master Admin Pipeline Content - Simplified -->
<div class="page-header">
    <div class="header-left">
        <h1><i class="fas fa-columns"></i> Recruitment Pipeline</h1>
        <p class="text-muted">Master Admin - Pipeline Management</p>
    </div>
    <div class="header-actions">
        <div class="filter-group">
            <select id="filterCrewing" class="form-select form-select-sm" onchange="filterByCrewing(this.value)">
                <option value="">All Applications</option>
                <option value="unassigned" <?= $filterUnassigned ? 'selected' : '' ?>>🔴 Unassigned Only</option>
                <?php foreach ($crewingStaff as $crew): ?>
                <option value="<?= $crew['id'] ?>" <?= $filterCrewingId == $crew['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($crew['full_name']) ?> (<?= $crew['active_assignments'] ?>)
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-row">
    <div class="stat-card total">
        <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
        <div class="stat-info">
            <span class="stat-value"><?= $stats['total'] ?></span>
            <span class="stat-label">Total Applications</span>
        </div>
    </div>
    <div class="stat-card assigned">
        <div class="stat-icon"><i class="fas fa-user-check"></i></div>
        <div class="stat-info">
            <span class="stat-value"><?= $stats['assigned'] ?></span>
            <span class="stat-label">Assigned</span>
        </div>
    </div>
    <div class="stat-card unassigned" onclick="filterByCrewing('unassigned')" style="cursor:pointer">
        <div class="stat-icon"><i class="fas fa-user-times"></i></div>
        <div class="stat-info">
            <span class="stat-value"><?= $stats['unassigned'] ?></span>
            <span class="stat-label">Unassigned</span>
        </div>
    </div>
    <div class="stat-card workload">
        <div class="stat-icon"><i class="fas fa-chart-bar"></i></div>
        <div class="stat-info">
            <span class="stat-label">Top Handlers</span>
            <div class="mini-chart">
                <?php foreach (array_slice($stats['by_crewing'], 0, 3) as $c): ?>
                <div class="mini-bar" title="<?= htmlspecialchars($c['full_name']) ?>: <?= $c['count'] ?>">
                    <span class="bar-fill" style="width: <?= min(100, ($c['count'] / max(1, $stats['assigned'])) * 300) ?>%"></span>
                    <span class="bar-label"><?= $c['count'] ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Pipeline Board -->
<div class="pipeline-container">
    <div class="pipeline-board">
        <?php foreach ($statuses as $status): ?>
        <div class="pipeline-column" data-status-id="<?= $status['id'] ?>">
            <div class="column-header" style="background: <?= $status['color'] ?? '#6c757d' ?>;">
                <h5><?= htmlspecialchars($status['name']) ?></h5>
                <span class="badge"><?= count($pipeline[$status['id']] ?? []) ?></span>
            </div>
            <div class="column-body">
                <?php if (empty($pipeline[$status['id']])): ?>
                    <div class="empty-column">
                        <i class="fas fa-inbox"></i>
                        <small>No applications</small>
                    </div>
                <?php else: ?>
                    <?php foreach ($pipeline[$status['id']] as $app): ?>
                    <div class="pipeline-card <?= !$app['crewing_id'] ? 'unassigned' : '' ?>" data-app-id="<?= $app['id'] ?>">
                        <!-- Header -->
                        <div class="card-name">
                            <h4><?= htmlspecialchars($app['applicant_name']) ?></h4>
                            <span class="vacancy-title"><?= htmlspecialchars($app['vacancy_title']) ?></span>
                        </div>
                        
                        <!-- Current Handler Section -->
                        <div class="handler-section">
                            <span class="handler-label">CURRENT HANDLER:</span>
                            <?php if ($app['crewing_name']): ?>
                            <div class="handler-pill assigned">
                                <i class="fas fa-user-circle"></i>
                                <span>Handled by: <strong><?= htmlspecialchars($app['crewing_name']) ?></strong></span>
                            </div>
                            <?php else: ?>
                            <div class="handler-pill unassigned">
                                <i class="fas fa-user-slash"></i>
                                <span>Unassigned</span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="card-action-buttons">
                            <div class="action-btn-wrapper">
                                <button class="action-btn" onclick="openTransferModal(<?= $app['id'] ?>, '<?= htmlspecialchars($app['applicant_name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($app['vacancy_title'], ENT_QUOTES) ?>', <?= $app['crewing_id'] ?: 0 ?>, '<?= htmlspecialchars($app['crewing_name'] ?? 'Unassigned', ENT_QUOTES) ?>')">
                                    <i class="fas fa-exchange-alt"></i>
                                </button>
                                <span class="action-label">Reassign</span>
                            </div>
                            <div class="action-btn-wrapper">
                                <button class="action-btn" data-bs-toggle="modal" data-bs-target="#moveModal<?= $app['id'] ?>">
                                    <i class="fas fa-arrows-alt-v"></i>
                                </button>
                                <span class="action-label">Change Status</span>
                            </div>
                            <div class="action-btn-wrapper">
                                <a href="<?= url('/admin/applicants/' . $app['id']) ?>" class="action-btn">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <span class="action-label">View Detail</span>
                            </div>
                            <?php if (in_array($app['status_id'], [6, 7])): // Approved or Rejected ?>
                            <div class="action-btn-wrapper">
                                <button class="action-btn archive-btn" onclick="archiveApplication(<?= $app['id'] ?>, '<?= htmlspecialchars($app['applicant_name'], ENT_QUOTES) ?>')" title="Move to Archive">
                                    <i class="fas fa-archive"></i>
                                </button>
                                <span class="action-label">Archive</span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Move Modal -->
                    <div class="modal fade" id="moveModal<?= $app['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <form action="<?= url('/master-admin/pipeline/update-status') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="application_id" value="<?= $app['id'] ?>">
                                    <div class="modal-header">
                                        <h6 class="modal-title">Move to Status</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <select name="status_id" class="form-select" required>
                                            <?php foreach ($statuses as $s): ?>
                                            <option value="<?= $s['id'] ?>" <?= $s['id'] == $status['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($s['name']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary btn-sm">Move</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Reassign Handler Modal - Exact Mockup Match -->
<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
        <div class="modal-content reassign-modal">
            <!-- Header with Review Badge -->
            <div class="modal-header reassign-header">
                <h5 class="modal-title">Reassign Handler</h5>
                <span class="review-badge">Review</span>
                <button type="button" class="btn-close btn-close-white" onclick="closeTransferModal()"></button>
            </div>
            <div class="modal-body">
                <!-- Applicant Info Box -->
                <div class="applicant-info-box">
                    <div class="applicant-avatar-lg">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="applicant-details">
                        <span id="transferApplicantName">-</span> - <span id="transferVacancyTitle">-</span>
                    </div>
                </div>
                
                <!-- FROM → TO Section -->
                <div class="reassign-transfer-section">
                    <!-- FROM Box -->
                    <div class="from-section">
                        <div class="section-header from-header">FROM (Current)</div>
                        <div class="section-content">
                            <div class="handler-info-card">
                                <div class="handler-photo">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div class="handler-details">
                                    <strong id="fromCrewingName">Unassigned</strong>
                                    <span class="handler-role" id="fromCrewingRole">Crewing Staff</span>
                                </div>
                            </div>
                            <input type="hidden" id="fromCrewingId" value="0">
                        </div>
                    </div>
                    
                    <!-- Arrow -->
                    <div class="transfer-arrow-icon">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    
                    <!-- TO Box -->
                    <div class="to-section">
                        <div class="section-header to-header">TO (New Handler)</div>
                        <div class="section-content">
                            <select id="toCrewingId" class="form-select handler-select" required>
                                <option value="">Select New Handler</option>
                                <?php foreach ($crewingStaff as $crew): ?>
                                <option value="<?= $crew['id'] ?>" data-role="<?= $crew['role_id'] == 5 ? 'Crewing Manager' : ($crew['role_id'] == 10 ? 'HR Specialist' : 'Crewing Staff') ?>">
                                    <?= htmlspecialchars($crew['full_name']) ?> - <?= $crew['role_id'] == 5 ? 'Crewing Manager' : ($crew['role_id'] == 10 ? 'HR Specialist' : 'Crewing Staff') ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Optional Notes -->
                <div class="optional-notes-section">
                    <label class="notes-label">Optional Notes</label>
                    <textarea id="transferReason" class="form-control notes-textarea" placeholder="Enter reason for reassignment or additional instructions..."></textarea>
                </div>
            </div>
            <div class="modal-footer reassign-footer">
                <input type="hidden" id="transferAppId" value="">
                <button type="button" class="btn btn-cancel" onclick="closeTransferModal()">Cancel</button>
                <button type="button" class="btn btn-reassign" onclick="submitTransfer()">
                    Reassign Now
                </button>
            </div>
        </div>
    </div>
</div>
<style>
/* Stats Row */
.stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
.stat-card { background: white; border-radius: 12px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.2s; }
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0,0,0,0.12); }
.stat-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
.stat-card.total .stat-icon { background: #e3f2fd; color: #1976d2; }
.stat-card.assigned .stat-icon { background: #e8f5e9; color: #388e3c; }
.stat-card.unassigned .stat-icon { background: #ffebee; color: #d32f2f; }
.stat-card.workload .stat-icon { background: #fff3e0; color: #f57c00; }
.stat-info { display: flex; flex-direction: column; }
.stat-value { font-size: 1.75rem; font-weight: 700; color: #1a1a2e; }
.stat-label { font-size: 0.85rem; color: #666; }
.mini-chart { display: flex; flex-direction: column; gap: 4px; margin-top: 4px; }
.mini-bar { display: flex; align-items: center; gap: 6px; height: 14px; }
.bar-fill { height: 8px; background: linear-gradient(90deg, #f57c00, #ff9800); border-radius: 4px; min-width: 8px; }
.bar-label { font-size: 0.7rem; color: #666; font-weight: 600; }

/* Header */
.page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
.header-actions { display: flex; gap: 0.75rem; align-items: center; }
.filter-group select { min-width: 200px; }

/* Pipeline Board */
.pipeline-board { display: flex; gap: 1rem; overflow-x: auto; padding: 1rem 0; }
.pipeline-column { min-width: 340px; max-width: 360px; background: #f8f9fa; border-radius: 12px; overflow: hidden; flex-shrink: 0; }
.column-header { padding: 0.875rem 1rem; color: white; display: flex; justify-content: space-between; align-items: center; }
.column-header h5 { margin: 0; font-size: 0.95rem; font-weight: 600; }
.column-header .badge { background: rgba(255,255,255,0.3); padding: 0.3rem 0.6rem; border-radius: 6px; font-weight: 600; }
.column-body { padding: 0.75rem; max-height: 600px; overflow-y: auto; }
.empty-column { text-align: center; padding: 2.5rem 1rem; color: #aaa; }
.empty-column i { font-size: 2.5rem; display: block; margin-bottom: 0.75rem; }

/* Pipeline Card - Matching Mockup */
.pipeline-card { 
    background: white; 
    border-radius: 16px; 
    padding: 1.5rem; 
    margin-bottom: 1rem; 
    box-shadow: 0 2px 12px rgba(0,0,0,0.08); 
    transition: all 0.3s ease;
    border-left: 4px solid #2196f3;
    display: flex;
    flex-direction: column;
}
.pipeline-card:hover { 
    box-shadow: 0 8px 24px rgba(0,0,0,0.12); 
    transform: translateY(-2px); 
}
.pipeline-card.unassigned { 
    border-left-color: #dc3545; 
    background: linear-gradient(to right, #fff8f8, white); 
}

/* Card Name Section */
.card-name h4 { 
    margin: 0 0 0.25rem 0; 
    font-size: 1.25rem; 
    font-weight: 700; 
    color: #1a1a2e; 
}
.card-name .vacancy-title { 
    color: #666; 
    font-size: 0.9rem; 
}

/* Handler Section */
.handler-section { 
    margin: 1.25rem 0; 
    text-align: center; 
}
.handler-label { 
    font-size: 0.75rem; 
    font-weight: 600; 
    color: #888; 
    letter-spacing: 0.5px;
    display: block;
    margin-bottom: 0.75rem;
}
.handler-pill { 
    display: inline-flex; 
    align-items: center; 
    gap: 0.5rem; 
    padding: 0.6rem 1.25rem; 
    border-radius: 50px; 
    font-size: 0.9rem;
}
.handler-pill.assigned { 
    background: linear-gradient(135deg, #2e7d32, #4caf50); 
    color: white; 
}
.handler-pill.assigned i { font-size: 1.25rem; }
.handler-pill.assigned strong { font-weight: 600; }
.handler-pill.unassigned { 
    background: #ffebee; 
    color: #c62828; 
}

/* Action Buttons - Circular */
.card-action-buttons { 
    display: flex; 
    justify-content: center; 
    gap: 2rem; 
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}
.action-btn-wrapper { 
    display: flex; 
    flex-direction: column; 
    align-items: center; 
    gap: 0.5rem; 
}
.action-btn { 
    width: 50px; 
    height: 50px; 
    border-radius: 50%; 
    border: 2px solid #ddd; 
    background: #f8f9fa; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    cursor: pointer;
    transition: all 0.2s ease;
    color: #666;
    font-size: 1.1rem;
    text-decoration: none;
}
.action-btn:hover { 
    background: #e9ecef; 
    border-color: #bbb;
    transform: scale(1.05);
}
.action-label { 
    font-size: 0.7rem; 
    color: #666; 
    font-weight: 500;
    text-align: center;
}


/* Modal - Bootstrap Modal Override */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1050;
    width: 100%;
    height: 100%;
    overflow: hidden;
    outline: 0;
    background: rgba(0,0,0,0.5);
}
.modal.show {
    display: block !important;
}
.modal.fade .modal-dialog {
    transform: translateY(-50px);
    transition: transform 0.3s ease-out;
}
.modal.show .modal-dialog {
    transform: translateY(0);
}
#transferModal .modal-dialog {
    margin: 1.75rem auto;
    max-width: 600px;
}

/* Reassign Modal - Matching Mockup */
.reassign-modal { 
    border-radius: 16px; 
    overflow: hidden; 
    border: none;
    box-shadow: 0 10px 40px rgba(0,0,0,0.25);
}
.reassign-header { 
    background: linear-gradient(135deg, #4a90d9, #63b3ed); 
    color: white; 
    padding: 1rem 1.5rem;
    border: none;
    display: flex;
    align-items: center;
    gap: 1rem;
}
.reassign-header .modal-title { 
    font-weight: 600; 
    font-size: 1.25rem;
    flex: 1;
}
.review-badge {
    background: #28a745;
    color: white;
    padding: 0.35rem 0.75rem;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
}
.reassign-footer { 
    background: #f8f9fa; 
    border-top: none; 
    padding: 1rem 1.5rem;
    gap: 1rem;
}

/* Applicant Info Box */
.applicant-info-box { 
    display: flex; 
    align-items: center; 
    gap: 1rem; 
    background: #f0f4f8; 
    padding: 1rem 1.25rem; 
    border-radius: 12px;
    margin-bottom: 1.5rem;
}
.applicant-avatar-lg { 
    width: 50px; 
    height: 50px; 
    background: #d0d8e0; 
    border-radius: 50%; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    color: #5a6a7a;
    font-size: 1.5rem;
}
.applicant-details { 
    font-size: 1.05rem; 
    color: #333;
    font-weight: 500;
}

/* Transfer Section */
.reassign-transfer-section { 
    display: flex; 
    align-items: stretch; 
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}
.from-section, .to-section { 
    flex: 1; 
    border-radius: 12px; 
    overflow: hidden;
    background: white;
    border: 1px solid #e0e0e0;
}
.section-header { 
    padding: 0.6rem 1rem; 
    font-size: 0.8rem; 
    font-weight: 600; 
    color: white;
}
.from-header { background: linear-gradient(135deg, #78909c, #90a4ae); }
.to-header { background: linear-gradient(135deg, #43a047, #66bb6a); }
.section-content { 
    padding: 1rem; 
    background: white;
}

/* Handler Info Card */
.handler-info-card { 
    display: flex; 
    align-items: center; 
    gap: 0.75rem; 
}
.handler-photo { 
    width: 40px; 
    height: 40px; 
    background: #e3f2fd; 
    border-radius: 50%; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    color: #1976d2;
}
.handler-details strong { 
    display: block; 
    font-size: 0.95rem;
    color: #333;
}
.handler-role { 
    font-size: 0.75rem; 
    color: #888; 
    background: #f0f0f0;
    padding: 2px 8px;
    border-radius: 4px;
    display: inline-block;
    margin-top: 4px;
}

/* Transfer Arrow */
.transfer-arrow-icon { 
    display: flex; 
    align-items: center; 
    justify-content: center;
    color: #4a90d9;
    font-size: 1.5rem;
}

/* Handler Select */
.handler-select { 
    border-radius: 8px; 
    border: 1px solid #ddd;
    padding: 0.6rem;
}

/* Optional Notes */
.optional-notes-section { margin-top: 0; }
.notes-label { 
    font-size: 0.9rem; 
    color: #666; 
    margin-bottom: 0.5rem;
    display: block;
}
.notes-textarea { 
    border-radius: 8px; 
    border: 1px solid #ddd;
    resize: none;
    min-height: 80px;
}

/* Buttons */
.btn-cancel { 
    background: #e9ecef; 
    border: none; 
    color: #333; 
    padding: 0.6rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
}
.btn-cancel:hover { background: #ddd; }
.btn-reassign { 
    background: linear-gradient(135deg, #4a90d9, #63b3ed); 
    border: none; 
    color: white; 
    padding: 0.6rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
}
.btn-reassign:hover { 
    background: linear-gradient(135deg, #3a7fc9, #53a3dd); 
}
</style>

<script>
function filterByCrewing(value) {
    if (value === 'unassigned') {
        window.location.href = '<?= url('/master-admin/pipeline') ?>?unassigned=1';
    } else if (value) {
        window.location.href = '<?= url('/master-admin/pipeline') ?>?crewing=' + value;
    } else {
        window.location.href = '<?= url('/master-admin/pipeline') ?>';
    }
}

function openTransferModal(appId, appName, vacancyTitle, fromCrewingId, fromCrewingName) {
    document.getElementById('transferAppId').value = appId;
    document.getElementById('transferApplicantName').textContent = appName;
    document.getElementById('transferVacancyTitle').textContent = vacancyTitle || '-';
    document.getElementById('fromCrewingId').value = fromCrewingId || 0;
    document.getElementById('fromCrewingName').textContent = fromCrewingName || 'Unassigned';
    document.getElementById('toCrewingId').value = '';
    document.getElementById('transferReason').value = '';
    
    // Load crewing staff dynamically
    const dropdown = document.getElementById('toCrewingId');
    dropdown.innerHTML = '<option value="">Loading...</option>';
    
    fetch('<?= url('/master-admin/pipeline/crewing-staff') ?>')
        .then(r => r.json())
        .then(data => {
            if (data.success && data.data) {
                dropdown.innerHTML = '<option value="">Select New Handler</option>';
                data.data.forEach(crew => {
                    const role = crew.role_id == 5 ? 'Crewing Manager' : 'Crewing Staff';
                    dropdown.innerHTML += `<option value="${crew.id}">${crew.full_name} - ${role}</option>`;
                });
            }
        })
        .catch(() => {
            dropdown.innerHTML = '<option value="">Error loading staff</option>';
        });
    
    // Show modal by adding class
    document.getElementById('transferModal').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeTransferModal() {
    document.getElementById('transferModal').classList.remove('show');
    document.body.style.overflow = '';
}

function submitTransfer() {
    const appId = document.getElementById('transferAppId').value;
    const fromCrewingId = document.getElementById('fromCrewingId').value;
    const toCrewingId = document.getElementById('toCrewingId').value;
    const reason = document.getElementById('transferReason').value.trim();
    
    if (!toCrewingId) {
        alert('Please select a new handler');
        return;
    }
    
    fetch('<?= url('/master-admin/pipeline/transfer') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: new URLSearchParams({
            csrf_token: '<?= csrf_token() ?>',
            application_id: appId,
            from_crewing_id: fromCrewingId,
            to_crewing_id: toCrewingId,
            reason: reason || 'Reassigned'
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            transferModal.hide();
            window.location.reload();
        } else {
            alert('❌ Error: ' + data.message);
        }
    })
    .catch(err => {
        alert('Error: ' + err.message);
    });
}

// Archive Application Function
function archiveApplication(appId, applicantName) {
    if (!confirm(`Archive application from ${applicantName}?\n\nThis will move the application to archive and remove it from the active pipeline.`)) {
        return;
    }
    
    fetch('<?= url('/master-admin/archive/archive/') ?>' + appId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'csrf_token=<?= csrf_token() ?>'
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            window.location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(err => {
        alert('Error: ' + err.message);
    });
}
</script>
