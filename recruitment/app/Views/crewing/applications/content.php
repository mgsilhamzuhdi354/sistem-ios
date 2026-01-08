<div class="page-header">
    <h1><i class="fas fa-file-alt"></i> My Applications</h1>
    <div class="header-actions">
        <div class="view-toggle">
            <a href="<?= url('/crewing/applications?view=my') ?>" class="btn <?= ($filters['view'] ?? 'my') === 'my' ? 'btn-primary' : 'btn-outline' ?>">
                <i class="fas fa-user"></i> My Assignments
            </a>
            <a href="<?= url('/crewing/applications?view=all') ?>" class="btn <?= ($filters['view'] ?? 'my') === 'all' ? 'btn-primary' : 'btn-outline' ?>">
                <i class="fas fa-globe"></i> All Applications
            </a>
        </div>
    </div>
</div>

<!-- Status Filter Tabs -->
<div class="status-tabs">
    <a href="<?= url('/crewing/applications?view=' . ($filters['view'] ?? 'my')) ?>" 
       class="status-tab <?= empty($filters['status']) ? 'active' : '' ?>">
        All
    </a>
    <?php foreach ($statusCounts as $status): ?>
    <a href="<?= url('/crewing/applications?view=' . ($filters['view'] ?? 'my') . '&status=' . $status['id']) ?>" 
       class="status-tab <?= ($filters['status'] ?? '') == $status['id'] ? 'active' : '' ?>"
       style="--status-color: <?= $status['color'] ?>">
        <?= $status['name'] ?>
        <span class="count"><?= $status['count'] ?></span>
    </a>
    <?php endforeach; ?>
</div>

<!-- Filters -->
<div class="filters-bar">
    <form method="GET" action="<?= url('/crewing/applications') ?>" class="filter-form">
        <input type="hidden" name="view" value="<?= $filters['view'] ?? 'my' ?>">
        
        <div class="filter-group">
            <select name="department" class="form-control">
                <option value="">All Departments</option>
                <?php foreach ($departments as $dept): ?>
                <option value="<?= $dept['id'] ?>" <?= ($filters['department'] ?? '') == $dept['id'] ? 'selected' : '' ?>>
                    <?= $dept['name'] ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="filter-group">
            <select name="priority" class="form-control">
                <option value="">All Priorities</option>
                <option value="urgent" <?= ($filters['priority'] ?? '') === 'urgent' ? 'selected' : '' ?>>🔴 Urgent</option>
                <option value="high" <?= ($filters['priority'] ?? '') === 'high' ? 'selected' : '' ?>>🟠 High</option>
                <option value="normal" <?= ($filters['priority'] ?? '') === 'normal' ? 'selected' : '' ?>>🟢 Normal</option>
                <option value="low" <?= ($filters['priority'] ?? '') === 'low' ? 'selected' : '' ?>>⚪ Low</option>
            </select>
        </div>
        
        <div class="filter-group search-group">
            <i class="fas fa-search"></i>
            <input type="text" name="search" placeholder="Search applicant or vacancy..." 
                   value="<?= htmlspecialchars($filters['search'] ?? '') ?>" class="form-control">
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-filter"></i> Filter
        </button>
        
        <a href="<?= url('/crewing/applications?view=' . ($filters['view'] ?? 'my')) ?>" class="btn btn-outline">
            <i class="fas fa-times"></i> Clear
        </a>
    </form>
</div>

<!-- Applications Table -->
<div class="table-responsive">
    <table class="data-table">
        <thead>
            <tr>
                <th>Applicant</th>
                <th>Vacancy</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Crewing PIC</th>
                <th>Rank</th>
                <th>Company</th>
                <th>Submitted</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($applications)): ?>
            <tr>
                <td colspan="9" class="text-center">
                    <div class="empty-table">
                        <i class="fas fa-inbox"></i>
                        <p>No applications found</p>
                    </div>
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($applications as $app): ?>
            <tr class="priority-<?= $app['priority'] ?? 'normal' ?>">
                <td>
                    <div class="applicant-cell">
                        <img src="<?= $app['avatar'] ? asset('uploads/avatars/' . $app['avatar']) : asset('images/avatar-default.png') ?>" 
                             alt="Avatar" class="avatar-sm">
                        <div class="applicant-info">
                            <strong><?= htmlspecialchars($app['full_name']) ?></strong>
                            <small><?= htmlspecialchars($app['email']) ?></small>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="vacancy-title"><?= htmlspecialchars($app['vacancy_title']) ?></span>
                    <small class="department-name"><?= htmlspecialchars($app['department_name'] ?? '') ?></small>
                </td>
                <td>
                    <span class="status-badge" style="background-color: <?= $app['status_color'] ?>">
                        <?= $app['status_name'] ?>
                    </span>
                </td>
                <td>
                    <?php
                    $priorityIcons = [
                        'urgent' => '🔴',
                        'high' => '🟠',
                        'normal' => '🟢',
                        'low' => '⚪'
                    ];
                    echo $priorityIcons[$app['priority'] ?? 'normal'] . ' ' . ucfirst($app['priority'] ?? 'normal');
                    ?>
                </td>
                <td>
                    <?php if ($app['assigned_to_name']): ?>
                    <div class="d-flex align-items-center">
                        <span class="online-indicator <?= isset($app['crewing_online']) && $app['crewing_online'] ? 'online' : 'offline' ?> me-2" style="width: 8px; height: 8px; border-radius: 50%; display: inline-block; background: <?= isset($app['crewing_online']) && $app['crewing_online'] ? '#22c55e' : '#9ca3af' ?>;"></span>
                        <span class="assigned-badge">
                            <i class="fas fa-user"></i>
                            <?= htmlspecialchars($app['assigned_to_name']) ?>
                        </span>
                    </div>
                    <?php else: ?>
                    <span class="unassigned-badge">Unassigned</span>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="rank-badge"><?= htmlspecialchars($app['crewing_rank'] ?? '-') ?></span>
                </td>
                <td>
                    <span class="company-badge"><?= htmlspecialchars($app['crewing_company'] ?? '-') ?></span>
                </td>
                <td>
                    <span class="date-text"><?= date('M d, Y', strtotime($app['submitted_at'])) ?></span>
                    <small class="time-text"><?= date('H:i', strtotime($app['submitted_at'])) ?></small>
                </td>
                <td>
                    <div class="action-buttons">
                        <a href="<?= url('/crewing/applications/' . $app['id']) ?>" class="btn btn-sm btn-primary" title="View Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline assign-btn" 
                                data-app-id="<?= $app['id'] ?>" 
                                data-app-name="<?= htmlspecialchars($app['full_name']) ?>"
                                title="Assign">
                            <i class="fas fa-user-plus"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Assign Modal -->
<div class="modal" id="assignModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> Assign Application</h3>
            <button class="modal-close">&times;</button>
        </div>
        <form method="POST" action="" id="assignForm">
            <?= csrf_field() ?>
            <div class="modal-body">
                <p>Assigning application for: <strong id="assignApplicantName"></strong></p>
                
                <div class="form-group">
                    <label>Assign to Crewing Staff</label>
                    <select name="assign_to" required class="form-control">
                        <option value="">Select Crewing Staff...</option>
                        <?php foreach ($crewingStaff as $crew): ?>
                        <option value="<?= $crew['id'] ?>">
                            <?= htmlspecialchars($crew['full_name']) ?>
                            (<?= $crew['active_assignments'] ?> active)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Notes (optional)</label>
                    <textarea name="notes" rows="3" class="form-control" 
                              placeholder="Add notes for the assigned crewing..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline modal-cancel">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> Assign
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('assignModal');
    const form = document.getElementById('assignForm');
    const applicantName = document.getElementById('assignApplicantName');
    
    // Open modal
    document.querySelectorAll('.assign-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const appId = this.dataset.appId;
            const name = this.dataset.appName;
            
            form.action = '<?= url('/crewing/applications/assign/') ?>' + appId;
            applicantName.textContent = name;
            modal.classList.add('show');
        });
    });
    
    // Close modal
    modal.querySelector('.modal-close').addEventListener('click', () => modal.classList.remove('show'));
    modal.querySelector('.modal-cancel').addEventListener('click', () => modal.classList.remove('show'));
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.remove('show');
    });
});
</script>
