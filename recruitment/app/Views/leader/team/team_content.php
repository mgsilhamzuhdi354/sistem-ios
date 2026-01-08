<!-- Leader Team Management Content -->
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1"><i class="fas fa-users-cog me-2"></i>Team Management</h1>
        <p class="text-muted mb-0">Manage your crewing staff and transfer applications</p>
    </div>
    <div class="d-flex gap-2">
        <div class="stat-mini">
            <span class="stat-value"><?= count($crewingStaff) ?></span>
            <span class="stat-label">Staff Members</span>
        </div>
        <div class="stat-mini online">
            <span class="stat-value"><?= count(array_filter($crewingStaff, fn($c) => $c['is_online'])) ?></span>
            <span class="stat-label">Online Now</span>
        </div>
    </div>
</div>

<!-- Team Members Grid -->
<div class="row g-4 mb-4">
    <?php foreach ($crewingStaff as $crewing): ?>
    <div class="col-lg-4 col-md-6">
        <div class="team-card">
            <div class="team-card-header">
                <div class="avatar-wrapper">
                    <div class="avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <span class="status-dot <?= $crewing['is_online'] ? 'online' : 'offline' ?>"></span>
                </div>
                <div class="team-info">
                    <h5 class="name"><?= htmlspecialchars($crewing['full_name']) ?></h5>
                    <span class="role"><?= $crewing['rank'] ?? 'Crewing Staff' ?></span>
                    <span class="company"><?= $crewing['company'] ?? 'PT Indo Ocean' ?></span>
                </div>
            </div>
            
            <div class="team-card-body">
                <!-- Workload Bar -->
                <?php 
                $maxApps = $crewing['max_applications'] ?? 50;
                $activeCount = $crewing['active_count'];
                $percentage = min(100, ($activeCount / $maxApps) * 100);
                $barClass = $percentage >= 80 ? 'danger' : ($percentage >= 50 ? 'warning' : 'success');
                ?>
                <div class="workload-section">
                    <div class="workload-label">
                        <span>Workload</span>
                        <span class="workload-value"><?= $activeCount ?> / <?= $maxApps ?></span>
                    </div>
                    <div class="workload-bar">
                        <div class="workload-progress <?= $barClass ?>" style="width: <?= $percentage ?>%"></div>
                    </div>
                </div>
                
                <!-- Stats Row -->
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="value text-primary"><?= $crewing['active_count'] ?></span>
                        <span class="label">Active</span>
                    </div>
                    <div class="stat-item">
                        <span class="value text-success"><?= $crewing['completed_count'] ?></span>
                        <span class="label">Completed</span>
                    </div>
                    <div class="stat-item">
                        <span class="value text-warning">
                            <?= $crewing['avg_rating'] ? number_format($crewing['avg_rating'], 1) : '-' ?>
                        </span>
                        <span class="label">Rating</span>
                    </div>
                </div>
                
                <!-- Rating Stars -->
                <div class="rating-stars">
                    <?php 
                    $rating = round($crewing['avg_rating'] ?? 0);
                    for ($s = 1; $s <= 5; $s++): 
                    ?>
                    <i class="fas fa-star <?= $s <= $rating ? 'active' : '' ?>"></i>
                    <?php endfor; ?>
                    <span class="reviews">(<?= $crewing['total_ratings'] ?> reviews)</span>
                </div>
                
                <!-- Online Status -->
                <div class="online-status">
                    <?php if ($crewing['is_online']): ?>
                    <span class="online-badge"><i class="fas fa-circle"></i> Online Now</span>
                    <?php else: ?>
                    <span class="offline-text">Last seen: <?= $crewing['last_activity'] ? date('d M, H:i', strtotime($crewing['last_activity'])) : 'Never' ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Transfer Applications Section -->
<div class="transfer-section">
    <div class="section-header">
        <div>
            <h5><i class="fas fa-exchange-alt me-2"></i>Transfer Applications</h5>
            <p class="text-muted mb-0">Reassign applications between crewing staff</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#transferModal">
            <i class="fas fa-plus me-1"></i> New Transfer
        </button>
    </div>
    
    <div class="table-container">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Applicant</th>
                    <th>Vacancy</th>
                    <th>Status</th>
                    <th>Current PIC</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($applications, 0, 15) as $app): ?>
                <tr>
                    <td>
                        <div class="applicant-cell">
                            <div class="avatar-sm">
                                <i class="fas fa-user"></i>
                            </div>
                            <span><?= htmlspecialchars($app['applicant_name']) ?></span>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($app['vacancy_title']) ?></td>
                    <td><span class="status-badge"><?= $app['status_name'] ?></span></td>
                    <td>
                        <div class="pic-cell">
                            <span class="dot online"></span>
                            <?= htmlspecialchars($app['crewing_name'] ?? 'Unassigned') ?>
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn-transfer" 
                                onclick="openTransferModal(<?= $app['id'] ?>, <?= $app['crewing_id'] ?? 0 ?>, '<?= htmlspecialchars($app['applicant_name']) ?>')">
                            <i class="fas fa-arrows-alt-h"></i> Transfer
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Transfer Modal -->
<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modern-modal">
            <form action="<?= url('/leader/team/transfer') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <div class="modal-icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <h5 class="modal-title">Transfer Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="application_id" id="transferAppId">
                    <input type="hidden" name="from_crewing_id" id="transferFromId">
                    
                    <div class="transfer-info">
                        <i class="fas fa-file-alt"></i>
                        <span>Transferring: <strong id="transferAppName"></strong></span>
                    </div>
                    
                    <div class="form-group">
                        <label>Transfer to:</label>
                        <select name="to_crewing_id" class="form-select modern-select" required>
                            <option value="">Select Crewing Staff...</option>
                            <?php foreach ($crewingStaff as $crewing): ?>
                            <option value="<?= $crewing['id'] ?>">
                                <?= htmlspecialchars($crewing['full_name']) ?> 
                                (<?= $crewing['active_count'] ?>/<?= $crewing['max_applications'] ?? 50 ?>)
                                <?= $crewing['is_online'] ? '🟢' : '⚪' ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Reason (optional):</label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="Why are you transferring this application?"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-1"></i> Confirm Transfer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Header Stats Mini */
.stat-mini {
    background: white;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    text-align: center;
}
.stat-mini .stat-value {
    display: block;
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e3a5f;
}
.stat-mini .stat-label {
    font-size: 0.7rem;
    color: #888;
    text-transform: uppercase;
}
.stat-mini.online .stat-value { color: #22c55e; }

/* Team Card */
.team-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
}
.team-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}
.team-card-header {
    background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}
.avatar-wrapper {
    position: relative;
}
.avatar {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}
.status-dot {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 2px solid #1e3a5f;
}
.status-dot.online { background: #22c55e; }
.status-dot.offline { background: #94a3b8; }
.team-info .name {
    color: white;
    margin: 0;
    font-size: 1.1rem;
}
.team-info .role {
    display: block;
    color: rgba(255,255,255,0.8);
    font-size: 0.85rem;
}
.team-info .company {
    display: block;
    color: rgba(255,255,255,0.6);
    font-size: 0.75rem;
}
.team-card-body {
    padding: 1.25rem;
}

/* Workload */
.workload-section { margin-bottom: 1rem; }
.workload-label {
    display: flex;
    justify-content: space-between;
    font-size: 0.8rem;
    margin-bottom: 0.5rem;
}
.workload-bar {
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
}
.workload-progress {
    height: 100%;
    border-radius: 4px;
    transition: width 0.5s;
}
.workload-progress.success { background: linear-gradient(90deg, #22c55e, #16a34a); }
.workload-progress.warning { background: linear-gradient(90deg, #f59e0b, #d97706); }
.workload-progress.danger { background: linear-gradient(90deg, #ef4444, #dc2626); }

/* Stats Row */
.stats-row {
    display: flex;
    justify-content: space-around;
    padding: 1rem 0;
    border-bottom: 1px solid #f3f4f6;
    margin-bottom: 1rem;
}
.stat-item { text-align: center; }
.stat-item .value {
    display: block;
    font-size: 1.25rem;
    font-weight: 700;
}
.stat-item .label {
    font-size: 0.7rem;
    color: #888;
    text-transform: uppercase;
}

/* Rating Stars */
.rating-stars {
    text-align: center;
    margin-bottom: 0.75rem;
}
.rating-stars .fa-star {
    color: #e5e7eb;
    font-size: 0.9rem;
}
.rating-stars .fa-star.active { color: #f59e0b; }
.rating-stars .reviews {
    font-size: 0.75rem;
    color: #888;
    margin-left: 0.5rem;
}

/* Online Status */
.online-status { text-align: center; }
.online-badge {
    color: #22c55e;
    font-size: 0.8rem;
    font-weight: 500;
}
.online-badge i { font-size: 8px; margin-right: 4px; }
.offline-text {
    color: #94a3b8;
    font-size: 0.8rem;
}

/* Transfer Section */
.transfer-section {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
}
.section-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.section-header h5 { margin: 0; }
.table-container { padding: 1rem; }

/* Modern Table */
.modern-table {
    width: 100%;
    border-collapse: collapse;
}
.modern-table th {
    background: #f8fafc;
    padding: 0.75rem 1rem;
    text-align: left;
    font-size: 0.75rem;
    text-transform: uppercase;
    color: #64748b;
    font-weight: 600;
}
.modern-table td {
    padding: 1rem;
    border-bottom: 1px solid #f3f4f6;
}
.applicant-cell, .pic-cell {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.avatar-sm {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.75rem;
}
.status-badge {
    background: #e5e7eb;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
}
.dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #22c55e;
}
.btn-transfer {
    background: linear-gradient(90deg, #f59e0b, #d97706);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.8rem;
    cursor: pointer;
    transition: transform 0.2s;
}
.btn-transfer:hover { transform: scale(1.05); }

/* Modal */
.modern-modal .modal-header {
    background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
    color: white;
    padding: 1.5rem;
}
.modal-icon {
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    margin-right: 1rem;
}
.transfer-info {
    background: #f0f9ff;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    color: #0369a1;
}
.transfer-info i { margin-right: 0.5rem; }
.form-group { margin-bottom: 1rem; }
.form-group label {
    display: block;
    font-weight: 500;
    margin-bottom: 0.5rem;
}
</style>

<script>
function openTransferModal(appId, fromId, appName) {
    document.getElementById('transferAppId').value = appId;
    document.getElementById('transferFromId').value = fromId || 0;
    document.getElementById('transferAppName').textContent = appName;
    new bootstrap.Modal(document.getElementById('transferModal')).show();
}
</script>
