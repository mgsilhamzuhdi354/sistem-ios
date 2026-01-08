<!-- Master Admin Vacancies Content -->
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1"><i class="fas fa-briefcase me-2"></i>Job Vacancies</h1>
        <p class="text-muted mb-0">Manage all job vacancies - Full access control</p>
    </div>
    <a href="<?= url('/admin/vacancies/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> New Vacancy
    </a>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-mini-card">
            <div class="stat-icon blue"><i class="fas fa-briefcase"></i></div>
            <div class="stat-content">
                <h3><?= $stats['total'] ?></h3>
                <span>Total Vacancies</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-mini-card">
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div class="stat-content">
                <h3><?= $stats['active'] ?></h3>
                <span>Active</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-mini-card">
            <div class="stat-icon red"><i class="fas fa-times-circle"></i></div>
            <div class="stat-content">
                <h3><?= $stats['closed'] ?></h3>
                <span>Closed</span>
            </div>
        </div>
    </div>
</div>

<!-- Vacancies Table -->
<div class="content-card">
    <div class="card-header-custom">
        <h5><i class="fas fa-list me-2"></i>All Vacancies</h5>
    </div>
    <div class="table-container">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Position</th>
                    <th>Department</th>
                    <th>Applications</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($vacancies)): ?>
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No vacancies found</td>
                </tr>
                <?php else: ?>
                <?php foreach ($vacancies as $v): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($v['title']) ?></strong>
                        <?php if (!empty($v['vessel_type'])): ?>
                        <br><small class="text-muted"><?= htmlspecialchars($v['vessel_type']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($v['department_name'] ?? 'N/A') ?></td>
                    <td>
                        <span class="badge bg-primary"><?= $v['application_count'] ?></span>
                    </td>
                    <td>
                        <?php if ($v['status'] === 'active'): ?>
                        <span class="status-badge active"><i class="fas fa-circle"></i> Active</span>
                        <?php else: ?>
                        <span class="status-badge closed"><i class="fas fa-circle"></i> Closed</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('d M Y', strtotime($v['created_at'])) ?></td>
                    <td>
                        <div class="action-buttons">
                            <form action="<?= url('/master-admin/vacancies/toggle/' . $v['id']) ?>" method="post" style="display:inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-action <?= $v['status'] === 'active' ? 'warning' : 'success' ?>" title="<?= $v['status'] === 'active' ? 'Close' : 'Activate' ?>">
                                    <i class="fas fa-<?= $v['status'] === 'active' ? 'pause' : 'play' ?>"></i>
                                </button>
                            </form>
                            <a href="<?= url('/admin/vacancies/edit/' . $v['id']) ?>" class="btn-action primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
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
/* Stat Mini Cards */
.stat-mini-card {
    background: white;
    border-radius: 12px;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
}
.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
}
.stat-icon.blue { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.stat-icon.green { background: linear-gradient(135deg, #22c55e, #16a34a); }
.stat-icon.red { background: linear-gradient(135deg, #ef4444, #dc2626); }
.stat-content h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
}
.stat-content span {
    font-size: 0.8rem;
    color: #6b7280;
}

/* Content Card */
.content-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
}
.card-header-custom {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f3f4f6;
}
.card-header-custom h5 {
    margin: 0;
    color: #374151;
}
.table-container {
    padding: 0;
}

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
    vertical-align: middle;
}

/* Status Badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
}
.status-badge.active {
    background: #dcfce7;
    color: #166534;
}
.status-badge.closed {
    background: #f3f4f6;
    color: #6b7280;
}
.status-badge i {
    font-size: 8px;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 0.5rem;
}
.btn-action {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform 0.2s;
    font-size: 0.8rem;
    color: white;
}
.btn-action:hover {
    transform: scale(1.1);
}
.btn-action.primary { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.btn-action.success { background: linear-gradient(135deg, #22c55e, #16a34a); }
.btn-action.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
.btn-action.danger { background: linear-gradient(135deg, #ef4444, #dc2626); }
</style>
