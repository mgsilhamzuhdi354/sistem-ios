<!-- Crewing Team Workload Page -->
<div class="page-header-modern">
    <div class="header-content">
        <div class="header-icon">
            <i class="fas fa-users-cog"></i>
        </div>
        <div>
            <h1>Team Workload</h1>
            <p>Monitor and manage team assignments</p>
        </div>
    </div>
    <div class="header-actions">
        <?php if ($unassignedCount > 0): ?>
        <form method="POST" action="<?= url('/crewing/team/auto-assign-all') ?>" style="display: inline;">
            <?= csrf_field() ?>
            <button type="submit" class="btn-action-gradient" onclick="return confirm('Auto-assign all unassigned applications?')">
                <i class="fas fa-magic"></i> Auto-Assign All
                <span class="count-badge"><?= $unassignedCount ?></span>
            </button>
        </form>
        <?php endif; ?>
    </div>
</div>

<!-- Team Workload Cards -->
<div class="team-grid-modern">
    <?php foreach ($crewingStaff as $crew): ?>
    <div class="team-card-modern <?= !$crew['is_active'] ? 'inactive' : '' ?>">
        <div class="card-header-gradient">
            <div class="avatar-wrapper">
                <div class="avatar-circle">
                    <?php 
                    $initials = strtoupper(substr($crew['full_name'], 0, 2));
                    ?>
                    <span><?= $initials ?></span>
                </div>
                <?php if ($crew['is_pic']): ?>
                <span class="pic-badge-new"><i class="fas fa-star"></i></span>
                <?php endif; ?>
            </div>
            <div class="card-header-info">
                <h3><?= htmlspecialchars($crew['full_name']) ?></h3>
                <?php if ($crew['employee_id']): ?>
                <span class="emp-id"><?= $crew['employee_id'] ?></span>
                <?php endif; ?>
                <span class="email-badge"><?= htmlspecialchars($crew['email']) ?></span>
            </div>
            <?php if (!$crew['is_active']): ?>
            <span class="inactive-ribbon">Inactive</span>
            <?php endif; ?>
        </div>
        
        <div class="card-body-modern">
            <!-- Workload Progress -->
            <div class="workload-container">
                <div class="workload-header">
                    <span class="workload-title"><i class="fas fa-tasks"></i> Workload</span>
                    <?php 
                    $max = $crew['max_applications'] ?: 50;
                    $percentage = min(100, ($crew['active_assignments'] / $max) * 100);
                    ?>
                    <span class="workload-percent"><?= round($percentage) ?>%</span>
                </div>
                <div class="progress-bar-modern">
                    <?php
                    $barClass = $percentage >= 80 ? 'danger' : ($percentage >= 50 ? 'warning' : 'success');
                    ?>
                    <div class="progress-fill <?= $barClass ?>" style="width: <?= $percentage ?>%"></div>
                </div>
                <div class="workload-count">
                    <span><?= $crew['active_assignments'] ?></span> / <span><?= $max ?></span> slots
                </div>
            </div>
            
            <!-- Stats Grid -->
            <div class="stats-grid-modern">
                <div class="stat-box pending">
                    <div class="stat-number"><?= $crew['pending_review'] ?></div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-box interview">
                    <div class="stat-number"><?= $crew['in_interview'] ?></div>
                    <div class="stat-label">Interview</div>
                </div>
                <div class="stat-box hired">
                    <div class="stat-number"><?= $crew['hired_month'] ?></div>
                    <div class="stat-label">Hired</div>
                </div>
                <div class="stat-box completed">
                    <div class="stat-number"><?= $crew['completed_month'] ?></div>
                    <div class="stat-label">Done</div>
                </div>
            </div>
            
            <!-- Additional Info -->
            <?php if ($crew['specialization']): ?>
            <div class="info-tag">
                <i class="fas fa-award"></i>
                <span><?= htmlspecialchars($crew['specialization']) ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ($crew['department_ids']): ?>
            <div class="info-tag">
                <i class="fas fa-building"></i>
                <?php 
                $deptIds = json_decode($crew['department_ids'], true);
                if ($deptIds && isset($departmentsMap)) {
                    $deptNames = [];
                    foreach ($deptIds as $id) {
                        if (isset($departmentsMap[$id])) {
                            $deptNames[] = $departmentsMap[$id];
                        }
                    }
                    echo implode(', ', $deptNames) ?: 'All Departments';
                } else {
                    echo 'All Departments';
                }
                ?>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="card-footer-modern">
            <a href="<?= url('/crewing/pipeline?view=team&crewing=' . $crew['id']) ?>" class="btn-footer view">
                <i class="fas fa-stream"></i> Pipeline
            </a>
            <button type="button" class="btn-footer assign assign-to-btn" 
                    data-crewing-id="<?= $crew['id'] ?>"
                    data-crewing-name="<?= htmlspecialchars($crew['full_name']) ?>">
                <i class="fas fa-user-plus"></i> Assign
            </button>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if ($unassignedCount > 0): ?>
<!-- Unassigned Applications Section -->
<div class="unassigned-section-modern">
    <div class="section-header-modern">
        <div class="section-icon warning">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div>
            <h2>Unassigned Applications</h2>
            <p><?= $unassignedCount ?> applications need to be assigned</p>
        </div>
    </div>
    
    <form method="POST" action="<?= url('/crewing/team/bulk-assign') ?>" id="bulkAssignForm">
        <?= csrf_field() ?>
        
        <div class="bulk-actions-modern">
            <div class="select-control">
                <input type="checkbox" id="selectAll" class="checkbox-modern">
                <label for="selectAll">Select All</label>
            </div>
            
            <div class="assign-controls">
                <select name="assign_to" class="select-modern" required>
                    <option value="">Assign to...</option>
                    <?php foreach ($crewingStaff as $crew): ?>
                    <?php if ($crew['is_active']): ?>
                    <option value="<?= $crew['id'] ?>">
                        <?= htmlspecialchars($crew['full_name']) ?> (<?= $crew['active_assignments'] ?> active)
                    </option>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="notes" class="input-modern" placeholder="Notes (optional)">
                <button type="submit" class="btn-assign-modern" id="bulkAssignBtn" disabled>
                    <i class="fas fa-user-plus"></i> 
                    <span>Assign</span>
                    <span class="selected-count">(0)</span>
                </button>
            </div>
        </div>
        
        <div class="table-modern-container">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th width="40"><input type="checkbox" id="selectAllHeader" class="checkbox-modern"></th>
                        <th>Applicant</th>
                        <th>Vacancy</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($unassignedApps as $app): ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="application_ids[]" value="<?= $app['id'] ?>" class="app-checkbox checkbox-modern">
                        </td>
                        <td>
                            <div class="applicant-cell">
                                <div class="applicant-avatar">
                                    <?= strtoupper(substr($app['full_name'], 0, 2)) ?>
                                </div>
                                <span><?= htmlspecialchars($app['full_name']) ?></span>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($app['vacancy_title']) ?></td>
                        <td>
                            <span class="status-pill" style="background-color: <?= $app['status_color'] ?>">
                                <?= $app['status_name'] ?>
                            </span>
                        </td>
                        <td>
                            <span class="date-cell">
                                <i class="fas fa-calendar"></i>
                                <?= date('M d, Y', strtotime($app['submitted_at'])) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= url('/crewing/applications/' . $app['id']) ?>" class="btn-view-mini">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </form>
</div>
<?php endif; ?>

<style>
/* Page Header Modern */
.page-header-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
    border-radius: 16px;
    color: white;
}
.header-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}
.header-icon {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
.header-content h1 {
    margin: 0;
    font-size: 1.5rem;
}
.header-content p {
    margin: 0;
    opacity: 0.8;
    font-size: 0.9rem;
}
.btn-action-gradient {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: transform 0.2s;
}
.btn-action-gradient:hover {
    transform: translateY(-2px);
}
.count-badge {
    background: rgba(255,255,255,0.3);
    padding: 0.25rem 0.5rem;
    border-radius: 20px;
    font-size: 0.8rem;
}

/* Team Grid */
.team-grid-modern {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

/* Team Card */
.team-card-modern {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s;
}
.team-card-modern:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.15);
}
.team-card-modern.inactive {
    opacity: 0.5;
}

.card-header-gradient {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
}
.avatar-wrapper {
    position: relative;
}
.avatar-circle {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.25);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.25rem;
    border: 3px solid rgba(255,255,255,0.4);
}
.pic-badge-new {
    position: absolute;
    bottom: -4px;
    right: -4px;
    width: 24px;
    height: 24px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.6rem;
    border: 2px solid white;
}
.card-header-info {
    flex: 1;
    color: white;
}
.card-header-info h3 {
    margin: 0;
    font-size: 1.1rem;
}
.emp-id {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    padding: 0.125rem 0.5rem;
    border-radius: 4px;
    font-size: 0.7rem;
    margin-top: 0.25rem;
}
.email-badge {
    display: block;
    font-size: 0.75rem;
    opacity: 0.8;
    margin-top: 0.25rem;
}
.inactive-ribbon {
    position: absolute;
    top: 10px;
    right: -30px;
    background: #ef4444;
    color: white;
    padding: 0.25rem 2rem;
    font-size: 0.7rem;
    font-weight: 600;
    transform: rotate(45deg);
}

.card-body-modern {
    padding: 1.5rem;
}

/* Workload */
.workload-container {
    margin-bottom: 1.25rem;
}
.workload-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}
.workload-title {
    font-size: 0.8rem;
    color: #64748b;
}
.workload-title i {
    margin-right: 0.25rem;
}
.workload-percent {
    font-weight: 700;
    font-size: 0.9rem;
}
.progress-bar-modern {
    height: 10px;
    background: #e5e7eb;
    border-radius: 5px;
    overflow: hidden;
}
.progress-fill {
    height: 100%;
    border-radius: 5px;
    transition: width 0.5s ease;
}
.progress-fill.success { background: linear-gradient(90deg, #22c55e, #16a34a); }
.progress-fill.warning { background: linear-gradient(90deg, #f59e0b, #d97706); }
.progress-fill.danger { background: linear-gradient(90deg, #ef4444, #dc2626); }
.workload-count {
    text-align: right;
    font-size: 0.75rem;
    color: #9ca3af;
    margin-top: 0.25rem;
}

/* Stats Grid */
.stats-grid-modern {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.75rem;
    margin-bottom: 1rem;
}
.stat-box {
    text-align: center;
    padding: 0.75rem 0.5rem;
    border-radius: 10px;
}
.stat-box.pending { background: #fef3c7; }
.stat-box.interview { background: #dbeafe; }
.stat-box.hired { background: #dcfce7; }
.stat-box.completed { background: #f3e8ff; }
.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
}
.stat-box.pending .stat-number { color: #d97706; }
.stat-box.interview .stat-number { color: #2563eb; }
.stat-box.hired .stat-number { color: #16a34a; }
.stat-box.completed .stat-number { color: #7c3aed; }
.stat-label {
    font-size: 0.65rem;
    text-transform: uppercase;
    color: #6b7280;
    font-weight: 500;
}

/* Info Tags */
.info-tag {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8rem;
    color: #64748b;
    padding: 0.5rem 0;
    border-top: 1px solid #f3f4f6;
}
.info-tag i {
    color: #f59e0b;
}

/* Card Footer */
.card-footer-modern {
    display: flex;
    gap: 0.5rem;
    padding: 1rem 1.5rem;
    background: #f8fafc;
}
.btn-footer {
    flex: 1;
    padding: 0.75rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 500;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}
.btn-footer.view {
    background: #f1f5f9;
    color: #475569;
}
.btn-footer.view:hover {
    background: #e2e8f0;
}
.btn-footer.assign {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
}
.btn-footer.assign:hover {
    transform: translateY(-2px);
}

/* Unassigned Section */
.unassigned-section-modern {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.section-header-modern {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f3f4f6;
}
.section-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}
.section-icon.warning {
    background: #fef3c7;
    color: #d97706;
}
.section-header-modern h2 {
    margin: 0;
    font-size: 1.25rem;
}
.section-header-modern p {
    margin: 0;
    color: #6b7280;
    font-size: 0.85rem;
}

/* Bulk Actions */
.bulk-actions-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 10px;
    margin-bottom: 1rem;
}
.select-control {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.checkbox-modern {
    width: 18px;
    height: 18px;
    accent-color: #3b82f6;
}
.assign-controls {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}
.select-modern, .input-modern {
    padding: 0.625rem 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.9rem;
    min-width: 180px;
}
.btn-assign-modern {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    border: none;
    padding: 0.625rem 1.25rem;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.btn-assign-modern:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Table Modern */
.table-modern-container {
    overflow-x: auto;
}
.table-modern {
    width: 100%;
    border-collapse: collapse;
}
.table-modern th {
    background: #f8fafc;
    padding: 0.75rem 1rem;
    text-align: left;
    font-size: 0.75rem;
    text-transform: uppercase;
    color: #64748b;
    font-weight: 600;
}
.table-modern td {
    padding: 1rem;
    border-bottom: 1px solid #f3f4f6;
}
.applicant-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.applicant-avatar {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 600;
}
.status-pill {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    color: white;
    font-size: 0.75rem;
    font-weight: 500;
}
.date-cell {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #6b7280;
    font-size: 0.85rem;
}
.date-cell i {
    color: #9ca3af;
}
.btn-view-mini {
    width: 32px;
    height: 32px;
    background: #f1f5f9;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    text-decoration: none;
    transition: all 0.2s;
}
.btn-view-mini:hover {
    background: #3b82f6;
    color: white;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.app-checkbox');
    const selectAll = document.getElementById('selectAll');
    const selectAllHeader = document.getElementById('selectAllHeader');
    const bulkAssignBtn = document.getElementById('bulkAssignBtn');
    const selectedCount = document.querySelector('.selected-count');
    
    function updateBulkBtn() {
        const checked = document.querySelectorAll('.app-checkbox:checked').length;
        if (bulkAssignBtn) {
            bulkAssignBtn.disabled = checked === 0;
        }
        if (selectedCount) {
            selectedCount.textContent = `(${checked})`;
        }
    }
    
    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkBtn);
    });
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkBtn();
        });
    }
    
    if (selectAllHeader) {
        selectAllHeader.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            if (selectAll) selectAll.checked = this.checked;
            updateBulkBtn();
        });
    }
});
</script>
