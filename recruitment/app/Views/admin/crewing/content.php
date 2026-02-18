<div class="page-header">
    <h1><i class="fas fa-user-tie"></i> Crewing Management</h1>
    <a href="<?= url('/admin/crewing/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Crewing Staff
    </a>
</div>

<div class="card">
    <div class="card-body">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Staff</th>
                    <th>Employee ID</th>
                    <th>Role</th>
                    <th>Active Assignments</th>
                    <th>Completed</th>
                    <th>Specialization</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($crewingStaff)): ?>
                <tr>
                    <td colspan="8" class="text-center">
                        <p>No crewing staff found. <a href="<?= url('/admin/crewing/create') ?>">Add one now</a>.</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($crewingStaff as $crew): ?>
                <tr>
                    <td>
                        <div class="user-cell">
                            <img src="<?= $crew['avatar'] ? asset('uploads/avatars/' . $crew['avatar']) : asset('images/avatar-default.svg') ?>" 
                                 alt="Avatar" class="avatar-sm">
                            <div class="user-info">
                                <strong><?= htmlspecialchars($crew['full_name']) ?></strong>
                                <small><?= htmlspecialchars($crew['email']) ?></small>
                            </div>
                        </div>
                    </td>
                    <td><?= $crew['employee_id'] ?: '-' ?></td>
                    <td>
                        <?php if ($crew['is_pic']): ?>
                        <span class="badge badge-warning">PIC</span>
                        <?php else: ?>
                        <span class="badge badge-secondary">Staff</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong><?= $crew['active_assignments'] ?></strong> / <?= $crew['max_applications'] ?: 50 ?>
                        <?php 
                        $max = $crew['max_applications'] ?: 50;
                        $percentage = $max > 0 ? min(100, ($crew['active_assignments'] / $max) * 100) : 0;
                        $barClass = $percentage >= 80 ? 'danger' : ($percentage >= 50 ? 'warning' : 'success');
                        ?>
                        <div class="mini-progress">
                            <div class="mini-progress-bar <?= $barClass ?>" style="width: <?= $percentage ?>%"></div>
                        </div>
                    </td>
                    <td><?= $crew['total_completed'] ?></td>
                    <td><?= $crew['specialization'] ?: '-' ?></td>
                    <td>
                        <?php if ($crew['is_active']): ?>
                        <span class="badge badge-success">Active</span>
                        <?php else: ?>
                        <span class="badge badge-danger">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="<?= url('/crewing/pipeline?view=team&crewing=' . $crew['id']) ?>" 
                               class="btn btn-sm btn-outline" title="View Pipeline">
                                <i class="fas fa-stream"></i>
                            </a>
                            <a href="<?= url('/admin/crewing/edit/' . $crew['id']) ?>" 
                               class="btn btn-sm btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if ($crew['active_assignments'] == 0): ?>
                            <form method="POST" action="<?= url('/admin/crewing/delete/' . $crew['id']) ?>" 
                                  style="display: inline;" onsubmit="return confirm('Delete this crewing staff?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.user-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-info strong {
    display: block;
}

.user-info small {
    color: #666;
}

.mini-progress {
    width: 60px;
    height: 4px;
    background: #e9ecef;
    border-radius: 2px;
    margin-top: 4px;
}

.mini-progress-bar {
    height: 100%;
    border-radius: 2px;
}

.mini-progress-bar.success { background: #27ae60; }
.mini-progress-bar.warning { background: #f39c12; }
.mini-progress-bar.danger { background: #e74c3c; }

.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 500;
}

.badge-success { background: #d4edda; color: #155724; }
.badge-warning { background: #fff3cd; color: #856404; }
.badge-danger { background: #f8d7da; color: #721c24; }
.badge-secondary { background: #e9ecef; color: #495057; }
</style>
