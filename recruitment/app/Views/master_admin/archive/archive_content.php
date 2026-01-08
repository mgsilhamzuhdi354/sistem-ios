<style>
.archive-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem; }
.stat-card { background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.stat-card .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; margin-bottom: 1rem; }
.stat-card .stat-value { font-size: 2rem; font-weight: 700; color: #1e293b; }
.stat-card .stat-label { color: #64748b; font-size: 0.875rem; }
.stat-card.total .stat-icon { background: #e0f2fe; color: #0284c7; }
.stat-card.approved .stat-icon { background: #dcfce7; color: #16a34a; }
.stat-card.rejected .stat-icon { background: #fee2e2; color: #dc2626; }
.stat-card.month .stat-icon { background: #fef3c7; color: #d97706; }

.archive-filters { background: white; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
.filter-group { display: flex; align-items: center; gap: 0.5rem; }
.filter-group label { font-weight: 500; color: #475569; font-size: 0.875rem; }
.filter-group select, .filter-group input { padding: 0.5rem 1rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem; }
.filter-btn { background: #1e3a5f; color: white; border: none; padding: 0.5rem 1.5rem; border-radius: 8px; cursor: pointer; }
.filter-btn:hover { background: #2c5282; }

.archive-actions { display: flex; gap: 1rem; margin-left: auto; }
.btn-export { background: #16a34a; color: white; border: none; padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; }
.btn-export:hover { background: #15803d; }

.archive-table { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.archive-table table { width: 100%; border-collapse: collapse; }
.archive-table th { background: #f8fafc; padding: 1rem; text-align: left; font-weight: 600; color: #475569; font-size: 0.875rem; border-bottom: 1px solid #e2e8f0; }
.archive-table td { padding: 1rem; border-bottom: 1px solid #f1f5f9; }
.archive-table tr:hover { background: #f8fafc; }

.status-badge { padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-block; }
.status-badge.approved { background: #dcfce7; color: #16a34a; }
.status-badge.rejected { background: #fee2e2; color: #dc2626; }
.status-badge.withdrawn { background: #f3f4f6; color: #6b7280; }

.action-btn { padding: 0.35rem 0.75rem; border-radius: 6px; font-size: 0.75rem; cursor: pointer; border: none; margin-right: 0.25rem; }
.action-btn.view { background: #e0f2fe; color: #0284c7; }
.action-btn.restore { background: #dcfce7; color: #16a34a; }
.action-btn.delete { background: #fee2e2; color: #dc2626; }
.action-btn:hover { opacity: 0.8; }

.pagination { display: flex; justify-content: center; gap: 0.5rem; padding: 1.5rem; }
.pagination a, .pagination span { padding: 0.5rem 1rem; border-radius: 8px; text-decoration: none; }
.pagination a { background: white; color: #1e3a5f; border: 1px solid #e2e8f0; }
.pagination a:hover { background: #f1f5f9; }
.pagination .active { background: #1e3a5f; color: white; }

.empty-state { text-align: center; padding: 3rem; color: #64748b; }
.empty-state i { font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; }
</style>

<div class="page-header">
    <h1><i class="fas fa-archive"></i> Archive Management</h1>
    <p>Manage archived applicants (approved/rejected)</p>
</div>

<!-- Stats Cards -->
<div class="archive-stats">
    <div class="stat-card total">
        <div class="stat-icon"><i class="fas fa-archive"></i></div>
        <div class="stat-value"><?= number_format($stats['total'] ?? 0) ?></div>
        <div class="stat-label">Total Archived</div>
    </div>
    <div class="stat-card approved">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-value"><?= number_format($stats['approved'] ?? 0) ?></div>
        <div class="stat-label">Approved</div>
    </div>
    <div class="stat-card rejected">
        <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
        <div class="stat-value"><?= number_format($stats['rejected'] ?? 0) ?></div>
        <div class="stat-label">Rejected</div>
    </div>
    <div class="stat-card month">
        <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
        <div class="stat-value"><?= number_format($stats['this_month'] ?? 0) ?></div>
        <div class="stat-label">This Month</div>
    </div>
</div>

<!-- Filters -->
<div class="archive-filters">
    <form method="GET" action="" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; width: 100%;">
        <div class="filter-group">
            <label>Year:</label>
            <select name="year">
                <?php foreach ($years as $y): ?>
                <option value="<?= $y['year'] ?>" <?= $currentYear == $y['year'] ? 'selected' : '' ?>><?= $y['year'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <label>Status:</label>
            <select name="status">
                <option value="all" <?= $currentStatus == 'all' ? 'selected' : '' ?>>All Status</option>
                <option value="approved" <?= $currentStatus == 'approved' ? 'selected' : '' ?>>Approved</option>
                <option value="rejected" <?= $currentStatus == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                <option value="withdrawn" <?= $currentStatus == 'withdrawn' ? 'selected' : '' ?>>Withdrawn</option>
            </select>
        </div>
        <div class="filter-group">
            <label>Search:</label>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Name, position, email...">
        </div>
        <button type="submit" class="filter-btn"><i class="fas fa-filter"></i> Filter</button>
        
        <div class="archive-actions">
            <a href="<?= url('/master-admin/archive/export?year=' . $currentYear . '&status=' . $currentStatus) ?>" class="btn-export">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        </div>
    </form>
</div>

<!-- Archive Table -->
<div class="archive-table">
    <?php if (empty($archives)): ?>
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <h3>No Archived Data</h3>
        <p>There are no archived applications matching your criteria.</p>
    </div>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Applicant</th>
                <th>Position</th>
                <th>Status</th>
                <th>Score</th>
                <th>Handler</th>
                <th>Archived Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($archives as $archive): ?>
            <tr>
                <td>
                    <strong><?= htmlspecialchars($archive['applicant_name']) ?></strong>
                    <br><small class="text-muted"><?= htmlspecialchars($archive['applicant_email']) ?></small>
                </td>
                <td>
                    <?= htmlspecialchars($archive['position_title']) ?>
                    <?php if ($archive['department_name']): ?>
                    <br><small class="text-muted"><?= htmlspecialchars($archive['department_name']) ?></small>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="status-badge <?= $archive['final_status'] ?>">
                        <?= ucfirst($archive['final_status']) ?>
                    </span>
                </td>
                <td>
                    <?= $archive['overall_score'] ?? '-' ?>
                </td>
                <td><?= htmlspecialchars($archive['handler_name'] ?? '-') ?></td>
                <td><?= date('d M Y', strtotime($archive['archived_at'])) ?></td>
                <td>
                    <button class="action-btn view" onclick="viewArchive(<?= $archive['id'] ?>)" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="action-btn restore" onclick="restoreArchive(<?= $archive['id'] ?>)" title="Restore">
                        <i class="fas fa-undo"></i>
                    </button>
                    <button class="action-btn delete" onclick="deleteArchive(<?= $archive['id'] ?>)" title="Delete Permanently">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($currentPage > 1): ?>
        <a href="?page=<?= $currentPage - 1 ?>&year=<?= $currentYear ?>&status=<?= $currentStatus ?>&search=<?= urlencode($search) ?>">
            <i class="fas fa-chevron-left"></i>
        </a>
        <?php endif; ?>
        
        <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
        <a href="?page=<?= $i ?>&year=<?= $currentYear ?>&status=<?= $currentStatus ?>&search=<?= urlencode($search) ?>" 
           class="<?= $i == $currentPage ? 'active' : '' ?>">
            <?= $i ?>
        </a>
        <?php endfor; ?>
        
        <?php if ($currentPage < $totalPages): ?>
        <a href="?page=<?= $currentPage + 1 ?>&year=<?= $currentYear ?>&status=<?= $currentStatus ?>&search=<?= urlencode($search) ?>">
            <i class="fas fa-chevron-right"></i>
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<script>
function viewArchive(id) {
    window.location.href = '<?= url('/master-admin/archive/view/') ?>' + id;
}

function restoreArchive(id) {
    if (!confirm('Restore this application to active pipeline?')) return;
    
    fetch('<?= url('/master-admin/archive/restore/') ?>' + id, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'csrf_token=<?= csrf_token() ?>'
    })
    .then(r => r.json())
    .then(data => {
        alert(data.success ? '✅ ' + data.message : '❌ ' + data.message);
        if (data.success) location.reload();
    });
}

function deleteArchive(id) {
    if (!confirm('⚠️ This will PERMANENTLY delete this archive. Continue?')) return;
    
    fetch('<?= url('/master-admin/archive/delete/') ?>' + id, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'csrf_token=<?= csrf_token() ?>'
    })
    .then(r => r.json())
    .then(data => {
        alert(data.success ? '✅ ' + data.message : '❌ ' + data.message);
        if (data.success) location.reload();
    });
}
</script>
