<?php
/**
 * Employee Report View
 */
$currentPage = 'reports';
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1><i class="fas fa-user-tie" style="color: var(--accent-gold);"></i> Employee Report</h1>
        <p>Employee statistics and data from HRIS</p>
    </div>
    <a href="<?= BASE_URL ?>reports" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<?php if (!empty($error)): ?>
    <div class="card" style="text-align: center; padding: 40px;">
        <i class="fas fa-exclamation-triangle" style="font-size: 40px; color: var(--warning); margin-bottom: 10px;"></i>
        <p style="color: var(--text-muted);">Failed to load employee data from HRIS</p>
        <p style="font-size: 14px; color: var(--danger);">
            <?= htmlspecialchars($error) ?>
        </p>
    </div>
<?php elseif (empty($employees)): ?>
    <div class="card" style="text-align: center; padding: 40px; color: var(--text-muted);">
        <i class="fas fa-users" style="font-size: 40px; margin-bottom: 10px; opacity: 0.5;"></i>
        <p>No employees found</p>
    </div>
<?php else: ?>
    <!-- Summary Statistics -->
    <div class="grid-3" style="margin-bottom: 24px;">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">Total Employees</div>
                    <div style="font-size: 28px; font-weight: 700; color: var(--accent-gold);">
                        <?= $totalEmployees ?>
                    </div>
                </div>
                <i class="fas fa-users" style="font-size: 32px; color: var(--info); opacity: 0.3;"></i>
            </div>
        </div>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">Departments</div>
                    <div style="font-size: 28px; font-weight: 700; color: var(--info);">
                        <?= count($byDepartment) ?>
                    </div>
                </div>
                <i class="fas fa-building" style="font-size: 32px; color: var(--success); opacity: 0.3;"></i>
            </div>
        </div>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">Status</div>
                    <div style="font-size: 14px; font-weight: 600;">
                        <span class="badge badge-success">Active</span>
                    </div>
                </div>
                <i class="fas fa-check-circle" style="font-size: 32px; color: var(--success); opacity: 0.3;"></i>
            </div>
        </div>
    </div>

    <!-- Department Breakdown -->
    <?php if (!empty($byDepartment)): ?>
        <div class="card" style="margin-bottom: 24px;">
            <h3 style="margin-bottom: 16px;"><i class="fas fa-chart-pie" style="color: var(--info);"></i> Employees by
                Department</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
                <?php foreach ($byDepartment as $dept => $count): ?>
                    <div
                        style="background: var(--card-hover); padding: 12px 16px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 500;">
                            <?= htmlspecialchars($dept) ?>
                        </span>
                        <span class="badge badge-info">
                            <?= $count ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Employee List -->
    <div class="card">
        <h3 style="margin-bottom: 16px;"><i class="fas fa-list" style="color: var(--success);"></i> Employee List</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                foreach ($employees as $emp): ?>
                    <tr>
                        <td>
                            <?= $no++ ?>
                        </td>
                        <td><strong>
                                <?= htmlspecialchars($emp['name'] ?? '-') ?>
                            </strong></td>
                        <td>
                            <?= htmlspecialchars($emp['email'] ?? '-') ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($emp['department'] ?? '-') ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($emp['position'] ?? '-') ?>
                        </td>
                        <td>
                            <?php
                            $status = $emp['status'] ?? 'unknown';
                            $badge = $status === 'aktif' ? 'success' : 'secondary';
                            ?>
                            <span class="badge badge-<?= $badge ?>">
                                <?= ucfirst($status) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>